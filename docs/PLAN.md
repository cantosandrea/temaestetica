# GlowUp — Plan de proyecto

**Versión:** 0.6.0
**Actualizado:** 15 de noviembre de 2026
**Estado:** Entrega académica. Fases 0 a 3 completadas y Fase 7 completada con la documentación de entrega. La Fase 4 (pagos) está iniciada y bloqueada por credenciales; las fases 5 (emails) y 6 (operación) no comenzaron. Las tres quedan fuera de alcance. La tienda pública sigue en “Próximamente”.

---

## 1. Resumen ejecutivo

GlowUp es un salón de estética integral en La Rioja capital. El proyecto construye un sitio propio sobre WordPress que reúne tres piezas:

1. Una vidriera de marca y contenido: servicios, equipo, políticas, preguntas frecuentes y contacto.
2. Un motor de reservas con cuenta de cliente, retención de horarios y políticas de cancelación y reprogramación. El cobro queda fuera de la entrega.
3. Una tienda WooCommerce planificada como fase futura, fuera de la entrega, con productos de cuidado, gift cards, retiro en el salón y envíos nacionales.

La implementación se apoya en WordPress y WooCommerce para cuentas, pedidos y pagos. La lógica de agenda vive en un plugin propio, `glowup-turnos`, para no mezclar reglas de negocio con la presentación. El tema `temaestetica` conserva el slug técnico y muestra la marca **GlowUp**.

El trabajo se desarrolla por fases. Este repositorio corresponde a un trabajo práctico universitario: la entrega cubre el sitio de marca y el flujo de reserva hasta la orden pendiente. No incluye cobro real, emails, operación diaria, hosting ni textos legales definitivos. La Fase 3 está cerrada: `/turnos/` permite crear una cuenta, elegir servicio, profesional, fecha y horario, retener el lugar por 15 minutos y generar una orden de WooCommerce. El interruptor `glowup_booking_payments_enabled()` sigue apagado, de modo que no se cobra nada. La tienda pública de productos muestra “Próximamente”; WooCommerce sigue activo para cuentas y órdenes de reservas.

---

## 2. Objetivos

### Objetivo general

Construir un sitio de estética integral que permita conocer los servicios del salón y reservar un turno, con una experiencia cuidada en mobile y escritorio. La entrega termina en la orden pendiente: no hay cobro online. La compra de productos queda para una fase futura.

### Objetivos específicos

- Consolidar una identidad visual propia, distinta de una plantilla genérica.
- Permitir que una clienta cree su cuenta, elija servicio, profesional, fecha y horario y genere una orden de reserva.
- Evitar turnos superpuestos y liberar los horarios retenidos que no se confirman.
- Publicar las políticas del salón: cancelación con reembolso hasta 24 h antes y reprogramación hasta 12 h antes. La implementación de esos flujos queda fuera de la entrega.
- Mantener la tienda pública en modo “Próximamente” durante la entrega, sin exponer productos, precios, carrito ni checkout.
- Dejar el catálogo interno y la infraestructura de WooCommerce preparados para abrir la tienda en la Fase 8.
- Dar al equipo un panel simple para consultar servicios, profesionales y bloqueos.
- Dejar documentado qué falta para pagos, notificaciones y operación diaria.

---

## 3. Alcance de la entrega

### 3.1 Sitio público

- Inicio con propuesta de valor, servicios destacados, equipo, testimonios y llamado a reservar.
- Servicios agrupados en Uñas, Cabello, Facial y Depilación definitiva, con duración, precio y descripción.
- Página del equipo con perfiles de Agustina y Estela.
- Página de contacto con dirección a confirmar en La Rioja capital, horarios, email y teléfono.
- Preguntas frecuentes y políticas de cancelación, privacidad y términos.
- Tienda pública con un cartel de “Próximamente”: sin catálogo, precios, carrito ni checkout de productos durante la entrega.
- Página de turnos con el wizard público: cuenta, selección de servicio, profesional, fecha y horario, retención y resumen previo a la orden.

### 3.2 Reservas

