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
		'body, body * { font-family: "Noto Sans JP", sans-serif !important; }'
	);

	// Footer 3-column row tweak: make the privacy-mark column slimmer and centered.
	wp_add_inline_style(
		'jqs-noto-sans-jp-global',
		'@media (min-width: 1025px) { .ct-footer .wp-block-columns.is-layout-flex:has(> .wp-block-column:nth-child(3)) { display: grid; grid-template-columns: 170px minmax(0, 1fr) minmax(0, 1fr); column-gap: 24px; align-items: center; } .ct-footer .wp-block-columns.is-layout-flex:has(> .wp-block-column:nth-child(3)) > .wp-block-column { margin: 0 !important; } .ct-footer .wp-block-columns.is-layout-flex:has(> .wp-block-column:nth-child(3)) > .wp-block-column:first-child { justify-self: center; text-align: center; } .ct-footer .wp-block-columns.is-layout-flex:has(> .wp-block-column:nth-child(3)) > .wp-block-column:first-child img { width: min(140px, 100%); height: auto; } }'
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
		':root { --wp--preset--font-family--system-font: "Noto Sans JP", sans-serif; } .editor-styles-wrapper, .editor-styles-wrapper * { font-family: "Noto Sans JP", sans-serif !important; }'
	);
}
add_action('enqueue_block_editor_assets', 'jqs_enqueue_noto_sans_jp_editor');

/**
 * Apply Noto Sans JP on wp-admin and login screens.
 */
function jqs_enqueue_noto_sans_jp_admin() {
	wp_enqueue_style(
		'jqs-noto-sans-jp-admin-font',
		jqs_get_noto_sans_jp_url(),
		[],
		null
	);

	wp_register_style('jqs-noto-sans-jp-admin', false, ['jqs-noto-sans-jp-admin-font'], null);
	wp_enqueue_style('jqs-noto-sans-jp-admin');
	wp_add_inline_style(
		'jqs-noto-sans-jp-admin',
		'body, body * { font-family: "Noto Sans JP", sans-serif !important; }'
	);
}
add_action('admin_enqueue_scripts', 'jqs_enqueue_noto_sans_jp_admin');
add_action('login_enqueue_scripts', 'jqs_enqueue_noto_sans_jp_admin');
