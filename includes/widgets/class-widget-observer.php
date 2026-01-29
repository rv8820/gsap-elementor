<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Observer Widget — Responds to scroll, touch, pointer events with animations.
 * Useful for full-page section transitions, gesture-driven UIs.
 */
class Widget_Observer extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_observer';
	}

	public function get_title() {
		return esc_html__( 'GSAP Observer', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-preview-medium';
	}

	public function get_keywords() {
		return [ 'gsap', 'observer', 'gesture', 'swipe', 'touch', 'scroll', 'section' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Content (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="height:300px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">Scroll or swipe to animate</div>',
		] );

		$this->end_controls_section();

		// --- Observer Settings ---
		$this->start_controls_section( 'section_observer', [
			'label' => esc_html__( 'Observer Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'event_type', [
			'label'   => esc_html__( 'Event Types', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT2,
			'default' => [ 'wheel', 'touch', 'pointer' ],
			'options' => [
				'wheel'   => 'Wheel (Scroll)',
				'touch'   => 'Touch',
				'pointer' => 'Pointer',
				'scroll'  => 'Scroll',
			],
			'multiple' => true,
		] );

		$this->add_control( 'axis', [
			'label'   => esc_html__( 'Axis', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'y',
			'options' => [
				'y' => 'Vertical',
				'x' => 'Horizontal',
			],
		] );

		$this->add_control( 'tolerance', [
			'label'       => esc_html__( 'Tolerance (px)', 'gsap-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 10,
			'min'         => 0,
			'max'         => 200,
			'description' => esc_html__( 'Minimum drag/scroll distance to trigger', 'gsap-elementor' ),
		] );

		$this->add_control( 'prevent_default', [
			'label'   => esc_html__( 'Prevent Default', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->end_controls_section();

		// --- Animation ---
		$this->start_controls_section( 'section_animation', [
			'label' => esc_html__( 'Animation on Event', 'gsap-elementor' ),
		] );

		$this->add_control( 'anim_up', [
			'label'   => esc_html__( 'On Up / Left', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'slide_up',
			'options' => [
				'none'         => 'None',
				'slide_up'     => 'Slide Up',
				'slide_left'   => 'Slide Left',
				'scale_down'   => 'Scale Down',
				'fade_out'     => 'Fade Out',
				'rotate_left'  => 'Rotate Left',
			],
		] );

		$this->add_control( 'anim_down', [
			'label'   => esc_html__( 'On Down / Right', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'slide_down',
			'options' => [
				'none'          => 'None',
				'slide_down'    => 'Slide Down',
				'slide_right'   => 'Slide Right',
				'scale_up'      => 'Scale Up',
				'fade_in'       => 'Fade In',
				'rotate_right'  => 'Rotate Right',
			],
		] );

		$this->register_animation_controls();

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'eventTypes'     => $s['event_type'],
			'axis'           => $s['axis'],
			'tolerance'      => intval( $s['tolerance'] ),
			'preventDefault' => 'yes' === $s['prevent_default'],
			'animUp'         => $s['anim_up'],
			'animDown'       => $s['anim_down'],
			'duration'       => floatval( $s['duration'] ),
			'ease'           => $s['ease'],
		];

		echo '<div class="gsap-widget-observer"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-observer-target">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
	}
}
