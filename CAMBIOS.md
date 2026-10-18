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

## 10. Diseño responsivo móvil (2026-10-17)

Objetivo: que el panel se vea y se use bien en teléfono y tablet, con acabado
profesional, **sin tocar** rutas, controladores, modelos, permisos ni las
librerías del tema.

**Archivo nuevo**

- `public/backend/assets/css/avip-mobile.css`: capa responsive única, cargada al
  final del cascade (después de `avip-ui.css` y de los CSS por módulo, antes de
  `@stack('css-after')`). Reutiliza los tokens existentes (`--ui-*`, `--avip-*`).

**Archivos modificados**

- `resources/views/admin/layouts/master.blade.php`: `viewport-fit=cover`,
  `theme-color` claro/oscuro, carga de `avip-mobile.css`, `div.vertical-overlay`
  (velo del menú móvil) y script que cierra el menú al tocar un enlace y restaura
  el icono de hamburguesa en móvil.
- `resources/views/layouts/guest.blade.php` (login): `viewport-fit=cover`,
  `theme-color`, carga de la capa móvil y **marca ESAM para móvil/tablet**
  (`div.avip-card-brand`, oculta en escritorio, ver sección 11).
- `resources/views/admin/partials/topbar.blade.php`: botón de tema visible también
  en móvil.
- `resources/views/admin/programas/index.blade.php`: en móvil `.programas-table`
  pasa a `table-layout:auto` con `min-width:560px` (la columna «Programa» deja de
  quedar en ~25 px; la tabla se desplaza dentro de `.table-responsive`).
- `resources/views/admin/programas/partials/table.blade.php`: tabla envuelta en
  `.table-responsive`.
- `resources/views/admin/marketing/estadisticas/index.blade.php`:
  `.programas-sidebar-layout` con `align-items:stretch`.
- `resources/views/admin/marketing/asesores-lista/index.blade.php`:
  `.cargo-group` con `overflow-x:auto` y `.table-asesores` con `min-width` en móvil.

**Qué resuelve `avip-mobile.css`** (breakpoints 991.98 / 767.98 / 575.98 px)

- `color-scheme` y safe-areas; topbar y menús de usuario/notificaciones sin desbordar.
- Sidebar off-canvas con velo, `body` bloqueado y restauración del icono.
- Héroes y títulos que apilan; rejillas de estadísticas a 2 columnas.
- Filtros que apilan sus campos a ancho completo.
- Tablas: las que recortaban (`overflow:hidden`) pasan a desplazarse en horizontal,
  con scroll táctil fino.
- Tabla sin datos: se oculta su cabecera y el mensaje de estado vacío se centra en
  el área visible (selector `table:has(> tbody > tr:only-child > td[colspan]:only-child)`).
- Formularios: campos a 16 px (evita el zoom automático de iOS) y botones táctiles.
- Pestañas desplazables, modales con cuerpo desplazable y pie a ancho completo.
- Correcciones puntuales: cabeceras de tarjeta con badges que envuelven;
  Seguridad › Roles (`.role-grid`) y › Permisos (`.perm-card`); Seguimiento diario
  (`.sd-h-row`) sin desbordes.

**Verificación** (Chrome headless + CDP, con sesión real reutilizable)

- 31 rutas del panel a 320 px y a 390 px: sin desborde horizontal de página.
- Menú móvil: velo visible, cierra al tocar fuera, `body` con `overflow:hidden`.
- `php artisan view:cache` compila sin errores.

---

## 11. Acceso (login y pantallas de autenticación) en móvil (2026-10-17)

**Problema**: en móvil, la columna lateral del login se oculta y en su lugar
aparecía la marca compacta `.avip-form-brand` con `logo-dark.png`, que es el
**logo de la plantilla** (Velzon), no el de la institución.

**Solución** (enfoque continuista: una sola marca para todas las pantallas de
acceso)

- `resources/views/layouts/guest.blade.php`: se agrega `div.avip-card-brand`
  antes del contenido de la tarjeta, con el **emblema ESAM** (`logo-sm.png`,
  fondo transparente) y la leyenda «Plataforma de Cronogramas».
- `resources/views/auth/login.blade.php`: se retira el bloque `.avip-form-brand`
  que usaba `logo-dark.png` (logo de la plantilla).
- `public/backend/assets/css/avip-mobile.css` (sección 11): la marca solo se
  muestra hasta `991.98px` (justo cuando desaparece la columna lateral); en
  escritorio queda con `display:none`, por lo que **el login de escritorio no
  cambia**. Filete dorado bajo la leyenda y tamaño del emblema adaptado a
  `575.98px`.

Como el bloque vive en el layout, **todas** las pantallas de acceso (login,
registro, recuperar/restablecer contraseña, verificar correo, confirmar
contraseña) muestran la marca correcta en móvil.

**Verificación**

- Login a 320 / 390 / 820 px y escritorio 1280 px: `pageOverflow = false`;
  en escritorio la columna lateral sigue visible y la marca móvil con `0x0`.
- Recuperar contraseña y registro a 320 / 390 px: `pageOverflow = false`.

---

## 12. Perfil: carga diferida y rediseño de pestañas (2026-10-18)

Archivos: `app/Http/Controllers/Admin/ProfileAdminController.php`, `routes/admin.php`,
`resources/views/admin/profile/index.blade.php`,
`resources/views/admin/profile/_tab_ofertas.blade.php` (nuevo),
`resources/views/admin/profile/_tab_inscripciones.blade.php` (nuevo).

- **Carga inicial más liviana (`/admin/profile`)**: antes se ejecutaban todas las
  consultas pesadas (ofertas con subconsultas correlacionadas, series de puntos e
  inscritos reales, agregados por programa) al abrir el perfil, incluso para quien no
  es de marketing. Ahora `index()` solo resuelve lo necesario (usuario, gestiones,
  asesor vinculado, responsable y un contador ligero de ofertas) y el resto se carga
  **por AJAX** al abrir cada pestaña:
  - `GET /admin/profile/ofertas-data` → pestaña **“Ofertas en inscripción”**.
  - `GET /admin/profile/inscripciones-data` → pestaña **“Inscripciones”** (KPIs,
    gráficos y programas). Los filtros de gestión/tipo ahora recargan por AJAX sin
    recargar la página.
  - La pestaña **“Presupuesto publicidad”** ya cargaba por AJAX; se mantiene.
- Las consultas se extrajeron a métodos privados (`buildOfertas()`,
  `buildInscripcionesData()`) y se eliminó la consulta `inscritosPorPrograma`, que no
  se usaba (el modal ya trae la lista por AJAX). Los dos cálculos de “reales” (conteo y
  puntos) se unificaron en **una sola consulta**.
- Resultado medido en local (usuario de marketing con 2.688 inscripciones):
  carga inicial de ~435 ms a ~55 ms y de ~556 KB a ~180 KB de HTML; el trabajo pesado
  pasa a la pestaña que se abre.
- **Rediseño de las pestañas**: ícono en chip redondeado, estado activo con degradado y
  sombra, hover con elevación, mejor comportamiento en móvil y animación sutil al
  mostrar cada panel.

---

## Notas

- Si un usuario ve solo “Conexión” en baileys_esam, es porque su número aún no está vinculado.
- Para que la analítica de recordatorios muestre datos, debe enviarse al menos un recordatorio de
  cobranza desde cuotas vencidas/próximas (`whatsapp_cobranza`).
- El estado del indicador depende del `uid` (id de usuario de cronogramas) que coincide con
  `owner_uid` de la sesión en baileys_esam.
