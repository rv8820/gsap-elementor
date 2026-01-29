<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ScrollTo Widget — Smooth scroll button to a target element or position.
 */
class Widget_Scroll_To extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_scroll_to';
	}

	public function get_title() {
		return esc_html__( 'GSAP Scroll To', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-arrow-down';
	}

	public function get_keywords() {
		return [ 'gsap', 'scroll', 'smooth', 'anchor', 'navigation' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_settings', [
			'label' => esc_html__( 'Scroll To Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'button_text', [
			'label'   => esc_html__( 'Button Text', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'Scroll Down',
		] );

		$this->add_control( 'scroll_target', [
			'label'       => esc_html__( 'Target', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '#section-2',
			'description' => esc_html__( 'CSS selector or pixel value (e.g., #my-section, .my-class, 500)', 'gsap-elementor' ),
		] );

		$this->add_control( 'scroll_axis', [
			'label'   => esc_html__( 'Axis', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'y',
			'options' => [
				'y' => 'Vertical',
				'x' => 'Horizontal',
			],
		] );

		$this->add_control( 'scroll_duration', [
			'label'   => esc_html__( 'Duration (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 1,
			'min'     => 0.1,
			'max'     => 10,
			'step'    => 0.1,
		] );

		$this->add_control( 'scroll_ease', [
			'label'   => esc_html__( 'Easing', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'power2.inOut',
			'options' => $this->get_easing_options(),
		] );

		$this->add_control( 'scroll_offset', [
			'label'       => esc_html__( 'Offset (px)', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'description' => esc_html__( 'Offset from target position (negative = above)', 'gsap-elementor' ),
		] );

		$this->add_control( 'auto_kill', [
			'label'   => esc_html__( 'Auto Kill on User Scroll', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->end_controls_section();

		// --- Button Style ---
		$this->start_controls_section( 'section_style', [
			'label' => esc_html__( 'Button Style', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'button_color', [
			'label'     => esc_html__( 'Text Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .gsap-scroll-to-btn' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_bg', [
			'label'     => esc_html__( 'Background Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6c63ff',
			'selectors' => [
				'{{WRAPPER}} .gsap-scroll-to-btn' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'button_padding', [
			'label'      => esc_html__( 'Padding', 'gsap-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [
				'top'    => '12',
				'right'  => '24',
				'bottom' => '12',
				'left'   => '24',
				'unit'   => 'px',
			],
			'selectors'  => [
				'{{WRAPPER}} .gsap-scroll-to-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'button_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 6, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-scroll-to-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'target'   => $s['scroll_target'],
			'axis'     => $s['scroll_axis'],
			'duration' => floatval( $s['scroll_duration'] ),
			'ease'     => $s['scroll_ease'],
			'offset'   => intval( $s['scroll_offset'] ),
			'autoKill' => 'yes' === $s['auto_kill'],
		];

		echo '<div class="gsap-widget-scroll-to"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<button class="gsap-scroll-to-btn" type="button">';
		echo esc_html( $s['button_text'] );
		echo '</button>';
		echo '</div>';
	}
}
