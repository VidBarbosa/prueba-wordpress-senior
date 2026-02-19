<?php
/**
 * Plugin activator.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Activator {
	/**
	 * Activation callback.
	 */
	public static function activate() {
		ERM_Database::create_tables();
		flush_rewrite_rules();
	}
}
