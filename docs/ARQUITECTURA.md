# GlowUp — Arquitectura

**Versión:** 0.5.0
**Actualizado:** 13 de noviembre de 2026
**Ámbito:** WordPress local `http://localhost/esteticaweb`

---

## 1. Vista general

```text
┌─────────────────────────────────────────────────────────────────────┐
│                         WordPress 7.1.4                             │
│                                                                     │
│  ┌────────────────────────┐        ┌─────────────────────────────┐  │
│  │ Tema temaestetica      │        │ Plugin glowup-turnos        │  │
│  │ (marca GlowUp)         │        │ (agenda y reglas de negocio)│  │
│  │                        │        │                             │  │
│  │ · Plantillas           │  lee   │ · Servicios                 │  │
│  │ · Design system        │◄───────│ · Profesionales             │  │
│  │ · theme.json           │        │ · Horarios / excepciones    │  │
│  │ · Integración Woo      │        │ · Turnos / retenciones      │  │
│  └───────────┬────────────┘        └──────────────┬──────────────┘  │
│              │                                    │                 │
│              │            ┌───────────────────────▼──────────────┐  │
│              │            │ WooCommerce 11.3.0                   │  │
│              └───────────►│ Cuentas · Pedidos · Pagos            │  │
│                           │ Tienda pública pausada (Fase 8)      │  │
│                           └───────────────────┬──────────────────┘  │
│                                               │                     │
│                           ┌───────────────────▼──────────────────┐  │
│                           │ Mercado Pago 8.10.0 (inactivo)       │  │
│                           │ Checkout · Webhooks · Reembolsos     │  │
│                           └──────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

El sitio se apoya en tres capas:

- **Presentación:** tema `temaestetica`, que muestra la marca GlowUp y consume datos de servicios y profesionales cuando el plugin está activo.
- **Negocio:** plugin `glowup-turnos`, dueño de la agenda, la disponibilidad, las políticas y la persistencia de turnos.
- **Comercio:** WooCommerce para cuentas, órdenes, pagos y checkout de reservas; Mercado Pago como pasarela prevista. La tienda pública de productos está pausada durante el MVP y se abre en la Fase 8.

La separación permite cambiar el tema sin perder reservas, y desactivar la agenda sin romper las cuentas ni los pedidos. El tema degrada a contenido de ejemplo si el plugin no está presente. La pausa de la tienda es solo de la vidriera pública de productos: las cuentas y los pedidos siguen operativos. La pantalla `order-pay` de una orden de reserva ya se exceptúa de la pausa y el núcleo de pagos de la Fase 4 está implementado; el cobro real se habilita cuando Mercado Pago tenga credenciales.

---

## 2. Stack técnico

| Componente | Versión / decisión |
| --- | --- |
| WordPress | 7.1.4 |
| PHP | 8.2.12 |
| Base de datos | MariaDB 10.4.32, puerto 3307 |
| Servidor local | XAMPP en Windows |
| WooCommerce | 11.3.0 |
| Mercado Pago | Plugin oficial 8.10.0, inactivo |
| Tema | `temaestetica` 0.2.0, marca GlowUp |
| Plugin de reservas | `glowup-turnos` 0.4.0, DB 1.1.0 |
| Prefijo de tablas | `wp_` |
| Moneda | ARS |
| Zona horaria | America/Argentina/La_Rioja (La Rioja capital, UTC-3) |
| Checkout de productos | Bloques de WooCommerce |
| Checkout de reservas | `order-pay` clásico de WooCommerce; pago real solo si hay una pasarela disponible |
| Wizard | JavaScript propio, sin bundler ni build |
| Email | `wp_mail` y cron de WordPress |

---

## 3. Tema `temaestetica`

### 3.1 Estructura

```text
temaestetica/
├── assets/
│   ├── css/main.css          Sistema de diseño y responsive
│   ├── css/editor.css        Estilos del editor de bloques
│   ├── img/                  Placeholders SVG y favicon
│   ├── js/main.js            Navegación, acordeón, reveal y contador del carrito
│   └── js/booking.js         Wizard: estado, REST, retención y orden
├── inc/
│   ├── setup.php             Supports, menús, tamaños de imagen
│   ├── assets.php            Encolado, fuentes y datos JS
│   ├── template-tags.php     Helpers de imágenes, iconos y URLs
│   ├── content.php           Contenido de respaldo y datos del plugin
│   ├── customizer.php        Opciones de imágenes de marca
│   ├── booking.php           Login, registro, avisos y helpers de reserva
│   └── woocommerce.php       Integración con la tienda
├── template-parts/           Secciones de la home y tarjetas
├── page-servicios.php        Servicios agrupados
├── page-nosotras.php         Equipo
├── page-contacto.php         Contacto y FAQ
├── page-turnos.php           Wizard público, cuenta, resumen y políticas
├── coming-soon.php           Pantalla de tienda “Próximamente”
├── front-page.php            Inicio
├── header.php / footer.php
├── woocommerce.php           Wrapper de WooCommerce
├── theme.json                Paleta, tipografía, espaciado y estilos
└── style.css                 Cabecera obligatoria del tema
```

### 3.2 Sistema de diseño

- **Tipografía:** Instrument Serif para display y Bricolage Grotesque para texto.
- **Paleta:** ciruela tinta `#2A0E1A`, cereza `#8E1B3A`, cereza profunda `#611025`, chicle `#FF6F91`, manteca `#F6D86B`, porcelana `#FFF7F1`, papel `#FFFCF8`, lila `#D9C7F0`, salvia `#8B9A7D`, malva `#6F5A61`.
- **Layout:** contenedor de 1240 px, contenido de 760 px, radios amplios, bordes finos y sombras suaves.
- **Componentes:** botones, kickers, títulos display, tarjetas de servicio, tarjetas de profesional, grilla de tienda, pasos de proceso, testimonios, FAQ y formularios.
- **Imágenes:** SVG reemplazables para hero, retrato, servicio, producto y banner ancho. El customizer permite apuntar cada slot a una imagen de la biblioteca o a una URL.
- **Accesibilidad:** foco visible, navegación por teclado en el menú móvil, `prefers-reduced-motion`, textos alternativos y contraste cuidado.