- Reserva exclusiva para clientas con cuenta.
- Registro e ingreso dentro de `/turnos/`, sin salir del flujo de reserva.
- Selección de servicio, profesional, fecha y horario.
- Consulta de disponibilidad contra la API del plugin y creación de una retención de 15 minutos.
- Creación automática de una orden de WooCommerce asociada al turno, en estado pendiente de pago.
- La orden queda pendiente: el cobro online está desactivado y no se procesa dinero.
- Confirmación de la reserva sin cobro, con código de turno y número de pedido.
- Políticas de cancelación y reprogramación publicadas. La cancelación y la reprogramación desde la cuenta quedan fuera de la entrega.
- Registro de consentimiento para datos de salud cuando el servicio lo requiera.
- Manejo explícito de horario ocupado, retención vencida, sesión vencida y doble envío.

### 3.3 Tienda (futura, fuera de la entrega)

La tienda pública completa es la **Fase 8** y no forma parte de la entrega. Durante la entrega:

- `/tienda/` muestra solamente un cartel de “Próximamente”.
- No se muestran productos, precios, carrito ni checkout de productos.
- No se puede completar una compra ni acceder al carrito o al checkout: esas pantallas muestran “Próximamente”.
- El icono del carrito se oculta del encabezado y la sección de tienda de la home se reemplaza por un aviso de “Próximamente”.
- Los 18 productos quedan cargados como catálogo interno, sin exposición pública, para no perder el trabajo ya hecho.
- WooCommerce sigue activo para cuentas y órdenes de reservas. Lo que se pausa es la tienda pública de productos, no el motor de cuentas.
- La pantalla de pago de una orden de reserva se exceptúa de la pausa para que el flujo pueda revisarse. El cobro sigue apagado.

Alcance futuro de la Fase 8: categorías y fichas de producto, carrito y checkout, stock, cupones, retiro en el salón, envío nacional con costos definidos, gift card, devoluciones y textos legales de compra.

### 3.4 Administración

- Panel GlowUp con métricas básicas y estado del proyecto.
- Listado de servicios y profesionales.
- Ajustes de reservas: retención, intervalo, anticipación, políticas, zona horaria, email y recordatorios.
- Agenda operativa con bloqueos puntuales para vacaciones, feriados y ausencias.
- La edición completa de horarios y la vista diaria/semanal quedan fuera de la entrega.

---

## 4. Decisiones cerradas

| Tema | Decisión |
| --- | --- |
| Plataforma | WordPress + WooCommerce |
| Tema | Tema propio `temaestetica`, marca visible GlowUp, híbrido clásico con `theme.json` |
| Reservas | Plugin propio `glowup-turnos`, separado del tema |
| Cuentas | Obligatorias para reservar; el cliente usa el rol `customer` de WooCommerce |
| Cobro | Fuera de alcance en la entrega; el cobro online sigue apagado. La seña queda como opción futura |
| Pasarela | Mercado Pago previsto con el plugin oficial, sin integrar |
| Retención de horario | 15 minutos mientras se confirma la reserva |
| Orden de reserva | Se crea en la Fase 3 sin cobro, queda `pending` y se asocia al turno |
| Cancelación | Política publicada: hasta 24 h antes con reembolso. El flujo desde la cuenta queda fuera de alcance |
| Reprogramación | Política publicada: hasta 12 h antes. El flujo desde la cuenta queda fuera de alcance |
| Recordatorios | Solo email; WhatsApp queda cancelado. Fuera de alcance |
| Bonos y gift cards | Fuera de alcance en la entrega |
| Tienda pública | Pausada durante la entrega: solo cartel “Próximamente”. Tienda completa en la Fase 8, futura |
| Envíos | Retiro en el salón y envío nacional, a definir en la Fase 8 |
| Precios | Finales, con IVA incluido |
| Facturación fiscal | Fuera de alcance |
| Idioma y moneda | Español de Argentina, ARS |
| Ubicación | La Rioja capital; dirección exacta a confirmar |
| Hosting | Solo localhost; dominio, hosting y correo de producción quedan fuera de alcance |
| Herramientas | Gratuitas o propias; sin licencias pagas |

---

## 5. Fases

