<?php

namespace App\Http\Controllers;

use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:40'],
            'urgencia' => ['nullable', 'in:baja,media,alta,critica,normal'],
        ]);

        $desde = Carbon::parse($filtros['desde'] ?? today()->subDays(29)->toDateString())->startOfDay();
        $hasta = Carbon::parse($filtros['hasta'] ?? today()->toDateString())->endOfDay();

        $inspecciones = Inspeccion::with('unidad')
            ->whereBetween('fecha_reporte', [$desde, $hasta])->latest('fecha_reporte')->get();
        $aseos = Aseo::with('unidad')
            ->whereBetween('fecha_hora', [$desde, $hasta])->latest('fecha_hora')->get();
        $incidencias = Queja::with('unidad')
            ->whereBetween('created_at', [$desde, $hasta])->latest()->get();

        $term = trim($filtros['q'] ?? '');
        if ($term !== '') {
            $needle = mb_strtolower($term);
            $inspecciones = $inspecciones->filter(fn ($item) => str_contains(mb_strtolower(($item->unidad?->economico ?? '').' '.$item->tipo_dano.' '.$item->descripcion), $needle))->values();
            $aseos = $aseos->filter(fn ($item) => str_contains(mb_strtolower(($item->unidad?->economico ?? '').' '.$item->area_terminal.' '.$item->observaciones), $needle))->values();
            $incidencias = $incidencias->filter(fn ($item) => str_contains(mb_strtolower(($item->eco ?: $item->unidad?->economico ?? '').' '.$item->area.' '.$item->tipo_queja.' '.$item->descripcion), $needle))->values();
        }
        if (! empty($filtros['estado'])) {
            $inspecciones = $inspecciones->where('estado', $filtros['estado'])->values();
            $aseos = $aseos->where('estado', $filtros['estado'])->values();
            $incidencias = $incidencias->where('estado', $filtros['estado'])->values();
        }
        if (! empty($filtros['urgencia'])) {
            $inspecciones = $inspecciones->where('prioridad', $filtros['urgencia'])->values();
            $incidencias = $incidencias->where('urgencia', $filtros['urgencia'])->values();
            $aseos = collect();
        }

        $inspeccionesPorTipo = $inspecciones->groupBy(fn ($item) => $item->tipo_dano ?: 'Sin categoría')
            ->map->count()->sortDesc();
        $incidenciasPorTipo = $incidencias->groupBy(fn ($item) => $item->tipo_queja ?: 'Sin categoría')
            ->map->count()->sortDesc();
        $aseosPorTipo = $aseos->groupBy(fn ($item) => $item->tipo_limpieza ?: 'Sin categoría')
            ->map->count()->sortDesc();

        return view('reportes.index', [
            'desde' => $desde,
            'hasta' => $hasta,
            'filtros' => $filtros,
            'inspecciones' => $inspecciones,
            'aseos' => $aseos,
            'incidencias' => $incidencias,
            'inspeccionesPorTipo' => $inspeccionesPorTipo,
            'incidenciasPorTipo' => $incidenciasPorTipo,
            'aseosPorTipo' => $aseosPorTipo,
            'estadosFlota' => Unidad::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado'),
        ]);
    }

    public function export(Request $request)
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:40'],
            'urgencia' => ['nullable', 'in:baja,media,alta,critica,normal'],
        ]);

        $desde = Carbon::parse($filtros['desde'] ?? today()->subDays(29)->toDateString())->startOfDay();
        $hasta = Carbon::parse($filtros['hasta'] ?? today()->toDateString())->endOfDay();

        return response()->streamDownload(function () use ($desde, $hasta, $filtros) {
            $zipPath = tempnam(sys_get_temp_dir(), 'rodagest-xlsx-');
            $sheetPath = tempnam(sys_get_temp_dir(), 'rodagest-sheet-');
            $sheet = fopen($sheetPath, 'wb');
            $xml = static fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $column = static function (int $number): string {
                $name = '';
                while ($number > 0) {
                    $number--;
                    $name = chr(65 + ($number % 26)) . $name;
                    $number = intdiv($number, 26);
                }

                return $name;
            };
            $writeRow = static function (array $values, int $rowNumber, bool $header = false) use ($sheet, $xml, $column): void {
                fwrite($sheet, '<row r="' . $rowNumber . '">');
                foreach (array_values($values) as $index => $value) {
                    $cell = $column($index + 1) . $rowNumber;
                    $style = $header ? ' s="1"' : '';
                    fwrite($sheet, '<c r="' . $cell . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $xml((string) ($value ?? '')) . '</t></is></c>');
                }
                fwrite($sheet, '</row>');
            };

            fwrite($sheet, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols><col min="1" max="1" width="19" customWidth="1"/><col min="2" max="2" width="20" customWidth="1"/><col min="3" max="8" width="24" customWidth="1"/></cols><sheetData>');
            $writeRow(['Fecha', 'Tipo de registro', 'Económico', 'Área', 'Categoría', 'Estado', 'Prioridad', 'Responsable', 'Detalle'], 1, true);
            $rowNumber = 2;
            $inspecciones = Inspeccion::with('unidad', 'inspector')->whereBetween('fecha_reporte', [$desde, $hasta]);
            $aseos = Aseo::with('unidad')->whereBetween('fecha_hora', [$desde, $hasta]);
            $incidencias = Queja::with(['unidad', 'operador'])->whereBetween('created_at', [$desde, $hasta]);
            if (! empty($filtros['q'])) {
                $term = $filtros['q'];
                $inspecciones->where(fn ($q) => $q->whereHas('unidad', fn ($u) => $u->where('economico', 'like', "%{$term}%"))->orWhere('tipo_dano', 'like', "%{$term}%")->orWhere('descripcion', 'like', "%{$term}%"));
                $aseos->where(fn ($q) => $q->whereHas('unidad', fn ($u) => $u->where('economico', 'like', "%{$term}%"))->orWhere('area_terminal', 'like', "%{$term}%")->orWhere('observaciones', 'like', "%{$term}%"));
                $incidencias->where(fn ($q) => $q->where('eco', 'like', "%{$term}%")
                    ->orWhereHas('unidad', fn ($unit) => $unit->where('economico', 'like', "%{$term}%"))
                    ->orWhere('area', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%"));
            }
            if (! empty($filtros['estado'])) {
                $inspecciones->where('estado', $filtros['estado']);
                $aseos->where('estado', $filtros['estado']);
                $incidencias->where('estado', $filtros['estado']);
            }
            if (! empty($filtros['urgencia'])) {
                $inspecciones->where('prioridad', $filtros['urgencia']);
                $incidencias->where('urgencia', $filtros['urgencia']);
                $aseos->whereRaw('1 = 0');
            }
            $inspecciones->orderBy('fecha_reporte')->chunk(200, function ($items) use ($writeRow, &$rowNumber) {
                foreach ($items as $item) $writeRow([$item->fecha_reporte?->format('Y-m-d H:i'), 'Inspección', $item->unidad?->etiqueta_economico, $item->municipio ?: $item->direccion, $item->tipo_dano, $item->dictamen ?: $item->estado, $item->prioridad, $item->inspector?->name ?: $item->inspector_responsable, $item->descripcion], $rowNumber++);
            });
            $aseos->orderBy('fecha_hora')->chunk(200, function ($items) use ($writeRow, &$rowNumber) {
                foreach ($items as $item) $writeRow([$item->fecha_hora?->format('Y-m-d H:i'), 'Aseo', $item->unidad?->etiqueta_economico, $item->area_terminal, $item->tipo_limpieza, $item->estado, '', $item->intendente_responsable, $item->observaciones ?: $item->producto_desinfectante], $rowNumber++);
            });
            $incidencias->orderBy('created_at')->chunk(200, function ($items) use ($writeRow, &$rowNumber) {
                foreach ($items as $item) $writeRow([$item->created_at?->format('Y-m-d H:i'), 'Incidencia', $item->eco ?: $item->unidad?->etiqueta_economico, $item->area ?: $item->area_reportada, $item->tipo_queja, $item->estado, $item->urgencia, $item->operador?->name, $item->descripcion], $rowNumber++);
            });
            fwrite($sheet, '</sheetData></worksheet>');
            fclose($sheet);

            $archive = new \ZipArchive();
            if ($archive->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                @unlink($zipPath); @unlink($sheetPath);
                abort(500, 'No se pudo crear el archivo de Excel.');
            }
            $archive->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
            $archive->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
            $archive->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Reportes operativos" sheetId="1" r:id="rId1"/></sheets></workbook>');
            $archive->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
            $archive->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF018FC7"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0"/></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>');
            $archive->addFile($sheetPath, 'xl/worksheets/sheet1.xml');
            $archive->close();
            readfile($zipPath);
            @unlink($zipPath); @unlink($sheetPath);
        }, 'rodagest-reporte-' . $desde->toDateString() . '-a-' . $hasta->toDateString() . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
