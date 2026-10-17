# Plan de mejora móvil (responsivo) — Sistema de Cronogramas AVIP

Objetivo: que el panel funcione con comodidad en teléfonos y tablets, con un
acabado **profesional, elegante y amigable**, **sin alterar la lógica ni el
funcionamiento actual**. Todos los cambios son de presentación (CSS) o ajustes
mínimos de marcado/atributos.

---

## 1. Diagnóstico

El panel usa el tema base (tipo Velzon) + capas propias:

| Capa | Archivo | Rol |
|---|---|---|
| Base | `public/backend/assets/css/app.min.css` | layout, sidebar, topbar |
| Tema AVIP | `avip-theme.css` | tokens de color, tipografía, layout |
| Componentes | `avip-ui.css` | héroes, tarjetas, tablas, modales (se carga al final) |
| Por módulo | `avip-academico.css`, `avip-marketing.css`, `avip-contable.css`, `avip-profile.css`, `avip-auth.css` | estilos por área |
| Por vista | bloques `<style>` dentro de cada `.blade.php` | decenas de reglas con tamaños fijos |

Hallazgos principales (los que sí rompen en móvil):

1. **Sin capa móvil global.** Cada vista improvisa; ~30 bloques `<style>` de
   vistas no tienen ni una `@media`, con `min-width` de 130–320 px y
   `white-space: nowrap`.
2. **Topbar.** El desplegable de notificaciones lleva `width:400px` en línea →
   desborda la pantalla en teléfonos. El menú de usuario tiene `min-width:260px`.
   El botón de tema (claro/oscuro) está oculto en pantallas < 576 px, así que
   **desde el celular no se puede cambiar el tema**.
3. **Sidebar móvil sin velo.** No existe el `div.vertical-overlay` que espera el
   JS del tema: al abrir el menú no hay fondo oscuro para cerrar tocando fuera.
4. **Tablas.** 95 tablas en el panel; 63 tienen `.table-responsive` y **32 no**
   (p. ej. `programas/partials/table.blade.php`, `marketing/inversiones`,
   `marketing/asesores-lista`, tablas dentro de modales en `programas/show`).
   Las no envueltas se recortan en lugar de desplazarse.
5. **Controles nativos y colores.** No se declara `color-scheme`, así que en
   algunos móviles los `select`/date-pickers/scrollbars se pintan en claro
   aunque el tema esté en oscuro (y viceversa). No hay `theme-color` ni
   `viewport-fit=cover` (muescas/iPhone).
6. **Detalles de rejilla.** Tarjetas `col-3` / `col` sin variante móvil, filas
   `d-flex` sin `flex-wrap`, `.plan-switch` en 2 columnas fija.

---

## 2. Estrategia

- **Una sola capa nueva al final del cascade**: `avip-mobile.css`, cargada
  después de `avip-ui.css` y de los CSS por módulo, antes de `@stack('css-after')`.
  Así gana a los estilos de página sin tocar el resto.
- **Solo tokens y selectores existentes**: se reutilizan las variables
  (`--ui-*`, `--avip-*`) en lugar de introducir colores nuevos.
- **Ajustes de marcado mínimos y reversibles** (atributos y un `div`), sin tocar
  controladores, rutas ni JS de negocio.
- **Breakpoints**: `991.98px` (tablet), `767.98px` (móvil), `575.98px` (móvil
  pequeño).

---

## 3. Fases

### Fase 1 — Fundación móvil (global, bajo riesgo) ✅ incluida en este cambio
- `avip-mobile.css` nuevo: topbar, sidebar off-canvas, héroes, tablas,
  formularios (anti-zoom iOS), pestañas desplazables, modales, `color-scheme`.
- `admin/layouts/master.blade.php`:
  - `viewport-fit=cover` + `theme-color` claro/oscuro + safe-areas.
  - carga de `avip-mobile.css` al final.
  - `div.vertical-overlay` (velo del menú móvil).
  - script que cierra el menú al tocar un enlace (solo en móvil).
- `admin/partials/topbar.blade.php`: botón de tema visible también en móvil.

### Fase 2 — Tablas y rejillas por vista (media) ✅
- Envolver en `.table-responsive` las tablas que faltan.
- `col-3` / `col` → `col-6 col-md-3` en tarjetas de estadística.
- `flex-wrap` en filas `d-flex` que desbordan; `min-width` reducidos.
- Tabla de programas: en móvil deja de recortar la columna «Programa».
- Tablas sin datos: cabecera oculta y mensaje vacío centrado.

### Fase 3 — Pulido por módulo (media) ✅
- Héroes y filtros en `marketing/*`, `contable/*`, `cronogramas/*`
  (los que no tienen `@media` propia).
- Modales: cuerpo desplazable y pie con botones a ancho completo en móvil.
- Correcciones puntuales: `seguridad/roles`, `seguridad/permisos`,
  `seguimiento-diario`, cabeceras de tarjeta con badges.

### Fase 4 — Verificación (bajo riesgo) ✅
- Repaso a 320 / 360 / 390 / 414 / 768 px, tema claro y oscuro, en las vistas
  más usadas: dashboard, programas, participantes, cronogramas, contable,
  marketing.
- Documentar en `CAMBIOS.md` los ajustes nuevos.

### Fase 5 — Acceso y marca en móvil ✅
- El login móvil mostraba el logo de la plantilla (Velzon). Ahora el layout de
  acceso (`layouts/guest.blade.php`) incluye la **marca ESAM**
  (`logo-sm.png` + «Plataforma de Cronogramas» + filete dorado), visible solo
  hasta `991.98px`; en escritorio se mantiene la columna lateral con el logo
  institucional.
- Aplica a todas las pantallas de acceso (login, registro, recuperar/restablecer
  contraseña, verificar correo, confirmar contraseña).

---

## 5. Estado (2026-10-17)

- Fases 1–5 completadas. Verificado con Chrome headless + CDP sobre 31 rutas del
  panel a **320 px** y **390 px**: `pageOverflow = false` en todas y sin elementos
  desbordados (salvo `svg` de ApexCharts y enlaces `text-truncate`, que se recortan
  por diseño).
- Acceso (login / recuperar / registro) a 320, 390 y 820 px con la marca ESAM
  centrada; escritorio 1280 px sin cambios en la columna lateral.
- Menú móvil: velo visible y funcional, `body` bloqueado al abrir.
- `php artisan view:cache` sin errores.

---

## 4. Qué NO se toca

- Rutas, controladores, modelos, migraciones y permisos.
- Librerías del tema (`app.min.css`, `layout.js`, `app.js`) — no se parchean.
- Estructura de datos ni consultas de las vistas.
- El comportamiento de escritorio: todo va bajo `@media`.
