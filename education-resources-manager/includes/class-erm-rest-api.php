<?php
/**
 * REST API endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ERM_REST_API {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		register_rest_route(
			'erm/v1',
			'/resources',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resources' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'erm/v1',
			'/resources/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resource' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'erm/v1',
			'/resources/(?P<id>\d+)/track',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'track_resource' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'erm/v1',
			'/stats',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function get_resources( WP_REST_Request $request ) {
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = min( 20, max( 1, (int) $request->get_param( 'per_page' ) ) );

		$args = array(
			'post_type'      => 'erm_resource',
			'post_status'    => 'publish',
			'paged'          => $page,
			'posts_per_page' => $per_page,
			's'              => sanitize_text_field( (string) $request->get_param( 'search' ) ),
		);

		$meta_query = array();
		if ( $request->get_param( 'type' ) ) {
			$meta_query[] = array(
				'key'   => '_erm_resource_type',
				'value' => sanitize_key( $request->get_param( 'type' ) ),
			);
		}
		if ( $request->get_param( 'level' ) ) {
			$meta_query[] = array(
				'key'   => '_erm_difficulty_level',
				'value' => sanitize_key( $request->get_param( 'level' ) ),
			);
		}
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		if ( $request->get_param( 'category' ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'erm_resource_category',
					'field'    => 'term_id',
					'terms'    => absint( $request->get_param( 'category' ) ),
				),
			);
		}

		$query = new WP_Query( $args );
		$items = array_map( array( $this, 'format_resource' ), $query->posts );

		return rest_ensure_response(
			array(
				'items'       => $items,
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
			)
		);
	}

	public function get_resource( WP_REST_Request $request ) {
		$post = get_post( (int) $request['id'] );
		if ( ! $post || 'erm_resource' !== $post->post_type ) {
			return new WP_Error( 'erm_not_found', __( 'Resource not found', 'education-resources-manager' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response( $this->format_resource( $post ) );
	}

	public function track_resource( WP_REST_Request $request ) {
		$resource_id = (int) $request['id'];
		$action      = $request->get_param( 'action_type' ) ? sanitize_key( $request->get_param( 'action_type' ) ) : 'view';

		$result = ERM_Database::track_action( $resource_id, $action );
		if ( ! $result ) {
			return new WP_Error( 'erm_track_failed', __( 'Tracking failed', 'education-resources-manager' ), array( 'status' => 400 ) );
		}

		return rest_ensure_response(
			array(
				'success' => true,
			)
		);
	}

	public function get_stats() {
		return rest_ensure_response(
			array(
				'top_resources'      => ERM_Database::get_top_resources( 5 ),
				'resources_by_month' => ERM_Database::get_resources_by_month( 6 ),
			)
		);
	}

	private function format_resource( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		return array(
			'id'         => (int) $post->ID,
			'title'      => get_the_title( $post->ID ),
			'excerpt'    => get_the_excerpt( $post->ID ),
			'content'    => apply_filters( 'the_content', $post->post_content ),
			'type'       => get_post_meta( $post->ID, '_erm_resource_type', true ),
			'level'      => get_post_meta( $post->ID, '_erm_difficulty_level', true ),
			'duration'   => (int) get_post_meta( $post->ID, '_erm_duration_minutes', true ),
			'url'        => get_post_meta( $post->ID, '_erm_resource_url', true ),
			'instructor' => get_post_meta( $post->ID, '_erm_instructor', true ),
			'price'      => get_post_meta( $post->ID, '_erm_price', true ),
			'status'     => get_post_meta( $post->ID, '_erm_resource_status', true ),
			'link'       => get_permalink( $post->ID ),
		);
	}
}