### 3.3 Integración con el plugin

`inc/content.php` comprueba la existencia de las clases del plugin:

- `GlowUp\Turnos\Repositories\Services_Repository`
- `GlowUp\Turnos\Repositories\Professionals_Repository`

Si existen, consume servicios activos y profesionales activos; si no, usa el contenido de respaldo. Esto mantiene la home y las páginas públicas funcionando aunque el plugin se desactive.

### 3.4 Integración con WooCommerce

- `woocommerce.php` como wrapper de plantillas.
- Soporte de galería, zoom, lightbox y slider.
- Placeholder de producto propio.
- Badge del carrito con fragmento AJAX clásico y sincronización con el Store API para el carrito por bloques.
- Ajustes de encabezado de tienda, categorías y ficha de producto.
- Estilos de carrito y checkout por bloques integrados con la paleta.

### 3.5 Wizard de reservas

La Fase 3 reemplazó el estado “próximamente” de `/turnos/` por el flujo público:

- `page-turnos.php` detecta si el plugin está activo y si hay una sesión iniciada.
- Sin sesión, muestra dos formularios inline: ingreso y creación de cuenta. Se procesan por `admin-post.php` en `inc/booking.php`, con nonce, validación y alta de la clienta como usuario `customer` de WooCommerce.
- Con sesión y capacidad `glowup_book_appointments`, muestra el wizard. Si la cuenta no puede reservar, explica el motivo y ofrece contacto.
- `assets/js/booking.js` maneja el estado de los pasos 1 a 4 (servicio, profesional, fecha y hora, confirmación), la retención y el resumen final.
- Los servicios y profesionales se consultan a `GET /glowup/v1/services` y `GET /glowup/v1/professionals`; la disponibilidad a `GET /glowup/v1/availability`.
- La creación del hold usa `POST /glowup/v1/holds` con una clave de idempotencia por intento. El contador muestra el tiempo restante de la retención.
- La confirmación usa `POST /glowup/v1/orders`, que crea la orden de WooCommerce y la vincula al turno. La pantalla final muestra código, servicio, profesional, fecha, hora, duración, total y número de pedido.
- Si hay una pasarela de Mercado Pago disponible, el wizard cambia el CTA a “Continuar al pago”, muestra el botón “Pagar ahora” con la URL de `order-pay` y avisa que el horario se libera si el pago no se acredita. Sin pasarela, mantiene el texto de “próxima etapa” y no ofrece cobro.
- Los consentimientos de términos, cancelación y datos de salud se registran antes del hold y se guardan en el turno y en la orden.
- Los errores de horario ocupado y retención vencida se muestran con una acción para elegir otro horario; la sesión vencida ofrece volver a ingresar; el doble envío reutiliza la orden existente.
- `glowupBooking` localiza la URL REST, el nonce, la zona horaria, la fecha mínima/máxima, la duración del hold, la disponibilidad de pagos y los textos de la interfaz.

### 3.6 Pausa de la tienda pública

La tienda pública es la Fase 8. Mientras el MVP no la abra, `inc/woocommerce.php` aplica una pausa de vidriera:

