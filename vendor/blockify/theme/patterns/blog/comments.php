<?php
/**
 * Title: Comments
 * Slug: comments
 * Categories: blog
 * ID: 12154
 */
?>
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:post-comments-form /-->

	<!-- wp:comments {"style":{"typography":{"textDecoration":"none"}},"className":"wp-block-comments-query-loop"} -->
	<div class="wp-block-comments wp-block-comments-query-loop" style="text-decoration:none"><!-- wp:comments-title {"level":4,"style":{"spacing":{"padding":{"top":"var:preset|spacing|xxs","bottom":"var:preset|spacing|xxs"}}}} /-->

		<!-- wp:comment-template -->
		<!-- wp:columns {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|md"}}}} -->
		<div class="wp-block-columns" style="margin-bottom:var(--wp--preset--spacing--md)"><!-- wp:column {"width":"40px"} -->
			<div class="wp-block-column" style="flex-basis:40px"><!-- wp:avatar {"size":40,"style":{"border":{"radius":"40px"}}} /--></div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:comment-author-name {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"className":"is-style-heading"} /-->

				<!-- wp:comment-date {"style":{"spacing":{"margin":{"top":"var:preset|spacing|xxs","bottom":"0"}}},"fontSize":"14"} /-->

				<!-- wp:comment-content /-->

				<!-- wp:comment-reply-link {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"14"} /-->

				<!-- wp:comment-edit-link {"fontSize":"14"} /--></div>
			<!-- /wp:column --></div>
		<!-- /wp:columns -->
		<!-- /wp:comment-template -->

		<!-- wp:spacer {"height":"var:preset|spacing|md"} -->
		<div style="height:var(--wp--preset--spacing--md)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:comments-pagination-previous /-->

		<!-- wp:comments-pagination-numbers /-->

		<!-- wp:comments-pagination-next /-->
		<!-- /wp:comments-pagination --></div>
	<!-- /wp:comments --></div>
<!-- /wp:group -->
