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
		wp_register_script( 'erm-public-script', ERM_PLUGIN_URL . 'public/js/public-scripts.js', array(), ERM_VERSION, true );

		wp_localize_script(
			'erm-public-script',
			'ermData',
			array(
				'apiBase' => esc_url_raw( rest_url( 'erm/v1' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Shortcode callback.
	 *
	 * @return string
	 */
	public function render_shortcode() {
		wp_enqueue_style( 'erm-public-style' );
		wp_enqueue_script( 'erm-public-script' );

		$categories = get_terms(
			array(
				'taxonomy'   => 'erm_resource_category',
				'hide_empty' => false,
			)
		);

		ob_start();
		?>
		<div class="erm-shortcode" id="erm-shortcode-app">
			<form class="erm-filters" id="erm-filters-form">
				<input type="text" name="search" placeholder="Buscar recursos" aria-label="Buscar recursos" />
				<select name="type">
					<option value="">Tipo</option>
					<option value="curso">Curso</option>
					<option value="tutorial">Tutorial</option>
					<option value="ebook">eBook</option>
					<option value="video">Video</option>
				</select>
				<select name="level">
					<option value="">Nivel</option>
					<option value="principiante">Principiante</option>
					<option value="intermedio">Intermedio</option>
					<option value="avanzado">Avanzado</option>
				</select>
				<select name="category">
					<option value="">Categoría</option>
					<?php foreach ( $categories as $category ) : ?>
						<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="submit">Filtrar</button>
			</form>
			<div class="erm-loading" id="erm-loading" hidden>Cargando...</div>
			<div class="erm-results" id="erm-results"></div>
			<div class="erm-pagination" id="erm-pagination"></div>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}
