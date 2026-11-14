# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico es `temaestetica` y el text domain también; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- Versiones de referencia: tema `0.2.0`, plugin `0.4.0`, esquema `1.1.0`, docs `0.6.1`.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado). El tema solo consume sus repositorios y su REST; no pongas reglas de agenda acá.
- Fase 3 cerrada y Fase 4 cerrada en sandbox: `/turnos/` tiene wizard público, retención y orden; Mercado Pago confirmó turnos con notificaciones reales (pendiente, rechazado, aprobado), la conciliación de holds vencidos está verificada con el cron real y el reembolso completo desde WooCommerce deja pedido `refunded` y turno `cancelled`. La producción queda fuera de alcance por ahora: el proyecto es una práctica local y no se publica. El próximo paso es la Fase 5 (emails y recordatorios); sus prerrequisitos ya están resueltos: Mailpit + WP Mail SMTP verificados y matriz definida en `docs/EMAILS.md`.
- Mercado Pago `8.10.0` está activo en modo test/sandbox (`woo-mercado-pago-basic`). Las credenciales de producción están cargadas pero sin validar, y no hace falta validarlas mientras el proyecto siga siendo una práctica local: no prometas cobro real, no propongas despliegue, no expongas ni commitees claves y no cambies de modo sin pedido explícito. Sin pasarela disponible, el wizard y `order-pay` muestran el aviso de pago futuro.
- Negocio: La Rioja capital; depilación **solo definitiva** (axilas, bozo, cavado, piernas, brazos y bikini, más un pack completo, todos con evaluación previa). No reintroduzcas servicios de cera. Precios ARS con IVA incluido; pago total al reservar; cancelación con reembolso hasta 24 h antes; reprogramación hasta 12 h antes; recordatorios solo por email (sin WhatsApp). Gift card simple incluida; packs/bonos completos y facturación fiscal quedan para post-MVP.
- Desarrollo local en `http://localhost/esteticaweb`; WordPress 7.1.4, PHP 8.2.12, MariaDB en `localhost:3307`.
- Acceso externo por ngrok: `wp-config.php` (fuera de los repos) define `WP_HOME`/`WP_SITEURL` dinámicos según `HTTP_HOST` y `X-Forwarded-Proto`. No hardcodees `localhost` en URLs nuevas y no toques ese bloque sin probar localhost y ngrok. `wp option get home` sigue mostrando localhost: es esperado, las constantes mandan en requests web. Mercado Pago no puede notificar a `localhost`: para probar webhooks iniciá el checkout desde la URL de ngrok vigente.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP:

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- Lint de JS: `node --check assets\js\booking.js` (Node está disponible).
- Lint de todo el tema (PowerShell, sin la copia anidada): `Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notlike '*\glowup-turnos\*' } | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`
- Lint del plugin: el mismo comando dentro de `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos`.
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Para datos/opciones o smoke tests, usá `wp eval-file archivo.php`; evita `wp eval` con comillas anidadas en PowerShell. No hay WC-CLI completo (`wp wc` no expone `order`): para pedidos usá `wc_get_order()`/`wc_get_order_notes()` dentro de `eval-file`.
- Correo local: Mailpit en `C:\xampp\mailpit` (SMTP `127.0.0.1:1025`, bandeja `http://127.0.0.1:8025`). Se inicia con `C:\xampp\mailpit\mailpit.exe --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025 --database C:\xampp\mailpit\mailpit.db`; hay un acceso directo en el inicio de sesión. WP Mail SMTP está activo y configurado contra ese SMTP.

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
- La integración con WooCommerce está en `inc/woocommerce.php`; no hay carpeta de overrides `woocommerce/`. Ahí viven la excepción de `order-pay` a la pausa de la tienda (el resto del catálogo, carrito y checkout muestra `coming-soon.php`), el aviso de hold/pago, el redirect de hold vencido y el botón de pago condicionado. El badge del carrito sincroniza fragmentos AJAX y el Store API de bloques: si tocás el carrito, verificá ambos caminos.
- Usá el prefijo `glowup_` para funciones y helpers nuevos. El copy va en español rioplatense.

## Reservas (plugin)

