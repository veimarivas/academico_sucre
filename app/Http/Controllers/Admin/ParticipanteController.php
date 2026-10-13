<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipanteController extends Controller
{
    public function index(Request $request)
    {
        $gestiones = DB::table('programas')
            ->select('gestion')
            ->distinct()
            ->orderBy('gestion', 'desc')
            ->pluck('gestion');

        $gestionActual = (int) date('Y');

        $programas = DB::table('programas')
            ->select('id', 'nombre', 'gestion')
            ->orderBy('gestion', 'desc')
            ->orderBy('nombre')
            ->get();

        if ($request->ajax()) {
            $page = (int) $request->get('page', 1);
            $perPage = 20;

            $idQuery = DB::table('participantes as p')
                ->leftJoin('inscripciones as i', 'p.id', '=', 'i.participante_id')
                ->leftJoin('programas as pr', 'i.programa_id', '=', 'pr.id');

            if ($search = $request->get('search')) {
                $idQuery->where(function ($q) use ($search) {
                    $q->where('p.ci', 'like', "%{$search}%")
                      ->orWhere('p.nombres', 'like', "%{$search}%")
                      ->orWhere('p.apellidos', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(p.apellidos, ' ', p.nombres) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CONCAT(p.nombres, ' ', p.apellidos) LIKE ?", ["%{$search}%"]);
                });
            }

            if ($gestion = $request->get('gestion')) {
                $idQuery->where('pr.gestion', $gestion);
            }

            if ($programaId = $request->get('programa_id')) {
                $idQuery->where('pr.id', $programaId);
            }

            $countQuery = clone $idQuery;
            $total = $countQuery->distinct()->count('p.id');

            $participanteIds = $idQuery
                ->select('p.id')
                ->groupBy('p.id')
                ->orderBy('p.apellidos')
                ->orderBy('p.nombres')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->pluck('p.id');

            $participantes = collect();
            if ($participanteIds->isNotEmpty()) {
                $participantes = DB::table('participantes as p')
                    ->leftJoin('inscripciones as i', 'p.id', '=', 'i.participante_id')
                    ->leftJoin('programas as pr', 'i.programa_id', '=', 'pr.id')
                    ->whereIn('p.id', $participanteIds)
                    ->select(
                        'p.id',
                        'p.ci',
                        'p.nombres',
                        'p.apellidos',
                        'p.sexo',
                        'p.celular',
                        'p.correo',
                        'p.departamento',
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT(pr.nombre, " (", pr.gestion, ")") SEPARATOR "||") as programas')
                    )
                    ->groupBy('p.id', 'p.ci', 'p.nombres', 'p.apellidos', 'p.sexo', 'p.celular', 'p.correo', 'p.departamento')
                    ->orderBy('p.apellidos')
                    ->orderBy('p.nombres')
                    ->get();

                $participantes->transform(function ($p) {
                    $p->programas = $p->programas ? explode('||', $p->programas) : [];
                    return $p;
                });
            }

            $lastPage = (int) ceil($total / $perPage);

            $html = view('admin.participantes.partials.table', compact('participantes', 'page', 'lastPage', 'total'))->render();
            return response()->json([
                'html' => $html,
                'total' => $total,
                'page' => $page,
                'lastPage' => $lastPage,
            ]);
        }

        return view('admin.participantes.index', compact('gestiones', 'gestionActual', 'programas'));
    }

    public function edit($id)
    {
        $participante = DB::table('participantes')->where('id', $id)->first();

        if (!$participante) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'error' => 'Participante no encontrado.']);
            }
            return redirect()->route('admin.participantes.index')
                ->with('error', 'Participante no encontrado.');
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'participante' => $participante]);
        }

        return view('admin.participantes.edit', compact('participante'));
    }

    public function update(Request $request, $id)
    {
        $participante = DB::table('participantes')->where('id', $id)->first();

        if (!$participante) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Participante no encontrado.']);
            }
            return redirect()->route('admin.participantes.index')
                ->with('error', 'Participante no encontrado.');
        }

        try {
            $validated = $request->validate([
                'nombres'   => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'ci'        => 'required|string|max:13',
                'sexo'      => 'nullable|in:M,F,Hombre,Mujer,Masculino,Femenino',
                'celular'   => 'nullable|string|max:20',
                'correo'    => 'nullable|email|max:150',
                'departamento' => 'nullable|string|max:100',
            ], [
                'nombres.required'   => 'El nombre es obligatorio.',
                'apellidos.required' => 'El apellido es obligatorio.',
                'ci.required'        => 'El CI es obligatorio.',
                'ci.max'             => 'El CI no debe exceder 13 caracteres.',
                'correo.email'       => 'Ingrese un correo electrónico válido.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()]);
            }
            throw $e;
        }

        DB::table('participantes')->where('id', $id)->update([
            'nombres'      => trim($validated['nombres']),
            'apellidos'    => trim($validated['apellidos']),
            'ci'           => trim($validated['ci']),
            'sexo'         => $this->normalizarSexo($validated['sexo'] ?? null) ?? ($participante->sexo ?: 'Hombre'),
            'celular'      => trim($validated['celular'] ?? ''),
            'correo'       => trim($validated['correo'] ?? ''),
            'departamento' => trim($validated['departamento'] ?? ''),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Participante actualizado correctamente.']);
        }

        return redirect()->route('admin.participantes.index')
            ->with('success', 'Participante actualizado correctamente.');
    }

    /** Normaliza el sexo a los valores del enum de la BD ('Hombre'/'Mujer'). */
    private function normalizarSexo(?string $sexo): ?string
    {
        $valor = strtoupper(trim((string) $sexo));

        if ($valor === '') {
            return null;
        }

        if (in_array($valor, ['M', 'H', 'HOMBRE', 'MASCULINO', 'MALE'], true)) {
            return 'Hombre';
        }

        if (in_array($valor, ['F', 'MUJER', 'FEMENINO', 'FEMALE'], true)) {
            return 'Mujer';
        }

        return null;
    }

    public function buscar(Request $request)
    {
        $canCobrar = auth()->user()->can('contable.cobrar');

        $ci = $request->input('ci');
        $participante = null;
        $programas = collect();

        if ($ci && mb_strlen($ci) >= 7 && mb_strlen($ci) <= 13) {
            $participante = DB::table('participantes')
                ->where('ci', $ci)
                ->select('id', 'ci', 'nombres', 'apellidos', 'sexo', 'celular', 'correo', 'departamento')
                ->first();

            if ($participante) {
                $inscripciones = DB::table('inscripciones')
                    ->join('programas', 'inscripciones.programa_id', '=', 'programas.id')
                    ->leftJoin('tipos', 'programas.tipo_id', '=', 'tipos.id')
                    ->leftJoin('estados', 'programas.estado_id', '=', 'estados.id')
                    ->where('inscripciones.participante_id', $participante->id)
                    ->select(
                        'inscripciones.id as inscripcion_id',
                        'inscripciones.programa_id',
                        'programas.nombre as programa_nombre',
                        'programas.gestion',
                        'programas.codigo',
                        'tipos.nombre as tipo_nombre',
                        'estados.nombre as estado_nombre',
                        'inscripciones.matricula',
                        'inscripciones.monto_programa',
                        'inscripciones.descuento_matricula',
                        'inscripciones.descuento_colegiatura',
                        'inscripciones.deuda_matricula',
                        'inscripciones.deuda',
                        'inscripciones.contado',
                        'inscripciones.fecha_inscripcion',
                        'inscripciones.estado'
                    )
                    ->orderBy('programas.gestion', 'desc')
                    ->orderBy('programas.nombre')
                    ->get();

                foreach ($inscripciones as $ins) {
                    $moduloIds = DB::table('modulos')
                        ->where('programa_id', $ins->programa_id)
                        ->pluck('id');

                    $cuotas = DB::table('modulos_participantes')
                        ->where('participante_id', $participante->id)
                        ->whereIn('modulo_id', $moduloIds)
                        ->select('id', 'tipo', 'deuda', 'descuento', 'total_pagado', 'fecha_pago')
                        ->orderBy('id')
                        ->get()
                        ->map(function ($c) {
                            $saldo = max(0, $c->deuda - $c->total_pagado);
                            return [
                                'id' => $c->id,
                                'tipo' => $c->tipo,
                                'total_cuota' => (float) $c->deuda,
                                'pagado' => (float) $c->total_pagado,
                                'saldo' => $saldo,
                                'fecha_pago' => $c->fecha_pago,
                            ];
                        });

                    $totalPagadoMatricula = $cuotas->where('tipo', 'Matricula')->sum('pagado');
                    $totalPagadoColegiatura = $cuotas->where('tipo', 'Colegiatura')->sum('pagado');
                    $totalDeudaMatricula = $cuotas->where('tipo', 'Matricula')->sum('saldo');
                    $totalDeudaColegiatura = $cuotas->where('tipo', 'Colegiatura')->sum('saldo');
                    $tieneVencidas = $cuotas->filter(function ($c) {
                        if ($c['saldo'] <= 0 || !$c['fecha_pago']) return false;
                        return \Carbon\Carbon::parse($c['fecha_pago'])->startOfDay()->diffInDays(now(), false) < 0;
                    })->isNotEmpty();

                    $programas->push([
                        'inscripcion_id' => $ins->inscripcion_id,
                        'programa_id' => $ins->programa_id,
                        'programa_nombre' => $ins->programa_nombre,
                        'gestion' => $ins->gestion,
                        'codigo' => $ins->codigo,
                        'tipo_nombre' => $ins->tipo_nombre,
                        'estado_nombre' => $ins->estado_nombre,
                        'matricula' => (float) $ins->matricula,
                        'monto_programa' => (float) $ins->monto_programa,
                        'descuento_matricula' => (float) ($ins->descuento_matricula ?? 0),
                        'descuento_colegiatura' => (float) ($ins->descuento_colegiatura ?? 0),
                        'contado' => $ins->contado,
                        'fecha_inscripcion' => $ins->fecha_inscripcion,
                        'inscripcion_estado' => $ins->estado,
                        'total_pagado_matricula' => $totalPagadoMatricula,
                        'total_pagado_colegiatura' => $totalPagadoColegiatura,
                        'total_deuda_matricula' => $totalDeudaMatricula,
                        'total_deuda_colegiatura' => $totalDeudaColegiatura,
                        'total_deuda' => $totalDeudaMatricula + $totalDeudaColegiatura,
                        'tiene_vencidas' => $tieneVencidas,
                        'cuotas' => $cuotas,
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            if (!$participante) {
                return response()->json(['found' => false]);
            }
            return response()->json([
                'found' => true,
                'can_cobrar' => $canCobrar,
                'participante' => $participante,
                'programas' => $programas,
            ]);
        }

        return view('admin.participantes.buscar', compact('canCobrar'));
    }
}
