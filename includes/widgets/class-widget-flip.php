<?php
namespace Gsap_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flip Widget — Seamless layout transitions (First, Last, Invert, Play).
 * Ideal for filterable grids, tab switches, and layout changes.
 */
class Widget_Flip extends Widget_GSAP_Base {

	public function get_name() {
		return 'gsap_flip';
	}

	public function get_title() {
		return esc_html__( 'GSAP Flip', 'gsap-elementor' );
	}

	public function get_icon() {
		return 'eicon-flip-box';
	}

	public function get_keywords() {
		return [ 'gsap', 'flip', 'layout', 'transition', 'filter', 'grid' ];
	}

	protected function register_controls() {

		// --- Content ---
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'gsap-elementor' ),
		] );

		$this->add_control( 'layout_type', [
			'label'   => esc_html__( 'Layout Type', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'grid',
			'options' => [
				'grid'    => 'Filterable Grid',
				'toggle'  => 'State Toggle',
				'shuffle' => 'Shuffle',
			],
		] );

		// --- Filters (for grid) ---
		$this->add_control( 'filters', [
			'label'       => esc_html__( 'Filter Labels', 'gsap-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'All, Red, Blue, Green',
			'description' => esc_html__( 'Comma-separated filter labels', 'gsap-elementor' ),
			'condition'   => [ 'layout_type' => 'grid' ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'item_label', [
			'label'   => esc_html__( 'Label', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'Item',
		] );

		$repeater->add_control( 'item_category', [
			'label'   => esc_html__( 'Category', 'gsap-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'red',
		] );

		$repeater->add_control( 'item_bg', [
			'label'   => esc_html__( 'Background Color', 'gsap-elementor' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#6c63ff',
		] );

		$this->add_control( 'grid_items', [
			'label'       => esc_html__( 'Grid Items', 'gsap-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'item_label' => 'Item 1', 'item_category' => 'red', 'item_bg' => '#ff6b6b' ],
				[ 'item_label' => 'Item 2', 'item_category' => 'blue', 'item_bg' => '#6c63ff' ],
				[ 'item_label' => 'Item 3', 'item_category' => 'green', 'item_bg' => '#0ae448' ],
				[ 'item_label' => 'Item 4', 'item_category' => 'red', 'item_bg' => '#ff6b6b' ],
				[ 'item_label' => 'Item 5', 'item_category' => 'blue', 'item_bg' => '#6c63ff' ],
				[ 'item_label' => 'Item 6', 'item_category' => 'green', 'item_bg' => '#0ae448' ],
			],
			'title_field' => '{{{ item_label }}} ({{{ item_category }}})',
			'condition'   => [ 'layout_type' => 'grid' ],
		] );

		$this->add_control( 'toggle_html_a', [
			'label'     => esc_html__( 'State A (HTML)', 'gsap-elementor' ),
			'type'      => Controls_Manager::CODE,
			'language'  => 'html',
			'default'   => '<div style="display:flex;gap:10px;"><div style="width:100px;height:100px;background:#6c63ff;border-radius:8px;"></div><div style="width:100px;height:100px;background:#0ae448;border-radius:8px;"></div></div>',
			'condition' => [ 'layout_type' => 'toggle' ],
		] );

		$this->add_control( 'toggle_html_b', [
			'label'     => esc_html__( 'State B (HTML)', 'gsap-elementor' ),
			'type'      => Controls_Manager::CODE,
			'language'  => 'html',
			'default'   => '<div style="display:flex;flex-direction:column;gap:10px;"><div style="width:200px;height:50px;background:#6c63ff;border-radius:8px;"></div><div style="width:200px;height:50px;background:#0ae448;border-radius:8px;"></div></div>',
			'condition' => [ 'layout_type' => 'toggle' ],
		] );

		$this->end_controls_section();

		// --- Flip Settings ---
		$this->start_controls_section( 'section_flip', [
			'label' => esc_html__( 'Flip Settings', 'gsap-elementor' ),
		] );

		$this->add_control( 'flip_duration', [
			'label'   => esc_html__( 'Duration (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0.5,
			'min'     => 0.1,
			'max'     => 5,
			'step'    => 0.1,
		] );

		$this->add_control( 'flip_ease', [
			'label'   => esc_html__( 'Easing', 'gsap-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'power1.inOut',
			'options' => $this->get_easing_options(),
		] );

		$this->add_control( 'flip_stagger', [
			'label'   => esc_html__( 'Stagger (s)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0.05,
			'min'     => 0,
			'max'     => 1,
			'step'    => 0.01,
		] );

		$this->add_control( 'flip_absolute', [
			'label'       => esc_html__( 'Absolute during flip', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => esc_html__( 'Position items absolutely during transition', 'gsap-elementor' ),
		] );

		$this->add_control( 'flip_scale', [
			'label'   => esc_html__( 'Animate Scale', 'gsap-elementor' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'flip_fade', [
			'label'       => esc_html__( 'Fade In/Out Hidden Items', 'gsap-elementor' ),
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'condition'   => [ 'layout_type' => 'grid' ],
		] );

		$this->end_controls_section();

		// --- Grid Style ---
		$this->start_controls_section( 'section_style', [
			'label'     => esc_html__( 'Grid Style', 'gsap-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout_type' => 'grid' ],
		] );

		$this->add_control( 'grid_columns', [
			'label'   => esc_html__( 'Columns', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 3,
			'min'     => 1,
			'max'     => 6,
		] );

		$this->add_control( 'grid_gap', [
			'label'   => esc_html__( 'Gap (px)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 10,
			'min'     => 0,
			'max'     => 50,
		] );

		$this->add_control( 'item_height', [
			'label'   => esc_html__( 'Item Height (px)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 100,
			'min'     => 40,
			'max'     => 500,
		] );

		$this->add_control( 'item_border_radius', [
			'label'   => esc_html__( 'Border Radius (px)', 'gsap-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 8,
			'min'     => 0,
			'max'     => 50,
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = [
			'layoutType' => $s['layout_type'],
			'duration'   => floatval( $s['flip_duration'] ),
			'ease'       => $s['flip_ease'],
			'stagger'    => floatval( $s['flip_stagger'] ),
			'absolute'   => 'yes' === $s['flip_absolute'],
			'scale'      => 'yes' === $s['flip_scale'],
			'fade'       => 'yes' === ( $s['flip_fade'] ?? '' ),
		];

		echo '<div class="gsap-widget-flip"';
		$this->render_data_attrs( $config );
		echo '>';

		if ( 'grid' === $s['layout_type'] ) {
			$filters = array_map( 'trim', explode( ',', $s['filters'] ) );

			echo '<div class="gsap-flip-filters" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">';
			foreach ( $filters as $filter ) {
				$data_filter = strtolower( $filter );
				$active      = ( 'all' === $data_filter ) ? ' gsap-flip-filter-active' : '';
				echo '<button class="gsap-flip-filter-btn' . $active . '" data-filter="' . esc_attr( $data_filter ) . '" type="button" style="padding:8px 16px;border:2px solid #333;background:transparent;cursor:pointer;border-radius:4px;font-weight:600;">';
				echo esc_html( $filter );
				echo '</button>';
			}
			echo '</div>';

			$cols = intval( $s['grid_columns'] );
			$gap  = intval( $s['grid_gap'] );
			echo '<div class="gsap-flip-grid" style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:' . $gap . 'px;">';
			foreach ( $s['grid_items'] as $item ) {
				$height = intval( $s['item_height'] );
				$radius = intval( $s['item_border_radius'] );
				echo '<div class="gsap-flip-item" data-category="' . esc_attr( strtolower( $item['item_category'] ) ) . '" style="height:' . $height . 'px;background:' . esc_attr( $item['item_bg'] ) . ';border-radius:' . $radius . 'px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;">';
				echo esc_html( $item['item_label'] );
				echo '</div>';
			}
			echo '</div>';
		} elseif ( 'toggle' === $s['layout_type'] ) {
			echo '<button class="gsap-flip-toggle-btn" type="button" style="margin-bottom:16px;padding:8px 20px;border:2px solid #333;background:transparent;cursor:pointer;border-radius:4px;font-weight:600;">Toggle Layout</button>';
			echo '<div class="gsap-flip-toggle-container" data-state="a">';
			echo wp_kses_post( $s['toggle_html_a'] );
			echo '</div>';
			echo '<template class="gsap-flip-state-a">' . $s['toggle_html_a'] . '</template>';
			echo '<template class="gsap-flip-state-b">' . $s['toggle_html_b'] . '</template>';
		} elseif ( 'shuffle' === $s['layout_type'] ) {
			echo '<button class="gsap-flip-shuffle-btn" type="button" style="margin-bottom:16px;padding:8px 20px;border:2px solid #333;background:transparent;cursor:pointer;border-radius:4px;font-weight:600;">Shuffle</button>';
			$cols = intval( $s['grid_columns'] ?? 3 );
			$gap  = intval( $s['grid_gap'] ?? 10 );
			echo '<div class="gsap-flip-grid" style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:' . $gap . 'px;">';
			foreach ( $s['grid_items'] as $item ) {
				$height = intval( $s['item_height'] ?? 100 );
				$radius = intval( $s['item_border_radius'] ?? 8 );
				echo '<div class="gsap-flip-item" style="height:' . $height . 'px;background:' . esc_attr( $item['item_bg'] ) . ';border-radius:' . $radius . 'px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;">';
				echo esc_html( $item['item_label'] );
				echo '</div>';
			}
			echo '</div>';
		}

		echo '</div>';
	}
}
