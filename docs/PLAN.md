# GlowUp — Plan de proyecto

**Versión:** 0.2.1
**Actualizado:** 10 de noviembre de 2026
**Estado:** Fase 2 completada. La Fase 3 (wizard público y creación de orden) es el siguiente paso.

---

## 1. Resumen ejecutivo

GlowUp es un salón de estética integral en La Rioja capital. El proyecto construye un sitio propio sobre WordPress que reúne tres piezas:

1. Una vidriera de marca y contenido: servicios, equipo, políticas, preguntas frecuentes y contacto.
2. Un motor de reservas con cuenta de cliente, pago total al reservar y políticas de cancelación y reprogramación.
3. Una tienda WooCommerce con productos de cuidado, gift cards, retiro en el salón y envíos nacionales.

La implementación se apoya en WordPress y WooCommerce para cuentas, catálogo, checkout y pagos. La lógica de agenda vive en un plugin propio, `glowup-turnos`, para no mezclar reglas de negocio con la presentación. El tema `temaestetica` conserva el slug técnico y muestra la marca **GlowUp**.

El trabajo se desarrolla por fases. Al cierre de este documento están completas la base técnica, el tema, el contenido inicial, los roles, las tablas de reservas, el catálogo inicial y el motor de agenda de la Fase 2. La agenda ya calcula disponibilidad, aplica bloqueos y retiene horarios, pero todavía no acepta reservas públicas: `/turnos/` muestra un estado “próximamente” hasta que el wizard y el cobro estén listos.

---

## 2. Objetivos

### Objetivo general

Publicar un sitio de estética integral que permita conocer los servicios del salón, reservar un turno con pago total y comprar productos, con una experiencia cuidada en mobile y escritorio.

### Objetivos específicos

- Consolidar una identidad visual propia, distinta de una plantilla genérica.
- Permitir que una clienta cree su cuenta, elija servicio, profesional, fecha y horario, y pague al reservar.
- Evitar turnos superpuestos y liberar automáticamente los horarios retenidos que no se pagan.
- Respetar las políticas del salón: cancelación con reembolso hasta 24 h antes y reprogramación hasta 12 h antes.
- Vender productos con retiro en el salón o envío nacional, con precios finales que incluyen IVA.
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
- Tienda con categorías, ficha de producto, carrito, checkout y mi cuenta.
- Página de turnos con estado “próximamente” mientras el wizard no esté operativo.

### 3.2 Reservas

- Reserva exclusiva para clientas con cuenta.
- Selección de servicio, profesional, fecha y horario.
- Pago total al reservar. La seña queda configurada como opción futura, no como modo activo.
- Retención del horario durante 15 minutos mientras se completa el pago.
- Confirmación automática al acreditarse el pago.
- Cancelación con reembolso hasta 24 horas antes del turno.
- Reprogramación hasta 12 horas antes del turno.
- Recordatorios por email 24 horas y 2 horas antes.
- Registro de consentimiento para datos de salud cuando el servicio lo requiera.

### 3.3 Tienda

- 18 productos iniciales en cinco categorías: Uñas, Cabello, Rostro, Cuerpo y depilación, Packs y gift cards.
- Precios finales en pesos argentinos, con IVA incluido.
- Retiro en el salón sin costo y envío nacional.
- Gift card simple por $25.000 y un pack de servicios combinados como producto inicial.
- Checkout por bloques de WooCommerce.
- Mercado Pago como pasarela prevista, instalada pero inactiva hasta contar con credenciales.

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
| Cancelación | Hasta 24 h antes con reembolso; fuera de plazo, sin reembolso |
| Reprogramación | Hasta 12 h antes |
| Recordatorios | Solo email; WhatsApp queda cancelado |
| Bonos y gift cards | Gift card simple incluida; sistema completo de packs/bonos después del MVP |
| Envíos | Retiro en el salón y envío nacional |
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
| 3. Wizard de reserva | Flujo público, cuenta, selección de servicio/profesional/horario y creación de orden | Siguiente |
| 4. Pagos | Mercado Pago, webhooks, confirmación, reembolsos y conciliación | Pendiente / bloqueada por credenciales |
| 5. Emails | Confirmación, cancelación, reprogramación y recordatorios 24 h / 2 h | Pendiente |
| 6. Operación | Agenda diaria, edición de horarios semanales, estados y notas | Pendiente |
| 7. QA y lanzamiento | Pruebas integrales, accesibilidad, performance, legales y despliegue | Pendiente |
| 8. Post-MVP | Packs/bonos completos, facturación fiscal, mejoras de fidelización | Pendiente |

---

## 6. Estado actual

### Completado

