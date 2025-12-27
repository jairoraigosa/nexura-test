<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $empleadoId = $this->route('id'); // Obtener ID de la ruta si existe
        
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-záéíóúñüA-ZÁÉÍÓÚÑÜ\s]+$/' // Solo letras con o sin tilde y espacios
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:empleados,email' . ($empleadoId ? ',' . $empleadoId : '')
            ],
            'sexo' => [
                'required',
                'in:M,F' // Solo M o F
            ],
            'area_id' => [
                'required',
                'integer',
                'exists:areas,id'
            ],
            'descripcion' => [
                'required',
                'string'
            ],
            'boletin' => [
                'nullable',
                'integer',
                'in:0,1' // Solo 0 o 1
            ],
            'roles' => [
                'required',
                'array',
                'min:1'
            ],
            'roles.*' => [
                'integer',
                'exists:roles,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.regex' => 'El nombre solo debe contener letras y espacios',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El formato del correo electrónico no es válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'email.max' => 'El correo no debe exceder 255 caracteres',
            'sexo.required' => 'Debe seleccionar el sexo',
            'sexo.in' => 'El sexo debe ser Masculino o Femenino',
            'area_id.required' => 'Debe seleccionar un área',
            'area_id.exists' => 'El área seleccionada no es válida',
            'descripcion.required' => 'La descripción es obligatoria',
            'roles.required' => 'Debe seleccionar al menos un rol',
            'roles.min' => 'Debe seleccionar al menos un rol',
            'roles.*.exists' => 'Uno o más roles seleccionados no son válidos'
        ];
    }
}
