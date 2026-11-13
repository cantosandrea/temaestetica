# GlowUp — Plan de proyecto

**Versión:** 0.4.0
**Actualizado:** 12 de noviembre de 2026
**Estado:** Fase 3 completada. La Fase 4 (pagos) es el siguiente paso, bloqueada por credenciales de Mercado Pago. La tienda pública queda para la Fase 8 y durante el MVP muestra solo “Próximamente”.

---

## 1. Resumen ejecutivo

GlowUp es un salón de estética integral en La Rioja capital. El proyecto construye un sitio propio sobre WordPress que reúne tres piezas:

1. Una vidriera de marca y contenido: servicios, equipo, políticas, preguntas frecuentes y contacto.
2. Un motor de reservas con cuenta de cliente, pago total al reservar y políticas de cancelación y reprogramación.
3. Una tienda WooCommerce planificada como fase futura, con productos de cuidado, gift cards, retiro en el salón y envíos nacionales.

La implementación se apoya en WordPress y WooCommerce para cuentas, pedidos y pagos. La lógica de agenda vive en un plugin propio, `glowup-turnos`, para no mezclar reglas de negocio con la presentación. El tema `temaestetica` conserva el slug técnico y muestra la marca **GlowUp**.

El trabajo se desarrolla por fases. La Fase 3 ya está cerrada: `/turnos/` permite crear una cuenta o iniciar sesión, elegir servicio, profesional, fecha y horario, retener el lugar por 15 minutos y generar una orden de WooCommerce asociada al turno. **Todavía no se cobra**: la orden queda pendiente de pago y el checkout online se habilita en la Fase 4. La tienda pública de productos sigue pausada: `/tienda/`, las fichas de producto, el carrito y el checkout de productos muestran “Próximamente”, mientras WooCommerce sigue activo para cuentas y órdenes de reservas.

---

## 2. Objetivos

### Objetivo general

Publicar un sitio de estética integral que permita conocer los servicios del salón y reservar un turno con pago total, con una experiencia cuidada en mobile y escritorio. La compra online de productos queda para una fase futura, después del lanzamiento de las reservas.

### Objetivos específicos

- Consolidar una identidad visual propia, distinta de una plantilla genérica.
- Permitir que una clienta cree su cuenta, elija servicio, profesional, fecha y horario, y pague al reservar.
- Evitar turnos superpuestos y liberar automáticamente los horarios retenidos que no se pagan.
- Respetar las políticas del salón: cancelación con reembolso hasta 24 h antes y reprogramación hasta 12 h antes.
- Mantener la tienda pública en modo “Próximamente” durante el MVP, sin exponer productos, precios, carrito ni checkout.
- Dejar el catálogo interno y la infraestructura de WooCommerce preparados para abrir la tienda en la Fase 8.
- Dar al equipo un panel simple para consultar servicios, profesionales y turnos.
- Dejar preparadas las integraciones de pago y notificaciones para cuando existan las cuentas definitivas.

---

## 3. Alcance del MVP

### 3.1 Sitio público

- Inicio con propuesta de valor, servicios destacados, equipo, testimonios y llamado a reservar.
- Servicios agrupados en Uñas, Cabello, Facial y Depilación definitiva, con duración, precio y descripción.
- Página del equipo con perfiles de Agustina y Estela.
- Página de contacto con dirección a confirmar en La Rioja capital, horarios, email y teléfono.
- Preguntas frecuentes y políticas de cancelación, privacidad y términos.
- Tienda pública con un cartel de “Próximamente”: sin catálogo, precios, carrito ni checkout de productos durante el MVP.
- Página de turnos con el wizard público: cuenta, selección de servicio, profesional, fecha y horario, retención y resumen previo al pago.

### 3.2 Reservas

- Reserva exclusiva para clientas con cuenta.
- Registro e ingreso dentro de `/turnos/`, sin salir del flujo de reserva.
- Selección de servicio, profesional, fecha y horario.
- Consulta de disponibilidad contra la API del plugin y creación de una retención de 15 minutos.
- Creación automática de una orden de WooCommerce asociada al turno, en estado pendiente de pago.
- Pago total al reservar. La seña queda configurada como opción futura, no como modo activo.
- Confirmación automática al acreditarse el pago (Fase 4).
- Cancelación con reembolso hasta 24 horas antes del turno (pendiente de implementación).
- Reprogramación hasta 12 horas antes del turno (pendiente de implementación).
- Recordatorios por email 24 horas y 2 horas antes (Fase 5).
- Registro de consentimiento para datos de salud cuando el servicio lo requiera.
- Manejo explícito de horario ocupado, retención vencida, sesión vencida y doble envío.

