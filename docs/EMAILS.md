# GlowUp — Emails y recordatorios

**Versión:** 0.2.0
**Actualizado:** 16 de noviembre de 2026
**Estado:** Fase 5 cerrada como demo visual en `/turnos/#emails`; el envío real no está implementado y queda fuera de alcance.
**Ámbito:** entorno local `http://localhost/esteticaweb`

Este documento conserva el diseño de la Fase 5. La demo visual ya está integrada; **el envío real no está implementado y queda fuera de alcance**. Salvo esa demo, lo que sigue describe cómo se implementaría el envío: transporte, matriz de mensajes, destinatarios, disparadores, idempotencia y plan de pruebas.

## 0. Demo visual implementada (Fase 5)

- `/turnos/#emails` muestra cuatro tarjetas: confirmación, recordatorio de 24 h, recordatorio de 2 h y cambio de turno.
- Cada tarjeta abre un `<dialog>` nativo con datos de ejemplo.
- No usa `wp_mail`, cron ni SMTP: no envía correos.
- El resto de este documento es diseño para una implementación futura en el plugin `glowup-turnos`.

---

## 1. Transporte local

| Pieza | Configuración |
| --- | --- |
| Servidor de correo | Mailpit `1.33.1` en `C:\xampp\mailpit` |
| SMTP | `127.0.0.1:1025`, sin autenticación ni TLS |
| Bandeja web | `http://127.0.0.1:8025` |
| Base de Mailpit | `C:\xampp\mailpit\mailpit.db` |
| Conector de WordPress | WP Mail SMTP `4.10.4`, mailer “Other SMTP” |
| Remitente | `GlowUp <no-reply@glowup.local>` |
| Respuestas | `Reply-To: hola@glowup.local` (valor de `notification_email`; lo agrega el plugin al armar cada email en la Fase 5) |

Verificado el 14 de noviembre de 2026 (para WooCommerce y pruebas puntuales; la demo de reservas no envía correos):

- `wp_mail()` devuelve `true` y el mensaje aparece en Mailpit.
- Un email real de WooCommerce (`WC_Email_Customer_Processing_Order`) llega a la bandeja con el remitente configurado.
- Los emails ya no fallan con “No se pudo establecer la función de correo”.

> El transporte local fija el remitente, pero todavía no el `Reply-To`: WP Mail SMTP no lo configura y el plugin lo hará por email recién en la Fase 5.

### Arranque

Mailpit se inicia con:

```powershell
C:\xampp\mailpit\mailpit.exe --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025 --database C:\xampp\mailpit\mailpit.db
```

Queda disponible un acceso directo en el inicio de sesión de Windows (`Mailpit GlowUp`) para que el transporte esté activo después de reiniciar. Si la bandeja no responde, revisar que el proceso `mailpit` esté corriendo y que los puertos `1025` y `8025` estén libres.

### Diagnóstico rápido

1. Abrir `http://127.0.0.1:8025` y confirmar que la bandeja carga.
2. Enviar un correo de prueba con `wp eval-file` o desde WooCommerce.
3. Si no llega, revisar `C:\xampp\mailpit\mailpit.log` y la configuración de WP Mail SMTP en `Ajustes → Email`.
4. Si WordPress vuelve a usar `mail()`, verificar que WP Mail SMTP esté activo y que el mailer sea `smtp`.

---

## 2. Decisiones de arquitectura

1. **El plugin es dueño de los emails de reserva.** `glowup-turnos` arma y envía los mensajes de ciclo de vida del turno con `wp_mail` y plantillas propias. No se usan las plantillas genéricas de WooCommerce para las órdenes de reserva.
2. **WooCommerce sigue siendo dueño de los emails de cuenta y de pedidos que no son reservas.** En la tienda futura, la tienda pública conservaría sus emails actuales.
3. **Se suprimen los emails genéricos de WooCommerce para órdenes de reserva.** Evita duplicados y copy incorrecto (“tu pedido está en camino” para un turno). El detalle está en la sección 7.
4. **Un evento, un email.** Cada clave de email se envía una sola vez por turno/orden. La idempotencia se registra en una tabla propia, no en transients.
5. **Destinatarios:**
   - Clienta: email de la cuenta de WordPress (`user_email`), no `billing_email`.
   - Copia interna: `Settings::get('notification_email')`, con respaldo en `get_option('admin_email')`.
   - Profesional: en el MVP no recibe un email separado; la copia interna y la agenda del panel cubren el aviso. Se puede agregar más adelante sin cambiar la matriz.
