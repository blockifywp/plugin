<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Icons\Icon;
use Blockify\Utilities\Str;
use function add_filter;
use function array_merge;
use function esc_html__;
use function glob;
use function sprintf;
use const GLOB_ONLYDIR;

add_filter( Icon::FILTER, __NAMESPACE__ . '\add_icon_sets' );
/**
 * Add icon sets.
 *
 * @since 1.5.0
 *
 * @param array $icon_sets Icon sets.
 *
 * @return array
 */
function add_icon_sets( array $icon_sets ): array {
	return array_merge( $icon_sets, get_pro_icon_sets() );
}

add_filter( 'blockify_editor_data', __NAMESPACE__ . '\\add_pro_icon_set_options' );
/**
 * Add Pro icon set options.
 *
 * @since 1.5.0
 *
 * @param array $data Editor data.
 *
 * @return array
 */
function add_pro_icon_set_options( array $data ): array {
	if ( ! isset( $data['pro'] ) ) {
		$data['pro'] = [];
	}

	if ( ! isset( $data['pro']['iconSets'] ) ) {
		$data['pro']['iconSets'] = [];
	}

	$slugs = [
		'hand-drawn',
		'heroicons',
		'feather',
		'phosphor-duotone',
	];

	$sets = get_pro_icon_sets();

	foreach ( $slugs as $slug ) {
		if ( isset( $sets[ $slug ] ) ) {
			continue;
		}

		/* translators: %1$s: Icon set name, %2$s: Pro */
		$title = sprintf(
			'%s (%s)',
			Str::title_case( $slug ),
			esc_html__( 'Pro', 'blockify-plugin' )
		);

		$data['pro']['iconSets'][] = [
			'value'    => $slug,
			'label'    => $title,
			'disabled' => true,
		];
	}

	return $data;
}

/**
 * Get Pro icon sets.
 *
 * @since 1.5.0
 *
 * @return array
 */
function get_pro_icon_sets(): array {
	$dirs = glob( CACHE_DIR . 'icons/*', GLOB_ONLYDIR );

	$icon_sets = [];

	foreach ( $dirs as $dir ) {
		$icon_sets[ basename( $dir ) ] = $dir;
	}

	return $icon_sets;
}
