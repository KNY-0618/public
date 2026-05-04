<?php
/**
 * Plugin Name: Japan Quick Service Noto Sans JP
 * Description: Loads Google Fonts Noto Sans JP across the site and block editor.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Build the Google Fonts URL for Noto Sans JP.
 */
function jqs_get_noto_sans_jp_url() {
	return 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap';
}

/**
 * Apply Noto Sans JP to the public-facing site.
 */
function jqs_enqueue_noto_sans_jp() {
	wp_enqueue_style(
		'jqs-noto-sans-jp',
		jqs_get_noto_sans_jp_url(),
		[],
		null
	);

	wp_register_style('jqs-noto-sans-jp-global', false, ['jqs-noto-sans-jp'], null);
	wp_enqueue_style('jqs-noto-sans-jp-global');
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'body, p, h1, h2, h3, h4, h5, h6, li, a, span, div, button, input, select, textarea { font-family: "Noto Sans JP", sans-serif !important; } #wpadminbar .ab-icon::before, #wpadminbar .ab-item::before, #wpadminbar [class*="dashicons"]::before, .dashicons::before { font-family: dashicons !important; }'
	);

	// Footer 3-column row tweak: size columns by content to reduce unnecessary gaps.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'@media (min-width: 1000px) { [data-footer*="type-1"] .ct-footer [data-row*="top"] > div { grid-template-columns: auto auto minmax(0, 1fr) !important; column-gap: 12px !important; align-items: center !important; } [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column="widget-area-1"] { justify-content: center !important; align-items: center !important; text-align: center !important; } [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column="widget-area-1"] img { width: min(105px, 100%) !important; height: auto !important; margin-inline: auto !important; } [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column*="widget-area"] { font-size: 12px !important; } [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column*="widget-area"] :is(p, li, a, span, div) { font-size: 14px !important; line-height: 1.6 !important; } }'
	);

	// Remove background colors only inside the footer top widget area.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'[data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column*="widget-area"] .has-background, [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column*="widget-area"] .wp-block-group, [data-footer*="type-1"] .ct-footer [data-row*="top"] [data-column*="widget-area"] [style*="background"] { background: transparent !important; }'
	);

	// Header tweak was disabled to avoid interfering with Blocksy Header Builder UI.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'@media (min-width: 1000px) { [data-header*="type-1"] { --header-height: 100px !important; --header-sticky-height: 100px !important; } [data-header*="type-1"] .ct-header [data-row*="middle"] { --height: 100px !important; min-height: 100px !important; height: 100px !important; } [data-header*="type-1"] .ct-header [data-row*="middle"] > div { min-height: 100px !important; height: 100px !important; } [data-header*="type-1"] .ct-header [data-row*="middle"] [data-column] { min-height: 100px !important; } }'
	);

	// Global: disable dark cover overlay when placing text on banner images.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'.wp-block-cover .wp-block-cover__background { opacity: 0 !important; background: transparent !important; }'
	);

	// Header logo: treat sticky logo as "second logo" and keep it visible at far left.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'.ct-header [data-id="logo"] .site-logo-container { display: inline-flex !important; align-items: center !important; gap: 10px !important; }'
		. '.ct-header [data-id="logo"] .site-logo-container img.default-logo { display: block !important; visibility: visible !important; opacity: 1 !important; position: static !important; inset: auto !important; transform: none !important; }'
		. '.ct-header [data-sticky*="yes"] [data-id="logo"] .site-logo-container img.default-logo { display: block !important; visibility: visible !important; opacity: 1 !important; position: static !important; inset: auto !important; transform: none !important; }'
		. '.ct-header [data-id="logo"] .site-logo-container img.sticky-logo { display: block !important; visibility: visible !important; opacity: 1 !important; position: static !important; inset: auto !important; transform: none !important; order: -1 !important; }'
	);
}
add_action('wp_enqueue_scripts', 'jqs_enqueue_noto_sans_jp', 20);

/**
 * Apply Noto Sans JP inside the block editor as well.
 */
function jqs_enqueue_noto_sans_jp_editor() {
	wp_enqueue_style(
		'jqs-noto-sans-jp-editor-font',
		jqs_get_noto_sans_jp_url(),
		[],
		null
	);

	wp_register_style('jqs-noto-sans-jp-editor', false, ['jqs-noto-sans-jp-editor-font'], null);
	wp_enqueue_style('jqs-noto-sans-jp-editor');
	wp_add_inline_style(
		'jqs-noto-sans-jp-editor',
		':root { --wp--preset--font-family--system-font: "Noto Sans JP", sans-serif; } .editor-styles-wrapper, .editor-styles-wrapper * { font-family: "Noto Sans JP", sans-serif !important; } .editor-styles-wrapper .wp-block-cover .wp-block-cover__background { opacity: 0 !important; background: transparent !important; }'
	);
}
add_action('enqueue_block_editor_assets', 'jqs_enqueue_noto_sans_jp_editor');
