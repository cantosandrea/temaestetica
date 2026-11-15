# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico y el text domain son `temaestetica`; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- Versiones de referencia: tema `0.3.0`, plugin `0.4.0`, esquema `1.1.0`, docs `0.7.0`.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado; no disponible en este entorno, se desarrolla en otro equipo). El tema solo consume sus repositorios y su REST; no pongas reglas de agenda acá.
- Fase 3 cerrada: `/turnos/` tiene wizard público de servicios, profesional, fecha/hora, retención y orden. Fase 4 (pagos) cerrada en sandbox: checkout, webhooks reales, pagos pendientes/rechazados/aprobados, conciliación de holds vencidos y reembolso completo verificados. La producción y el cobro real quedan fuera de alcance: el proyecto es una práctica local y no se publica.
- Fase 5 (emails) tiene solo una demo visual en `/turnos/` (`#emails`): cuatro tarjetas con vista previa modal de confirmación, recordatorios de 24 h y 2 h y cambio de turno, con datos de ejemplo. No envía correos; el envío real (plantillas, cron, prevención de duplicados y SMTP) sigue pendiente en el plugin. Los prerrequisitos locales (Mailpit + WP Mail SMTP) y la matriz están en `docs/EMAILS.md`.
- Fase 6 (operación) está diferida: la agenda diaria/semanal, los horarios semanales, los estados, las notas, la cancelación/reprogramación manual y el CRUD completo de servicios y profesionales pertenecen al plugin `glowup-turnos`, que se desarrolla en otro equipo y no está disponible en este entorno. La decisión no altera el resultado del tema ni del flujo público de reservas; no implementes esa operación en el tema.
- Mercado Pago oficial `8.10.2` activo en modo test/sandbox (`woo-mercado-pago-basic`). Las credenciales de producción están cargadas pero sin validar, y no hace falta validarlas mientras el proyecto siga siendo una práctica local: no prometas cobro real, no propongas despliegue, no expongas ni commitees claves y no cambies de modo sin pedido explícito. Sin pasarela disponible, el wizard y `order-pay` muestran el aviso de pago futuro.
- Negocio: La Rioja capital; depilación **solo definitiva** (axilas, bozo, cavado, piernas, brazos y bikini, más un pack completo, todos con evaluación previa). No reintroduzcas servicios de cera. Precios ARS con IVA incluido; pago total al reservar; cancelación con reembolso hasta 24 h antes; reprogramación hasta 12 h antes; recordatorios solo por email (sin WhatsApp). Gift card simple incluida; packs/bonos completos y facturación fiscal quedan para post-MVP.
- Desarrollo local en `http://localhost/esteticaweb`: WordPress `7.1.4`, WooCommerce `11.3.0`, PHP `8.2.12`, MariaDB `10.4.32` en `localhost:3307`.
- Acceso externo por ngrok: `wp-config.php` (fuera de los repos) define `WP_HOME`/`WP_SITEURL` dinámicos según `HTTP_HOST` y `X-Forwarded-Proto`. No hardcodees `localhost` en URLs nuevas ni toques ese bloque sin probar localhost y ngrok; `wp option get home` sigue mostrando localhost porque mandan las constantes. Mercado Pago no notifica a `localhost`: iniciá el checkout desde la URL de ngrok vigente para probar webhooks.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP:

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- Lint de JS: `node --check assets\js\booking.js` y `node --check assets\js\main.js` (Node está disponible).
- Lint de todo el tema (PowerShell, sin la copia anidada): `Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notlike '*\glowup-turnos\*' } | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`
- Lint del plugin: el mismo comando dentro de `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos`.
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Para datos/opciones o smoke tests, usá `wp eval-file archivo.php`; evita `wp eval` con comillas anidadas en PowerShell. No hay WC-CLI completo (`wp wc` no expone `order`): para pedidos usá `wc_get_order()`/`wc_get_order_notes()` dentro de `eval-file`.
- Correo local: Mailpit en `C:\xampp\mailpit` (SMTP `127.0.0.1:1025`, bandeja `http://127.0.0.1:8025`). Se inicia con `C:\xampp\mailpit\mailpit.exe --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025 --database C:\xampp\mailpit\mailpit.db`; hay un acceso directo en el inicio de sesión. WP Mail SMTP `4.10.4` está activo y configurado contra ese SMTP.

