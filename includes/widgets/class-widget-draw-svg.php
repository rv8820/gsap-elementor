<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DrawSVG Widget — Progressively reveal or hide SVG strokes.
 */
class Widget_Draw_SVG extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_draw_svg';
	}

	public function get_title() {
		return esc_html__( 'GSAP DrawSVG', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-shape';
	}

	public function get_keywords() {
		return [ 'gsap', 'draw', 'svg', 'stroke', 'line', 'path' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'SVG Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'svg_source', [
			'label'   => esc_html__( 'SVG Source', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'code',
			'options' => [
				'code'   => 'Custom SVG Code',
				'preset' => 'Preset Shape',
			],
		] );

		$this->add_control( 'svg_preset', [
			'label'     => esc_html__( 'Preset', 'gsap-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'circle',
			'options'   => [
				'circle'    => 'Circle',
				'square'    => 'Square',
				'triangle'  => 'Triangle',
				'star'      => 'Star',
				'checkmark' => 'Checkmark',
				'heart'     => 'Heart',
			],
			'condition' => [ 'svg_source' => 'preset' ],
		] );

		$this->add_control( 'svg_code', [
			'label'       => esc_html__( 'SVG Code', 'gsap-elementor' ),
			'type'        => Controls_Manager::CODE,
			'language'    => 'html',
			'default'     => '<svg viewBox="0 0 200 200" width="200" height="200"><circle cx="100" cy="100" r="80" fill="none" stroke="#0ae448" stroke-width="4"/></svg>',
			'condition'   => [ 'svg_source' => 'code' ],
		] );

		$this->add_control( 'svg_width', [
			'label'      => esc_html__( 'Width', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [ 'min' => 50, 'max' => 1000 ],
				'%'  => [ 'min' => 10, 'max' => 100 ],
			],
			'default'    => [ 'size' => 200, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-draw-svg-target svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		// --- Draw Settings ---
		$this->start_controls_section( 'section_draw', [
			'label' => esc_html__( 'Draw Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'draw_from', [
			'label'       => esc_html__( 'Draw From', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '0%',
			'description' => esc_html__( 'Start position: 0%, 50%, or pixel value', 'gsap-elementor' ),
		] );

		$this->add_control( 'draw_to', [
			'label'       => esc_html__( 'Draw To', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '100%',
			'description' => esc_html__( 'End position: 100%, 50% 75%, or pixel value', 'gsap-elementor' ),
		] );

		$this->add_control( 'stroke_color', [
			'label'   => esc_html__( 'Stroke Color', 'gsap-elementor' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#0ae448',
			'selectors' => [
				'{{WRAPPER}} .gsap-draw-svg-target svg path, {{WRAPPER}} .gsap-draw-svg-target svg circle, {{WRAPPER}} .gsap-draw-svg-target svg rect, {{WRAPPER}} .gsap-draw-svg-target svg polygon, {{WRAPPER}} .gsap-draw-svg-target svg line, {{WRAPPER}} .gsap-draw-svg-target svg polyline' => 'stroke: {{VALUE}};',
			],
		] );

		$this->add_control( 'stroke_width', [
			'label'   => esc_html__( 'Stroke Width', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 4,
			'min'     => 1,
			'max'     => 50,
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

		$config = [
			'drawFrom'    => $s['draw_from'],
			'drawTo'      => $s['draw_to'],
			'strokeWidth' => intval( $s['stroke_width'] ),
			'duration'    => floatval( $s['duration'] ),
			'delay'       => floatval( $s['delay'] ),
			'ease'        => $s['ease'],
			'repeat'      => intval( $s['repeat'] ),
			'yoyo'        => 'yes' === $s['yoyo'],
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

		$svg_content = '';
		if ( 'preset' === $s['svg_source'] ) {
			$svg_content = $this->get_preset_svg( $s['svg_preset'], intval( $s['stroke_width'] ) );
		} else {
			$svg_content = $s['svg_code'];
		}

		echo '<div class="gsap-widget-draw-svg"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-draw-svg-target">';
		echo $svg_content; // SVG markup - intentionally unescaped.
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Get preset SVG shapes.
	 */
	private function get_preset_svg( $preset, $stroke_width = 4 ) {
		$sw = intval( $stroke_width );

		$presets = [
			'circle'    => '<svg viewBox="0 0 200 200" width="200" height="200"><circle cx="100" cy="100" r="80" fill="none" stroke="currentColor" stroke-width="' . $sw . '"/></svg>',
			'square'    => '<svg viewBox="0 0 200 200" width="200" height="200"><rect x="20" y="20" width="160" height="160" fill="none" stroke="currentColor" stroke-width="' . $sw . '"/></svg>',
			'triangle'  => '<svg viewBox="0 0 200 200" width="200" height="200"><polygon points="100,20 180,180 20,180" fill="none" stroke="currentColor" stroke-width="' . $sw . '"/></svg>',
			'star'      => '<svg viewBox="0 0 200 200" width="200" height="200"><polygon points="100,10 125,75 195,80 140,125 155,195 100,160 45,195 60,125 5,80 75,75" fill="none" stroke="currentColor" stroke-width="' . $sw . '"/></svg>',
			'checkmark' => '<svg viewBox="0 0 200 200" width="200" height="200"><polyline points="30,110 80,160 170,40" fill="none" stroke="currentColor" stroke-width="' . $sw . '" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'heart'     => '<svg viewBox="0 0 200 200" width="200" height="200"><path d="M100,180 C60,140 10,110 10,70 C10,30 50,10 100,50 C150,10 190,30 190,70 C190,110 140,140 100,180Z" fill="none" stroke="currentColor" stroke-width="' . $sw . '"/></svg>',
		];

		return $presets[ $preset ] ?? $presets['circle'];
	}
}
