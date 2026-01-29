<?php
namespace Gsap_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles script and style enqueuing.
 */
class Assets {

	/**
	 * Enqueue frontend scripts (public-facing pages).
	 */
	public static function enqueue_frontend() {
		wp_enqueue_script(
			'gsap-elementor-frontend',
			GSAP_ELEMENTOR_ASSETS_URL . 'js/gsap-frontend.js',
			[],
			GSAP_ELEMENTOR_VERSION,
			true
		);

		wp_enqueue_style(
			'gsap-elementor-frontend',
			GSAP_ELEMENTOR_ASSETS_URL . 'css/gsap-frontend.css',
			[],
			GSAP_ELEMENTOR_VERSION
		);
	}

	/**
	 * Enqueue editor scripts (Elementor panel).
	 */
	public static function enqueue_editor() {
		wp_enqueue_script(
			'gsap-elementor-editor',
			GSAP_ELEMENTOR_ASSETS_URL . 'js/gsap-editor.js',
			[],
			GSAP_ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * Enqueue preview scripts (Elementor iframe preview).
	 */
	public static function enqueue_preview() {
		wp_enqueue_script(
			'gsap-elementor-frontend',
			GSAP_ELEMENTOR_ASSETS_URL . 'js/gsap-frontend.js',
			[],
			GSAP_ELEMENTOR_VERSION,
			true
		);

		wp_enqueue_style(
			'gsap-elementor-frontend',
			GSAP_ELEMENTOR_ASSETS_URL . 'css/gsap-frontend.css',
			[],
			GSAP_ELEMENTOR_VERSION
		);
	}
}
