# GlowUp — Plan de proyecto

**Versión:** 0.8.0
**Actualizado:** 15 de noviembre de 2026
**Estado:** Fases 0–4 cerradas; la Fase 4 quedó verificada en sandbox (checkout, webhooks reales, pagos pendientes/rechazados/aprobados, conciliación de holds vencidos y reembolso completo). La Fase 5 se cierra como demo visual de emails; el envío real queda fuera de alcance. La Fase 6 (operación) queda fuera de alcance porque la agenda interna pertenece al plugin `glowup-turnos`, que se desarrolla en otro equipo y no está disponible en este entorno. La Fase 7 pasa a ser “Cierre de la práctica”: README, guion de demo y limpieza del repositorio, sin QA formal, legales, hosting, dominio, correo ni despliegue. La tienda pública y el post-MVP quedan fuera de alcance; la tienda solo muestra “Próximamente”.

---

## 1. Resumen ejecutivo

GlowUp es un salón de estética integral en La Rioja capital. El proyecto construye un sitio propio sobre WordPress que reúne tres piezas:

1. Una vidriera de marca y contenido: servicios, equipo, políticas, preguntas frecuentes y contacto.
2. Un motor de reservas con cuenta de cliente, pago total al reservar y políticas de cancelación y reprogramación.
3. WooCommerce como infraestructura de cuentas y órdenes; la tienda pública queda fuera de alcance y solo muestra “Próximamente”.

La implementación se apoya en WordPress y WooCommerce para cuentas, pedidos y pagos. La lógica de agenda vive en un plugin propio, `glowup-turnos`, para no mezclar reglas de negocio con la presentación. El tema `temaestetica` conserva el slug técnico y muestra la marca **GlowUp**.

El trabajo se desarrolla por fases. La Fase 3 está cerrada: `/turnos/` permite crear una cuenta o iniciar sesión, elegir servicio, profesional, fecha y horario, retener el lugar por 15 minutos y generar una orden de WooCommerce asociada al turno. La Fase 4 está cerrada en sandbox: el plugin bloquea el pago cuando la retención venció, confirma el turno al acreditarse la orden, libera el turno al cancelar o reembolsar y concilia las órdenes pendientes cuyos holds expiraron. El circuito de pago se probó con Mercado Pago en modo test; **no hay cobro real** ni despliegue, y el tema muestra el botón de pago cuando detecta una pasarela disponible. La Fase 5 se cierra como demo visual de emails y la Fase 6 queda fuera de alcance. La Fase 7 es el cierre de la práctica: documentación, guion de demo y limpieza del repositorio. La tienda pública de productos sigue pausada y no forma parte del alcance: `/tienda/`, las fichas de producto, el carrito y el checkout de productos muestran “Próximamente”, mientras WooCommerce sigue activo para cuentas y órdenes de reservas.

---

## 2. Objetivos

### Objetivo general

Construir un sitio de estética integral que permita conocer los servicios del salón y reservar un turno con pago total en sandbox, con una experiencia cuidada en mobile y escritorio. La compra online de productos queda fuera del alcance de la práctica.

### Objetivos específicos

- Consolidar una identidad visual propia, distinta de una plantilla genérica.
- Permitir que una clienta cree su cuenta, elija servicio, profesional, fecha y horario, y pague al reservar.
- Evitar turnos superpuestos y liberar automáticamente los horarios retenidos que no se pagan.
- Respetar las políticas del salón: cancelación con reembolso hasta 24 h antes y reprogramación hasta 12 h antes.
- Mantener la tienda pública en modo “Próximamente”, sin exponer productos, precios, carrito ni checkout.
- Conservar el catálogo interno y la infraestructura de WooCommerce como base técnica, sin abrir la tienda.
- Dar al equipo un panel simple para consultar servicios, profesionales y turnos.
- Dejar el pago probado en sandbox y la demo visual de emails integrada; el cobro real y el envío real quedan fuera de alcance.

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
- Pago total al reservar. La seña queda configurada como opción futura, no como modo activo. El cobro se prueba con Mercado Pago en sandbox; la producción queda fuera de alcance por ahora.
- Confirmación automática al acreditarse el pago, implementada en la Fase 4.
- Guarda server-side que impide pagar una orden cuya retención venció o cuyo turno ya no está pendiente.
- Conciliación de órdenes pendientes: si la retención vence sin pago, el turno pasa a `expired` y la orden se cancela.
- Pago tardío: si el horario sigue libre y el turno es futuro, se confirma; si ya está ocupado o el turno fue cancelado, la orden queda marcada para revisión manual y reembolso.
- Cancelación con reembolso hasta 24 horas antes del turno. El reembolso desde WooCommerce está implementado y verificado; la acción de la clienta desde su cuenta queda fuera de alcance.
- Reprogramación hasta 12 horas antes del turno; la acción desde la cuenta queda fuera de alcance.
- Recordatorios por email 24 horas y 2 horas antes: demo visual en `/turnos/`; el envío real queda fuera de alcance.
- Registro de consentimiento para datos de salud cuando el servicio lo requiera.
- Manejo explícito de horario ocupado, retención vencida, sesión vencida y doble envío.

