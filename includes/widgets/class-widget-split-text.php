<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SplitText Widget — Split text into chars, words, or lines and animate them.
 */
class Widget_Split_Text extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_split_text';
	}

	public function get_title() {
		return esc_html__( 'GSAP SplitText', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	public function get_keywords() {
		return [ 'gsap', 'split', 'text', 'chars', 'words', 'lines', 'animate' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'text_content', [
			'label'   => esc_html__( 'Text', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => 'Animate every character with GSAP SplitText',
			'rows'    => 4,
		] );

		$this->add_control( 'html_tag', [
			'label'   => esc_html__( 'HTML Tag', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'p'    => 'p',
				'div'  => 'div',
				'span' => 'span',
			],
		] );

		$this->end_controls_section();

		// --- Split Settings ---
		$this->start_controls_section( 'section_split', [
			'label' => esc_html__( 'Split Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'split_type', [
			'label'   => esc_html__( 'Split Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'chars',
			'options' => [
				'chars'       => 'Characters',
				'words'       => 'Words',
				'lines'       => 'Lines',
				'chars,words' => 'Characters & Words',
				'words,lines' => 'Words & Lines',
				'chars,words,lines' => 'All',
			],
		] );

		$this->add_control( 'animate_target', [
			'label'   => esc_html__( 'Animate', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'chars',
			'options' => [
				'chars' => 'Characters',
				'words' => 'Words',
				'lines' => 'Lines',
			],
		] );

		$this->add_control( 'mask_enabled', [
			'label'       => esc_html__( 'Mask (Clip Overflow)', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Clips overflow on each line for reveal effects', 'gsap-elementor' ),
		] );

		$this->end_controls_section();

		// --- Animation ---
		$this->start_controls_section( 'section_animation', [
			'label' => esc_html__( 'Animation', 'gsap-elementor' ),
		] );

		$this->add_control( 'anim_preset', [
			'label'   => esc_html__( 'Preset', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'custom',
			'options' => [
				'custom'     => 'Custom',
				'fade_up'    => 'Fade Up',
				'fade_down'  => 'Fade Down',
				'fade_left'  => 'Fade Left',
				'fade_right' => 'Fade Right',
				'scale_up'   => 'Scale Up',
				'rotate_in'  => 'Rotate In',
				'blur_in'    => 'Blur In',
			],
		] );

		$this->register_animation_controls();

		$this->end_controls_section();

		// --- Custom Transform ---
		$this->start_controls_section( 'section_transform', [
			'label'     => esc_html__( 'Custom Transform', 'gsap-elementor' ),
			'condition' => [ 'anim_preset' => 'custom' ],
		] );

		$this->register_transform_controls();

		$this->add_control( 'filter_blur', [
			'label'   => esc_html__( 'Blur (px)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0,
			'min'     => 0,
			'max'     => 50,
		] );

		$this->end_controls_section();

		// --- Stagger ---
		$this->start_controls_section( 'section_stagger', [
			'label' => esc_html__( 'Stagger', 'gsap-elementor' ),
		] );

		$this->add_control( 'stagger_amount', [
			'label'   => esc_html__( 'Stagger Amount (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0.03,
			'min'     => 0,
			'max'     => 2,
			'step'    => 0.01,
		] );

		$this->add_control( 'stagger_from', [
			'label'   => esc_html__( 'Stagger From', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'start',
			'options' => [
				'start'  => 'Start',
				'end'    => 'End',
				'center' => 'Center',
				'edges'  => 'Edges',
				'random' => 'Random',
			],
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
			'selector' => '{{WRAPPER}} .gsap-split-text-target',
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .gsap-split-text-target' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'text_align', [
			'label'   => esc_html__( 'Alignment', 'gsap-elementor' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'left'   => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
			],
			'selectors' => [
				'{{WRAPPER}} .gsap-split-text-target' => 'text-align: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'splitType'     => $s['split_type'],
			'animateTarget' => $s['animate_target'],
			'mask'          => 'yes' === $s['mask_enabled'],
			'preset'        => $s['anim_preset'],
			'duration'      => floatval( $s['duration'] ),
			'delay'         => floatval( $s['delay'] ),
			'ease'          => $s['ease'],
			'repeat'        => intval( $s['repeat'] ),
			'yoyo'          => 'yes' === $s['yoyo'],
			'stagger'       => [
				'each' => floatval( $s['stagger_amount'] ),
				'from' => $s['stagger_from'],
			],
		];

		if ( 'custom' === $s['anim_preset'] ) {
			$config['transform'] = [
				'x'        => floatval( $s['x'] ),
				'y'        => floatval( $s['y'] ),
				'rotation' => floatval( $s['rotation'] ),
				'scale'    => floatval( $s['scale'] ),
				'opacity'  => floatval( $s['opacity']['size'] ?? 1 ),
				'blur'     => floatval( $s['filter_blur'] ),
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

		$tag = in_array( $s['html_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' ], true )
			? $s['html_tag'] : 'h2';

		echo '<div class="gsap-widget-split-text"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<' . $tag . ' class="gsap-split-text-target">';
		echo esc_html( $s['text_content'] );
		echo '</' . $tag . '>';
		echo '</div>';
	}
}