| Fase | Contenido | Estado |
| --- | --- | --- |
| 0. Descubrimiento | Relevamiento, decisiones de negocio, alcance y stack | Completada |
| 1. Base técnica | Entorno local, backup, WooCommerce, tema, plugin base, contenido y tienda inicial | Completada |
| 2. Motor de agenda | Disponibilidad, excepciones, retenciones, prevención de solapamientos y persistencia | Completada |
| 3. Wizard de reserva | Cuenta, selección de servicio/profesional/horario, retención y creación de orden | Completada |
| 4. Pagos | Mercado Pago, webhooks, confirmación, reembolsos y conciliación | Iniciada; fuera de alcance |
| 5. Emails | Confirmación, cancelación, reprogramación y recordatorios 24 h / 2 h | Pendiente; fuera de alcance |
| 6. Operación | Agenda diaria, edición de horarios semanales, estados y notas | Pendiente; fuera de alcance |
| 7. Cierre y entrega académica | README, guía de demo, fases, plan y arquitectura | Completada |
| 8. Tienda online | Tienda pública completa: catálogo, carrito, checkout, envíos y gift cards | Futura; fuera de alcance |
| 9. Post-MVP | Packs/bonos completos, facturación fiscal, mejoras de fidelización | No aplica a la entrega |

---

## 6. Estado actual

### Completado

- Entorno local XAMPP con WordPress, PHP 8.2 y MariaDB.
- Backup de la base anterior en `C:\xampp\backups\esteticaweb\estetica_bd-20261108-230343.sql`.
- WooCommerce 11.3.0 activo. Mercado Pago 8.10.0 instalado, inactivo y fuera de alcance.
- Configuración regional es_AR, moneda ARS, precios con IVA incluido, páginas legales y menús.
- Tema GlowUp 0.3.0 activo: plantillas, sistema de diseño, `theme.json`, estilos de editor, placeholders de imagen y favicon.
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
- La pantalla de pago de una orden de reserva queda exceptuada de la pausa de la tienda. Con los pagos desactivados (por defecto), muestra un aviso, oculta el botón de pago y ofrece volver a Mi cuenta.
- Fase 4 iniciada y detenida: `glowup_booking_payments_enabled()` apagado por defecto y filtrable, avisos y botón de pago del `order-pay` condicionados al interruptor, y copia del wizard adaptada al estado de pagos.
- Errores verificados: horario ocupado (409), retención vencida (409), sesión vencida (403) y doble envío de orden (reutilización idempotente).
- Accesibilidad 100 y Best Practices 100 en Lighthouse; sin desbordes horizontales en la verificación responsive.
- Página de inicio, Servicios, Nosotras, Contacto, Turnos, Tienda, categoría, producto, carrito, checkout y Mi cuenta verificadas sin errores de PHP. En Tienda, carrito y checkout se verificó el cartel de “Próximamente” para visitantes; las pantallas reales se revisaron con una cuenta con permisos de gestión.
- Checkout con carrito real verificado con una cuenta de gestión: artículo, total, métodos de entrega y botón de compra.
- Sincronización del contador del carrito corregida para el carrito por bloques; el contador queda oculto mientras la tienda pública está pausada.
- Datos de prueba de la Fase 3 limpiados: 0 turnos y 0 órdenes de reserva al cierre.
- Fase 7: `README.md`, `docs/GUIA-DEMO.md` y documentación sincronizada para la entrega académica.

### Cierre de la entrega

La Fase 7 queda cerrada con `README.md`, `docs/GUIA-DEMO.md` y la sincronización de `docs/PLAN.md`, `docs/ARQUITECTURA.md` y `docs/FASES-REORGANIZADAS.txt`. La entrega no incluye cobro, emails, operación diaria, hosting ni textos legales definitivos.

### Fuera de alcance

- Fase 4: integración real de Mercado Pago, webhooks, conciliación de órdenes y reembolsos.
- Fase 5: emails transaccionales y recordatorios 24 h / 2 h.
- Fase 6: agenda diaria y semanal, edición de horarios, estados y notas internas.
- Cancelación y reprogramación desde la cuenta de la clienta.
- Hosting, dominio, correo de producción, textos legales definitivos y QA formal.

