<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MarketingOtrasSedesController extends Controller
{
    public function index(Request $request)
    {
        $gestion = $request->get('gestion', date('Y'));
        $search = trim($request->get('search', ''));

        $query = DB::table('programas_otras_sedes')
            ->where('programas_otras_sedes.gestion', $gestion)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('programas_otras_sedes.nombre', 'like', "%{$search}%")
                        ->orWhere('programas_otras_sedes.codigo', 'like', "%{$search}%");
                });
            })
            ->select('programas_otras_sedes.*')
            ->orderByDesc('programas_otras_sedes.id')
            ->get();

        $counts = DB::table('inscripciones_otras_sedes')
            ->whereIn('programa_id', $query->pluck('id'))
            ->select(
                'programa_id',
                DB::raw('COUNT(*) as inscritos'),
                DB::raw("COALESCE(SUM(CASE WHEN estado = 1 THEN monto_total ELSE 0 END), 0) as total_cobrado")
            )
            ->groupBy('programa_id')
            ->get()
            ->keyBy('programa_id');

        foreach ($query as $p) {
            $c = $counts->get($p->id);
            $p->inscritos = $c->inscritos ?? 0;
            $p->total_cobrado = (float) ($c->total_cobrado ?? 0);
        }

        $gestiones = DB::table('programas_otras_sedes')
            ->select(DB::raw('DISTINCT gestion'))
            ->orderByDesc('gestion')
            ->pluck('gestion');

