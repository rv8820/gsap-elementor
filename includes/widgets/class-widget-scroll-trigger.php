<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ScrollTrigger Widget — Dedicated scroll-based animation widget.
 * Pin elements, scrub animations, batch triggers, and parallax.
 */
class Widget_Scroll_Trigger extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_scroll_trigger';
	}

	public function get_title() {
		return esc_html__( 'GSAP ScrollTrigger', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-scroll';
	}

	public function get_keywords() {
		return [ 'gsap', 'scroll', 'trigger', 'parallax', 'pin', 'scrub' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Content (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="width:100%;height:300px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">Scroll to animate</div>',
		] );

		$this->end_controls_section();

		// --- Scroll Trigger Config ---
		$this->start_controls_section( 'section_trigger', [
			'label' => esc_html__( 'Trigger Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'trigger_start', [
			'label'   => esc_html__( 'Start', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'top 80%',
		] );

		$this->add_control( 'trigger_end', [
			'label'   => esc_html__( 'End', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'bottom 20%',
		] );

		$this->add_control( 'trigger_scrub', [
			'label'   => esc_html__( 'Scrub', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'false',
			'options' => [
				'false' => 'Off',
				'true'  => 'On',
				'0.5'   => '0.5s smooth',
				'1'     => '1s smooth',
				'2'     => '2s smooth',
			],
		] );

		$this->add_control( 'trigger_pin', [
			'label'   => esc_html__( 'Pin Element', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'trigger_pin_spacing', [
			'label'     => esc_html__( 'Pin Spacing', 'gsap-elementor' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'trigger_pin' => 'yes' ],
		] );

		$this->add_control( 'trigger_toggle_actions', [
			'label'   => esc_html__( 'Toggle Actions', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'play none none none',
		] );

		$this->add_control( 'trigger_markers', [
			'label'   => esc_html__( 'Debug Markers', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->end_controls_section();

		// --- Animation ---
		$this->start_controls_section( 'section_animation', [
			'label' => esc_html__( 'Animation', 'gsap-elementor' ),
		] );

		$this->add_control( 'anim_type', [
			'label'   => esc_html__( 'Animation Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'custom',
			'options' => [
				'custom'    => 'Custom Transform',
				'parallax'  => 'Parallax',
				'fade_in'   => 'Fade In',
				'slide_up'  => 'Slide Up',
				'slide_left' => 'Slide Left',
				'zoom_in'   => 'Zoom In',
			],
		] );

		$this->add_control( 'parallax_speed', [
			'label'     => esc_html__( 'Parallax Speed', 'gsap-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [ 'size' => 100 ],
			'range'     => [ 'px' => [ 'min' => -500, 'max' => 500 ] ],
			'condition' => [ 'anim_type' => 'parallax' ],
		] );

		$this->register_animation_controls();

		$this->end_controls_section();

		// --- Custom Transform ---
		$this->start_controls_section( 'section_transform', [
			'label'     => esc_html__( 'Custom Transform', 'gsap-elementor' ),
			'condition' => [ 'anim_type' => 'custom' ],
		] );

		$this->register_transform_controls();

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$scrub = $s['trigger_scrub'];
		if ( 'true' === $scrub ) {
			$scrub = true;
		} elseif ( 'false' === $scrub ) {
			$scrub = false;
		} else {
			$scrub = floatval( $scrub );
		}

		$config = [
			'animType'     => $s['anim_type'],
			'duration'     => floatval( $s['duration'] ),
			'delay'        => floatval( $s['delay'] ),
			'ease'         => $s['ease'],
			'repeat'       => intval( $s['repeat'] ),
			'yoyo'         => 'yes' === $s['yoyo'],
			'scrollTrigger' => [
				'start'         => $s['trigger_start'],
				'end'           => $s['trigger_end'],
				'scrub'         => $scrub,
				'pin'           => 'yes' === $s['trigger_pin'],
				'pinSpacing'    => 'yes' === $s['trigger_pin_spacing'],
				'markers'       => 'yes' === $s['trigger_markers'],
				'toggleActions' => $s['trigger_toggle_actions'],
			],
		];

		if ( 'parallax' === $s['anim_type'] ) {
			$config['parallaxSpeed'] = floatval( $s['parallax_speed']['size'] ?? 100 );
		} elseif ( 'custom' === $s['anim_type'] ) {
			$config['transform'] = [
				'x'        => floatval( $s['x'] ),
				'y'        => floatval( $s['y'] ),
				'rotation' => floatval( $s['rotation'] ),
				'scale'    => floatval( $s['scale'] ),
				'opacity'  => floatval( $s['opacity']['size'] ?? 1 ),
			];
		}

		echo '<div class="gsap-widget-scroll-trigger"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-scroll-trigger-target">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
	}
}
