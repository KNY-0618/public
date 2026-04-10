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
<!-- wp:group {"align":"full","style":{"color":{"background":"#f2f2f2"},"spacing":{"padding":{"top":"72px","bottom":"72px","left":"0","right":"0"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#f2f2f2;padding-top:72px;padding-right:0;padding-bottom:72px;padding-left:0">
<!-- wp:columns {"verticalAlignment":"center","className":"jqs-momotaro-intro","style":{"spacing":{"blockGap":{"left":"56px"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-momotaro-intro">
<!-- wp:column {"width":"30%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-momotaro-intro__image"} -->
<figure class="wp-block-image size-full jqs-momotaro-intro__image"><img src="' . $image_url . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"70%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:70%">
<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"72px","fontWeight":"700","lineHeight":"1.2"}},"className":"jqs-momotaro-intro__title"} -->
<h2 class="wp-block-heading jqs-momotaro-intro__title" style="font-size:72px;font-style:normal;font-weight:700;line-height:1.2">より速く・より正確に・より安全に</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"25px","lineHeight":"1.9"}},"className":"jqs-momotaro-intro__text"} -->
<p class="jqs-momotaro-intro__text" style="font-size:25px;line-height:1.9">桃太郎のおとぎ話は、どなたでもご存じです。<br>犬（勇敢、敏速）猿（知恵と計画性）雉（空を飛んで情報収集と慎重性）これらをまとめてシステム化するコーディネーターとしての桃太郎の役割・・・。そして宝物を運ぶ訳であります。<br>このような桃太郎のおとぎ話にあてはめ、「より速く、より正確に、より安全に」をモットーとしたのが「桃太郎便」であります。</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"36px"}}}} -->
<div class="wp-block-buttons" style="margin-top:36px">
<!-- wp:button {"backgroundColor":"vivid-pink-cyan","textColor":"white","width":100,"style":{"border":{"radius":"0px"},"typography":{"fontSize":"24px","fontWeight":"600"},"spacing":{"padding":{"top":"18px","bottom":"18px"}}}} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link has-white-color has-vivid-pink-cyan-background-color has-text-color has-background wp-element-button" style="border-radius:0px;padding-top:18px;padding-bottom:18px;font-size:24px;font-style:normal;font-weight:600">Amazon配送のお問い合わせ・再配達のご依頼・お荷物の追跡はコチラ</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
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

/**
 * Add responsive style for the pattern layout.
 */
function jqs_home_patterns_style() {
	wp_register_style('jqs-home-patterns-style', false, [], null);
	wp_enqueue_style('jqs-home-patterns-style');
	wp_add_inline_style(
		'jqs-home-patterns-style',
		'.jqs-momotaro-intro__image img { width: 100%; max-width: 420px; height: auto; } @media (max-width: 1024px) { .jqs-momotaro-intro { gap: 32px !important; } .jqs-momotaro-intro__title { font-size: 46px !important; } .jqs-momotaro-intro__text { font-size: 20px !important; line-height: 1.8 !important; } } @media (max-width: 781px) { .jqs-momotaro-intro__title { font-size: 34px !important; } .jqs-momotaro-intro__text { font-size: 16px !important; line-height: 1.75 !important; } }'
	);
}
add_action('wp_enqueue_scripts', 'jqs_home_patterns_style', 25);
