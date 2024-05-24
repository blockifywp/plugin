<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use Blockify\Utilities\Str;
use WP_REST_Request;
use WP_REST_Server;
use function absint;
use function add_filter;
use function add_options_page;
use function apply_filters;
use function array_merge;
use function current_user_can;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function printf;
use function register_rest_route;
use function register_setting;
use function update_option;
use function wp_send_json_success;

/**
 * Settings.
 *
 * @since 0.1.0
 */
class Settings {

	public const HOOK = 'fieldify_settings';

	/**
	 * Meta boxes.
	 *
	 * @var MetaBoxes
	 */
	private MetaBoxes $meta_boxes;

	/**
	 * Rest schema.
	 *
	 * @var RestSchema
	 */
	private RestSchema $rest_schema;

	/**
	 * Sanitizer.
	 *
	 * @var Sanitizer
	 */
	private Sanitizer $sanitizer;

	/**
	 * Meta boxes.
	 *
	 * @param MetaBoxes  $meta_boxes  Meta boxes.
	 * @param RestSchema $rest_schema Rest schema.
	 *
	 * @return void
	 */
	public function __construct( MetaBoxes $meta_boxes, RestSchema $rest_schema, Sanitizer $sanitizer ) {
		$this->meta_boxes  = $meta_boxes;
		$this->rest_schema = $rest_schema;
		$this->sanitizer   = $sanitizer;
	}

	/**
	 * Registers settings.
	 *
	 * @param string $id       The settings ID.
	 * @param array  $settings The settings.
	 *
	 * @return void
	 */
	public static function register_settings( string $id, array $settings ): void {
		add_filter(
			static::HOOK,
			static fn( array $registered_settings ): array => array_merge( $registered_settings, [ $id => $settings ] )
		);
	}

	/**
	 * Get settings.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function get_settings(): array {
		$settings  = apply_filters( self::HOOK, [] );
		$formatted = [];
		$sanitizer = $this->sanitizer;

		foreach ( $settings as $id => $args ) {
			if ( ! isset( $args['name'] ) ) {
				$args['name'] = $id;
			}

			$panels = $args['panels'] ?? [];

			foreach ( $panels as $panel_id => $panel ) {
				$panel['initialOpen'] = $panel['initial_open'] ?? false;

				unset( $panel['initial_open'] );

				$args['panels'][ $panel_id ] = $panel;
			}

			$fields = $args['fields'] ?? [];

			foreach ( $fields as $field_id => $field ) {
				$args['fields'][ $field_id ] = $this->meta_boxes->replace_condition_key( $field, 'setting' );

				if ( isset( $field['show_if'] ) ) {
					$args['fields'][ $field_id ]['showIf'] = $field['show_if'];
					$field['showIf']                       = $field['show_if'];

					unset( $args['fields'][ $field_id ]['show_if'] );
				}

				if ( ! empty( $field['showIf'] ?? [] ) ) {
					foreach ( $field['showIf'] as $index => $showIf ) {
						if ( isset( $showIf['setting'] ) ) {
							$args['fields'][ $field_id ]['showIf'][ $index ]['condition'] = $showIf['setting'];

							unset( $args['fields'][ $field_id ]['showIf'][ $index ]['setting'] );
						}
					}
				}
			}

			$formatted[ $id ] = $args;
		}

		return $formatted;
	}

	/**
	 * Register rest settings.
	 *
	 * @since 1.0.0
	 *
	 * @hook  admin_init
	 * @hook  rest_api_init
	 *
	 * @return void
	 */
	public function register_rest_setting(): void {
		$settings  = $this->get_settings();
		$sanitizer = $this->sanitizer;

		foreach ( $settings as $id => $args ) {
			$field_schema = [];

			foreach ( ( $args['fields'] ?? [] ) as $field_id => $field ) {
				$field_schema[ $field_id ] = $this->rest_schema->get_item_schema( $field ) ?? [
					'type' => 'string',
				];
			}

			register_setting(
				'options',
				$id,
				[
					'description'       => $args['title'] ?? Str::title_case( $id ),
					'type'              => 'object',
					'sanitize_callback' => static fn( $value ) => $sanitizer->sanitize_option( $value, $id, $args ),
					'show_in_rest'      => [
						'schema' => [
							'type'       => 'object',
							'properties' => $field_schema ?? [],
						],
					],
				]
			);
		}
	}

