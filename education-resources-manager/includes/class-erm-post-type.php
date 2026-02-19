<?php
/**
 * Registers custom post type and meta fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Post_Type {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_erm_resource', array( $this, 'save_resource_meta' ) );
	}

	/**
	 * Register post type.
	 */
	public function register_post_type() {
		register_post_type(
			'erm_resource',
			array(
				'labels'       => array(
					'name'          => __( 'Recursos Educativos', 'education-resources-manager' ),
					'singular_name' => __( 'Recurso Educativo', 'education-resources-manager' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-welcome-learn-more',
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author' ),
				'rewrite'      => array( 'slug' => 'recursos-educativos' ),
			)
		);
	}

	/**
	 * Add custom meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'erm_resource_details',
			__( 'Detalles del Recurso', 'education-resources-manager' ),
			array( $this, 'render_meta_box' ),
			'erm_resource',
			'normal',
			'default'
		);
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'erm_resource_meta_nonce', 'erm_resource_meta_nonce' );

		$values = array(
			'type'       => get_post_meta( $post->ID, '_erm_resource_type', true ),
			'level'      => get_post_meta( $post->ID, '_erm_difficulty_level', true ),
			'duration'   => get_post_meta( $post->ID, '_erm_duration_minutes', true ),
			'url'        => get_post_meta( $post->ID, '_erm_resource_url', true ),
			'instructor' => get_post_meta( $post->ID, '_erm_instructor', true ),
			'price'      => get_post_meta( $post->ID, '_erm_price', true ),
			'status'     => get_post_meta( $post->ID, '_erm_resource_status', true ),
		);
		?>
		<p>
			<label for="erm_resource_type"><strong><?php esc_html_e( 'Tipo de recurso', 'education-resources-manager' ); ?></strong></label><br>
			<select id="erm_resource_type" name="erm_resource_type">
				<?php foreach ( $this->get_types() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $values['type'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="erm_difficulty_level"><strong><?php esc_html_e( 'Nivel de dificultad', 'education-resources-manager' ); ?></strong></label><br>
			<select id="erm_difficulty_level" name="erm_difficulty_level">
				<?php foreach ( $this->get_levels() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $values['level'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="erm_duration_minutes"><strong><?php esc_html_e( 'Duración estimada (minutos)', 'education-resources-manager' ); ?></strong></label><br>
			<input type="number" id="erm_duration_minutes" name="erm_duration_minutes" value="<?php echo esc_attr( $values['duration'] ); ?>" min="0" step="1" />
		</p>
		<p>
			<label for="erm_resource_url"><strong><?php esc_html_e( 'URL del recurso', 'education-resources-manager' ); ?></strong></label><br>
			<input type="url" class="widefat" id="erm_resource_url" name="erm_resource_url" value="<?php echo esc_attr( $values['url'] ); ?>" />
		</p>
		<p>
			<label for="erm_instructor"><strong><?php esc_html_e( 'Instructor/Autor', 'education-resources-manager' ); ?></strong></label><br>
			<input type="text" class="widefat" id="erm_instructor" name="erm_instructor" value="<?php echo esc_attr( $values['instructor'] ); ?>" />
		</p>
		<p>
			<label for="erm_price"><strong><?php esc_html_e( 'Precio', 'education-resources-manager' ); ?></strong></label><br>
			<input type="text" id="erm_price" name="erm_price" value="<?php echo esc_attr( $values['price'] ); ?>" placeholder="0 o gratuito" />
		</p>
		<p>
			<label for="erm_resource_status"><strong><?php esc_html_e( 'Estado de publicación', 'education-resources-manager' ); ?></strong></label><br>
			<select id="erm_resource_status" name="erm_resource_status">
				<?php foreach ( $this->get_statuses() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $values['status'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save meta values.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_resource_meta( $post_id ) {
		if ( ! isset( $_POST['erm_resource_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['erm_resource_meta_nonce'] ) ), 'erm_resource_meta_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$allowed_maps = array(
			'_erm_resource_type'   => array( 'field' => 'erm_resource_type', 'allowed' => array_keys( $this->get_types() ) ),
			'_erm_difficulty_level' => array( 'field' => 'erm_difficulty_level', 'allowed' => array_keys( $this->get_levels() ) ),
			'_erm_resource_status' => array( 'field' => 'erm_resource_status', 'allowed' => array_keys( $this->get_statuses() ) ),
		);

		foreach ( $allowed_maps as $meta_key => $map ) {
			if ( isset( $_POST[ $map['field'] ] ) ) {
				$value = sanitize_key( wp_unslash( $_POST[ $map['field'] ] ) );
				if ( in_array( $value, $map['allowed'], true ) ) {
					update_post_meta( $post_id, $meta_key, $value );
				}
			}
		}

		if ( isset( $_POST['erm_duration_minutes'] ) ) {
			update_post_meta( $post_id, '_erm_duration_minutes', absint( $_POST['erm_duration_minutes'] ) );
		}

		if ( isset( $_POST['erm_resource_url'] ) ) {
			update_post_meta( $post_id, '_erm_resource_url', esc_url_raw( wp_unslash( $_POST['erm_resource_url'] ) ) );
		}

		if ( isset( $_POST['erm_instructor'] ) ) {
			update_post_meta( $post_id, '_erm_instructor', sanitize_text_field( wp_unslash( $_POST['erm_instructor'] ) ) );
		}

		if ( isset( $_POST['erm_price'] ) ) {
			update_post_meta( $post_id, '_erm_price', sanitize_text_field( wp_unslash( $_POST['erm_price'] ) ) );
		}
	}

	/**
	 * Allowed resource types.
	 *
	 * @return array
	 */
	private function get_types() {
		return array(
			'curso'    => __( 'Curso', 'education-resources-manager' ),
			'tutorial' => __( 'Tutorial', 'education-resources-manager' ),
			'ebook'    => __( 'eBook', 'education-resources-manager' ),
			'video'    => __( 'Video', 'education-resources-manager' ),
		);
	}

	/**
	 * Allowed levels.
	 *
	 * @return array
	 */
	private function get_levels() {
		return array(
			'principiante' => __( 'Principiante', 'education-resources-manager' ),
			'intermedio'   => __( 'Intermedio', 'education-resources-manager' ),
			'avanzado'     => __( 'Avanzado', 'education-resources-manager' ),
		);
	}

	/**
	 * Allowed publication statuses.
	 *
	 * @return array
	 */
	private function get_statuses() {
		return array(
			'borrador'  => __( 'Borrador', 'education-resources-manager' ),
			'publicado' => __( 'Publicado', 'education-resources-manager' ),
			'archivado' => __( 'Archivado', 'education-resources-manager' ),
		);
	}
}