### 3.3 Tienda pública (fuera de alcance)

La tienda pública completa queda fuera de alcance. Durante la práctica:

- `/tienda/` muestra solamente un cartel de “Próximamente”.
- No se muestran productos, precios, carrito ni checkout de productos.
- No se puede completar una compra ni acceder al carrito o al checkout: esas pantallas muestran “Próximamente”.
- El icono del carrito se oculta del encabezado y la sección de tienda de la home se reemplaza por un aviso de “Próximamente”.
- Los 18 productos quedan cargados como catálogo interno, sin exposición pública, para no perder el trabajo ya hecho.
- WooCommerce sigue activo para cuentas, órdenes y el checkout de reservas de la Fase 4. Lo que se pausa es la tienda pública de productos, no el motor de cuentas.
- La pantalla de pago de una orden de reserva se exceptúa de la pausa y cobra en modo test.

El comercio online, los envíos, la gift card, las devoluciones y los textos legales de compra quedan fuera de alcance.

### 3.4 Administración

- Panel GlowUp con métricas básicas y estado del proyecto.
- Listado de servicios y profesionales.
- Ajustes de reservas: retención, intervalo, anticipación, políticas, zona horaria, email y recordatorios.
- Agenda operativa con bloqueos puntuales para vacaciones, feriados y ausencias.
- La edición completa de horarios semanales y la vista diaria/semanal quedan fuera de alcance: pertenecen al plugin `glowup-turnos`, que no está disponible en este entorno.

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
| Orden de reserva | Se crea sin cobro, queda `pending` y se asocia al turno; el pago se habilita cuando la pasarela está disponible |
| Cancelación | Hasta 24 h antes con reembolso; fuera de plazo, sin reembolso |
| Reprogramación | Hasta 12 h antes |
| Recordatorios | Solo email; WhatsApp queda cancelado |
| Bonos y gift cards | Gift card simple incluida; sistema completo de packs/bonos después del MVP |
| Tienda pública | Pausada: solo cartel “Próximamente”. La tienda completa queda fuera de alcance |
| Envíos | Fuera de alcance; no se implementa comercio |
| Precios | Finales, con IVA incluido |
| Facturación fiscal | A evaluar más adelante |
| Idioma y moneda | Español de Argentina, ARS |
| Ubicación | La Rioja capital; dirección exacta a confirmar |
| Hosting | Solo localhost; no se prevé despliegue por ahora |
| Herramientas | Gratuitas o propias; sin licencias pagas |

---

## 5. Fases

| Fase | Contenido | Estado |
| --- | --- | --- |
| 0. Descubrimiento | Relevamiento, decisiones de negocio, alcance y stack | Completada |
| 1. Base técnica | Entorno local, backup, WooCommerce, tema, plugin base, contenido y tienda inicial | Completada |
| 2. Motor de agenda | Disponibilidad, excepciones, retenciones, prevención de solapamientos y persistencia | Completada |
| 3. Wizard de reserva | Cuenta, selección de servicio/profesional/horario, retención y creación de orden | Completada |
| 4. Pagos | Mercado Pago, webhooks, confirmación, reembolsos y conciliación | Completada en sandbox: checkout, webhooks reales, pagos pendientes/rechazados/aprobados, reembolso completo y conciliación verificados. Producción fuera de alcance |
| 5. Emails | Demo visual de confirmación, recordatorios 24 h / 2 h y cambio de turno | Cerrada como demo visual; el envío real queda fuera de alcance |
| 6. Operación | Agenda diaria, edición de horarios semanales, estados y notas | Fuera de alcance: pertenece al plugin `glowup-turnos`, desarrollado en otro equipo |
| 7. Cierre de la práctica | README, guion de demo, datos de ejemplo y limpieza del repositorio | Pendiente |

