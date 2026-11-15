# GlowUp

GlowUp es un salón de estética en La Rioja capital. Este repositorio guarda el tema de WordPress del sitio: la vidriera de marca, las páginas de contenido y el wizard de reservas.

Es un trabajo práctico de la facultad. Corre en un entorno local y no cobra turnos.

## Stack

| Pieza | Versión |
| --- | --- |
| WordPress | 7.1.4 |
| PHP | 8.2.12 |
| MariaDB | 10.4.32, puerto 3307 |
| WooCommerce | 11.3.0 |
| Tema `temaestetica` | 0.3.0 |
| Plugin `glowup-turnos` | 0.3.0, esquema 1.1.0 |
| Moneda | ARS, precios con IVA incluido |
| Zona horaria | `America/Argentina/La_Rioja` |

## Cómo funciona una reserva

1. La clienta crea su cuenta en `/turnos/` o inicia sesión.
2. Elige servicio, profesional, fecha y horario. El tema consulta la disponibilidad del plugin.
3. El plugin retiene el horario 15 minutos y crea un turno `pending`.
4. El plugin crea una orden de WooCommerce y la asocia al turno.
5. La clienta ve el resumen con el código de reserva y el número de pedido.

El cobro online está apagado. La orden queda pendiente y el plugin libera el horario cuando vence la retención.

## Puesta en marcha

Necesitás XAMPP con WordPress y WooCommerce activos.

1. Copiá este repo en `wp-content/themes/temaestetica`.
2. Cloná `glowup-turnos` en `wp-content/plugins/glowup-turnos`.
3. Activá WooCommerce, `glowup-turnos` y el tema.
4. Revisá Ajustes > Generales para la zona horaria `America/Argentina/La_Rioja` y WooCommerce > Ajustes para la moneda ARS.
5. Creá las páginas de la tabla y asigná cada plantilla.
6. Armá los menús principal, del pie y legal.
7. Cargá las imágenes en Apariencia > Personalizar > GlowUp · Marca y contacto.
8. Abrí `/turnos/` con una cuenta de clienta.

La base de datos no está en el repo. El plugin siembra servicios, profesionales, relaciones y horarios al activarse. Las páginas, los menús, los productos y las imágenes están en la base: recrearlos o restaurarlos desde el entorno de la demo.

## Páginas y slugs

| Página | Slug | Plantilla |
| --- | --- | --- |
| Inicio | `/` | `front-page.php` |
| Servicios | `/servicios/` | `page-servicios.php` |
| Nosotras | `/nosotras/` | `page-nosotras.php` |
| Contacto | `/contacto/` | `page-contacto.php` |
| Turnos | `/turnos/` | `page-turnos.php` |
| Tienda | `/tienda/` | WooCommerce, cartel “Próximamente” |
| Carrito | `/carrito/` | WooCommerce, cartel “Próximamente” |
| Finalizar compra | `/finalizar-compra/` | WooCommerce, cartel “Próximamente” |
| Mi cuenta | `/mi-cuenta/` | WooCommerce |
| Privacidad | `/politica-de-privacidad/` | Página común |
| Términos | `/terminos-y-condiciones/` | Página común |
| Cancelación | `/politica-de-cancelacion/` | Página común |

La home usa `front-page.php`. El tema ignora el contenido de la página Inicio.

## Estructura

```text
temaestetica/
├── assets/
│   ├── css/main.css          Diseño y responsive
│   ├── css/editor.css        Estilos del editor
│   ├── img/                  Placeholders y favicon
│   ├── js/main.js            Menú, acordeón y contador del carrito
│   └── js/booking.js         Wizard de reserva
├── inc/
│   ├── setup.php             Soporte del tema y menús
│   ├── assets.php            Encolado y datos para el wizard
│   ├── template-tags.php     Helpers de imágenes, iconos y URLs
│   ├── content.php           Servicios, profesionales, FAQ y políticas
│   ├── customizer.php        Imágenes de marca y contacto
│   ├── booking.php           Cuenta, avisos y helpers de reserva
│   └── woocommerce.php       Integración con WooCommerce
├── template-parts/           Secciones de la home
├── page-*.php                Plantillas de páginas
├── coming-soon.php           Cartel de tienda “Próximamente”
├── front-page.php            Home
├── header.php / footer.php
├── theme.json                Paleta, tipografía y espaciado
└── style.css                 Cabecera del tema
```

## Datos del entorno de demo

El plugin siembra:

- 29 servicios en uñas, cabello, facial y depilación definitiva.
- 2 profesionales y 29 relaciones servicio-profesional.
- 12 bloques horarios: lunes a viernes de 9 a 20 con pausa de 13 a 14, sábados de 9 a 14.

El entorno de la demo también tiene 18 productos de WooCommerce en cinco categorías. Quedan como catálogo interno y el público no los ve.

## Qué incluye la entrega

- Home, Servicios, Nosotras y Contacto con contenido propio.
- Cuenta de clienta con registro e ingreso dentro de `/turnos/`.
- Wizard con servicio, profesional, fecha, horario, resumen y consentimientos.
- Retención de 15 minutos con contador y liberación al vencer.
- Prevención de solapamientos con lock por profesional y revalidación.
- Orden de WooCommerce `pending` asociada al turno, sin cobro.
- Agenda de bloqueos en el panel GlowUp.
- Tienda pública pausada con el cartel “Próximamente”.
- Documentación del proyecto y guía de demo.

## Fuera de alcance

- Cobro online con Mercado Pago, webhooks y reembolsos. El interruptor de pagos sigue apagado.
- Emails transaccionales y recordatorios.
- Agenda diaria y semanal, edición de horarios y notas internas.
- Cancelación y reprogramación desde la cuenta.
- Dominio, hosting, correo de producción y textos legales definitivos.
- QA formal, backups, monitoreo y rollback.
- Tienda pública completa: catálogo, carrito y checkout de productos.

## Documentación

- `docs/PLAN.md`: alcance, fases y estado.
- `docs/ARQUITECTURA.md`: modelo de datos, API y flujo técnico.
- `docs/FASES-REORGANIZADAS.txt`: fases y alcance de la entrega.
- `docs/GUIA-DEMO.md`: recorrido para la presentación.

El plugin `glowup-turnos` está en un repositorio aparte.
