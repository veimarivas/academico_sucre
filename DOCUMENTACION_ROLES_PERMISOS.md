# Documentación — Roles, Permisos y Diseño (cronogramas)

Registro de los cambios de **acceso por roles**, **permisos finos** y **rediseño
de vistas** aplicados al sistema `cronogramas.avi-p.site`
(BD `esam_academico`).

- **Ruta:** `C:\xampp_82_12\htdocs\cronogramas.avi-p.site`
- **Panel:** `http://localhost`
- Relacionado: `DOCUMENTACION_WHATSAPP.md` (integración con Seguimiento ESAM).

---

## 1. Roles del sistema

Roles creados y gestionados con **Spatie Permission** (`web`)

| Rol | Permisos | Descripción |
| --- | --- | --- |
| `Super Admin` | 82 (todos) | Acceso total. No se puede renombrar ni eliminar. |
| `Administrador` | 42 | Rol general (sin módulo Académico). |
| `Academicos` | 29 | Área académica (sin pago docente, sesión extra, registrar sesiones, editar/asignar docente). |
| `Responsable Académico` | 33 | **Nuevo.** Base de `Academicos` + `sesión extra`, `registrar sesiones`, `pago docente/gastos`, `cambiar estado`. |
| `Responsable Académico UPI` | 5 | **Nuevo.** Solo asignar docentes, y únicamente en programas UPI (`UPI Diplomados` / `UPI Diplomado Autogestionado`). |
| `Contabilidad` | 25 | Área contable + Personas, tabs contables, Marketing y Publicidad. |
| `Gerencia Marketing` | 9 | Marketing (sin Publicidad). |
| `Supervisor de Marketing` | 9 | Marketing (sin Publicidad). |
| `Asesor de Marketing` | 1 | Solo `marketing.publicidad.subir` (carga desde su perfil). |
| `Consulta` | 11 | Solo listados operativos (sin Seguridad ni Académico). |

---

## 2. Permisos nuevos

| Permiso | Asignado a | Uso |
| --- | --- | --- |
| `programas.modulos.pago.docente` | Super Admin, Responsable Académico | Configurar/procesar pago docente y gastos. |
| `programas.modulos.cambiar.estado` | Super Admin, Responsable Académico | Cambiar estado de módulo. |
| `asesores.gestionar` | Super Admin | Editar/eliminar asesores y cargos. |

Permisos ya existentes reutilizados: `whatsapp.admin`, `contable.*`,
`marketing.publicidad.*`, `programas.tab.*`, `programas.modulos.*`.

---

## 3. Middlewares nuevos

Registrados en `bootstrap/app.php`:

| Alias | Clase | Reglas |
| --- | --- | --- |
| `programa.acceso` | `App\Http\Middleware\EnsureProgramaAccess` | Modos: `responsable`, `baja`, `cambiar-estado`, `asignar-docente`. Resuelve el programa desde `programa`/`id`, `modulo` o `horario` (ruta o input). Super Admin siempre pasa. |
| `whatsapp.autorizado` | `App\Http\Middleware\EnsureWhatsappAuthorized` | Super Admin o usuario con `whatsapp_autorizado`. |

### `programa.acceso`
- `responsable`: Super Admin o **responsable asignado** del programa
  (`programas_responsables` + `users.responsable_id`; soporta varios responsables).
- `baja`: además permite al **área contable** (`Contabilidad` o `contable.cobrar`).
- `cambiar-estado`: además permite al rol **`Responsable Académico`**.
- `asignar-docente`: Super Admin, `Responsable Académico` (cualquier tipo) y
  **`Responsable Académico UPI` solo si el programa es `UPI Diplomados` (6) o
  `UPI Diplomado Autogestionado` (7)**.

Helper en `App\Models\User`: `esResponsableDelPrograma()`, `esAreaContable()`,
`puedeWhatsappPrograma()`, `puedeGestionarBaja()`, `canAccessMarketing()`.

---

## 4. Matriz de acceso por módulo / vista

### 4.1 General
- **Dashboard** y **Programas Vigentes** → `dashboard.listar`. Al rol
  `Asesor de Marketing` se le revocó (`..._150000`).
- **Login/raíz**: todo usuario autenticado aterriza en **su perfil**
  (`AuthenticatedSessionController@store` → `admin.profile`).

### 4.2 Académico
- **Programas / Folios** → `programas.listar`
  (Super Admin, Academicos, Responsable Académico, Contabilidad).
- **Tipos / Áreas** (ver) → `tipos.listar` / `areas.listar`.
  Editar/Eliminar → `role:Super Admin`.
- **Cronogramas** → `role:Super Admin|Academicos|Gerencia Marketing|Supervisor de Marketing`.
- **Seguimiento Académico / Diario / Control Responsables / Responsables**
  → `role:Super Admin|Responsable Académico`.
- **Crear/editar/eliminar programa, asignar responsable, costos universidad**
  → `role:Super Admin`.
- **Rebalancear cuotas** → `role:Super Admin|Administrador`.
- **Pagos a Docentes** → `role:Super Admin|Responsable Académico|Contabilidad`.