Las antiguas fases 8 (tienda online) y 9 (post-MVP) se retiran del plan y quedan fuera de alcance.

---

## 6. Estado actual

### Completado

- Entorno local XAMPP con WordPress, PHP 8.2 y MariaDB.
- Backup de la base anterior en `C:\xampp\backups\esteticaweb\estetica_bd-20261108-230343.sql`.
- WooCommerce 11.3.0 activo. Mercado Pago 8.10.2 activo en modo test/sandbox.
- Configuración regional es_AR, moneda ARS, precios con IVA incluido, páginas legales y menús.
- Tema GlowUp 0.3.0 activo: plantillas, sistema de diseño, `theme.json`, estilos de editor, placeholders de imagen y favicon.
- Plugin `glowup-turnos` 0.4.0 activo con esquema 1.1.0: tablas, roles, ajustes, repositorios, contenido inicial y panel de administración.
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
- La pantalla de pago de una orden de reserva queda exceptuada de la pausa de la tienda. Sin pasarela configurada muestra un aviso, oculta el botón de pago y ofrece volver a Mi cuenta; con una pasarela disponible muestra el pago real y el tiempo restante de la retención.
- Fase 4 (núcleo): guarda server-side que bloquea el pago cuando la retención venció o el turno ya no está pendiente, y que impide reconfirmar turnos cancelados, completados o ausentes.
- Fase 4 (núcleo): pago acreditado (`processing`/`completed`) confirma el turno y limpia la retención; cancelación o reembolso de la orden cancela el turno.
- Fase 4 (núcleo): la conciliación de holds vencidos marca el turno como `expired` y cancela las órdenes de reserva `pending`/`failed` asociadas.
- Fase 4 (núcleo): un pago tardío confirma el turno si el horario sigue libre y es futuro; si ya está ocupado o el turno fue cancelado, deja la orden marcada para revisión manual y reembolso.
- Fase 4 (tema): los textos de pago, el CTA del wizard, el aviso de `order-pay` y el botón de pago se adaptan a si hay una pasarela de Mercado Pago disponible.
- Fase 5 (demo visual, cerrada): `/turnos/` muestra la confirmación, los recordatorios de 24 h y 2 h y el aviso de cambio de turno como tarjetas con vista previa modal. Usa datos de ejemplo y no envía correos.
- Fase 4 verificada con 17 comprobaciones de WP-CLI y pruebas HTTP de `/turnos/` y `order-pay` en ambos estados (con y sin pasarela simulada), sin errores de PHP.
- Fase 4 (sandbox): un pago aprobado con la cuenta de prueba confirmó el turno automáticamente y dejó la orden en `processing`.
- Fase 4 (conciliación): el cron se programa al arrancar aunque el plugin ya estuviera activo; un turno con hold vencido pasó a `expired`, su pedido `pending` a `cancelled` y una segunda corrida no volvió a actuar.
- Fase 4 (webhook real): Mercado Pago envió notificaciones reales por ngrok con `source_news=webhooks`. Un pago pendiente dejó la orden en `pending` y el turno en `pending`; uno rechazado dejó la orden en `failed` y el turno en `pending`; uno aprobado dejó la orden en `processing` y el turno en `confirmed`. La notificación repetida no duplicó la confirmación.
- Fase 4 (reembolso): el reembolso completo desde WooCommerce dejó el pedido en `refunded` y el turno en `cancelled`, con la nota de liberación correspondiente.
- Los fixtures de las pruebas de Fase 4 se eliminaron al cierre. Los pedidos #103 y #104 quedan como evidencia histórica de la conciliación y del pago aprobado.
- Prerrequisitos de Fase 5: Mailpit `1.33.1` escuchando en `127.0.0.1:1025` con bandeja en `http://127.0.0.1:8025`, WP Mail SMTP `4.10.4` configurado contra ese SMTP y `wp_mail()` verificado. También se verificó un email real de WooCommerce. La matriz de emails y recordatorios quedó definida en `docs/EMAILS.md`.
- Errores verificados: horario ocupado (409), retención vencida (409), sesión vencida (403) y doble envío de orden (reutilización idempotente).
- Accesibilidad 100 y Best Practices 100 en Lighthouse; sin desbordes horizontales en la verificación responsive.
- Página de inicio, Servicios, Nosotras, Contacto, Turnos, Tienda, categoría, producto, carrito, checkout y Mi cuenta verificadas sin errores de PHP. En Tienda, carrito y checkout se verificó el cartel de “Próximamente” para visitantes; las pantallas reales se revisaron con una cuenta con permisos de gestión.
- Checkout con carrito real verificado con una cuenta de gestión: artículo, total, métodos de entrega y botón de compra.
- Sincronización del contador del carrito corregida para el carrito por bloques; el contador queda oculto mientras la tienda pública está pausada.
- Datos de prueba de la Fase 3 limpiados: 0 turnos y 0 órdenes de reserva al cierre.

