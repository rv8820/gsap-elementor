<?php
namespace Gsap_Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for all GSAP Elementor widgets.
 * Provides shared controls for easing, duration, delay, and repeat.
 */
abstract class Widget_GSAP_Base extends Widget_Base {

	/**
	 * Widget category.
	 */
	public function get_categories() {
		return [ 'gsap-elementor' ];
	}

	/**
	 * Common GSAP easing options.
	 */
	protected function get_easing_options() {
		return [
			'none'             => 'none (linear)',
			'power1.in'       => 'power1.in',
			'power1.out'      => 'power1.out',
			'power1.inOut'    => 'power1.inOut',
			'power2.in'       => 'power2.in',
			'power2.out'      => 'power2.out',
			'power2.inOut'    => 'power2.inOut',
			'power3.in'       => 'power3.in',
			'power3.out'      => 'power3.out',
			'power3.inOut'    => 'power3.inOut',
			'power4.in'       => 'power4.in',
			'power4.out'      => 'power4.out',
			'power4.inOut'    => 'power4.inOut',
			'back.in'         => 'back.in',
			'back.out'        => 'back.out',
			'back.inOut'      => 'back.inOut',
			'bounce.in'       => 'bounce.in',
			'bounce.out'      => 'bounce.out',
			'bounce.inOut'    => 'bounce.inOut',
			'circ.in'         => 'circ.in',
			'circ.out'        => 'circ.out',
			'circ.inOut'      => 'circ.inOut',
			'elastic.in'      => 'elastic.in',
			'elastic.out'     => 'elastic.out',
			'elastic.inOut'   => 'elastic.inOut',
			'expo.in'         => 'expo.in',
			'expo.out'        => 'expo.out',
			'expo.inOut'      => 'expo.inOut',
			'sine.in'         => 'sine.in',
			'sine.out'        => 'sine.out',
			'sine.inOut'      => 'sine.inOut',
			'steps(10)'       => 'steps(10)',
			'steps(20)'       => 'steps(20)',
			'slow(0.7,0.7,false)' => 'slow',
			'rough({...})'    => 'rough',
		];
	}

	/**
	 * Add common animation controls to a section.
	 */
	protected function register_animation_controls() {
		$this->add_control(
			'duration',
			[
				'label'   => esc_html__( 'Duration (s)', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 0,
				'max'     => 20,
				'step'    => 0.1,
			]
		);

		$this->add_control(
			'delay',
			[
				'label'   => esc_html__( 'Delay (s)', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 20,
				'step'    => 0.1,
			]
		);

		$this->add_control(
			'ease',
			[
				'label'   => esc_html__( 'Easing', 'gsap-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'power2.out',
				'options' => $this->get_easing_options(),
			]
		);

		$this->add_control(
			'repeat',
			[
				'label'       => esc_html__( 'Repeat', 'gsap-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => -1,
				'max'         => 100,
				'description' => esc_html__( '-1 for infinite loop', 'gsap-elementor' ),
			]
		);

		$this->add_control(
			'yoyo',
			[
				'label'        => esc_html__( 'Yoyo', 'gsap-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'description'  => esc_html__( 'Reverse on each repeat cycle', 'gsap-elementor' ),
			]
		);
	}

	/**
	 * Add transform controls (x, y, rotation, scale, opacity).
	 */
	protected function register_transform_controls( $prefix = '' ) {
		$this->add_control(
			$prefix . 'x',
			[
				'label'   => esc_html__( 'X (px)', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$this->add_control(
			$prefix . 'y',
			[
				'label'   => esc_html__( 'Y (px)', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$this->add_control(
			$prefix . 'rotation',
			[
				'label'   => esc_html__( 'Rotation (deg)', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => -360,
				'max'     => 360,
			]
		);

		$this->add_control(
			$prefix . 'scale',
			[
				'label'   => esc_html__( 'Scale', 'gsap-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 0,
				'max'     => 10,
				'step'    => 0.1,
			]
		);

		$this->add_control(
			$prefix . 'opacity',
			[
				'label'   => esc_html__( 'Opacity', 'gsap-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default' => [
					'size' => 1,
				],
			]
		);
	}

	/**
	 * Add ScrollTrigger controls.
	 */
	protected function register_scroll_trigger_controls() {
		$this->add_control(
			'scroll_trigger_enabled',
			[
				'label'   => esc_html__( 'Enable ScrollTrigger', 'gsap-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'scroll_trigger_start',
			[
				'label'     => esc_html__( 'Start', 'gsap-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'top 80%',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
			]
		);

		$this->add_control(
			'scroll_trigger_end',
			[
				'label'     => esc_html__( 'End', 'gsap-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'bottom 20%',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
			]
		);

		$this->add_control(
			'scroll_trigger_scrub',
			[
				'label'     => esc_html__( 'Scrub', 'gsap-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
			]
		);

		$this->add_control(
			'scroll_trigger_pin',
			[
				'label'     => esc_html__( 'Pin', 'gsap-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
			]
		);

		$this->add_control(
			'scroll_trigger_markers',
			[
				'label'     => esc_html__( 'Show Markers (Debug)', 'gsap-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
			]
		);

		$this->add_control(
			'scroll_trigger_toggle_actions',
			[
				'label'     => esc_html__( 'Toggle Actions', 'gsap-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'play none none none',
				'condition' => [ 'scroll_trigger_enabled' => 'yes' ],
				'description' => esc_html__( 'onEnter onLeave onEnterBack onLeaveBack', 'gsap-elementor' ),
			]
		);
	}

	/**
	 * Output widget data as JSON data attributes.
	 */
	protected function render_data_attrs( array $data ) {
		echo ' data-gsap-widget="' . esc_attr( $this->get_name() ) . '"';
		echo " data-gsap-config='" . wp_json_encode( $data ) . "'";
	}
}
