<?php
/**
 * Registers custom taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Taxonomy {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Register taxonomies.
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'erm_resource_category',
			'erm_resource',
			array(
				'label'        => __( 'Categorías de recursos', 'education-resources-manager' ),
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'categoria-recurso' ),
			)
		);

		register_taxonomy(
			'erm_skill_tag',
			'erm_resource',
			array(
				'label'        => __( 'Etiquetas de habilidades', 'education-resources-manager' ),
				'hierarchical' => false,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'habilidad' ),
			)
		);
	}
}