### 3.3 Tienda (futura, fuera del MVP)

La tienda pública completa es la **Fase 8** y no forma parte del MVP. Durante el MVP:

- `/tienda/` muestra solamente un cartel de “Próximamente”.
- No se muestran productos, precios, carrito ni checkout de productos.
- No se puede completar una compra ni acceder al carrito o al checkout: esas pantallas muestran “Próximamente”.
- El icono del carrito se oculta del encabezado y la sección de tienda de la home se reemplaza por un aviso de “Próximamente”.
- Los 18 productos quedan cargados como catálogo interno, sin exposición pública, para no perder el trabajo ya hecho.
- WooCommerce sigue activo para cuentas, órdenes y el checkout de reservas de la Fase 4. Lo que se pausa es la tienda pública de productos, no el motor de cuentas.
- La pantalla de pago de una orden de reserva se exceptúa de la pausa para que el flujo pueda revisarse y completarse en la Fase 4.

Alcance futuro de la Fase 8: categorías y fichas de producto, carrito y checkout, stock, cupones, retiro en el salón, envío nacional con costos definidos, gift card, devoluciones y textos legales de compra.

### 3.4 Administración

- Panel GlowUp con métricas básicas y estado del proyecto.
- Listado de servicios y profesionales.
- Ajustes de reservas: retención, intervalo, anticipación, políticas, zona horaria, email y recordatorios.
- Agenda operativa con bloqueos puntuales para vacaciones, feriados y ausencias.
- Edición completa de horarios semanales y vista diaria/semanal en la Fase 6.

---

## 4. Decisiones cerradas

| Tema | Decisión |
| --- | --- |
| Plataforma | WordPress + WooCommerce |
| Tema | Tema propio `temaestetica`, marca visible GlowUp, híbrido clásico con `theme.json` |
| Reservas | Plugin propio `glowup-turnos`, separado del tema |
| Cuentas | Obligatorias para reservar; el cliente usa el rol `customer` de WooCommerce |
| Cobro | Pago total al reservar; la seña queda como opción futura |
| Pasarela | Mercado Pago mediante el plugin oficial |
| Retención de horario | 15 minutos mientras se completa el pago |
| Orden de reserva | Se crea en la Fase 3 sin cobro, queda `pending` y se asocia al turno |
| Cancelación | Hasta 24 h antes con reembolso; fuera de plazo, sin reembolso |
| Reprogramación | Hasta 12 h antes |
| Recordatorios | Solo email; WhatsApp queda cancelado |
| Bonos y gift cards | Gift card simple incluida; sistema completo de packs/bonos después del MVP |
| Tienda pública | Pausada durante el MVP: solo cartel “Próximamente”. Tienda completa en la Fase 8, futura |
| Envíos | Retiro en el salón y envío nacional, a definir en la Fase 8 |
| Precios | Finales, con IVA incluido |
| Facturación fiscal | A evaluar más adelante |
| Idioma y moneda | Español de Argentina, ARS |
| Ubicación | La Rioja capital; dirección exacta a confirmar |
| Hosting | Solo localhost por ahora; producción pendiente |
| Herramientas | Gratuitas o propias; sin licencias pagas |

---

## 5. Fases

| Fase | Contenido | Estado |
| --- | --- | --- |
| 0. Descubrimiento | Relevamiento, decisiones de negocio, alcance y stack | Completada |
| 1. Base técnica | Entorno local, backup, WooCommerce, tema, plugin base, contenido y tienda inicial | Completada |
| 2. Motor de agenda | Disponibilidad, excepciones, retenciones, prevención de solapamientos y persistencia | Completada |
| 3. Wizard de reserva | Cuenta, selección de servicio/profesional/horario, retención y creación de orden | Completada |
| 4. Pagos | Mercado Pago, webhooks, confirmación, reembolsos y conciliación | Siguiente / bloqueada por credenciales |
| 5. Emails | Confirmación, cancelación, reprogramación y recordatorios 24 h / 2 h | Pendiente |
| 6. Operación | Agenda diaria, edición de horarios semanales, estados y notas | Pendiente |
| 7. QA y lanzamiento | Pruebas integrales, accesibilidad, performance, legales y despliegue | Pendiente |
| 8. Tienda online | Tienda pública completa: catálogo, carrito, checkout, envíos y gift cards | Futura |
| 9. Post-MVP | Packs/bonos completos, facturación fiscal, mejoras de fidelización | Futura |

---

## 6. Estado actual

### Completado