### Próximo paso

- Fase 7: cerrar la práctica con README de entrega, guion de demo, datos de ejemplo y limpieza del repositorio.

### Pendiente para el cierre

- README con alcance, estado, limitaciones y pasos para ejecutar el tema en local.
- Guion de demo y limpieza de datos de prueba.
- Revisión final del repositorio: sin credenciales ni archivos temporales.

### Opcional

- Fotos y logo definitivos; los placeholders son aceptables para la práctica.
- Capturas o evidencia de la demo y presentación oral.

### Fuera de alcance

- Envío real de emails y recordatorios (plantillas, cron, duplicados y SMTP).
- Fase 6 (operación): agenda diaria/semanal, horarios, estados, notas y CRUD completo de servicios y profesionales.
- Cancelación y reprogramación desde la cuenta de la clienta.
- Tienda online, envíos, gift card y devoluciones.
- Post-MVP: packs/bonos, facturación fiscal y fidelización.
- Producción: hosting, dominio, correo, backups, monitoreo, rollback y cobro real.
- QA formal, auditoría de accesibilidad/performance y revisión legal.

---

## 7. Criterios de aceptación del MVP

- Una visitante puede crear una cuenta y reservar un turno en menos de cinco minutos.
- El sistema impide dos reservas superpuestas para la misma profesional.
- Un horario retenido sin pago se libera automáticamente a los 15 minutos.
- El pago total acredita el turno en sandbox; la confirmación por email es una demo visual.
- La cancelación y el reembolso desde WooCommerce se reflejan en el turno; la acción desde la cuenta queda fuera de alcance.
- La demo visual de recordatorios está visible en `/turnos/`; el envío real queda fuera de alcance.
- La tienda pública muestra solo “Próximamente” y no expone productos, precios, carrito ni checkout de productos.
- El checkout de una reserva funciona en sandbox sin abrir la tienda de productos.
- El panel de bloqueos permite consultar servicios, profesionales y turnos.
- Las políticas y el consentimiento de datos de salud son visibles antes de reservar; la revisión legal queda fuera de alcance.

---

## 8. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
| --- | --- | --- |
| Cobro real en producción | Fuera de alcance por ahora | No se publica; si algún día se despliega, repetir checkout, webhooks y reembolsos con credenciales reales |
| Medios de pago no instantáneos | El turno puede liberarse antes de acreditarse | Definir ventana según medio; evaluar extender retención o confirmar manualmente |
| Orden pendiente con retención vencida | La clienta puede llegar a pagar un horario ya liberado | La guarda bloquea el pago y la conciliación cancela la orden; si el pago llega igual, se confirma solo con horario libre o se marca para revisión manual |
| Doble reserva por concurrencia | Mala experiencia y conflicto operativo | Bloqueo transaccional y revalidación antes de insertar |
| Doble envío de orden | Órdenes duplicadas | Lock por turno e idempotencia; la orden existente se reutiliza |
| Datos de salud | Riesgo legal y de privacidad | Consentimiento explícito, acceso restringido y retención definida |
| Fotos y marca pendientes | El sitio queda con placeholders | Aceptado para la práctica; los slots de imagen son reemplazables |
| Demo de emails sin envío real | No hay recordatorios automáticos | Aceptado: el envío real queda fuera de alcance y la demo no promete correos |
| Textos legales provisorios | Sin revisión legal | Aceptado para la práctica; se aclara que son provisorios |
| Actualizaciones de WooCommerce | Ruptura de integraciones | Versiones fijas y verificación local antes de actualizar |
| Tienda pública fuera de alcance | No hay comercio online | Se mantiene el cartel “Próximamente” y no se exponen productos ni precios |
| Tienda pública expuesta por error | Se promete una compra que no existe | Verificación del cartel “Próximamente” sin sesión y con cuenta de gestión |

