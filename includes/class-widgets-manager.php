<?php
namespace Gsap_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages registration of all GSAP widgets.
 */
class Widgets_Manager {

	/**
	 * Widget class map: file => class name.
	 */
	private static function get_widgets() {
		return [
			// Text Animation
			'widget-scramble-text'       => 'Widget_Scramble_Text',
			'widget-text-typewriter'     => 'Widget_Text_Typewriter',

			// SVG Animation
			'widget-draw-svg'            => 'Widget_Draw_SVG',
			'widget-morph-svg'           => 'Widget_Morph_SVG',
			'widget-motion-path'         => 'Widget_Motion_Path',

			// Layout & Interaction
			'widget-flip'               => 'Widget_Flip',
			'widget-draggable'          => 'Widget_Draggable',
			'widget-scroll-smoother'    => 'Widget_Scroll_Smoother',
			'widget-observer'           => 'Widget_Observer',
			'widget-physics2d'          => 'Widget_Physics2D',
		];
	}

	/**
	 * Register all widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public static function register( $widgets_manager ) {
		$widgets_dir = GSAP_ELEMENTOR_PATH . 'includes/widgets/';

		// Load base class first.
		require_once $widgets_dir . 'class-widget-base.php';

		foreach ( self::get_widgets() as $file => $class ) {
			$filepath = $widgets_dir . 'class-' . $file . '.php';
			if ( file_exists( $filepath ) ) {
				require_once $filepath;
				$full_class = __NAMESPACE__ . '\\' . $class;
				if ( class_exists( $full_class ) ) {
					$widgets_manager->register( new $full_class() );
				}
			}
		}
	}
}
