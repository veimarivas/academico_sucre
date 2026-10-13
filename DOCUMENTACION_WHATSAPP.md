# Documentación — Integración WhatsApp (cronogramas)

Cambios y módulos agregados al sistema `cronogramas.avi-p.site`
(BD `esam_academico`) para enviar por WhatsApp a través de **Seguimiento ESAM**
y hacer seguimiento de cobranza/retención.

- **Ruta del proyecto:** `C:\xampp_82_12\htdocs\cronogramas.avi-p.site`
- **Panel:** `http://localhost`
- **Seguimiento ESAM:** `http://127.0.0.1:8020`

> Ver también: `DOCUMENTACION_ROLES_PERMISOS.md` (matriz de acceso por rol,
> middlewares `programa.acceso` / `whatsapp.autorizado` y rediseño de vistas).
>
> **Cambio de accesos**: el apartado WhatsApp dejó de estar dentro de Contable y
> ahora es una sección propia **“WhatsApp Seguimiento ESAM”**, visible solo a
> usuarios con `whatsapp_autorizado` (o Super Admin).

---

## 1. Cliente de integración

`App\Services\SeguimientoEsam\SeguimientoEsamClient` — cliente HTTP con Bearer
token hacia `SEGUIMIENTO_ESAM_URL/api/v1`.

Métodos: `health`, `groups`, `resolveGroup`, `registerAccount`, `sendBatch`,
`batchStatus`, `setNumero`, `numero`.

`registerAccount($user, $password = null)` — registra/actualiza la cuenta
corporativa y el **usuario espejo** en Seguimiento ESAM; envía `username`, la
contraseña (solo cuando se crea/cambia) y los `roles` del usuario.

`sendBatch($name, $items, $category, $templateId, $dryRun, $defaultMedia, $account, $meta)`.

`App\Services\SeguimientoEsam\SeguimientoEsamSso` — arma la URL de SSO firmada
(HMAC-SHA256) hacia Seguimiento ESAM. El token incluye `uid`, `name`, `email`,
`username`, `autorizado` (`whatsapp_autorizado`), `area` (`whatsapp_area`),
`numero` (`whatsapp_numero`) y `roles` (roles Spatie del usuario). Seguimiento
ESAM los usa para **verificar el permiso de acceso** y **limitar las secciones
del panel** según el rol.

`App\Services\SeguimientoEsam\SenderAccount::resolve()` — determina desde qué
cuenta envía el usuario: si está **autorizado con número corporativo** envía
desde su cuenta; si tiene permiso `whatsapp.admin` usa la sesión principal; si no
→ **403 “no autorizado”**.

---

## 2. Envíos

### 2.1 Contable — por programa
- Rutas: `POST /admin/contable/whatsapp/enviar`
- Controller: `Admin\WhatsappEnvioController@enviarContable`
- Servicio: `App\Services\Cobranzas\RecordatorioCobranza`
- Modos:
  - **vencidas**: todas las cuotas impagas con vencimiento anterior a hoy.
  - **próximas**: **solo la cuota del mes en curso** que aún no venció.
- Mensaje: apellidos/nombres, programa, **cuota N (número real por tipo)**,
  monto y total adeudado; título y cierre según el modo.
- (La vista `admin/contable/cuotas-reporte.blade.php` envía por la API; ya no
  abre pestañas.)

### 2.2 Académico — notas por módulo
- Ruta: `POST /admin/programas/modulo/{modulo}/whatsapp`
- Controller: `Admin\WhatsappEnvioController@enviarAcademico`
- Filtro `tipo=all|aprobado|reprobado`; mensaje con programa, módulo, nota,
  estado y nivelación.

### 2.3 Por programa (grupo y participantes)
- Vista: `/admin/programas/{id}/whatsapp`
  (`Admin\WhatsappProgramaController`)
- **Grupo del programa**: se guarda en `programas.whatsapp_grupo_id` /
  `whatsapp_grupo_nombre`. Se registra pegando el **enlace de invitación**
  (se resuelve a `...@g.us`) o el ID manual. Si ya hay grupo → **solo lectura**;
  solo con permiso `whatsapp.admin` aparece **Editar**.
