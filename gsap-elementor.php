<?php
/**
 * Plugin Name: GSAP for Elementor
 * Plugin URI: https://craft.com.sg
 * Description: Brings the full power of GSAP animations to Elementor with dedicated widgets for ScrollTrigger, SplitText, MorphSVG, DrawSVG, Flip, Draggable, and more.
 * Version: 1.0.0
 * Author: Craft
 * Author URI: https://craft.com.sg
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gsap-elementor
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Elementor tested up to: 3.20
 * Elementor Pro tested up to: 3.20
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GSAP_ELEMENTOR_VERSION', '1.0.0' );
define( 'GSAP_ELEMENTOR_FILE', __FILE__ );
define( 'GSAP_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GSAP_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );
define( 'GSAP_ELEMENTOR_ASSETS_URL', GSAP_ELEMENTOR_URL . 'assets/' );

/**
 * Check if Elementor is active before bootstrapping.
 */
function gsap_elementor_init() {
	// Check for Elementor
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'gsap_elementor_missing_elementor_notice' );
		return;
	}

	// Check Elementor version
	if ( ! version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
		add_action( 'admin_notices', 'gsap_elementor_outdated_elementor_notice' );
		return;
	}

	require_once GSAP_ELEMENTOR_PATH . 'includes/class-plugin.php';
	\Gsap_Elementor\Plugin::instance();
}
add_action( 'plugins_loaded', 'gsap_elementor_init' );

/**
 * Admin notice: Elementor not found.
 */
function gsap_elementor_missing_elementor_notice() {
	$message = sprintf(
		/* translators: 1: Plugin name, 2: Elementor */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'gsap-elementor' ),
		'<strong>GSAP for Elementor</strong>',
		'<strong>Elementor</strong>'
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}

/**
 * Admin notice: Elementor outdated.
 */
function gsap_elementor_outdated_elementor_notice() {
	$message = sprintf(
		/* translators: 1: Plugin name, 2: Elementor, 3: Required version */
		esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'gsap-elementor' ),
		'<strong>GSAP for Elementor</strong>',
		'<strong>Elementor</strong>',
		'3.5.0'
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}
