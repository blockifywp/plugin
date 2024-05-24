<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

class RestSchema {

	private const MAP = [
		'text'        => [
			'type' => 'string',
		],
		'textarea'    => [
			'type' => 'string',
		],
		'radio'       => [
			'type' => 'string',
		],
		'color'       => [
			'type' => 'string',
		],
		'blocks'      => [
			'type' => 'string',
		],
		'embed'       => [
			'type' => 'string',
		],
		'code'        => [
			'type' => 'string',
		],
		'number'      => [
			'type' => 'number',
		],
		'range'       => [
			'type' => 'number',
		],
		'image'       => [
			'type' => 'number',
		],
		'checkbox'    => [
			'type' => 'boolean',
		],
		'toggle'      => [
			'type' => 'boolean',
		],
		'select'      => [
			'type'       => 'object',
			'properties' => [
				'value' => [
					'type' => 'string',
				],
				'label' => [
					'type' => 'string',
				],
			],
		],
		'multiselect' => [
			'type'  => 'array',
			'items' => [
				'type'       => 'object',
				'properties' => [
					'value' => [
						'type' => 'string',
					],
					'label' => [
						'type' => 'string',
					],
				],
			],
		],
		'icon'        => [
			'type'       => 'object',
			'properties' => [
				'set'  => [
					'type' => 'string',
				],
				'name' => [
					'type' => 'string',
				],
				'html' => [
					'type' => 'string',
				],
			],
		],
		'gallery'     => [
			'type'  => 'array',
			'items' => [
				'type' => 'number',
			],
		],
		'repeater'    => [
			'type'  => 'array',
			'items' => [
				'type' => 'object',
			],
		],
		'license'     => [
			'type'       => 'object',
			'properties' => [
				'license'       => [
					'type' => 'string',
				],
				'licenseStatus' => [
					'type' => 'string',
				],
			],
		],
		'post'        => [
			'type'       => 'object',
			'properties' => [
				'value' => [
					'type' => 'number',
				],
				'label' => [
					'type' => 'string',
				],
			],
		],
	];

	/**
	 * Get the meta type based on the field type.
	 *
	 * @param array $field Field data.
	 *
	 * @return array
	 */
	public function get_item_schema( array $field ): array {
		$field_type = $field['control'] ?? $field['type'] ?? 'text';

		if ( $field_type === 'select' && ( $field['multiple'] ?? false ) ) {
			$field_type = 'multiselect';
		}

		$schema   = self::MAP[ $field_type ] ?? [ 'type' => 'string' ];
		$sub_type = $schema['items']['type'] ?? null;

		if ( $sub_type === 'object' ) {
			$sub_fields = $field['subfields'] ?? [];

			foreach ( $sub_fields as $sub_field_id => $sub_field ) {
				$schema['items']['properties'][ $sub_field_id ?? $sub_field['id'] ?? '' ] = $this->get_item_schema( $sub_field );
			}
		}

		return $schema;
	}

}
