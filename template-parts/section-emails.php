<?php
/**
 * Email notifications visual demo.
 *
 * This section shows how the booking emails would look. It is a visual
 * demonstration only: no email is sent from the theme.
 *
 * @package GlowUp
 */

$glowup_email_examples = array(
	'confirmation'  => array(
		'label'   => __( 'Confirmación', 'temaestetica' ),
		'when'    => __( 'Al confirmar la reserva', 'temaestetica' ),
		'subject' => __( 'Tu turno en GlowUp está confirmado', 'temaestetica' ),
		'preview' => __( 'Viernes 20 de noviembre · 15:30 · Definitiva axilas', 'temaestetica' ),
		'icon'    => 'check',
	),
	'reminder-24'   => array(
		'label'   => __( 'Recordatorio 24 h', 'temaestetica' ),
		'when'    => __( 'Un día antes', 'temaestetica' ),
		'subject' => __( 'Tu turno es mañana', 'temaestetica' ),
		'preview' => __( 'Te esperamos mañana a las 15:30 en GlowUp.', 'temaestetica' ),
		'icon'    => 'clock',
	),
	'reminder-2'    => array(
		'label'   => __( 'Recordatorio 2 h', 'temaestetica' ),
		'when'    => __( 'El mismo día', 'temaestetica' ),
		'subject' => __( 'Tu turno es en 2 horas', 'temaestetica' ),
		'preview' => __( 'Hoy a las 15:30. Te esperamos en GlowUp.', 'temaestetica' ),
		'icon'    => 'clock',
	),
	'changes'       => array(
		'label'   => __( 'Cambios de turno', 'temaestetica' ),
		'when'    => __( 'Si reprogramás o cancelás', 'temaestetica' ),
		'subject' => __( 'Tu turno cambió de fecha', 'temaestetica' ),
		'preview' => __( 'Pasamos tu turno al viernes 20 de noviembre a las 15:30. También avisamos cancelaciones y reembolsos.', 'temaestetica' ),
		'icon'    => 'arrow',
	),
);
?>
<section class="section emails-demo" id="emails">
	<div class="container">
		<header class="section-head section-head--split">
			<div>
				<p class="kicker"><?php esc_html_e( 'Acompañamiento', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Tus turnos también llegan por email.', 'temaestetica' ); ?></h2>
			</div>
			<div class="section-head__aside">
				<p><?php esc_html_e( 'Te enviamos la confirmación y dos recordatorios antes del turno. Sin WhatsApp: solo email, al correo de tu cuenta.', 'temaestetica' ); ?></p>
				<p class="emails-demo__note"><?php esc_html_e( 'Ejemplos de los avisos que recibirías al reservar.', 'temaestetica' ); ?></p>
			</div>
		</header>

		<div class="emails-demo__grid">
			<?php foreach ( $glowup_email_examples as $glowup_key => $glowup_example ) : ?>
				<article class="email-card">
					<div class="email-card__top">
						<span class="email-card__icon"><?php echo glowup_icon( $glowup_example['icon'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="email-card__when"><?php echo esc_html( $glowup_example['when'] ); ?></span>
					</div>
					<h3><?php echo esc_html( $glowup_example['label'] ); ?></h3>
					<p class="email-card__subject"><?php echo esc_html( $glowup_example['subject'] ); ?></p>
					<p class="email-card__snippet"><?php echo esc_html( $glowup_example['preview'] ); ?></p>
					<button
						class="btn btn--ghost btn--small email-card__button"
						type="button"
						data-email-demo="<?php echo esc_attr( $glowup_key ); ?>"
						aria-haspopup="dialog"
						aria-controls="email-demo-<?php echo esc_attr( $glowup_key ); ?>"
					>
						<?php esc_html_e( 'Ver ejemplo', 'temaestetica' ); ?>
						<?php echo glowup_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<?php foreach ( $glowup_email_examples as $glowup_key => $glowup_example ) : ?>
		<dialog class="email-modal" id="email-demo-<?php echo esc_attr( $glowup_key ); ?>" aria-labelledby="email-demo-title-<?php echo esc_attr( $glowup_key ); ?>">
			<div class="email-modal__bar">
				<span class="email-modal__label"><?php esc_html_e( 'Email de ejemplo', 'temaestetica' ); ?></span>
				<button class="email-modal__close" type="button" data-email-close aria-label="<?php esc_attr_e( 'Cerrar ejemplo', 'temaestetica' ); ?>">
					<?php echo glowup_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>

			<?php
			get_template_part(
				'template-parts/email',
				'preview',
				array(
					'type'    => $glowup_key,
					'label'   => $glowup_example['label'],
					'subject' => $glowup_example['subject'],
					'id'      => 'email-demo-title-' . $glowup_key,
				)
			);
			?>
		</dialog>
	<?php endforeach; ?>
</section>
