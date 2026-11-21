@sect('title', 'Panel de Operador')

@sect('content')
<div class="p-6 sm:p-6 lg:p-8 max-w-7xl mx-auto">

    <!-- Tarjeta Hero: Mi Unidad Asignada -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#B1D0E2] mb-8">

        <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
            </svg>
            Mi Unidad Asignada
        </h2>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-slate-500">Número Económico</p>
                <p class="text-2xl font-bold text-[#018FC7]" id="num-economico">UN-004</p>
            </div>
            <div>
            </div>
            <div>
                <p class="text-sm text-slate-500">Ruta Asignada</p>
                <p class="text-2xl font-bold text-[#018FC7]" id="ruta">Ruta Centro</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Kilometraje</p>
                <p class="text-2xl font-bold text-[#018FC7]" id="km">45,230 km</p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-[#B1D0E2]">
            <!-- Status badges -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-[#DBE7F3] text-[#018FC7] text-xs font-medium px-3 py-1 rounded">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l13 13l-13 13"/>
                    </svg>
                    Técnico: <span class="font-normal">Regular</span>
                </div>
                <div class="bg-[#10B981] text-white text-xs font-medium px-3 py-1 rounded">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l13 13l-13 13"/>
                    </svg>
                    Limpieza: <span class="font-normal">Lista</span>
                </div>
            </div>

            <button class="bg-[#018FC7] text-white px-6 py-3 rounded-2xl font-medium hover:bg-[#165a7c] transition-colors w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                </svg>
                Reportar Incidencia / Falla en Ruta
            </button>
        </div>
    </div>

    <!-- Bitácora de Turno y Mis Reportes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Reporte de Falla -->
        <div class="bg-[#FBF1E5] rounded-2xl p-6 shadow-sm border border-[#B1D0E2]">

            <h3 class="font-medium text-slate-800 mb-3">Reportar Falla / Incidencia</h3>

            <div class="space-y-3">
                <label class="group bg-white border border-[#B1D0E2] rounded-lg p-3 cursor-pointer hover:bg-[#DBE7F3] transition-colors">
                    <input type="radio" name="urgencia" class="hidden peer" value="baja" checked>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 roundedbg-[#FBF1E5] flex items-center justify-center shrink-0 flex-shrink-0">
                            <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Baja</p>
                            <p class="text-xs text-slate-500">Falla menor, unidad operable</p>
                        </div>
                    </div>
                </label>

                <label class="group bg-white border border-[#B1D0E2] rounded-lg p-3 cursor-pointer hover:bg-[#FBBF24] transition-colors">
                    <input type="radio" name="urgencia" class="hidden peer" value="media">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 roundedbg-[#FBF1E5] flex items-center justify-center shrink-0 flex-shrink-0">
                            <svg class="w-4 h-4 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Media</p>
                            <p class="text-xs text-slate-500">Requiere atención pronto</p>
                        </div>
                    </div>
                </label>

                <label class="group bg-white border border-[#B1D0E2] rounded-lg p-3 cursor-pointer hover-bg-[#EF4444] transition-colors">
                    <input type="radio" name="urgencia" class="hidden peer" value="urgente">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 roundedbg-[#FBF1E5] flex items-center justify-center shrink-0 flex-shrink-0">
                            <svg class="w-4 h-4 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Urgente</p>
                            <p class="text-xs text-slate-500">Unidad fuera de servicio</p>
                        </div>
                    </div>
                </label>
            </div>

            <button class="mt-4 bg-[#018FC7] text-white px-6 py-2 rounded-lg font-medium hover:bg-[#165a7c] transition-colors w-full">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Reportar Incidencia
            </button>
        </div>

        <!-- Mis Reportes -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#B1D0E2]">

            <h3 class="font-medium text-slate-800 mb-3">Mis Reportes</h3>

            <p class="text-sm text-slate-500">No tiene reportes pendientes</p>

            <div class="mt-4 h-64 bg-[#FBF1E5] border border-[#B1D0E2] rounded-lg flex items-center justify-center text-slate-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection