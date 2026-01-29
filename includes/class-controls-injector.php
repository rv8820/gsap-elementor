<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Injects a "GSAP Animation" advanced tab into ALL Elementor widgets,
 * sections, containers, and columns. This lets users animate any element
 * — including third-party widgets — directly from the Elementor panel.
 *
 * The injected controls write data-gsap-anim='{ JSON }' onto the widget
 * wrapper, which frontend.js picks up and applies.
 */
class Controls_Injector {

	/**
	 * Hook into Elementor to register controls on every element.
	 */
	public static function init() {
		// Inject after the Advanced → Custom CSS section (or at the end of Advanced).
		add_action( 'elementor/element/common/_section_style/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );
		// Elementor containers (flexbox layout — Elementor 3.6+)
		add_action( 'elementor/element/container/section_layout/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );

		// Render the data attribute on the frontend.
		add_action( 'elementor/frontend/widget/before_render', [ __CLASS__, 'before_render' ] );
		add_action( 'elementor/frontend/section/before_render', [ __CLASS__, 'before_render' ] );
		add_action( 'elementor/frontend/column/before_render', [ __CLASS__, 'before_render' ] );
		add_action( 'elementor/frontend/container/before_render', [ __CLASS__, 'before_render' ] );
	}

	/**
	 * Easing options (static copy — avoids widget instance dependency).
	 */
	private static function get_easing_options() {
		return [
			'none'           => 'none (linear)',
			'power1.in'     => 'power1.in',
			'power1.out'    => 'power1.out',
			'power1.inOut'  => 'power1.inOut',
			'power2.in'     => 'power2.in',
			'power2.out'    => 'power2.out',
			'power2.inOut'  => 'power2.inOut',
			'power3.in'     => 'power3.in',
			'power3.out'    => 'power3.out',
			'power3.inOut'  => 'power3.inOut',
			'power4.in'     => 'power4.in',
			'power4.out'    => 'power4.out',
			'power4.inOut'  => 'power4.inOut',
			'back.in'       => 'back.in',
			'back.out'      => 'back.out',
			'back.inOut'    => 'back.inOut',
			'bounce.in'     => 'bounce.in',
			'bounce.out'    => 'bounce.out',
			'bounce.inOut'  => 'bounce.inOut',
			'circ.in'       => 'circ.in',
			'circ.out'      => 'circ.out',
			'circ.inOut'    => 'circ.inOut',
			'elastic.in'    => 'elastic.in',
			'elastic.out'   => 'elastic.out',
			'elastic.inOut' => 'elastic.inOut',
			'expo.in'       => 'expo.in',
			'expo.out'      => 'expo.out',
			'expo.inOut'    => 'expo.inOut',
			'sine.in'       => 'sine.in',
			'sine.out'      => 'sine.out',
			'sine.inOut'    => 'sine.inOut',
		];
	}

	/**
	 * Register the GSAP Animation controls on the given element.
	 *
	 * @param Element_Base $element
	 */
	public static function register_controls( $element ) {

		// ─── Main Section ───────────────────────────────────────────────
		$element->start_controls_section( 'gsap_animation_section', [
			'label' => esc_html__( 'GSAP Animation', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_ADVANCED,
		] );

		$element->add_control( 'gsap_enable', [
			'label'        => esc_html__( 'Enable GSAP Animation', 'gsap-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => '',
			'return_value' => 'yes',
		] );

		// ─── Animation Preset ───────────────────────────────────────────
		$element->add_control( 'gsap_preset', [
			'label'     => esc_html__( 'Animation Preset', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'fade_up',
			'options'   => [
				'fade_up'      => 'Fade Up',
				'fade_down'    => 'Fade Down',
				'fade_left'    => 'Fade Left',
				'fade_right'   => 'Fade Right',
				'zoom_in'      => 'Zoom In',
				'zoom_out'     => 'Zoom Out',
				'rotate_in'    => 'Rotate In',
				'flip_x'       => 'Flip X',
				'flip_y'       => 'Flip Y',
				'blur_in'      => 'Blur In',
				'bounce_in'    => 'Bounce In',
				'slide_masked' => 'Slide (Masked)',
				'custom'       => 'Custom',
			],
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		// ─── Custom Transform ────────────────────────────────────────────
		$element->add_control( 'gsap_heading_custom', [
			'label'     => esc_html__( 'Custom "From" Values', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_x', [
			'label'     => esc_html__( 'X (px)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_y', [
			'label'     => esc_html__( 'Y (px)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_rotation', [
			'label'     => esc_html__( 'Rotation (deg)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'min'       => -360,
			'max'       => 360,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_scale_x', [
			'label'     => esc_html__( 'Scale X', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 1,
			'min'       => 0,
			'max'       => 10,
			'step'      => 0.1,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_scale_y', [
			'label'     => esc_html__( 'Scale Y', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 1,
			'min'       => 0,
			'max'       => 10,
			'step'      => 0.1,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_opacity', [
			'label'     => esc_html__( 'Opacity', 'gsap-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'   => [ 'size' => 0 ],
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_blur', [
			'label'     => esc_html__( 'Blur (px)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'min'       => 0,
			'max'       => 50,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_skew_x', [
			'label'     => esc_html__( 'Skew X (deg)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'min'       => -90,
			'max'       => 90,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_skew_y', [
			'label'     => esc_html__( 'Skew Y (deg)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'min'       => -90,
			'max'       => 90,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		// ─── Timing ────────────────────────────────────────────────────
		$element->add_control( 'gsap_heading_timing', [
			'label'     => esc_html__( 'Timing', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_duration', [
			'label'     => esc_html__( 'Duration (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 1,
			'min'       => 0,
			'max'       => 20,
			'step'      => 0.1,
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_delay', [
			'label'     => esc_html__( 'Delay (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0,
			'min'       => 0,
			'max'       => 20,
			'step'      => 0.1,
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_ease', [
			'label'     => esc_html__( 'Easing', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'power2.out',
			'options'   => self::get_easing_options(),
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_repeat', [
			'label'       => esc_html__( 'Repeat', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => -1,
			'max'         => 100,
			'description' => esc_html__( '-1 for infinite', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_yoyo', [
			'label'     => esc_html__( 'Yoyo', 'gsap-elementor' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => '',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		// ─── Stagger (for child elements) ─────────────────────────────
		$element->add_control( 'gsap_heading_stagger', [
			'label'     => esc_html__( 'Stagger', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_enable', [
			'label'       => esc_html__( 'Stagger Children', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Animate direct children instead of the element itself', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_target', [
			'label'       => esc_html__( 'Stagger Selector', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '> *',
			'description' => esc_html__( 'CSS selector relative to this element (default: direct children)', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_amount', [
			'label'     => esc_html__( 'Stagger Amount (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0.15,
			'min'       => 0,
			'max'       => 5,
			'step'      => 0.01,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_from', [
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
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		// ─── ScrollTrigger ──────────────────────────────────────────────
		$element->add_control( 'gsap_heading_scroll', [
			'label'     => esc_html__( 'ScrollTrigger', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_trigger', [
			'label'     => esc_html__( 'Enable ScrollTrigger', 'gsap-elementor' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_start', [
			'label'     => esc_html__( 'Start', 'gsap-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'top 85%',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_end', [
			'label'     => esc_html__( 'End', 'gsap-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'bottom 20%',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_scrub', [
			'label'     => esc_html__( 'Scrub', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'false',
			'options'   => [
				'false' => 'Off',
				'true'  => 'On',
				'0.5'   => '0.5s smooth',
				'1'     => '1s smooth',
				'2'     => '2s smooth',
			],
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_pin', [
			'label'     => esc_html__( 'Pin', 'gsap-elementor' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => '',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_toggle_actions', [
			'label'       => esc_html__( 'Toggle Actions', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'play none none none',
			'description' => esc_html__( 'onEnter onLeave onEnterBack onLeaveBack', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_markers', [
			'label'     => esc_html__( 'Debug Markers', 'gsap-elementor' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => '',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		// ─── SplitText (text elements) ──────────────────────────────────
		$element->add_control( 'gsap_heading_split', [
			'label'     => esc_html__( 'SplitText', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_enable', [
			'label'       => esc_html__( 'Enable SplitText', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Splits text into chars/words/lines and animates each one', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_type', [
			'label'     => esc_html__( 'Split Type', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'chars',
			'options'   => [
				'chars'             => 'Characters',
				'words'             => 'Words',
				'lines'             => 'Lines',
				'chars,words'       => 'Characters & Words',
				'words,lines'       => 'Words & Lines',
				'chars,words,lines' => 'All',
			],
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_animate', [
			'label'     => esc_html__( 'Animate', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'chars',
			'options'   => [
				'chars' => 'Characters',
				'words' => 'Words',
				'lines' => 'Lines',
			],
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_selector', [
			'label'       => esc_html__( 'Text Selector', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'CSS selector for the text element (leave empty = first heading/paragraph found)', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_stagger', [
			'label'     => esc_html__( 'Stagger (s)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 0.03,
			'min'       => 0,
			'max'       => 2,
			'step'      => 0.01,
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->end_controls_section();
	}

	/**
	 * Before render: add data-gsap-anim attribute to the element wrapper.
	 *
	 * @param Element_Base $element
	 */
	public static function before_render( $element ) {
		$settings = $element->get_settings_for_display();

		if ( empty( $settings['gsap_enable'] ) || 'yes' !== $settings['gsap_enable'] ) {
			return;
		}

		$config = [
			'preset'   => $settings['gsap_preset'] ?? 'fade_up',
			'duration' => floatval( $settings['gsap_duration'] ?? 1 ),
			'delay'    => floatval( $settings['gsap_delay'] ?? 0 ),
			'ease'     => $settings['gsap_ease'] ?? 'power2.out',
			'repeat'   => intval( $settings['gsap_repeat'] ?? 0 ),
			'yoyo'     => 'yes' === ( $settings['gsap_yoyo'] ?? '' ),
		];

		// Custom transform values
		if ( 'custom' === $config['preset'] ) {
			$config['custom'] = [
				'x'        => floatval( $settings['gsap_x'] ?? 0 ),
				'y'        => floatval( $settings['gsap_y'] ?? 0 ),
				'rotation' => floatval( $settings['gsap_rotation'] ?? 0 ),
				'scaleX'   => floatval( $settings['gsap_scale_x'] ?? 1 ),
				'scaleY'   => floatval( $settings['gsap_scale_y'] ?? 1 ),
				'opacity'  => floatval( $settings['gsap_opacity']['size'] ?? 0 ),
				'blur'     => floatval( $settings['gsap_blur'] ?? 0 ),
				'skewX'    => floatval( $settings['gsap_skew_x'] ?? 0 ),
				'skewY'    => floatval( $settings['gsap_skew_y'] ?? 0 ),
			];
		}

		// Stagger
		if ( 'yes' === ( $settings['gsap_stagger_enable'] ?? '' ) ) {
			$config['stagger'] = [
				'target' => $settings['gsap_stagger_target'] ?? '> *',
				'each'   => floatval( $settings['gsap_stagger_amount'] ?? 0.15 ),
				'from'   => $settings['gsap_stagger_from'] ?? 'start',
			];
		}

		// ScrollTrigger
		if ( 'yes' === ( $settings['gsap_scroll_trigger'] ?? '' ) ) {
			$scrub = $settings['gsap_scroll_scrub'] ?? 'false';
			if ( 'true' === $scrub ) {
				$scrub = true;
			} elseif ( 'false' === $scrub ) {
				$scrub = false;
			} else {
				$scrub = floatval( $scrub );
			}

			$config['scrollTrigger'] = [
				'start'         => $settings['gsap_scroll_start'] ?? 'top 85%',
				'end'           => $settings['gsap_scroll_end'] ?? 'bottom 20%',
				'scrub'         => $scrub,
				'pin'           => 'yes' === ( $settings['gsap_scroll_pin'] ?? '' ),
				'markers'       => 'yes' === ( $settings['gsap_scroll_markers'] ?? '' ),
				'toggleActions' => $settings['gsap_scroll_toggle_actions'] ?? 'play none none none',
			];
		}

		// SplitText
		if ( 'yes' === ( $settings['gsap_split_enable'] ?? '' ) ) {
			$config['splitText'] = [
				'type'     => $settings['gsap_split_type'] ?? 'chars',
				'animate'  => $settings['gsap_split_animate'] ?? 'chars',
				'selector' => $settings['gsap_split_selector'] ?? '',
				'stagger'  => floatval( $settings['gsap_split_stagger'] ?? 0.03 ),
			];
		}

		$element->add_render_attribute( '_wrapper', [
			'data-gsap-anim' => wp_json_encode( $config ),
		] );
	}
}
