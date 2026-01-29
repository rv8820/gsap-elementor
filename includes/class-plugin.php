<?php
namespace Gsap_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class. Singleton.
 */
final class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once GSAP_ELEMENTOR_PATH . 'includes/class-assets.php';
		require_once GSAP_ELEMENTOR_PATH . 'includes/class-widgets-manager.php';
	}

	/**
	 * Register hooks.
	 */
	private function register_hooks() {
		add_action( 'elementor/widgets/register', [ Widgets_Manager::class, 'register' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ Assets::class, 'enqueue_frontend' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ Assets::class, 'enqueue_editor' ] );
		add_action( 'elementor/preview/enqueue_scripts', [ Assets::class, 'enqueue_preview' ] );

		// Register widget categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ] );
	}

	/**
	 * Register custom widget category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public function register_categories( $elements_manager ) {
		$elements_manager->add_category( 'gsap-elementor', [
			'title' => esc_html__( 'GSAP Animations', 'gsap-elementor' ),
			'icon'  => 'eicon-animation',
		] );
	}
}
