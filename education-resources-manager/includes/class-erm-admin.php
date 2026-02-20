<?php
/**
 * Admin screens and assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Education Resources', 'education-resources-manager' ),
			__( 'Education Resources', 'education-resources-manager' ),
			'manage_options',
			'erm-admin',
			array( $this, 'render_admin_page' ),
			'dashicons-chart-bar',
			26
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Hook suffix.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_erm-admin' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'erm-admin-style', ERM_PLUGIN_URL . 'admin/css/admin-styles.css', array(), ERM_VERSION );
		wp_enqueue_script( 'erm-admin-script', ERM_PLUGIN_URL . 'admin/js/admin-scripts.js', array(), ERM_VERSION, true );
	}

	/**
	 * Render admin page.
	 */
	public function render_admin_page() {
		$filters = array(
			'type'     => isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '',
			'level'    => isset( $_GET['level'] ) ? sanitize_key( wp_unslash( $_GET['level'] ) ) : '',
			'category' => isset( $_GET['category'] ) ? absint( $_GET['category'] ) : 0,
		);

		$query_args = array(
			'post_type'      => 'erm_resource',
			'posts_per_page' => 20,
			'post_status'    => array( 'publish', 'draft' ),
		);

		$meta_query = array();

		if ( $filters['type'] ) {
			$meta_query[] = array(
				'key'   => '_erm_resource_type',
				'value' => $filters['type'],
			);
		}

		if ( $filters['level'] ) {
			$meta_query[] = array(
				'key'   => '_erm_difficulty_level',
				'value' => $filters['level'],
			);
		}

		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		if ( $filters['category'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'erm_resource_category',
					'field'    => 'term_id',
					'terms'    => $filters['category'],
				),
			);
		}

		$resources         = get_posts( $query_args );
		$top_resources     = ERM_Database::get_top_resources( 5 );
		$resources_by_type = $this->get_resources_by_type();
		$month_stats       = ERM_Database::get_resources_by_month( 6 );
		$categories        = get_terms(
			array(
				'taxonomy'   => 'erm_resource_category',
				'hide_empty' => false,
			)
		);

		require ERM_PLUGIN_DIR . 'admin/views/admin-page.php';
	}

	/**
	 * Aggregate resources by type.
	 *
	 * @return array
	 */
	private function get_resources_by_type() {
		$types = array( 'curso', 'tutorial', 'ebook', 'video' );
		$data  = array();

		foreach ( $types as $type ) {
			$data[ $type ] = (int) count(
				get_posts(
					array(
						'post_type'      => 'erm_resource',
						'posts_per_page' => -1,
						'fields'         => 'ids',
						'post_status'    => array( 'publish', 'draft' ),
						'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'key'   => '_erm_resource_type',
								'value' => $type,
							),
						),
					)
				)
			);
		}

		return $data;
	}
}
