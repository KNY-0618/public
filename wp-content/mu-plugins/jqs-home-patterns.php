<?php
/**
 * Plugin Name: JQS Home Patterns
 * Description: Registers reusable homepage block patterns for Blocksy.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register homepage pattern category and pattern.
 */
function jqs_register_home_patterns() {
	if (! function_exists('register_block_pattern')) {
		return;
	}

	if (function_exists('register_block_pattern_category')) {
		register_block_pattern_category(
			'jqs-home',
			[
				'label' => __('JQS Home', 'default'),
			]
		);
	}

	$image_url = esc_url(home_url('/wp-content/uploads/2026/04/momotarou_top_img.png'));

	$pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"28%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:28%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $image_url . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"72%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:72%">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">より速く・より正確に・より安全に</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>桃太郎のおとぎ話は、どなたでもご存じです。<br>犬（勇敢、敏速）猿（知恵と計画性）雉（空を飛んで情報収集と慎重性）これらをまとめてシステム化するコーディネーターとしての桃太郎の役割・・・。そして宝物を運ぶ訳であります。<br>このような桃太郎のおとぎ話にあてはめ、「より速く、より正確に、より安全に」をモットーとしたのが「桃太郎便」であります。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/momotaro-intro',
		[
			'title'       => __('Momotaro Intro Section', 'default'),
			'description' => __('Hero-follow section with left logo image and right text/button.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $pattern_content,
		]
	);
}
add_action('init', 'jqs_register_home_patterns');