- El plugin expone `glowup/v1`: `GET /services`, `/professionals`, `/config`, `/availability`; `POST /holds` y `POST /orders` requieren login y capacidad `glowup_book_appointments` (o `manage_options`); `GET /appointments` requiere login. `POST /holds` exige `idempotency_key` que cumpla `^[A-Za-z0-9_-]{8,64}$`; `POST /orders` recibe `appointment_id`.
- La zona horaria de reservas es `America/Argentina/La_Rioja` y sale de `Settings::get('timezone')`; para fechas usá `Time`/esa configuración, no `wp_timezone()` ni la zona default de PHP.
- La agenda operativa está en `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes; hay que reprogramar o cancelar a mano.
- La concurrencia de holds se resuelve con `GET_LOCK` por profesional, transacción y consulta de solapamiento; no la reemplaces por un índice único. Los holds duran 15 minutos por defecto (`hold_minutes`) y los vencidos se conservan como `expired`.
- `includes/class-booking-payments.php` engancha los estados de pago de WooCommerce (`woocommerce_order_payment_status_changed`, `woocommerce_order_status_changed`, `_cancelled`, `_refunded`, `woocommerce_order_needs_payment`) y `woocommerce_before_pay_action`, que resincroniza email/nombre/apellido de la orden desde la cuenta de la clienta antes de que la pasarela arme el pago. Un pago tardío solo confirma si el horario sigue libre y futuro; si no, deja el turno `expired`/revisión manual y marca `_glowup_payment_review=yes`.
- La identidad de facturación sale de `user_email`, `first_name` y `last_name` del usuario WP, con los meta `billing_*` como respaldo. No vuelvas a priorizar solo `billing_*`: la orden es una copia y puede quedar vieja.
- La orden usa el producto virtual oculto `Turno GlowUp` (SKU `glowup-turno`) porque Mercado Pago necesita un `WC_Product` real; no uses `product_id = 0`.
- El cron `glowup_turnos_cleanup_holds` vence holds y cancela órdenes `pending`/`failed` asociadas; se asegura en cada arranque con `Activator::ensure_cron()` y en la activación. La disponibilidad ya ignora holds vencidos, pero sin cron la orden queda `pending`: si no corre, verificá `wp_next_scheduled('glowup_turnos_cleanup_holds')`.
- El webhook de Mercado Pago entra por `/?wc-api=WC_WooMercadoPago_Basic_Gateway&source_news=webhooks` (consulta el pago real contra la API y no duplica la confirmación). Sin `source_news=webhooks` el plugin oficial usa `CoreNotification` y responde `400 Invalid notification id`. La URL viaja en el `notification_url` de cada pago: para probar webhooks hay que iniciar el checkout desde la URL de ngrok, no desde `localhost`.
- Cualquier cambio de reservas va al repo del plugin y se commitea por separado; ese repo no tiene remoto Git configurado, no intentes push. El tema solo consume la API.

## Documentación y verificación

- `docs/PLAN.md`, `docs/ARQUITECTURA.md` y `docs/EMAILS.md` (en español) son la referencia de alcance, modelo de datos, fases y emails. Mantenelos sincronizados con el código; `docs/GlowUp-Proyecto.docx` es la versión formal (binaria: no la edites a mano).
- La documentación de fases puede quedar desfasada: contrastá siempre con `glowup-turnos.php`, `class-rest-api.php`, `class-booking-payments.php` y el README del plugin antes de asumir un estado.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, `node --check` sobre el JS tocado, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- En local el correo sale por Mailpit + WP Mail SMTP: verificá la bandeja en `http://127.0.0.1:8025`. Si `wp_mail` falla, revisá que Mailpit esté corriendo y que WP Mail SMTP siga apuntando a `127.0.0.1:1025`. Los emails de la Fase 5 todavía no están implementados; la matriz está en `docs/EMAILS.md`.
- Antes de editar o commitear, mirá `git status`: el árbol puede tener trabajo del usuario (por ejemplo, fotos subidas o cambios en `inc/template-tags.php`). No lo pises ni lo incluyas en un commit sin confirmación; usá `git add` selectivo. La copia anidada `glowup-turnos/` es un repo del plugin sin trackear: no la edites, borres ni commitees.
- Commits atómicos. Convención: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`. No hagas push sin confirmación del usuario.