- `glowup_store_is_coming_soon()` devuelve `true` por defecto y se puede filtrar con `glowup_store_coming_soon`.
- `glowup_store_is_public_page()` identifica tienda, ficha de producto, taxonomías de producto, carrito y checkout.
- `glowup_store_coming_soon_template()` engancha en `template_include` (prioridad 99) y reemplaza esas pantallas por `coming-soon.php`.
- Las cuentas con `manage_woocommerce` (administradoras y encargadas de tienda) siguen viendo la tienda real para gestionar productos y probar el checkout.
- El encabezado oculta el icono del carrito a las visitantes y la sección de tienda de la home se reemplaza por un aviso de “Próximamente”.
- `/mi-cuenta/` no se interviene: las cuentas siguen disponibles para reservar y consultar turnos.
- La pantalla `order-pay` de una orden de reserva se excluye de `glowup_store_is_public_page()`. Sin pasarela configurada muestra un aviso, reemplaza el botón de pago por un enlace a Mi cuenta y oculta el checkbox de términos que ya se aceptó en el wizard. Con una pasarela disponible muestra el tiempo restante de la retención y deja el botón real de pago.
- Si la retención venció, `before_woocommerce_pay` redirige a `/turnos/?glowup_pay=expired` para mostrar un aviso claro en lugar del error genérico de WooCommerce. La guarda del plugin sigue bloqueando el pago del lado del servidor.
- El cobro real de esa pantalla se habilita cuando Mercado Pago esté configurado; el núcleo de Fase 4 ya está implementado y probado sin credenciales.

---

## 4. Plugin `glowup-turnos`

### 4.1 Estructura

```text
glowup-turnos/
├── glowup-turnos.php              Entrada, constantes y hooks
├── uninstall.php                  Limpieza opt-in
├── includes/
│   ├── class-db.php               Tablas y versión de esquema
│   ├── class-settings.php         Opción única de ajustes
│   ├── class-time.php             Zona horaria y fechas locales
│   ├── class-roles.php            Rol profesional y capacidades
│   ├── class-activator.php        Instalación, seeds y upgrades
│   ├── class-availability.php     Motor de disponibilidad
│   ├── class-rest-api.php         Endpoints públicos y de reserva
│   ├── class-booking-orders.php   Creación y vínculo de órdenes de reserva
│   ├── class-booking-payments.php Pago, confirmación, cancelación y conciliación
│   ├── class-plugin.php           Bootstrap y cron
│   └── repositories/
│       ├── class-services-repository.php
│       ├── class-professionals-repository.php
│       ├── class-schedules-repository.php
│       └── class-appointments-repository.php
├── admin/class-admin.php          Panel, agenda, listados y ajustes
├── assets/css/admin.css           Estilos del panel
├── public/                        Reservado para assets públicos
├── templates/                     Reservado (el wizard vive en el tema)
└── languages/                     Traducciones
```

### 4.2 Ciclo de vida

1. `register_activation_hook` ejecuta `Activator::activate()`.
2. Se crean las tablas con `dbDelta()`.
3. Se crea el rol `glowup_professional` y se otorgan capacidades.
4. Se guardan los ajustes por defecto y se siembran servicios, profesionales, horarios y relaciones.
5. Se programa el cron horario `glowup_turnos_cleanup_holds`.
6. En cada carga, `Activator::maybe_upgrade()` compara `glowup_turnos_version` y reinstala de forma idempotente si cambió.
7. Al desinstalar, los datos se conservan salvo que `GLOWUP_TURNOS_REMOVE_DATA` sea verdadero.

---

## 5. Modelo de datos

Todas las tablas usan el prefijo `wp_glowup_` y `utf8mb4`.

### 5.1 `wp_glowup_services`

| Campo | Tipo | Uso |
| --- | --- | --- |
| `id` | bigint unsigned | PK |
| `name` | varchar(190) | Nombre visible |
| `slug` | varchar(190) | URL / identificador único |
| `description` | text | Descripción pública |
| `category` | varchar(50) | `unas`, `cabello`, `facial`, `depilacion` (definitiva por zona y pack), `otros` |
| `duration_min` | smallint | Duración en minutos |
| `price` | decimal(12,2) | Precio final |
| `deposit_type` | varchar(20) | `percent` / `fixed`; preparado para señas |
| `deposit_value` | decimal(12,2) | Valor de seña; hoy 100% |
| `requires_evaluation` | tinyint(1) | Requiere evaluación previa |
| `active` | tinyint(1) | Visible o no |
| `display_order` | smallint | Orden |
| `created_at`, `updated_at` | datetime | Auditoría |

