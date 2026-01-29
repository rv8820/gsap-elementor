<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text Typewriter Widget — Uses GSAP TextPlugin for typing effects.
 */
class Widget_Text_Typewriter extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_text_typewriter';
	}

	public function get_title() {
		return esc_html__( 'GSAP Typewriter', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-edit';
	}

	public function get_keywords() {
		return [ 'gsap', 'text', 'type', 'typewriter', 'typing' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'text_content', [
			'label'   => esc_html__( 'Text to Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => 'This text is typed with GSAP!',
			'rows'    => 3,
		] );

		$this->add_control( 'html_tag', [
			'label'   => esc_html__( 'HTML Tag', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'p',
			'options' => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4',
				'h5' => 'H5', 'h6' => 'H6', 'p' => 'p', 'div' => 'div',
			],
		] );

		$this->add_control( 'placeholder_text', [
			'label'       => esc_html__( 'Placeholder Text', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'Text shown before animation starts (leave empty for blank)', 'gsap-elementor' ),
		] );

		$this->end_controls_section();

		// --- Typewriter Settings ---
		$this->start_controls_section( 'section_typewriter', [
			'label' => esc_html__( 'Typewriter Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'type_speed', [
			'label'   => esc_html__( 'Duration (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 2,
			'min'     => 0.1,
			'max'     => 20,
			'step'    => 0.1,
		] );

		$this->add_control( 'type_delay', [
			'label'   => esc_html__( 'Delay (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0.5,
			'min'     => 0,
			'max'     => 10,
			'step'    => 0.1,
		] );

		$this->add_control( 'type_ease', [
			'label'   => esc_html__( 'Easing', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'none',
			'options' => $this->get_easing_options(),
		] );

		$this->add_control( 'type_delimiter', [
			'label'       => esc_html__( 'Delimiter', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'Leave empty for character-by-character. Use space for word-by-word.', 'gsap-elementor' ),
		] );

		$this->add_control( 'show_cursor', [
			'label'   => esc_html__( 'Show Cursor', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'cursor_char', [
			'label'     => esc_html__( 'Cursor Character', 'gsap-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '|',
			'condition' => [ 'show_cursor' => 'yes' ],
		] );

		$this->add_control( 'loop_enabled', [
			'label'   => esc_html__( 'Loop', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'loop_delay', [
			'label'     => esc_html__( 'Loop Delay (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 1,
			'min'       => 0,
			'max'       => 10,
			'step'      => 0.1,
			'condition' => [ 'loop_enabled' => 'yes' ],
		] );

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
			'selector' => '{{WRAPPER}} .gsap-typewriter-target',
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .gsap-typewriter-target' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'cursor_color', [
			'label'     => esc_html__( 'Cursor Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .gsap-typewriter-cursor' => 'color: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'text'       => $s['text_content'],
			'duration'   => floatval( $s['type_speed'] ),
			'delay'      => floatval( $s['type_delay'] ),
			'ease'       => $s['type_ease'],
			'delimiter'  => $s['type_delimiter'],
			'cursor'     => 'yes' === $s['show_cursor'],
			'cursorChar' => $s['cursor_char'],
			'loop'       => 'yes' === $s['loop_enabled'],
			'loopDelay'  => floatval( $s['loop_delay'] ?? 1 ),
		];

		if ( 'yes' === $s['scroll_trigger_enabled'] ) {
			$config['scrollTrigger'] = [
				'start'         => $s['scroll_trigger_start'],
				'end'           => $s['scroll_trigger_end'],
				'toggleActions' => $s['scroll_trigger_toggle_actions'],
				'markers'       => 'yes' === $s['scroll_trigger_markers'],
			];
		}

		$tag = in_array( $s['html_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ], true )
			? $s['html_tag'] : 'p';

		echo '<div class="gsap-widget-text-typewriter"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<' . $tag . ' class="gsap-typewriter-target">';
		echo esc_html( $s['placeholder_text'] );
		echo '</' . $tag . '>';
		if ( 'yes' === $s['show_cursor'] ) {
			echo '<span class="gsap-typewriter-cursor">' . esc_html( $s['cursor_char'] ) . '</span>';
		}
		echo '</div>';
	}
}
