<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use function function_exists;
use function is_array;
use function is_callable;
use function method_exists;
use function sanitize_text_field;

class Sanitizer {

	private const MAP = [

		// Standard sanitizers.
		'textarea' => 'sanitize_text_field',
		'radio'    => 'sanitize_text_field',
		'color'    => 'sanitize_text_field',
		'license'  => 'sanitize_text_field',
		'unit'     => 'sanitize_text_field',
		'embed'    => 'sanitize_url',
		'toggle'   => 'rest_sanitize_boolean',
		'checkbox' => 'rest_sanitize_boolean',
		'number'   => 'floatval',
		'range'    => 'floatval',
		'image'    => 'intval',
		'post'     => 'intval',
		'html'     => 'wp_kses_post',

		// Custom sanitizers.
		'text'     => 'sanitize_input_field',
		'select'   => 'sanitize_select_field',
		'gallery'  => 'sanitize_gallery_field',
		'icon'     => 'sanitize_icon_field',
		'code'     => 'sanitize_code_field',
		'repeater' => 'sanitize_repeater_field',
	];

	/**
	 * Sanitize.
	 *
	 * @param mixed $value      Value.
	 * @param array $field_args Field args.
	 *
	 * @return mixed
	 */
	public function sanitize( $value, array $field_args ) {
		$type = $field_args['type'] ?? 'text';

		if ( ! isset( self::MAP[ $type ] ) ) {
			return sanitize_text_field( $value );
		}

		$callback = self::MAP[ $type ] ?? 'sanitize_text_field';

		if ( method_exists( $this, $callback ) ) {
			$sanitized = $this->$callback( $value, $field_args );
		} elseif ( function_exists( $callback ) ) {
			$sanitized = $callback( $value );
		} else {
			$sanitized = sanitize_text_field( $value );
		}

		return $sanitized;
	}

	/**
	 * Sanitize post meta.
	 *
	 * @param mixed  $meta_value     Value.
	 * @param string $meta_key       Meta key.
	 * @param string $object_type    Object type.
	 * @param string $object_subtype Object subtype.
	 * @param array  $field_args     Field args.
	 *
	 * @return mixed
	 */
	public function sanitize_meta( $meta_value, string $meta_key, string $object_type, string $object_subtype, array $field_args ) {
		$sanitized = $this->sanitize( $meta_value, $field_args );
		$custom    = $field_args['sanitizeCallback'] ?? $field_args['sanitize_callback'] ?? null;

		if ( is_callable( $custom ) ) {
			$sanitized = $custom( $sanitized, $meta_key, $object_type, $object_subtype );
		}

		return $sanitized;
	}