Índices: `slug` único, `category_active`, `display_order`.

### 5.2 `wp_glowup_professionals`

| Campo | Tipo | Uso |
| --- | --- | --- |
| `id` | bigint unsigned | PK |
| `user_id` | bigint unsigned | Usuario WP vinculado (opcional) |
| `name` | varchar(190) | Nombre |
| `slug` | varchar(190) | Identificador |
| `role_label` | varchar(190) | Especialidad visible |
| `bio` | text | Biografía |
| `photo_url` | varchar(500) | Foto |
| `instagram` | varchar(190) | Red social |
| `active` | tinyint(1) | Activa o no |
| `display_order` | smallint | Orden |
| `created_at`, `updated_at` | datetime | Auditoría |

### 5.3 `wp_glowup_service_professionals`

Pivote muchos-a-muchos entre servicios y profesionales. Clave primaria compuesta (`service_id`, `professional_id`) e índice por profesional. Es imprescindible para saber quién puede realizar cada servicio.

### 5.4 `wp_glowup_schedules`

| Campo | Tipo | Uso |
| --- | --- | --- |
| `id` | bigint unsigned | PK |
| `professional_id` | bigint unsigned | Profesional |
| `weekday` | tinyint | 1 = lunes … 7 = domingo |
| `start_time`, `end_time` | time | Bloque de atención |
| `break_start`, `break_end` | time null | Pausa |
| `active` | tinyint(1) | Vigente |

Horario base sembrado: lunes a viernes 09:00–20:00 con pausa 13:00–14:00; sábados 09:00–14:00; domingo cerrado.

### 5.5 `wp_glowup_time_off`

Excepciones puntuales: vacaciones, feriados, ausencias o bloqueos. Rango `start_at`–`end_at` por profesional, con motivo. Índice `professional_range`.

### 5.6 `wp_glowup_appointments`

| Campo | Tipo | Uso |
| --- | --- | --- |
| `id` | bigint unsigned | PK |
| `code` | varchar(32) | Código humano único `GLW-...` |
| `client_id` | bigint unsigned | Usuario WP |
| `service_id` | bigint unsigned | Servicio |
| `professional_id` | bigint unsigned | Profesional |
| `start_at`, `end_at` | datetime | Rango del turno |
| `status` | varchar(20) | `pending`, `confirmed`, `cancelled`, `completed`, `no_show`, `expired` |
| `price` | decimal(12,2) | Precio al reservar |
| `deposit_amount` | decimal(12,2) | Monto a cobrar (100% en el MVP) |
| `order_id` | bigint unsigned null | Pedido WooCommerce asociado en la Fase 3 |
| `hold_expires_at` | datetime null | Vencimiento de la retención |
| `idempotency_key` | varchar(64) null | Clave única de reintento de reserva |
| `notes` | text | Notas internas |
| `health_consent` | tinyint(1) | Consentimiento de datos de salud |
| `created_at`, `updated_at` | datetime | Auditoría |
| `cancelled_at` | datetime null | Fecha de cancelación |
| `cancel_reason` | varchar(190) | Motivo |

Índices: `code` único, `idempotency_key` único, `professional_start`, `professional_status_start`, `client_start`, `status_start`, `hold_expires`, `order_id`.

### 5.7 Relaciones

```text
services 1 ─── n service_professionals n ─── 1 professionals
professionals 1 ─── n schedules
professionals 1 ─── n time_off
services 1 ─── n appointments n ─── 1 professionals
users (WP) 1 ─── n appointments
orders (Woo) 1 ─── 0..1 appointments
```

### 5.8 Orden de reserva en WooCommerce

La orden se crea desde `Booking_Orders` y guarda una copia de los datos del turno para soporte y conciliación:

- `_glowup_booking_order` = `yes`: marca la orden como reserva.
- `_glowup_appointment_id` y `_glowup_appointment_code`: vínculo con el turno.
- `_glowup_service_id`, `_glowup_professional_id`, `_glowup_booking_start` y `_glowup_hold_expires_at`.
- `_glowup_health_consent` y `_glowup_terms_consent`: consentimientos registrados.
- `_glowup_payment_review` = `yes`: pago tardío que no pudo confirmarse y requiere revisión manual y reembolso.
- El ítem de línea es un producto virtual sin `product_id`, con nombre del servicio, subtotal, total y metadatos de turno, fecha, hora, profesional y duración.

El turno es la fuente de verdad del servicio, la profesional y el precio congelado al reservar. La orden es la fuente de verdad del pedido y del pago.

---

## 6. Flujo de reserva

### 6.1 Flujo implementado (Fases 3 y 4)

