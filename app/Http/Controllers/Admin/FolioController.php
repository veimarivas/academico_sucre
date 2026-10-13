<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolioController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('folios')
            ->select('folios.*')
            ->selectRaw('(SELECT COUNT(*) FROM folio_participantes WHERE folio_participantes.folio_id = folios.id) as participantes_count');

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('folios.nombre', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('folios.status', $status);
        }

        $folios = $query->orderBy('folios.created_at', 'desc')->get();

        $resumen = [
            'total' => $folios->count(),
            'abiertos' => $folios->where('status', 'abierto')->count(),
            'cerrados' => $folios->where('status', 'cerrado')->count(),
            'total_participantes' => (int) $folios->sum('participantes_count'),
        ];

        if ($request->ajax()) {
            $html = view('admin.folios.partials.table', compact('folios'))->render();
            return response()->json(compact('html', 'resumen'));
        }

        return view('admin.folios.index', compact('folios', 'resumen'));
    }

    public function create()
    {
        return redirect()->route('admin.folios.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'fecha_registro' => 'required|date',
            'conceptos' => 'required|array|min:1',
            'conceptos.*' => 'required|string|in:Inscripcion,Matricula,Colegiatura,Certificacion',
            'participantes' => 'nullable|array',
            'participantes.*' => 'integer|exists:participantes,id',
        ]);

        $conceptosSel = $validated['conceptos'];
        $participantesRaw = $request->input('participantes');
        $participantes = $participantesRaw ? array_unique($participantesRaw) : [];
        $cant = count($participantes);

        $totalInsc = 0;
        $totalMat = 0;
        $totalCol = 0;
        $totalCert = 0;
        $rows = [];

        if ($cant > 0) {
            $programasDeParticipantes = DB::table('inscripciones')
                ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
                ->whereIn('inscripciones.participante_id', $participantes)
                ->select('inscripciones.participante_id', 'programas.*')
                ->get()
                ->keyBy('participante_id');

            $conceptToColumn = [
                'Inscripcion' => 'monto_inscripcion',
                'Matricula' => 'monto_matricula',
                'Colegiatura' => 'monto_colegiatura',
                'Certificacion' => 'monto_certificacion',
            ];

            foreach ($participantes as $pid) {
                $prog = $programasDeParticipantes->get($pid);
                $costoInsc = (float) ($prog->costo_pago_inscripcion_universidad ?? 0);
                $costoMat = (float) ($prog->costo_pago_matricula_universidad ?? 0);
                $costoCol = (float) ($prog->costo_pago_colegiatura_universidad ?? 0);
                $costoCert = (float) ($prog->costo_pago_certificacion_universidad ?? 0);

                $row = ['folio_id' => 0, 'participante_id' => $pid];
                foreach ($conceptToColumn as $concept => $column) {
                    $costo = match ($concept) {
                        'Inscripcion' => $costoInsc,
                        'Matricula' => $costoMat,
                        'Colegiatura' => $costoCol,
                        'Certificacion' => $costoCert,
                    };
                    $row[$column] = in_array($concept, $conceptosSel) ? $costo : 0;
                }
                $rows[] = $row;

                $totalInsc += $row['monto_inscripcion'];
                $totalMat += $row['monto_matricula'];
                $totalCol += $row['monto_colegiatura'];
                $totalCert += $row['monto_certificacion'];
            }
        }

        $totalGeneral = $totalInsc + $totalMat + $totalCol + $totalCert;

        $folioId = DB::table('folios')->insertGetId([
            'programa_id' => null,
            'nombre' => $validated['nombre'],
            'fecha_registro' => $validated['fecha_registro'],
            'concepto' => json_encode($conceptosSel),
            'status' => 'abierto',
            'total_inscripcion' => $totalInsc,
            'total_matricula' => $totalMat,
            'total_colegiatura' => $totalCol,
            'total_certificacion' => $totalCert,
            'total_general' => $totalGeneral,
            'cantidad_participantes' => $cant,
        ]);

        if ($cant > 0) {
            foreach ($rows as &$r) {
                $r['folio_id'] = $folioId;
            }
            DB::table('folio_participantes')->insert($rows);
        }

        return response()->json([
            'success' => true,
            'message' => 'Folio creado correctamente',
        ]);
    }

    public function show($id)
    {
        $folio = DB::table('folios')->where('id', $id)->first();
        if (!$folio) {
            abort(404);
        }

        $participantes = DB::table('folio_participantes')
            ->join('participantes', 'folio_participantes.participante_id', '=', 'participantes.id')
            ->leftJoin('inscripciones', 'participantes.id', '=', 'inscripciones.participante_id')
            ->leftJoin('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->where('folio_participantes.folio_id', $id)
            ->select(
                'participantes.id',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.ci',
                DB::raw('GROUP_CONCAT(DISTINCT programas.nombre SEPARATOR ", ") as programa_nombre'),
                DB::raw('MAX(folio_participantes.monto_inscripcion) as monto_inscripcion'),
                DB::raw('MAX(folio_participantes.monto_matricula) as monto_matricula'),
                DB::raw('MAX(folio_participantes.monto_colegiatura) as monto_colegiatura'),
                DB::raw('MAX(folio_participantes.monto_certificacion) as monto_certificacion')
            )
            ->groupBy('participantes.id', 'participantes.nombres', 'participantes.apellidos', 'participantes.ci')
            ->get();

        return view('admin.folios.show', compact('folio', 'participantes'));
    }

    public function edit($id)
    {
        $folio = DB::table('folios')->where('id', $id)->first();
        if (!$folio) {
            abort(404);
        }

        $participantesEnFolio = DB::table('folio_participantes')
            ->where('folio_id', $id)
            ->pluck('participante_id')
            ->toArray();

        $programas = DB::table('programas')
            ->join('tipos', 'programas.tipo_id', '=', 'tipos.id')
            ->select('programas.id', 'programas.nombre', 'tipos.nombre as tipo_nombre')
            ->orderBy('programas.nombre')
            ->get();

        $participantes = DB::table('participantes')
            ->join('inscripciones', 'participantes.id', '=', 'inscripciones.participante_id')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->select(
                'participantes.id',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.ci',
                'inscripciones.programa_id',
                'programas.nombre as programa_nombre'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->groupBy('programa_id');

        $conceptosRegistrados = DB::table('folio_participantes')
            ->join('folios', 'folio_participantes.folio_id', '=', 'folios.id')
            ->where('folio_participantes.folio_id', '!=', $id)
            ->select(
                'folio_participantes.participante_id',
                DB::raw("CONCAT_WS(',',
                    IF(monto_inscripcion > 0, 'Inscripcion', NULL),
                    IF(monto_matricula > 0, 'Matricula', NULL),
                    IF(monto_colegiatura > 0, 'Colegiatura', NULL),
                    IF(monto_certificacion > 0, 'Certificacion', NULL)
                ) as conceptos"),
                'folios.nombre as folio_nombre',
                'folios.id as folio_id'
            )
            ->get()
            ->groupBy('participante_id')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'folio_id' => $item->folio_id,
                        'folio_nombre' => $item->folio_nombre,
                        'conceptos' => array_filter(explode(',', $item->conceptos)),
                    ];
                });
            });

        return view('admin.folios.form', compact(
            'folio', 'programas', 'participantes',
            'participantesEnFolio', 'conceptosRegistrados'
        ));
    }

    public function update(Request $request, $id)
    {
        $folio = DB::table('folios')->where('id', $id)->first();
        if (!$folio) {
            return response()->json(['success' => false, 'message' => 'Folio no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'fecha_registro' => 'required|date',
            'status' => 'required|in:abierto,cerrado',
            'conceptos' => 'required|array|min:1',
            'conceptos.*' => 'required|string|in:Inscripcion,Matricula,Colegiatura,Certificacion',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'required|integer|exists:participantes,id',
        ]);

        $programasDeParticipantes = DB::table('inscripciones')
            ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
            ->whereIn('inscripciones.participante_id', $validated['participantes'])
            ->select('inscripciones.participante_id', 'programas.*')
            ->get()
            ->keyBy('participante_id');

        $participantes = array_unique($validated['participantes']);
        $cant = count($participantes);
        $conceptosSel = $validated['conceptos'];

        $conceptToColumn = [
            'Inscripcion' => 'monto_inscripcion',
            'Matricula' => 'monto_matricula',
            'Colegiatura' => 'monto_colegiatura',
            'Certificacion' => 'monto_certificacion',
        ];

        $totalInsc = 0;
        $totalMat = 0;
        $totalCol = 0;
        $totalCert = 0;

        $rows = [];
        foreach ($participantes as $pid) {
            $prog = $programasDeParticipantes->get($pid);
            $costoInsc = (float) ($prog->costo_pago_inscripcion_universidad ?? 0);
            $costoMat = (float) ($prog->costo_pago_matricula_universidad ?? 0);
            $costoCol = (float) ($prog->costo_pago_colegiatura_universidad ?? 0);
            $costoCert = (float) ($prog->costo_pago_certificacion_universidad ?? 0);

            $row = ['folio_id' => $id, 'participante_id' => $pid];
            foreach ($conceptToColumn as $concept => $column) {
                $costo = match ($concept) {
                    'Inscripcion' => $costoInsc,
                    'Matricula' => $costoMat,
                    'Colegiatura' => $costoCol,
                    'Certificacion' => $costoCert,
                };
                $row[$column] = in_array($concept, $conceptosSel) ? $costo : 0;
            }
            $rows[] = $row;

            $totalInsc += $row['monto_inscripcion'];
            $totalMat += $row['monto_matricula'];
            $totalCol += $row['monto_colegiatura'];
            $totalCert += $row['monto_certificacion'];
        }

        $totalGeneral = $totalInsc + $totalMat + $totalCol + $totalCert;

        DB::table('folios')->where('id', $id)->update([
            'nombre' => $validated['nombre'],
            'fecha_registro' => $validated['fecha_registro'],
            'status' => $validated['status'],
            'concepto' => json_encode($conceptosSel),
            'total_inscripcion' => $totalInsc,
            'total_matricula' => $totalMat,
            'total_colegiatura' => $totalCol,
            'total_certificacion' => $totalCert,
            'total_general' => $totalGeneral,
            'cantidad_participantes' => $cant,
        ]);

        DB::table('folio_participantes')->where('folio_id', $id)->delete();
        DB::table('folio_participantes')->insert($rows);

        return response()->json([
            'success' => true,
            'message' => 'Folio actualizado correctamente',
        ]);
    }

    public function destroy($id)
    {
        $folio = DB::table('folios')->where('id', $id)->first();
        if (!$folio) {
            return response()->json(['success' => false, 'message' => 'Folio no encontrado'], 404);
        }

        DB::table('folio_participantes')->where('folio_id', $id)->delete();
        DB::table('folios')->where('id', $id)->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Folio eliminado correctamente',
            ]);
        }

        return redirect()->route('admin.folios.index')
            ->with('success', 'Folio eliminado correctamente');
    }

    public function getProgramasConCostos()
    {
        $programas = DB::table('programas')
            ->select(
                'id',
                'nombre',
                'costo_pago_inscripcion_universidad',
                'costo_pago_matricula_universidad',
                'costo_pago_colegiatura_universidad',
                'costo_pago_certificacion_universidad'
            )
            ->orderBy('nombre')
            ->get();

        return response()->json($programas);
    }
}
