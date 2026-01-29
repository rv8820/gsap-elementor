<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Draggable Widget — Make elements draggable with GSAP Draggable + Inertia.
 */
class Widget_Draggable extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_draggable';
	}

	public function get_title() {
		return esc_html__( 'GSAP Draggable', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-drag-n-drop';
	}

	public function get_keywords() {
		return [ 'gsap', 'draggable', 'drag', 'inertia', 'throw', 'fling' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'content_html', [
			'label'   => esc_html__( 'Draggable Element (HTML)', 'gsap-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '<div style="width:120px;height:120px;background:#6c63ff;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;cursor:grab;user-select:none;">Drag me</div>',
		] );

		$this->end_controls_section();

		// --- Draggable Settings ---
		$this->start_controls_section( 'section_draggable', [
			'label' => esc_html__( 'Draggable Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'drag_type', [
			'label'   => esc_html__( 'Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'x,y',
			'options' => [
				'x,y'      => 'X & Y (Free)',
				'x'        => 'X only (Horizontal)',
				'y'        => 'Y only (Vertical)',
				'rotation' => 'Rotation',
			],
		] );

		$this->add_control( 'bounds_enabled', [
			'label'   => esc_html__( 'Constrain to Bounds', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'bounds_selector', [
			'label'       => esc_html__( 'Bounds', 'gsap-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'parent',
			'options'     => [
				'parent' => 'Parent Container',
				'window' => 'Window',
				'custom' => 'Custom Selector',
			],
			'condition'   => [ 'bounds_enabled' => 'yes' ],
		] );

		$this->add_control( 'bounds_custom', [
			'label'     => esc_html__( 'Custom Bounds Selector', 'gsap-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '.my-container',
			'condition' => [ 'bounds_selector' => 'custom', 'bounds_enabled' => 'yes' ],
		] );

		$this->add_control( 'inertia_enabled', [
			'label'       => esc_html__( 'Inertia (Throw)', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => esc_html__( 'Continue moving after release with momentum', 'gsap-elementor' ),
		] );

		$this->add_control( 'edge_resistance', [
			'label'   => esc_html__( 'Edge Resistance', 'gsap-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [ 'size' => 0.65 ],
			'range'   => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
		] );

		$this->add_control( 'snap_enabled', [
			'label'   => esc_html__( 'Snap', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'snap_increment', [
			'label'     => esc_html__( 'Snap Increment (px or deg)', 'gsap-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 50,
			'min'       => 1,
			'max'       => 500,
			'condition' => [ 'snap_enabled' => 'yes' ],
		] );

		$this->add_control( 'lock_axis', [
			'label'       => esc_html__( 'Lock Axis', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => esc_html__( 'Lock to initial drag direction', 'gsap-elementor' ),
		] );

		$this->end_controls_section();

		// --- Container Style ---
		$this->start_controls_section( 'section_style', [
			'label' => esc_html__( 'Container', 'gsap-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'container_width', [
			'label'      => esc_html__( 'Width', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [ 'min' => 100, 'max' => 2000 ],
				'%'  => [ 'min' => 10, 'max' => 100 ],
			],
			'default'    => [ 'size' => 100, 'unit' => '%' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-draggable-container' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'container_height', [
			'label'      => esc_html__( 'Height', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 1000 ] ],
			'default'    => [ 'size' => 300, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-draggable-container' => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'container_bg', [
			'label'     => esc_html__( 'Background', 'gsap-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f5f5',
			'selectors' => [
				'{{WRAPPER}} .gsap-draggable-container' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'container_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'gsap-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 12, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .gsap-draggable-container' => 'border-radius: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$bounds = 'parent';
		if ( 'yes' === $s['bounds_enabled'] ) {
			if ( 'custom' === $s['bounds_selector'] ) {
				$bounds = $s['bounds_custom'];
			} elseif ( 'window' === $s['bounds_selector'] ) {
				$bounds = 'window';
			}
		}

		$config = [
			'type'           => $s['drag_type'],
			'bounds'         => 'yes' === $s['bounds_enabled'] ? $bounds : false,
			'inertia'        => 'yes' === $s['inertia_enabled'],
			'edgeResistance' => floatval( $s['edge_resistance']['size'] ?? 0.65 ),
			'lockAxis'       => 'yes' === $s['lock_axis'],
		];

		if ( 'yes' === $s['snap_enabled'] ) {
			$config['snap'] = intval( $s['snap_increment'] );
		}

		echo '<div class="gsap-widget-draggable"';
		$this->render_data_attrs( $config );
		echo '>';
		echo '<div class="gsap-draggable-container" style="position:relative;overflow:hidden;">';
		echo '<div class="gsap-draggable-target" style="display:inline-block;">';
		echo wp_kses_post( $s['content_html'] );
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}