1. La clienta entra a `/turnos/` e inicia sesión o crea su cuenta.
2. Elige servicio. El sistema filtra las profesionales habilitadas en el pivote.
3. Elige profesional, fecha y horario disponible.
4. Revisa el resumen y acepta los términos, la política de cancelación y el consentimiento de datos de salud.
5. El sistema crea un turno `pending` con `hold_expires_at = ahora + 15 min`.
6. El sistema crea una orden WooCommerce `pending` y la asocia al turno por `order_id`; guarda los metadatos del turno en la orden.
7. La pantalla final muestra el código de reserva, el detalle y el número de pedido. Si hay una pasarela de Mercado Pago disponible, ofrece “Pagar ahora”; si no, avisa que el pago online se habilita en la próxima etapa y no se realiza ningún cobro.

### 6.2 Continuación implementada (Fase 4)

8. La clienta paga el total con Mercado Pago desde el `order-pay` de la reserva.
9. Al acreditarse el pago, `Booking_Payments` confirma el turno, limpia la retención y registra una nota en la orden. La orden pasa a `processing` o `completed` según WooCommerce.
10. Si la orden se cancela o se reembolsa, el turno pasa a `cancelled`.
11. Si el pago no se acredita antes del vencimiento, el cron marca el turno como `expired` y cancela la orden `pending` o `failed`.
12. Si el pago llega después del vencimiento, se confirma solo cuando el horario sigue libre y el turno es futuro; en caso contrario, el turno queda `expired` o en su estado previo y la orden se marca con `_glowup_payment_review=yes` para revisión manual.
13. Los emails de confirmación siguen pendientes para la Fase 5.

### 6.3 Máquina de estados

```text
                 pago acreditado
pending ─────────────────────────────► confirmed
   │                                      │
   │ hold vencido / pago rechazado        │ turno cumplido
   ├──────────────► expired               ├──────────────► completed
   │                                      │
   │ cancelación                          │ ausencia
   └──────────────► cancelled ◄───────────┤
                                          └──────────────► no_show

confirmed ── cancelación con reembolso ──► cancelled
```

Reglas:

- `pending` retiene el horario hasta `hold_expires_at`.
- Solo `pending` y `confirmed` bloquean disponibilidad.
- `cancelled`, `completed`, `no_show` y `expired` liberan el horario.
- Crear la orden no confirma el turno: la orden queda `pending` y el turno sigue `pending`.
- Si el hold vence, el turno pasa a `expired` y libera el horario. La conciliación cancela la orden `pending` o `failed` asociada; si el pago llega igual, se aplica la regla de pago tardío.
- Un pago acreditado no resucita turnos `cancelled`, `completed` o `no_show`: la orden queda marcada para revisión manual y reembolso.
- Cancelación con reembolso: al menos 24 h antes.
- Reprogramación: al menos 12 h antes.
- Fuera de plazo: se permite cancelar, pero sin reembolso, según la política vigente.

### 6.4 Retención y concurrencia

Una retención no puede depender solo de un índice único: dos rangos distintos pueden superponerse sin compartir la misma clave. La estrategia implementada es:

1. Abrir una transacción o tomar un lock de aplicación (`GET_LOCK`) por profesional.
2. Consultar solapamientos con `start_at < nuevo_end AND end_at > nuevo_start`.
3. Contar como bloqueantes los turnos `pending` vigentes y `confirmed`.
4. Insertar el turno solo si no hay conflicto.
5. Liberar el lock.

Además, cada intento de reserva lleva una clave de idempotencia para que reintentos de red o doble clic no creen dos turnos. La Fase 2 implementa esta lógica y la Fase 3 la conserva.

La creación de la orden tiene su propia protección: `Booking_Orders` toma `GET_LOCK` por turno, vuelve a leer el turno y reutiliza la orden existente si ya fue creada. Si el vínculo con el turno falla, elimina la orden recién creada para no dejar pedidos huérfanos.

### 6.5 Disponibilidad

Entradas del cálculo:

- Servicio y duración.
- Profesional habilitada.
- Fecha y zona horaria.
- Horario semanal y pausas.
- Excepciones (`time_off`).
- Turnos `pending` vigentes y `confirmed`.
- Anticipación mínima (120 min por defecto).
- Anticipación máxima (60 días por defecto).
- Intervalo de slots (15 min por defecto).

Salida: lista de horarios de inicio que permiten completar la duración del servicio antes del cierre, sin cruzar pausas, excepciones ni turnos existentes.

---

## 7. Pagos y WooCommerce

### 7.1 Estrategia

