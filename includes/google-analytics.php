<?php

declare(strict_types=1);

namespace Blockify\Plugin;

use function add_action;
use function get_option;

add_action( 'wp_head', NS . 'add_google_analytics_scripts', 1 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_google_analytics_scripts() {
	$id = get_option( SLUG )['googleAnalytics'] ?? '';

	if ( ! $id ) {
		return;
	}

	$script = <<<EOT
<script async src="https://www.googletagmanager.com/gtag/js?id=$id"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','$id');</script>
EOT;

	echo $script;
}