$resumen = [
            'programas' => $query->count(),
            'inscritos' => $query->sum('inscritos'),
            'cobrado' => $query->sum('total_cobrado'),
        ];

        $tipos = DB::table('tipos')->orderBy('nombre')->get(['id', 'nombre', 'puntos']);

        return view('admin.marketing.otras-sedes.index', compact('query', 'gestion', 'search', 'gestiones', 'resumen', 'tipos'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePrograma($request);
        $data = $this->setTipoNombre($data);
        $data['gestion'] = $data['gestion'] ?: date('Y');
        $data['inscripciones_habilitadas'] = $request->boolean('inscripciones_habilitadas', true);
        $data['estado'] = $request->boolean('estado', true);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('programas_otras_sedes')->insert($data);

        return redirect()->route('admin.marketing.programas-otras-sedes.index')
            ->with('success', 'Programa de otra sede registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $programa = DB::table('programas_otras_sedes')->where('id', $id)->first();
        abort_if(!$programa, 404, 'Programa no encontrado');

        $data = $this->validatePrograma($request);
        $data = $this->setTipoNombre($data);
        $data['gestion'] = ($data['gestion'] ?? '') !== '' ? $data['gestion'] : $programa->gestion;
        $data['inscripciones_habilitadas'] = $request->boolean('inscripciones_habilitadas', (int)$programa->inscripciones_habilitadas === 1);
        $data['estado'] = $request->boolean('estado', (int)$programa->estado === 1);
        $data['updated_at'] = now();

        DB::table('programas_otras_sedes')->where('id', $id)->update($data);

        return redirect()->route('admin.marketing.programas-otras-sedes.index')
            ->with('success', 'Programa actualizado correctamente.');
    }

    public function destroy($id)
    {
        $programa = DB::table('programas_otras_sedes')->where('id', $id)->first();
        abort_unless($programa, 404, 'Programa no encontrado');

        $inscripciones = DB::table('inscripciones_otras_sedes')->where('programa_id', $id)->get();
        foreach ($inscripciones as $i) {
            $this->borrarArchivos($i);
        }
        DB::table('inscripciones_otras_sedes')->where('programa_id', $id)->delete();
        DB::table('programas_otras_sedes')->where('id', $id)->delete();

        return redirect()->route('admin.marketing.programas-otras-sedes.index')
            ->with('success', 'Programa eliminado correctamente.');
    }

    public function registro($id)
    {
        $programa = DB::table('programas_otras_sedes')->where('id', $id)->first();
        abort_unless($programa, 404, 'Programa no encontrado');

        $inscripciones = DB::table('inscripciones_otras_sedes')
            ->leftJoin('participantes', 'participantes.id', '=', 'inscripciones_otras_sedes.participante_id')
            ->leftJoin('asesores', 'asesores.id', '=', 'inscripciones_otras_sedes.asesor_id')
            ->where('inscripciones_otras_sedes.programa_id', $id)
            ->select(
                'inscripciones_otras_sedes.*',
                'participantes.ci',
                'participantes.nombres as p_nombres',
                'participantes.apellidos as p_apellidos',
                'asesores.nombres as a_nombres',
                'asesores.apellidos as a_apellidos'
            )
            ->orderByDesc('inscripciones_otras_sedes.id')
            ->get();

        $asesores = DB::table('asesores')
            ->where('estado', 'Habilitado')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos', 'ci']);

        return view('admin.marketing.otras-sedes.registro', compact('programa', 'inscripciones', 'asesores'));
    }

    public function buscarParticipante(Request $request)
    {
        $ci = trim($request->get('ci', ''));
        if ($ci === '') {
            return response()->json(['success' => false, 'message' => 'Ingrese número de CI']);
        }

        $p = DB::table('participantes')->where('ci', $ci)->first();

        if ($p) {
            return response()->json(['success' => true, 'existe' => true, 'participante' => $p]);
        }

        return response()->json(['success' => true, 'existe' => false, 'message' => 'No existe un participante con ese CI. Complete los datos para registrarlo.']);
    }

    public function inscribir(Request $request, $id)
    {
        $programa = DB::table('programas_otras_sedes')->where('id', $id)->first();
        abort_unless($programa, 404, 'Programa no encontrado');

        if ((int)$programa->inscripciones_habilitadas !== 1) {
            return redirect()->back()->withErrors(['inscripcion' => 'Las inscripciones de este programa están inhabilitadas.']);
        }

        $validated = $request->validate([
            'ci' => 'required|string|max:10',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'sexo' => 'nullable|in:Hombre,Mujer',
            'celular' => 'nullable|string|max:11',
            'correo' => 'nullable|email|max:200',
            'departamento' => 'nullable|string|max:200',
            'asesor_id' => 'nullable|integer|exists:asesores,id',
            'contado' => 'nullable',
            'fecha_inscripcion' => 'nullable|date',
            'monto_total' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string',
            'recibo' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'factura' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $participante = DB::table('participantes')->where('ci', $validated['ci'])->first();

        if (!$participante) {
            $participanteId = DB::table('participantes')->insertGetId([
                'ci' => $validated['ci'],
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'sexo' => $validated['sexo'] ?? 'Hombre',
                'celular' => $validated['celular'] ?? '',
                'correo' => $validated['correo'] ?? '',
                'departamento' => $validated['departamento'] ?? 'Sin Datos',
            ]);
        } else {
            $participanteId = $participante->id;
        }

        $folder = "otras_sedes/" . ($programa->gestion ?: date('Y')) . "/{$id}";
        $batch = time();

        $recibo = $request->file('recibo');
        $reciboPath = $recibo->storeAs($folder, "recibo_{$participanteId}_{$batch}." . $recibo->extension(), 'public');

        $factura = $request->file('factura');
        $facturaPath = $factura->storeAs($folder, "factura_{$participanteId}_{$batch}." . $factura->extension(), 'public');

        DB::table('inscripciones_otras_sedes')->insert([
            'programa_id' => $id,
            'participante_id' => $participanteId,
            'asesor_id' => $request->filled('asesor_id') ? $request->input('asesor_id') : null,
            'fecha_inscripcion' => $request->filled('fecha_inscripcion') ? $request->input('fecha_inscripcion') : date('Y-m-d'),
            'contado' => $request->boolean('contado'),
            'estado' => 1,
            'monto_total' => (float) ($request->input('monto_total') ?? 0),
            'recibo_path' => $reciboPath,
            'recibo_original' => $recibo->getClientOriginalName(),
            'factura_path' => $facturaPath,
            'factura_original' => $factura->getClientOriginalName(),
            'observacion' => $validated['observacion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.marketing.programas-otras-sedes.registro', $id)
            ->with('success', 'Participante inscrito correctamente en ' . $programa->nombre . '.');
    }

    public function eliminarInscripcion($inscripcion)
    {
        $row = DB::table('inscripciones_otras_sedes')->where('id', $inscripcion)->first();
        abort_unless($row, 404, 'Inscripción no encontrada');

        $this->borrarArchivos($row);
        DB::table('inscripciones_otras_sedes')->where('id', $inscripcion)->delete();

        return redirect()->back()->with('success', 'Inscripción eliminada correctamente.');
    }

    private function setTipoNombre(array $data)
    {
        if (!empty($data['tipo_id'])) {
            $data['tipo'] = DB::table('tipos')->where('id', $data['tipo_id'])->value('nombre');
        }
        return $data;
    }

    private function validatePrograma(Request $request)
    {
        return $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:200',
            'gestion' => 'nullable|string|max:4',
            'sede_origen' => 'nullable|string|max:150',
            'tipo' => 'nullable|string|max:120',
            'tipo_id' => 'nullable|integer|exists:tipos,id',
            'modalidad' => 'nullable|string|max:80',
            'matricula' => 'nullable|numeric|min:0',
            'colegiatura' => 'nullable|numeric|min:0',
            'n_modulos' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'observacion' => 'nullable|string',
        ]);
    }

    private function borrarArchivos($row)
    {
        if (!empty($row->recibo_path)) {
            Storage::disk('public')->delete($row->recibo_path);
        }
        if (!empty($row->factura_path)) {
            Storage::disk('public')->delete($row->factura_path);
        }
    }
}