<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Empleados</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Título -->
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Lista de empleados</h1>
        
        <!-- Botón Crear -->
        <div class="mb-6 flex justify-end">
            <button onclick="abrirModal()" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                <span>Crear</span>
            </button>
        </div>
        
        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-500"></i>
                                <span>Nombre</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-500"></i>
                                <span>Email</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-venus-mars text-gray-500"></i>
                                <span>Sexo</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-building text-gray-500"></i>
                                <span>Área</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                            Boletín
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                            Modificar
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                            Eliminar
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="empleados-tbody">
                    <!-- Los empleados se cargarán dinámicamente con JavaScript -->
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                            <p class="mt-2">Cargando empleados...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>

    <!-- Modal Crear Empleado -->
    <div id="modalCrearEmpleado" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 id="modalTitulo" class="text-2xl font-bold text-gray-900">Crear empleado</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 text-3xl font-bold">
                    &times;
                </button>
            </div>

            <!-- Mensaje de campos obligatorios -->
            <div class="mt-4 bg-blue-50 border-l-4 border-blue-200 p-4">
                <p class="text-sm text-blue-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    Los campos con asteriscos (*) son obligatorios.
                </p>
            </div>

            <!-- Formulario -->
            <form id="formCrearEmpleado" class="mt-6 space-y-6">
                
                <!-- Campo oculto para ID en modo edición -->
                <input type="hidden" id="empleadoId" name="empleado_id" value="">
                
                <!-- Campo: Nombre completo -->
                <div class="flex items-center">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12">
                        <input type="text" name="nombre" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Nombre completo del empleado">
                    </div>
                </div>

                <!-- Campo: Correo electrónico -->
                <div class="flex items-center">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4">
                        Correo electrónico <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12">
                        <input type="email" name="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Correo electrónico">
                    </div>
                </div>

                <!-- Campo: Sexo -->
                <div class="flex items-start">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4 pt-2">
                        Sexo <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12 space-y-2">
                        <div class="flex items-center">
                            <input type="radio" name="sexo" value="M" id="sexoM" required
                                class="w-4 h-4 text-cyan-600 focus:ring-cyan-500">
                            <label for="sexoM" class="ml-2 text-sm text-gray-700">Masculino</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="sexo" value="F" id="sexoF" required
                                class="w-4 h-4 text-cyan-600 focus:ring-cyan-500">
                            <label for="sexoF" class="ml-2 text-sm text-gray-700">Femenino</label>
                        </div>
                    </div>
                </div>

                <!-- Campo: Área -->
                <div class="flex items-center">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4">
                        Área <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12">
                        <select name="area_id" id="selectArea" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="">Seleccione un área</option>
                        </select>
                    </div>
                </div>

                <!-- Campo: Descripción -->
                <div class="flex items-start">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4 pt-2">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12">
                        <textarea name="descripcion" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Descripción de la experiencia del empleado"></textarea>
                    </div>
                </div>

                <!-- Campo: Boletín -->
                <div class="flex items-center">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4">
                    </label>
                    <div class="w-9/12">
                        <div class="flex items-center">
                            <input type="checkbox" name="boletin" value="on" id="boletin"
                                class="w-4 h-4 text-cyan-600 focus:ring-cyan-500 rounded">
                            <label for="boletin" class="ml-2 text-sm text-gray-700">
                                Deseo recibir boletín informativo
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Campo: Roles -->
                <div class="flex items-start">
                    <label class="w-3/12 text-sm font-medium text-gray-700 text-right pr-4 pt-2">
                        Roles <span class="text-red-500">*</span>
                    </label>
                    <div class="w-9/12 space-y-2" id="rolesContainer">
                        <!-- Los roles se cargarán dinámicamente -->
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="cerrarModal()"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSubmit"
                        class="px-6 py-2.5 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 font-medium transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="btnSubmitText">Guardar</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="/js/empleados.js"></script>
</body>
</html>