### 4.3 Programas — acciones en la vista `show`
- **Inscripciones (Total Registros / Fase Formativa)**
  - Editar inscripción / Eliminar inscripción → **Super Admin**.
  - Gestionar baja → **responsable del programa + área contable + Super Admin**
    (`programa.acceso:baja`); documentos de baja (Fase Formativa) → **solo área contable**.
- **Módulos / Seguimiento Académico**
  - Cambiar estado → `$puedeCambiarEstado` (Super Admin, Responsable Académico o responsable).
  - Editar (Moodle) / Asignar docente → **Super Admin** y `Responsable Académico`
    (cualquier tipo); `Responsable Académico UPI` solo en programas UPI
    (`UPI Diplomados` / `UPI Diplomado Autogestionado`).
  - Sesión extra / Registrar sesiones / Pago docente y gastos → Super Admin + Responsable Académico.
  - WhatsApp por módulo → **responsable del programa** (`programa.acceso:responsable`).
- **Tabs**:
  - Marketing (Total Inscritos reales) → todos con acceso.
  - Contabilidad / Historial → solo `Contabilidad` (+ Super Admin).
  - Desarrollo Académico → Super Admin, Contabilidad, Academicos, Responsable Académico.
  - Seguimiento Contable → Super Admin, Contabilidad.
  - Rentabilidad / Registros Universidad → solo **Super Admin**.

### 4.4 Personas
- Acceso (ver) → `role:Super Admin|Contabilidad|Academicos|Responsable Académico`.
- **Registrar/editar** docentes y estudios → `role:Super Admin|Responsable Académico`.
- Registrar/editar participantes se hace desde el programa (inscripciones).

### 4.5 Contable
- Todo el módulo (`contable.*`, `comprobantes-pago.*`) → `role:Super Admin|Contabilidad`.
- `Contabilidad` recibió `contable.listar`, `contable.asignaciones.listar/definir`,
  `contable.cobrar`, `contable.cuotas-vencidas/proximas.listar`.
- **WhatsApp**: se sacó de Contable y se movió a la sección propia
  **“WhatsApp Seguimiento ESAM”**.

### 4.6 Marketing
- Módulo Marketing + Masterclass → `role:Super Admin|Contabilidad|Gerencia Marketing|Supervisor de Marketing`
  (**sin `Asesor de Marketing`**).
- **Publicidad** (índice, presupuestos, autogestión, comprobantes y acciones)
  → `role:Super Admin|Contabilidad`.
- Editar/eliminar asesores y cargos → `permission:asesores.gestionar` (Super Admin).

### 4.7 Seguridad
- Todo el módulo (`seguridad.*`: usuarios, roles, permisos) → `role:Super Admin`.
- El enlace **Folios** se movió de Seguridad al apartado **Académico**.

### 4.8 WhatsApp Seguimiento ESAM
- Sección del sidebar visible para usuarios **autorizados**
  (`whatsapp_autorizado`) o Super Admin (`whatsapp.autorizado`).
- `GET /admin/ajustes/whatsapp` y `/admin/seguimiento-esam` → `whatsapp.autorizado`.
- `POST /admin/ajustes/whatsapp` (guardar) → `whatsapp.admin`.
- La vista muestra solo lectura para no-admin.
- Los **roles Spatie** del usuario se envían por **SSO** al panel de Seguimiento
  ESAM (`roles` en el token firmado); allí el administrador ve todo y el resto
  solo Conversaciones y Conexión. Ver `DOCUMENTACION_WHATSAPP.md` §1.

---

## 5. Manejo global de errores

En `bootstrap/app.php` (`withExceptions`):
- **403** (Spatie `UnauthorizedException`, `abort(403)`, `AuthorizationException`):
  - Web → redirect a `admin.profile` con `error` = *“No tenés permisos para acceder a esa vista.”*
  - JSON/AJAX → `403 {"message": ...}`.
- **404** → redirect a `admin.profile` (o login) con aviso.

Así, cualquier acceso no autorizado **redirige al perfil con mensaje** en lugar
de la pantalla de error.

---

## 6. Perfil (`admin.profile`)

- **Redirección de login** al perfil.
- **Pestañas** rediseñadas (pills en tarjeta, activo en degradado, conteo, scroll móvil).
- **Información**: para todos (tarjeta de identidad + datos + actividad/seguridad).
- **Ofertas en inscripción / Inscripciones / Presupuesto publicidad**:
  solo `Super Admin`, `Gerencia Marketing`, `Supervisor de Marketing`, `Asesor de Marketing`
  (`User::canAccessMarketing()`), en nav, paneles, KPIs y dropdown del topbar.
- Endpoints del perfil protegidos por `authorizeMarketing()`
  (`publicidadData`, `storeGastoAutogestion`, `inscritosPrograma`).
- Sidebar: se agregó **Cuenta → Mi perfil** para todos.

---

## 7. Migraciones de roles/permisos