- Entorno local XAMPP con WordPress, PHP 8.2 y MariaDB.
- Backup de la base anterior en `C:\xampp\backups\esteticaweb\estetica_bd-20261108-230343.sql`.
- WooCommerce 11.3.0 activo. Mercado Pago 8.10.0 instalado e inactivo.
- Configuración regional es_AR, moneda ARS, precios con IVA incluido, páginas legales y menús.
- Tema GlowUp 0.2.0 activo: plantillas, sistema de diseño, `theme.json`, estilos de editor, placeholders de imagen y favicon.
- Plugin `glowup-turnos` 0.3.0 activo con esquema 1.1.0: tablas, roles, ajustes, repositorios, contenido inicial y panel de administración.
- Motor de agenda de la Fase 2: disponibilidad por servicio, profesional y fecha; pausas, excepciones y anticipación mínima/máxima.
- Retención de horarios durante 15 minutos con lock por profesional, transacción, idempotencia y prevención de solapamientos.
- API REST de disponibilidad, creación de retenciones y consulta de turnos de la clienta autenticada.
- Agenda de bloqueos en el panel GlowUp, con filtro por profesional y alta/baja de vacaciones, feriados o ausencias.
- Los holds vencidos se conservan como `expired` para auditoría y dejan de bloquear la disponibilidad de inmediato.
- 29 servicios, 2 profesionales, 29 relaciones servicio-profesional y 12 bloques horarios sembrados.
- Depilación solo definitiva: seis zonas sueltas (axilas, bozo, cavado, piernas, brazos y bikini) más un pack completo, todos con evaluación previa.
- 18 productos en 5 categorías, con SKU, stock, descripciones y precios, cargados como catálogo interno.
- Tienda pública pausada con una plantilla propia: `/tienda/`, las fichas de producto, `/carrito/` y el checkout de productos muestran “Muy pronto” a las visitantes. Administradoras y encargadas de tienda siguen viendo la tienda real para gestionarla.
- Home y encabezado adaptados a la pausa: la sección de tienda se reemplaza por un aviso de “Próximamente” y el icono del carrito se oculta a las visitantes.
- Wizard público en `/turnos/`: registro e ingreso inline, selección de servicio, profesional, fecha y horario, resumen, consentimientos y creación de la orden.
- Creación de la orden de WooCommerce desde el plugin: `POST /glowup/v1/orders`, asociación por `order_id`, metadatos del turno y reutilización idempotente ante doble envío.
- La retención vence a los 15 minutos y el wizard muestra el contador. Si el horario se ocupa o la retención vence, el flujo ofrece elegir otro horario sin perder el estado.
- La pantalla de pago de una orden de reserva queda exceptuada de la pausa de la tienda. Mientras no exista cobro, muestra un aviso, oculta el botón de pago y ofrece volver a Mi cuenta.
- Errores verificados: horario ocupado (409), retención vencida (409), sesión vencida (403) y doble envío de orden (reutilización idempotente).
- Accesibilidad 100 y Best Practices 100 en Lighthouse; sin desbordes horizontales en la verificación responsive.
- Página de inicio, Servicios, Nosotras, Contacto, Turnos, Tienda, categoría, producto, carrito, checkout y Mi cuenta verificadas sin errores de PHP. En Tienda, carrito y checkout se verificó el cartel de “Próximamente” para visitantes; las pantallas reales se revisaron con una cuenta con permisos de gestión.
- Checkout con carrito real verificado con una cuenta de gestión: artículo, total, métodos de entrega y botón de compra.
- Sincronización del contador del carrito corregida para el carrito por bloques; el contador queda oculto mientras la tienda pública está pausada.
- Datos de prueba de la Fase 3 limpiados: 0 turnos y 0 órdenes de reserva al cierre.

### Próximo paso

- Fase 4: integrar Mercado Pago en sandbox, conectar el pago de la orden de reserva, mapear webhooks a estados del turno y conciliar órdenes pendientes con retenciones vencidas. Está bloqueada hasta contar con credenciales.

### Pendientes funcionales

- Cancelación y reprogramación desde la cuenta de la clienta. No formaron parte del alcance cerrado de la Fase 3 y deben resolverse antes del lanzamiento.
- Emails transaccionales y recordatorios de la Fase 5.
- Agenda operativa completa y edición de horarios de la Fase 6.

### Bloqueado o pendiente de terceros

- Credenciales de Mercado Pago para probar checkout, webhooks y reembolsos.
- Fotos reales, logo y textos legales definitivos.
- Hosting, dominio y correo de producción.
- Costos de envío y dirección exacta del salón, necesarios para abrir la tienda en la Fase 8.