	/**
	 * Sanitize option.
	 *
	 * @param mixed  $original_value Option value.
	 * @param string $option_name    Option name.
	 * @param array  $args           Field args.
	 */
	public function sanitize_option( $original_value, string $option_name, array $args ) {
		if ( empty( $original_value ) ) {
			return [];
		}

		$fields    = $args['fields'] ?? [];
		$sanitized = [];

		foreach ( $original_value as $key => $value ) {
			$field_args = $fields[ $key ] ?? [];

			if ( $field_args ) {
				$sanitized[ $key ] = $this->sanitize( $value, $field_args );
			} else {
				$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}

		$custom = $args['sanitizeCallback'] ?? $args['sanitize_callback'] ?? null;

		if ( is_callable( $custom ) ) {
			$sanitized = $custom( $sanitized, $option_name, $original_value );
		}

		return $sanitized;
	}

	/**
	 * Sanitize input.
	 *
	 * @param mixed $meta_value Value.
	 * @param array $field_args Field args.
	 *
	 * @return string
	 */
	private function sanitize_input_field( $meta_value, array $field_args ): string {
		$input_type = $field_args['input_type'] ?? $field_args['inputType'] ?? 'text';

		$map = [
			'text'     => 'sanitize_text_field',
			'password' => 'sanitize_text_field',
			'hidden'   => 'sanitize_text_field',
			'date'     => 'sanitize_text_field',
			'time'     => 'sanitize_text_field',
			'url'      => 'sanitize_url',
			'email'    => 'sanitize_email',
			'file'     => 'sanitize_file_name',
		];

		if ( ! isset( $map[ $input_type ] ) ) {
			return sanitize_text_field( $meta_value );
		}

		$callback = $map[ $input_type ];

		if ( ! function_exists( $callback ) ) {
			return sanitize_text_field( $meta_value );
		}

		return $callback( $meta_value );
	}

	/**
	 * Sanitize select.
	 *
	 * @param mixed $meta_value Value.
	 * @param array $field_args Field args.
	 *
	 * @return array
	 */
	private function sanitize_select_field( $meta_value, array $field_args ): array {
		if ( ! is_array( $meta_value ) ) {
			return [];
		}

		$multiple = $field_args['multiple'] ?? false;

		if ( $multiple ) {
			foreach ( $meta_value as $option ) {
				foreach ( $option as $key => $val ) {
					$option[ $key ] = sanitize_text_field( $val );
				}
			}
		} else {
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = sanitize_text_field( $val );
			}
		}

		return $meta_value;
	}

	/**
	 * Sanitize gallery.
	 *
	 * @param mixed $meta_value Value.
	 * @param array $field_args Field args.
	 *
	 * @return array
	 */
	private function sanitize_gallery_field( $meta_value, array $field_args ): array {
		if ( ! is_array( $meta_value ) ) {
			return [];
		}

		foreach ( $meta_value as $key => $value ) {
			$meta_value[ $key ] = intval( $value );
		}

		return $meta_value;
	}

	/**
	 * Sanitize icon.
	 *
	 * @param mixed $meta_value Value.
	 * @param array $field_args Field args.
	 *
	 * @return array
	 */
	private function sanitize_icon_field( $meta_value, array $field_args ): array {
		if ( ! is_array( $meta_value ) ) {
			return [];
		}

		foreach ( $meta_value as $key => $value ) {
			$meta_value[ $key ] = sanitize_text_field( $value );
		}

		return $meta_value;
	}

	/**
	 * Sanitize code.
	 *
	 * @param mixed $meta_value Value.
	 * @param array $field_args Field args.
	 *
	 * @return string
	 */
	private function sanitize_code_field( $meta_value, array $field_args ): string {
		if ( ! $meta_value ) {
			return '';
		}

		$map = [
			'js'   => 'esc_js',
			'json' => 'esc_js',
			'css'  => 'strip_tags',
			'html' => 'esc_html',
		];

		$language = $field_args['language'] ?? 'html';

		if ( ! isset( $map[ $language ] ) ) {
			return sanitize_text_field( $meta_value );
		}

		$callback = $map[ $language ];

		if ( ! function_exists( $callback ) ) {
			return sanitize_text_field( $meta_value );
		}

		return $callback( $meta_value );
	}

	/**
	 * Sanitize repeater.
	 *
	 * @param array $field_args Field args.
	 * @param mixed $meta_value Value.
	 *
	 * @return array
	 */
	private function sanitize_repeater_field( $meta_value, array $field_args ): array {
		if ( ! is_array( $meta_value ) ) {
			return [];
		}

		$subfields = $field_args['subfields'] ?? [];

		foreach ( $meta_value as $index => $value ) {
			foreach ( $value as $field_id => $field_value ) {
				$subfield_args = $subfields[ $field_id ] ?? [];

				if ( ! $subfield_args ) {
					continue;
				}

				$type = $subfield_args['type'] ?? null;

				if ( ! $type ) {
					continue;
				}

				$meta_value[ $index ][ $field_id ] = $this->sanitize( $field_value, $subfield_args );
			}
		}

		return $meta_value;
	}
}
