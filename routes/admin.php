<?php

use App\Http\Controllers\Admin\AjustesWhatsappController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ComprobantePagoController;
use App\Http\Controllers\Admin\ContableController;
use App\Http\Controllers\Admin\ControlResponsablesController;
use App\Http\Controllers\Admin\CronogramaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocenteController;
use App\Http\Controllers\Admin\FolioController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\MarketingOtrasSedesController;
use App\Http\Controllers\Admin\MarketingProspectoController;
use App\Http\Controllers\Admin\MasterclassController;
use App\Http\Controllers\Admin\NotificacionController;
use App\Http\Controllers\Admin\ParticipanteController;
use App\Http\Controllers\Admin\ProfileAdminController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\PublicidadController;
use App\Http\Controllers\Admin\ResponsableController;
use App\Http\Controllers\Admin\RetencionController;
use App\Http\Controllers\Admin\SeguimientoController;
use App\Http\Controllers\Admin\SeguimientoDiarioController;
use App\Http\Controllers\Admin\SeguimientoEsamSsoController;
use App\Http\Controllers\Admin\Seguridad\PermissionController;
use App\Http\Controllers\Admin\Seguridad\RoleController;
use App\Http\Controllers\Admin\Seguridad\UserController as SeguridadUserController;
use App\Http\Controllers\Admin\TipoController;
use App\Http\Controllers\Admin\WhatsappEnvioController;
use App\Http\Controllers\Admin\WhatsappLoteController;
use App\Http\Controllers\Admin\WhatsappProgramaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // === Dashboard (visión general) ===
    Route::middleware('permission:dashboard.listar')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/online-users', [DashboardController::class, 'onlineUsers'])->name('dashboard.online-users');
        Route::get('/dashboard/programa/{id}/participantes', [DashboardController::class, 'programaParticipantes'])->name('dashboard.programa-participantes');
        Route::get('/programas-vigentes', [DashboardController::class, 'programasVigentes'])->name('programas-vigentes');
        Route::get('/programas-vigentes/stats', [DashboardController::class, 'programasVigentesStats'])->name('programas-vigentes.stats');
    });

    // === Programas (Académico) ===
    Route::prefix('programas')->name('programas.')->group(function () {
        Route::middleware('permission:programas.listar')->group(function () {
            Route::get('/', [ProgramaController::class, 'index'])->name('index');
            Route::get('/tipo/{tipo}', [ProgramaController::class, 'byTipo'])->name('tipo');
            Route::get('/stats', [ProgramaController::class, 'stats'])->name('stats');
            Route::get('/{id}/show', [ProgramaController::class, 'show'])->name('show');
            Route::get('/{id}/whatsapp', [WhatsappProgramaController::class, 'index'])->middleware('programa.acceso:responsable')->name('whatsapp.index');
            Route::get('/{id}/data', [ProgramaController::class, 'data'])->name('data');
            Route::get('/{id}/responsables-data', [ProgramaController::class, 'responsablesData'])->name('responsables-data');
            Route::get('/buscar-asesores', [ProgramaController::class, 'buscarAsesores'])->name('buscar-asesores');
            Route::get('/{id}/buscar-participante', [ProgramaController::class, 'buscarParticipante'])->name('buscar-participante');
            Route::get('/{programa}/inscripcion/{inscripcion}/baja-historial', [ProgramaController::class, 'bajaHistorial'])->name('baja-historial');
            Route::get('/{programa}/inscripcion/{inscripcion}/baja-pdf', [ProgramaController::class, 'bajaPdf'])->name('baja-pdf');
            Route::get('/{programa}/inscripcion/{inscripcion}/baja-archivo', [ProgramaController::class, 'bajaArchivo'])->name('baja-archivo');
            Route::get('/horario/{horario}/asistencia', [ProgramaController::class, 'asistenciaSesionData'])->name('asistencia-data');
            Route::get('/horario/{horario}/asistencia-pdf', [ProgramaController::class, 'asistenciaSesionPdf'])->name('asistencia-pdf');
            Route::get('/modulo/{modulo}/asistencia-stats', [ProgramaController::class, 'asistenciaStats'])->name('asistencia-stats');
            Route::get('/notificaciones', [ProgramaController::class, 'notificaciones'])->name('notificaciones');
            Route::get('/modulo/{modulo}/notas-estado', [ProgramaController::class, 'notasEstadoModulo'])->name('modulo-notas-estado');
            Route::get('/modulo/{modulo}/mensajes-whatsapp', [ProgramaController::class, 'mensajesWhatsApp'])->name('modulo-mensajes-whatsapp');
            Route::post('/modulo/{modulo}/whatsapp', [WhatsappEnvioController::class, 'enviarAcademico'])->middleware('programa.acceso:responsable')->name('modulo-whatsapp-enviar');
            Route::get('/modulo/{modulo}/detalle', [ProgramaController::class, 'moduloDetalle'])->name('modulo-detalle');
            Route::get('/modulo/{modulo}/pdf', [ProgramaController::class, 'moduloPdf'])->name('modulo-pdf');
            Route::get('/modulo/{modulo}/pdf-nivelacion', [ProgramaController::class, 'moduloPdfNivelacion'])->name('modulo-pdf-nivelacion');
            Route::get('/{id}/horarios-pdf', [ProgramaController::class, 'horariosPdf'])->name('horarios-pdf');
            Route::get('/{id}/horarios-pdf-docentes', [ProgramaController::class, 'horariosPdfDocentes'])->name('horarios-pdf-docentes');
            Route::get('/{id}/notas-pdf', [ProgramaController::class, 'notasPdf'])->name('notas-pdf');
            Route::get('/pagos-docentes', [ProgramaController::class, 'pagosDocentes'])->middleware('role:Super Admin|Responsable Académico|Contabilidad')->name('pagos-docentes');
            Route::get('/modulo/{modulo}/notas-data', [ProgramaController::class, 'notasDataModulo'])->name('modulo-notas-data');
            Route::get('/{id}/costos-universidad', [ProgramaController::class, 'costosUniversidadData'])->name('costos-universidad-data');
            Route::get('/participante/{participante}/detalle', [ProgramaController::class, 'participanteDetalle'])->name('participante-detalle');
            Route::get('/participante/{participante}/modulo/{modulo}/horarios', [ProgramaController::class, 'participanteModuloHorarios'])->name('participante-modulo-horarios');
            Route::get('/{programa}/participante/{participante}/fechas', [ProgramaController::class, 'getFechasParticipante'])->name('participante-fechas-get');
            Route::get('/{programa}/folios', [ProgramaController::class, 'getFolios'])->name('folios');
        });

        // Envío a varios grupos de WhatsApp (programas por estado)
        Route::get('/whatsapp-grupos', [WhatsappProgramaController::class, 'grupos'])->middleware('permission:programas.listar')->name('whatsapp.grupos');
        Route::post('/whatsapp-grupos/enviar', [WhatsappProgramaController::class, 'enviarGrupos'])->middleware('permission:programas.listar')->name('whatsapp.grupos-enviar');

        // Envío por WhatsApp (grupo del programa y participantes seleccionados)
        Route::post('/{programa}/whatsapp/grupo', [WhatsappProgramaController::class, 'guardarGrupo'])->middleware('permission:whatsapp.admin')->name('whatsapp.grupo');
        Route::post('/{programa}/whatsapp/grupo/resolver', [WhatsappProgramaController::class, 'resolverGrupo'])->middleware('permission:whatsapp.admin')->name('whatsapp.grupo-resolver');
        Route::post('/{programa}/whatsapp/grupo/enviar', [WhatsappProgramaController::class, 'enviarGrupo'])->middleware('programa.acceso:responsable')->name('whatsapp.grupo-enviar');
        Route::post('/{programa}/whatsapp/participantes', [WhatsappProgramaController::class, 'enviarParticipantes'])->middleware('programa.acceso:responsable')->name('whatsapp.participantes');

        // Inscripciones
        Route::post('/{id}/registrar-participante', [ProgramaController::class, 'registrarParticipante'])->middleware('permission:inscripciones.registrar')->name('registrar-participante');
        Route::post('/{id}/inscribir-participante', [ProgramaController::class, 'inscribirParticipante'])->middleware('permission:inscripciones.registrar')->name('inscribir-participante');
        Route::put('/{programa}/inscripcion/{inscripcion}', [ProgramaController::class, 'updateInscripcion'])->middleware('role:Super Admin')->name('update-inscripcion');
        Route::delete('/{programa}/inscripcion/{inscripcion}', [ProgramaController::class, 'eliminarInscripcion'])->middleware('role:Super Admin')->name('eliminar-inscripcion');
        Route::post('/{programa}/inscripcion/{inscripcion}/toggle-baja', [ProgramaController::class, 'toggleBaja'])->middleware('programa.acceso:baja')->name('toggle-baja');
        Route::post('/{id}/toggle-inscripciones', [ProgramaController::class, 'toggleInscripciones'])->middleware('permission:inscripciones.cambiarestado')->name('toggle-inscripciones');

        // Módulos y horarios
        Route::post('/{id}/crear-modulos', [ProgramaController::class, 'crearModulos'])->middleware('permission:programas.modulos.agregar')->name('crear-modulos');
        Route::post('/{id}/crear-horarios', [ProgramaController::class, 'crearHorarios'])->middleware('permission:programas.modulos.registrar.horarios')->name('crear-horarios');
        Route::post('/{id}/asignar-responsables', [ProgramaController::class, 'asignarResponsables'])->middleware('role:Super Admin')->name('asignar-responsables');
        Route::post('/{id}/rebalancear-colegiaturas', [ProgramaController::class, 'rebalancearColegiaturas'])->middleware('role:Super Admin|Administrador')->name('rebalancear-colegiaturas');
        Route::post('/sesion-extra', [ProgramaController::class, 'storeSesionExtra'])->middleware('permission:programas.modulos.sesion.extra')->name('sesion-extra');
        Route::post('/horario-estado', [ProgramaController::class, 'updateHorarioEstado'])->middleware('programa.acceso:cambiar-estado')->name('horario-estado');
        Route::post('/horario-fecha', [ProgramaController::class, 'updateHorarioFecha'])->middleware('programa.acceso:cambiar-estado')->name('horario-fecha');
        Route::post('/horario/{horario}/asistencia', [ProgramaController::class, 'guardarAsistenciaSesion'])->middleware('programa.acceso:modulo')->name('asistencia-guardar');
        Route::post('/modulo-estado', [ProgramaController::class, 'updateModuloEstado'])->middleware('programa.acceso:cambiar-estado')->name('modulo-estado');
        Route::post('/docente-por-ci', [ProgramaController::class, 'buscarDocentePorCi'])->middleware(['permission:programas.modulos.asignar.docente', 'programa.acceso:asignar-docente'])->name('docente-por-ci');
        Route::post('/docente-registrar', [ProgramaController::class, 'registrarDocente'])->middleware(['permission:programas.modulos.asignar.docente', 'programa.acceso:asignar-docente'])->name('docente-registrar');
        Route::post('/asignar-docente', [ProgramaController::class, 'asignarDocenteModulo'])->middleware(['permission:programas.modulos.asignar.docente', 'programa.acceso:asignar-docente'])->name('asignar-docente');
        Route::post('/docente/update-credenciales', [ProgramaController::class, 'updateDocenteCredenciales'])->middleware('permission:docentes.credenciales')->name('docente-update-credenciales');
        Route::post('/modulo/{modulo}/moodle-link', [ProgramaController::class, 'updateModuloMoodleLink'])->middleware('role:Super Admin')->name('modulo-moodle-link');
        Route::post('/modulo/{modulo}/pago-docente', [ProgramaController::class, 'updateModuloPagoDocente'])->middleware('permission:programas.modulos.pago.docente')->name('modulo-pago-docente');
        Route::post('/modulo/{modulo}/procesar-pago', [ProgramaController::class, 'procesarPagoDocente'])->middleware('permission:programas.modulos.pago.docente')->name('modulo-procesar-pago');
        Route::post('/{id}/rentabilidad', [ProgramaController::class, 'guardarRentabilidad'])->middleware('permission:programas.editar')->name('rentabilidad-guardar');
        Route::post('/{id}/costos-universidad', [ProgramaController::class, 'guardarCostosUniversidad'])->middleware('role:Super Admin')->name('costos-universidad-guardar');
        Route::post('/{programa}/moodle-link', [ProgramaController::class, 'updateProgramaMoodleLink'])->middleware('role:Super Admin')->name('programa-moodle-link');
        Route::post('/modulo/{modulo}/notas', [ProgramaController::class, 'guardarNotasModulo'])->middleware('programa.acceso:modulo')->name('modulo-notas');
        Route::post('/modulo/{modulo}/nivelacion', [ProgramaController::class, 'guardarNivelacionModulo'])->middleware('programa.acceso:modulo')->name('modulo-nivelacion');
        Route::post('/{programa}/modulo/{modulo}/nivelacion', [ProgramaController::class, 'guardarNivelacionModuloPrograma'])->middleware('programa.acceso:modulo')->name('modulo-nivelacion-programa');
        Route::post('/{programa}/nivelacion/horario', [ProgramaController::class, 'agregarHorarioNivelacion'])->middleware('programa.acceso:modulo')->name('nivelacion-horario');
        Route::post('/cuota/{cuota}/comprobante-nivelacion', [ProgramaController::class, 'subirComprobanteNivelacion'])->middleware('permission:contable.cobrar')->name('cuota-comprobante-nivelacion');
        Route::post('/cuota/{cuota}/validar-pago-nivelacion', [ProgramaController::class, 'validarPagoNivelacion'])->middleware('permission:contable.cobrar')->name('cuota-validar-pago-nivelacion');
        Route::post('/cuota/{cuota}/nota-nivelacion', [ProgramaController::class, 'guardarNotaNivelacionCuota'])->middleware('permission:programas.modulos.estado')->name('cuota-nota-nivelacion');
        Route::post('/modulo/{modulo}/notas-nivelacion', [ProgramaController::class, 'guardarNotasNivelacionModulo'])->middleware('programa.acceso:modulo')->name('modulo-notas-nivelacion');

        // Pagos y cuotas
        Route::post('/cuota/{cuota}/pago', [ProgramaController::class, 'registrarPagoCuota'])->middleware('role:Super Admin|Contabilidad')->name('cuota-pago');
        Route::post('/cuota/{cuota}/fecha', [ProgramaController::class, 'updateCuotaFecha'])->middleware('permission:contable.cobrar')->name('cuota-fecha');
        Route::post('/{programa}/participante/{participante}/fechas', [ProgramaController::class, 'saveFechasParticipante'])->middleware('permission:contable.cobrar')->name('participante-fechas-save');
        Route::post('/pago-multiple', [ProgramaController::class, 'registrarPagoMultiple'])->middleware('role:Super Admin|Contabilidad')->name('pago-multiple');
        Route::post('/{programa}/participante/{participante}/certificacion-fecha', [ProgramaController::class, 'guardarFechaCertificacion'])->middleware('permission:contable.cobrar')->name('certificacion-fecha');
        Route::post('/{programa}/generar-deudas-certificacion', [ProgramaController::class, 'generarDeudasCertificacion'])->middleware('permission:contable.cobrar')->name('generar-deudas-certificacion');
        Route::post('/{programa}/participante/{participante}/certificacion-pago', [ProgramaController::class, 'registrarPagoCertificacion'])->middleware('permission:contable.cobrar')->name('certificacion-pago');

        // CRUD programas — reservado a Super Admin
        Route::post('/', [ProgramaController::class, 'store'])->middleware('role:Super Admin')->name('store');
        Route::put('/{id}', [ProgramaController::class, 'update'])->middleware('role:Super Admin')->name('update');
        Route::delete('/{id}', [ProgramaController::class, 'destroy'])->middleware('role:Super Admin')->name('destroy');

        // Modalidad titulación
        Route::post('/{programa}/participantes/modalidad-titulacion', [ProgramaController::class, 'actualizarModalidadTitulacion'])->middleware('permission:inscripciones.cambiarestado')->name('modalidad-titulacion');

        // Folios dentro de programa
        Route::post('/{programa}/folios', [ProgramaController::class, 'storeFolio'])->middleware('permission:programas.editar')->name('folios-store');
        Route::post('/{programa}/folios/{folio}/registrar-participantes', [ProgramaController::class, 'registrarParticipantesEnFolio'])->middleware('permission:programas.editar')->name('folios-registrar-participantes');
    });

    // === Notificaciones ===
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->middleware('permission:dashboard.listar')->name('notificaciones.index');

    // === Ajustes WhatsApp (Seguimiento ESAM) ===
    Route::get('/ajustes/whatsapp', [AjustesWhatsappController::class, 'index'])
        ->middleware('whatsapp.autorizado')->name('ajustes.whatsapp');
    Route::post('/ajustes/whatsapp', [AjustesWhatsappController::class, 'update'])
        ->middleware('permission:whatsapp.admin')->name('ajustes.whatsapp.update');

    // === SSO hacia Seguimiento ESAM ===
    Route::get('/seguimiento-esam', [SeguimientoEsamSsoController::class, 'redirect'])
        ->middleware('whatsapp.autorizado')->name('seguimiento-esam');

    // Estado de la conexión de Seguimiento ESAM (indicador del topbar)
    Route::get('/seguimiento-esam/estado', [AjustesWhatsappController::class, 'estado'])
        ->middleware('whatsapp.autorizado')->name('seguimiento-esam.estado');

    // === Progreso de lote (Seguimiento ESAM) ===
    Route::get('/whatsapp/lote/{batch}', [WhatsappLoteController::class, 'show'])
        ->middleware('permission:whatsapp.admin|contable.cobrar|programas.editar|programas.modulos.estado')
        ->name('whatsapp.lote');

    // === Eliminar pago (ruta fuera del prefix programas para mantener URL /admin/pagos/{pago}) ===
    Route::delete('/pagos/{pago}', [ProgramaController::class, 'eliminarPago'])->middleware('role:Super Admin|Contabilidad')->name('pagos.eliminar');

    // === Folios globales ===
    Route::prefix('folios')->name('folios.')->middleware('permission:programas.listar')->group(function () {
        Route::get('/', [FolioController::class, 'index'])->name('index');
        Route::get('/create', [FolioController::class, 'create'])->name('create');
        Route::post('/', [FolioController::class, 'store'])->name('store');
        Route::get('/{id}', [FolioController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FolioController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FolioController::class, 'update'])->name('update');
        Route::delete('/{id}', [FolioController::class, 'destroy'])->name('destroy');
        Route::get('/programas-costos', [FolioController::class, 'getProgramasConCostos'])->name('programas-costos');
    });

    // === Cronogramas ===
    // Cronogramas: área académica + gerencia/supervisor de marketing (Super Admin siempre).
    Route::prefix('cronogramas')->name('cronogramas.')->middleware('role:Academicos|Gerencia Marketing|Supervisor de Marketing|Super Admin|Responsable Académico')->group(function () {
        Route::get('/', [CronogramaController::class, 'index'])->name('index');
        Route::get('/events', [CronogramaController::class, 'events'])->name('events');
        Route::get('/modulos-inicio', [CronogramaController::class, 'modulosInicio'])->name('modulos-inicio');
        Route::get('/modulo/{modulo}/horarios', [CronogramaController::class, 'moduloHorarios'])->name('modulo-horarios');
    });

    // === Participantes (contable + académico; alta/edición en programa) ===
    Route::prefix('participantes')->name('participantes.')->middleware('role:Super Admin|Contabilidad|Academicos|Responsable Académico')->group(function () {
        Route::get('/', [ParticipanteController::class, 'index'])->name('index');
        Route::get('/buscar', [ParticipanteController::class, 'buscar'])->name('buscar');
        Route::get('/{id}/edit', [ParticipanteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ParticipanteController::class, 'update'])->name('update');
    });

    // === Docentes (ver: contable + académico; registrar/editar: Super Admin + Responsable Académico) ===
    Route::prefix('docentes')->name('docentes.')->middleware('role:Super Admin|Contabilidad|Academicos|Responsable Académico')->group(function () {
        Route::get('/', [DocenteController::class, 'index'])->name('index');
        Route::get('/check-ci', [DocenteController::class, 'checkCi'])->name('check-ci');
        Route::get('/check-email', [DocenteController::class, 'checkEmail'])->name('check-email');
        Route::post('/', [DocenteController::class, 'store'])->middleware('role:Super Admin|Responsable Académico')->name('store');
        Route::get('/{id}/show', [DocenteController::class, 'show'])->name('show');
        Route::get('/{id}/events', [DocenteController::class, 'events'])->name('events');
        Route::get('/{id}/data', [DocenteController::class, 'data'])->name('data');
        Route::put('/{id}', [DocenteController::class, 'update'])->middleware('role:Super Admin|Responsable Académico')->name('update');
        Route::get('/{id}/modulos', [DocenteController::class, 'modulos'])->name('modulos');
        Route::get('/{id}/estudios', [DocenteController::class, 'estudios'])->name('estudios');
        Route::post('/{id}/estudios', [DocenteController::class, 'storeEstudio'])->middleware('role:Super Admin|Responsable Académico')->name('estudios.store');
        Route::put('/estudios/{id}', [DocenteController::class, 'updateEstudio'])->middleware('role:Super Admin|Responsable Académico')->name('estudios.update');
        Route::delete('/estudios/{id}', [DocenteController::class, 'destroyEstudio'])->middleware('role:Super Admin|Responsable Académico')->name('estudios.destroy');
        Route::get('/estudios/catalogos', [DocenteController::class, 'catalogos'])->name('estudios.catalogos');
        Route::post('/estudios/catalogo', [DocenteController::class, 'storeCatalogo'])->middleware('role:Super Admin|Responsable Académico')->name('estudios.catalogo.store');
    });

    // === Tipos ===
    Route::prefix('tipos')->name('tipos.')->middleware('permission:tipos.listar')->group(function () {
        Route::get('/', [TipoController::class, 'index'])->name('index');
        Route::get('/{id}/data', [TipoController::class, 'data'])->name('data');
        Route::post('/', [TipoController::class, 'store'])->middleware('permission:tipos.registrar')->name('store');
        Route::put('/{id}', [TipoController::class, 'update'])->middleware('role:Super Admin')->name('update');
        Route::delete('/{id}', [TipoController::class, 'destroy'])->middleware('role:Super Admin')->name('destroy');
    });

    // === Áreas ===
    Route::prefix('areas')->name('areas.')->middleware('permission:areas.listar')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('index');
        Route::get('/{id}/data', [AreaController::class, 'data'])->name('data');
        Route::post('/', [AreaController::class, 'store'])->middleware('permission:areas.registrar')->name('store');
        Route::put('/{id}', [AreaController::class, 'update'])->middleware('role:Super Admin')->name('update');
        Route::delete('/{id}', [AreaController::class, 'destroy'])->middleware('role:Super Admin')->name('destroy');
    });

    // === Seguimiento (Super Admin + Responsable Académico) ===
    Route::get('/seguimiento', [SeguimientoController::class, 'index'])->middleware('role:Super Admin|Responsable Académico')->name('seguimiento.index');
    Route::get('/seguimiento-diario', [SeguimientoDiarioController::class, 'index'])->middleware('role:Super Admin|Responsable Académico')->name('seguimiento-diario.index');
    Route::get('/control-responsables', [ControlResponsablesController::class, 'index'])->middleware('role:Super Admin|Responsable Académico')->name('control-responsables.index');

    // === Contable (solo rol Contabilidad + Super Admin) ===
    Route::prefix('contable')->name('contable.')->middleware('role:Super Admin|Contabilidad')->group(function () {
        Route::get('/seguimiento', [ContableController::class, 'seguimiento'])->name('seguimiento');
        Route::get('/retencion', [RetencionController::class, 'index'])->name('retencion');
        Route::get('/asignaciones', [ContableController::class, 'asignaciones'])->name('asignaciones');
        Route::get('/cuotas-vencidas', [ContableController::class, 'cuotasVencidas'])->name('cuotas-vencidas');
        Route::get('/cuotas-proximas', [ContableController::class, 'cuotasProximas'])->name('cuotas-proximas');
        Route::get('/programa/{programa}/fechas', [ContableController::class, 'getFechasPrograma'])->name('fechas-get');
        Route::post('/programa/{programa}/fechas', [ContableController::class, 'saveFechasPrograma'])->middleware('permission:contable.asignaciones.definir')->name('fechas-save');
        Route::post('/whatsapp/enviar', [WhatsappEnvioController::class, 'enviarContable'])->middleware('permission:contable.cobrar')->name('whatsapp.enviar');
        Route::get('/programa/{programa}/preview-recalcular', [ContableController::class, 'previewRecalcularContado'])->middleware('permission:contable.cobrar')->name('preview-recalcular');
        Route::post('/programa/{programa}/recalcular-contado', [ContableController::class, 'recalcularContado'])->middleware('permission:contable.cobrar')->name('recalcular-contado');
        Route::post('/programa/{programa}/participante/{participante}/cambiar-plan', [ContableController::class, 'cambiarPlanParticipante'])->middleware('permission:contable.asignaciones.definir')->name('cambiar-plan');
    });

    // === Comprobantes de pago ===
    Route::prefix('comprobantes-pago')->name('comprobantes-pago.')->middleware('role:Super Admin|Contabilidad')->group(function () {
        Route::get('/', [ComprobantePagoController::class, 'index'])->name('index');
        Route::get('/{id}', [ComprobantePagoController::class, 'show'])->name('show');
        Route::post('/{id}/verificar', [ComprobantePagoController::class, 'verificar'])->middleware('permission:contable.cobrar')->name('verificar');
    });

    // === Responsables ===
    Route::prefix('responsables')->name('responsables.')->group(function () {
        Route::get('/', [ResponsableController::class, 'index'])->middleware('role:Super Admin|Responsable Académico')->name('index');
        Route::get('/counts', [ResponsableController::class, 'counts'])->middleware('role:Super Admin|Responsable Académico')->name('counts');
        // Detalle y seguimiento: gestores o el propio responsable vinculado al
        // usuario (el perfil muestra "Mis programas" para quien tiene responsable_id).
        Route::get('/{id}/detalle', [ResponsableController::class, 'detalle'])->name('detalle');
        Route::get('/{id}/seguimiento-semanal', [ResponsableController::class, 'seguimientoSemanal'])->name('seguimiento-semanal');
    });

    // === Profile (sin protección de permiso específico — es del propio usuario) ===
    Route::get('/profile', [ProfileAdminController::class, 'index'])->name('profile');
    Route::get('/profile/programa/{programa}/inscritos', [ProfileAdminController::class, 'inscritosPrograma'])->name('profile.programa-inscritos');
    Route::put('/profile/password', [ProfileAdminController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/profile/publicidad-data', [ProfileAdminController::class, 'publicidadData'])->name('profile.publicidad-data');
    Route::get('/profile/ofertas-data', [ProfileAdminController::class, 'ofertasData'])->name('profile.ofertas-data');
    Route::get('/profile/inscripciones-data', [ProfileAdminController::class, 'inscripcionesData'])->name('profile.inscripciones-data');
    Route::post('/profile/publicidad/autogestion', [ProfileAdminController::class, 'storeGastoAutogestion'])->name('profile.publicidad.autogestion.store');

    // === Prospectos de marketing (asesores incluidos; cada asesor ve los suyos) ===
    Route::prefix('marketing/prospectos')->name('marketing.prospectos')
        ->middleware('role:Super Admin|Gerencia Marketing|Supervisor de Marketing|Asesor de Marketing')->group(function () {
            Route::get('/', [MarketingProspectoController::class, 'index'])->name('');
            Route::post('/', [MarketingProspectoController::class, 'store'])->name('.store');
            Route::post('/enviar', [MarketingProspectoController::class, 'enviar'])->name('.enviar');
            Route::get('/lote/{batch}', [MarketingProspectoController::class, 'lote'])->whereNumber('batch')->name('.lote');
            Route::put('/{id}', [MarketingProspectoController::class, 'update'])->whereNumber('id')->name('.update');
            Route::patch('/{id}/estado', [MarketingProspectoController::class, 'updateEstado'])->whereNumber('id')->name('.estado');
            Route::post('/{id}/sincronizar', [MarketingProspectoController::class, 'sincronizarUno'])->whereNumber('id')->name('.sincronizar');
            Route::delete('/{id}', [MarketingProspectoController::class, 'destroy'])->whereNumber('id')->name('.destroy');
        });

    // === Marketing (Super Admin, Contabilidad, Gerencia/Supervisor Marketing) ===
    Route::prefix('marketing')->name('marketing.')->middleware('role:Super Admin|Contabilidad|Gerencia Marketing|Supervisor de Marketing')->group(function () {
        Route::get('/estadisticas', [MarketingController::class, 'estadisticas'])->name('estadisticas');
        Route::get('/asesores', [MarketingController::class, 'asesores'])->name('asesores');
        Route::post('/asesores', [MarketingController::class, 'storeAsesor'])->middleware('permission:asesores.registrar')->name('asesores.store');
        Route::get('/asesor-detalle', [MarketingController::class, 'asesorDetalle'])->middleware('permission:asesores.verdetalle')->name('asesor-detalle');
        Route::get('/asesor/{id}', [MarketingController::class, 'asesorShow'])->middleware('permission:asesores.vercompleto')->name('asesor-show');
        Route::get('/programas', [MarketingController::class, 'programas'])->name('programas');
        Route::get('/inversiones', [MarketingController::class, 'inversiones'])->name('inversiones');
        Route::get('/asesores-lista', [MarketingController::class, 'cargosAsesores'])->name('asesores.lista');
        Route::get('/asesor/{id}/data', [MarketingController::class, 'asesorData'])->name('asesor-data');
        Route::put('/asesores/{id}', [MarketingController::class, 'updateAsesor'])->middleware('permission:asesores.gestionar')->name('asesores.update');
        Route::patch('/asesores/{id}/toggle-estado', [MarketingController::class, 'toggleAsesorEstado'])->middleware('permission:asesores.gestionar')->name('asesores.toggle-estado');
        Route::delete('/asesores/{id}', [MarketingController::class, 'deleteAsesor'])->middleware('permission:asesores.gestionar')->name('asesores.delete');
        Route::post('/cargos', [MarketingController::class, 'storeCargo'])->middleware('permission:asesores.registrar')->name('cargos.store');
        Route::put('/cargos/{id}', [MarketingController::class, 'updateCargo'])->middleware('permission:asesores.gestionar')->name('cargos.update');
        Route::delete('/cargos/{id}', [MarketingController::class, 'deleteCargo'])->middleware('permission:asesores.gestionar')->name('cargos.delete');
        Route::get('/programas/{id}/inscritos-reales', [MarketingController::class, 'programaInscritosReales'])->name('programas.inscritos-reales');
        Route::post('/programas/{id}/inscritos-minimo', [MarketingController::class, 'updateInscritosMinimo'])->middleware('permission:programas.editar')->name('programas.inscritos-minimo');

        // === Programas de otras sedes ===
        Route::get('/programas-otras-sedes', [MarketingOtrasSedesController::class, 'index'])->name('programas-otras-sedes.index');
        Route::post('/programas-otras-sedes', [MarketingOtrasSedesController::class, 'store'])->name('programas-otras-sedes.store');
        Route::get('/programas-otras-sedes/{id}', [MarketingOtrasSedesController::class, 'registro'])->name('programas-otras-sedes.registro');
        Route::put('/programas-otras-sedes/{id}', [MarketingOtrasSedesController::class, 'update'])->name('programas-otras-sedes.update');
        Route::delete('/programas-otras-sedes/{id}', [MarketingOtrasSedesController::class, 'destroy'])->name('programas-otras-sedes.destroy');
        Route::post('/programas-otras-sedes/buscar-participante', [MarketingOtrasSedesController::class, 'buscarParticipante'])->name('programas-otras-sedes.buscar-participante');
        Route::post('/programas-otras-sedes/{id}/inscribir', [MarketingOtrasSedesController::class, 'inscribir'])->name('programas-otras-sedes.inscribir');
        Route::delete('/programas-otras-sedes/inscripciones/{inscripcion}', [MarketingOtrasSedesController::class, 'eliminarInscripcion'])->name('programas-otras-sedes.inscripcion.destroy');
    });

    // === Marketing: Masterclass ===
    Route::prefix('marketing/masterclass')->name('marketing.masterclass.')->middleware('role:Super Admin|Contabilidad|Gerencia Marketing|Supervisor de Marketing')->group(function () {
        Route::get('/', [MasterclassController::class, 'index'])->name('index');
        Route::get('/events', [MasterclassController::class, 'events'])->name('events');
        Route::post('/', [MasterclassController::class, 'store'])->name('store');
        Route::put('/{id}', [MasterclassController::class, 'update'])->name('update');
        Route::delete('/{id}', [MasterclassController::class, 'destroy'])->name('destroy');
    });

    // === Marketing: Publicidad (solo Super Admin + Contabilidad) ===
    Route::middleware('role:Super Admin|Contabilidad')->group(function () {
        Route::get('/marketing/publicidad', [PublicidadController::class, 'index'])->name('marketing.publicidad.index');
        Route::get('/marketing/publicidad/presupuestos', [PublicidadController::class, 'presupuestosIndex'])->name('marketing.publicidad.presupuestos');
        Route::get('/marketing/publicidad/presupuestos/reporte', [PublicidadController::class, 'reportePdf'])->name('marketing.publicidad.presupuestos.reporte');
        Route::get('/marketing/publicidad/comprobantes/{asesor}', [PublicidadController::class, 'comprobantesAsesor'])->name('marketing.publicidad.comprobantes');
        Route::get('/marketing/publicidad/autogestion', [PublicidadController::class, 'autogestionIndex'])->name('marketing.publicidad.autogestion');
        Route::post('/marketing/publicidad/autogestion', [PublicidadController::class, 'storeAutogestion'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.autogestion.store');
        Route::get('/marketing/publicidad/autogestion/{id}/archivo/{tipo}', [PublicidadController::class, 'verArchivoAutogestion'])->name('marketing.publicidad.autogestion.archivo');
        Route::get('/marketing/publicidad/autogestion/{id}/edit', [PublicidadController::class, 'editAutogestion'])->name('marketing.publicidad.autogestion.edit');
        Route::post('/marketing/publicidad/autogestion/{id}/update', [PublicidadController::class, 'updateAutogestion'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.autogestion.update');
        Route::delete('/marketing/publicidad/autogestion/{id}', [PublicidadController::class, 'destroyAutogestion'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.autogestion.destroy');
    });
    Route::post('/marketing/publicidad/presupuestos/toggle', [PublicidadController::class, 'togglePeriodo'])->middleware('permission:marketing.publicidad.presupuestos')->name('marketing.publicidad.presupuestos.toggle');
    Route::post('/marketing/publicidad', [PublicidadController::class, 'store'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.store');
    Route::patch('/marketing/publicidad/{id}/verificar', [PublicidadController::class, 'verificar'])->middleware('permission:marketing.publicidad.verificar')->name('marketing.publicidad.verificar');
    Route::post('/marketing/publicidad/{id}/update', [PublicidadController::class, 'update'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.update');
    Route::delete('/marketing/publicidad/{id}', [PublicidadController::class, 'destroy'])->middleware('permission:marketing.publicidad.subir')->name('marketing.publicidad.destroy');
    Route::post('/marketing/publicidad/presupuestos', [PublicidadController::class, 'storePresupuesto'])->middleware('permission:marketing.publicidad.presupuestos')->name('marketing.publicidad.presupuesto.store');
    Route::post('/marketing/publicidad/presupuestos/{id}/reembolsar', [PublicidadController::class, 'reembolsar'])->middleware('permission:marketing.publicidad.presupuestos')->name('marketing.publicidad.presupuesto.reembolsar');
    Route::put('/marketing/publicidad/presupuestos/{id}', [PublicidadController::class, 'updatePresupuesto'])->middleware('permission:marketing.publicidad.presupuestos')->name('marketing.publicidad.presupuesto.update');
    Route::delete('/marketing/publicidad/presupuestos/{id}', [PublicidadController::class, 'destroyPresupuesto'])->middleware('permission:marketing.publicidad.presupuestos')->name('marketing.publicidad.presupuesto.destroy');

    // === Seguridad (Spatie) — solo Super Admin ===
    Route::prefix('seguridad')->name('seguridad.')->middleware('role:Super Admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.listar')->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.crear')->name('roles.store');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:roles.editar')->name('roles.update');
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.eliminar')->name('roles.destroy');

        Route::get('/permisos', [PermissionController::class, 'index'])->middleware('permission:permisos.listar')->name('permisos.index');
        Route::post('/permisos', [PermissionController::class, 'store'])->middleware('permission:permisos.crear')->name('permisos.store');
        Route::put('/permisos/{id}', [PermissionController::class, 'update'])->middleware('permission:permisos.editar')->name('permisos.update');
        Route::delete('/permisos/{id}', [PermissionController::class, 'destroy'])->middleware('permission:permisos.eliminar')->name('permisos.destroy');

        Route::get('/usuarios', [SeguridadUserController::class, 'index'])->middleware('permission:usuarios.listar')->name('usuarios.index');
        Route::post('/usuarios', [SeguridadUserController::class, 'store'])->middleware('permission:usuarios.crear')->name('usuarios.store');
        Route::put('/usuarios/{id}', [SeguridadUserController::class, 'update'])->middleware('permission:usuarios.editar')->name('usuarios.update');
        Route::delete('/usuarios/{id}', [SeguridadUserController::class, 'destroy'])->middleware('permission:usuarios.eliminar')->name('usuarios.destroy');
        Route::post('/usuarios/{id}/reset-password', [SeguridadUserController::class, 'resetPassword'])->middleware('permission:usuarios.editar')->name('usuarios.reset-password');
    });
});
