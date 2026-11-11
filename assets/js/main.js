/**
 * GlowUp front-end behaviour.
 *
 * Small, dependency-free enhancements: mobile navigation, sticky header,
 * cart badge fragments, FAQ accordion and a soft scroll reveal.
 */
(function () {
	'use strict';

	var doc = document;
	var body = doc.body;
	var root = doc.documentElement;

	/* ---------------------------------------------------------------
	 * Sticky header
	 * ------------------------------------------------------------- */
	var header = doc.querySelector('[data-site-header]');

	if (header) {
		var setHeaderState = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};

		setHeaderState();
		window.addEventListener('scroll', setHeaderState, { passive: true });
	}

	/* ---------------------------------------------------------------
	 * Mobile navigation
	 * ------------------------------------------------------------- */
	var toggle = doc.querySelector('[data-nav-toggle]');
	var menu = doc.getElementById('mobile-menu');

	if (toggle && menu) {
		var closeButton = menu.querySelector('[data-nav-close]');
		var focusableSelector = 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])';
		var lastFocused = null;

		var getFocusable = function () {
			return Array.prototype.filter.call(menu.querySelectorAll(focusableSelector), function (element) {
				return element.offsetParent !== null;
			});
		};

		var openMenu = function () {
			lastFocused = doc.activeElement;
			menu.hidden = false;
			body.classList.add('nav-open');
			toggle.setAttribute('aria-expanded', 'true');
			window.setTimeout(function () {
				var focusable = getFocusable();
				if (focusable.length) {
					focusable[0].focus();
				}
			}, 30);
		};

		var closeMenu = function (restoreFocus) {
			menu.hidden = true;
			body.classList.remove('nav-open');
			toggle.setAttribute('aria-expanded', 'false');

			if (restoreFocus && lastFocused && typeof lastFocused.focus === 'function') {
				lastFocused.focus();
			}
		};

		toggle.addEventListener('click', function () {
			if (menu.hidden) {
				openMenu();
			} else {
				closeMenu(true);
			}
		});

		if (closeButton) {
			closeButton.addEventListener('click', function () {
				closeMenu(true);
			});
		}

		menu.addEventListener('click', function (event) {
			var link = event.target.closest('a');
			if (link) {
				closeMenu(false);
			}
		});

		doc.addEventListener('keydown', function (event) {
			if (menu.hidden) {
				return;
			}

			if (event.key === 'Escape') {
				closeMenu(true);
				return;
			}

			if (event.key !== 'Tab') {
				return;
			}

			var focusable = getFocusable();
			if (!focusable.length) {
				return;
			}

			var first = focusable[0];
			var last = focusable[focusable.length - 1];

			if (event.shiftKey && doc.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && doc.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});

		window.addEventListener('resize', function () {
			if (window.innerWidth > 920 && !menu.hidden) {
				closeMenu(false);
			}
		});
	}

	/* ---------------------------------------------------------------
	 * WooCommerce cart badge
	 * -------------------------------------------------------------
	 * Classic AJAX adds refresh the fragment server-side. The block cart
	 * updates through the Store API, so listen to its events and sync the
	 * header badge with the current cart count.
	 * ------------------------------------------------------------- */
	var cartBadges = doc.querySelectorAll('[data-cart-count]');

	if (cartBadges.length) {
		var setCartBadge = function (count) {
			count = parseInt(count, 10) || 0;

			cartBadges.forEach(function (badge) {
				badge.textContent = String(count);
				badge.classList.toggle('is-empty', 0 === count);
			});
		};

		var storeCartCount = function () {
			try {
				var store = window.wp && wp.data && wp.data.select('wc/store/cart');
				var cart = store && store.getCartData ? store.getCartData() : null;

				return cart && typeof cart.itemsCount !== 'undefined' ? cart.itemsCount : null;
			} catch (error) {
				return null;
			}
		};

		/* Block cart and checkout mutate the Store API directly. Subscribe to
		 * the cart store so the header badge follows every change. The store
		 * may initialize after this footer script, so retry on window load. */
		var watchCartStore = function () {
			if (!window.wp || !wp.data || !wp.data.subscribe) {
				return false;
			}

			var lastCartCount = storeCartCount();

			if (null !== lastCartCount) {
				setCartBadge(lastCartCount);
			}

			wp.data.subscribe(function () {
				var count = storeCartCount();

				if (null !== count && count !== lastCartCount) {
					lastCartCount = count;
					setCartBadge(count);
				}
			});

			return true;
		};

		if (!watchCartStore()) {
			window.addEventListener('load', watchCartStore);
		}

		/* Classic AJAX flows still fire these events; use the Store API as a
		 * fallback when the cart store is not registered on the page. */
		var refreshCartBadge = function () {
			if (!window.fetch || !window.glowupData || !glowupData.storeApiUrl) {
				return;
			}

			fetch(glowupData.storeApiUrl, {
				credentials: 'same-origin',
				headers: { Accept: 'application/json' }
			})
				.then(function (response) {
					return response.ok ? response.json() : null;
				})
				.then(function (cart) {
					if (cart && typeof cart.items_count !== 'undefined') {
						setCartBadge(cart.items_count);
					}
				})
				.catch(function () {});
		};

		doc.body.addEventListener('wc-blocks_added_to_cart', refreshCartBadge);
		doc.body.addEventListener('wc-blocks_removed_from_cart', refreshCartBadge);
	}

	/* ---------------------------------------------------------------
	 * FAQ accordion: one open answer at a time
	 * ------------------------------------------------------------- */
	var faqItems = doc.querySelectorAll('.faq-list .faq-item');

	if (faqItems.length) {
		faqItems.forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) {
					return;
				}

				faqItems.forEach(function (other) {
					if (other !== item) {
						other.open = false;
					}
				});
			});
		});
	}

	/* ---------------------------------------------------------------
	 * Scroll reveal
	 * ------------------------------------------------------------- */
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!reduceMotion && 'IntersectionObserver' in window) {
		var revealSelectors = [
			'.section-head',
			'.service-group',
			'.pro-card',
			'.shop-category',
			'.testimonial',
			'.process-step',
			'.policy-card',
			'.contact-card',
			'.post-card',
			'.page-hero__inner',
			'.booking-status__step'
		];

		var revealItems = doc.querySelectorAll(revealSelectors.join(','));
		var revealObserver = null;

		try {
			root.classList.add('js-reveal');

			revealObserver = new IntersectionObserver(
				function (entries, observer) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) {
							return;
						}

						entry.target.classList.add('is-revealed');
						observer.unobserve(entry.target);
					});
				},
				{
					rootMargin: '0px 0px -8% 0px',
					threshold: 0.08
				}
			);

			revealItems.forEach(function (item) {
				revealObserver.observe(item);
			});
		} catch (error) {
			root.classList.remove('js-reveal');
		}
	}

	/* ---------------------------------------------------------------
	 * Service marquee: constant speed, seamless loop
	 * -------------------------------------------------------------
	 * The CSS animation is the no-JS fallback. Here we measure one group,
	 * clone it until the strip covers the viewport plus one group, and move
	 * the track with requestAnimationFrame. Resetting the offset by the
	 * exact group width makes the loop invisible at any screen size.
	 * ------------------------------------------------------------- */
	var marquees = doc.querySelectorAll('[data-marquee]');

	if (marquees.length && !reduceMotion) {
		marquees.forEach(function (track) {
			var wrapper = track.parentNode;
			var original = track.querySelector('.service-marquee__group');

			if (!wrapper || !original) {
				return;
			}

			var speed = parseFloat(track.getAttribute('data-marquee-speed')) || 60;
			var groupWidth = 0;
			var offset = 0;
			var lastTime = null;
			var frame = null;

			var getGroups = function () {
				return track.querySelectorAll('.service-marquee__group');
			};

			var removeClones = function () {
				getGroups().forEach(function (group, index) {
					if (index > 0) {
						group.remove();
					}
				});
			};

			var fill = function () {
				removeClones();
				groupWidth = original.getBoundingClientRect().width;

				if (!groupWidth) {
					return;
				}

				var target = wrapper.clientWidth + groupWidth;

				while (track.getBoundingClientRect().width < target) {
					track.appendChild(original.cloneNode(true));
				}
			};

			var tick = function (time) {
				if (null === lastTime) {
					lastTime = time;
				}

				var delta = Math.min(time - lastTime, 100);
				lastTime = time;
				offset = (offset + (speed * delta) / 1000) % groupWidth;
				track.style.transform = 'translate3d(' + (-offset) + 'px, 0, 0)';
				frame = window.requestAnimationFrame(tick);
			};

			var start = function () {
				if (frame || !groupWidth) {
					return;
				}

				lastTime = null;
				frame = window.requestAnimationFrame(tick);
			};

			var stop = function () {
				if (!frame) {
					return;
				}

				window.cancelAnimationFrame(frame);
				frame = null;
			};

			fill();
			track.classList.add('is-marquee-ready');
			start();

			doc.addEventListener('visibilitychange', function () {
				if (doc.hidden) {
					stop();
				} else {
					start();
				}
			});

			if ('IntersectionObserver' in window) {
				new IntersectionObserver(function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							start();
						} else {
							stop();
						}
					});
				}).observe(wrapper);
			}

			var resizeTimer = null;
			window.addEventListener('resize', function () {
				window.clearTimeout(resizeTimer);
				resizeTimer = window.setTimeout(function () {
					fill();
					offset = groupWidth ? offset % groupWidth : 0;
				}, 150);
			});
		});
	}
})();
