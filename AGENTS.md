# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico es `temaestetica` y el text domain también; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado). El tema solo consume sus repositorios y su REST; no pongas reglas de agenda acá.
- `/turnos/` es un “próximamente” intencional hasta que el wizard de Fase 3 esté operativo. No lo conectes a medias ni prometas reservas desde la home.
- Desarrollo solo local: `http://localhost/esteticaweb`; WordPress 7.1.4, PHP 8.2.12, MariaDB en `localhost:3307`.
- Reglas cerradas: pago total al reservar; cancelación con reembolso hasta 24 h antes; reprogramación hasta 12 h antes; recordatorios solo por email (sin WhatsApp); precios en ARS con IVA incluido.
- Mercado Pago está instalado pero inactivo y sin credenciales. No lo actives ni cargues claves hasta que la Fase 4 lo requiera.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP:

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Lint de todo el tema (PowerShell): `Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`

No hay `package.json`, Composer, bundler ni suite de tests/CI. Los assets se sirven directo: editá `assets/css/main.css` y `assets/js/main.js`; no busques un paso de build.

## Arquitectura del tema

- `functions.php` define constantes y carga `inc/`; los hooks están ahí y en cada módulo.
- `inc/content.php` contiene servicios, profesionales, FAQ, testimonios y políticas. Si el plugin está activo, lee sus repositorios; si no, usa los arrays de respaldo. No dupliques esos datos en las plantillas.
- Servicios y profesionales no son posts: viven en tablas propias del plugin (`wp_glowup_*`). No los busques con `WP_Query` ni los modeles como CPT.
- Las páginas Servicios, Nosotras, Contacto y Turnos usan plantillas con contenido hardcodeado. Editá el PHP, no el editor de bloques. La página “Inicio” también se ignora: la home se arma en `front-page.php` con `template-parts/section-*.php`.
- No cambies los slugs de páginas (`/turnos/`, `/servicios/`, `/nosotras/`, `/contacto/`) ni las páginas de WooCommerce: hay enlaces, plantillas y menús que dependen de ellos.
- Para imágenes usá `glowup_image()` y los slots del Customizer (`Apariencia > Personalizar > GlowUp · Marca y contacto`); los defaults están en `glowup_image_defaults()`. No hardcodees `<img>` ni URLs de fotos.
- Los colores y las fuentes están duplicados en `theme.json`, `assets/css/main.css` y `assets/css/editor.css`. Si cambiás la paleta o la tipografía, actualizá los tres.
- La integración con WooCommerce está en `inc/woocommerce.php`; no hay carpeta de overrides `woocommerce/`. El badge del carrito sincroniza fragmentos AJAX y el Store API de bloques: si tocás el carrito, verificá ambos caminos.
- Usá el prefijo `glowup_` para funciones y helpers nuevos. El copy va en español rioplatense.

## Reservas (plugin)

- El plugin expone `glowup/v1`: `GET /availability`, `POST /holds` (requiere sesión con `glowup_book_appointments` e `idempotency_key`), `GET /appointments` (requiere login), más `GET /services`, `/professionals` y `/config`.
- La agenda operativa está en `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes; hay que reprogramar o cancelar a mano.
- Cualquier cambio de reservas va al repo del plugin y se commitea por separado. El tema solo consume la API.

## Documentación y verificación

- `docs/PLAN.md` y `docs/ARQUITECTURA.md` (en español) son la referencia de alcance, modelo de datos y fases. Mantenelos sincronizados con el código; `docs/GlowUp-Proyecto.docx` es la versión formal.
- La documentación de fases puede quedar desfasada: contrastala siempre con `glowup-turnos.php` y el código del plugin antes de asumir qué falta.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- Commits atómicos. Convención: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`.
