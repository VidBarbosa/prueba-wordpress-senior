<?php
/**
 * Plugin deactivator.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Deactivator {
	/**
	 * Deactivation callback.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
