@sect('title', 'Panel de Inspección')

@sect('content')
<div class="p-6 sm:p-6 lg:p-8 max-w-7xl mx-auto">

    <!-- KPIs Técnicos Inspector -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Unidades Revisadas Hoy -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#0C7075] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Unidades Revisadas Hoy</p>
                    <p class="text-2xl font-bold text-[#0C7075]" id="insp-hoy">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Pendientes de Inspección -->
        <div class="bg-[#F4F9FB] rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#0C7075] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Pendientes de Inspección</p>
                    <p class="text-2xl font-bold text-[#0C7075]" id="insp-pendientes">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Hallazgos Críticos -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#EF4444] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Hallazgos Críticos</p>
                    <p class="text-2xl font-bold text-[#EF4444]" id="insp-fallas">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Tasa de Aprobación -->
        <div class="bg-[#F4F9FB] rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 hover:hover:shadow-md transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-[#10B981] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l13 13-13 13"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-[#294D61]">Tasa de Aprobación</p>
                    <p class="text-2xl font-bold text-[#10B981]" id="insp-aprobadas">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Digital Rápido -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#6DA5C0]/50 mb-8">

        <h2 class="font-semibold text-[#05161A] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#294D61]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l3 3l-3 3m6-6l-3 3l-3-3m6 6l-3-3l3-3m-6 6l3 3l3 3"/>
            </svg>
            Checklist Digital Rápido
        </h2>

        <div class="space-y-3">
            <label class="group bg-[#F4F9FB] border border-[#6DA5C0]/50 rounded-lg p-4 cursor-pointer hover:bg-[#F4F9FB] transition-colors">
                <input type="radio" name="revision-unidad" class="hidden peer" value="frenos">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Frenos</p>
                        <p class="text-xs text-[#294D61]">Verificar presión y desgaste</p>
                    </div>
                </div>
            </label>

            <label class="group bg-[#F4F9FB] border border-[#6DA5C0]/50 rounded-lg p-4 cursor-pointer hover:bg-[#F4F9FB] transition-colors">
                <input type="radio" name="revision-unidad" class="hidden peer" value="neumaticos">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Neumáticos</p>
                        <p class="text-xs text-[#294D61]">Presión y estado de la banda</p>
                    </div>
                </div>
            </label>

            <label class="group bg-[#F4F9FB] border border-[#6DA5C0]/50 rounded-lg p-4 cursor-pointer hover:bg-[#F4F9FB] transition-colors">
                <input type="radio" name="revision-unidad" class="hidden peer" value="luces">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Luces</p>
                        <p class="text-xs text-[#294D61]">Faros y señalización</p>
                    </div>
                </div>
            </label>

            <label class="group bg-[#F4F9FB] border border-[#6DA5C0]/50 rounded-lg p-4 cursor-pointer hover:bg-[#F4F9FB] transition-colors">
                <input type="radio" name="revision-unidad" class="hidden peer" value="motor">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Motor / Niveles</p>
                        <p class="text-xs text-[#294D61]">Aceite, refrigerante, líquidos</p>
                    </div>
                </div>
            </label>

            <label class="group bg-[#F4F9FB] border border-[#6DA5C0]/50 rounded-lg p-4 cursor-pointer hover:bg-[#F4F9FB] transition-colors">
                <input type="radio" name="revision-unidad" class="hidden peer" value="carroceria">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0C7075]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Carrocería</p>
                        <p class="text-xs text-[#294D61]">Estado exterior y daños</p>
                    </div>
                </div>
            </label>
        </div>

        <!-- Botón para subir evidencia fotográfica -->
        <div class="mt-6 pt-6 border-t border-[#6DA5C0]/50">
            <button class="bg-[#0F969C] text-white px-6 py-3 rounded-2xl font-medium hover:bg-[#072E33] transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Subir Evidencia Fotográfica
            </button>
        </div>
    </div>
</div>
@endsection