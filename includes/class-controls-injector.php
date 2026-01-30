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
	 * Grouped easing options with friendly labels.
	 */
	private static function get_easing_options() {
		return [
			'none'           => 'Linear (no easing)',
			'power1.out'    => 'Gentle (ease out)',
			'power2.out'    => 'Smooth (ease out) — recommended',
			'power3.out'    => 'Strong (ease out)',
			'power4.out'    => 'Extra strong (ease out)',
			'power1.inOut'  => 'Gentle (ease in & out)',
			'power2.inOut'  => 'Smooth (ease in & out)',
			'power3.inOut'  => 'Strong (ease in & out)',
			'power1.in'     => 'Gentle (ease in)',
			'power2.in'     => 'Smooth (ease in)',
			'power3.in'     => 'Strong (ease in)',
			'back.out'      => 'Overshoot (ease out)',
			'back.inOut'    => 'Overshoot (in & out)',
			'bounce.out'    => 'Bounce (ease out)',
			'bounce.inOut'  => 'Bounce (in & out)',
			'elastic.out'   => 'Elastic (ease out)',
			'elastic.inOut' => 'Elastic (in & out)',
			'circ.out'      => 'Circular (ease out)',
			'circ.inOut'    => 'Circular (in & out)',
			'expo.out'      => 'Exponential (ease out)',
			'expo.inOut'    => 'Exponential (in & out)',
			'sine.out'      => 'Sine wave (ease out)',
			'sine.inOut'    => 'Sine wave (in & out)',
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
			'label' => esc_html__( '✦ GSAP Animation', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_ADVANCED,
		] );

		$element->add_control( 'gsap_enable', [
			'label'        => esc_html__( 'Enable Animation', 'gsap-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => '',
			'return_value' => 'yes',
			'description'  => esc_html__( 'Turn on to animate this element when it scrolls into view.', 'gsap-elementor' ),
		] );

		// ─── Animation Effect ──────────────────────────────────────────
		$element->add_control( 'gsap_preset', [
			'label'       => esc_html__( 'Animation Effect', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'fade_up',
			'options'     => [
				'fade_up'      => 'Fade Up ↑',
				'fade_down'    => 'Fade Down ↓',
				'fade_left'    => 'Fade Left ←',
				'fade_right'   => 'Fade Right →',
				'zoom_in'      => 'Zoom In',
				'zoom_out'     => 'Zoom Out',
				'rotate_in'    => 'Rotate In',
				'flip_x'       => 'Flip Horizontal',
				'flip_y'       => 'Flip Vertical',
				'blur_in'      => 'Blur In',
				'bounce_in'    => 'Bounce In',
				'slide_masked' => 'Slide Up (Masked)',
				'custom'       => '— Custom Values —',
			],
			'description' => esc_html__( 'Choose a preset animation or "Custom Values" for full control.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		// ─── Custom Transform (only visible when preset = custom) ────
		$element->add_control( 'gsap_heading_custom', [
			'label'     => esc_html__( 'Custom Starting State', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_custom_note', [
			'type'      => Controls_Manager::RAW_HTML,
			'raw'       => '<p style="color:#93003c;font-size:11px;line-height:1.4;">' . esc_html__( 'Set the starting state of the element. It will animate FROM these values TO its normal position.', 'gsap-elementor' ) . '</p>',
			'condition' => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_x', [
			'label'       => esc_html__( 'Move X (horizontal)', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'description' => esc_html__( 'Horizontal offset in pixels. Positive = from right, negative = from left.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_y', [
			'label'       => esc_html__( 'Move Y (vertical)', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'description' => esc_html__( 'Vertical offset in pixels. Positive = from below, negative = from above.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_rotation', [
			'label'       => esc_html__( 'Rotation', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => -360,
			'max'         => 360,
			'description' => esc_html__( 'Starting rotation in degrees.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_scale_x', [
			'label'       => esc_html__( 'Scale X', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 1,
			'min'         => 0,
			'max'         => 10,
			'step'        => 0.1,
			'description' => esc_html__( 'Horizontal scale. 1 = normal size, 0.5 = half, 2 = double.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_scale_y', [
			'label'       => esc_html__( 'Scale Y', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 1,
			'min'         => 0,
			'max'         => 10,
			'step'        => 0.1,
			'description' => esc_html__( 'Vertical scale. 1 = normal size, 0.5 = half, 2 = double.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_opacity', [
			'label'       => esc_html__( 'Starting Opacity', 'gsap-elementor' ),
			'type'        => Controls_Manager::SLIDER,
			'range'       => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'     => [ 'size' => 0 ],
			'description' => esc_html__( '0 = invisible (fades in), 1 = fully visible (no fade).', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_blur', [
			'label'       => esc_html__( 'Blur', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => 0,
			'max'         => 50,
			'description' => esc_html__( 'Starting blur in pixels. 0 = sharp, higher = more blurred.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_skew_x', [
			'label'       => esc_html__( 'Skew X', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => -90,
			'max'         => 90,
			'description' => esc_html__( 'Horizontal skew in degrees.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		$element->add_control( 'gsap_skew_y', [
			'label'       => esc_html__( 'Skew Y', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => -90,
			'max'         => 90,
			'description' => esc_html__( 'Vertical skew in degrees.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_preset' => 'custom' ],
		] );

		// ─── Timing ────────────────────────────────────────────────────
		$element->add_control( 'gsap_heading_timing', [
			'label'     => esc_html__( 'Timing', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_duration', [
			'label'       => esc_html__( 'Duration', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 1,
			'min'         => 0,
			'max'         => 20,
			'step'        => 0.1,
			'description' => esc_html__( 'How long the animation takes, in seconds.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_delay', [
			'label'       => esc_html__( 'Delay', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => 0,
			'max'         => 20,
			'step'        => 0.1,
			'description' => esc_html__( 'Wait time before animation starts, in seconds.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_ease', [
			'label'       => esc_html__( 'Easing', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'power2.out',
			'options'     => self::get_easing_options(),
			'description' => esc_html__( 'Controls the acceleration curve. "Ease out" starts fast and slows down. "Ease in" starts slow and speeds up.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_repeat', [
			'label'       => esc_html__( 'Repeat', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => -1,
			'max'         => 100,
			'description' => esc_html__( '0 = play once. Set -1 to loop forever.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_yoyo', [
			'label'       => esc_html__( 'Yoyo (reverse on repeat)', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'When repeating, play the animation backwards every other cycle.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		// ─── Stagger (animate children one by one) ───────────────────
		$element->add_control( 'gsap_heading_stagger', [
			'label'     => esc_html__( 'Stagger Children', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_enable', [
			'label'       => esc_html__( 'Animate children one by one', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Instead of animating the whole element, animate each child element with a delay between them.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_target', [
			'label'       => esc_html__( 'Target Selector', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '> *',
			'description' => esc_html__( 'CSS selector for which children to animate. Default "> *" targets all direct children.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_amount', [
			'label'       => esc_html__( 'Delay Between Each', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0.15,
			'min'         => 0,
			'max'         => 5,
			'step'        => 0.01,
			'description' => esc_html__( 'Time in seconds between each child starting its animation.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_stagger_from', [
			'label'       => esc_html__( 'Start From', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'start',
			'options'     => [
				'start'  => 'First child',
				'end'    => 'Last child',
				'center' => 'Center outward',
				'edges'  => 'Edges inward',
				'random' => 'Random order',
			],
			'description' => esc_html__( 'Which child animates first.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_stagger_enable' => 'yes' ],
		] );

		// ─── Scroll Trigger ─────────────────────────────────────────────
		$element->add_control( 'gsap_heading_scroll', [
			'label'     => esc_html__( 'Scroll Settings', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_note', [
			'type'      => Controls_Manager::RAW_HTML,
			'raw'       => '<p style="color:#555;font-size:11px;line-height:1.4;">' . esc_html__( 'By default, the animation plays when this element scrolls into view.', 'gsap-elementor' ) . '</p>',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_trigger', [
			'label'       => esc_html__( 'Trigger on scroll into view', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => esc_html__( 'Play the animation when the element enters the viewport. Turn off to play immediately on page load.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_start', [
			'label'       => esc_html__( 'Start Position', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'top 85%',
			'description' => esc_html__( 'When to start. "top 85%" means: when the top of the element reaches 85% from the top of the viewport.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_end', [
			'label'       => esc_html__( 'End Position', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'bottom 20%',
			'description' => esc_html__( 'When to end (only matters for scrub/pin). "bottom 20%" means: when the bottom of the element reaches 20% from the top.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_scrub', [
			'label'       => esc_html__( 'Scrub', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'false',
			'options'     => [
				'false' => 'Off — play once when triggered',
				'true'  => 'On — animation follows scroll position',
				'0.5'   => 'Smooth (0.5s catch-up)',
				'1'     => 'Smooth (1s catch-up)',
				'2'     => 'Smooth (2s catch-up)',
			],
			'description' => esc_html__( 'When on, the animation progress is tied to scroll position instead of playing through.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_pin', [
			'label'       => esc_html__( 'Pin element while scrolling', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Fix the element in place while the animation plays during scroll.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_toggle_actions', [
			'label'       => esc_html__( 'Toggle Actions', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'play none none none',
			'options'     => [
				'play none none none'      => 'Play once',
				'play none none reverse'   => 'Play on enter, reverse on leave',
				'play reverse play reverse' => 'Play/reverse on every scroll',
				'play pause resume reset'  => 'Pause when out of view, reset on leave',
				'restart none none none'   => 'Restart every time',
			],
			'description' => esc_html__( 'What happens when scrolling in and out of the trigger area.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		$element->add_control( 'gsap_scroll_markers', [
			'label'       => esc_html__( 'Show Debug Markers', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Display start/end markers on the page for debugging. Remember to turn this off before publishing.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_scroll_trigger' => 'yes' ],
		] );

		// ─── SplitText (text elements) ──────────────────────────────────
		$element->add_control( 'gsap_heading_split', [
			'label'     => esc_html__( 'Text Splitting', 'gsap-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_enable', [
			'label'       => esc_html__( 'Animate text by character/word/line', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Split text content and animate each piece individually. Works best on headings and paragraphs.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_type', [
			'label'       => esc_html__( 'Split Into', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'chars',
			'options'     => [
				'chars'             => 'Characters',
				'words'             => 'Words',
				'lines'             => 'Lines',
				'chars,words'       => 'Characters & Words',
				'words,lines'       => 'Words & Lines',
				'chars,words,lines' => 'All (characters, words & lines)',
			],
			'description' => esc_html__( 'How to break apart the text.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_animate', [
			'label'       => esc_html__( 'Animate Each', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'chars',
			'options'     => [
				'chars' => 'Character',
				'words' => 'Word',
				'lines' => 'Line',
			],
			'description' => esc_html__( 'Which pieces to animate. Must be included in "Split Into" above.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_selector', [
			'label'       => esc_html__( 'Text Element Selector', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'CSS selector for the text element to split. Leave empty to auto-detect the first heading or paragraph.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
		] );

		$element->add_control( 'gsap_split_stagger', [
			'label'       => esc_html__( 'Stagger Delay', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0.03,
			'min'         => 0,
			'max'         => 2,
			'step'        => 0.01,
			'description' => esc_html__( 'Time in seconds between each character/word/line animating in.', 'gsap-elementor' ),
			'condition'   => [ 'gsap_enable' => 'yes', 'gsap_split_enable' => 'yes' ],
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

		// ScrollTrigger — enabled by default
		$scroll_enabled = $settings['gsap_scroll_trigger'] ?? 'yes';
		if ( 'yes' === $scroll_enabled ) {
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