---

## 9. Próximos pasos

1. Crear el README de entrega con alcance, estado, limitaciones y pasos para ejecutar el tema en local.
2. Preparar el guion de demo y limpiar los datos de prueba.
3. Revisar el repositorio y dejar el árbol listo para entregar.
4. (Opcional) Reemplazar fotos y logo, y preparar la presentación.

---

## 10. Registro de cambios

| Fecha | Cambio |
| --- | --- |
| 2026-11-08 | Cierre de Fase 0 y Fase 1; documentación inicial del proyecto. |
| 2026-11-09 | Cierre de Fase 2: disponibilidad, excepciones, retenciones, anti-solapamiento, API REST y agenda de bloqueos. |
| 2026-11-10 | Ajuste de negocio: ubicación en La Rioja capital, zona horaria `America/Argentina/La_Rioja` y depilación solo definitiva por zona más pack completo. Plugin 0.2.1. |
| 2026-11-11 | Reorganización de fases: la tienda pública pasa a la Fase 8 (futura) y Post-MVP a la Fase 9. Durante el MVP, tienda, carrito y checkout de productos muestran “Próximamente”. WooCommerce sigue activo para cuentas y órdenes de reservas. |
| 2026-11-12 | Cierre de Fase 3: wizard público, registro e ingreso, disponibilidad, retención, orden de WooCommerce asociada y resumen previo al pago. Plugin 0.3.0 y tema 0.2.0. La Fase 4 queda como siguiente paso, bloqueada por credenciales de Mercado Pago. |
| 2026-11-13 | Fase 4 (núcleo): guarda de pago, confirmación al acreditarse, cancelación/reembolso, conciliación de holds vencidos y revisión manual de pagos tardíos. Plugin 0.4.0. El tema adapta la UI de pago según la pasarela disponible. Activación de Mercado Pago pendiente de credenciales. |
| 2026-11-14 | Cierre de Fase 4 en sandbox: pago aprobado con confirmación automática, sincronización de la facturación con la cuenta de la clienta, cron de limpieza asegurado al arrancar, conciliación real (turno `expired` + pedido `cancelled`), notificaciones reales de Mercado Pago por ngrok para pagos pendientes, rechazados y aprobados, y reembolso completo con turno `cancelled`. La producción queda fuera de alcance por ahora. |
| 2026-11-14 | Prerrequisitos de Fase 5: Mailpit + WP Mail SMTP configurados y verificados; matriz de emails y recordatorios definida en `docs/EMAILS.md`. La implementación de la Fase 5 queda como próximo paso. |
| 2026-11-15 | Fase 5 (demo visual, cerrada): `/turnos/` muestra los cuatro emails de ejemplo (confirmación, recordatorios de 24 h y 2 h, cambio de turno) en tarjetas con vista previa modal. Tema 0.3.0. No envía correos: el envío real queda fuera de alcance. |
| 2026-11-15 | Fase 6 (operación) fuera de alcance: el plugin `glowup-turnos` se desarrolla en otro equipo y no está disponible en este entorno. La agenda operativa, los horarios, los estados/notas y el CRUD quedan excluidos de la práctica. |
| 2026-11-15 | Reorganización de alcance para la práctica universitaria: la Fase 7 pasa a “Cierre de la práctica”; se omiten QA formal, legales, hosting, dominio, correo y despliegue. La Fase 5 se cierra como demo visual, la Fase 6 queda fuera de alcance y se retiran las fases 8 y 9. Docs 0.8.0. |
