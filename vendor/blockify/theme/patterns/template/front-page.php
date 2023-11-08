<?php
/**
 * Title: Front Page
 * Slug: front-page
 * Categories: template
 * Block Types: core/post-content
 * Inserter: false
 */

declare(strict_types=1);

namespace Blockify\Theme;

use function get_option;
use function get_post_field;

$front_page = get_option( 'page_on_front', null );
$content    = get_post_field( 'post_content', $front_page );

if ( $content ) : ?>
	<!-- wp:post-content -->
<?php else : ?>
	<!-- wp:pattern {"slug":"page-landing"} /-->
<?php endif; ?>
