// Variables globales
let areasData = [];
let rolesData = [];
let modoEdicion = false;
let empleadoEditandoId = null;

// Cargar empleados cuando la página se cargue
document.addEventListener('DOMContentLoaded', function() {
    cargarEmpleados();
    cargarAreas();
    cargarRoles();
    inicializarFormulario();
});

function cargarEmpleados() {
    fetch('/api/empleados')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarEmpleados(data.data);
            } else {
                mostrarError('Error al cargar los empleados');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al servidor');
        });
}

function cargarAreas() {
    fetch('/api/areas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                areasData = data.data;
                const select = document.getElementById('selectArea');
                select.innerHTML = '<option value="">Seleccione un área</option>';
                data.data.forEach(area => {
                    select.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar áreas:', error);
        });
}

function cargarRoles() {
    fetch('/api/roles')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                rolesData = data.data;
                const container = document.getElementById('rolesContainer');
                container.innerHTML = '';
                data.data.forEach(rol => {
                    container.innerHTML += `
                        <div class="flex items-center">
                            <input type="checkbox" name="roles[]" value="${rol.id}" id="rol${rol.id}"
                                class="w-4 h-4 text-cyan-600 focus:ring-cyan-500 rounded">
                            <label for="rol${rol.id}" class="ml-2 text-sm text-gray-700">
                                ${rol.nombre}
                            </label>
                        </div>
                    `;
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar roles:', error);
        });
}

function abrirModal() {
    modoEdicion = false;
    empleadoEditandoId = null;
    document.getElementById('modalTitulo').textContent = 'Crear empleado';
    document.getElementById('btnSubmitText').textContent = 'Guardar';
    document.getElementById('empleadoId').value = '';
    document.getElementById('modalCrearEmpleado').classList.remove('hidden');
    document.getElementById('formCrearEmpleado').reset();
    limpiarErrores();
}

function abrirModalEdicion(id) {
    modoEdicion = true;
    empleadoEditandoId = id;
    document.getElementById('modalTitulo').textContent = 'Editar empleado';
    document.getElementById('btnSubmitText').textContent = 'Actualizar';
    document.getElementById('empleadoId').value = id;
    document.getElementById('modalCrearEmpleado').classList.remove('hidden');
    limpiarErrores();
    
    // Cargar datos del empleado
    fetch(`/api/empleados/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const empleado = data.data;
                
                // Llenar formulario
                document.querySelector('input[name="nombre"]').value = empleado.nombre;
                document.querySelector('input[name="email"]').value = empleado.email;
                document.querySelector(`input[name="sexo"][value="${empleado.sexo}"]`).checked = true;
                document.querySelector('select[name="area_id"]').value = empleado.area_id;
                document.querySelector('textarea[name="descripcion"]').value = empleado.descripcion;
                document.querySelector('input[name="boletin"]').checked = empleado.boletin == 1;
                
                // Seleccionar roles
                empleado.roles.forEach(rolId => {
                    const checkbox = document.querySelector(`input[name="roles[]"][value="${rolId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            } else {
                mostrarNotificacion('Error al cargar datos del empleado', 'error');
                cerrarModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error de conexión al servidor', 'error');
            cerrarModal();
        });
}

function cerrarModal() {
    modoEdicion = false;
    empleadoEditandoId = null;
    document.getElementById('modalCrearEmpleado').classList.add('hidden');
    limpiarErrores();
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalCrearEmpleado')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
});

function inicializarFormulario() {
    const form = document.getElementById('formCrearEmpleado');
    if (form) {
        form.addEventListener('submit', manejarSubmit);
        
        // Validación en tiempo real
        const inputNombre = form.querySelector('input[name="nombre"]');
        const inputEmail = form.querySelector('input[name="email"]');
        
        if (inputNombre) {
            inputNombre.addEventListener('blur', function() {
                validarNombre(this.value);
            });
        }
        
        if (inputEmail) {
            inputEmail.addEventListener('blur', function() {
                validarEmail(this.value);
            });
        }
    }
}

function manejarSubmit(e) {
    e.preventDefault();
    
    limpiarErrores();
    
    const formData = new FormData(e.target);
    
    // Validaciones del formulario
    const errores = validarFormulario(formData);
    
    if (errores.length > 0) {
        mostrarErroresValidacion(errores);
        return;
    }
    
    // Convertir FormData a objeto JSON
    const data = {
        nombre: formData.get('nombre'),
        email: formData.get('email'),
        sexo: formData.get('sexo'),
        area_id: formData.get('area_id'),
        descripcion: formData.get('descripcion'),
        boletin: formData.get('boletin') ? 1 : 0,
        roles: formData.getAll('roles[]')
    };
    
    // Deshabilitar botón de submit
    const btnSubmit = e.target.querySelector('button[type="submit"]');
    const textoOriginal = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
    
    const url = modoEdicion ? `/api/empleados/${empleadoEditandoId}` : '/api/empleados';
    const method = modoEdicion ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = textoOriginal;
        
        if (data.success) {
            const mensaje = modoEdicion ? 'Empleado actualizado exitosamente' : 'Empleado creado exitosamente';
            mostrarNotificacion(mensaje, 'success');
            cerrarModal();
            cargarEmpleados();
        } else {
            if (data.errors) {
                mostrarErroresBackend(data.errors);
            } else {
                mostrarNotificacion('Error: ' + (data.message || 'Error desconocido'), 'error');
            }
        }
    })
    .catch(error => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = textoOriginal;
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al servidor', 'error');
    });
}

