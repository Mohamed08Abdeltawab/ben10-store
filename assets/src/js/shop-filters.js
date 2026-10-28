/**
 * Advanced Shop Product Filtering & Mobile Drawer Controller
 *
 * Manages the slide-over mobile filter drawer, smooth transitions,
 * backdrop dismissals, accordion section toggling, and price range validation.
 *
 * @package Ashrafco
 * @since 1.0.15
 */

document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	// =========================================================================
	// 1. Mobile Filter Drawer Elements & Handlers
	// =========================================================================
	var openBtn  = document.getElementById('test-theme-mobile-filter-trigger');
	var drawer   = document.getElementById('test-theme-filters-drawer');
	var backdrop = document.getElementById('test-theme-filters-backdrop');
	var sheet    = document.getElementById('test-theme-filters-sheet');
	var closeBtn = document.getElementById('test-theme-filters-close');

	var isDrawerOpen = false;

	function openFiltersDrawer() {
		if (!drawer) return;
		isDrawerOpen = true;
		drawer.classList.remove('invisible', 'pointer-events-none');
		drawer.classList.add('opacity-100');
		drawer.setAttribute('aria-hidden', 'false');

		if (openBtn) {
			openBtn.setAttribute('aria-expanded', 'true');
		}

		requestAnimationFrame(function () {
			if (backdrop) {
				backdrop.classList.remove('opacity-0');
				backdrop.classList.add('opacity-100');
			}
			if (sheet) {
				sheet.classList.remove('translate-x-full');
				sheet.classList.add('translate-x-0');
			}
		});

		document.body.style.overflow = 'hidden';
	}

	function closeFiltersDrawer() {
		if (!drawer) return;
		isDrawerOpen = false;

		if (openBtn) {
			openBtn.setAttribute('aria-expanded', 'false');
		}

		if (backdrop) {
			backdrop.classList.remove('opacity-100');
			backdrop.classList.add('opacity-0');
		}
		if (sheet) {
			sheet.classList.remove('translate-x-0');
			sheet.classList.add('translate-x-full');
		}

		document.body.style.overflow = '';

		setTimeout(function () {
			if (!isDrawerOpen) {
				drawer.classList.add('invisible', 'pointer-events-none');
				drawer.classList.remove('opacity-100');
				drawer.setAttribute('aria-hidden', 'true');
			}
		}, 300);
	}

	if (openBtn) {
		openBtn.addEventListener('click', function (e) {
			e.preventDefault();
			if (isDrawerOpen) {
				closeFiltersDrawer();
			} else {
				openFiltersDrawer();
			}
		});
	}

	if (closeBtn) {
		closeBtn.addEventListener('click', function (e) {
			e.preventDefault();
			closeFiltersDrawer();
		});
	}

	if (backdrop) {
		backdrop.addEventListener('click', function () {
			closeFiltersDrawer();
		});
	}

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && isDrawerOpen) {
			closeFiltersDrawer();
		}
	});

	// =========================================================================
	// 2. Accordion Collapsible Filter Sections
	// =========================================================================
	var accordionToggles = document.querySelectorAll('.filter-section-toggle');
	accordionToggles.forEach(function (toggle) {
		toggle.addEventListener('click', function (e) {
			e.preventDefault();
			var section = toggle.closest('.filter-section');
			if (!section) return;

			var content = section.querySelector('.filter-section-content');
			var chevron = toggle.querySelector('.filter-section-chevron');

			if (!content) return;

			var isExpanded = toggle.getAttribute('aria-expanded') !== 'false';

			if (isExpanded) {
				// Collapse
				content.classList.add('hidden');
				toggle.setAttribute('aria-expanded', 'false');
				if (chevron) {
					chevron.classList.add('rotate-180');
				}
			} else {
				// Expand
				content.classList.remove('hidden');
				toggle.setAttribute('aria-expanded', 'true');
				if (chevron) {
					chevron.classList.remove('rotate-180');
				}
			}
		});
	});

	// =========================================================================
	// 3. Price Range Combo Box Validation & Synchronization
	// =========================================================================
	var forms = document.querySelectorAll('.test-theme-filter-form');

	forms.forEach(function (form) {
		var minSelect = form.querySelector('select[name="min_price"]');
		var maxSelect = form.querySelector('select[name="max_price"]');
		var errorNotice = form.querySelector('.price-range-error');

		if (!minSelect || !maxSelect) return;

		function syncPriceOptions() {
			var minVal = minSelect.value ? parseFloat(minSelect.value) : null;
			var maxVal = maxSelect.value ? parseFloat(maxSelect.value) : null;

			// Restrict max options based on minVal
			var maxOptions = maxSelect.querySelectorAll('option');
			maxOptions.forEach(function (opt) {
				if (!opt.value) return; // 'بدون حد أقصى'
				var optVal = parseFloat(opt.value);
				if (minVal !== null && optVal < minVal) {
					opt.disabled = true;
				} else {
					opt.disabled = false;
				}
			});

			// Restrict min options based on maxVal
			var minOptions = minSelect.querySelectorAll('option');
			minOptions.forEach(function (opt) {
				if (!opt.value) return; // 'بدون حد أدنى'
				var optVal = parseFloat(opt.value);
				if (maxVal !== null && optVal > maxVal) {
					opt.disabled = true;
				} else {
					opt.disabled = false;
				}
			});

			// Check validity
			if (minVal !== null && maxVal !== null && minVal > maxVal) {
				if (errorNotice) {
					errorNotice.classList.remove('hidden');
					errorNotice.textContent = 'الحد الأدنى للسعر يجب أن يكون أقل من أو يساوي الحد الأقصى';
				}
			} else {
				if (errorNotice) {
					errorNotice.classList.add('hidden');
				}
			}
		}

		minSelect.addEventListener('change', function () {
			var minVal = minSelect.value ? parseFloat(minSelect.value) : null;
			var maxVal = maxSelect.value ? parseFloat(maxSelect.value) : null;
			if (minVal !== null && maxVal !== null && maxVal < minVal) {
				maxSelect.value = ''; // Reset max if lower than new min
			}
			syncPriceOptions();
		});

		maxSelect.addEventListener('change', function () {
			var minVal = minSelect.value ? parseFloat(minSelect.value) : null;
			var maxVal = maxSelect.value ? parseFloat(maxSelect.value) : null;
			if (minVal !== null && maxVal !== null && minVal > maxVal) {
				minSelect.value = ''; // Reset min if higher than new max
			}
			syncPriceOptions();
		});

		// Initial sync
		syncPriceOptions();

		// Form Submission validation and URL cleanup
		form.addEventListener('submit', function (e) {
			var minVal = minSelect.value ? parseFloat(minSelect.value) : null;
			var maxVal = maxSelect.value ? parseFloat(maxSelect.value) : null;
			if (minVal !== null && maxVal !== null && minVal > maxVal) {
				e.preventDefault();
				if (errorNotice) {
					errorNotice.classList.remove('hidden');
					errorNotice.textContent = 'يرجى تصحيح نطاق السعر: الحد الأدنى لا يمكن أن يتجاوز الحد الأقصى';
				}
				minSelect.focus();
				return;
			}

			// Omit empty inputs from query string
			var sInput = form.querySelector('input[name="s"]');
			if (sInput && sInput.value.trim() === '') {
				sInput.disabled = true;
			}
			if (minSelect && minSelect.value === '') {
				minSelect.disabled = true;
			}
			if (maxSelect && maxSelect.value === '') {
				maxSelect.disabled = true;
			}
		});
	});
});
