# AGENTS.md — GlowUp (`temaestetica`)

## Contexto

- Tema clásico híbrido de WordPress para GlowUp. El slug técnico y el text domain son `temaestetica`; no los cambies aunque la marca visible sea GlowUp.
- Es un tema clásico con `theme.json`, no un block theme: no agregues `templates/*.html` ni `parts/`.
- Versiones: tema `0.3.0`, plugin `0.4.0`, esquema `1.1.0`, docs `0.8.1`, `docs/EMAILS.md` `0.2.0`. La versión del tema está duplicada en `style.css` y `GLOWUP_VERSION` (`functions.php`): si la subís, actualizá ambas.
- La lógica de reservas vive en el plugin hermano `C:\xampp\htdocs\esteticaweb\wp-content\plugins\glowup-turnos` (repo Git separado, sin remoto). Está montado en esta instalación. El tema solo consume sus clases, repositorios y REST; no pongas reglas de agenda acá.
- **El cobro online está apagado**: `glowup_booking_payments_enabled()` devuelve `false` salvo filtro, aunque Mercado Pago `8.10.2` esté instalado y la Fase 4 se haya verificado en sandbox. No lo enciendas, no prometas cobro real y no cambies de modo sin pedido explícito. La producción y el despliegue quedan fuera de alcance.
- Fase 5 es una **demo visual** en `/turnos/#emails`: cuatro tarjetas con `<dialog>` de confirmación, recordatorios de 24 h y 2 h y cambios de turno, con datos de ejemplo. No envía correo. El envío real (plantillas, cron, dedupe, SMTP) pertenece al plugin y está fuera de alcance; `docs/EMAILS.md` es solo diseño.
- Fase 6 (agenda operativa, estados, notas, cancelación/reprogramación manual, CRUD) también pertenece al plugin y está fuera de alcance: no la implementes en el tema. Fase 7 (cierre de la práctica) está completada: `README.md`, `docs/GUIA-DEMO.md`, datos de prueba limpios (0 turnos, 0 órdenes) y revisión final hecha. La tienda pública solo muestra “Próximamente”.
- Negocio: La Rioja capital; depilación **solo definitiva** (axilas, bozo, cavado, piernas, brazos y bikini, más un pack completo, todos con evaluación previa). No reintroduzcas servicios de cera. Precios ARS con IVA incluido; pago total al reservar; cancelación con reembolso hasta 24 h antes; reprogramación hasta 12 h antes; recordatorios solo por email (sin WhatsApp). Gift card simple; packs/bonos completos y facturación fiscal quedan fuera.
- Entorno: WordPress `7.1.4`, WooCommerce `11.3.0`, PHP `8.2.12`, MariaDB `10.4.32` en `localhost:3307`.
- ngrok: `wp-config.php` (fuera de los repos) define `WP_HOME`/`WP_SITEURL` según `HTTP_HOST` y `X-Forwarded-Proto`. No hardcodees `localhost` en URLs nuevas ni toques ese bloque sin probar localhost y ngrok; `wp option get home` muestra localhost porque mandan las constantes.

## Comandos

`php` y `wp` no están en el PATH. Usá las rutas de XAMPP; si tu instalación usa otra raíz (por ejemplo `C:\xamppnuevo`), cambialas.

- Lint de un archivo: `C:\xampp\php\php.exe -l ruta\archivo.php`
- Lint de JS: `node --check assets\js\booking.js` y `node --check assets\js\main.js`.
- Lint de todo el tema (sin la copia anidada): `Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notlike '*\glowup-turnos\*' } | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }`
- WP-CLI: `C:\xampp\php\php.exe C:\xampp\wp-cli\wp-cli.phar --path=C:\xampp\htdocs\esteticaweb <comando>`
- Datos/opciones y smoke tests: `wp eval-file archivo.php`; evitá `wp eval` con comillas anidadas en PowerShell. No hay WC-CLI completo (`wp wc` no expone `order`): usá `wc_get_order()`/`wc_get_order_notes()` dentro de `eval-file`.
- Si WP-CLI falla con `Error establishing a database connection`, revisá que el MySQL de XAMPP (puerto `3307`) esté corriendo; si no arranca, mirá `C:\xampp\mysql\data\mysql_error.log` antes de tocar nada.
- Correo local: Mailpit en `C:\xampp\mailpit` (SMTP `127.0.0.1:1025`, bandeja `http://127.0.0.1:8025`). WP Mail SMTP está instalado, pero el tema no envía correo: solo lo usan WooCommerce y pruebas puntuales de `wp_mail`.

