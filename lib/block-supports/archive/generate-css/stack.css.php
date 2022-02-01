<?php
/**
 * Creates css class for spacing.
 *
 * If theme blockGap setting exists, create the css class &
 * use WP custom prop--wp--style--block-gap for spacing.
 *
 * By default, WordPress core generates .wp-container-{$id} with a random
 * id for every element pulling in spacing, layout options & places both
 * contentSize & alignment values in the same class. We've chosen to separate
 * those concerns instead. The css class generated here only handles vertical
 * spacing.
 *
 * References every-layout concepts & WP Core defaults.
 * @url https://every-layout.dev/layouts/stack/
 *
 * @param string $selector CSS selector
 */

function wf_create_stack( string $selector ): bool
{
    // pull theme.json blockGap setting
    $get_block_gap    = wp_get_global_settings( array( 'spacing', 'blockGap' ) );

    // Create our CSS class if blockGap is set.
    $style = '';
    if ( isset( $get_block_gap ) ) {
        // use WP default custom prop
        $gap_style = '--wp--style--block-gap';

        // fallback value
        $fallback = '1.5rem';

        // First child, no margin margin-block = margin-top & margin-bottom.
        $style .= "$selector > * { margin-block: 0; }";

        // Apply margin-top (margin-block-start) to children.
        // We use margin-block-start because it applies to ltr / rtl etc by default.
        $style .= "$selector > * + * { margin-block-start: var($gap_style, $fallback); }";

    }
    return add_action(
      'wp_footer',
      function () use ( $style ) {
          echo '<style>' . $style . '</style>';
      }
    );

}
