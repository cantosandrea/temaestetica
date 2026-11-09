# GlowUp — Arquitectura

**Versión:** 0.1.0
**Actualizado:** 8 de noviembre de 2026
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
│              │            │ WooCommerce 11.2.2                   │  │
│              └───────────►│ Cuentas · Catálogo · Carrito         │  │
│                           │ Checkout · Pedidos · Reembolsos      │  │
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
- **Comercio:** WooCommerce para cuentas, productos, pedidos, checkout y pagos; Mercado Pago como pasarela prevista.

La separación permite cambiar el tema sin perder reservas, y desactivar la agenda sin romper la tienda. El tema degrada a contenido de ejemplo si el plugin no está presente.

---

## 2. Stack técnico

| Componente | Versión / decisión |
| --- | --- |
| WordPress | 7.1.4 |
| PHP | 8.2.12 |
| Base de datos | MariaDB 10.4.32, puerto 3307 |
| Servidor local | XAMPP en Windows |
| WooCommerce | 11.2.2 |
| Mercado Pago | Plugin oficial 8.10.0, inactivo |
| Tema | `temaestetica` 0.1.0, marca GlowUp |
| Plugin de reservas | `glowup-turnos` 0.1.0, DB 1.0.0 |
| Prefijo de tablas | `wp_` |
| Moneda | ARS |
| Zona horaria | America/Argentina/Buenos_Aires |
| Checkout | Bloques de WooCommerce |
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
│   └── js/main.js            Navegación, acordeón, reveal y contador del carrito
├── inc/
│   ├── setup.php             Supports, menús, tamaños de imagen
│   ├── assets.php            Encolado, fuentes y datos JS
│   ├── template-tags.php     Helpers de imágenes, iconos y URLs
│   ├── content.php           Contenido de respaldo y datos del plugin
│   ├── customizer.php        Opciones de imágenes de marca
│   └── woocommerce.php       Integración con la tienda
├── template-parts/           Secciones de la home y tarjetas
├── page-servicios.php        Servicios agrupados
├── page-nosotras.php         Equipo
├── page-contacto.php         Contacto y FAQ
├── page-turnos.php           Estado del wizard (próximamente)
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
│   ├── class-roles.php            Rol profesional y capacidades
│   ├── class-activator.php        Instalación, seeds y upgrades
│   ├── class-rest-api.php         Endpoints públicos de lectura
│   ├── class-plugin.php           Bootstrap y cron
│   └── repositories/
│       ├── class-services-repository.php
│       ├── class-professionals-repository.php
│       └── class-appointments-repository.php
├── admin/class-admin.php          Panel, listados y ajustes
├── assets/css/admin.css           Estilos del panel
├── public/                        Reservado para assets públicos
├── templates/                     Reservado para plantillas del wizard
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
| `category` | varchar(50) | `unas`, `cabello`, `facial`, `depilacion`, `otros` |
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
| `deposit_amount` | decimal(12,2) | Monto cobrado |
| `order_id` | bigint unsigned null | Pedido WooCommerce |
| `hold_expires_at` | datetime null | Vencimiento de la retención |
| `notes` | text | Notas internas |
| `health_consent` | tinyint(1) | Consentimiento de datos de salud |
| `created_at`, `updated_at` | datetime | Auditoría |
| `cancelled_at` | datetime null | Fecha de cancelación |
| `cancel_reason` | varchar(190) | Motivo |

Índices: `code` único, `professional_start`, `client_start`, `status_start`, `hold_expires`, `order_id`.

### 5.7 Relaciones

```text
services 1 ─── n service_professionals n ─── 1 professionals
professionals 1 ─── n schedules
professionals 1 ─── n time_off
services 1 ─── n appointments n ─── 1 professionals
users (WP) 1 ─── n appointments
orders (Woo) 1 ─── 0..1 appointments
```

---

## 6. Flujo de reserva

### 6.1 Flujo objetivo

1. La clienta entra a `/turnos/` e inicia sesión o crea su cuenta.
2. Elige servicio. El sistema filtra las profesionales habilitadas en el pivote.
3. Elige profesional, fecha y horario disponible.
4. Completa el consentimiento de datos de salud si el servicio lo requiere.
5. El sistema crea un turno `pending` con `hold_expires_at = ahora + 15 min`.
6. Se crea un pedido WooCommerce con el turno asociado y se redirige al checkout.
7. La clienta paga el total con Mercado Pago.
8. El webhook acredita el pago: el turno pasa a `confirmed` y el pedido a `completed`.
9. Se envía email de confirmación a la clienta y copia al salón.
10. Si el pago no se acredita antes del vencimiento, el cron libera la retención.