6. **Formato:** HTML simple con CSS inline y versión de texto plano. Sin imágenes externas obligatorias, sin trackers y sin adjuntos.
7. **Zona horaria:** todas las fechas se arman con `GlowUp\Turnos\Time` y la zona `America/Argentina/La_Rioja`. No se usa `wp_timezone()` ni la zona del servidor.
8. **Remitente y respuestas:** `no-reply@glowup.local` para el entorno local; `Reply-To` apunta a `notification_email` para que la clienta pueda responder. En producción se reemplaza por un dominio verificado con SPF y DKIM; eso queda fuera de alcance.
9. **Filtros de extensión:** `glowup_email_from_name`, `glowup_email_from_address`, `glowup_email_reply_to` y `glowup_email_enabled` permiten ajustar el comportamiento sin tocar las plantillas.

---

## 3. Matriz de emails a la clienta

| Clave | Evento | Disparador | Asunto | Contenido mínimo | Idempotencia |
| --- | --- | --- | --- | --- | --- |
| `booking_created` | Reserva registrada, pendiente de pago | Después de crear y asociar la orden en `Booking_Orders::create_from_appointment()` | `Reserva registrada: {service} el {date} a las {time}` | Código, servicio, profesional, fecha/hora, duración, total, botón “Pagar ahora”, vencimiento de la retención (15 min), políticas y contacto | `booking_created:{order_id}` |
| `booking_confirmed` | Pago acreditado y turno confirmado | Cuando el turno pasa a `confirmed` (pago normal o pago tardío con horario libre) | `Turno confirmado: {service} el {date} a las {time}` | Código, servicio, profesional, fecha/hora, duración, total pagado, dirección del salón, políticas, botón “Ver mi turno” | `booking_confirmed:{order_id}` |
| `booking_payment_failed` | Pago rechazado | Orden pasa a `failed` | `No pudimos procesar el pago de tu reserva {code}` | Código, servicio, fecha/hora, motivo genérico, botón “Reintentar pago” si la retención sigue vigente, aviso de vencimiento y contacto | `booking_payment_failed:{order_id}` |
| `booking_cancelled` | Turno cancelado sin reembolso | Turno pasa a `cancelled` por cancelación manual o de la clienta, sin reembolso | `Tu turno en GlowUp fue cancelado` | Código, servicio, fecha/hora, motivo, política aplicada, botón “Reservar otro horario” y contacto | `booking_cancelled:{order_id}` |
| `booking_refunded` | Turno cancelado con reembolso | Orden pasa a `refunded` y turno a `cancelled` | `Tu turno fue cancelado y el pago reembolsado` | Código, servicio, fecha/hora, importe reembolsado, medio de pago, plazo estimado, contacto | `booking_refunded:{order_id}` |
| `booking_expired` | Reserva vencida sin pago | El cron marca el hold como `expired` y cancela la orden, solo si existía una orden creada | `Tu reserva {code} venció` | Código, servicio, fecha/hora liberada, botón “Reservar otro horario”, contacto | `booking_expired:{order_id}` |
| `booking_rescheduled` | Turno reprogramado | Acción de reprogramación (se activa cuando exista el flujo de la clienta o del panel) | `Tu turno cambió de fecha: {service}` | Código, fecha/hora anterior, fecha/hora nueva, profesional, políticas, botón “Ver mi turno” | `booking_rescheduled:{order_id}:{start_hash}` |
| `reminder_24h` | Recordatorio 24 h antes | Cron de recordatorios | `Recordatorio: tu turno es mañana a las {time}` | Código, servicio, profesional, fecha/hora, dirección, políticas, contacto | `reminder_24h:{start_hash}` |
| `reminder_2h` | Recordatorio 2 h antes | Cron de recordatorios | `Recordatorio: tu turno es en 2 horas` | Código, servicio, profesional, hora, dirección, contacto | `reminder_2h:{start_hash}` |

Notas:

- `booking_created` cubre el estado “pendiente de pago”, incluido Rapipago/Pago Fácil. **No se envía un email separado de “pago pendiente” en el MVP**: el mensaje de reserva ya incluye el botón de pago y el aviso de vencimiento. Si en las pruebas se detecta que Mercado Pago no muestra las instrucciones del cupón, se evalúa agregar `booking_payment_pending` sin duplicar el de creación.
- `booking_cancelled` y `booking_refunded` comparten la misma plantilla base con un bloque de reembolso condicional.
- `booking_rescheduled` queda definido, pero no se dispara hasta que exista la acción de reprogramación. No se implementa un email para un evento que todavía no existe.
- `booking_expired` solo se envía si la clienta llegó a generar una orden. Si abandonó antes de crear la orden, no recibe nada.