### Limitaciones de la demo

- La base de datos no está versionada. La demo corre sobre el entorno local preparado.
- El cobro online está apagado. La reserva termina en una orden `pending`.
- Los 18 productos quedan como catálogo interno.

---

## 7. Criterios de aceptación de la entrega

- Una visitante puede crear una cuenta y reservar un turno en menos de cinco minutos.
- El sistema impide dos reservas superpuestas para la misma profesional.
- Un horario retenido sin confirmar se libera a los 15 minutos.
- La reserva termina en una orden de WooCommerce `pending`, sin cobro.
- La clienta acepta términos, política de cancelación y consentimiento de datos de salud antes de reservar.
- La tienda pública muestra solo “Próximamente” y no expone productos, precios, carrito ni checkout de productos.
- El panel muestra servicios, profesionales, turnos y bloqueos.
- La documentación permite levantar el sitio y recorrer la demo.

---

## 8. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
| --- | --- | --- |
| Doble reserva por concurrencia | Mala experiencia y conflicto operativo | Bloqueo transaccional y revalidación antes de insertar |
| Doble envío de orden | Órdenes duplicadas | Lock por turno e idempotencia; la orden existente se reutiliza |
| Orden pendiente con retención vencida | La orden sobrevive al horario | La entrega no cobra; el turno vence y el horario se libera |
| Datos de salud | Riesgo de privacidad | Consentimiento explícito y acceso restringido |
| Fotos y marca provisorias | El sitio queda con placeholders | Slots de imagen reemplazables sin tocar código |
| Actualizaciones de WooCommerce | Ruptura de integraciones | Versiones fijas y pruebas antes de actualizar |
| Base de datos no versionada | La demo no se reproduce desde el repo | Documentar el entorno y conservar el dump local |
| Tienda pública expuesta por error | Se promete una compra que no existe | Prueba del cartel “Próximamente” sin sesión |
| Expectativa de cobro en la demo | El jurado espera un pago | La guía de demo aclara que el cobro está fuera de alcance |

---

## 9. Próximos pasos

1. Preparar la cuenta de clienta y los datos de la demo.
2. Recorrer `docs/GUIA-DEMO.md` y capturar las pantallas.
3. Presentar el proyecto.
4. Si el proyecto continúa, retomar la Fase 4 con credenciales de Mercado Pago y después avanzar con emails y operación.

---

## 10. Registro de cambios

| Fecha | Cambio |
| --- | --- |
| 2026-11-08 | Cierre de Fase 0 y Fase 1; documentación inicial del proyecto. |
| 2026-11-09 | Cierre de Fase 2: disponibilidad, excepciones, retenciones, anti-solapamiento, API REST y agenda de bloqueos. |
| 2026-11-10 | Ajuste de negocio: ubicación en La Rioja capital, zona horaria `America/Argentina/La_Rioja` y depilación solo definitiva por zona más pack completo. Plugin 0.2.1. |
| 2026-11-11 | Reorganización de fases: la tienda pública pasa a la Fase 8 (futura) y Post-MVP a la Fase 9. Durante el MVP, tienda, carrito y checkout de productos muestran “Próximamente”. WooCommerce sigue activo para cuentas y órdenes de reservas. |
| 2026-11-12 | Cierre de Fase 3: wizard público, registro e ingreso, disponibilidad, retención, orden de WooCommerce asociada y resumen previo al pago. Plugin 0.3.0 y tema 0.2.0. La Fase 4 queda como siguiente paso, bloqueada por credenciales de Mercado Pago. |
| 2026-11-12 | Inicio de Fase 4 en el tema: `glowup_booking_payments_enabled()` apagado por defecto, avisos y botón del `order-pay` condicionados y copia del wizard según el estado de pagos. Tema 0.3.0. La integración real de Mercado Pago sigue bloqueada por credenciales. |
| 2026-11-15 | Cierre de Fase 7: README, guía de demo y documentación sincronizada. Fases 4, 5 y 6 quedan fuera de alcance; la entrega termina en la orden pendiente. |
