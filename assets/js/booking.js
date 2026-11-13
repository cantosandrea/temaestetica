/**
 * GlowUp booking wizard.
 *
 * Dependency-free flow: services → professional → date and time → hold →
 * WooCommerce order. All booking rules stay in the glowup/v1 REST API.
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-booking-wizard]');
	var config = window.glowupBooking;

	if (!root || !config || !window.fetch) {
		return;
	}

	var state = {
		step: 1,
		categories: [],
		services: [],
		service: null,
		professionals: [],
		professional: null,
		date: '',
		slots: [],
		slot: null,
		hold: null,
		order: null,
		appointment: null,
		idempotency: '',
		loading: false,
		holdTimer: null,
		holdDeadline: 0
	};

	var els = {
		alert: root.querySelector('[data-wizard-alert]'),
		services: root.querySelector('[data-services]'),
		servicesLoading: root.querySelector('[data-services-loading]'),
		professionals: root.querySelector('[data-professionals]'),
		professionalsLoading: root.querySelector('[data-professionals-loading]'),
		date: root.querySelector('[data-date-input]'),
		dateHint: root.querySelector('[data-date-hint]'),
		slots: root.querySelector('[data-slots]'),
		review: root.querySelector('[data-review]'),
		reviewEvaluation: root.querySelector('[data-review-evaluation]'),
		terms: root.querySelector('[data-terms]'),
		health: root.querySelector('[data-health]'),
		holdButton: root.querySelector('[data-create-hold]'),
		holdCountdown: root.querySelector('[data-hold-countdown]'),
		holdSummary: root.querySelector('[data-hold-summary]'),
		orderButton: root.querySelector('[data-create-order]'),
		orderNumber: root.querySelector('[data-order-number]'),
		orderStatus: root.querySelector('[data-order-status]'),
		orderHoldNote: root.querySelector('[data-order-hold-note]'),
		orderSummary: root.querySelector('[data-order-summary]'),
		orderView: root.querySelector('[data-order-view]'),
		orderPay: root.querySelector('[data-order-pay]'),
		summaryService: root.querySelector('[data-summary-service]'),
		summaryProfessional: root.querySelector('[data-summary-professional]'),
		summaryWhen: root.querySelector('[data-summary-when]'),
		summaryDuration: root.querySelector('[data-summary-duration]'),
		summaryTotalLabel: root.querySelector('[data-summary-total-label]'),
		summaryTotal: root.querySelector('[data-summary-total]')
	};

	init();

	/**
	 * Boot the wizard.
	 */
	function init() {
		if (els.date) {
			els.date.min = config.today || '';
			els.date.max = config.maxDate || '';
		}

		if (els.dateHint && config.maxDate) {
			els.dateHint.textContent = 'Reservas disponibles hasta el ' + formatDate(config.maxDate) + '.';
		}

		bindEvents();
		updateSummary();
		renderSlotsMessage(config.i18n.chooseDate);
		loadServices();
	}

	/**
	 * Wire DOM events.
	 */
	function bindEvents() {
		root.addEventListener('click', function (event) {
			var next = event.target.closest('[data-next]');
			var back = event.target.closest('[data-back]');

			if (next) {
				handleNext(Number(next.getAttribute('data-next')));
				return;
			}

			if (back) {
				goTo(Number(back.getAttribute('data-back')));
			}
		});

		if (els.services) {
			els.services.addEventListener('change', function (event) {
				if (!event.target.matches('input[name="glowup-service"]')) {
					return;
				}

				var service = findById(state.services, Number(event.target.value));
				var changed = !state.service || !service || state.service.id !== service.id;

				state.service = service || null;

				if (changed) {
					resetAfterService();
				}

				updateSummary();
				updateNextButton(1);
				clearAlert();
			});
		}

		if (els.professionals) {
			els.professionals.addEventListener('change', function (event) {
				if (!event.target.matches('input[name="glowup-professional"]')) {
					return;
				}

				var professional = findById(state.professionals, Number(event.target.value));
				var changed = !state.professional || !professional || state.professional.id !== professional.id;

				state.professional = professional || null;

				if (changed) {
					resetAfterProfessional();
				}

				updateSummary();
				updateNextButton(2);
				clearAlert();
			});
		}

		if (els.date) {
			els.date.addEventListener('change', function () {
				state.date = els.date.value;
				state.slots = [];
				state.slot = null;
				resetHold();
				updateSummary();
				updateNextButton(3);

				if (!state.date) {
					renderSlotsMessage(config.i18n.chooseDate);
					return;
				}

				loadAvailability();
			});
		}

		if (els.slots) {
			els.slots.addEventListener('click', function (event) {
				var button = event.target.closest('.slot');

				if (!button) {
					return;
				}

				var slot = findSlot(button.getAttribute('data-time'));

				if (slot) {
					selectSlot(slot);
				}
			});
		}

		if (els.terms) {
			els.terms.addEventListener('change', updateHoldButton);
		}

		if (els.health) {
			els.health.addEventListener('change', updateHoldButton);
		}

		if (els.holdButton) {
			els.holdButton.addEventListener('click', createHold);
		}

		if (els.orderButton) {
			els.orderButton.addEventListener('click', createOrder);
		}

		if (root.querySelector('[data-restart]')) {
			root.querySelector('[data-restart]').addEventListener('click', restart);
		}
	}

	/**
	 * Move to a wizard step.
	 *
	 * @param {number} step Step number.
	 */
	function goTo(step) {
		var activeIndicator = Math.min(step, 4);

		state.step = step;

		root.querySelectorAll('[data-step-panel]').forEach(function (panel) {
			panel.hidden = Number(panel.getAttribute('data-step-panel')) !== step;
		});

		root.querySelectorAll('[data-step-indicator]').forEach(function (indicator) {
			var number = Number(indicator.getAttribute('data-step-indicator'));

			indicator.classList.toggle('is-current', number === activeIndicator);
			indicator.classList.toggle('is-complete', number < activeIndicator);

			if (number === activeIndicator) {
				indicator.setAttribute('aria-current', 'step');
			} else {
				indicator.removeAttribute('aria-current');
			}
		});

		var current = panel(step);

		if (current) {
			var heading = current.querySelector('h2');

			if (heading) {
				heading.focus({ preventScroll: true });
			}

			if (current.scrollIntoView) {
				current.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		}
	}

	/**
	 * Continue from the current step.
	 *
	 * @param {number} target Target step.
	 */
	function handleNext(target) {
		clearAlert();

		if (target === 2) {
			if (!state.service) {
				setAlert(config.i18n.genericError, 'error');
				return;
			}

			goTo(2);
			loadProfessionals();
			return;
		}

		if (target === 3) {
			if (!state.professional) {
				setAlert(config.i18n.genericError, 'error');
				return;
			}

			goTo(3);

			if (state.date) {
				loadAvailability();
			} else {
				renderSlotsMessage(config.i18n.chooseDate);
			}

			return;
		}

		if (target === 4) {
			if (!state.slot) {
				setAlert(config.i18n.genericError, 'error');
				return;
			}

			renderReview();
			goTo(4);
		}
	}

	/**
	 * Load services from the REST API.
	 */
	function loadServices() {
		clearAlert();

		if (els.servicesLoading) {
			els.servicesLoading.hidden = false;
		}

		clear(els.services);

		api('services')
			.then(function (data) {
				state.categories = data.categories || [];
				state.services = [];

				state.categories.forEach(function (category) {
					(category.services || []).forEach(function (service) {
						state.services.push(service);
					});
				});

				renderServices();

				if (els.servicesLoading) {
					els.servicesLoading.hidden = true;
				}
			})
			.catch(function (error) {
				if (els.servicesLoading) {
					els.servicesLoading.hidden = true;
				}

				handleError(error, 'services');
			});
	}

	/**
	 * Render service categories and radio cards.
	 */
	function renderServices() {
		clear(els.services);

		if (!state.services.length) {
			renderEmpty(els.services, config.i18n.servicesError);
			return;
		}

		state.categories.forEach(function (category) {
			var fieldset = document.createElement('fieldset');
			fieldset.className = 'wizard-service-group';

			var legend = document.createElement('legend');
			legend.className = 'wizard-service-group__legend';

			var title = document.createElement('span');
			title.className = 'wizard-service-group__title';
			title.textContent = category.title;

			var description = document.createElement('span');
			description.className = 'wizard-service-group__description';
			description.textContent = category.description || '';

			legend.appendChild(title);
			legend.appendChild(description);
			fieldset.appendChild(legend);

			var grid = document.createElement('div');
			grid.className = 'choice-grid';

			(category.services || []).forEach(function (service) {
				grid.appendChild(renderServiceCard(service));
			});

			fieldset.appendChild(grid);
			els.services.appendChild(fieldset);
		});
	}

	/**
	 * Build one service radio card.
	 *
	 * @param {object} service Service data.
	 * @return {HTMLElement}
	 */
	function renderServiceCard(service) {
		var label = document.createElement('label');
		label.className = 'choice-card';

		var input = document.createElement('input');
		input.type = 'radio';
		input.name = 'glowup-service';
		input.value = String(service.id);
		input.checked = !!state.service && state.service.id === service.id;

		var check = document.createElement('span');
		check.className = 'choice-card__check';
		check.setAttribute('aria-hidden', 'true');

		var body = document.createElement('span');
		body.className = 'choice-card__body';

		var top = document.createElement('span');
		top.className = 'choice-card__top';

		var name = document.createElement('strong');
		name.className = 'choice-card__title';
		name.textContent = service.name;

		var price = document.createElement('span');
		price.className = 'choice-card__price';
		price.textContent = formatPrice(service.price);

		top.appendChild(name);
		top.appendChild(price);

		var meta = document.createElement('span');
		meta.className = 'choice-card__meta';
		meta.textContent = formatDuration(service.duration) + ' · ' + service.category;

		var description = document.createElement('span');
		description.className = 'choice-card__text';
		description.textContent = service.description || '';

		body.appendChild(top);
		body.appendChild(meta);
		body.appendChild(description);

		if (service.requires_evaluation) {
			var badge = document.createElement('span');
			badge.className = 'choice-card__badge';
			badge.textContent = 'Requiere evaluación previa';
			body.appendChild(badge);
		}

		label.appendChild(input);
		label.appendChild(check);
		label.appendChild(body);

		return label;
	}

	/**
	 * Load professionals for the selected service.
	 */
	function loadProfessionals() {
		if (!state.service) {
			return;
		}

		clearAlert();

		if (els.professionalsLoading) {
			els.professionalsLoading.hidden = false;
		}

		clear(els.professionals);

		api('professionals?service_id=' + encodeURIComponent(state.service.id))
			.then(function (data) {
				state.professionals = data.professionals || [];
				renderProfessionals();

				if (els.professionalsLoading) {
					els.professionalsLoading.hidden = true;
				}
			})
			.catch(function (error) {
				if (els.professionalsLoading) {
					els.professionalsLoading.hidden = true;
				}

				handleError(error, 'professionals');
			});
	}

	/**
	 * Render professional radio cards.
	 */
	function renderProfessionals() {
		clear(els.professionals);

		if (!state.professionals.length) {
			renderEmpty(els.professionals, 'No hay profesionales disponibles para este servicio.');
			return;
		}

		state.professionals.forEach(function (professional) {
			var label = document.createElement('label');
			label.className = 'choice-card choice-card--professional';

			var input = document.createElement('input');
			input.type = 'radio';
			input.name = 'glowup-professional';
			input.value = String(professional.id);
			input.checked = !!state.professional && state.professional.id === professional.id;

			var check = document.createElement('span');
			check.className = 'choice-card__check';
			check.setAttribute('aria-hidden', 'true');

			var avatar = document.createElement('span');
			avatar.className = 'choice-card__avatar';
			avatar.textContent = professional.name.charAt(0).toUpperCase();

			var body = document.createElement('span');
			body.className = 'choice-card__body';

			var name = document.createElement('strong');
			name.className = 'choice-card__title';
			name.textContent = professional.name;

			var role = document.createElement('span');
			role.className = 'choice-card__meta';
			role.textContent = professional.role || '';

			var bio = document.createElement('span');
			bio.className = 'choice-card__text';
			bio.textContent = professional.bio || '';

			body.appendChild(name);
			body.appendChild(role);
			body.appendChild(bio);

			label.appendChild(input);
			label.appendChild(check);
			label.appendChild(avatar);
			label.appendChild(body);

			els.professionals.appendChild(label);
		});
	}

	/**
	 * Query availability for the current date.
	 */
	function loadAvailability() {
		if (!state.service || !state.professional || !state.date) {
			renderSlotsMessage(config.i18n.chooseDate);
			return;
		}

		clearAlert();
		state.slots = [];
		state.slot = null;
		renderSlotsMessage(config.i18n.loadingSlots, true);
		updateNextButton(3);
		updateSummary();

		var path = 'availability?service_id=' + encodeURIComponent(state.service.id)
			+ '&professional_id=' + encodeURIComponent(state.professional.id)
			+ '&date=' + encodeURIComponent(state.date);

		api(path)
			.then(function (data) {
				state.slots = data.slots || [];
				renderSlots();
			})
			.catch(function (error) {
				state.slots = [];
				renderSlotsMessage(error.message || config.i18n.slotsError);

				if (error.status === 401 || error.status === 403) {
					handleError(error, 'slots');
				}
			});
	}

	/**
	 * Render available slots grouped by morning and afternoon.
	 */
	function renderSlots() {
		clear(els.slots);

		if (!state.slots.length) {
			renderSlotsMessage(config.i18n.noSlots);
			updateNextButton(3);
			return;
		}

		var groups = [
			{ label: config.i18n.morning, items: [] },
			{ label: config.i18n.afternoon, items: [] }
		];

		state.slots.forEach(function (slot) {
			var hour = parseInt(slot.time.split(':')[0], 10);
			groups[hour < 13 ? 0 : 1].items.push(slot);
		});

		groups.forEach(function (group) {
			if (!group.items.length) {
				return;
			}

			var wrapper = document.createElement('div');
			wrapper.className = 'slot-group';

			var title = document.createElement('h3');
			title.textContent = group.label;

			var grid = document.createElement('div');
			grid.className = 'slot-grid';

			group.items.forEach(function (slot) {
				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'slot';
				button.setAttribute('data-time', slot.time);
				button.setAttribute('aria-pressed', state.slot && state.slot.time === slot.time ? 'true' : 'false');
				button.textContent = slot.time;

				grid.appendChild(button);
			});

			wrapper.appendChild(title);
			wrapper.appendChild(grid);
			els.slots.appendChild(wrapper);
		});
	}

	/**
	 * Select an available slot.
	 *
	 * @param {object} slot Slot data.
	 */
	function selectSlot(slot) {
		state.slot = slot;
		state.idempotency = '';
		resetHold();
		clearAlert();

		els.slots.querySelectorAll('.slot').forEach(function (button) {
			button.setAttribute('aria-pressed', button.getAttribute('data-time') === slot.time ? 'true' : 'false');
		});

		updateSummary();
		updateNextButton(3);
	}

	/**
	 * Render the pre-hold review.
	 */
	function renderReview() {
		if (!state.service || !state.professional || !state.slot) {
			return;
		}

		renderRows(els.review, [
			['Servicio', state.service.name],
			['Profesional', state.professional.name],
			['Fecha', formatDate(state.date)],
			['Hora', state.slot.time],
			['Duración', formatDuration(state.service.duration)],
			[paymentLabel(), formatPrice(state.service.price)]
		]);

		if (els.reviewEvaluation) {
			els.reviewEvaluation.hidden = !state.service.requires_evaluation;
		}

		updateHoldButton();
	}

	/**
	 * Create the 15-minute hold.
	 */
	function createHold() {
		if (state.loading || !state.service || !state.professional || !state.slot) {
			return;
		}

		if (!els.terms.checked || !els.health.checked) {
			setAlert(config.i18n.consentRequired, 'error');
			return;
		}

		state.loading = true;
		setBusy(els.holdButton, true);
		clearAlert();

		state.idempotency = state.idempotency || makeIdempotencyKey();

		api('holds', {
			method: 'POST',
			body: {
				service_id: state.service.id,
				professional_id: state.professional.id,
				date: state.date,
				time: state.slot.time,
				health_consent: true,
				idempotency_key: state.idempotency
			}
		})
			.then(function (data) {
				state.hold = data;
				state.appointment = data.appointment;
				state.loading = false;
				setBusy(els.holdButton, false);

				renderHoldSummary();
				updateSummary();
				goTo(5);
				startHoldTimer(Number(data.hold_expires_in || 0));
			})
			.catch(function (error) {
				state.loading = false;
				setBusy(els.holdButton, false);
				handleError(error, 'hold');

				if (error.code === 'glowup_slot_unavailable' || error.code === 'glowup_hold_expired') {
					state.idempotency = '';
				}
			});
	}

	/**
	 * Render the hold summary.
	 */
	function renderHoldSummary() {
		renderRows(els.holdSummary, [
			['Código', state.appointment ? state.appointment.code : '—'],
			['Servicio', state.service.name],
			['Profesional', state.professional.name],
			['Fecha', formatDate(state.date)],
			['Hora', state.slot.time],
			['Duración', formatDuration(state.service.duration)],
			[paymentLabel(), formatPrice(amountDue())]
		]);
	}

	/**
	 * Create the WooCommerce order from the hold.
	 */
	function createOrder() {
		if (state.loading || !state.hold || !state.appointment) {
			return;
		}

		if (state.holdDeadline && Date.now() >= state.holdDeadline) {
			holdExpired();
			return;
		}

		state.loading = true;
		setBusy(els.orderButton, true);
		clearAlert();

		api('orders', {
			method: 'POST',
			body: {
				appointment_id: state.appointment.id,
				terms_consent: true
			}
		})
			.then(function (data) {
				state.order = data;
				state.loading = false;
				setBusy(els.orderButton, false);
				stopHoldTimer();
				renderSuccess(data);
				updateSummary();
				goTo(6);
			})
			.catch(function (error) {
				state.loading = false;
				setBusy(els.orderButton, false);
				handleError(error, 'order');

				if (error.code === 'glowup_hold_expired' || error.code === 'glowup_appointment_not_pending') {
					stopHoldTimer();
					text(els.holdCountdown, '00:00');
					disableOrderButton();
				}
			});
	}

	/**
	 * Render the final order summary.
	 *
	 * @param {object} data Order response.
	 */
	function renderSuccess(data) {
		text(els.orderNumber, data.order_number || '—');
		text(els.orderStatus, data.status_label ? 'Estado: ' + data.status_label : '');

		if (els.orderHoldNote) {
			if (Number(data.hold_expires_in) > 0) {
				var minutes = Math.ceil(Number(data.hold_expires_in) / 60);

				els.orderHoldNote.textContent = config.paymentAvailable
					? 'El horario queda retenido por ' + minutes + ' minutos. Si no se acredita el pago, se libera automáticamente.'
					: 'El horario queda retenido por ' + minutes + ' minutos. El pago online todavía no está habilitado, así que no se realiza ningún cobro.';
			} else {
				els.orderHoldNote.textContent = 'La retención del horario ya venció.';
			}
		}

		if (els.orderPay) {
			if (config.paymentAvailable && data.payment_url) {
				els.orderPay.href = data.payment_url;
				els.orderPay.hidden = false;
			} else {
				els.orderPay.hidden = true;
				els.orderPay.removeAttribute('href');
			}
		}

		renderRows(els.orderSummary, [
			['Código', state.appointment ? state.appointment.code : '—'],
			['Servicio', state.service.name],
			['Profesional', state.professional.name],
			['Fecha y hora', formatDate(state.date) + ' · ' + state.slot.time],
			['Duración', formatDuration(state.service.duration)],
			['Total', formatPrice(data.total)]
		]);

		if (els.orderView) {
			els.orderView.href = data.view_url || config.accountUrl;
		}
	}

	/**
	 * Start the hold countdown.
	 *
	 * @param {number} seconds Seconds remaining.
	 */
	function startHoldTimer(seconds) {
		stopHoldTimer();

		state.holdDeadline = Date.now() + Math.max(0, seconds) * 1000;

		if (seconds <= 0) {
			holdExpired();
			return;
		}

		updateHoldCountdown();

		state.holdTimer = window.setInterval(function () {
			if (Date.now() >= state.holdDeadline) {
				stopHoldTimer();
				holdExpired();
				return;
			}

			updateHoldCountdown();
		}, 1000);
	}

	/**
	 * Paint the countdown.
	 */
	function updateHoldCountdown() {
		var remaining = Math.max(0, Math.ceil((state.holdDeadline - Date.now()) / 1000));
		var minutes = Math.floor(remaining / 60);
		var seconds = remaining % 60;

		text(
			els.holdCountdown,
			(minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds
		);
	}

	/**
	 * Stop the hold countdown.
	 */
	function stopHoldTimer() {
		if (state.holdTimer) {
			window.clearInterval(state.holdTimer);
			state.holdTimer = null;
		}
	}

	/**
	 * Handle an expired hold.
	 */
	function holdExpired() {
		stopHoldTimer();
		text(els.holdCountdown, '00:00');
		disableOrderButton();
		setAlert(config.i18n.holdExpired, 'error', {
			label: config.i18n.restart,
			handler: restart
		});
	}

	/**
	 * Disable the order button.
	 */
	function disableOrderButton() {
		if (els.orderButton) {
			els.orderButton.disabled = true;
			els.orderButton.setAttribute('aria-disabled', 'true');
		}
	}

	/**
	 * Go back to date selection after a hold.
	 */
	function restart() {
		stopHoldTimer();
		state.hold = null;
		state.order = null;
		state.appointment = null;
		state.slot = null;
		state.idempotency = '';

		if (els.orderButton) {
			els.orderButton.disabled = false;
			els.orderButton.removeAttribute('aria-disabled');
		}

		updateSummary();
		clearAlert();
		goTo(3);

		if (state.date) {
			loadAvailability();
		} else {
			renderSlotsMessage(config.i18n.chooseDate);
		}
	}

	/**
	 * Clear selections after a service change.
	 */
	function resetAfterService() {
		state.professional = null;
		state.professionals = [];
		state.date = '';
		state.slots = [];
		state.slot = null;
		resetHold();

		if (els.date) {
			els.date.value = '';
		}

		if (els.professionals) {
			clear(els.professionals);
		}

		renderSlotsMessage(config.i18n.chooseDate);
		updateNextButton(2);
		updateNextButton(3);
	}

	/**
	 * Clear selections after a professional change.
	 */
	function resetAfterProfessional() {
		state.date = '';
		state.slots = [];
		state.slot = null;
		resetHold();

		if (els.date) {
			els.date.value = '';
		}

		renderSlotsMessage(config.i18n.chooseDate);
		updateNextButton(3);
	}

	/**
	 * Clear hold and order state.
	 */
	function resetHold() {
		stopHoldTimer();
		state.hold = null;
		state.order = null;
		state.appointment = null;
		state.idempotency = '';

		if (els.orderButton) {
			els.orderButton.disabled = false;
			els.orderButton.removeAttribute('aria-disabled');
		}
	}

	/**
	 * Update the wizard sidebar.
	 */
	function updateSummary() {
		text(els.summaryService, state.service ? state.service.name : '—');
		text(els.summaryProfessional, state.professional ? state.professional.name : '—');

		if (state.date && state.slot) {
			text(els.summaryWhen, formatDate(state.date) + ' · ' + state.slot.time);
		} else if (state.date) {
			text(els.summaryWhen, formatDate(state.date));
		} else {
			text(els.summaryWhen, '—');
		}

		text(els.summaryDuration, state.service ? formatDuration(state.service.duration) : '—');
		text(els.summaryTotalLabel, state.service ? paymentLabel() : 'Total');

		if (!state.service) {
			text(els.summaryTotal, '—');
			return;
		}

		text(els.summaryTotal, formatPrice(amountDue()));
	}

	/**
	 * Amount to show in summaries.
	 *
	 * @return {number}
	 */
	function amountDue() {
		if (state.order && typeof state.order.total !== 'undefined') {
			return Number(state.order.total);
		}

		if (state.hold && typeof state.hold.amount_due !== 'undefined') {
			return Number(state.hold.amount_due);
		}

		return state.service ? Number(state.service.price) : 0;
	}

	/**
	 * Label for the amount due.
	 *
	 * @return {string}
	 */
	function paymentLabel() {
		return config.paymentMode === 'deposit' ? config.i18n.depositLabel : config.i18n.totalLabel;
	}

	/**
	 * Update a next button.
	 *
	 * @param {number} step Step number.
	 */
	function updateNextButton(step) {
		var container = panel(step);

		if (!container) {
			return;
		}

		var button = container.querySelector('[data-next]');

		if (!button) {
			return;
		}

		if (step === 1) {
			button.disabled = !state.service;
		} else if (step === 2) {
			button.disabled = !state.professional;
		} else if (step === 3) {
			button.disabled = !state.slot;
		}
	}

	/**
	 * Enable the hold button only with both consents.
	 */
	function updateHoldButton() {
		if (!els.holdButton) {
			return;
		}

		var accepted = els.terms && els.terms.checked && els.health && els.health.checked;
		els.holdButton.disabled = !accepted || state.loading;
	}

	/**
	 * Render summary rows in a definition list.
	 *
	 * @param {HTMLElement} container Container.
	 * @param {Array}       rows      Rows of [label, value].
	 */
	function renderRows(container, rows) {
		if (!container) {
			return;
		}

		clear(container);

		rows.forEach(function (row) {
			var wrapper = document.createElement('div');
			wrapper.className = 'summary-row';

			var term = document.createElement('dt');
			term.textContent = row[0];

			var detail = document.createElement('dd');
			detail.textContent = row[1];

			wrapper.appendChild(term);
			wrapper.appendChild(detail);
			container.appendChild(wrapper);
		});
	}

	/**
	 * Render an empty state in a container.
	 *
	 * @param {HTMLElement} container Container.
	 * @param {string}      message   Message.
	 */
	function renderEmpty(container, message) {
		if (!container) {
			return;
		}

		clear(container);

		var empty = document.createElement('p');
		empty.className = 'wizard-empty';
		empty.textContent = message;
		container.appendChild(empty);
	}

	/**
	 * Render the slots placeholder.
	 *
	 * @param {string}  message Message.
	 * @param {boolean} loading Whether a spinner is shown.
	 */
	function renderSlotsMessage(message, loading) {
		if (!els.slots) {
			return;
		}

		clear(els.slots);

		var empty = document.createElement('p');
		empty.className = 'wizard-empty' + (loading ? ' is-loading' : '');

		if (loading) {
			var spinner = document.createElement('span');
			spinner.className = 'wizard-spinner';
			spinner.setAttribute('aria-hidden', 'true');
			empty.appendChild(spinner);
		}

		var label = document.createElement('span');
		label.textContent = message;
		empty.appendChild(label);

		els.slots.appendChild(empty);
	}

	/**
	 * Display an alert.
	 *
	 * @param {string} message Message.
	 * @param {string} type    Error, info or success.
	 * @param {object} action  Optional action.
	 */
	function setAlert(message, type, action) {
		if (!els.alert) {
			return;
		}

		clear(els.alert);
		els.alert.hidden = false;
		els.alert.className = 'booking-alert booking-alert--' + (type || 'error');

		var paragraph = document.createElement('p');
		paragraph.textContent = message;
		els.alert.appendChild(paragraph);

		if (action && action.label) {
			var link;

			if (action.url) {
				link = document.createElement('a');
				link.href = action.url;
			} else {
				link = document.createElement('button');
				link.type = 'button';

				if (action.handler) {
					link.addEventListener('click', action.handler);
				}
			}

			link.className = 'text-link';
			link.textContent = action.label;
			els.alert.appendChild(link);
		}

		els.alert.focus();
	}

	/**
	 * Clear the alert.
	 */
	function clearAlert() {
		if (!els.alert) {
			return;
		}

		els.alert.hidden = true;
		els.alert.className = 'booking-alert';
		clear(els.alert);
	}

	/**
	 * Central error handling.
	 *
	 * @param {Error}  error   Error.
	 * @param {string} context Context.
	 */
	function handleError(error, context) {
		if (
			error
			&& (
				error.status === 401
				|| error.status === 403
				|| error.code === 'glowup_auth_required'
				|| error.code === 'rest_cookie_invalid_nonce'
			)
		) {
			setAlert(config.i18n.sessionExpired, 'error', {
				label: config.i18n.login,
				url: config.loginUrl
			});
			return;
		}

		if (error && error.code === 'glowup_slot_unavailable') {
			setAlert(config.i18n.slotTaken, 'error', {
				label: config.i18n.restart,
				handler: restart
			});
			return;
		}

		if (error && (error.code === 'glowup_hold_expired' || error.code === 'glowup_appointment_not_pending')) {
			setAlert(config.i18n.holdExpired, 'error', {
				label: config.i18n.restart,
				handler: restart
			});
			return;
		}

		if (error && error.code === 'glowup_terms_required') {
			setAlert(config.i18n.consentRequired, 'error');
			return;
		}

		var retry = null;

		if (context === 'services') {
			retry = { label: config.i18n.retry, handler: loadServices };
		} else if (context === 'professionals') {
			retry = { label: config.i18n.retry, handler: loadProfessionals };
		} else if (context === 'slots') {
			retry = { label: config.i18n.retry, handler: loadAvailability };
		}

		setAlert(error && error.message ? error.message : config.i18n.genericError, 'error', retry);
	}

	/**
	 * Mark a button as busy.
	 *
	 * @param {HTMLElement} button Button.
	 * @param {boolean}     busy   Busy state.
	 */
	function setBusy(button, busy) {
		if (!button) {
			return;
		}

		button.classList.toggle('is-busy', busy);
		button.setAttribute('aria-busy', busy ? 'true' : 'false');

		button.disabled = !!busy;
	}

	/**
	 * REST request helper.
	 *
	 * @param {string} path    Endpoint path.
	 * @param {object} options Fetch options.
	 * @return {Promise}
	 */
	function api(path, options) {
		options = options || {};

		var url = config.restUrl.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
		var headers = { 'X-WP-Nonce': config.nonce };
		var request = {
			method: options.method || 'GET',
			credentials: 'same-origin',
			headers: headers
		};

		if (options.body) {
			headers['Content-Type'] = 'application/json';
			request.body = JSON.stringify(options.body);
		}

		return fetch(url, request).then(function (response) {
			return response.json().catch(function () {
				return null;
			}).then(function (body) {
				if (!response.ok) {
					var error = new Error(body && body.message ? body.message : config.i18n.genericError);
					error.code = body && body.code ? body.code : '';
					error.status = response.status;
					error.data = body && body.data ? body.data : null;
					throw error;
				}

				return body;
			});
		});
	}

	/**
	 * Find an item by ID.
	 *
	 * @param {Array}  items Items.
	 * @param {number} id    ID.
	 * @return {object|null}
	 */
	function findById(items, id) {
		for (var i = 0; i < items.length; i++) {
			if (Number(items[i].id) === Number(id)) {
				return items[i];
			}
		}

		return null;
	}

	/**
	 * Find a slot by time.
	 *
	 * @param {string} time Time.
	 * @return {object|null}
	 */
	function findSlot(time) {
		for (var i = 0; i < state.slots.length; i++) {
			if (state.slots[i].time === time) {
				return state.slots[i];
			}
		}

		return null;
	}

	/**
	 * Get a wizard panel.
	 *
	 * @param {number} step Step.
	 * @return {HTMLElement|null}
	 */
	function panel(step) {
		return root.querySelector('[data-step-panel="' + step + '"]');
	}

	/**
	 * Set text content safely.
	 *
	 * @param {HTMLElement} element Element.
	 * @param {string}      value   Text.
	 */
	function text(element, value) {
		if (element) {
			element.textContent = value;
		}
	}

	/**
	 * Remove all children.
	 *
	 * @param {HTMLElement} element Element.
	 */
	function clear(element) {
		if (!element) {
			return;
		}

		while (element.firstChild) {
			element.removeChild(element.firstChild);
		}
	}

	/**
	 * Format a price in ARS.
	 *
	 * @param {number} value Amount.
	 * @return {string}
	 */
	function formatPrice(value) {
		var amount = Number(value);

		if (!isFinite(amount)) {
			return '—';
		}

		var formatted = new Intl.NumberFormat('es-AR', {
			minimumFractionDigits: 0,
			maximumFractionDigits: 0
		}).format(amount);

		return (config.currency || '$') + ' ' + formatted;
	}

	/**
	 * Format a duration.
	 *
	 * @param {number} minutes Minutes.
	 * @return {string}
	 */
	function formatDuration(minutes) {
		minutes = Number(minutes) || 0;

		if (!minutes) {
			return '—';
		}

		var hours = Math.floor(minutes / 60);
		var rest = minutes % 60;

		if (hours && rest) {
			return hours + ' h ' + (rest < 10 ? '0' : '') + rest + ' min';
		}

		if (hours) {
			return hours + ' h';
		}

		return rest + ' min';
	}

	/**
	 * Format a Y-m-d date in Spanish.
	 *
	 * @param {string} value Date.
	 * @return {string}
	 */
	function formatDate(value) {
		if (!value) {
			return '—';
		}

		var parts = String(value).split('-');

		if (parts.length !== 3) {
			return value;
		}

		var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]), 12, 0, 0);

		try {
			var formatted = new Intl.DateTimeFormat('es-AR', {
				weekday: 'long',
				day: 'numeric',
				month: 'long'
			}).format(date);

			return formatted.charAt(0).toUpperCase() + formatted.slice(1);
		} catch (error) {
			return value;
		}
	}

	/**
	 * Build an idempotency key accepted by the API.
	 *
	 * @return {string}
	 */
	function makeIdempotencyKey() {
		var key;

		if (window.crypto && typeof window.crypto.randomUUID === 'function') {
			key = 'bk_' + window.crypto.randomUUID();
		} else {
			key = 'bk_' + Date.now().toString(36) + Math.random().toString(36).slice(2);
		}

		return key.replace(/[^A-Za-z0-9_-]/g, '').slice(0, 64);
	}
})();