No hay `package.json`, Composer, bundler ni suite de tests/CI. Los assets se sirven directo: `style.css` solo tiene la cabecera; editá `assets/css/main.css`, `assets/js/main.js` y `assets/js/booking.js`.

## Arquitectura del tema

- `functions.php` define `GLOWUP_VERSION` y carga `inc/`; los hooks viven ahí y en cada módulo.
- `inc/content.php` contiene servicios, profesionales, FAQ, testimonios y políticas. Si el plugin está activo lee sus repositorios; si no, usa arrays de respaldo. Servicios y profesionales no son posts: viven en `wp_glowup_*`, no los busques con `WP_Query` ni los modeles como CPT. No dupliques esos datos en las plantillas.
- Las páginas Servicios, Nosotras, Contacto y Turnos tienen contenido hardcodeado: editá el PHP, no el editor de bloques. La página “Inicio” se ignora: la home se arma en `front-page.php` con `template-parts/section-*.php`.
- La sección `#emails` vive en `template-parts/section-emails.php` y `template-parts/email-preview.php`, con `<dialog>` nativo y datos de ejemplo. No la conectes a `wp_mail` ni agregues envío desde el tema.
- No cambies los slugs (`/turnos/`, `/servicios/`, `/nosotras/`, `/contacto/`) ni las páginas de WooCommerce: hay enlaces, plantillas y menús que dependen de ellos.
- Para imágenes usá `glowup_image()` y el Customizer (`Apariencia > Personalizar > GlowUp · Marca y contacto`); los defaults están en `glowup_image_defaults()` (`inc/template-tags.php`). No hardcodees `<img>` ni URLs.
- Colores y fuentes están duplicados en `theme.json`, `assets/css/main.css` y `assets/css/editor.css`; las Google Fonts se cargan en `inc/assets.php`. Si cambiás la paleta o la tipografía, actualizá los cuatro.
- La UI del wizard está en `page-turnos.php`; `inc/booking.php` concentra login/registro, los helpers de `order-pay` y los dos flags de pago: `glowup_booking_payments_enabled()` (switch del tema, default `false`) y `glowup_booking_payments_available()` (gateways `woo-mercado-pago*`). El template combina ambos en `$glowup_pay_online`; no decidas un cobro con un solo flag.
- `assets/js/booking.js` es el wizard sin dependencias y solo se encola en `page-turnos.php`. Los booleanos de `wp_localize_script` (`paymentsEnabled`, `paymentAvailable`, `isLoggedIn`) llegan como strings (`"1"`/`""`): usá truthiness, no los compares con `true`/`false`.
- La integración con WooCommerce está en `inc/woocommerce.php`; no hay carpeta de overrides `woocommerce/`. Ahí viven la excepción de `order-pay` a la pausa de la tienda (el resto del catálogo, carrito y checkout muestra `coming-soon.php` a visitantes; `manage_woocommerce` ve la tienda real), el aviso de hold/pago, el redirect de hold vencido y el botón de pago condicionado. El badge del carrito sincroniza fragmentos AJAX y el Store API de bloques: si tocás el carrito, verificá ambos caminos.
- Usá el prefijo `glowup_` para funciones y helpers nuevos. El copy va en español rioplatense.

## Reservas (plugin)

