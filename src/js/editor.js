/**
 * GSAP Elementor — Editor Script
 *
 * Handles live preview by reinitializing GSAP animations
 * when Elementor detects changes in the editor panel.
 *
 * Supports both:
 * - Standalone GSAP widgets (data-gsap-widget)
 * - Injected controls on ANY widget (data-gsap-anim)
 */

(function () {
	'use strict';

	if (typeof elementor === 'undefined') return;

	/**
	 * Watch for panel changes on ANY widget — both standalone GSAP widgets
	 * and the injected "GSAP Animation" controls on standard widgets.
	 */
	elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
		model.on('change', debounce(function () {
			reinitElement(view);
		}, 300));
	});

	// Also listen on sections, columns, and containers.
	elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
		model.on('change', debounce(function () {
			reinitElement(view);
		}, 300));
	});

	elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
		model.on('change', debounce(function () {
			reinitElement(view);
		}, 300));
	});

	elementor.hooks.addAction('panel/open_editor/container', function (panel, model, view) {
		model.on('change', debounce(function () {
			reinitElement(view);
		}, 300));
	});

	/**
	 * Reinitialize GSAP animations in the preview iframe.
	 */
	function reinitElement(view) {
		if (!view || !view.el) return;

		var previewWindow = getPreviewWindow();
		if (!previewWindow || !previewWindow.gsapElementor) return;

		// Clear init flags on standalone widgets
		var widgets = view.el.querySelectorAll('[data-gsap-widget]');
		widgets.forEach(function (el) {
			delete el.dataset.gsapInit;
			if (previewWindow.gsap) {
				previewWindow.gsap.killTweensOf(el.querySelectorAll('*'));
			}
		});

		// Clear init flags on injected elements
		var injected = view.el.querySelectorAll('[data-gsap-anim]');
		injected.forEach(function (el) {
			delete el.dataset.gsapAnimInit;
			if (previewWindow.gsap) {
				previewWindow.gsap.killTweensOf(el);
				previewWindow.gsap.killTweensOf(el.querySelectorAll('*'));
				// Reset transforms so the animation can replay
				previewWindow.gsap.set(el, { clearProps: 'all' });
			}
		});

		// Also check the view element itself (for sections/columns/containers)
		if (view.el.dataset && view.el.dataset.gsapAnim) {
			delete view.el.dataset.gsapAnimInit;
			if (previewWindow.gsap) {
				previewWindow.gsap.killTweensOf(view.el);
				previewWindow.gsap.set(view.el, { clearProps: 'all' });
			}
		}

		// Re-run initialization
		previewWindow.gsapElementor.init(view.el);
	}

	/**
	 * Get the preview window safely.
	 */
	function getPreviewWindow() {
		try {
			return elementor.$preview && elementor.$preview[0] && elementor.$preview[0].contentWindow;
		} catch (e) {
			return null;
		}
	}

	/**
	 * Simple debounce helper.
	 */
	function debounce(fn, delay) {
		var timer;
		return function () {
			clearTimeout(timer);
			timer = setTimeout(fn, delay);
		};
	}

	/**
	 * When Elementor preview is fully loaded, initialize all animations.
	 */
	elementor.on('preview:loaded', function () {
		var previewWindow = getPreviewWindow();
		if (previewWindow && previewWindow.gsapElementor) {
			previewWindow.gsapElementor.init();
		}
	});
})();
