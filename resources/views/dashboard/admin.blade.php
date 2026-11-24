@sect('title', 'Panel de Administración')

@sect('content')
<div class="p-6 sm:p-6 lg:p-8 max-w-7xl mx-auto">

    <!-- KPIs Ejecutivos Admin -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Total Flota -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#F4F9FB] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Total Flota</p>
                    <p class="text-2xl font-bold text-[#0C7075]" id="admin-unidades">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Personal Activo -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#F4F9FB] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#6DA5C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Personal Activo</p>
                    <p class="text-2xl font-bold text-[#6DA5C0]" id="personal-activo">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Inspecciones del Día -->
        <div class="bg-[#F4F9FB] rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#0C7075] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Inspecciones Hoy</p>
                    <p class="text-2xl font-bold text-[#0C7075]" id="inspecciones-hoy">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Reportes Pendientes -->
        <div class="bg-[#F4F9FB] rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#0C7075] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Reportes Pendientes</p>
                    <p class="text-2xl font-bold text-[#0C7075]" id="reportes-pendientes">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulo: Expedientes Completo del Personal -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 mb-8">

        <h2 class="font-semibold text-[#05161A] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#294D61]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l3 3l-3 3m6-6l-3 3l-3-3m6 6l-3-3l3-3m-6 6l3 3l3 3"/>
            </svg>
            Expedientes Completo del Personal
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#6DA5C0]">
                <thead class="bg-[#F4F9FB] text-left text-sm text-[#294D61] uppercase">
                    <tr>
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Rol</th>
                        <th class="py-3 px-4">Años en Empresa</th>
                        <th class="py-3 px-4">Unidad Asignada</th>
                        <th class="py-3 px-4">Tel. Emergencia</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-[#294D61]">
                    <tr class="odd:bg-[#F4F9FB]">
                        <td class="py-4 px-4">
                            <img src="{{ asset('empleados/admin.jpg') }}" alt="Admin" class="w-8 h-8 rounded object-cover">
                        </td>
                        <td class="py-4 px-4">Administrador RodaGest</td>
                        <td class="py-4 px-4">Admin</td>
                        <td class="py-4 px-4">5 años 9 meses</td>
                        <td class="py-4 px-4">N/A</td>
                        <td class="py-4 px-4">+52 55 1234 5678</td>
                    </tr>
                    <tr class="even:bg-white">
                        <td class="py-4 px-4">
                            <img src="{{ asset('empleados/inspector.jpg') }}" alt="Inspector" class="w-8 h-8 rounded object-cover">
                        </td>
                        <td class="py-4 px-4">Inspector de Patio</td>
                        <td class="py-4 px-4">Inspector</td>
                        <td class="py-4 px-4">3 años 7 meses</td>
                        <td class="py-4 px-4">N/A</td>
                        <td class="py-4 px-4">+52 55 2345 6789</td>
                    </tr>
                    <tr class="odd:bg-[#F4F9FB]">
                        <td class="py-4 px-4">
                            <img src="{{ asset('empleados/operador.jpg') }}" alt="Operador" class="w-8 h-8 rounded object-cover">
                        </td>
                        <td class="py-4 px-4">Operador / Chófer</td>
                        <td class="py-4 px-4">Operador</td>
                        <td class="py-4 px-4">1 año 3 meses</td>
                        <td class="py-4 px-4">UN-004</td>
                        <td class="py-4 px-4">+52 55 3456 7890</td>
                    </tr>
                    <tr class="even:bg-white">
                        <td class="py-4 px-4">
                            <img src="{{ asset('empleados/limpieza.jpg') }}" alt="Limpieza" class="w-8 h-8 rounded object-cover">
                        </td>
                        <td class="py-4 px-4">Personal de Limpieza</td>
                        <td class="py-4 px-4">Limpieza</td>
                        <td class="py-4 px-4">2 años 4 meses</td>
                        <td class="py-4 px-4">UN-012</td>
                        <td class="py-4 px-4">+52 55 4567 8901</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Botón para abrir modal de registro/edición de empleado -->
        <div class="mt-6 pt-6 border-t border-[#6DA5C0]/50">
            <button class="bg-[#0F969C] text-white px-6 py-3 rounded-2xl font-medium hover:bg-[#072E33] transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar / Editar Empleado
            </button>
        </div>
    </div>
</div>
@endsection