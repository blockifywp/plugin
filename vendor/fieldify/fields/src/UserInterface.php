<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use function esc_html;
use function get_post_meta;
use function get_post_types;

class UserInterface {

	const POST_TYPE = 'field_group';

	/**
	 * Register user interface.
	 *
	 * @hook after_setup_theme
	 *
	 * @return void
	 */
	public function register(): void {
		PostTypes::register_post_type(
			self::POST_TYPE,
			[
				'allowed_blocks' => [],
				'show_in_rest'   => true,
				'supports'       => [
					'title',
					'editor', // Required for new editor.
					'custom-fields', // Required for meta boxes to save.
					// 'page-attributes', // Required for page templates.
				],
			]
		);

		$all_post_types    = get_post_types( [ 'public' => true ] );
		$post_type_options = [];

		foreach ( $all_post_types as $post_type ) {
			$post_type_options[] = [
				'label' => $post_type,
				'value' => $post_type,
			];
		}

		MetaBoxes::register_meta_box(
			'field_group_settings',
			[
				'title'      => __( 'Field Group Settings', 'fieldify' ),
				'context'    => 'side',
				'priority'   => 'low',
				'post_types' => [ self::POST_TYPE ],
				'fields'     => [
					'post_types' => [
						'type'    => 'select',
						'label'   => __( 'Post Types', 'fieldify' ),
						'options' => $post_type_options,
					],
					'context'    => [
						'type'    => 'select',
						'label'   => __( 'Context', 'fieldify' ),
						'options' => [
							[
								'label' => __( 'Normal', 'fieldify' ),
								'value' => 'normal',
							],
							[
								'label' => __( 'Side', 'fieldify' ),
								'value' => 'side',
							],
							[
								'label' => __( 'Advanced', 'fieldify' ),
								'value' => 'advanced',
							],
						],
					],
					'priority'   => [
						'type'    => 'select',
						'label'   => __( 'Priority', 'fieldify' ),
						'options' => [
							[
								'label' => __( 'High', 'fieldify' ),
								'value' => 'high',
							],
							[
								'label' => __( 'Default', 'fieldify' ),
								'value' => 'default',
							],
							[
								'label' => __( 'Low', 'fieldify' ),
								'value' => 'low',
							],
						],
					],
				],
			]
		);

		MetaBoxes::register_meta_box(
			'fields',
			[
				'title'      => __( 'Field Groups', 'fieldify' ),
				'context'    => 'normal',
				'priority'   => 'high',
				'post_types' => [ self::POST_TYPE ],
				'fields'     => [
					'fields' => [
						'type'      => 'repeater',
						'direction' => 'row',
						'add'       => __( 'Add Field', 'fieldify' ),
						'remove'    => __( 'Remove Field', 'fieldify' ),
						'subfields' => [
							'key'     => [
								'type'  => 'text',
								'label' => __( 'Key', 'fieldify' ),
							],
							'label'   => [
								'type'  => 'text',
								'label' => __( 'Label', 'fieldify' ),
							],
							'type'    => [
								'type'    => 'select',
								'label'   => __( 'Type', 'fieldify' ),
								'options' => [
									[
										'label' => __( 'Text', 'fieldify' ),
										'value' => 'text',
									],
									[
										'label' => __( 'Select', 'fieldify' ),
										'value' => 'select',
									],
								],
							],
							'options' => [
								'type'      => 'repeater',
								'label'     => __( 'Options', 'fieldify' ),
								'add'       => __( 'Add Option', 'fieldify' ),
								'remove'    => __( 'Remove Option', 'fieldify' ),
								'subfields' => [
									'label' => [
										'type'  => 'text',
										'label' => __( 'Label', 'fieldify' ),
									],
									'value' => [
										'type'  => 'text',
										'label' => __( 'Value', 'fieldify' ),
									],
								],
								'show_if'   => [
									[
										'field'    => 'type',
										'operator' => '==',
										'value'    => 'select',
									],
								],
							],
						],
					],
				],
			],
		);
	}

	/**
	 * Registers fields from field groups post type.
	 *
	 * @hook after_setup_theme
	 *
	 * @return void
	 */
	public function register_fields(): void {
		$field_groups = get_posts(
			[
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			]
		);

		foreach ( $field_groups as $field_group ) {
			$post_types = (array) ( get_post_meta( $field_group->ID, 'post_types', true )['value'] ?? [] );
			$context    = get_post_meta( $field_group->ID, 'context', true )['value'] ?? 'normal';
			$priority   = get_post_meta( $field_group->ID, 'priority', true )['value'] ?? 'default';
			$fields     = (array) ( get_post_meta( $field_group->ID, 'fields', true ) ?? [] );

			foreach ( $post_types as $post_type ) {
				MetaBoxes::register_meta_box(
					$field_group->post_name,
					[
						'title'      => $field_group->post_title,
						'context'    => $context,
						'priority'   => $priority,
						'post_types' => [ $post_type ],
						'fields'     => $this->format_fields( $fields ),
					]
				);
			}
		}
	}

	/**
	 * Formats field group fields.
	 *
	 * @param array $fields Field group fields.
	 *
	 * @return array
	 */
	public function format_fields( array $fields ): array {
		$formatted_fields = [];

		foreach ( $fields as $field ) {
			$key     = esc_html( $field['key'] ?? '' );
			$label   = esc_html( $field['label'] ?? '' );
			$type    = esc_html( $field['type']['value'] ?? 'text' );
			$options = $field['options'] ?? [];

			$formatted_fields[ $key ] = [
				'label'   => $label,
				'type'    => $type,
				'options' => $options,
			];
		}

		return $formatted_fields;
	}

	/**
	 * Enqueue editor scripts and styles.
	 *
	 * @hook enqueue_block_editor_assets
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$post_type = get_post_type();

		if ( $post_type !== self::POST_TYPE ) {
			return;
		}

		$css = <<<CSS
.edit-post-layout__metaboxes {
  height: 100%;
}
CSS;

		wp_register_style( self::class, '' );
		wp_add_inline_style( self::class, $css );
		wp_enqueue_style( self::class );
	}
}