function validarFormulario(formData) {
    const errores = [];
    
    // Validar nombre
    const nombre = formData.get('nombre');
    const errorNombre = validarNombre(nombre);
    if (errorNombre) errores.push({ campo: 'nombre', mensaje: errorNombre });
    
    // Validar email
    const email = formData.get('email');
    const errorEmail = validarEmail(email);
    if (errorEmail) errores.push({ campo: 'email', mensaje: errorEmail });
    
    // Validar sexo
    const sexo = formData.get('sexo');
    if (!sexo) {
        errores.push({ campo: 'sexo', mensaje: 'Debe seleccionar el sexo' });
    }
    
    // Validar área
    const area_id = formData.get('area_id');
    if (!area_id) {
        errores.push({ campo: 'area_id', mensaje: 'Debe seleccionar un área' });
    }
    
    // Validar descripción
    const descripcion = formData.get('descripcion');
    if (!descripcion || descripcion.trim() === '') {
        errores.push({ campo: 'descripcion', mensaje: 'La descripción es obligatoria' });
    }
    
    // Validar roles
    const roles = formData.getAll('roles[]');
    if (roles.length === 0) {
        errores.push({ campo: 'roles', mensaje: 'Debe seleccionar al menos un rol' });
    }
    
    return errores;
}

function validarNombre(nombre) {
    if (!nombre || nombre.trim() === '') {
        return 'El nombre es obligatorio';
    }
    
    // Solo letras con o sin tilde, espacios, no números ni caracteres especiales
    const regex = /^[a-záéíóúñüA-ZÁÉÍÓÚÑÜ\s]+$/;
    if (!regex.test(nombre)) {
        return 'El nombre solo debe contener letras y espacios';
    }
    
    if (nombre.length > 255) {
        return 'El nombre no debe exceder 255 caracteres';
    }
    
    return null;
}

function validarEmail(email) {
    if (!email || email.trim() === '') {
        return 'El correo electrónico es obligatorio';
    }
    
    // Validar formato de email
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(email)) {
        return 'El formato del correo electrónico no es válido';
    }
    
    if (email.length > 255) {
        return 'El correo no debe exceder 255 caracteres';
    }
    
    return null;
}

function mostrarErroresValidacion(errores) {
    errores.forEach((error, index) => {
        setTimeout(() => {
            mostrarNotificacion(error.mensaje, 'error');
        }, index * 500); // Mostrar cada error con 500ms de diferencia
        
        const input = document.querySelector(`[name="${error.campo}"], [name="${error.campo}[]"]`);
        if (input) {
            input.classList.add('border-red-500');
        }
    });
}

function mostrarErroresBackend(errores) {
    let index = 0;
    Object.keys(errores).forEach(campo => {
        const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
        mensajes.forEach(mensaje => {
            setTimeout(() => {
                mostrarNotificacion(mensaje, 'error');
            }, index * 500);
            index++;
            
            const input = document.querySelector(`[name="${campo}"], [name="${campo}[]"]`);
            if (input) {
                input.classList.add('border-red-500');
            }
        });
    });
}

function limpiarErrores() {
    // Remover clases de error de inputs
    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
}

function mostrarNotificacion(mensaje, tipo = 'success') {
    const icono = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const bgColor = tipo === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-slide-in`;
    notificacion.style.animation = 'slideIn 0.3s ease-out';
    notificacion.innerHTML = `
        <i class="fas ${icono}"></i>
        <span>${mensaje}</span>
    `;
    
    document.body.appendChild(notificacion);
    
    // Ajustar posición si hay múltiples notificaciones
    const toasts = document.querySelectorAll('.fixed.top-4.right-4');
    if (toasts.length > 1) {
        toasts.forEach((toast, index) => {
            toast.style.top = `${16 + (index * 70)}px`;
        });
    }
    
    setTimeout(() => {
        notificacion.style.opacity = '0';
        notificacion.style.transform = 'translateX(400px)';
        notificacion.style.transition = 'all 0.3s ease-out';
        setTimeout(() => {
            notificacion.remove();
            // Reajustar posición de toasts restantes
            const remainingToasts = document.querySelectorAll('.fixed.top-4.right-4');
            remainingToasts.forEach((toast, index) => {
                toast.style.top = `${16 + (index * 70)}px`;
            });
        }, 300);
    }, 4000);
}

function mostrarEmpleados(empleados) {
    const tbody = document.getElementById('empleados-tbody');
    
    if (empleados.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-2"></i>
                    <p class="mt-2">No hay empleados registrados</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = empleados.map(empleado => `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${empleado.nombre}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                ${empleado.email}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                ${empleado.sexo === 'M' ? 'Masculino' : 'Femenino'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                ${empleado.area ? empleado.area.nombre : 'Sin área'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${empleado.boletin == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${empleado.boletin == 1 ? 'Sí' : 'No'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <button onclick="editarEmpleado(${empleado.id})" class="text-blue-600 hover:text-blue-900 transition">
                    <i class="fas fa-edit text-lg"></i>
                </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <button onclick="eliminarEmpleado(${empleado.id})" class="text-red-600 hover:text-red-900 transition">
                    <i class="fas fa-trash text-lg"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function mostrarError(mensaje) {
    const tbody = document.getElementById('empleados-tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-8 text-center text-red-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                <p class="mt-2">${mensaje}</p>
                <button onclick="cargarEmpleados()" class="mt-4 px-4 py-2 bg-cyan-600 text-white rounded hover:bg-cyan-700">
                    Reintentar
                </button>
            </td>
        </tr>
    `;
}

function editarEmpleado(id) {
    abrirModalEdicion(id);
}

function eliminarEmpleado(id) {
    if (!confirm('¿Está seguro de eliminar este empleado?')) {
        return;
    }
    
    fetch(`/api/empleados/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('Empleado eliminado exitosamente', 'success');
            cargarEmpleados();
        } else {
            mostrarNotificacion('Error al eliminar: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al servidor', 'error');
    });
}
