                                        </a>

                                        <form action="{{ route('inspecciones.destroy', $inspeccion->id) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar reporte #{{ $inspeccion->id }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                <i class="ri-file-text-line text-4xl block mb-3"></i>
                                No hay reportes de daños registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($inspecciones, 'links'))
            <div class="px-6 py-4">{{ $inspecciones->links() }}</div>
        @endif
    </section>

    {{-- Modal de detalle --}}
    <div x-cloak x-show="detalleAbierto" x-transition
         @keydown.escape.window="detalleAbierto = false"
         @click.self="detalleAbierto = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl p-6 md:p-8 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between mb-6">
                <h3 class="text-xl md:text-2xl font-bold text-primary-800">Reporte #<span x-text="detalle.id"></span></h3>
                <button type="button" @click="detalleAbierto = false" class="text-slate-400 hover:text-slate-700 text-xl">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-slate-500 text-sm mb-1">Unidad</p>
                    <p class="text-primary-600 font-bold" x-text="detalle.unidad"></p>
                    <p class="text-slate-400 text-sm" x-text="detalle.tipo_unidad"></p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-1">Inspector</p>
                    <p class="font-semibold text-slate-800" x-text="detalle.inspector"></p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-1">Tipo de daño</p>
                    <p class="font-semibold text-slate-800" x-text="detalle.tipo_dano"></p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-1">Prioridad</p>
                    <p class="font-semibold text-slate-800" x-text="detalle.prioridad"></p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-1">Estado</p>
                    <p class="font-semibold text-slate-800" x-text="detalle.estado"></p>
                </div>
                <div>
                    <p class="text-slate-500 text-sm mb-1">Fecha del reporte</p>
                    <p class="text-slate-700" x-text="detalle.fecha"></p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-slate-500 text-sm mb-1">Descripción</p>
                    <p class="text-slate-700 whitespace-pre-wrap break-words" x-text="detalle.descripcion"></p>
                </div>
                @if(auth()->user()->role === 'admin')
                    <div class="md:col-span-2">
                        <p class="text-slate-500 text-sm mb-1">Intendente responsable</p>
                        <p class="text-slate-700" x-text="detalle.intendente"></p>
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" @click="detalleAbierto = false"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-700 hover:bg-slate-300">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function inspeccionesPage() {
        return {
            detalleAbierto: false,
            detalle: {},
            abrirDetalle(datos) {
                this.detalle = datos;
                this.detalleAbierto = true;
            }
        };
    }
</script>
@endpushsi
