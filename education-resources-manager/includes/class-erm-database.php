<?php
/**
 * Handles database operations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_Database {
	/**
	 * Get tracking table name.
	 *
	 * @return string
	 */
	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'erm_resource_tracking';
	}

	/**
	 * Create custom tracking tables.
	 */
	public static function create_tables() {
		global $wpdb;

		$table_name      = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			resource_id BIGINT(20) UNSIGNED NOT NULL,
			user_id BIGINT(20) UNSIGNED DEFAULT 0,
			action_date DATETIME NOT NULL,
			action_type VARCHAR(20) NOT NULL,
			ip_address VARCHAR(45) NOT NULL,
			PRIMARY KEY  (id),
			KEY resource_id (resource_id),
			KEY user_id (user_id),
			KEY action_type (action_type),
			KEY action_date (action_date)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Insert a tracking action.
	 *
	 * @param int    $resource_id Post ID.
	 * @param string $action_type view|download.
	 *
	 * @return bool
	 */
	public static function track_action( $resource_id, $action_type = 'view' ) {
		global $wpdb;

		$resource_id = absint( $resource_id );
		$action_type = sanitize_key( $action_type );

		if ( ! in_array( $action_type, array( 'view', 'download' ), true ) ) {
			return false;
		}

		if ( 'erm_resource' !== get_post_type( $resource_id ) ) {
			return false;
		}

		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		$inserted = $wpdb->insert(
			self::table_name(),
			array(
				'resource_id' => $resource_id,
				'user_id'     => get_current_user_id(),
				'action_date' => current_time( 'mysql' ),
				'action_type' => $action_type,
				'ip_address'  => $ip_address,
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		return false !== $inserted;
	}

	/**
	 * Get top viewed resources.
	 *
	 * @param int $limit Number of rows.
	 * @return array
	 */
	public static function get_top_resources( $limit = 5 ) {
		global $wpdb;

		$limit = absint( $limit );
		if ( $limit < 1 ) {
			$limit = 5;
		}

		$table_name = self::table_name();

		$query = $wpdb->prepare(
			"SELECT resource_id, COUNT(*) AS total
			FROM {$table_name}
			WHERE action_type = %s
			GROUP BY resource_id
			ORDER BY total DESC
			LIMIT %d",
			'view',
			$limit
		);

		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get resources created by month.
	 *
	 * @param int $months Number of months.
	 * @return array
	 */
	public static function get_resources_by_month( $months = 6 ) {
		global $wpdb;

		$months = absint( $months );
		if ( $months < 1 ) {
			$months = 6;
		}

		$query = $wpdb->prepare(
			"SELECT DATE_FORMAT(post_date, '%%Y-%%m') AS ym, COUNT(ID) AS total
			FROM {$wpdb->posts}
			WHERE post_type = %s
			AND post_status IN ('publish', 'draft')
			AND post_date >= DATE_SUB(CURDATE(), INTERVAL %d MONTH)
			GROUP BY ym
			ORDER BY ym ASC",
			'erm_resource',
			$months
		);

		return $wpdb->get_results( $query, ARRAY_A );
	}
}