	/**
	 * Register setting pages.
	 *
	 * @since 1.0.0
	 *
	 * @hook  admin_menu
	 *
	 * @return void
	 */
	public function register_setting_pages(): void {
		$settings = $this->get_settings();
		$instance = $this;

		foreach ( $settings as $id => $args ) {
			$page = esc_html( $args['page'] ?? '' );

			if ( empty( $page ) ) {
				continue;
			}

			add_options_page(
				esc_html( $args['title'] ?? Str::title_case( $id ) ),
				esc_html( $args['title'] ?? Str::title_case( $id ) ),
				esc_html( $args['capability'] ?? 'manage_options' ),
				$page,
				static fn() => $instance->render_settings_page( $id ),
				absint( $args['position'] ?? 10 )
			);
		}
	}

	/**
	 * Render setting page.
	 *
	 * @param string $id The settings ID.
	 *
	 * @return void
	 */
	public function render_settings_page( string $id ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__( 'You do not have permission to access this page.', 'fieldify' )
			);

			return;
		}

		printf(
			'<div id="fieldify-settings-%s" class="fieldify-settings-page"></div>',
			esc_attr( $id )
		);
	}

	/**
	 * Register endpoint for saving settings page.
	 *
	 * @since 1.0.0
	 *
	 * @hook  rest_api_init
	 *
	 * @return void
	 */
	public function register_rest_endpoint(): void {
		$settings = $this->get_settings();

		foreach ( $settings as $id => $args ) {
			$page = esc_html( $args['page'] ?? '' );

			if ( empty( $page ) ) {
				continue;
			}

			register_rest_route(
				'fieldify/v1',
				"/settings",
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'save_settings' ],
					'permission_callback' => static fn(): bool => current_user_can( 'manage_options' ),
					'args'                => [
						'id'      => [
							'type'     => 'string',
							'required' => true,
						],
						'options' => [
							'type'     => 'object',
							'required' => false,
						],
					],
				]
			);
		}
	}

	/**
	 * Save settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return void
	 */
	public function save_settings( WP_REST_Request $request ): void {
		$id = $request->get_param( 'id' );

		if ( ! $id ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'Invalid settings ID.', 'fieldify' ),
				]
			);
		}

		$reset = $request->get_param( 'reset' ) ?? false;

		if ( $reset ) {
			update_option( $id, [] );

			wp_send_json_success(
				[
					'message' => esc_html__( 'Reset ', 'fieldify' ) . $id . esc_html__( ' settings.', 'fieldify' ),
				]
			);
		}

		$options = $request->get_param( 'options' ) ?? [];

		if ( empty( $options ) ) {
			wp_send_json_success(
				[
					'message' => esc_html__( 'No options to save.', 'fieldify' ),
				]
			);
		}

		$fields           = $this->get_settings()[ $id ]['fields'] ?? [];
		$existing_options = get_option( $id, [] );

		foreach ( $options as $key => $value ) {
			$field = null;

			foreach ( $fields as $field_id => $field_args ) {
				if ( $field_id === $key ) {
					$field = $field_args;
					break;
				}
			}

			if ( ! $field ) {
				continue;
			}

			if ( ! isset( $field['id'] ) ) {
				$field['id'] = $key;
			}

			$option_name = $field['option_name'] ?? '';

			if ( $option_name ) {
				update_option( $option_name, $this->sanitizer->sanitize( $value, $field ) );

				continue;
			}

			$existing_options[ $key ] = $this->sanitizer->sanitize( $value, $field );
		}

		update_option( $id, $existing_options );

		wp_send_json_success(
			[
				'message' => esc_html__( 'Saved ', 'fieldify' ) . $id . esc_html__( ' settings.', 'fieldify' ),
			]
		);
	}
}