- WooCommerce es la fuente de verdad del pedido y del pago. La orden se crea al confirmar la retención, queda `pending` y solo se cobra cuando hay una pasarela disponible.
- El turno se vincula al pedido por `appointments.order_id` y meta del pedido (`_glowup_appointment_id`). `Booking_Orders::get_appointment_for_order()` resuelve el turno desde la orden y `Appointments_Repository::get_by_order_id()` lo busca por `order_id`.
- `POST /glowup/v1/orders` valida propiedad del turno, hold vigente, consentimiento de salud, servicio y profesional activos, y un importe mayor a cero.
- `Booking_Orders` crea un ítem de producto virtual, calcula totales, guarda los metadatos del turno y asocia la orden al turno. Si la orden ya existe, la reutiliza.
- `Booking_Payments` (plugin) implementa el mapeo orden→turno:
  - `woocommerce_order_payment_status_changed` y el fallback `woocommerce_order_status_changed` confirman el turno cuando la orden pasa a un estado pagado (`processing`/`completed`).
  - `woocommerce_order_status_cancelled` y `woocommerce_order_status_refunded` cancelan el turno.
  - `woocommerce_order_needs_payment` bloquea el pago si el turno no está `pending` o si el hold venció.
  - La conciliación del cron (`glowup_turnos_cleanup_holds`) marca los holds vencidos como `expired` y cancela sus órdenes `pending`/`failed`.
- Pago tardío: si el horario sigue libre y el turno es futuro, se confirma; si el horario ya está ocupado, el turno queda `expired` y la orden se marca con `_glowup_payment_review=yes`. Los estados `cancelled`, `completed` y `no_show` nunca se resucitan automáticamente: siempre van a revisión manual.
- La pantalla `order-pay` de una reserva se exceptúa de la pausa de la tienda. El tema muestra el aviso y el botón de pago según `glowup_booking_payments_available()`, que detecta pasarelas disponibles cuyo ID empieza con `woo-mercado-pago` y admite el filtro `glowup_booking_payments_available` para pruebas o extensiones.
- Si la retención venció, el tema redirige a `/turnos/?glowup_pay=expired` antes de renderizar el formulario de pago. El plugin vuelve a validar del lado del servidor.
- El cobro es total al reservar. La configuración `deposit` existe pero no se usa en el MVP.
- Mercado Pago se integra con el plugin oficial, que gestiona checkout, webhooks y reembolsos.
- Los reembolsos se inician desde WooCommerce y se reflejan en el estado del turno.

### 7.2 Consideraciones

- Los medios de pago no instantáneos (efectivo, transferencia) pueden tardar más que la retención de 15 minutos. Antes de habilitarlos hay que definir una ventana mayor o una confirmación manual.
- El webhook debe ser idempotente: un mismo evento no puede confirmar dos veces ni duplicar emails. La confirmación del turno es idempotente porque solo actúa sobre turnos `pending`.
- El estado del pedido y el estado del turno se mapean explícitamente: pagado → `confirmed`, cancelado/reembolsado → `cancelled`, hold vencido → `expired`.
- Una orden `pending` puede sobrevivir a su retención: el cron la cancela y el pago queda bloqueado. Si el pago llega después, se confirma solo con horario libre o se marca para revisión.
- Las credenciales de producción todavía no están disponibles. El plugin de Mercado Pago está instalado e inactivo; no se activa ni se cargan claves hasta que existan.
- Hasta entonces, `glowup_booking_payments_available()` devuelve `false`, el wizard no promete cobro y `order-pay` muestra el aviso de “próxima etapa”.
- No se deben commitear credenciales; se cargan en la configuración de WooCommerce o en constantes de `wp-config.php`.
- Verificación de la Fase 4: 17 comprobaciones de WP-CLI sobre pago normal, guarda, conciliación, pagos tardíos con y sin horario libre, cancelación y reembolso; pruebas HTTP de `/turnos/` y `order-pay` en ambos estados (sin pasarela y con pasarela simulada por filtro). Los datos de prueba se eliminaron al cierre.

### 7.3 Productos y tienda futura

- El catálogo interno tiene 18 productos simples en cinco categorías, con precios finales y IVA incluido.
- Stock gestionado en productos físicos; gift card virtual sin stock.
- Los productos no se exponen al público durante el MVP: tienda, fichas, carrito y checkout de productos muestran “Próximamente”.
- La tienda pública completa, con retiro en el salón y envío nacional, es la Fase 8 y queda fuera del MVP.
- El costo de envío definitivo y los textos legales de compra quedan pendientes para esa fase.
- WooCommerce sigue siendo la infraestructura de cuentas y órdenes; la pausa no desactiva el motor de cuentas. El `order-pay` de reservas está exceptuado de la pausa y el cobro se habilita cuando Mercado Pago tenga credenciales.