- **Participantes**: selección múltiple con personalización `{{nombre}}` y
  `{{programa}}`, adjuntando imagen/audio/video (misma media a todos).
- **Enviar** requiere `programas.editar`; **gestionar el grupo** requiere
  `whatsapp.admin`.

### 2.4 Media
La media (imagen/audio/video/documento) viaja en base64 y se envía como
`default_media` del lote.

### 2.5 Progreso del lote en la UI
Tras encolar, en vez de un `alert` con el `batch_id`, se abre un **modal de
progreso** (`resources/views/admin/partials/whatsapp-lote-modal.blade.php`,
helper `EsamLote.abrir(batchId, titulo)`) que consulta cada 3 s:
- Endpoint: `GET /admin/whatsapp/lote/{batch}`
  (`Admin\WhatsappLoteController@show`), que proxya
  `SeguimientoEsamClient::batchStatus()`.
- Devuelve `{ success, status, total, sent, failed, skipped, pending, progress }`.
- Wireado en cuotas-reporte (contable), Programa → Módulos (académico) y
  Programa → WhatsApp (grupo y participantes).

---

## 3. Cobranza y Retención

- Ruta: `GET /admin/contable/retencion` (`Admin\RetencionController`)
- KPIs: inscritos, con mora, deuda, cobrado, saldo, **% cobrado**, bajas,
  avisos WhatsApp.
- Tabla por programa: inscritos, con mora, deuda, cobrado, saldo, % cobrado,
  bajas y **avisos enviados** (tabla `mensajes`).
- **Bajas recientes**: participante, programa, tipo (operativa/académica/contable),
  motivo (`baja_historial`) y fecha.
- **Avisos por tipo**.

---

## 4. Personal / WhatsApp corporativo

- `users`: columnas `whatsapp_numero`, `whatsapp_area`
  (`contabilidad|academico|marketing`), `whatsapp_autorizado`.
- En **Seguridad → Usuarios** (crear/editar) hay una sección **“WhatsApp
  corporativo”**: número, área y check **Autorizado para usar Seguimiento ESAM**.
- La **lista de usuarios** muestra un indicador **Seguimiento ESAM** por usuario
  (*Habilitado / Sin autorizar / Sin número*) con el número y el área.
- Al guardar, `UserController` registra/actualiza automáticamente la cuenta en
  Seguimiento ESAM (`registerAccount`) — crea la sesión del número corporativo.
  Envía `username`, la **contraseña** (solo al crear/cambiarla) y los `roles` del
  usuario; con eso Seguimiento ESAM crea el **usuario espejo** y habilita el
  **login directo** (usuario o correo + contraseña) condicionado a
  `whatsapp_autorizado`.
- Se sincroniza también al **editar** un usuario, al **restablecer** su contraseña
  (`resetPassword` → `12345678`) y cuando el propio usuario **cambia su
  contraseña** en su perfil (`ProfileAdminController@updatePassword`).
- El **rol** y el permiso viajan en el SSO y en el registro de cuenta: en
  Seguimiento ESAM el administrador ve todo y el resto solo **Conversaciones** y
  **Conexión**; el rol y el último ingreso se ven en `/monitoring`.

---

## 5. Permisos

- `whatsapp.admin` — gestionar el grupo/configuración (asignado a Super Admin y
  Administrador).
- `programas.editar` — enviar por WhatsApp (grupos y participantes).
- Los envíos contable/académico requieren los permisos ya existentes
  (`contable.cobrar`, `programas.modulos.estado`).

---

## 6. Ajustes WhatsApp

- Ruta: `/admin/ajustes/whatsapp` (`Admin\AjustesWhatsappController`)
- Registra el **número autorizado** (se envía a Seguimiento ESAM) y los
  **días de envío automático** (por defecto `1,5,10,15`). Muestra el estado de la
  conexión con Seguimiento ESAM.
- El **topbar** muestra un **indicador de conexión de Seguimiento ESAM**
  (punto verde/rojo + número) **solo para usuarios autorizados**; se actualiza
  cada 60 s vía `GET /admin/seguimiento-esam/estado` (respuesta cacheada 30 s).

---

## 7. Envío automático programado

