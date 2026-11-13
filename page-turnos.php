<?php
/**
 * Template Name: Turnos
 *
 * @package GlowUp
 */

get_header();

$glowup_policies     = glowup_get_booking_policies();
$glowup_steps        = glowup_get_process_steps();
$glowup_is_logged_in = is_user_logged_in();
$glowup_can_book     = glowup_booking_can_book();
$glowup_available    = glowup_booking_is_available();
$glowup_payment_available = glowup_booking_payments_available();
$glowup_current_user = $glowup_is_logged_in ? wp_get_current_user() : null;
$glowup_privacy_url  = get_privacy_policy_url() ? get_privacy_policy_url() : glowup_booking_legal_url( 'politica-de-privacidad' );
$glowup_terms_url    = glowup_booking_legal_url( 'terminos-y-condiciones' );
$glowup_cancel_url   = glowup_booking_legal_url( 'politica-de-cancelacion' );
$glowup_hold_minutes = 15;

if ( class_exists( 'GlowUp\\Turnos\\Settings' ) ) {
	$glowup_hold_minutes = max( 1, (int) GlowUp\Turnos\Settings::get( 'hold_minutes' ) );
}
?>
<main id="main" class="site-main">
	<section class="page-hero page-hero--booking">
		<div class="container page-hero__grid">
			<div class="page-hero__inner">
				<p class="kicker"><?php esc_html_e( 'Turnos online', 'temaestetica' ); ?></p>
				<h1 class="page-hero__title"><?php esc_html_e( 'Reservá tu turno.', 'temaestetica' ); ?></h1>
				<p class="page-hero__lead">
					<?php
					printf(
						/* translators: %d: hold duration in minutes. */
						esc_html__( 'Elegí tu servicio, la profesional y el horario. Te guardamos el lugar por %d minutos mientras confirmás la reserva.', 'temaestetica' ),
						(int) $glowup_hold_minutes
					);
					?>
				</p>

				<?php if ( $glowup_is_logged_in ) : ?>
					<p class="page-hero__meta">
						<?php
						printf(
							/* translators: %s: customer display name. */
							esc_html__( 'Sesión iniciada como %s.', 'temaestetica' ),
							'<strong>' . esc_html( $glowup_current_user->display_name ) . '</strong>'
						);
						?>
						<a class="text-link" href="<?php echo esc_url( glowup_account_url() ); ?>"><?php esc_html_e( 'Mi cuenta', 'temaestetica' ); ?></a>
						<span aria-hidden="true">·</span>
						<a class="text-link" href="<?php echo esc_url( wp_logout_url( glowup_booking_url() ) ); ?>"><?php esc_html_e( 'Cerrar sesión', 'temaestetica' ); ?></a>
					</p>
				<?php else : ?>
					<div class="page-hero__actions">
						<a class="btn" href="#cuenta"><?php esc_html_e( 'Crear cuenta o ingresar', 'temaestetica' ); ?></a>
						<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/servicios/' ) ); ?>"><?php esc_html_e( 'Ver servicios', 'temaestetica' ); ?></a>
					</div>
					<p class="page-hero__meta">
						<?php if ( $glowup_payment_available ) : ?>
							<?php esc_html_e( 'El pago online se realiza con Mercado Pago al confirmar la reserva.', 'temaestetica' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'El pago online se habilita en la próxima etapa. No se realizan cobros todavía.', 'temaestetica' ); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="page-hero__image">
				<?php glowup_image( 'booking', __( 'Turnos en GlowUp', 'temaestetica' ), 'page-hero__image-el', true ); ?>
			</div>
		</div>
	</section>

	<section class="section booking-section" id="reservar">
		<div class="container">
			<?php glowup_booking_auth_notice(); ?>

			<?php if ( isset( $_GET['glowup_pay'] ) && 'expired' === sanitize_key( wp_unslash( $_GET['glowup_pay'] ) ) ) : ?>
				<div class="booking-alert booking-alert--error" role="alert">
					<p><?php esc_html_e( 'La retención de ese horario venció y el pedido ya no se puede pagar. Elegí un nuevo horario para reservar.', 'temaestetica' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! $glowup_available ) : ?>
				<div class="booking-notice">
					<p class="kicker"><?php esc_html_e( 'Agenda online', 'temaestetica' ); ?></p>
					<h2><?php esc_html_e( 'La agenda no está disponible.', 'temaestetica' ); ?></h2>
					<p><?php esc_html_e( 'El motor de reservas no está activo en este momento. Escribinos y coordinamos tu turno a mano.', 'temaestetica' ); ?></p>
					<a class="btn" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></a>
				</div>
			<?php elseif ( ! $glowup_is_logged_in ) : ?>
				<div class="booking-auth" id="cuenta">
					<div class="booking-auth__intro">
						<p class="kicker"><?php esc_html_e( 'Tu cuenta', 'temaestetica' ); ?></p>
						<h2><?php esc_html_e( 'Reservá en menos de cinco minutos.', 'temaestetica' ); ?></h2>
						<p><?php esc_html_e( 'Para guardar el turno necesitás una cuenta. Es gratis y te sirve para ver tus reservas, reprogramar o cancelar.', 'temaestetica' ); ?></p>

						<ol class="booking-auth__steps">
							<li>
								<span>01</span>
								<div>
									<strong><?php esc_html_e( 'Creá tu cuenta', 'temaestetica' ); ?></strong>
									<p><?php esc_html_e( 'Con tu email y una contraseña. Si ya compraste en la tienda, usá el mismo email.', 'temaestetica' ); ?></p>
								</div>
							</li>
							<li>
								<span>02</span>
								<div>
									<strong><?php esc_html_e( 'Elegí tu turno', 'temaestetica' ); ?></strong>
									<p><?php esc_html_e( 'Servicio, profesional, fecha y horario disponible, todo en la misma pantalla.', 'temaestetica' ); ?></p>
								</div>
							</li>
							<li>
								<span>03</span>
								<div>
									<strong><?php esc_html_e( 'Confirmá', 'temaestetica' ); ?></strong>
									<p>
										<?php if ( $glowup_payment_available ) : ?>
											<?php
											printf(
												/* translators: %d: hold duration in minutes. */
												esc_html__( 'Retenemos el lugar por %d minutos y creamos tu pedido para que pagues online.', 'temaestetica' ),
												(int) $glowup_hold_minutes
											);
											?>
										<?php else : ?>
											<?php
											printf(
												/* translators: %d: hold duration in minutes. */
												esc_html__( 'Retenemos el lugar por %d minutos y creamos tu pedido. El pago online se habilita en la próxima etapa.', 'temaestetica' ),
												(int) $glowup_hold_minutes
											);
											?>
										<?php endif; ?>
									</p>
								</div>
							</li>
						</ol>

						<p class="booking-auth__note"><?php esc_html_e( '¿Preferís coordinar por email?', 'temaestetica' ); ?> <a class="text-link" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php echo esc_html( glowup_contact( 'email' ) ); ?></a></p>
					</div>

					<div class="booking-auth__forms">
						<article class="auth-card">
							<p class="kicker"><?php esc_html_e( 'Ya tengo cuenta', 'temaestetica' ); ?></p>
							<h3><?php esc_html_e( 'Ingresá', 'temaestetica' ); ?></h3>
							<p class="auth-card__lead"><?php esc_html_e( 'Usá tu email o usuario y tu contraseña.', 'temaestetica' ); ?></p>

							<form class="auth-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
								<input type="hidden" name="action" value="glowup_login">
								<?php wp_nonce_field( 'glowup_login', 'glowup_login_nonce' ); ?>

								<div class="auth-field">
									<label for="booking-login"><?php esc_html_e( 'Email o usuario', 'temaestetica' ); ?></label>
									<input id="booking-login" name="log" type="text" autocomplete="username" required>
								</div>

								<div class="auth-field">
									<label for="booking-password"><?php esc_html_e( 'Contraseña', 'temaestetica' ); ?></label>
									<input id="booking-password" name="pwd" type="password" autocomplete="current-password" required>
								</div>

								<label class="auth-check">
									<input type="checkbox" name="rememberme" value="forever">
									<span><?php esc_html_e( 'Mantener sesión iniciada', 'temaestetica' ); ?></span>
								</label>

								<button class="btn" type="submit"><?php esc_html_e( 'Ingresar', 'temaestetica' ); ?></button>

								<p class="auth-links">
									<a class="text-link" href="<?php echo esc_url( wp_lostpassword_url( glowup_booking_url() ) ); ?>"><?php esc_html_e( 'Olvidé mi contraseña', 'temaestetica' ); ?></a>
								</p>
							</form>
						</article>

						<article class="auth-card auth-card--accent">
							<p class="kicker"><?php esc_html_e( 'Primera vez', 'temaestetica' ); ?></p>
							<h3><?php esc_html_e( 'Creá tu cuenta', 'temaestetica' ); ?></h3>
							<p class="auth-card__lead"><?php esc_html_e( 'Solo necesitás un email y una contraseña de al menos 8 caracteres.', 'temaestetica' ); ?></p>

							<form class="auth-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
								<input type="hidden" name="action" value="glowup_register">
								<?php wp_nonce_field( 'glowup_register', 'glowup_register_nonce' ); ?>

								<div class="auth-row">
									<div class="auth-field">
										<label for="booking-first-name"><?php esc_html_e( 'Nombre', 'temaestetica' ); ?></label>
										<input id="booking-first-name" name="first_name" type="text" autocomplete="given-name" required>
									</div>

									<div class="auth-field">
										<label for="booking-last-name"><?php esc_html_e( 'Apellido', 'temaestetica' ); ?></label>
										<input id="booking-last-name" name="last_name" type="text" autocomplete="family-name">
									</div>
								</div>

								<div class="auth-field">
									<label for="booking-email"><?php esc_html_e( 'Email', 'temaestetica' ); ?></label>
									<input id="booking-email" name="email" type="email" autocomplete="email" required>
								</div>

								<div class="auth-field">
									<label for="booking-new-password"><?php esc_html_e( 'Contraseña', 'temaestetica' ); ?></label>
									<input id="booking-new-password" name="password" type="password" autocomplete="new-password" minlength="8" required>
									<p class="field-hint"><?php esc_html_e( 'Mínimo 8 caracteres.', 'temaestetica' ); ?></p>
								</div>

								<label class="auth-check">
									<input type="checkbox" name="privacy" value="1" required>
									<span>
										<?php
										printf(
											/* translators: %s: privacy policy link. */
											esc_html__( 'Acepto la %s.', 'temaestetica' ),
											'<a href="' . esc_url( $glowup_privacy_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'política de privacidad', 'temaestetica' ) . '</a>'
										);
										?>
									</span>
								</label>

								<button class="btn" type="submit"><?php esc_html_e( 'Crear cuenta', 'temaestetica' ); ?></button>
							</form>
						</article>
					</div>
				</div>
			<?php elseif ( ! $glowup_can_book ) : ?>
				<div class="booking-notice">
					<p class="kicker"><?php esc_html_e( 'Tu cuenta', 'temaestetica' ); ?></p>
					<h2><?php esc_html_e( 'Esta cuenta no puede reservar.', 'temaestetica' ); ?></h2>
					<p>
						<?php
						printf(
							/* translators: %s: customer display name. */
							esc_html__( 'Ingresaste como %s, pero la reserva online está disponible para cuentas de clienta. Si creés que es un error, escribinos.', 'temaestetica' ),
							'<strong>' . esc_html( $glowup_current_user->display_name ) . '</strong>'
						);
						?>
					</p>
					<div class="booking-notice__actions">
						<a class="btn" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></a>
						<a class="btn btn--ghost" href="<?php echo esc_url( glowup_account_url() ); ?>"><?php esc_html_e( 'Mi cuenta', 'temaestetica' ); ?></a>
					</div>
				</div>
			<?php else : ?>
				<div class="booking-wizard" data-booking-wizard>
					<div class="booking-wizard__main">
						<ol class="wizard-steps" aria-label="<?php esc_attr_e( 'Pasos de la reserva', 'temaestetica' ); ?>">
							<li class="wizard-step is-current" data-step-indicator="1" aria-current="step"><span>01</span><span><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></span></li>
							<li class="wizard-step" data-step-indicator="2"><span>02</span><span><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></span></li>
							<li class="wizard-step" data-step-indicator="3"><span>03</span><span><?php esc_html_e( 'Fecha y hora', 'temaestetica' ); ?></span></li>
							<li class="wizard-step" data-step-indicator="4"><span>04</span><span><?php esc_html_e( 'Confirmar', 'temaestetica' ); ?></span></li>
						</ol>

						<div class="booking-alert" data-wizard-alert role="alert" tabindex="-1" hidden></div>

						<section class="wizard-panel" data-step-panel="1" aria-labelledby="wizard-step-1">
							<header class="wizard-panel__head">
								<p class="kicker"><?php esc_html_e( 'Paso 1', 'temaestetica' ); ?></p>
								<h2 id="wizard-step-1" tabindex="-1"><?php esc_html_e( 'Elegí tu servicio', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Mirá la carta por categoría. Los precios son finales e incluyen impuestos.', 'temaestetica' ); ?></p>
							</header>

							<div class="wizard-loading" data-services-loading>
								<span class="wizard-spinner" aria-hidden="true"></span>
								<span><?php esc_html_e( 'Cargando servicios…', 'temaestetica' ); ?></span>
							</div>
							<div class="service-picker" data-services></div>

							<div class="wizard-actions">
								<button class="btn" type="button" data-next="2" disabled><?php esc_html_e( 'Continuar', 'temaestetica' ); ?></button>
							</div>
						</section>

						<section class="wizard-panel" data-step-panel="2" aria-labelledby="wizard-step-2" hidden>
							<header class="wizard-panel__head">
								<p class="kicker"><?php esc_html_e( 'Paso 2', 'temaestetica' ); ?></p>
								<h2 id="wizard-step-2" tabindex="-1"><?php esc_html_e( 'Elegí a tu profesional', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Solo aparecen las profesionales que realizan el servicio que elegiste.', 'temaestetica' ); ?></p>
							</header>

							<div class="wizard-loading" data-professionals-loading hidden>
								<span class="wizard-spinner" aria-hidden="true"></span>
								<span><?php esc_html_e( 'Buscando profesionales…', 'temaestetica' ); ?></span>
							</div>
							<div class="choice-grid choice-grid--professionals" data-professionals></div>

							<div class="wizard-actions">
								<button class="btn btn--ghost" type="button" data-back="1"><?php esc_html_e( 'Volver', 'temaestetica' ); ?></button>
								<button class="btn" type="button" data-next="3" disabled><?php esc_html_e( 'Continuar', 'temaestetica' ); ?></button>
							</div>
						</section>

						<section class="wizard-panel" data-step-panel="3" aria-labelledby="wizard-step-3" hidden>
							<header class="wizard-panel__head">
								<p class="kicker"><?php esc_html_e( 'Paso 3', 'temaestetica' ); ?></p>
								<h2 id="wizard-step-3" tabindex="-1"><?php esc_html_e( 'Elegí fecha y horario', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Los horarios se actualizan según la agenda real de la profesional.', 'temaestetica' ); ?></p>
							</header>

							<div class="wizard-field">
								<label for="booking-date"><?php esc_html_e( 'Fecha del turno', 'temaestetica' ); ?></label>
								<input id="booking-date" type="date" data-date-input>
								<p class="field-hint" data-date-hint></p>
							</div>

							<div class="wizard-slots" data-slots aria-live="polite"></div>

							<div class="wizard-actions">
								<button class="btn btn--ghost" type="button" data-back="2"><?php esc_html_e( 'Volver', 'temaestetica' ); ?></button>
								<button class="btn" type="button" data-next="4" disabled><?php esc_html_e( 'Continuar', 'temaestetica' ); ?></button>
							</div>
						</section>

						<section class="wizard-panel" data-step-panel="4" aria-labelledby="wizard-step-4" hidden>
							<header class="wizard-panel__head">
								<p class="kicker"><?php esc_html_e( 'Paso 4', 'temaestetica' ); ?></p>
								<h2 id="wizard-step-4" tabindex="-1"><?php esc_html_e( 'Revisá y confirmá', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Revisá el detalle antes de guardar el horario. La retención se libera automáticamente si no confirmás a tiempo.', 'temaestetica' ); ?></p>
							</header>

							<dl class="summary-list summary-list--paper" data-review></dl>
							<p class="review-note" data-review-evaluation hidden></p>

							<div class="review-consents">
								<label class="checkline">
									<input type="checkbox" data-terms value="1">
									<span>
										<?php
										printf(
											/* translators: 1: terms link, 2: cancellation policy link. */
											esc_html__( 'Acepto los %1$s y la %2$s.', 'temaestetica' ),
											'<a href="' . esc_url( $glowup_terms_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'términos y condiciones', 'temaestetica' ) . '</a>',
											'<a href="' . esc_url( $glowup_cancel_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'política de cancelación', 'temaestetica' ) . '</a>'
										);
										?>
									</span>
								</label>

								<label class="checkline">
									<input type="checkbox" data-health value="1">
									<span>
										<?php
										printf(
											/* translators: %s: privacy policy link. */
											esc_html__( 'Autorizo el tratamiento de mis datos de salud para gestionar este turno, según la %s.', 'temaestetica' ),
											'<a href="' . esc_url( $glowup_privacy_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'política de privacidad', 'temaestetica' ) . '</a>'
										);
										?>
									</span>
								</label>
							</div>

							<p class="review-note"><?php echo $glowup_payment_available ? esc_html__( 'El cobro se realiza al finalizar, con Mercado Pago.', 'temaestetica' ) : esc_html__( 'No se realiza ningún cobro en este paso.', 'temaestetica' ); ?></p>

							<div class="wizard-actions">
								<button class="btn btn--ghost" type="button" data-back="3"><?php esc_html_e( 'Volver', 'temaestetica' ); ?></button>
								<button class="btn" type="button" data-create-hold disabled><?php esc_html_e( 'Retener horario', 'temaestetica' ); ?></button>
							</div>
						</section>

						<section class="wizard-panel" data-step-panel="5" aria-labelledby="wizard-step-5" hidden>
							<header class="wizard-panel__head">
								<p class="kicker"><?php esc_html_e( 'Horario retenido', 'temaestetica' ); ?></p>
								<h2 id="wizard-step-5" tabindex="-1"><?php esc_html_e( 'Guardamos tu lugar.', 'temaestetica' ); ?></h2>
								<p>
									<?php if ( $glowup_payment_available ) : ?>
										<?php esc_html_e( 'Tenés unos minutos para completar el pago. Si no se acredita, el horario vuelve a quedar libre.', 'temaestetica' ); ?>
									<?php else : ?>
										<?php esc_html_e( 'Tenés unos minutos para confirmar la reserva. Después, el horario vuelve a quedar libre.', 'temaestetica' ); ?>
									<?php endif; ?>
								</p>
							</header>

							<div class="hold-timer" data-hold-timer>
								<span class="hold-timer__label"><?php esc_html_e( 'Tiempo restante', 'temaestetica' ); ?></span>
								<strong data-hold-countdown>15:00</strong>
							</div>

							<dl class="summary-list summary-list--paper" data-hold-summary></dl>

							<div class="wizard-actions">
								<button class="btn btn--ghost" type="button" data-restart><?php esc_html_e( 'Elegir otro horario', 'temaestetica' ); ?></button>
								<button class="btn" type="button" data-create-order><?php echo $glowup_payment_available ? esc_html__( 'Continuar al pago', 'temaestetica' ) : esc_html__( 'Confirmar reserva', 'temaestetica' ); ?></button>
							</div>
						</section>

						<section class="wizard-panel wizard-panel--success" data-step-panel="6" aria-labelledby="wizard-step-6" hidden>
							<p class="kicker"><?php esc_html_e( 'Pedido creado', 'temaestetica' ); ?></p>
							<?php if ( $glowup_payment_available ) : ?>
								<h2 id="wizard-step-6" tabindex="-1"><?php esc_html_e( 'Tu reserva está casi lista.', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Guardamos el horario y creamos el pedido. Completá el pago online para confirmar tu turno.', 'temaestetica' ); ?></p>
							<?php else : ?>
								<h2 id="wizard-step-6" tabindex="-1"><?php esc_html_e( 'Tu reserva quedó registrada.', 'temaestetica' ); ?></h2>
								<p><?php esc_html_e( 'Guardamos el horario y creamos el pedido. El pago online se habilita en la próxima etapa; no se realizó ningún cobro.', 'temaestetica' ); ?></p>
							<?php endif; ?>

							<div class="booking-alert booking-alert--info">
								<p>
									<?php
									printf(
										/* translators: %s: order number. */
										esc_html__( 'Pedido %s.', 'temaestetica' ),
										'<strong data-order-number>—</strong>'
									);
									?>
									<span data-order-status></span>
								</p>
								<p data-order-hold-note></p>
							</div>

							<dl class="summary-list summary-list--paper" data-order-summary></dl>

							<div class="wizard-actions">
								<a class="btn" href="#" data-order-pay hidden><?php esc_html_e( 'Pagar ahora', 'temaestetica' ); ?></a>
								<a class="btn<?php echo $glowup_payment_available ? ' btn--ghost' : ''; ?>" href="#" data-order-view><?php esc_html_e( 'Ver mi pedido', 'temaestetica' ); ?></a>
								<a class="btn btn--ghost" href="<?php echo esc_url( glowup_account_url() ); ?>"><?php esc_html_e( 'Ir a mi cuenta', 'temaestetica' ); ?></a>
							</div>
						</section>
					</div>

					<aside class="booking-summary" aria-label="<?php esc_attr_e( 'Resumen de la reserva', 'temaestetica' ); ?>" aria-live="polite">
						<p class="kicker kicker--light"><?php esc_html_e( 'Tu reserva', 'temaestetica' ); ?></p>
						<h2><?php esc_html_e( 'Resumen', 'temaestetica' ); ?></h2>

						<dl class="summary-list summary-list--dark">
							<div class="summary-row">
								<dt><?php esc_html_e( 'Servicio', 'temaestetica' ); ?></dt>
								<dd data-summary-service>—</dd>
							</div>
							<div class="summary-row">
								<dt><?php esc_html_e( 'Profesional', 'temaestetica' ); ?></dt>
								<dd data-summary-professional>—</dd>
							</div>
							<div class="summary-row">
								<dt><?php esc_html_e( 'Fecha y hora', 'temaestetica' ); ?></dt>
								<dd data-summary-when>—</dd>
							</div>
							<div class="summary-row">
								<dt><?php esc_html_e( 'Duración', 'temaestetica' ); ?></dt>
								<dd data-summary-duration>—</dd>
							</div>
						</dl>

						<p class="booking-summary__total">
							<span data-summary-total-label><?php esc_html_e( 'Total', 'temaestetica' ); ?></span>
							<strong data-summary-total>—</strong>
						</p>
						<p class="booking-summary__note"><?php esc_html_e( 'Precio final con impuestos incluidos.', 'temaestetica' ); ?></p>
						<p class="booking-summary__note"><?php esc_html_e( 'Cancelación con reembolso hasta 24 h antes. Reprogramación hasta 12 h antes.', 'temaestetica' ); ?></p>
					</aside>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section policies-section">
		<div class="container">
			<header class="section-head section-head--split">
				<div>
					<p class="kicker"><?php esc_html_e( 'Antes de reservar', 'temaestetica' ); ?></p>
					<h2><?php esc_html_e( 'Las reglas del cuidado.', 'temaestetica' ); ?></h2>
				</div>
				<div class="section-head__aside">
					<p><?php esc_html_e( 'Reservar un turno también es reservar el tiempo de otra persona. Estas son las condiciones para que todo funcione bien.', 'temaestetica' ); ?></p>
				</div>
			</header>

			<div class="policies-grid">
				<article class="policy-card">
					<h3><?php esc_html_e( 'Cancelación', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['cancel'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Reprogramación', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['reschedule'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Demoras', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['late'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Ausencias', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['noshow'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Reembolsos', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['refund'] ); ?></p>
				</article>
				<article class="policy-card policy-card--accent">
					<h3><?php esc_html_e( 'Pago', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['payment'] ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="section booking-how">
		<div class="container">
			<header class="section-head section-head--split">
				<div>
					<p class="kicker"><?php esc_html_e( 'Cómo funciona', 'temaestetica' ); ?></p>
					<h2><?php esc_html_e( 'Tres pasos, sin vueltas.', 'temaestetica' ); ?></h2>
				</div>
				<div class="section-head__aside">
					<p><?php esc_html_e( 'La agenda muestra solo los horarios que la profesional tiene libres. La retención evita que otra persona tome el mismo lugar mientras confirmás.', 'temaestetica' ); ?></p>
				</div>
			</header>

			<div class="booking-how__grid">
				<?php foreach ( $glowup_steps as $step ) : ?>
					<article class="booking-how__step">
						<span><?php echo esc_html( $step['number'] ); ?></span>
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