No hay `package.json`, Composer, bundler ni suite de tests/CI. Los assets se sirven directo y no hay paso de build: `style.css` solo contiene la cabecera del tema; editá `assets/css/main.css`, `assets/js/main.js` y `assets/js/booking.js`.

## Arquitectura del tema

- `functions.php` define `GLOWUP_VERSION` y carga `inc/`; los hooks están ahí y en cada módulo. La versión está duplicada en `style.css` y en `GLOWUP_VERSION`: si la subís, actualizá ambas.
- `inc/content.php` contiene servicios, profesionales, FAQ, testimonios y políticas. Si el plugin está activo lee sus repositorios; si no, usa los arrays de respaldo. Servicios y profesionales no son posts: viven en `wp_glowup_*`, no los busques con `WP_Query` ni los modeles como CPT. No dupliques esos datos en las plantillas.
- Las páginas Servicios, Nosotras, Contacto y Turnos usan plantillas con contenido hardcodeado. Editá el PHP, no el editor de bloques. La página “Inicio” también se ignora: la home se arma en `front-page.php` con `template-parts/section-*.php`.
- La sección `#emails` de `/turnos/` es una demo visual (`template-parts/section-emails.php` y `template-parts/email-preview.php`) con datos de ejemplo y `<dialog>` nativo. No envíes correo desde el tema ni la conectes a `wp_mail`: el envío real, el cron y la prevención de duplicados pertenecen al plugin.
- No cambies los slugs de páginas (`/turnos/`, `/servicios/`, `/nosotras/`, `/contacto/`) ni las páginas de WooCommerce: hay enlaces, plantillas y menús que dependen de ellos.
- Para imágenes usá `glowup_image()` y los slots del Customizer (`Apariencia > Personalizar > GlowUp · Marca y contacto`); los defaults están en `glowup_image_defaults()` (`inc/template-tags.php`). No hardcodees `<img>` ni URLs de fotos.
- Los colores y las fuentes están duplicados en `theme.json`, `assets/css/main.css` y `assets/css/editor.css`; la carga de Google Fonts está en `inc/assets.php`. Si cambiás la paleta o la tipografía, actualizá los cuatro.
- La UI del wizard está en `page-turnos.php`; `inc/booking.php` concentra login/registro, los helpers de `order-pay` y `glowup_booking_payments_available()`. La disponibilidad de cobro sale de gateways `woo-mercado-pago*`, no de un flag fijo.
- `assets/js/booking.js` es el wizard sin dependencias y solo se encola en `page-turnos.php`. Los booleanos de `wp_localize_script` (p. ej. `paymentAvailable`, `isLoggedIn`) llegan como strings (`"1"`/`""`), no como booleanos JSON: usá truthiness, no los compares contra `true`/`false`.
- La integración con WooCommerce está en `inc/woocommerce.php`; no hay carpeta de overrides `woocommerce/`. Ahí viven la excepción de `order-pay` a la pausa de la tienda (el resto del catálogo, carrito y checkout muestra `coming-soon.php` a visitantes; `manage_woocommerce` ve la tienda real), el aviso de hold/pago, el redirect de hold vencido y el botón de pago condicionado. El badge del carrito sincroniza fragmentos AJAX y el Store API de bloques: si tocás el carrito, verificá ambos caminos.
- Usá el prefijo `glowup_` para funciones y helpers nuevos. El copy va en español rioplatense.

## Reservas (plugin)

