<?php
/**
 * Frontend shortcode and assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Shortcode {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'recursos_educativos', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register public assets.
	 */
	public function register_assets() {
		wp_register_style( 'erm-public-style', ERM_PLUGIN_URL . 'public/css/public-styles.css', array(), ERM_VERSION );
		wp_register_script( 'erm-public-script', ERM_PLUGIN_URL . 'public/js/public-scripts.js', array( 'wp-element' ), ERM_VERSION, true );
	}

	/**
	 * Shortcode callback.
	 *
	 * @return string
	 */
	public function render_shortcode() {
		$categories = get_terms(
			array(
				'taxonomy'   => 'erm_resource_category',
				'hide_empty' => false,
			)
		);

		wp_enqueue_style( 'erm-public-style' );
		wp_enqueue_script( 'erm-public-script' );
		wp_localize_script(
			'erm-public-script',
			'ermData',
			array(
				'apiBase'    => esc_url_raw( rest_url( 'erm/v1' ) ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'categories' => array_map(
					static function( $category ) {
						return array(
							'id'   => (int) $category->term_id,
							'name' => $category->name,
						);
					},
					is_array( $categories ) ? $categories : array()
				),
			)
		);

		ob_start();
		?>
		<div class="erm-shortcode" id="erm-shortcode-app">
			<noscript><?php esc_html_e( 'Debes habilitar JavaScript para usar los filtros de recursos.', 'education-resources-manager' ); ?></noscript>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}