- REST `glowup/v1`: `GET /services`, `/professionals`, `/config` y `/availability` son públicos; `POST /holds` y `POST /orders` requieren login y capacidad `glowup_book_appointments` (o `manage_options`); `GET /appointments` requiere login. `POST /holds` exige `idempotency_key` que cumpla `^[A-Za-z0-9_-]{8,64}$`; `POST /orders` recibe `appointment_id`.
- Zona horaria de reservas: `America/Argentina/La_Rioja`, desde `Settings::get('timezone')`. Para fechas usá `Time`/esa configuración, no `wp_timezone()` ni la zona default de PHP.
- Agenda operativa: `wp-admin/admin.php?page=glowup-turnos-agenda`. Los bloqueos descuentan disponibilidad pero no cancelan turnos existentes.
- Concurrencia de holds: `GET_LOCK` por profesional, transacción y consulta de solapamiento; no la reemplaces por un índice único. Holds de 15 minutos (`hold_minutes`); los vencidos quedan `expired`.
- `includes/class-booking-payments.php` engancha estados de WooCommerce (`woocommerce_order_payment_status_changed`, `woocommerce_order_status_changed`, `_cancelled`, `_refunded`, `woocommerce_order_needs_payment`) y `woocommerce_before_pay_action`, que resincroniza email/nombre/apellido desde la cuenta. Un pago tardío solo confirma si el horario sigue libre y futuro; si no, queda `expired`/revisión manual con `_glowup_payment_review=yes`.
- La identidad de facturación sale de `user_email`, `first_name` y `last_name` del usuario WP, con los meta `billing_*` como respaldo. No priorices solo `billing_*`: la orden es una copia y puede quedar vieja.
- La orden usa el producto virtual oculto `Turno GlowUp` (SKU `glowup-turno`) porque Mercado Pago necesita un `WC_Product` real; no uses `product_id = 0`.
- Cron `glowup_turnos_cleanup_holds`: vence holds y cancela órdenes `pending`/`failed`. Se asegura en cada arranque con `Activator::ensure_cron()`; si agregás otro cron, programalo ahí y limpialo en `deactivate()`. Sin cron la disponibilidad igual ignora holds vencidos, pero la orden queda `pending`.
- Webhook de Mercado Pago: `/?wc-api=WC_WooMercadoPago_Basic_Gateway&source_news=webhooks`. Sin `source_news=webhooks` el plugin oficial responde `400 Invalid notification id`. La URL viaja en el `notification_url` de cada pago: para probar webhooks hay que iniciar el checkout desde ngrok, no desde `localhost`.
- Cambios de reservas van al repo del plugin y se commitean por separado; no tiene remoto, no intentes push. Si cambiás el esquema, subí `GLOWUP_TURNOS_VERSION` para que `Activator::maybe_upgrade()` ejecute `DB::install()`: cambiar solo `GLOWUP_TURNOS_DB_VERSION` no corre la migración.

## Documentación y verificación

- `docs/PLAN.md` y `docs/ARQUITECTURA.md` (0.8.1) y `docs/EMAILS.md` (0.2.0, solo diseño) son referencia de alcance y diseño; `docs/GlowUp-Proyecto.docx` es la versión formal (binaria, no la edites a mano). El `.docx` quedó en 0.5.0 y no hay Word ni pandoc en este entorno para regenerarlo: si se actualiza, hacelo desde una herramienta externa, nunca editando el XML a mano.
- Los Markdown de `docs/` y el `README.md` están alineados con el código al 16/11/2026. Si tocás reservas o pagos, contrastá siempre con `glowup-turnos.php`, `class-rest-api.php` y `class-booking-payments.php` antes de dar una afirmación por cierta.
- Antes de cerrar un cambio: `php -l` sobre los archivos tocados, `node --check` sobre el JS tocado, WP-CLI para datos/opciones y navegador para render, consola, enlaces y responsive. No hay tests automatizados.
- Antes de editar o commitear, mirá `git status`: puede haber trabajo del usuario (fotos, cambios en `inc/template-tags.php`). No lo pises; usá `git add` selectivo (nunca `git add .` ni `git add -A`). La copia anidada `glowup-turnos/` es un repo del plugin sin trackear: no la edites, borres ni commitees.
- Commits atómicos: `feat(theme): ...`, `fix(theme): ...`, `docs: ...`; en el plugin, `feat(booking): ...` / `fix(booking): ...`. No hagas push sin confirmación. El tema tiene remoto `origin`; el plugin no.