- El plugin expone `glowup/v1`: `GET /services`, `/professionals`, `/config` y `/availability` son públicos; `POST /holds` y `POST /orders` requieren login y capacidad `glowup_book_appointments` (o `manage_options`); `GET /appointments` requiere login. `POST /holds` exige `idempotency_key` que cumpla `^[A-Za-z0-9_-]{8,64}$`; `POST /orders` recibe `appointment_id`.
- La zona horaria de reservas es `America/Argentina/La_Rioja` y sale de `Settings::get('timezone')`; para fechas usá `Time`/esa configuración, no `wp_timezone()` ni la zona default de PHP.
- La agenda operativa está en `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes; hay que reprogramar o cancelar a mano.
- La concurrencia de holds se resuelve con `GET_LOCK` por profesional, transacción y consulta de solapamiento; no la reemplaces por un índice único. Los holds duran 15 minutos por defecto (`hold_minutes`) y los vencidos se conservan como `expired`.
- `includes/class-booking-payments.php` engancha los estados de pago de WooCommerce (`woocommerce_order_payment_status_changed`, `woocommerce_order_status_changed`, `_cancelled`, `_refunded`, `woocommerce_order_needs_payment`) y `woocommerce_before_pay_action`, que resincroniza email/nombre/apellido de la orden desde la cuenta de la clienta antes de que la pasarela arme el pago. Un pago tardío solo confirma si el horario sigue libre y futuro; si no, deja el turno `expired`/revisión manual y marca `_glowup_payment_review=yes`.
- La identidad de facturación sale de `user_email`, `first_name` y `last_name` del usuario WP, con los meta `billing_*` como respaldo. No vuelvas a priorizar solo `billing_*`: la orden es una copia y puede quedar vieja.
- La orden usa el producto virtual oculto `Turno GlowUp` (SKU `glowup-turno`) porque Mercado Pago necesita un `WC_Product` real; no uses `product_id = 0`.
- El cron `glowup_turnos_cleanup_holds` vence holds y cancela órdenes `pending`/`failed` asociadas; se asegura en cada arranque con `Activator::ensure_cron()` y en la activación. La disponibilidad ya ignora holds vencidos, pero sin cron la orden queda `pending`: si no corre, verificá `wp_next_scheduled('glowup_turnos_cleanup_holds')`. Si agregás otro cron, programalo en `ensure_cron()` y limpialo en `deactivate()`.
- El webhook de Mercado Pago entra por `/?wc-api=WC_WooMercadoPago_Basic_Gateway&source_news=webhooks` (consulta el pago real contra la API y no duplica la confirmación). Sin `source_news=webhooks` el plugin oficial usa `CoreNotification` y responde `400 Invalid notification id`. La URL viaja en el `notification_url` de cada pago: para probar webhooks hay que iniciar el checkout desde la URL de ngrok, no desde `localhost`.
- Cualquier cambio de reservas va al repo del plugin y se commitea por separado; ese repo no tiene remoto Git configurado, no intentes push. El tema solo consume sus clases y su REST. Si cambiás el esquema, subí `GLOWUP_TURNOS_VERSION` para que `Activator::maybe_upgrade()` ejecute `DB::install()`: cambiar solo `GLOWUP_TURNOS_DB_VERSION` no corre la migración en una instalación existente.

## Documentación y verificación

- `docs/PLAN.md` y `docs/ARQUITECTURA.md` (0.7.0) y `docs/EMAILS.md` (0.1.0) son la referencia de alcance, modelo de datos, fases y emails. Mantenelos sincronizados con el código; `docs/GlowUp-Proyecto.docx` es la versión formal (binaria: no la edites a mano).
- `docs/FASES-REORGANIZADAS.txt` resume el plan por fases; para el estado operativo, contrastá siempre con `glowup-turnos.php`, `class-rest-api.php`, `class-booking-payments.php` y el README del plugin antes de asumir un estado.
- La Fase 5 (emails y recordatorios) se implementa en el plugin, no en el tema: el tema solo aporta la documentación y consume sus clases y su REST. La matriz y las decisiones están en `docs/EMAILS.md`.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, `node --check` sobre el JS tocado, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- En local el correo sale por Mailpit + WP Mail SMTP: verificá la bandeja en `http://127.0.0.1:8025`. Si `wp_mail` falla, revisá que Mailpit esté corriendo y que WP Mail SMTP siga apuntando a `127.0.0.1:1025`.
- Antes de editar o commitear, mirá `git status`: el árbol puede tener trabajo del usuario (por ejemplo, fotos subidas o cambios en `inc/template-tags.php`). No lo pises ni lo incluyas en un commit sin confirmación; usá `git add` selectivo (nunca `git add .` ni `git add -A`). Si aparece una copia anidada `glowup-turnos/`, es un repo del plugin sin trackear: no la edites, borres ni commitees.
- Commits atómicos. Convención: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`. No hagas push sin confirmación del usuario. El tema tiene remoto `origin`; el plugin no tiene remoto configurado.
