<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ScrollSmoother Widget — Enables smooth scrolling and parallax for a page section.
 */
class Widget_Scroll_Smoother extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_scroll_smoother';
	}

	public function get_title() {
		return esc_html__( 'GSAP ScrollSmoother', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-scroll';
	}

	public function get_keywords() {
		return [ 'gsap', 'scroll', 'smooth', 'smoother', 'parallax', 'inertia' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_settings', [
			'label' => esc_html__( 'ScrollSmoother Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'smooth_amount', [
			'label'       => esc_html__( 'Smooth Amount', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 1,
			'min'         => 0,
			'max'         => 5,
			'step'        => 0.1,
			'description' => esc_html__( 'Higher = smoother/slower. 0 disables smoothing.', 'gsap-elementor' ),
		] );

		$this->add_control( 'effects_enabled', [
			'label'       => esc_html__( 'Enable Parallax Effects', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => esc_html__( 'Enables data-speed and data-lag attributes', 'gsap-elementor' ),
		] );

		$this->add_control( 'normalize_scroll', [
			'label'       => esc_html__( 'Normalize Scroll', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Forces consistent scroll behavior across devices', 'gsap-elementor' ),
		] );

		$this->add_control( 'smooth_touch', [
			'label'   => esc_html__( 'Smooth Touch', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'false',
			'options' => [
				'false' => 'Disabled',
				'0.1'   => 'Light (0.1)',
				'0.3'   => 'Medium (0.3)',
				'0.5'   => 'Heavy (0.5)',
			],
		] );

		$this->end_controls_section();

		// --- Content with parallax elements ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Demo Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Content (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div data-speed="0.5" style="padding:40px;background:#6c63ff;color:#fff;border-radius:12px;margin-bottom:20px;"><h3>Slow parallax element (speed: 0.5)</h3></div><div data-speed="1.5" style="padding:40px;background:#0ae448;color:#fff;border-radius:12px;margin-bottom:20px;"><h3>Fast parallax element (speed: 1.5)</h3></div><div data-lag="0.5" style="padding:40px;background:#ff6b6b;color:#fff;border-radius:12px;"><h3>Lagging element (lag: 0.5)</h3></div>',
			'description' => esc_html__( 'Use data-speed (parallax) and data-lag attributes on elements', 'gsap-elementor' ),
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$smooth_touch = $s['smooth_touch'];
		if ( 'false' === $smooth_touch ) {
			$smooth_touch = false;
		} else {
			$smooth_touch = floatval( $smooth_touch );
		}

		$config = [
			'smooth'         => floatval( $s['smooth_amount'] ),
			'effects'        => 'yes' === $s['effects_enabled'],
			'normalizeScroll' => 'yes' === $s['normalize_scroll'],
			'smoothTouch'    => $smooth_touch,
		];

		echo '<div class="gsap-widget-scroll-smoother"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-scroll-smoother-content">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
	}
}
