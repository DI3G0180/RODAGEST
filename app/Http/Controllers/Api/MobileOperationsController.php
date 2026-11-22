<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileOperationsController extends Controller
{
    public function units(Request $request)
    {
        $query = Unidad::with('operador:id,name')->orderBy('economico');
        if ($request->user()->role === 'operador') $query->where('operador_id', $request->user()->id);
        return response()->json(['data'=>$query->paginate(20),'message'=>'Unidades consultadas.']);
    }

    public function inspections(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'unidad_id'=>['required','exists:unidades,id'], 'tipo_dano'=>['required','string','max:50'],
            'descripcion'=>['required','string','max:500'], 'prioridad'=>['required','in:baja,media,alta,critica'],
            'dictamen'=>['required','in:operativa,en_taller,fuera_servicio'], 'fecha_reporte'=>['required','date'],
            'checklist'=>['required','array'], 'foto'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
        ]);
        $path = $request->file('foto')?->store('inspecciones/evidencias','public');
        $inspection = Inspeccion::create([
            'unidad_id'=>$data['unidad_id'],'tipo_dano'=>$data['tipo_dano'],'descripcion'=>$data['descripcion'],
            'prioridad'=>$data['prioridad'],'dictamen'=>$data['dictamen'],'fecha_reporte'=>$data['fecha_reporte'],
            'checklist'=>$data['checklist'],'evidencia_foto'=>$path,'inspector_id'=>$user->id,
            'inspector_responsable'=>$user->name,'estado'=>'pendiente',
        ]);
        return response()->json(['data'=>$inspection->load('unidad'),'message'=>'Inspección guardada.'],201);
    }

    public function reports(Request $request)
    {
        $user = $request->user();
        return response()->json(['data'=>Queja::with('unidad')->where('operador_id',$user->id)->latest()->paginate(20),'message'=>'Reportes consultados.']);
    }

    public function storeReport(Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['tipo_queja'=>['required','string','max:50'],'descripcion'=>['required','string','max:500'],'urgencia'=>['required','in:normal,alta,critica'],'foto'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120']]);
        $unit = $user->unidadAsignada;
        if (! $unit) return response()->json(['message'=>'No tienes una unidad asignada.'],422);
        $path = $request->file('foto')?->store('quejas','public');
        $report = Queja::create(['unidad_id'=>$unit->id,'operador_id'=>$user->id,'tipo_queja'=>$data['tipo_queja'],'descripcion'=>$data['descripcion'],'urgencia'=>$data['urgencia'],'evidencia_foto'=>$path,'estado'=>'pendiente','medio_reporte'=>'app']);
        return response()->json(['data'=>$report,'message'=>'Reporte guardado.'],201);
    }

    public function aseos(Request $request)
    {
        return response()->json(['data'=>Aseo::with('unidad')->latest()->paginate(20),'message'=>'Aseos consultados.']);
    }

    public function storeAseo(Request $request)
    {
        $data = $request->validate(['unidad_id'=>['required','exists:unidades,id'],'tipo_limpieza'=>['required','string','max:100'],'area_terminal'=>['nullable','string','max:150'],'fecha_hora'=>['required','date'],'observaciones'=>['nullable','string','max:2000'],'foto'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120']]);
        $path = $request->file('foto')?->store('aseos','public');
        $aseo = Aseo::create(['unidad_id'=>$data['unidad_id'],'tipo_limpieza'=>$data['tipo_limpieza'],'area_terminal'=>$data['area_terminal'] ?? 'Patio','fecha_hora'=>$data['fecha_hora'],'observaciones'=>$data['observaciones'] ?? null,'comentarios'=>$data['observaciones'] ?? null,'intendente_responsable'=>$request->user()->name,'estado'=>'en_proceso','etapas'=>[],'evidencia_foto'=>$path]);
        $aseo->unidad()->update(['estado'=>'en_limpieza']);
        return response()->json(['data'=>$aseo->load('unidad'),'message'=>'Aseo guardado.'],201);
    }
}