| Migración | Cambio |
| --- | --- |
| `2026_09_13_140000_revoke_seguridad_from_consulta_role` | Quita Seguridad a `Consulta`. |
| `2026_09_13_150000_revoke_dashboard_from_asesor_marketing_role` | Quita Dashboard/Programas Vigentes a `Asesor de Marketing`. |
| `2026_09_13_160000_revoke_cronogramas_from_asesor_marketing_role` | Quita Cronogramas a `Asesor de Marketing`. |
| `2026_09_13_170000_restrict_academico_to_academicos_role` | Académico solo para `Academicos`/Super Admin (revoca a Administrador/Consulta). |
| `2026_09_13_180000_add_responsable_academico_and_module_permissions` | Crea permisos de módulo y el rol `Responsable Académico`; ajusta `Academicos`. |
| `2026_09_13_190000_grant_desarrollo_tab_to_contabilidad` | `programas.tab.desarrollo` a `Contabilidad`. |
| `2026_09_13_200000_grant_contable_permissions_to_contabilidad` | Permisos contables a `Contabilidad`. |
| `2026_09_13_210000_grant_personas_to_contabilidad` | Permisos de Personas a `Contabilidad`. |
| `2026_09_13_220000_grant_marketing_to_contabilidad` | Permisos de Marketing/Publicidad a `Contabilidad`. |
| `2026_09_13_230000_add_asesores_gestionar_permission` | Crea `asesores.gestionar` (Super Admin). |
| `2026_09_21_100000_grant_asignar_docente_to_responsable_academico` | Asignar docente a `Responsable Académico`. |
| `2026_09_22_100000_add_responsable_academico_upi_role` | Crea el rol `Responsable Académico UPI` (asignar docente solo en programas UPI). |

`database/seeders/PermissionsSeeder.php` fue actualizado para instalaciones nuevas
(Administrador/Consulta sin Académico, `baseAcademicos` compartida, rol
`Responsable Académico`, nuevos permisos).

---

## 8. Correcciones de estructura

- **`programas/show.blade.php`**: se corrigió un `@can('programas.tab.contabilidad')`
  **sin cerrar** que envolvía todos los tabs siguientes (por eso solo funcionaba
  para Super Admin). Ahora la estructura Blade está balanceada y los tabs se
  muestran según su propio gate.
- **Pantallas a prueba de roles**: se blindó JS con `?.`/`filter(Boolean)` donde
  se referencian botones o modales que pueden no existir para ciertos roles
  (rentabilidad, folios, docentes, asesores, etc.).
- **`ResponsableController@detalle`**: se agregó `docente_id` a los módulos para
  que el enlace al docente se genere correctamente en el tab **Mis programas**.

---

## 9. Rediseño de vistas

Se unificó el lenguaje visual (hero navy/teal con degradado, radio 18–20, sombras
profundas, KPIs con íconos, chips y pills con buen contraste).

| Vista | Mejoras |
| --- | --- |
| `admin/profile/index` | Tabs profesionales, tarjeta de identidad, gating de marketing. **Tab "Mis programas"**: contenido dinámico con contenedores con fondo y texto legible en claro/oscuro, hero con color del responsable robusto (hex de 3 o 6 dígitos), estados de módulo con color por estado, enlaces con contraste en modo oscuro, estados vacío/carga/error y sub-tabs "Programas / Seguimiento Semanal". |
| `admin/programas/index` | Stats por tipo en una fila y clicables; acciones solo Super Admin. |
| `admin/programas/show` | Corrección estructural; gating por tab; columnas de módulos más angostas. |
| `admin/programas/whatsapp` | Envío visible a responsables. |
| `admin/programas/participante-detalle` | Hero rediseñado (identidad + stats claras con número oscuro sobre tarjeta blanca); registrar pago/pago general solo Super Admin + Contabilidad. |
| `admin/contable/retencion` | Rediseño completo (hero, KPIs, anillo de cobranza, tabla por programa sin scroll horizontal). |
| `admin/contable/*` | Hero unificado (seguimiento, asignaciones, cuotas, comprobantes). |
| `admin/marketing/estadisticas` | Stats en una fila; Top Asesores sin scroll (chips de puntos); Inscripciones por Programa a ancho completo. |
| `admin/marketing/inversiones` | Layout reordenado; Ranking a ancho completo sin scroll; gráficos con fondo por serie. |
| `admin/marketing/publicidad/*` | Hero unificado; Mis Comprobantes con cabecera de tarjeta, estados con pill y chips. |
| `admin/marketing/asesores-lista` | Editar/eliminar solo Super Admin; tablas sin scroll; diseño. |
| `admin/marketing/asesores/show` | Stats del hero con contraste; comprobantes de publicidad rediseñados. |
| `admin/seguridad/{usuarios,permisos,roles}` | Rediseño total: KPIs, tarjetas/grillas, tabs por rol; `role-chip` sólido con texto legible. |

---

## 10. Verificación

- `php -l` en rutas/middlewares/migraciones.
- `php artisan migrate` (todas las migraciones aplicadas).
- `php artisan view:cache` (todas las vistas compilan).
- `php artisan route:list -v` (middleware `role:`/`permission:`/`programa.acceso`/
  `whatsapp.autorizado` aplicados).
- Comprobaciones de balance de directivas Blade del sidebar y `show`.