---

## 7. Criterios de aceptación del MVP

- Una visitante puede crear una cuenta y reservar un turno en menos de cinco minutos.
- El sistema impide dos reservas superpuestas para la misma profesional.
- Un horario retenido sin pago se libera automáticamente a los 15 minutos.
- El pago total acredita el turno y envía confirmación por email.
- La clienta puede cancelar con reembolso hasta 24 h antes y reprogramar hasta 12 h antes.
- Los recordatorios se envían 24 h y 2 h antes, sin duplicados.
- La tienda pública muestra solo “Próximamente” y no expone productos, precios, carrito ni checkout de productos.
- El checkout de una reserva funciona sin abrir la tienda de productos.
- El panel muestra los turnos próximos y sus estados.
- No hay errores de PHP, console ni desbordes en mobile y escritorio.
- Las políticas y el consentimiento de datos de salud son visibles antes de reservar.

---

## 8. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
| --- | --- | --- |
| Credenciales de Mercado Pago tardías | Bloquea pagos y confirmación automática | Probar en sandbox apenas estén; dejar webhook y estados preparados |
| Medios de pago no instantáneos | El turno puede liberarse antes de acreditarse | Definir ventana según medio; evaluar extender retención o confirmar manualmente |
| Orden pendiente con retención vencida | La clienta puede llegar a pagar un horario ya liberado | La Fase 4 debe conciliar la orden antes del pago y cancelar o reasignar si el hold venció |
| Doble reserva por concurrencia | Mala experiencia y conflicto operativo | Bloqueo transaccional y revalidación antes de insertar |
| Doble envío de orden | Órdenes duplicadas | Lock por turno e idempotencia; la orden existente se reutiliza |
| Datos de salud | Riesgo legal y de privacidad | Consentimiento explícito, acceso restringido y retención definida |
| Fotos y marca pendientes | El sitio queda con placeholders | Slots de imagen reemplazables y carga posterior sin tocar código |
| Emails que caen en spam | Clientas sin recordatorio | Configurar SPF/DKIM en producción y probar entregabilidad |
| Textos legales provisorios | Riesgo de cumplimiento | Revisión legal antes de publicar |
| Actualizaciones de WooCommerce | Ruptura de integraciones | Staging, versiones fijas y pruebas de regresión |
| Costos de envío sin definir | Totales incorrectos cuando abra la tienda | Configurar tarifas antes de iniciar la Fase 8 |
| Tienda pública expuesta por error | Se promete una compra que aún no existe | Prueba del cartel “Próximamente” sin sesión y con cuenta de gestión antes de publicar |

---

## 9. Próximos pasos

1. Integrar Mercado Pago en sandbox y mapear webhooks a estados de turno y orden (Fase 4).
2. Resolver la conciliación de órdenes pendientes cuando la retención vence, antes de habilitar el cobro (Fase 4).
3. Definir e implementar cancelación y reprogramación desde la cuenta (antes del lanzamiento).
4. Implementar emails transaccionales y recordatorios (Fase 5).
5. Completar la agenda operativa del panel con vista diaria/semanal y edición de horarios (Fase 6).
6. Reemplazar placeholders con fotos y cerrar textos legales.
7. Ejecutar QA integral y preparar el despliegue a producción (Fase 7).
8. Recién después del lanzamiento, abrir la tienda pública como Fase 8.

---

## 10. Registro de cambios

| Fecha | Cambio |
| --- | --- |
| 2026-11-08 | Cierre de Fase 0 y Fase 1; documentación inicial del proyecto. |
| 2026-11-09 | Cierre de Fase 2: disponibilidad, excepciones, retenciones, anti-solapamiento, API REST y agenda de bloqueos. |
| 2026-11-10 | Ajuste de negocio: ubicación en La Rioja capital, zona horaria `America/Argentina/La_Rioja` y depilación solo definitiva por zona más pack completo. Plugin 0.2.1. |
| 2026-11-11 | Reorganización de fases: la tienda pública pasa a la Fase 8 (futura) y Post-MVP a la Fase 9. Durante el MVP, tienda, carrito y checkout de productos muestran “Próximamente”. WooCommerce sigue activo para cuentas y órdenes de reservas. |
| 2026-11-12 | Cierre de Fase 3: wizard público, registro e ingreso, disponibilidad, retención, orden de WooCommerce asociada y resumen previo al pago. Plugin 0.3.0 y tema 0.2.0. La Fase 4 queda como siguiente paso, bloqueada por credenciales de Mercado Pago. |