- Comando: `WhatsappRecordatorios` (`php artisan whatsapp:recordatorios`)
  - `--modo=vencidas|proximas|ambos` · `--gestion=` · `--force` · `--dry-run`.
  - Corre a diario y solo dispara en los días configurados (`whatsapp_dias_envio`).
- Scheduler (`routes/console.php`): todos los días 08:00 (America/La_Paz).
- Requiere el cron del programador activo en el servidor.

---

## 8. Migraciones y esquema

- `configuraciones` — clave/valor (número autorizado, días de envío).
- `programas.whatsapp_grupo_id` / `whatsapp_grupo_nombre`.
- `users.whatsapp_numero` / `whatsapp_area` / `whatsapp_autorizado`.
- `mensajes` — registro de avisos enviados (`tipo=whatsapp_*`).
- Permiso `whatsapp.admin` (migración + seeder).

---

## 9. Configuración (.env)

```
SEGUIMIENTO_ESAM_URL=http://127.0.0.1:8020
SEGUIMIENTO_ESAM_TOKEN=...
SEGUIMIENTO_ESAM_ENABLED=true
SEGUIMIENTO_ESAM_SSO_SECRET=...
```

En **hosting compartido** (sin consola):

- Ajustar `DB_*`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` y
  las cuatro variables de arriba (el token y el `SSO_SECRET` deben ser **idénticos**
  a los de Seguimiento ESAM).
- No hace falta migrar nada nuevo en cronogramas para esta integración (no hay
  columnas nuevas), pero si se copia `bootstrap/cache/*.php` desde local hay que
  **borrarlo** (las cachés de config/rutas guardan rutas absolutas).
- El **envío automático** (`whatsapp:recordatorios`) necesita el cron del
  programador (`php artisan schedule:run`); si el hosting no lo permite, usar el
  envío manual desde las pantallas.

---

## 10. Pantallas nuevas

| Ruta | Descripción |
| --- | --- |
| `/admin/ajustes/whatsapp` | Número autorizado y días de envío |
| `/admin/programas/{id}/whatsapp` | Grupo del programa + envío a participantes |
| `/admin/contable/retencion` | Cobranza y retención |
| `/admin/seguimiento-esam` | SSO hacia el panel de Seguimiento ESAM |
| `/admin/whatsapp/lote/{batch}` | Progreso JSON del lote (modal en las vistas de envío) |

---

## 11. Metadata de cada envío

Cada lote enviado incluye `meta` para el seguimiento en Seguimiento ESAM:

```json
{
  "area": "contabilidad | academico | general",
  "tipo": "cobranza | notas | grupo | participantes",
  "programa": "Nombre del programa",
  "programa_id": 175,
  "modo": "vencidas | proximas",
  "gestion": 2026,
  "modulo": "Nombre del módulo (solo notas)"
}
```
- Contable → `area=contabilidad`; Académico → `area=academico`;
  grupo/participantes → área del usuario (`whatsapp_area`) o `general`.
- Con esto, Seguimiento ESAM muestra el **área**, el **programa** y el
  **historial por programa** (`/batches/programa/{id}`) agrupado por área.

## 12. Estado verificado

- Envíos por programa (vencidas/próximas) y notas por módulo: verificados en
  `dry-run` contra la API real.
- Metadata (`area`, `tipo`, `programa`, `programa_id`, `modo`, `gestión`,
  `módulo`) y `account` llegan en el payload.
- Retención renderiza; vistas compilan; permiso `whatsapp.admin` creado.
- Progreso de lote: endpoint `admin/whatsapp/lote/{batch}` + modal reutilizable
  incluido en contable, académico y programa; vistas compilan (`view:cache`).
- SSO enriquecido: envía `autorizado`, `area`, `numero` y `roles`; verificado que
  Seguimiento ESAM registra el ingreso y aplica el control de acceso por rol.
- **Login directo**: `registerAccount` envía `username`, contraseña (al
  crear/cambiar) y `roles`; Seguimiento ESAM permite entrar con usuario o correo +
  contraseña si el usuario está `whatsapp_autorizado`. Se sincroniza al crear,
  editar, restablecer y al cambiar la propia contraseña en el perfil.
