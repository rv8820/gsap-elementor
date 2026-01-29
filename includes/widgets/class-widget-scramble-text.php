<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ScrambleText Widget — Decodes/scrambles text with randomized characters.
 */
class Widget_Scramble_Text extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_scramble_text';
	}

	public function get_title() {
		return esc_html__( 'GSAP ScrambleText', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-code-highlight';
	}

	public function get_keywords() {
		return [ 'gsap', 'scramble', 'text', 'decode', 'cipher', 'glitch' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'text_content', [
			'label'   => esc_html__( 'Text', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => 'GSAP ScrambleText Plugin',
			'rows'    => 3,
		] );

		$this->add_control( 'html_tag', [
			'label'   => esc_html__( 'HTML Tag', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4',
				'h5' => 'H5', 'h6' => 'H6', 'p' => 'p', 'div' => 'div',
			],
		] );

		$this->end_controls_section();

		// --- Scramble Settings ---
		$this->start_controls_section( 'section_scramble', [
			'label' => esc_html__( 'Scramble Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'scramble_chars', [
			'label'       => esc_html__( 'Scramble Characters', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'upperCase',
			'description' => esc_html__( 'upperCase, lowerCase, or custom character set', 'gsap-elementor' ),
		] );

		$this->add_control( 'reveal_delay', [
			'label'   => esc_html__( 'Reveal Delay', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0,
			'min'     => 0,
			'max'     => 5,
			'step'    => 0.1,
		] );

		$this->add_control( 'scramble_speed', [
			'label'   => esc_html__( 'Speed', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 1,
			'min'     => 0.1,
			'max'     => 5,
			'step'    => 0.1,
		] );

		$this->add_control( 'new_class', [
			'label'       => esc_html__( 'New Class', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'CSS class added to new characters during reveal', 'gsap-elementor' ),
		] );

		$this->add_control( 'old_class', [
			'label'       => esc_html__( 'Old Class', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'CSS class for the scrambled characters', 'gsap-elementor' ),
		] );

		$this->end_controls_section();

		// --- Animation ---
		$this->start_controls_section( 'section_animation', [
			'label' => esc_html__( 'Animation', 'gsap-elementor' ),
		] );

		$this->register_animation_controls();

		$this->end_controls_section();

		// --- ScrollTrigger ---
		$this->start_controls_section( 'section_scroll_trigger', [
			'label' => esc_html__( 'ScrollTrigger', 'gsap-elementor' ),
		] );

		$this->register_scroll_trigger_controls();

		$this->end_controls_section();

		// --- Style ---
		$this->start_controls_section( 'section_style', [
			'label' => esc_html__( 'Typography', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'text_typography',
			'selector' => '{{WRAPPER}} .gsap-scramble-text-target',
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .gsap-scramble-text-target' => 'color: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'duration'      => floatval( $s['duration'] ),
			'delay'         => floatval( $s['delay'] ),
			'ease'          => $s['ease'],
			'repeat'        => intval( $s['repeat'] ),
			'yoyo'          => 'yes' === $s['yoyo'],
			'scrambleChars' => $s['scramble_chars'],
			'revealDelay'   => floatval( $s['reveal_delay'] ),
			'speed'         => floatval( $s['scramble_speed'] ),
			'newClass'      => $s['new_class'],
			'oldClass'      => $s['old_class'],
		];

		if ( 'yes' === $s['scroll_trigger_enabled'] ) {
			$config['scrollTrigger'] = [
				'start'         => $s['scroll_trigger_start'],
				'end'           => $s['scroll_trigger_end'],
				'scrub'         => 'yes' === $s['scroll_trigger_scrub'],
				'markers'       => 'yes' === $s['scroll_trigger_markers'],
				'toggleActions' => $s['scroll_trigger_toggle_actions'],
			];
		}

		$tag = in_array( $s['html_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ], true )
			? $s['html_tag'] : 'h2';

		echo '<div class="gsap-widget-scramble-text"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<' . $tag . ' class="gsap-scramble-text-target">';
		echo esc_html( $s['text_content'] );
		echo '</' . $tag . '>';
		echo '</div>';
	}
}