### 6.2 Máquina de estados

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
- Cancelación con reembolso: al menos 24 h antes.
- Reprogramación: al menos 12 h antes.
- Fuera de plazo: se permite cancelar, pero sin reembolso, según la política vigente.

### 6.3 Retención y concurrencia

Una retención no puede depender solo de un índice único: dos rangos distintos pueden superponerse sin compartir la misma clave. La estrategia correcta es:

1. Abrir una transacción o tomar un lock de aplicación (`GET_LOCK`) por profesional.
2. Consultar solapamientos con `start_at < nuevo_end AND end_at > nuevo_start`.
3. Contar como bloqueantes los turnos `pending` vigentes y `confirmed`.
4. Insertar el turno solo si no hay conflicto.
5. Liberar el lock.

Además, cada intento de reserva debe llevar una clave de idempotencia para que reintentos de red o doble clic no creen dos turnos. La Fase 2 implementa esta lógica.

### 6.4 Disponibilidad

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

- WooCommerce es la fuente de verdad del pedido y del pago.
- El turno se vincula al pedido por `appointments.order_id` y meta del pedido (`_glowup_appointment_id`).
- El cobro es total al reservar. La configuración `deposit` existe pero no se usa en el MVP.
- Mercado Pago se integra con el plugin oficial, que gestiona checkout, webhooks y reembolsos.
- Los reembolsos se inician desde WooCommerce y deben reflejarse en el estado del turno.

### 7.2 Consideraciones

- Los medios de pago no instantáneos (efectivo, transferencia) pueden tardar más que la retención de 15 minutos. Antes de habilitarlos hay que definir una ventana mayor o una confirmación manual.
- El webhook debe ser idempotente: un mismo evento no puede confirmar dos veces ni duplicar emails.
- El estado del pedido y el estado del turno deben mapearse explícitamente para evitar inconsistencias.
- Las credenciales de producción todavía no están disponibles. El plugin está instalado e inactivo.
- No se deben commitear credenciales; se cargan en la configuración de WooCommerce o en constantes de `wp-config.php`.

### 7.3 Productos y envíos

- 18 productos simples en cinco categorías.
- Precios finales con IVA incluido.
- Stock gestionado en productos físicos; gift card virtual sin stock.
- Zona de envío nacional y retiro en el salón sin costo.
- El costo de envío definitivo queda pendiente de definición.

---

## 8. Roles y permisos

| Rol | Uso | Capacidades principales |
| --- | --- | --- |
| `administrator` | Agustina / administración | Todas las capacidades GlowUp y WooCommerce |
| `glowup_professional` | Estela / Agustina | Ver turnos, gestionar su agenda, subir archivos |
| `customer` | Clientas | Comprar, reservar, ver sus turnos |

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

### 9.2 Endpoints previstos

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/glowup/v1/availability` | Slots disponibles por servicio, profesional y fecha |
| POST | `/glowup/v1/holds` | Crear retención y pedido |
| POST | `/glowup/v1/appointments/{id}/cancel` | Cancelar con validación de plazo |
| POST | `/glowup/v1/appointments/{id}/reschedule` | Reprogramar con validación de plazo |
| GET | `/glowup/v1/appointments` | Turnos de la clienta autenticada |

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

- Motor de disponibilidad, holds y prevención de solapamientos: Fase 2.
- Wizard público y creación de orden: Fase 3.
- Mercado Pago sandbox/producción y webhooks: Fase 4, bloqueado por credenciales.
- Emails reales y recordatorios: Fase 5.
- CRUD completo de servicios, profesionales, horarios y excepciones: Fase 6.
- Agenda operativa con vista diaria/semanal: Fase 6.
- Tests automatizados y CI: posterior al MVP.
- Estilos finos del checkout por bloques y del email: posterior.
- Costos de envío y dirección exacta: negocio.
- Textos legales definitivos y facturación: negocio/legal.
- Fotos y logo definitivos: contenido.
- Evaluar si los turnos vencidos se eliminan o se conservan como `expired` para auditoría.
- Evaluar extensión de retención para medios de pago no instantáneos.

---

## 15. Glosario

- **Turno / appointment:** reserva de un servicio con una profesional en un rango horario.
- **Hold / retención:** bloqueo temporal del horario mientras se paga.
- **Slot:** horario de inicio posible según intervalo y duración.
- **Pivote:** tabla que relaciona servicios con profesionales.
- **Seña:** pago parcial; no se usa en el MVP.
- **Gift card:** tarjeta de regalo simple por un monto.
- **Pack:** combinación de servicios; su automatización completa es post-MVP.
- **HPOS:** almacenamiento de pedidos de WooCommerce en tablas propias.
- **Idempotencia:** propiedad por la cual repetir una operación no duplica su efecto.
