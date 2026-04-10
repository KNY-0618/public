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

	$service_banner = esc_url(home_url('/wp-content/uploads/2026/04/deliveryservice_banner.png'));
	$service_image_1 = esc_url(home_url('/wp-content/uploads/2026/04/deliveryservice1.png'));
	$service_image_2 = esc_url(home_url('/wp-content/uploads/2026/04/deliveryservice2.png'));
	$service_image_3 = esc_url(home_url('/wp-content/uploads/2026/04/deliveryservice3.png'));

	$service_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:cover {"url":"' . $service_banner . '","id":0,"dimRatio":40,"isUserOverlayColor":true,"minHeight":230,"minHeightUnit":"px"} -->
<div class="wp-block-cover" style="min-height:230px"><img class="wp-block-cover__image-background" alt="" src="' . $service_banner . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">サービス概要</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">SERVICE</p>
<!-- /wp:paragraph -->
</div></div>
<!-- /wp:cover -->

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"border":{"color":"#2eb79a","width":"4px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#2eb79a;border-width:4px">
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">3つの“お届け”サービス</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"align":"center","backgroundColor":"vivid-pink-cyan","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-vivid-pink-cyan-background-color has-text-color has-background">1</p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_1 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","textColor":"white","style":{"color":{"background":"#2eb79a"}}} -->
<p class="has-text-align-center has-white-color has-text-color has-background" style="background-color:#2eb79a">スポットチャーター</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">緊急輸送</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">急遽発生する配送ニーズにも、全国1,000社のネットワークを活用し、当日納品を実現いたします。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"align":"center","backgroundColor":"vivid-pink-cyan","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-vivid-pink-cyan-background-color has-text-color has-background">2</p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_2 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","textColor":"white","style":{"color":{"background":"#2eb79a"}}} -->
<p class="has-text-align-center has-white-color has-text-color has-background" style="background-color:#2eb79a">BtoC</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">宅配サービス</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">EC市場の成長に伴う宅配ニーズに、20年にわたり業界をリードし、進化し続けるサービスをご提供いたします。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"align":"center","backgroundColor":"vivid-pink-cyan","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-vivid-pink-cyan-background-color has-text-color has-background">3</p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_3 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"align":"center","textColor":"white","style":{"color":{"background":"#2eb79a"}}} -->
<p class="has-text-align-center has-white-color has-text-color has-background" style="background-color:#2eb79a">企業専属チャーター</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">定期便</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">お客様の専用車両として自由度が高く、配送プラスアルファのサービスをご提供いたします。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/service-overview-cards',
		[
			'title'       => __('Service Overview Cards', 'default'),
			'description' => __('Service banner and 3 delivery service cards section.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $service_pattern_content,
		]
	);
}
add_action('init', 'jqs_register_home_patterns');
