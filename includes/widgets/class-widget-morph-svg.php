<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MorphSVG Widget — Morph one SVG shape into another smoothly.
 */
class Widget_Morph_SVG extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_morph_svg';
	}

	public function get_title() {
		return esc_html__( 'GSAP MorphSVG', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-integration';
	}

	public function get_keywords() {
		return [ 'gsap', 'morph', 'svg', 'shape', 'transform' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'SVG Shapes', 'gsap-elementor' ),
		] );

		$this->add_control( 'start_shape', [
			'label'       => esc_html__( 'Start Shape (SVG Path)', 'gsap-elementor' ),
			'type'        => Controls_Manager::CODE,
			'language'    => 'html',
			'default'     => '<svg viewBox="0 0 200 200" width="200" height="200"><circle id="gsap-morph-start" cx="100" cy="100" r="80" fill="#6c63ff"/></svg>',
			'description' => esc_html__( 'The SVG must contain a shape with id="gsap-morph-start"', 'gsap-elementor' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'shape_label', [
			'label'   => esc_html__( 'Label', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'Shape',
		] );

		$repeater->add_control( 'shape_path', [
			'label'       => esc_html__( 'SVG Path Data or Shape', 'gsap-elementor' ),
			'type'        => Controls_Manager::CODE,
			'language'    => 'html',
			'default'     => '<rect x="20" y="20" width="160" height="160" rx="10" fill="#0ae448"/>',
			'description' => esc_html__( 'SVG path d attribute or shape element markup', 'gsap-elementor' ),
		] );

		$repeater->add_control( 'shape_fill', [
			'label'   => esc_html__( 'Fill Color', 'gsap-elementor' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#0ae448',
		] );

		$this->add_control( 'morph_shapes', [
			'label'       => esc_html__( 'Target Shapes', 'gsap-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'shape_label' => 'Square',
					'shape_path'  => '<rect x="20" y="20" width="160" height="160" rx="10"/>',
					'shape_fill'  => '#0ae448',
				],
				[
					'shape_label' => 'Star',
					'shape_path'  => '<polygon points="100,10 125,75 195,80 140,125 155,195 100,160 45,195 60,125 5,80 75,75"/>',
					'shape_fill'  => '#ff6b6b',
				],
			],
			'title_field' => '{{{ shape_label }}}',
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
				'{{WRAPPER}} .gsap-morph-svg-target svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
			],
		] );

		$this->end_controls_section();

		// --- Morph Settings ---
		$this->start_controls_section( 'section_morph', [
			'label' => esc_html__( 'Morph Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'morph_type', [
			'label'   => esc_html__( 'Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'rotational',
			'options' => [
				'rotational' => 'Rotational',
				'linear'     => 'Linear',
			],
		] );

		$this->add_control( 'morph_origin', [
			'label'       => esc_html__( 'Shape Origin', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '50% 50%',
			'description' => esc_html__( 'Alignment origin for morphing', 'gsap-elementor' ),
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

		$shapes = [];
		foreach ( $s['morph_shapes'] as $shape ) {
			$shapes[] = [
				'path' => $shape['shape_path'],
				'fill' => $shape['shape_fill'],
			];
		}

		$config = [
			'morphType' => $s['morph_type'],
			'origin'    => $s['morph_origin'],
			'shapes'    => $shapes,
			'duration'  => floatval( $s['duration'] ),
			'delay'     => floatval( $s['delay'] ),
			'ease'      => $s['ease'],
			'repeat'    => intval( $s['repeat'] ),
			'yoyo'      => 'yes' === $s['yoyo'],
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

		echo '<div class="gsap-widget-morph-svg"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-morph-svg-target">';
		echo $s['start_shape']; // SVG markup
		echo '</div>';
		// Hidden container for target shapes
		echo '<div class="gsap-morph-shapes-hidden" style="display:none;">';
		foreach ( $s['morph_shapes'] as $index => $shape ) {
			echo '<div class="gsap-morph-shape-' . intval( $index ) . '">';
			echo $shape['shape_path'];
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
	}
}