---

## 8. Roles y permisos

| Rol | Uso | Capacidades principales |
| --- | --- | --- |
| `administrator` | Agustina / administración | Todas las capacidades GlowUp y WooCommerce |
| `glowup_professional` | Estela / Agustina | Ver turnos, gestionar su agenda, subir archivos |
| `customer` | Clientas | Reservar y ver sus turnos; comprar cuando la tienda abra en la Fase 8 |

Capacidades propias:

- `glowup_manage_settings`
- `glowup_manage_services`
- `glowup_manage_professionals`
- `glowup_manage_appointments`
- `glowup_view_appointments`
- `glowup_manage_own_schedule`
- `glowup_book_appointments`

Principio: el rol profesional no debe poder cambiar precios, servicios ajenos ni configuraciones globales. La agenda propia se limita por `user_id` vinculado.

---

## 9. API REST

### 9.1 Endpoints actuales

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/wp-json/glowup/v1/services` | Servicios activos agrupados por categoría |
| GET | `/wp-json/glowup/v1/professionals` | Profesionales activas; acepta `service_id` |
| GET | `/wp-json/glowup/v1/config` | Configuración pública del wizard |
| GET | `/wp-json/glowup/v1/availability` | Slots disponibles por servicio, profesional y fecha |
| POST | `/wp-json/glowup/v1/holds` | Crear retención pendiente; requiere sesión con `glowup_book_appointments` e `idempotency_key` |
| POST | `/wp-json/glowup/v1/orders` | Crear la orden de la reserva desde un hold; requiere sesión y `appointment_id` |
| GET | `/wp-json/glowup/v1/appointments` | Turnos de la clienta autenticada |

La retención usa lock por profesional, transacción y validación de solapamientos. La clave de idempotencia debe tener entre 8 y 64 caracteres alfanuméricos, guiones o guiones bajos. Los holds vencidos se marcan como `expired` y dejan de bloquear la disponibilidad.

`POST /orders` recibe `appointment_id`, `terms_consent` y `health_consent` ya registrado en el turno. Responde 201 con la orden creada o 200 si reutiliza una existente. La orden incluye `order_id`, número, estado, total formateado, URL de pago, URL de detalle y segundos restantes de la retención. Los errores de sesión, propiedad, términos, consentimiento, hold vencido e importe inválido se devuelven con códigos y estados HTTP explícitos.

### 9.2 Errores de reserva

| Código | HTTP | Cuándo |
| --- | --- | --- |
| `rest_forbidden` | 401/403 | No hay sesión o la cuenta no tiene `glowup_book_appointments` |
| `glowup_auth_required` | 401 | Falta sesión en la creación de hold u orden |
| `glowup_slot_unavailable` | 409 | El horario se ocupó entre la consulta y el hold |
| `glowup_hold_expired` | 409 | La retención venció antes de crear la orden |
| `glowup_terms_required` | 400 | Falta el consentimiento de términos |
| `glowup_health_consent_required` | 400 | Falta el consentimiento de datos de salud |
| `glowup_appointment_not_found` | 404 | El turno no existe o no pertenece a la clienta |
| `glowup_appointment_not_pending` | 409 | El turno ya no está pendiente |
| `glowup_service_unavailable` | 409 | El servicio se desactivó después del hold |
| `glowup_professional_unavailable` | 409 | La profesional ya no realiza el servicio |
| `glowup_woocommerce_unavailable` | 503 | WooCommerce no está disponible |

### 9.3 Endpoints previstos

| Método | Ruta | Descripción |
| --- | --- | --- |
| POST | `/glowup/v1/appointments/{id}/cancel` | Cancelar con validación de plazo |
| POST | `/glowup/v1/appointments/{id}/reschedule` | Reprogramar con validación de plazo |

Las mutaciones deben usar nonce, verificar sesión y capacidad, validar propiedad del turno y responder con errores claros.

---

## 10. Emails y recordatorios

- Confirmación al acreditarse el pago.
- Aviso de cancelación y de reprogramación.
- Recordatorio 24 h antes.
- Recordatorio 2 h antes.
- Copia interna al email configurado en ajustes.
- Envío con `wp_mail`; en producción se recomienda SMTP transaccional con SPF y DKIM.
- Los recordatorios se disparan por cron y deben registrar si ya se enviaron para evitar duplicados.

WhatsApp está fuera del alcance por decisión del proyecto.

---

## 11. Seguridad y privacidad

- Consultas SQL con `$wpdb->prepare()` y casts explícitos.
- Escapado de salida con `esc_html`, `esc_attr`, `esc_url` y `wp_kses_post`.
- Nonces en formularios y mutaciones REST.
- Verificación de capacidades y de propiedad de los turnos.
- Validación de fechas, duraciones y estados contra listas permitidas.
- Idempotencia en pagos y reservas.
- Verificación de propiedad en la creación de la orden: el turno debe pertenecer a la clienta autenticada.
- Lock por turno y relectura antes de crear la orden para evitar duplicados.
- Rate limiting en endpoints públicos sensibles.
- Datos de salud: consentimiento explícito, acceso restringido, retención definida y exclusión de métricas públicas.
- Backups antes de migraciones o cambios de esquema.
- `WP_DEBUG` desactivado en local por ahora; en producción, logging a archivo y nunca mostrar errores al público.

---

## 12. Entornos y despliegue

### Local

- URL: `http://localhost/esteticaweb`
- WordPress: `C:\xampp\htdocs\esteticaweb`
- Tema: `wp-content/themes/temaestetica`
- Plugin: `wp-content/plugins/glowup-turnos`
- Base: MariaDB en `127.0.0.1:3307`
- Backups: `C:\xampp\backups\esteticaweb`

