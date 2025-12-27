<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Area;
use App\Http\Requests\StoreEmpleadoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('area')->get();
        
        return response()->json([
            'success' => true,
            'data' => $empleados
        ]);
    }

    public function getAreas()
    {
        $areas = Area::all();
        
        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }

    public function getRoles()
    {
        $roles = DB::table('roles')->get();
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    public function store(StoreEmpleadoRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Obtener el próximo ID manualmente
            $maxId = DB::table('empleados')->max('id');
            $nextId = $maxId ? $maxId + 1 : 1;
            
            // Insertar el empleado con ID manual
            DB::table('empleados')->insert([
                'id' => $nextId,
                'nombre' => $request->nombre,
                'email' => $request->email,
                'sexo' => $request->sexo,
                'area_id' => $request->area_id,
                'descripcion' => $request->descripcion,
                'boletin' => $request->boletin ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar roles
            foreach ($request->roles as $rolId) {
                DB::table('empleado_rol')->insert([
                    'empleado_id' => $nextId,
                    'rol_id' => $rolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();

            // Cargar el empleado con su área para la respuesta
            $empleado = Empleado::with('area')->find($nextId);

            return response()->json([
                'success' => true,
                'data' => $empleado,
                'message' => 'Empleado creado exitosamente'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(StoreEmpleadoRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Verificar que el empleado existe
            $empleado = Empleado::find($id);
            if (!$empleado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            // Actualizar el empleado
            DB::table('empleados')
                ->where('id', $id)
                ->update([
                    'nombre' => $request->nombre,
                    'email' => $request->email,
                    'sexo' => $request->sexo,
                    'area_id' => $request->area_id,
                    'descripcion' => $request->descripcion,
                    'boletin' => $request->boletin ?? 0,
                    'updated_at' => now(),
                ]);

            // Eliminar roles anteriores y asignar nuevos
            DB::table('empleado_rol')->where('empleado_id', $id)->delete();
            
            foreach ($request->roles as $rolId) {
                DB::table('empleado_rol')->insert([
                    'empleado_id' => $id,
                    'rol_id' => $rolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();

            // Cargar el empleado actualizado con su área
            $empleadoActualizado = Empleado::with('area')->find($id);

            return response()->json([
                'success' => true,
                'data' => $empleadoActualizado,
                'message' => 'Empleado actualizado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Verificar que el empleado existe
            $empleado = Empleado::find($id);
            if (!$empleado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            // Eliminar roles del empleado (por cascade se eliminarán automáticamente, pero lo hacemos explícito)
            DB::table('empleado_rol')->where('empleado_id', $id)->delete();
            
            // Eliminar empleado
            DB::table('empleados')->where('id', $id)->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $empleado = Empleado::with('area')->find($id);
        
        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        // Obtener los roles del empleado
        $roles = DB::table('empleado_rol')
            ->where('empleado_id', $id)
            ->pluck('rol_id')
            ->toArray();

        $empleado->roles = $roles;
        
        return response()->json([
            'success' => true,
            'data' => $empleado
        ]);
    }
}