---

## 4. Matriz de emails internos

| Clave | Evento | Destinatario | Asunto | Contenido mínimo | Idempotencia |
| --- | --- | --- | --- | --- | --- |
| `admin_new_booking` | Nueva reserva con orden creada | `notification_email` | `Nueva reserva: {service} el {date} a las {time}` | Nombre, email y teléfono de la clienta, código, servicio, profesional, fecha/hora, total, estado del pago, enlace al pedido | `admin_new_booking:{order_id}` |
| `admin_payment_review` | Pago tardío que no se pudo confirmar | `notification_email` | `Revisar pago: pedido #{order} - {code}` | Pedido, código, importe, ID de pago, estado del turno, motivo, acción sugerida (reembolsar o reprogramar) | `admin_payment_review:{order_id}` |
| `admin_cancelled` | Cancelación o reembolso | `notification_email` | `Turno cancelado: {code}` | Código, servicio, fecha/hora, motivo, estado del reembolso, enlace al pedido | `admin_cancelled:{order_id}` |

No se envía un email interno por cada recordatorio. Los recordatorios son solo para la clienta.

---

## 5. Contenido y variables

### Variables disponibles

| Variable | Origen |
| --- | --- |
| `{client_first_name}` | `first_name` de la cuenta; si está vacío, “Hola” |
| `{client_full_name}` | `display_name` de la cuenta |
| `{code}` | `appointments.code` (`GLW-...`) |
| `{service}` | `Services_Repository::get()` |
| `{professional}` | `Professionals_Repository::get()` |
| `{date}` | Fecha local en español, por ejemplo “lunes 1 de diciembre de 2026” |
| `{time}` | Hora local `H:i` |
| `{duration}` | Duración del servicio en minutos |
| `{price}` | Precio formateado con `wc_price()` |
| `{order_number}` | Número de pedido de WooCommerce |
| `{payment_url}` | URL de `order-pay` de la orden |
| `{account_url}` | URL de `/mi-cuenta/` |
| `{booking_url}` | URL de `/turnos/` |
| `{address}` | Dirección del salón, desde ajustes o contenido |
| `{contact_email}` | `notification_email` |
| `{site_url}` | `home_url()` |

### Estructura de la plantilla

1. Encabezado con el wordmark “GlowUp” y la bajada “Estética integral · La Rioja”.
2. Tarjeta principal con saludo, estado del turno y tabla de datos.
3. Botón de acción principal según el evento.
4. Bloque de políticas: cancelación con reembolso hasta 24 h antes, reprogramación hasta 12 h antes, contacto.
5. Pie con dirección, email, teléfono, sitio y aviso de que es un mensaje automático.

Lineamientos:

- Ancho máximo de 600 px, una columna, CSS inline.
- Paleta: `#8E1B3A` (cereza), `#2A0E1A` (tinta), `#FFF7F1` (porcelana), `#F6D86B` (manteca) para acentos.
- Tipografía de sistema con fallback: `Georgia, 'Times New Roman', serif` para títulos y `Arial, Helvetica, sans-serif` para el cuerpo.
- Sin imágenes externas obligatorias; si se usa el logo, que sea un PNG servido desde el sitio con texto alternativo.
- Versión de texto plano equivalente, generada a partir de los mismos datos.
- Escapado con `esc_html()`, `esc_url()` y `wp_kses_post()`; los datos de la clienta nunca se interpolan sin escapar.
- Los enlaces deben usar `home_url()` y funcionar tanto en `localhost` como en ngrok.

---

## 6. Recordatorios

### Cron

- Hook: `glowup_turnos_send_reminders`.
- Frecuencia: cada 15 minutos, con un intervalo propio `glowup_fifteen_minutes`.
- Se programa en `Activator::ensure_cron()` junto con `glowup_turnos_cleanup_holds` y se limpia en la desactivación.
- Se puede ejecutar a mano con `wp cron event run glowup_turnos_send_reminders`.

### Ventanas

