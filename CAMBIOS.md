# CAMBIOS — cronogramas

Registro de lo implementado y corregido en este proyecto para la integración con
**baileys_esam (Seguimiento ESAM)**. Fecha de referencia: **2026-09-28**.

---

## 1. Usuarios y Seguimiento ESAM (`/admin/seguridad/usuarios`)

Archivos: `app/Http/Controllers/Admin/Seguridad/UserController.php`,
`resources/views/admin/seguridad/usuarios.blade.php`,
`app/Services/SeguimientoEsam/SeguimientoEsamClient.php`,
`resources/views/admin/seguridad/_styles.blade.php`.

- **Estado en vivo por usuario** según su cuenta en ESAM: se consulta `GET /api/v1/accounts`
  (nuevo `SeguimientoEsamClient::accounts()`) y se compara el número autorizado con el vinculado:
  - **Verificado** (cuenta lista y número coincide).
  - **Pendiente de escaneo** (cuenta creada, falta vincular).
  - **No registrado** → mensaje **“Este número no está registrado para la cuenta de usuario.”**
  - **Habilitado** (integración no disponible).
- Se busca la cuenta primero por `owner_uid` y, si no existe, por número (cubre la sesión
  principal ya vinculada).
- **Validación**: el número es obligatorio si se marca “Autorizado para usar Seguimiento ESAM”
  (`required_if`).
- **`syncWhatsapp()`** ahora devuelve el error; al crear/editar/restablecer contraseña se muestra un
  mensaje si ESAM rechaza la cuenta (antes solo se registraba en el log).
- **Vista**: badge de estado por usuario, mensaje en rojo, KPI **“Verificados ESAM”**, alerta si hay
  usuarios con número no registrado y nota de que el número escaneado debe coincidir.
- Estilos `.esam-pill.danger` y `.esam-alert`.

## 2. Alta de cuenta espejo en baileys_esam

- Al crear/editar un usuario **autorizado + con número**, cronogramas ya llamaba a
  `POST /api/v1/accounts`; en baileys_esam ahora ese endpoint también **crea el usuario del panel**
  (login espejo con el mismo usuario/contraseña). Ver `baileys_esam/CAMBIOS.md`.

## 3. Integración SSO (enlace “Seguimiento ESAM”)

- **`config/services.php`**: nueva clave `seguimiento_esam.panel_url`
  (`SEGUIMIENTO_ESAM_PANEL_URL`, por defecto `http://127.0.0.1:8030`), separada de la URL de la API
  (`SEGUIMIENTO_ESAM_URL`).
- **`app/Services/SeguimientoEsam/SeguimientoEsamSso.php`**: el enlace `/sso?token=...` se arma con
  `panel_url` (antes usaba la URL de la API → “Cannot GET /sso”).
- **`resources/views/admin/ajustes/whatsapp.blade.php`**: el botón “Abrir Seguimiento ESAM” usa
  `panel_url`.
- **`.env` / `.env.example`**: se agrega `SEGUIMIENTO_ESAM_PANEL_URL` y se documentan las variables
  de la integración.
- El `SEGUIMIENTO_ESAM_SSO_SECRET` debe coincidir con `SSO_SECRET` de baileys_esam.

## 4. Corrección: editar participante (`/admin/participantes`)

Archivos: `app/Http/Controllers/Admin/ParticipanteController.php`,
`resources/views/admin/participantes/index.blade.php`,
`resources/views/admin/participantes/partials/table.blade.php`.

- **Causa del error**: el `update()` enviaba `updated_at`, columna que **no existe** en
  `participantes` (`SQLSTATE[42S22] Unknown column 'updated_at'`). Se eliminó.
- **`sexo`**: la BD es `enum('Hombre','Mujer') NOT NULL` y el formulario enviaba `M`/`F`. Ahora:
  - Validación `nullable|in:M,F,Hombre,Mujer,Masculino,Femenino`.
  - Nuevo `normalizarSexo()` que mapea a `Hombre`/`Mujer` y, si viene vacío, conserva el valor actual.
  - El `<select>` usa valores `Hombre`/`Mujer` con preselección correcta.
  - El badge de la tabla detecta `Mujer` (antes nunca marcaba femenino) y muestra `F`/`M`.

## 5. Análisis contable: recordatorios vs cobros (`/admin/contable/asignaciones`)

