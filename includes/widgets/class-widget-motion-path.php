<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MotionPath Widget — Animate an element along an SVG path.
 */
class Widget_Motion_Path extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_motion_path';
	}

	public function get_title() {
		return esc_html__( 'GSAP MotionPath', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-route';
	}

	public function get_keywords() {
		return [ 'gsap', 'motion', 'path', 'follow', 'curve', 'bezier' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Moving Element (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="width:40px;height:40px;background:#ff6b6b;border-radius:50%;"></div>',
		] );

		$this->end_controls_section();

		// --- Path ---
		$this->start_controls_section( 'section_path', [
			'label' => esc_html__( 'Motion Path', 'gsap-elementor' ),
		] );

		$this->add_control( 'path_source', [
			'label'   => esc_html__( 'Path Source', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'preset',
			'options' => [
				'preset' => 'Preset Path',
				'custom' => 'Custom SVG Path',
			],
		] );

		$this->add_control( 'path_preset', [
			'label'     => esc_html__( 'Preset', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'wave',
			'options'   => [
				'wave'    => 'Wave',
				'circle'  => 'Circle',
				'figure8' => 'Figure 8',
				'zigzag'  => 'Zigzag',
				'arc'     => 'Arc',
			],
			'condition' => [ 'path_source' => 'preset' ],
		] );

		$this->add_control( 'path_custom', [
			'label'       => esc_html__( 'SVG Path Data (d attribute)', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => 'M0,100 C50,0 150,200 200,100 C250,0 350,200 400,100',
			'condition'   => [ 'path_source' => 'custom' ],
			'description' => esc_html__( 'Paste the d="" attribute value from an SVG path', 'gsap-elementor' ),
		] );

		$this->add_control( 'show_path', [
			'label'   => esc_html__( 'Show Path', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'path_color', [
			'label'     => esc_html__( 'Path Color', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#cccccc',
			'condition' => [ 'show_path' => 'yes' ],
		] );

		$this->add_control( 'path_width', [
			'label'      => esc_html__( 'Container Width', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [ 'min' => 100, 'max' => 2000 ],
				'%'  => [ 'min' => 10, 'max' => 100 ],
			],
			'default'    => [ 'size' => 400, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-motion-path-container' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'path_height', [
			'label'      => esc_html__( 'Container Height', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 50, 'max' => 1000 ] ],
			'default'    => [ 'size' => 200, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-motion-path-container' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		// --- Motion Settings ---
		$this->start_controls_section( 'section_motion', [
			'label' => esc_html__( 'Motion Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'align_rotation', [
			'label'       => esc_html__( 'Auto-Rotate', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => esc_html__( 'Rotate element to match path direction', 'gsap-elementor' ),
		] );

		$this->add_control( 'align_origin', [
			'label'       => esc_html__( 'Align Origin', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '0.5 0.5',
			'description' => esc_html__( 'x y (0-1) alignment of element on path', 'gsap-elementor' ),
		] );

		$this->add_control( 'path_start_progress', [
			'label'   => esc_html__( 'Start (%)', 'gsap-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default' => [ 'size' => 0 ],
		] );

		$this->add_control( 'path_end_progress', [
			'label'   => esc_html__( 'End (%)', 'gsap-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default' => [ 'size' => 100 ],
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
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$path_data = '';
		if ( 'preset' === $s['path_source'] ) {
			$path_data = $this->get_preset_path( $s['path_preset'] );
		} else {
			$path_data = $s['path_custom'];
		}

		$origin_parts = explode( ' ', trim( $s['align_origin'] ) );

		$config = [
			'pathData'      => $path_data,
			'autoRotate'    => 'yes' === $s['align_rotation'],
			'alignOrigin'   => [
				floatval( $origin_parts[0] ?? 0.5 ),
				floatval( $origin_parts[1] ?? 0.5 ),
			],
			'start'         => floatval( $s['path_start_progress']['size'] ?? 0 ) / 100,
			'end'           => floatval( $s['path_end_progress']['size'] ?? 100 ) / 100,
			'duration'      => floatval( $s['duration'] ),
			'delay'         => floatval( $s['delay'] ),
			'ease'          => $s['ease'],
			'repeat'        => intval( $s['repeat'] ),
			'yoyo'          => 'yes' === $s['yoyo'],
			'showPath'      => 'yes' === $s['show_path'],
			'pathColor'     => $s['path_color'],
		];

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

		echo '<div class="gsap-widget-motion-path"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-motion-path-container" style="position:relative;">';
		if ( 'yes' === $s['show_path'] ) {
			$w = intval( $s['path_width']['size'] ?? 400 );
			$h = intval( $s['path_height']['size'] ?? 200 );
			echo '<svg class="gsap-motion-path-svg" viewBox="0 0 ' . $w . ' ' . $h . '" style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:visible;">';
			echo '<path d="' . esc_attr( $path_data ) . '" fill="none" stroke="' . esc_attr( $s['path_color'] ) . '" stroke-width="2" stroke-dasharray="4 4"/>';
			echo '</svg>';
		}
		echo '<div class="gsap-motion-path-element" style="position:absolute;top:0;left:0;">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	private function get_preset_path( $preset ) {
		$paths = [
			'wave'    => 'M0,100 C50,0 100,200 150,100 C200,0 250,200 300,100 C350,0 400,200 400,100',
			'circle'  => 'M200,100 A100,100 0 1,1 199.99,100',
			'figure8' => 'M200,100 C200,0 300,0 300,100 C300,200 200,200 200,100 C200,0 100,0 100,100 C100,200 200,200 200,100',
			'zigzag'  => 'M0,100 L80,20 L160,180 L240,20 L320,180 L400,100',
			'arc'     => 'M0,180 Q200,-50 400,180',
		];
		return $paths[ $preset ] ?? $paths['wave'];
	}
}
