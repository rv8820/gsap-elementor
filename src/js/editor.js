/**
 * GSAP Elementor — Editor Script
 *
 * Handles live preview by reinitializing GSAP widgets
 * when Elementor detects changes in the editor panel.
 */

(function () {
	'use strict';

	if (typeof elementor === 'undefined') return;

	/**
	 * When a widget is rendered in the preview, reinitialize its GSAP animation.
	 */
	elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
		const widgetType = model.get('widgetType');

		// Only act on our GSAP widgets
		if (!widgetType || !widgetType.startsWith('gsap_')) return;

		// Watch for setting changes and reinitialize
		model.on('change', debounce(function () {
			reinitWidget(view);
		}, 300));
	});

	/**
	 * Reinitialize a widget in the preview iframe.
	 */
	function reinitWidget(view) {
		if (!view || !view.el) return;

		const previewWindow = elementor.$preview?.[0]?.contentWindow;
		if (!previewWindow || !previewWindow.gsapElementor) return;

		// Clear init flag so the widget reinitializes
		const widgets = view.el.querySelectorAll('[data-gsap-widget]');
		widgets.forEach(function (el) {
			delete el.dataset.gsapInit;

			// Kill existing GSAP animations on this element
			if (previewWindow.gsap) {
				previewWindow.gsap.killTweensOf(el.querySelectorAll('*'));
			}
		});

		// Re-run initialization
		previewWindow.gsapElementor.init(view.el);
	}

	/**
	 * Simple debounce helper.
	 */
	function debounce(fn, delay) {
		let timer;
		return function () {
			clearTimeout(timer);
			timer = setTimeout(fn, delay);
		};
	}

	/**
	 * When Elementor preview is fully loaded, initialize all widgets.
	 */
	elementor.on('preview:loaded', function () {
		const previewWindow = elementor.$preview?.[0]?.contentWindow;
		if (previewWindow && previewWindow.gsapElementor) {
			previewWindow.gsapElementor.init();
		}
	});
})();