- Entorno local XAMPP con WordPress, PHP 8.2 y MariaDB.
- Backup de la base anterior en `C:\xampp\backups\esteticaweb\estetica_bd-20261108-230343.sql`.
- WooCommerce 11.2.2 activo. Mercado Pago 8.10.0 instalado e inactivo.
- Configuración regional es_AR, moneda ARS, precios con IVA incluido, páginas legales y menús.
- Tema GlowUp activo: plantillas, sistema de diseño, `theme.json`, estilos de editor, placeholders de imagen y favicon.
- Plugin `glowup-turnos` activo en versión 0.2.1 con esquema 1.1.0: tablas, roles, ajustes, repositorios, contenido inicial y panel de administración.
- Motor de agenda de la Fase 2: disponibilidad por servicio, profesional y fecha; pausas, excepciones y anticipación mínima/máxima.
- Retención de horarios durante 15 minutos con lock por profesional, transacción, idempotencia y prevención de solapamientos.
- API REST de disponibilidad, creación de retenciones y consulta de turnos de la clienta autenticada.
- Agenda de bloqueos en el panel GlowUp, con filtro por profesional y alta/baja de vacaciones, feriados o ausencias.
- Los holds vencidos se conservan como `expired` para auditoría y dejan de bloquear la disponibilidad de inmediato.
- 29 servicios, 2 profesionales, 29 relaciones servicio-profesional y 12 bloques horarios sembrados.
- Depilación solo definitiva: seis zonas sueltas (axilas, bozo, cavado, piernas, brazos y bikini) más un pack completo, todos con evaluación previa.
- 18 productos en 5 categorías, con SKU, stock, descripciones y precios.
- Página de inicio, Servicios, Nosotras, Contacto, Turnos, Tienda, categoría, producto, carrito, checkout y Mi cuenta verificadas sin errores de PHP ni desbordes horizontales.
- Checkout con carrito real verificado: artículo, total, métodos de entrega y botón de compra.
- Sincronización del contador del carrito corregida para el carrito por bloques.
- Datos de prueba de la Fase 2 limpiados: 0 turnos y 0 bloqueos al cierre.

### En curso

- Preparación de la Fase 3: wizard público, creación de la orden WooCommerce y conexión con el checkout.

### Bloqueado o pendiente de terceros

- Credenciales de Mercado Pago para probar checkout, webhooks y reembolsos.
- Fotos reales, logo y textos legales definitivos.
- Hosting, dominio y correo de producción.
- Definición de costos de envío y dirección exacta del salón.

---

## 7. Criterios de aceptación del MVP

- Una visitante puede crear una cuenta y reservar un turno en menos de cinco minutos.
- El sistema impide dos reservas superpuestas para la misma profesional.
- Un horario retenido sin pago se libera automáticamente a los 15 minutos.
- El pago total acredita el turno y envía confirmación por email.
- La clienta puede cancelar con reembolso hasta 24 h antes y reprogramar hasta 12 h antes.
- Los recordatorios se envían 24 h y 2 h antes, sin duplicados.
- La tienda permite retiro y envío nacional, con totales correctos.
- El panel muestra los turnos próximos y sus estados.
- No hay errores de PHP, console ni desbordes en mobile y escritorio.
- Las políticas y el consentimiento de datos de salud son visibles antes de reservar.

---

## 8. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
| --- | --- | --- |
| Credenciales de Mercado Pago tardías | Bloquea pagos y confirmación automática | Probar en sandbox apenas estén; dejar webhook y estados preparados |
| Medios de pago no instantáneos | El turno puede liberarse antes de acreditarse | Definir ventana según medio; evaluar extender retención o confirmar manualmente |
| Doble reserva por concurrencia | Mala experiencia y conflicto operativo | Bloqueo transaccional y revalidación antes de insertar |
| Datos de salud | Riesgo legal y de privacidad | Consentimiento explícito, acceso restringido y retención definida |
| Fotos y marca pendientes | El sitio queda con placeholders | Slots de imagen reemplazables y carga posterior sin tocar código |
| Emails que caen en spam | Clientas sin recordatorio | Configurar SPF/DKIM en producción y probar entregabilidad |
| Textos legales provisorios | Riesgo de cumplimiento | Revisión legal antes de publicar |
| Actualizaciones de WooCommerce | Ruptura de integraciones | Staging, versiones fijas y pruebas de regresión |
| Costos de envío sin definir | Totales incorrectos | Configurar tarifas antes de abrir la tienda |

---

## 9. Próximos pasos

1. Construir el wizard público de reserva y el flujo de cuenta en `/turnos/`.
2. Crear la orden de WooCommerce al confirmar la retención y asociarla al turno.
3. Integrar Mercado Pago en sandbox y mapear webhooks a estados de turno.
4. Implementar emails transaccionales y recordatorios.
5. Completar la agenda operativa del panel con vista diaria/semanal y edición de horarios.
6. Reemplazar placeholders con fotos y cerrar textos legales.
7. Ejecutar QA integral y preparar el despliegue a producción.

---

## 10. Registro de cambios

| Fecha | Cambio |
| --- | --- |
| 2026-11-08 | Cierre de Fase 0 y Fase 1; documentación inicial del proyecto. |
| 2026-11-09 | Cierre de Fase 2: disponibilidad, excepciones, retenciones, anti-solapamiento, API REST y agenda de bloqueos. |
| 2026-11-10 | Ajuste de negocio: ubicación en La Rioja capital, zona horaria `America/Argentina/La_Rioja` y depilación solo definitiva por zona más pack completo. Plugin 0.2.1. |
