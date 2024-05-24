<?php
/**
 * Title: Utility Dark Mode Toggle Switch
 * Slug: dark-mode-toggle-switch
 * Categories: utility
 */
?>
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"},"onclick":""} -->
<div class="wp-block-group">
	<!-- wp:group {"style":{"spacing":{"blockGap":"0"},"border":{"radius":"50px","width":"4px"},"elements":{"link":{"color":{"text":"var:preset|color|neutral-0"}}},"width":{"all":"3.4em"}},"borderColor":"neutral-950","backgroundColor":"neutral-950","textColor":"neutral-0","className":"toggle-switch","layout":{"type":"flex","flexWrap":"nowrap"},"fontSize":"12","onclick":"( () =\u003e {\n    const isDark = document.body.classList.contains( 'is-style-dark' );\n    const currentMode = isDark ? 'dark' : 'light';\n    const nextMode = isDark ? 'light' : 'dark';\n    const cookieValue = isDark ? 'false' : 'true';\n\n    document.body.classList.remove( `is-style-${ currentMode }` );\n    document.body.classList.add( `is-style-${ nextMode }` );\n    document.cookie = `blockifyDarkMode=${ cookieValue };path=/;max-age=86400`;\n} )()"} -->
	<div
		class="wp-block-group toggle-switch has-border-color has-neutral-950-border-color has-neutral-0-color has-neutral-950-background-color has-text-color has-background has-link-color has-12-font-size"
		style="border-width:4px;border-radius:50px">
		<!-- wp:group {"style":{"width":{"all":"100%"}},"className":"hide-light-mode","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
		<div class="wp-block-group hide-light-mode">
			<!-- wp:image {"className":"is-style-icon","iconSet":"wordpress","iconName":"sun","iconSvgString":"\u003csvg xmlns=\u0022http://www.w3.org/2000/svg\u0022 viewBox=\u00220 0 24 24\u0022 role=\u0022img\u0022 aria-labelledby=\u0022icon-65dc3d2729510\u0022 data-icon=\u0022wordpress-sun\u0022 width=\u002224\u0022 height=\u002224\u0022 fill=\u0022currentColor\u0022\u003e\u003ctitle id=\u0022icon-65dc3d2729510\u0022\u003eSun Icon\u003c/title\u003e\u003cpath d=\u0022M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0 6.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5zM12.8 3h-1.5v3h1.5V3zm-1.6 18h1.5v-3h-1.5v3zm6.8-9.8v1.5h3v-1.5h-3zm-12 0H3v1.5h3v-1.5zm9.7 5.6 2.1 2.1 1.1-1.1-2.1-2.1-1.1 1.1zM8.3 7.2 6.2 5.1 5.1 6.2l2.1 2.1 1.1-1.1zM5.1 17.8l1.1 1.1 2.1-2.1-1.1-1.1-2.1 2.1zM18.9 6.2l-1.1-1.1-2.1 2.1 1.1 1.1 2.1-2.1z\u0022\u003e\u003c/path\u003e\u003c/svg\u003e"} -->
			<figure class="wp-block-image is-style-icon"
					style="--wp--custom--icon--url:url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; role=&quot;img&quot; aria-labelledby=&quot;icon-65dc3d2729510&quot; data-icon=&quot;wordpress-sun&quot; width=&quot;24&quot; height=&quot;24&quot; fill=&quot;currentColor&quot;&gt;<title id=&quot;icon-65dc3d2729510&quot;&gt;Sun Icon</title&gt;<path d=&quot;M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0 6.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5zM12.8 3h-1.5v3h1.5V3zm-1.6 18h1.5v-3h-1.5v3zm6.8-9.8v1.5h3v-1.5h-3zm-12 0H3v1.5h3v-1.5zm9.7 5.6 2.1 2.1 1.1-1.1-2.1-2.1-1.1 1.1zM8.3 7.2 6.2 5.1 5.1 6.2l2.1 2.1 1.1-1.1zM5.1 17.8l1.1 1.1 2.1-2.1-1.1-1.1-2.1 2.1zM18.9 6.2l-1.1-1.1-2.1 2.1 1.1 1.1 2.1-2.1z&quot;&gt;</path&gt;</svg&gt;')">
				<img alt=""/></figure>
			<!-- /wp:image --></div>
		<!-- /wp:group -->
		<!-- wp:group {"style":{"width":{"all":"100%"}},"className":"hide-dark-mode","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
		<div class="wp-block-group hide-dark-mode">
			<!-- wp:image {"className":"is-style-icon","iconSet":"wordpress","iconName":"moon","iconSvgString":"\u003csvg xmlns=\u0022http://www.w3.org/2000/svg\u0022 viewBox=\u00220 0 24 24\u0022 role=\u0022img\u0022 aria-labelledby=\u0022icon-65dc3d271f77e\u0022 data-icon=\u0022wordpress-moon\u0022 width=\u002224\u0022 height=\u002224\u0022 fill=\u0022currentColor\u0022\u003e\u003ctitle id=\u0022icon-65dc3d271f77e\u0022\u003eMoon Icon\u003c/title\u003e\u003cpath d=\u0022M17.8 13.5c-2 .5-4.2 0-5.8-1.5s-2.1-3.8-1.5-5.8c.2-.6.4-1.1.7-1.7-.7.1-1.3.3-1.9.5-.9.4-1.8.9-2.6 1.7-2.9 2.9-2.9 7.7 0 10.6 2.9 2.9 7.7 2.9 10.6 0 .8-.8 1.3-1.6 1.7-2.6.2-.6.4-1.3.5-1.9-.5.3-1.1.6-1.7.7zm-1.5 2.7c-2.3 2.3-6.1 2.3-8.5 0-2.3-2.3-2.3-6.1 0-8.5.3-.3.7-.6 1-.8-.2 2.2.5 4.5 2.2 6.1s3.9 2.4 6.1 2.1c-.2.4-.5.8-.8 1.1z\u0022\u003e\u003c/path\u003e\u003c/svg\u003e"} -->
			<figure class="wp-block-image is-style-icon"
					style="--wp--custom--icon--url:url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; role=&quot;img&quot; aria-labelledby=&quot;icon-65dc3d271f77e&quot; data-icon=&quot;wordpress-moon&quot; width=&quot;24&quot; height=&quot;24&quot; fill=&quot;currentColor&quot;&gt;<title id=&quot;icon-65dc3d271f77e&quot;&gt;Moon Icon</title&gt;<path d=&quot;M17.8 13.5c-2 .5-4.2 0-5.8-1.5s-2.1-3.8-1.5-5.8c.2-.6.4-1.1.7-1.7-.7.1-1.3.3-1.9.5-.9.4-1.8.9-2.6 1.7-2.9 2.9-2.9 7.7 0 10.6 2.9 2.9 7.7 2.9 10.6 0 .8-.8 1.3-1.6 1.7-2.6.2-.6.4-1.3.5-1.9-.5.3-1.1.6-1.7.7zm-1.5 2.7c-2.3 2.3-6.1 2.3-8.5 0-2.3-2.3-2.3-6.1 0-8.5.3-.3.7-.6 1-.8-.2 2.2.5 4.5 2.2 6.1s3.9 2.4 6.1 2.1c-.2.4-.5.8-.8 1.1z&quot;&gt;</path&gt;</svg&gt;')">
				<img alt=""/></figure>
			<!-- /wp:image --></div>
		<!-- /wp:group --></div>
	<!-- /wp:group --></div>
<!-- /wp:group -->