| Recordatorio | Condición | Envío |
| --- | --- | --- |
| 24 h | `start_at > now + 2 h` y `start_at <= now + 24 h` | Una vez, si `email_reminders` y `reminder_24h` están activos |
| 2 h | `start_at > now` y `start_at <= now + 2 h` | Una vez, si `email_reminders` y `reminder_2h` están activos |

La ventana es tolerante a caídas del cron: si el recordatorio de 24 h no se envió y el turno todavía está a más de 2 h, se manda tarde en lugar de perderse. Si el turno ya está a menos de 2 h, el de 24 h se omite y se envía el de 2 h.

### Condiciones de omisión

- Turno en estado distinto de `confirmed`.
- Turno cancelado, vencido, completado o ausente.
- Turno en el pasado.
- `email_reminders = 0`.
- Recordatorio específico desactivado (`reminder_24h = 0` o `reminder_2h = 0`).
- Email ya registrado como enviado para esa fecha/hora.

### Reprogramación

- Al reprogramar, se borran o invalidan los registros de `reminder_24h` y `reminder_2h` del turno, y la clave incluye el hash de la nueva fecha/hora (`start_hash`).
- El recordatorio de la fecha vieja no se envía.
- Si la clienta reprograma dentro de las 24 h, el recordatorio de 24 h se envía en la próxima corrida si todavía está a más de 2 h; si no, se envía el de 2 h.

### Fallos

- Si `wp_mail()` devuelve `false` o dispara `wp_mail_failed`, el registro queda en estado `failed` con el error y se reintenta en la próxima corrida.
- Después de 3 intentos fallidos, se marca `failed` definitivo y se envía una alerta interna a `notification_email`.
- Los fallos no bloquean el resto de los recordatorios.

---

## 7. Emails de WooCommerce

Para las órdenes de reserva (`Booking_Orders::is_booking_order()`) se suprimen los emails genéricos de WooCommerce y se reemplazan por los del plugin. La supresión se implementa con el filtro `woocommerce_email_enabled_{$email_id}` y el objeto de la orden.

| Email de WooCommerce | ¿Se envía en órdenes de reserva? | Motivo |
| --- | --- | --- |
| `WC_Email_New_Order` | No | Lo reemplaza `admin_new_booking` |
| `WC_Email_Cancelled_Order` | No | Lo reemplaza `admin_cancelled` |
| `WC_Email_Failed_Order` | No | Lo reemplaza `admin_payment_review` / `booking_payment_failed` |
| `WC_Email_Customer_Processing_Order` | No | Lo reemplaza `booking_confirmed` |
| `WC_Email_Customer_Completed_Order` | No | El turno se completa en la agenda, no en WooCommerce |
| `WC_Email_Customer_On_Hold_Order` | No | No se usa `on-hold` en reservas |
| `WC_Email_Customer_Failed_Order` | No | Lo reemplaza `booking_payment_failed` |
| `WC_Email_Customer_Cancelled_Order` | No | Lo reemplaza `booking_cancelled` |
| `WC_Email_Customer_Refunded_Order` | No | Lo reemplaza `booking_refunded` |
| `WC_Email_Customer_Invoice` | No | La factura fiscal queda para post-MVP |
| `WC_Email_Customer_Note` | Sí | Las notas manuales del panel siguen siendo útiles |
| Emails de cuenta, contraseña y usuario | Sí | No son emails de pedido |

Los emails de WooCommerce para pedidos que no son reservas (tienda futura) no se tocan.

---

## 8. Ajustes existentes

| Ajuste | Uso previsto en una implementación futura |
| --- | --- |
| `notification_email` | Copia interna y `Reply-To`; por defecto `hola@glowup.local` |
| `email_reminders` | Interruptor maestro de recordatorios |
| `reminder_24h` | Activa el recordatorio de 24 h |
| `reminder_2h` | Activa el recordatorio de 2 h |
| `timezone` | Zona de todas las fechas y ventanas de recordatorio |

No se agregan ajustes nuevos en el MVP. El remitente se controla por filtros y, si hace falta, por constantes de WP Mail SMTP.

---

## 9. Registro e idempotencia

Se propone una tabla propia, `wp_glowup_email_log`, con esquema `1.2.0`:

