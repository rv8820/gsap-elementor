<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Physics2D Widget — Velocity, angle, and gravity-based animations.
 */
class Widget_Physics2D extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_physics2d';
	}

	public function get_title() {
		return esc_html__( 'GSAP Physics2D', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-flash';
	}

	public function get_keywords() {
		return [ 'gsap', 'physics', 'gravity', 'velocity', 'particle', 'explode' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Element (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="width:40px;height:40px;background:#ff6b6b;border-radius:50%;"></div>',
		] );

		$this->add_control( 'num_particles', [
			'label'       => esc_html__( 'Number of Particles', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 20,
			'min'         => 1,
			'max'         => 100,
			'description' => esc_html__( 'Clones the element above for particle effects', 'gsap-elementor' ),
		] );

		$this->add_control( 'randomize_colors', [
			'label'   => esc_html__( 'Randomize Colors', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'color_palette', [
			'label'       => esc_html__( 'Color Palette', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '#ff6b6b, #6c63ff, #0ae448, #ffd93d, #ff8a5c',
			'condition'   => [ 'randomize_colors' => 'yes' ],
			'description' => esc_html__( 'Comma-separated colors', 'gsap-elementor' ),
		] );

		$this->end_controls_section();

		// --- Physics Settings ---
		$this->start_controls_section( 'section_physics', [
			'label' => esc_html__( 'Physics Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'velocity', [
			'label'   => esc_html__( 'Velocity', 'gsap-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 1000 ] ],
			'default' => [ 'size' => 300 ],
		] );

		$this->add_control( 'angle_min', [
			'label'   => esc_html__( 'Angle Min (deg)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 200,
			'min'     => 0,
			'max'     => 360,
		] );

		$this->add_control( 'angle_max', [
			'label'   => esc_html__( 'Angle Max (deg)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 340,
			'min'     => 0,
			'max'     => 360,
		] );

		$this->add_control( 'gravity', [
			'label'   => esc_html__( 'Gravity', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 500,
			'min'     => 0,
			'max'     => 2000,
		] );

		$this->add_control( 'friction', [
			'label'   => esc_html__( 'Friction', 'gsap-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.01 ] ],
			'default' => [ 'size' => 0.02 ],
		] );

		$this->end_controls_section();

		// --- Trigger ---
		$this->start_controls_section( 'section_trigger', [
			'label' => esc_html__( 'Trigger', 'gsap-elementor' ),
		] );

		$this->add_control( 'trigger_type', [
			'label'   => esc_html__( 'Trigger', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'click',
			'options' => [
				'click'   => 'Click Button',
				'load'    => 'On Page Load',
				'scroll'  => 'On Scroll (ScrollTrigger)',
			],
		] );

		$this->add_control( 'button_text', [
			'label'     => esc_html__( 'Button Text', 'gsap-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Launch!',
			'condition' => [ 'trigger_type' => 'click' ],
		] );

		$this->end_controls_section();

		// --- Container ---
		$this->start_controls_section( 'section_style', [
			'label' => esc_html__( 'Container', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'container_height', [
			'label'      => esc_html__( 'Height', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 1000 ] ],
			'default'    => [ 'size' => 400, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-physics2d-container' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'numParticles'   => intval( $s['num_particles'] ),
			'randomizeColors' => 'yes' === $s['randomize_colors'],
			'colorPalette'   => array_map( 'trim', explode( ',', $s['color_palette'] ?? '' ) ),
			'velocity'       => floatval( $s['velocity']['size'] ?? 300 ),
			'angleMin'       => floatval( $s['angle_min'] ),
			'angleMax'       => floatval( $s['angle_max'] ),
			'gravity'        => floatval( $s['gravity'] ),
			'friction'       => floatval( $s['friction']['size'] ?? 0.02 ),
			'trigger'        => $s['trigger_type'],
		];

		echo '<div class="gsap-widget-physics2d"';
		$this->render_data_attrs( $config );
		echo '>';

		if ( 'click' === $s['trigger_type'] ) {
			echo '<button class="gsap-physics2d-trigger" type="button" style="display:block;margin:0 auto 16px;padding:10px 24px;border:2px solid #333;background:transparent;cursor:pointer;border-radius:6px;font-weight:600;">';
			echo esc_html( $s['button_text'] );
			echo '</button>';
		}

		echo '<div class="gsap-physics2d-container" style="position:relative;overflow:hidden;width:100%;">';
		echo '<div class="gsap-physics2d-template" style="display:none;">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}
}
