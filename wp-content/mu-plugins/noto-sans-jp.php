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
		'body, button, input, select, textarea { font-family: "Noto Sans JP", sans-serif; }'
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
		':root { --wp--preset--font-family--system-font: "Noto Sans JP", sans-serif; } .editor-styles-wrapper, .editor-styles-wrapper button, .editor-styles-wrapper input, .editor-styles-wrapper select, .editor-styles-wrapper textarea { font-family: "Noto Sans JP", sans-serif; }'
	);
}
add_action('enqueue_block_editor_assets', 'jqs_enqueue_noto_sans_jp_editor');
