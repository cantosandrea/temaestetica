<?php
/**
 * Email preview mockup for the booking page demo.
 *
 * Visual demonstration only: this template does not send email.
 *
 * @package GlowUp
 */

$glowup_email_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'type'    => 'confirmation',
		'label'   => '',
		'subject' => '',
		'id'      => '',
	)
);

$glowup_sample = array(
	'client'       => __( 'Camila', 'temaestetica' ),
	'service'      => __( 'Definitiva axilas', 'temaestetica' ),
	'professional' => __( 'Agustina', 'temaestetica' ),
	'when'         => __( 'Viernes 20 de noviembre · 15:30', 'temaestetica' ),
	'previous'     => __( 'Miércoles 18 de noviembre · 10:00', 'temaestetica' ),
	'duration'     => glowup_duration( 15 ),
	'total'        => glowup_price( 6000 ),
	'code'         => 'GLW-8F2K',
	'address'      => glowup_contact( 'address' ),
);

$glowup_contact_email = glowup_contact( 'email' );
$glowup_instagram     = glowup_contact( 'instagram' );
$glowup_account_url   = glowup_account_url();
?>
<article class="email-preview" aria-labelledby="<?php echo esc_attr( $glowup_email_args['id'] ); ?>">
	<header class="email-preview__header">
		<span class="email-preview__mark" aria-hidden="true"><?php echo glowup_mark_svg( 32 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="email-preview__brand">glowup</span>
		<span class="email-preview__eyebrow"><?php echo esc_html( $glowup_email_args['label'] ); ?></span>
	</header>

	<div class="email-preview__body">
		<p class="email-preview__from">
			<span><?php esc_html_e( 'De: GlowUp', 'temaestetica' ); ?></span>
			<span>&lt;<?php echo esc_html( $glowup_contact_email ); ?>&gt;</span>
		</p>

		<h2 id="<?php echo esc_attr( $glowup_email_args['id'] ); ?>"><?php echo esc_html( $glowup_email_args['subject'] ); ?></h2>

		<?php if ( 'confirmation' === $glowup_email_args['type'] ) : ?>
			<p class="email-preview__hello">
				<?php
				printf(
					/* translators: %s: sample client name. */
					esc_html__( 'Hola, %s.', 'temaestetica' ),
					esc_html( $glowup_sample['client'] )
				);
				?>
			</p>
			<p><?php esc_html_e( 'Tu turno quedó confirmado. Te esperamos en GlowUp.', 'temaestetica' ); ?></p>

			<ul class="email-preview__details">
				<li><span><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['service'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['professional'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Fecha y hora', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['when'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Duración', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['duration'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Total', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['total'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Código', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['code'] ); ?></strong></li>
			</ul>

			<a class="email-preview__button" href="<?php echo esc_url( $glowup_account_url ); ?>"><?php esc_html_e( 'Ver mi turno', 'temaestetica' ); ?></a>
			<p class="email-preview__small"><?php esc_html_e( 'Cancelación con reembolso hasta 24 h antes. Reprogramación hasta 12 h antes.', 'temaestetica' ); ?></p>
		<?php elseif ( 'reminder-24' === $glowup_email_args['type'] ) : ?>
			<p class="email-preview__hello">
				<?php
				printf(
					/* translators: %s: sample client name. */
					esc_html__( 'Hola, %s.', 'temaestetica' ),
					esc_html( $glowup_sample['client'] )
				);
				?>
			</p>
			<p><?php esc_html_e( 'Te recordamos que mañana tenés un turno en GlowUp.', 'temaestetica' ); ?></p>

			<ul class="email-preview__details">
				<li><span><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['service'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['professional'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Fecha y hora', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['when'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Dirección', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['address'] ); ?></strong></li>
			</ul>

			<a class="email-preview__button" href="<?php echo esc_url( $glowup_account_url ); ?>"><?php esc_html_e( 'Ver mi turno', 'temaestetica' ); ?></a>
			<p class="email-preview__small"><?php esc_html_e( 'Si no podés asistir, avisanos con al menos 24 h de anticipación.', 'temaestetica' ); ?></p>
		<?php elseif ( 'reminder-2' === $glowup_email_args['type'] ) : ?>
			<p class="email-preview__hello">
				<?php
				printf(
					/* translators: %s: sample client name. */
					esc_html__( 'Hola, %s.', 'temaestetica' ),
					esc_html( $glowup_sample['client'] )
				);
				?>
			</p>
			<p><?php esc_html_e( 'Tu turno es hoy a las 15:30. Te esperamos en GlowUp.', 'temaestetica' ); ?></p>

			<ul class="email-preview__details">
				<li><span><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['service'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['professional'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Hora', 'temaestetica' ); ?></span><strong><?php esc_html_e( '15:30', 'temaestetica' ); ?></strong></li>
				<li><span><?php esc_html_e( 'Dirección', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['address'] ); ?></strong></li>
			</ul>

			<a class="email-preview__button" href="<?php echo esc_url( $glowup_account_url ); ?>"><?php esc_html_e( 'Ver mi turno', 'temaestetica' ); ?></a>
			<p class="email-preview__small"><?php esc_html_e( 'Llegá 5 minutos antes. Si estás demorada, escribinos y lo vemos.', 'temaestetica' ); ?></p>
		<?php else : ?>
			<p class="email-preview__hello">
				<?php
				printf(
					/* translators: %s: sample client name. */
					esc_html__( 'Hola, %s.', 'temaestetica' ),
					esc_html( $glowup_sample['client'] )
				);
				?>
			</p>
			<p><?php esc_html_e( 'Tu turno cambió de fecha. Este es el nuevo horario:', 'temaestetica' ); ?></p>

			<div class="email-preview__change">
				<div>
					<span><?php esc_html_e( 'Antes', 'temaestetica' ); ?></span>
					<strong><?php echo esc_html( $glowup_sample['previous'] ); ?></strong>
				</div>
				<div>
					<span><?php esc_html_e( 'Ahora', 'temaestetica' ); ?></span>
					<strong><?php echo esc_html( $glowup_sample['when'] ); ?></strong>
				</div>
			</div>

			<ul class="email-preview__details">
				<li><span><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['service'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['professional'] ); ?></strong></li>
				<li><span><?php esc_html_e( 'Código', 'temaestetica' ); ?></span><strong><?php echo esc_html( $glowup_sample['code'] ); ?></strong></li>
			</ul>

			<a class="email-preview__button" href="<?php echo esc_url( $glowup_account_url ); ?>"><?php esc_html_e( 'Ver mi turno', 'temaestetica' ); ?></a>
			<p class="email-preview__small"><?php esc_html_e( 'Si el nuevo horario no te sirve, escribinos. Si cancelás con más de 24 h, te devolvemos el pago.', 'temaestetica' ); ?></p>
		<?php endif; ?>
	</div>

	<footer class="email-preview__footer">
		<p class="email-preview__footer-brand"><?php esc_html_e( 'GlowUp', 'temaestetica' ); ?></p>
		<p><?php echo esc_html( $glowup_sample['address'] ); ?></p>
		<p><?php echo esc_html( glowup_contact( 'hours' ) ); ?></p>
		<p>
			<a href="mailto:<?php echo esc_attr( $glowup_contact_email ); ?>"><?php echo esc_html( $glowup_contact_email ); ?></a>
			<?php if ( $glowup_instagram ) : ?>
				<span aria-hidden="true"> · </span>
				<a href="<?php echo esc_url( $glowup_instagram ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
			<?php endif; ?>
		</p>
		<p class="email-preview__legal"><?php esc_html_e( 'Recibís este email porque tenés una reserva en GlowUp. Los avisos se envían solo por email.', 'temaestetica' ); ?></p>
	</footer>
</article>