Archivos: `app/Http/Controllers/Admin/ContableController.php`,
`resources/views/admin/contable/asignaciones.blade.php`.

- **KPIs nuevos**: **Recordatorios** (mensajes de cobranza del mes) y **Cobros registrados** (Bs
  pagados en el mes).
- **Por programa**: `Rec` (recordatorios), `Reg Bs` (cobrado registrado), `Pagaron` (participantes
  que pagaron) y `Efect` (recordados que luego pagaron → efectividad del sistema).
- **Gráfico** “Recordatorios enviados vs cobros registrados” (columnas = recordatorios, línea = Bs
  del mes; ApexCharts).
- **Columna “Seguimiento”** por participante en la tabla: icono de recordatorio enviado e icono de
  pago registrado en el mes.
- **Fuentes de datos**:
  - Recordatorios: `mensajes.tipo = 'whatsapp_cobranza'` del mes (generados desde
    `/admin/contable/cuotas-vencidas` y `/cuotas-proximas`), vía `inscripciones`.
  - Pagos: `pagos.created` del mes → `deudas.pago_id` → `modulos_participantes` → `modulos` →
    `programas`; monto = `SUM(deudas.monto_bs)`.

## 6. Datos que envía cronogramas a ESAM (para el monitoreo)

Los envíos ya incluyen en el lote: `account` (usuario), `tipo`, `programa_id`, `programa`, `area` y
`gestion`, lo que permite en baileys_esam el seguimiento por **usuario, programa, área y origen**:

- Masivos desde `/admin/programas/{id}/whatsapp` → `tipo = participantes` (o `grupo`).
- Seguimiento contable → `tipo = cobranza`.
- Módulos / notas → `tipo = notas`.

---

## 8. Estado por usuario en Seguimiento ESAM (2026-10-05)

Archivos: `app/Services/SeguimientoEsam/SeguimientoEsamClient.php`,
`app/Http/Controllers/Admin/AjustesWhatsappController.php`,
`resources/views/admin/ajustes/whatsapp.blade.php`,
`resources/views/admin/partials/topbar.blade.php`.

**Problema**: al ingresar con un usuario, el indicador del topbar y la vista
`/admin/ajustes/whatsapp` mostraban la **sesión global** de baileys_esam (conectada
con el número de otro usuario) en vez de la sesión del usuario que ingresó.

- **`SeguimientoEsamClient::health()`**: acepta `$uid` y `$phone` opcionales y los
  envía como query (`/api/v1/health?uid=...&phone=...`). El gateway devuelve la
  sesión de ese usuario (o `unlinked` si no tiene número vinculado).
- **`AjustesWhatsappController::estado()`** (indicador del topbar): consulta el
  estado con el `uid`/`whatsapp_numero` del usuario autenticado y **cachea por
  usuario** (`esam_health_estado:{uid}`) — antes la clave era global y todos veían
  el mismo resultado.
- **`AjustesWhatsappController::index()`** (`/admin/ajustes/whatsapp`): igual, usa
  el `uid`/número del usuario para el bloque “Estado de Seguimiento ESAM”.
- **Vista `ajustes/whatsapp.blade.php`**: el bloque “Número del usuario” ahora
  muestra el **número registrado** del usuario (`whatsapp_numero`), su estado
  **Autorizado** (Sí/No) y su `Área`; se retiró el formulario que editaba el
  número/días globales (solo un admin ve el número global como referencia).
- **Topbar**: el JS ya mapea `unlinked`/`desconectado` a “Desconectado”, así que un
  usuario sin número vinculado ya no ve el número de otra cuenta.

## 9. Verificación

- `php -l` limpio en los controladores modificados y `php artisan view:clear`.
- Pruebas de render de las vistas de usuarios, participantes, asignaciones y ajustes.
- Verificado en el gateway: `health?uid=1` (conectado, su número), `uid=10/26`
  (desconectados con sus números), `uid` inexistente → `unlinked`.

## Notas

- Si un usuario ve solo “Conexión” en baileys_esam, es porque su número aún no está vinculado.
- Para que la analítica de recordatorios muestre datos, debe enviarse al menos un recordatorio de
  cobranza desde cuotas vencidas/próximas (`whatsapp_cobranza`).
- El estado del indicador depende del `uid` (id de usuario de cronogramas) que coincide con
  `owner_uid` de la sesión en baileys_esam.
