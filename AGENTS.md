# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico es `temaestetica` y el text domain también; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- Versiones de referencia: tema `0.2.0`, plugin `0.4.0`, esquema `1.1.0`, docs `0.5.0`.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado). El tema solo consume sus repositorios y su REST; no pongas reglas de agenda acá.
- Fase 3 cerrada: `/turnos/` tiene wizard público de servicios, profesional, fecha/hora, retención y orden. Fase 4 (pagos) tiene el núcleo implementado: guarda de pago, confirmación al acreditarse, cancelación/reembolso, conciliación de holds y revisión de pagos tardíos.
- Mercado Pago `8.10.0` está instalado, inactivo y sin credenciales. No actives la pasarela, no cargues claves ni prometas cobro online sin pedido explícito; sin pasarela el wizard funciona y `order-pay` avisa que el pago online se habilita más adelante.
- Negocio: La Rioja capital; depilación **solo definitiva** (axilas, bozo, cavado, piernas, brazos y bikini, más un pack completo, todos con evaluación previa). No reintroduzcas servicios de cera. Precios ARS con IVA incluido; pago total al reservar; cancelación con reembolso hasta 24 h antes; reprogramación hasta 12 h antes; recordatorios solo por email (sin WhatsApp). Gift card simple incluida; packs/bonos completos y facturación fiscal quedan para post-MVP.
- Desarrollo local en `http://localhost/esteticaweb`; WordPress 7.1.4, PHP 8.2.12, MariaDB en `localhost:3307`.
- Acceso externo por ngrok: `wp-config.php` (fuera de los repos) define `WP_HOME`/`WP_SITEURL` dinámicos según `HTTP_HOST` y `X-Forwarded-Proto`. No hardcodees `localhost` en URLs nuevas y no toques ese bloque sin probar localhost y ngrok. `wp option get home` sigue mostrando localhost: es esperado, las constantes mandan en requests web.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP:

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- Lint de JS: `node --check assets\js\booking.js` (Node está disponible).
- Lint de todo el tema (PowerShell, sin la copia anidada): `Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notlike '*\glowup-turnos\*' } | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`
- Lint del plugin: el mismo comando dentro de `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos`.
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Para datos/opciones o smoke tests, usá `wp eval-file archivo.php`; evita `wp eval` con comillas anidadas en PowerShell.

No hay `package.json`, Composer, bundler ni suite de tests/CI. Los assets se sirven directo: editá `assets/css/main.css`, `assets/js/main.js` y `assets/js/booking.js`; no busques un paso de build.

## Arquitectura del tema

- `functions.php` define constantes y carga `inc/`; los hooks están ahí y en cada módulo.
- `inc/content.php` contiene servicios, profesionales, FAQ, testimonios y políticas. Si el plugin está activo, lee sus repositorios; si no, usa los arrays de respaldo. No dupliques esos datos en las plantillas.
- Servicios y profesionales no son posts: viven en tablas propias del plugin (`wp_glowup_*`). No los busques con `WP_Query` ni los modeles como CPT.
- Las páginas Servicios, Nosotras, Contacto y Turnos usan plantillas con contenido hardcodeado. Editá el PHP, no el editor de bloques. La página “Inicio” también se ignora: la home se arma en `front-page.php` con `template-parts/section-*.php`.
- No cambies los slugs de páginas (`/turnos/`, `/servicios/`, `/nosotras/`, `/contacto/`) ni las páginas de WooCommerce: hay enlaces, plantillas y menús que dependen de ellos.
- Para imágenes usá `glowup_image()` y los slots del Customizer (`Apariencia > Personalizar > GlowUp · Marca y contacto`); los defaults están en `glowup_image_defaults()`. No hardcodees `<img>` ni URLs de fotos.
- Los colores y las fuentes están duplicados en `theme.json`, `assets/css/main.css` y `assets/css/editor.css`. Si cambiás la paleta o la tipografía, actualizá los tres.
- `inc/booking.php` concentra la integración del wizard: login/registro, orden desde `order-pay` y `glowup_booking_payments_available()`. La disponibilidad de cobro sale de gateways `woo-mercado-pago*`, no de un flag fijo.
- `assets/js/booking.js` es el wizard sin dependencias. `paymentAvailable` llega por `wp_localize_script` como booleano JSON: el JS usa truthiness, no lo compares contra strings.
- La integración con WooCommerce está en `inc/woocommerce.php`; no hay carpeta de overrides `woocommerce/`. Ahí viven la excepción de `order-pay` a la pausa de la tienda, el aviso de hold/pago, el redirect de hold vencido y el botón de pago condicionado. El badge del carrito sincroniza fragmentos AJAX y el Store API de bloques: si tocás el carrito, verificá ambos caminos.
- Usá el prefijo `glowup_` para funciones y helpers nuevos. El copy va en español rioplatense.

## Reservas (plugin)

- El plugin expone `glowup/v1`: `GET /services`, `/professionals`, `/config`, `/availability`; `POST /holds` y `POST /orders` requieren login y capacidad `glowup_book_appointments` (o `manage_options`); `GET /appointments` requiere login. `POST /holds` exige `idempotency_key` que cumpla `^[A-Za-z0-9_-]{8,64}$`; `POST /orders` recibe `appointment_id`.
- La zona horaria de reservas es `America/Argentina/La_Rioja` y sale de `Settings::get('timezone')`; para fechas usá `Time`/esa configuración, no `wp_timezone()` ni la zona default de PHP.
- La agenda operativa está en `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes; hay que reprogramar o cancelar a mano.
- La concurrencia de holds se resuelve con `GET_LOCK` por profesional, transacción y consulta de solapamiento; no la reemplaces por un índice único. Los holds duran 15 minutos por defecto (`hold_minutes`) y los vencidos se conservan como `expired`.
- `includes/class-booking-payments.php` engancha los estados de pago de WooCommerce (`woocommerce_order_payment_status_changed`, `woocommerce_order_status_changed`, `_cancelled`, `_refunded`, `woocommerce_order_needs_payment`). Un pago tardío solo confirma si el horario sigue libre y futuro; si no, deja el turno `expired`/revisión manual y marca `_glowup_payment_review=yes`. El cron `glowup_turnos_cleanup_holds` vence holds y cancela órdenes `pending`/`failed` asociadas.
- Cualquier cambio de reservas va al repo del plugin y se commitea por separado; ese repo no tiene remoto Git configurado, no intentes push. El tema solo consume la API.

## Documentación y verificación

- `docs/PLAN.md` y `docs/ARQUITECTURA.md` (en español) son la referencia de alcance, modelo de datos y fases. Mantenelos sincronizados con el código; `docs/GlowUp-Proyecto.docx` es la versión formal (binaria: no la edites a mano).
- La documentación de fases puede quedar desfasada (por ejemplo, `PLAN.md` aún dice “Fase 4 en curso” aunque el núcleo esté commiteado): contrastá siempre con `glowup-turnos.php`, `class-rest-api.php` y `class-booking-payments.php` antes de asumir un estado.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, `node --check` sobre el JS tocado, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- Antes de editar o commitear, mirá `git status`: el árbol puede tener trabajo del usuario (por ejemplo, fotos subidas o cambios en `inc/template-tags.php`). No lo pises ni lo incluyas en un commit sin confirmación; usá `git add` selectivo. La copia anidada `glowup-turnos/` es un repo del plugin sin trackear: no la edites, borres ni commitees.
- Commits atómicos. Convención: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`. No hagas push sin confirmación del usuario.
