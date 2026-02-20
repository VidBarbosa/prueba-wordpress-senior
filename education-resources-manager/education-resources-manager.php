<?php
/**
 * Plugin Name: Education Resources Manager
 * Description: Sistema de gestión de recursos educativos con panel administrativo, REST API y filtros dinámicos.
 * Version: 1.0.0
 * Author: Candidate
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: education-resources-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ERM_VERSION', '1.0.0' );
define( 'ERM_PLUGIN_FILE', __FILE__ );
define( 'ERM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ERM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ERM_PLUGIN_DIR . 'includes/class-erm-activator.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-deactivator.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-database.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-post-type.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-taxonomy.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-admin.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-rest-api.php';
require_once ERM_PLUGIN_DIR . 'includes/class-erm-shortcode.php';

register_activation_hook( ERM_PLUGIN_FILE, array( 'ERM_Activator', 'activate' ) );
register_deactivation_hook( ERM_PLUGIN_FILE, array( 'ERM_Deactivator', 'deactivate' ) );

function erm_init_plugin() {
	new ERM_Post_Type();
	new ERM_Taxonomy();
	new ERM_Admin();
	new ERM_REST_API();
	new ERM_Shortcode();
}
add_action( 'plugins_loaded', 'erm_init_plugin' );
