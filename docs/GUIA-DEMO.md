# Guía de demo

Recorrido para presentar GlowUp en la facultad. Dura entre 10 y 15 minutos.

## Antes de la demo

- [ ] XAMPP con Apache y MySQL activos.
- [ ] Tema `temaestetica` y plugin `glowup-turnos` activos.
- [ ] WooCommerce activo.
- [ ] Cuenta de clienta lista.
- [ ] Cuenta de administradora lista.
- [ ] Servicios, profesionales y horarios sembrados.
- [ ] Imágenes cargadas en el Customizer.
- [ ] `/tienda/` muestra “Próximamente” en una ventana sin sesión.
- [ ] Turnos de prueba viejos cancelados o eliminados.
- [ ] Navegador con la consola abierta para detectar errores.

## Datos de la demo

| Dato | Valor |
| --- | --- |
| Cuenta de clienta | |
| Contraseña | |
| Cuenta de administradora | |
| Servicio elegido | |
| Profesional elegida | |

Completá esta tabla antes de presentar. No subas contraseñas reales al repositorio.

## Recorrido

### 1. Home (2 minutos)

Mostrá la propuesta, los servicios destacados, el equipo y el llamado a reservar. Señalá que la tienda aparece como “Próximamente” y que el carrito no está visible para las visitantes.

### 2. Servicios (2 minutos)

Recorré las categorías: uñas, cabello, facial y depilación definitiva. Mostrá duración y precio. Aclará que los precios incluyen IVA.

### 3. Nosotras y Contacto (1 minuto)

Perfiles de Agustina y Estela. En Contacto, dirección, horarios, email y preguntas frecuentes.

### 4. Turnos sin sesión (2 minutos)

Entrá a `/turnos/`. Mostrá el formulario de registro. Creá una cuenta nueva o iniciá sesión con la cuenta de prueba. Explicá que la cuenta es un usuario de WooCommerce con rol `customer`.

### 5. Wizard (5 minutos)

1. Elegí un servicio.
2. Elegí la profesional habilitada.
3. Elegí fecha y horario.
4. Revisá el resumen y aceptá términos, cancelación y datos de salud.
5. Retené el horario. Mostrá el contador de 15 minutos.
6. Confirmá la reserva. Mostrá el código, el número de pedido y el estado pendiente.

Aclaración para el jurado: no hay cobro. La orden queda pendiente y el horario se libera al vencer la retención.

### 6. Mi cuenta (1 minuto)

Abrí Mi cuenta y mostrá el pedido pendiente. Si el tiempo alcanza, mostrá el detalle del pedido y el aviso de que el pago online se habilita en otra etapa.

### 7. Panel de administración (2 minutos)

Entrá con la cuenta de administradora. Recorré:

- El panel GlowUp.
- Servicios y profesionales.
- Agenda de bloqueos: `wp-admin/admin.php?page=glowup-turnos-agenda`.
- Ajustes de reservas: retención, anticipación y zona horaria.

Cerrá con la tienda. `/tienda/` muestra “Próximamente” para visitantes.

## Puntos técnicos para explicar

- El tema y el plugin están separados. El tema muestra. El plugin decide la disponibilidad y guarda los turnos.
- Servicios y profesionales están en tablas propias (`wp_glowup_*`), no en posts.
- La retención usa `GET_LOCK` por profesional, transacción y revalidación de solapamientos.
- La creación de la orden es idempotente: un doble envío reutiliza la orden existente.
- La tienda pública está pausada, pero WooCommerce sigue activo para cuentas y órdenes de reserva.
- El interruptor `glowup_booking_payments_enabled()` controla el cobro y sigue apagado.

## Preguntas probables

**¿Por qué WordPress y WooCommerce?**
Porque dan cuentas, órdenes y checkout sin construir esa infraestructura. El plugin propio conserva las reglas de agenda.

**¿Por qué separar el tema del plugin?**
Para cambiar el diseño sin tocar turnos. Si desactivás el plugin, el sitio sigue navegable y `/turnos/` avisa que la agenda no está disponible.

**¿Cómo evitan dos reservas en el mismo horario?**
El plugin bloquea por profesional con `GET_LOCK`, abre una transacción y vuelve a validar solapamientos antes de insertar.

**¿Qué pasa si la clienta no confirma?**
La retención vence a los 15 minutos. El turno pasa a `expired` y el horario vuelve a estar disponible.

**¿Por qué no se cobra?**
La integración de Mercado Pago necesita credenciales y cuentas reales. Queda fuera de la entrega. El flujo termina con la orden pendiente.

**¿Qué falta para producción?**
Cobro, emails, agenda operativa, hosting, dominio, textos legales y QA.

**¿La tienda está terminada?**
No. La tienda pública muestra “Próximamente” a propósito. WooCommerce se usa para cuentas y órdenes de reserva.

## Capturas sugeridas

- Home completa.
- Servicios por categoría.
- Wizard en el paso de fecha y hora.
- Resumen con la retención activa.
- Pantalla final con el pedido pendiente.
- Mi cuenta con el pedido.
- Panel GlowUp y agenda de bloqueos.
- `/tienda/` con el cartel “Próximamente”.

Guardalas en la entrega si la cátedra las pide.

## Plan B

Si falla el entorno local:

- Mostrá las capturas.
- Abrí el código de `page-turnos.php`, `assets/js/booking.js` y los endpoints del plugin.
- Explicá el flujo con el diagrama de `docs/ARQUITECTURA.md`.
