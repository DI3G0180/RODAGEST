@sect('title', 'Panel de Limpieza')

@sect('content')
<div class="p-6 sm:p-6 lg:p-8 max-w-7xl mx-auto">

    <!-- KPIs de Higiene y Flota -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Unidades Desinfectadas Hoy -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#7FB3B8] hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#10B981] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Unidades Desinfectadas Hoy</p>
                    <p class="text-2xl font-bold text-[#10B981]" id="limp-hoy">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Cola Pendiente de Lavado -->
        <div class="bg-[#EADCC8] rounded-2xl p-6 shadow-sm border border-[#7FB3B8] hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#F59E0B] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Cola Pendiente de Lavado</p>
                    <p class="text-2xl font-bold text-[#F59E0B]" id="limp-pendientes">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- En Proceso -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#7FB3B8] hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#FBBF24] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">En Proceso</p>
                    <p class="text-2xl font-bold text-[#F59E0B]" id="limp-proceso">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Cumplimiento Diario -->
        <div class="bg-[#EADCC8] rounded-2xl p-6 shadow-sm border border-[#7FB3B8] hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#2E6F73] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-500">Cumplimiento Diario</p>
                    <p class="text-2xl font-bold text-[#2E6F73]" id="limp-cumplimiento">98%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablero Interactivo de Lavado y Desinfección -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#7FB3B8] mb-8">

        <h2 class="font-semibold text-[#0A0F14] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l3 3l-3 3m6-6l-3 3l-3-3m6 6l-3-3l3-3m-6 6l3 3l3 3"/>
            </svg>
            Tablero de Lavado y Desinfección
        </h2>

        <div class="space-y-4">
            
            <!-- Unidad 1 -->
            <div class="group bg-[#EADCC8] border border-[#7FB3B8] rounded-lg p-4 cursor-pointer hover:bg-[#EADCC8] transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">Unidad UN-012</p>
                        <p class="text-xs text-slate-500">Microbús ejecutivo</p>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-[#7FB3B8]">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="font-medium text-slate-600">Interior:</span>
                            <span class="text-slate-900">✓ Completado</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Asientos:</span>
                            <span class="text-slate-900">✓ Completado</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                        <div>
                            <span class="font-medium text-slate-600">Exterior:</span>
                            <span class="text-slate-900">✓ Completado</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Vidrios:</span>
                            <span class="text-slate-900">✓ Completado</span>
                        </div>
                    </div>
                    <button class="mt-3 w-full bg-[#C6601F] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#A84D17] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Marcar como Lista para Ruta
                    </button>
                </div>
            </div>

            <!-- Unidad 2 -->
            <div class="group bg-[#EADCC8] border border-[#7FB3B8] rounded-lg p-4 cursor-pointer hover:bg-[#EADCC8] transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">Unidad UN-023</p>
                        <p class="text-xs text-slate-500">Camión de carga</p>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-[#7FB3B8]">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="font-medium text-slate-600">Interior:</span>
                            <span class="text-slate-500">⟳ Pendiente</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Asientos:</span>
                            <span class="text-slate-500">⟳ Pendiente</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                        <div>
                            <span class="font-medium text-slate-600">Exterior:</span>
                            <span class="text-slate-500">⟳ Pendiente</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Vidrios:</span>
                            <span class="text-slate-500">⟳ Pendiente</span>
                        </div>
                    </div>
                    <button class="mt-3 w-full bg-[#C6601F] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#A84D17] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Registrar Lavado
                    </button>
                </div>
            </div>

            <!-- Unidad 3 -->
            <div class="group bg-[#EADCC8] border border-[#7FB3B8] rounded-lg p-4 cursor-pointer hover:bg-[#EADCC8] transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">Unidad UN-045</p>
                        <p class="text-xs text-slate-500">Tanque cisterna</p>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-[#7FB3B8]">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="font-medium text-slate-600">Interior:</span>
                            <span class="text-slate-400">✗ Daño reportado</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Asientos:</span>
                            <span class="text-slate-400">✗ Daño reportado</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                        <div>
                            <span class="font-medium text-slate-600">Exterior:</span>
                            <span class="text-slate-400">✗ Daño reportado</span>
                        </div>
                        <div>
                            <span class="font-medium text-slate-600">Vidrios:</span>
                            <span class="text-slate-400">✗ Daño reportado</span>
                        </div>
                    </div>
                    <button class="mt-3 w-full bg-[#C6601F] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#A84D17] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Reportar Novedad en Cabina
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection