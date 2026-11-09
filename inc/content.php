<?php
/**
 * Content data and fallbacks.
 *
 * The booking plugin is the source of truth once it is active. These arrays
 * keep the theme usable while the plugin is being built and serve as the
 * initial seed content.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback service menu, grouped by category.
 *
 * @return array
 */
function glowup_fallback_service_menu() {
	return array(
		'unas'       => array(
			'title'       => 'Uñas',
			'description' => 'Manos y pies con acabados que duran, sin sacrificar la salud de la uña.',
			'items'       => array(
				array( 'name' => 'Esmaltado semipermanente en manos', 'duration' => 60, 'price' => 18000, 'description' => 'Preparación de la uña, esmaltado y sellado de larga duración.', 'requires_evaluation' => false ),
				array( 'name' => 'Esmaltado semipermanente en pies', 'duration' => 60, 'price' => 16000, 'description' => 'Acabado parejo y resistente, con preparación completa.', 'requires_evaluation' => false ),
				array( 'name' => 'Soft gel', 'duration' => 90, 'price' => 24000, 'description' => 'Extensión liviana con acabado natural y estructura flexible.', 'requires_evaluation' => false ),
				array( 'name' => 'Capping', 'duration' => 90, 'price' => 26000, 'description' => 'Refuerzo sobre la uña natural para protegerla y darle forma.', 'requires_evaluation' => false ),
				array( 'name' => 'Uñas esculpidas', 'duration' => 120, 'price' => 32000, 'description' => 'Extensión a medida con estructura personalizada.', 'requires_evaluation' => false ),
				array( 'name' => 'Nail art', 'duration' => 30, 'price' => 6000, 'description' => 'Diseño a mano alzada o con stickers. Se cotiza según complejidad.', 'requires_evaluation' => false ),
				array( 'name' => 'Spa de manos con parafina', 'duration' => 45, 'price' => 14000, 'description' => 'Exfoliación, masaje y baño de parafina tibia.', 'requires_evaluation' => false ),
				array( 'name' => 'Spa de pies con parafina', 'duration' => 60, 'price' => 16000, 'description' => 'Exfoliación, masaje y baño de parafina para pies cansados.', 'requires_evaluation' => false ),
			),
		),
		'cabello'    => array(
			'title'       => 'Cabello',
			'description' => 'Color, textura y movimiento con una mirada que respeta tu pelo.',
			'items'       => array(
				array( 'name' => 'Corte', 'duration' => 45, 'price' => 15000, 'description' => 'Corte personalizado según textura, rutina y estilo de vida.', 'requires_evaluation' => false ),
				array( 'name' => 'Color', 'duration' => 120, 'price' => 28000, 'description' => 'Coloración completa con diagnóstico previo y cuidado del tono.', 'requires_evaluation' => false ),
				array( 'name' => 'Mechas y balayage', 'duration' => 180, 'price' => 45000, 'description' => 'Iluminación a medida, con técnica y mantenimiento personalizado.', 'requires_evaluation' => false ),
				array( 'name' => 'Botox capilar', 'duration' => 90, 'price' => 25000, 'description' => 'Tratamiento de reconstrucción y brillo para cabellos castigados.', 'requires_evaluation' => false ),
				array( 'name' => 'Keratina', 'duration' => 180, 'price' => 50000, 'description' => 'Alisado progresivo con protocolo de cuidado posterior.', 'requires_evaluation' => false ),
				array( 'name' => 'Baño de crema', 'duration' => 45, 'price' => 12000, 'description' => 'Hidratación profunda y desenredo suave.', 'requires_evaluation' => false ),
				array( 'name' => 'Brushing', 'duration' => 45, 'price' => 12000, 'description' => 'Secado y peinado con movimiento natural.', 'requires_evaluation' => false ),
				array( 'name' => 'Peinado o recogido para eventos', 'duration' => 90, 'price' => 25000, 'description' => 'Diseño a medida para novias, fiestas y ocasiones especiales.', 'requires_evaluation' => false ),
			),
		),
		'facial'     => array(
			'title'       => 'Facial',
			'description' => 'Limpieza, hidratación y detalles que se notan en la piel y en el ánimo.',
			'items'       => array(
				array( 'name' => 'Peeling y limpieza para piel acneica', 'duration' => 75, 'price' => 25000, 'description' => 'Limpieza profunda, extracción cuidadosa y protocolo calmante.', 'requires_evaluation' => true ),
				array( 'name' => 'Hidratación facial y masaje con gua sha', 'duration' => 60, 'price' => 22000, 'description' => 'Hidratación intensa, masaje de drenaje y efecto descanso.', 'requires_evaluation' => false ),
				array( 'name' => 'Perfilado y laminado de cejas', 'duration' => 45, 'price' => 15000, 'description' => 'Diseño según rostro, laminado y nutrición.', 'requires_evaluation' => false ),
				array( 'name' => 'Lifting y tintura de pestañas', 'duration' => 60, 'price' => 20000, 'description' => 'Curvatura y color para una mirada despierta sin extensiones.', 'requires_evaluation' => true ),
				array( 'name' => 'Limpieza facial express', 'duration' => 45, 'price' => 18000, 'description' => 'Limpieza suave para mantener la piel equilibrada entre tratamientos.', 'requires_evaluation' => false ),
				array( 'name' => 'Mascarilla facial personalizada', 'duration' => 30, 'price' => 9000, 'description' => 'Protocolo corto según necesidad: hidratación, calma o luminosidad.', 'requires_evaluation' => false ),
			),
		),
		'depilacion' => array(
			'title'       => 'Depilación',
			'description' => 'Cera tibia, técnica cuidadosa y un después que calma la piel.',
			'items'       => array(
				array( 'name' => 'Cera piernas completas', 'duration' => 45, 'price' => 12000, 'description' => 'Depilación con cera tibia y cuidado posterior.', 'requires_evaluation' => false ),
				array( 'name' => 'Cera axilas', 'duration' => 15, 'price' => 5000, 'description' => 'Depilación rápida y prolija.', 'requires_evaluation' => false ),
				array( 'name' => 'Cera bozo', 'duration' => 10, 'price' => 3500, 'description' => 'Depilación facial con cera de baja temperatura.', 'requires_evaluation' => false ),
				array( 'name' => 'Cera cavado', 'duration' => 20, 'price' => 6000, 'description' => 'Técnica cuidadosa con cera tibia.', 'requires_evaluation' => false ),
				array( 'name' => 'Cera brazos completos', 'duration' => 30, 'price' => 9000, 'description' => 'Depilación completa de brazos.', 'requires_evaluation' => false ),
				array( 'name' => 'Depilación masculina de espalda', 'duration' => 45, 'price' => 15000, 'description' => 'Depilación con cera y protocolo calmante.', 'requires_evaluation' => false ),
				array( 'name' => 'Tratamiento calmante post-depilación', 'duration' => 30, 'price' => 8000, 'description' => 'Loción calmante, hidratación y recomendaciones de cuidado.', 'requires_evaluation' => false ),
				array( 'name' => 'Depilación definitiva por zona', 'duration' => 30, 'price' => 18000, 'description' => 'Requiere evaluación previa. Se recomienda un ciclo de 6 sesiones.', 'requires_evaluation' => true ),
			),
		),
	);
}

/**
 * Service menu. Uses the plugin repository when available.
 *
 * @return array
 */
function glowup_get_service_menu() {
	if ( class_exists( 'GlowUp\\Turnos\\Repositories\\Services_Repository' ) ) {
		$repository = new GlowUp\Turnos\Repositories\Services_Repository();
		$groups     = $repository->get_active_grouped();
		if ( ! empty( $groups ) ) {
			return $groups;
		}
	}

	return glowup_fallback_service_menu();
}

/**
 * Flat list of services.
 *
 * @return array
 */
function glowup_get_services_flat() {
	$flat = array();
	foreach ( glowup_get_service_menu() as $slug => $group ) {
		foreach ( $group['items'] as $item ) {
			$item['category']      = $group['title'];
			$item['category_slug'] = $slug;
			$flat[]                = $item;
		}
	}

	return $flat;
}

/**
 * Fallback professionals.
 *
 * @return array
 */
function glowup_fallback_professionals() {
	return array(
		array(
			'slug'      => 'agustina',
			'name'      => 'Agustina',
			'role'      => 'Uñas y facial',
			'bio'       => 'Manicurista y esteticista. Le gusta el detalle prolijo, las uñas que se sienten naturales y las pieles que vuelven a respirar.',
			'image'     => 'agustina',
			'instagram' => '',
		),
		array(
			'slug'      => 'estela',
			'name'      => 'Estela',
			'role'      => 'Peluquería y depilación',
			'bio'       => 'Peluquera y depiladora. Trabaja el color con calma, escucha lo que querés y cuida la salud del cabello.',
			'image'     => 'estela',
			'instagram' => '',
		),
	);
}

/**
 * Professionals. Uses the plugin repository when available.
 *
 * @return array
 */
function glowup_get_professionals() {
	if ( class_exists( 'GlowUp\\Turnos\\Repositories\\Professionals_Repository' ) ) {
		$repository = new GlowUp\Turnos\Repositories\Professionals_Repository();
		$people     = $repository->get_active();
		if ( ! empty( $people ) ) {
			return $people;
		}
	}

	return glowup_fallback_professionals();
}

/**
 * Process steps.
 *
 * @return array
 */
function glowup_get_process_steps() {
	return array(
		array(
			'number' => '01',
			'title'  => 'Elegí tu servicio',
			'text'   => 'Mirá la carta, filtrá por lo que necesitás y elegí la profesional que te acompaña.',
		),
		array(
			'number' => '02',
			'title'  => 'Reservá y pagá',
			'text'   => 'Elegí día y horario, pagá online y guardá tu confirmación.',
		),
		array(
			'number' => '03',
			'title'  => 'Vení y disfrutá',
			'text'   => 'Llegá unos minutos antes. Nosotras nos ocupamos del resto.',
		),
	);
}