| Columna | Tipo | Uso |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED AUTO_INCREMENT` | Clave primaria |
| `appointment_id` | `BIGINT UNSIGNED` | Turno asociado |
| `email_key` | `VARCHAR(64)` | Clave de la matriz, con orden o hash cuando corresponda |
| `recipient` | `VARCHAR(191)` | Destinatario real |
| `status` | `VARCHAR(20)` | `sent`, `failed`, `skipped` |
| `attempts` | `TINYINT UNSIGNED` | Reintentos |
| `error` | `TEXT NULL` | Último error |
| `created_at` | `DATETIME` | Alta |
| `sent_at` | `DATETIME NULL` | Envío exitoso |

- Índice único `(appointment_id, email_key)`.
- Antes de enviar: buscar el registro; si `status = sent`, omitir.
- Si `status = failed` y `attempts < 3`, reintentar y actualizar.
- Si el envío falla, no marcar como `sent`.
- La tabla se limpia junto con el turno o se conserva para auditoría; se define retención de 90 días en una tarea futura.

---

## 10. Plan de pruebas de una implementación futura

1. **Transporte:** `wp_mail()` y un email de WooCommerce llegan a Mailpit con remitente y `Reply-To` correctos.
2. **Reserva creada:** crear un turno y una orden; verificar asunto, datos, botón de pago, vencimiento y versión de texto plano.
3. **Pago aprobado:** confirmar el turno; verificar `booking_confirmed` y que no se duplique si el webhook se repite.
4. **Pago rechazado:** verificar `booking_payment_failed`, que el turno siga `pending` y que el botón de reintento solo aparezca si la retención sigue vigente.
5. **Pago pendiente (Rapipago):** verificar que no se envíe un email duplicado y que el mensaje de reserva creada tenga las instrucciones necesarias.
6. **Reembolso:** verificar `booking_refunded`, importe y plazo; comprobar que no se envíe `booking_cancelled` además.
7. **Cancelación sin reembolso:** verificar `booking_cancelled`.
8. **Reserva vencida:** dejar vencer un hold con orden; verificar `booking_expired` y que no se envíe si no había orden.
9. **Recordatorio 24 h:** turno confirmado a 24 h; ejecutar el cron; verificar envío y que una segunda corrida no duplique.
10. **Recordatorio 2 h:** turno confirmado a 2 h; verificar envío y orden de los recordatorios.
11. **Recordatorio caído:** simular un cron detenido; verificar que el recordatorio de 24 h se envíe tarde si el turno sigue a más de 2 h.
12. **Reprogramación:** reprogramar un turno; verificar que no se envíe el recordatorio viejo y que se envíe el nuevo.
13. **Ajustes apagados:** desactivar `email_reminders`, `reminder_24h` y `reminder_2h`; verificar que no se envíe nada.
14. **Cancelado/vencido:** verificar que no reciban recordatorios.
15. **Fallo de transporte:** detener Mailpit; verificar registro `failed`, reintento y alerta interna tras 3 intentos.
16. **Órdenes que no son reservas:** verificar que los emails de WooCommerce sigan funcionando para la tienda futura.
17. **Escapado:** probar nombres con acentos, apóstrofes y caracteres especiales; verificar HTML y texto plano.
18. **Zona horaria:** crear un turno a las 10:00 y verificar que el recordatorio muestre 10:00, no otra hora.

---

## 11. Fuera de alcance

- Envío real a clientas y configuración de SPF/DKIM: producción, fuera de alcance.
- WhatsApp y SMS: fuera de alcance por decisión del proyecto.
- Facturación fiscal adjunta: post-MVP.
- Editor visual de plantillas o personalización por evento desde el panel: post-MVP.
- Email marketing, cumpleaños y promociones: fuera de alcance.
- Adjuntos y calendarios `.ics`: se evalúan después del MVP.

---

## 12. Criterios de aceptación

### Demo visual (Fase 5, cumplida)

- Las cuatro tarjetas están visibles en `/turnos/#emails`.
- Cada tarjeta abre una vista previa modal con datos de ejemplo.
- El copy aclara que el envío no está activo.
- No se envía correo desde el tema: no hay `wp_mail`, cron ni SMTP.

### Implementación real (fuera de alcance)

Si se retoma el envío real, se deben cumplir estos criterios:

- Todos los emails de la matriz se envían una sola vez por evento.
- Los recordatorios de 24 h y 2 h llegan a Mailpit con la fecha/hora correcta y sin duplicados.
- Los emails de reserva usan las plantillas del plugin y no los genéricos de WooCommerce.
- Un fallo de correo no rompe el flujo de reserva ni el pago.
- Los ajustes de recordatorios se respetan.
- La documentación de `PLAN.md` y `ARQUITECTURA.md` queda sincronizada al cierre.
