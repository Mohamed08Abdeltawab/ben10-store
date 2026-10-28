/**
 * Mobile Navigation Bar & Categories/Saved Drawers Controller
 *
 * Handles mobile drawer sheet animation, backdrop dismissals,
 * keyboard accessibility, and cart count micro-animations.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	// =========================================================================
	// 1. Categories Drawer
	// =========================================================================
	var catTriggerBtn    = document.getElementById('test-theme-mobile-categories-trigger');
	var catTriggerHeader = document.getElementById('open-categories-drawer');
	var catDrawer        = document.getElementById('test-theme-categories-drawer');
	var catBackdrop   = document.getElementById('test-theme-drawer-backdrop');
	var catSheet      = document.getElementById('test-theme-drawer-sheet');
	var catCloseBtn   = document.getElementById('test-theme-drawer-close');

	if ((catTriggerBtn || catTriggerHeader) && catDrawer) {
		var isCatOpen = false;

		function openCatDrawer() {
			isCatOpen = true;
			catDrawer.classList.remove('invisible', 'pointer-events-none');
			catDrawer.classList.add('opacity-100');
			catDrawer.setAttribute('aria-hidden', 'false');
			if (catTriggerBtn) catTriggerBtn.setAttribute('aria-expanded', 'true');
			if (catTriggerHeader) catTriggerHeader.setAttribute('aria-expanded', 'true');

			requestAnimationFrame(function () {
				if (catBackdrop) {
					catBackdrop.classList.remove('opacity-0');
					catBackdrop.classList.add('opacity-100');
				}
				if (catSheet) {
					catSheet.classList.remove('translate-y-full');
					catSheet.classList.add('translate-y-0');
				}
			});

			document.body.style.overflow = 'hidden';
		}

		function closeCatDrawer() {
			isCatOpen = false;
			if (catTriggerBtn) catTriggerBtn.setAttribute('aria-expanded', 'false');
			if (catTriggerHeader) catTriggerHeader.setAttribute('aria-expanded', 'false');

			if (catBackdrop) {
				catBackdrop.classList.remove('opacity-100');
				catBackdrop.classList.add('opacity-0');
			}
			if (catSheet) {
				catSheet.classList.remove('translate-y-0');
				catSheet.classList.add('translate-y-full');
			}

			document.body.style.overflow = '';

			setTimeout(function () {
				if (!isCatOpen) {
					catDrawer.classList.add('invisible', 'pointer-events-none');
					catDrawer.classList.remove('opacity-100');
					catDrawer.setAttribute('aria-hidden', 'true');
				}
			}, 300);
		}

		if (catTriggerBtn) {
			catTriggerBtn.addEventListener('click', function (e) {
				e.preventDefault();
				if (isCatOpen) {
					closeCatDrawer();
				} else {
					openCatDrawer();
				}
			});
		}

		if (catTriggerHeader) {
			catTriggerHeader.addEventListener('click', function (e) {
				e.preventDefault();
				if (isCatOpen) {
					closeCatDrawer();
				} else {
					openCatDrawer();
				}
			});
		}

		if (catCloseBtn) {
			catCloseBtn.addEventListener('click', function (e) {
				e.preventDefault();
				closeCatDrawer();
			});
		}

		if (catBackdrop) {
			catBackdrop.addEventListener('click', function () {
				closeCatDrawer();
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isCatOpen) {
				closeCatDrawer();
			}
		});

		var catLinks = catDrawer.querySelectorAll('a');
		catLinks.forEach(function (link) {
			link.addEventListener('click', function () {
				closeCatDrawer();
			});
		});
	}

	// =========================================================================
	// 2. Saved / Bookmarks Drawer (Triggered via header bookmark or bottom nav)
	// =========================================================================
	var savedTriggerNav    = document.getElementById('test-theme-mobile-saved-trigger');
	var savedTriggerHeader = document.getElementById('test-theme-bookmark-btn');
	var savedDrawer        = document.getElementById('test-theme-saved-drawer');
	var savedBackdrop      = document.getElementById('test-theme-saved-backdrop');
	var savedSheet         = document.getElementById('test-theme-saved-sheet');
	var savedCloseBtn      = document.getElementById('test-theme-saved-close');

	if (savedDrawer) {
		var isSavedOpen = false;

		function openSavedDrawer() {
			isSavedOpen = true;
			savedDrawer.classList.remove('invisible', 'pointer-events-none');
			savedDrawer.classList.add('opacity-100');
			savedDrawer.setAttribute('aria-hidden', 'false');

			requestAnimationFrame(function () {
				if (savedBackdrop) {
					savedBackdrop.classList.remove('opacity-0');
					savedBackdrop.classList.add('opacity-100');
				}
				if (savedSheet) {
					savedSheet.classList.remove('translate-y-full');
					savedSheet.classList.add('translate-y-0');
				}
			});

			document.body.style.overflow = 'hidden';
		}

		function closeSavedDrawer() {
			isSavedOpen = false;

			if (savedBackdrop) {
				savedBackdrop.classList.remove('opacity-100');
				savedBackdrop.classList.add('opacity-0');
			}
			if (savedSheet) {
				savedSheet.classList.remove('translate-y-0');
				savedSheet.classList.add('translate-y-full');
			}

			document.body.style.overflow = '';

			setTimeout(function () {
				if (!isSavedOpen) {
					savedDrawer.classList.add('invisible', 'pointer-events-none');
					savedDrawer.classList.remove('opacity-100');
					savedDrawer.setAttribute('aria-hidden', 'true');
				}
			}, 300);
		}

		if (savedTriggerNav) {
			savedTriggerNav.addEventListener('click', function (e) {
				e.preventDefault();
				if (isSavedOpen) {
					closeSavedDrawer();
				} else {
					openSavedDrawer();
				}
			});
		}

		if (savedTriggerHeader) {
			savedTriggerHeader.addEventListener('click', function (e) {
				e.preventDefault();
				if (isSavedOpen) {
					closeSavedDrawer();
				} else {
					openSavedDrawer();
				}
			});
		}

		if (savedCloseBtn) {
			savedCloseBtn.addEventListener('click', function (e) {
				e.preventDefault();
				closeSavedDrawer();
			});
		}

		if (savedBackdrop) {
			savedBackdrop.addEventListener('click', function () {
				closeSavedDrawer();
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isSavedOpen) {
				closeSavedDrawer();
			}
		});
	}
});
