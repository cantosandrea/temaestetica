# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico es `temaestetica` y el text domain también; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado). El tema solo consume sus repositorios y su REST; no pongas reglas de agenda acá.
- Fases 0 a 3 cerradas. La Fase 4 (pagos) está iniciada y detenida: `glowup_booking_payments_enabled()` sigue apagado y no hay cobro. Las fases 5 (emails) y 6 (operación) no empezaron. Las tres quedan fuera de la entrega académica. `/turnos/` ya tiene el wizard público; la tienda sigue en “Próximamente”. No agregues cobros, emails ni agenda operativa sin confirmación explícita.
- Negocio: La Rioja capital; depilación **solo definitiva** (axilas, bozo, cavado, piernas, brazos y bikini, más un pack completo, todos con evaluación previa). No reintroduzcas servicios de cera. Precios ARS con IVA incluido. La política del salón es pago total al reservar, pero el cobro no está implementado. Cancelación con reembolso hasta 24 h antes y reprogramación hasta 12 h antes: políticas publicadas, flujos fuera de alcance. Recordatorios solo por email (sin WhatsApp), fuera de alcance. Gift card simple, packs/bonos completos y facturación fiscal quedan fuera de la entrega.
- Desarrollo local en `http://localhost/esteticaweb` como referencia; WordPress 7.1.4, PHP 8.2.12, MariaDB en `localhost:3307`. La ruta de XAMPP cambia según la máquina: verificá que exista antes de correr comandos.
- Acceso externo por ngrok: `wp-config.php` (fuera de los repos) define `WP_HOME`/`WP_SITEURL` dinámicos según `HTTP_HOST` y `X-Forwarded-Proto`. No hardcodees `localhost` en URLs nuevas y no toques ese bloque sin probar localhost y ngrok. `wp option get home` sigue mostrando localhost: es esperado, las constantes mandan en requests web.
- Mercado Pago 8.10.0 instalado, inactivo y sin credenciales. No lo actives: la Fase 4 está fuera de alcance y el cobro sigue apagado.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP. Si tu instalación usa otra raíz, por ejemplo `C:\xamppnuevo`, cambiá las rutas:

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- Lint de todo el tema (PowerShell): `Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`
- Lint del plugin: el mismo comando dentro de `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos`.
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Para datos/opciones o smoke tests, usá `wp eval-file archivo.php`; evita `wp eval` con comillas anidadas en PowerShell.

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

- El plugin expone `glowup/v1`: `GET /availability`, `POST /holds` (requiere sesión con `glowup_book_appointments` e `idempotency_key` que cumpla `^[A-Za-z0-9_-]{8,64}$`), `GET /appointments` (requiere login), más `GET /services`, `/professionals` y `/config`.
- Versión documentada del plugin: `0.3.0`, esquema `1.1.0`. Verificá `glowup-turnos.php` si el repo está montado. La zona horaria de reservas es `America/Argentina/La_Rioja` y sale de `Settings::get('timezone')`; para fechas usá `Time`/esa configuración, no `wp_timezone()` ni la zona default de PHP.
- La agenda operativa está en `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes; hay que reprogramar o cancelar a mano.
- La concurrencia de holds se resuelve con `GET_LOCK` por profesional, transacción y consulta de solapamiento; no la reemplaces por un índice único. Los holds duran 15 minutos por defecto (`hold_minutes`) y los vencidos se conservan como `expired`.
- Cualquier cambio de reservas va al repo del plugin y se commitea por separado; ese repo no tiene remoto Git configurado, no intentes push. El tema solo consume la API.

## Documentación y verificación

- `docs/PLAN.md` y `docs/ARQUITECTURA.md` (en español) son la referencia de alcance, modelo de datos y fases. Mantenelos sincronizados con el código; `docs/GlowUp-Proyecto.docx` es la versión formal (binaria: no la edites a mano).
- La documentación de fases puede quedar desfasada. Contrastá siempre con `page-turnos.php`, `glowup-turnos.php` y el código del plugin antes de afirmar el estado de una fase. La versión y el esquema actuales están en la sección de reservas.
- La entrega académica está documentada en `README.md` y `docs/GUIA-DEMO.md`. No agregues funciones de producción (pagos, emails, hosting) sin pedido explícito.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- Antes de editar o commitear, mirá `git status`: el árbol puede tener trabajo del usuario (por ejemplo, fotos subidas o cambios en `inc/template-tags.php`). No lo pises ni lo incluyas en un commit sin confirmación; usá `git add` selectivo.
- Commits atómicos. Convención: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`. No hagas push sin confirmación del usuario.
