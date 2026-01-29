<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GSAP Animate Widget — Core tween/timeline animations.
 * Supports from/to/fromTo with full transform controls.
 */
class Widget_GSAP_Animate extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_animate';
	}

	public function get_title() {
		return esc_html__( 'GSAP Animate', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-animation';
	}

	public function get_keywords() {
		return [ 'gsap', 'animate', 'tween', 'fade', 'slide', 'scale', 'rotate' ];
	}

	protected function register_controls() {

		// --- Content Section ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Content (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="width:120px;height:120px;background:#0ae448;border-radius:12px;"></div>',
		] );

		$this->end_controls_section();

		// --- Animation Settings ---
		$this->start_controls_section( 'section_animation', [
			'label' => esc_html__( 'Animation', 'gsap-elementor' ),
		] );

		$this->add_control( 'anim_method', [
			'label'   => esc_html__( 'Method', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'from',
			'options' => [
				'to'     => 'to',
				'from'   => 'from',
				'fromTo' => 'fromTo',
			],
		] );

		$this->register_animation_controls();

		$this->end_controls_section();

		// --- Transform "To" Values ---
		$this->start_controls_section( 'section_transform_to', [
			'label' => esc_html__( 'Transform — Target', 'gsap-elementor' ),
		] );

		$this->register_transform_controls( 'to_' );

		$this->end_controls_section();

		// --- Transform "From" Values (for fromTo) ---
		$this->start_controls_section( 'section_transform_from', [
			'label'     => esc_html__( 'Transform — From', 'gsap-elementor' ),
			'condition' => [ 'anim_method' => 'fromTo' ],
		] );

		$this->register_transform_controls( 'from_' );

		$this->end_controls_section();

		// --- Stagger ---
		$this->start_controls_section( 'section_stagger', [
			'label' => esc_html__( 'Stagger', 'gsap-elementor' ),
		] );

		$this->add_control( 'stagger_enabled', [
			'label'   => esc_html__( 'Enable Stagger', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'stagger_amount', [
			'label'     => esc_html__( 'Stagger Amount (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0.2,
			'min'       => 0,
			'max'       => 5,
			'step'      => 0.05,
			'condition' => [ 'stagger_enabled' => 'yes' ],
		] );

		$this->add_control( 'stagger_from', [
			'label'     => esc_html__( 'Stagger From', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'start',
			'options'   => [
				'start'  => 'Start',
				'end'    => 'End',
				'center' => 'Center',
				'edges'  => 'Edges',
				'random' => 'Random',
			],
			'condition' => [ 'stagger_enabled' => 'yes' ],
		] );

		$this->end_controls_section();

		// --- ScrollTrigger ---
		$this->start_controls_section( 'section_scroll_trigger', [
			'label' => esc_html__( 'ScrollTrigger', 'gsap-elementor' ),
		] );

		$this->register_scroll_trigger_controls();

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'method'   => $s['anim_method'],
			'duration' => floatval( $s['duration'] ),
			'delay'    => floatval( $s['delay'] ),
			'ease'     => $s['ease'],
			'repeat'   => intval( $s['repeat'] ),
			'yoyo'     => 'yes' === $s['yoyo'],
			'to'       => [
				'x'        => floatval( $s['to_x'] ),
				'y'        => floatval( $s['to_y'] ),
				'rotation' => floatval( $s['to_rotation'] ),
				'scale'    => floatval( $s['to_scale'] ),
				'opacity'  => floatval( $s['to_opacity']['size'] ?? 1 ),
			],
		];

		if ( 'fromTo' === $s['anim_method'] ) {
			$config['from'] = [
				'x'        => floatval( $s['from_x'] ),
				'y'        => floatval( $s['from_y'] ),
				'rotation' => floatval( $s['from_rotation'] ),
				'scale'    => floatval( $s['from_scale'] ),
				'opacity'  => floatval( $s['from_opacity']['size'] ?? 1 ),
			];
		}

		if ( 'yes' === $s['stagger_enabled'] ) {
			$config['stagger'] = [
				'each' => floatval( $s['stagger_amount'] ),
				'from' => $s['stagger_from'],
			];
		}

		if ( 'yes' === $s['scroll_trigger_enabled'] ) {
			$config['scrollTrigger'] = [
				'start'         => $s['scroll_trigger_start'],
				'end'           => $s['scroll_trigger_end'],
				'scrub'         => 'yes' === $s['scroll_trigger_scrub'],
				'pin'           => 'yes' === $s['scroll_trigger_pin'],
				'markers'       => 'yes' === $s['scroll_trigger_markers'],
				'toggleActions' => $s['scroll_trigger_toggle_actions'],
			];
		}

		echo '<div class="gsap-widget-animate"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-animate-target">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
	}
}
