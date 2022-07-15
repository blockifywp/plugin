<?php

declare( strict_types=1 );

namespace Blockify;

use function add_action;
use function add_filter;
use function str_replace;
use function wp_add_inline_script;
use function wp_enqueue_script;
use function wp_enqueue_style;

add_filter( 'render_block', NS . 'render_video_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_video_block( string $content, array $block ): string {
	if ( 'core/video' !== $block['blockName'] ) {
		return $content;
	}

	$content = str_replace(
		[
			'style="background:',
			'style="background-color:',
		],
		[
			'style="--video--background:',
		],
		$content
	);

	add_action( 'wp_enqueue_scripts', NS . 'video_scripts_styles' );

	return $content;
}

/**
 * Enqueue media element scripts and styles.
 *
 * @since 0.0.2
 *
 * @return void
 */
function video_scripts_styles(): void {
	wp_enqueue_script( 'wp-mediaelement' );
	wp_enqueue_style( 'wp-mediaelement' );

	wp_add_inline_script( 'wp-mediaelement', '
		const videoBlocks = document.getElementsByTagName("video");
		
		[...videoBlocks].forEach(function(videoBlock) {
			const video = new MediaElementPlayer( videoBlock, {
			    videoWidth: "100%",
			    videoHeight: "100%",
			    enableAutosize: true,
			 });
			
			videoBlock.style.width = "100%";
			videoBlock.style.height = "100%"; 
		} );
	' );
}