### Futuro

- staging con copia de base y contenido.
- Producción con HTTPS, caché, SMTP, backups automáticos y monitoreo.
- Variables sensibles en `wp-config.php` o entorno, nunca en el repositorio.
- Migración con plugin de migración o export/import controlado; nunca copiar la base local sin sanitizar.

---

## 13. Workflow de trabajo

1. **Backup** de base antes de cambios de esquema o datos masivos.
2. **Implementación** en tema o plugin según corresponda; no mezclar lógica de agenda en el tema.
3. **Verificación estática:** `php -l` en todos los archivos PHP.
4. **Verificación funcional:** WP-CLI para datos y navegador para render, consola, enlaces, formularios y responsive.
5. **Documentación:** actualizar `docs/PLAN.md`, `docs/ARQUITECTURA.md` y el Word cuando cambie el alcance.
6. **Commit** atómico con mensaje claro; el tema y el plugin se versionan por separado.
7. **Release:** bump de versión del tema/plugin y de `GLOWUP_TURNOS_DB_VERSION` si cambia el esquema.
8. **Despliegue:** staging → QA → producción, con backup y rollback preparados.

### Convención de commits

```text
feat(theme): ...
feat(booking): ...
fix(booking): ...
docs: ...
chore: ...
```

---

## 14. Deuda técnica y decisiones pendientes

- Wizard público, cuenta, creación de orden y núcleo de pagos: Fases 3 y 4 implementadas.
- Cancelación y reprogramación desde la cuenta de la clienta: pendiente antes del lanzamiento. La cancelación/reembolso desde WooCommerce ya se refleja en el turno.
- Activación de Mercado Pago sandbox/producción, credenciales, webhooks y pruebas end-to-end: Fase 4, bloqueado por credenciales.
- Emails reales y recordatorios: Fase 5.
- CRUD completo de servicios, profesionales, horarios y excepciones: Fase 6.
- Agenda operativa con vista diaria/semanal: Fase 6.
- Apertura de la tienda pública completa: Fase 8.
- Tests automatizados y CI: posterior al MVP.
- Estilos finos del checkout por bloques y del email: posterior.
- Costos de envío, logística y dirección exacta: negocio, para la Fase 8.
- Textos legales de compra, envío y devoluciones: Fase 8.
- Textos legales definitivos y facturación: negocio/legal.
- Fotos y logo definitivos: contenido.
- Los turnos vencidos se conservan como `expired` para auditoría; revisar la política de retención de datos antes de producción.
- Evaluar extensión de retención para medios de pago no instantáneos.
- El wizard depende de las clases del plugin; si el plugin se desactiva, `/turnos/` muestra un aviso y no permite reservar.

---

## 15. Glosario

- **Turno / appointment:** reserva de un servicio con una profesional en un rango horario.
- **Hold / retención:** bloqueo temporal del horario mientras se completa la reserva.
- **Orden de reserva:** pedido de WooCommerce creado desde el turno; queda pendiente hasta que se acredite el pago o se cancele.
- **Slot:** horario de inicio posible según intervalo y duración.
- **Pivote:** tabla que relaciona servicios con profesionales.
- **Seña:** pago parcial; no se usa en el MVP.
- **Gift card:** tarjeta de regalo simple por un monto.
- **Pack:** combinación de servicios; su automatización completa es post-MVP.
- **HPOS:** almacenamiento de pedidos de WooCommerce en tablas propias.
- **Idempotencia:** propiedad por la cual repetir una operación no duplica su efecto.