/**
 * Studio values.
 *
 * @return array
 */
function glowup_get_values() {
	return array(
		array( 'title' => 'Tiempos reales', 'text' => 'Turnos espaciados para que no tengas que correr.' ),
		array( 'title' => 'Productos con criterio', 'text' => 'Elegimos marcas y fórmulas que funcionan.' ),
		array( 'title' => 'Mirada profesional', 'text' => 'Cada servicio empieza con una pregunta, no con una receta.' ),
		array( 'title' => 'Un espacio cuidado', 'text' => 'Luz, música y detalles para que la pausa sea completa.' ),
	);
}

/**
 * Shop categories.
 *
 * @return array
 */
function glowup_get_shop_categories() {
	$categories = array(
		array( 'slug' => 'unas', 'title' => 'Uñas', 'text' => 'Esmaltes, bases, limas y todo para mantener el acabado.', 'image' => 'shop', 'url' => glowup_shop_url() ),
		array( 'slug' => 'cabello', 'title' => 'Cabello', 'text' => 'Shampoo, mascarillas y protección térmica.', 'image' => 'shop', 'url' => glowup_shop_url() ),
		array( 'slug' => 'rostro', 'title' => 'Rostro', 'text' => 'Limpieza, hidratación y protección solar.', 'image' => 'shop', 'url' => glowup_shop_url() ),
		array( 'slug' => 'cuerpo-depilacion', 'title' => 'Cuerpo', 'text' => 'Cremas, aceites y cuidado post-depilación.', 'image' => 'shop', 'url' => glowup_shop_url() ),
	);

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return $categories;
	}

	$by_slug = array();
	foreach ( $terms as $term ) {
		$by_slug[ $term->slug ] = $term;
	}

	foreach ( $categories as &$category ) {
		if ( isset( $by_slug[ $category['slug'] ] ) ) {
			$link = get_term_link( $by_slug[ $category['slug'] ] );
			if ( ! is_wp_error( $link ) ) {
				$category['url'] = $link;
			}
		}
	}

	return $categories;
}

/**
 * Testimonials.
 *
 * @return array
 */
function glowup_get_testimonials() {
	return array(
		array( 'quote' => 'Hace años que voy con Agustina. Mis uñas nunca estuvieron tan sanas.', 'name' => 'Malena', 'detail' => 'Esmaltado semipermanente' ),
		array( 'quote' => 'El color me quedó exactamente como lo imaginaba. Estela escucha de verdad.', 'name' => 'Caro', 'detail' => 'Color y brushing' ),
		array( 'quote' => 'Reservar online y pagar en el momento me cambió la semana.', 'name' => 'Sofía', 'detail' => 'Limpieza facial' ),
	);
}

/**
 * FAQ items.
 *
 * @return array
 */
function glowup_get_faq_items() {
	return array(
		array( 'q' => '¿Cómo reservo un turno?', 'a' => 'Creá tu cuenta, elegí el servicio, la fecha y el horario disponible. El turno queda confirmado cuando el pago es aprobado.' ),
		array( 'q' => '¿Puedo cancelar?', 'a' => 'Sí, hasta 24 horas antes del turno con reembolso. Para reprogramar, hasta 12 horas antes.' ),
		array( 'q' => '¿Qué pasa si llego tarde?', 'a' => 'Vamos a hacer lo posible por atenderte, pero el servicio puede acortarse para no afectar los turnos siguientes.' ),
		array( 'q' => '¿Los precios incluyen impuestos?', 'a' => 'Sí, los precios publicados son finales e incluyen impuestos cuando corresponda.' ),
		array( 'q' => '¿Hacen envíos?', 'a' => 'Hacemos envíos nacionales y también podés retirar en el salón.' ),
		array( 'q' => '¿Necesito una evaluación antes de algún tratamiento?', 'a' => 'Algunos servicios, como depilación definitiva o peelings, requieren una evaluación previa. Lo indicamos en la carta de servicios.' ),
	);
}

/**
 * Booking policies.
 *
 * @return array
 */
function glowup_get_booking_policies() {
	return array(
		'cancel'    => 'Podés cancelar hasta 24 horas antes y te devolvemos el pago completo.',
		'reschedule'=> 'Podés reprogramar hasta 12 horas antes, sujeto a disponibilidad.',
		'late'      => 'Si llegás tarde, el servicio puede acortarse para no afectar los turnos siguientes.',
		'noshow'    => 'Si no te presentás, el turno se considera ausente y no genera reembolso.',
		'refund'    => 'Los reembolsos se procesan por el mismo medio de pago. El plazo depende de Mercado Pago y de tu banco.',
	);
}
