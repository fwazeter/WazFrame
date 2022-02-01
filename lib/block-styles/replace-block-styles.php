<?php

/**
 * Removes default Block Styles from WordPress and
 * enqueues theme specific CSS files instead.
 *
 * We do it this way so that we can not only add to
 * a style but also entirely replace values that
 * conflict with design schema.
 * @url https://github.com/WordPress/gutenberg/issues/32051
 * @since 0.0.3
 */

function wf_replace_block_styles() {
	$styles = array(
		// Text Blocks
		'wp-block-paragraph',
		'wp-block-heading',
		'wp-block-list',
		'wp-block-quote',
		'wp-block-code',
		'wp-block-preformatted',
		'wp-block-pullquote',
		'wp-block-table',

		// Media Blocks
		//'wp-block-image',
		//'wp-block-media-text',

		'wp-block-group',
		// Design Blocks
		// 'wp-block-columns',
		'wp-block-buttons',
		'wp-block-button',
		'wp-block-separator',

		// Not in Wires
		'wp-block-columns',
		'wp-block-image',
		'wp-block-media-text',

		'wp-block-archives',
		'wp-block-audio',
		// wp-block
		'wp-block-calendar',
		'wp-block-categories',
		'wp-block-column',
		'wp-block-comment-author-avatar',
		'wp-block-comment-author-name',
		'wp-block-comment-content',
		'wp-block-comment-date',
		'wp-block-comment-edit-link',
		'wp-block-comment-reply-link',
		'wp-block-comment-template',
		'wp-block-comments-pagination-next',
		'wp-block-comments-pagination-numbers',
		'wp-block-comments-pagination-previous',
		'wp-block-comments-pagination',
		'wp-block-comments-query-loop',
		'wp-block-cover',
		'wp-block-embed',
		'wp-block-file',
		'wp-block-freeform',
		'wp-block-gallery',
		'wp-block-home-link',
		'wp-block-html',
		'wp-block-latest-comments',
		'wp-block-latest-posts',
		'wp-block-loginout',
		'wp-block-missing',
		'wp-block-more',
		'wp-block-navigation-area',
		'wp-block-navigation-link',
		'wp-block-navigation-submenu',
		'wp-block-navigation',
		'wp-block-nextpage',
		'wp-block-page-list',
		'wp-block-pattern',
		'wp-block-post-author-name',
		'wp-block-post-author',
		'wp-block-post-comment',
		'wp-block-post-comments-link',
		'wp-block-post-comments',
		'wp-block-post-content',
		'wp-block-post-date',
		'wp-block-post-excerpt',
		'wp-block-post-featured-image',
		'wp-block-post-navigation-link',
		'wp-block-post-template',
		'wp-block-post-terms',
		'wp-block-post-title',
		'wp-block-query-pagination-next',
		'wp-block-query-pagination-numbers',
		'wp-block-query-pagination-previous',
		'wp-block-query-pagination',
		'wp-block-query-title',
		'wp-block-query',
		'wp-block-rss',
		'wp-block-search',
		'wp-block-shortcode',
		'wp-block-site-logo',
		'wp-block-site-tagline',
		'wp-block-site-title',
		'wp-block-social-link',
		'wp-block-social-links',
		'wp-block-spacer',
		'wp-block-table-of-contents',
		'wp-block-tag-cloud',
		'wp-block-template-part',
		'wp-block-term-description',
		'wp-block-text-columns',
		'wp-block-utils',
		'wp-block-verse',
		'wp-block-video',

	);
	foreach ( $styles as $style ) {
		// Remove the block style
		wp_deregister_style( $style );

		// Register the theme style
		//$register_style = str_replace( 'wp-block-', '', $style );
		//$style_url      = get_theme_file_uri( "/assets/css/blocks/{$register_style}.css" );
		// If the file exists, register the style.
		//if ( file_exists( get_theme_file_path( "assets/css/blocks/{$register_style}.css" ) ) ) {
			//wp_register_style( $style,
				//$style_url,
				//''
			//);
		//}
	}
}

add_action( 'wp_enqueue_scripts', 'wf_replace_block_styles', 20 );
add_action( 'enqueue_block_editor_assets', 'wf_replace_block_styles', 20 );