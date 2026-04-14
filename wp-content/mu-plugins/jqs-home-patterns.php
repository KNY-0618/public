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
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-service-overview","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-service-overview has-white-background-color has-background">
<!-- wp:cover {"url":"' . $service_banner . '","id":0,"dimRatio":0,"isUserOverlayColor":true,"minHeight":230,"minHeightUnit":"px"} -->
<div class="wp-block-cover" style="min-height:230px"><img class="wp-block-cover__image-background" alt="" src="' . $service_banner . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
<!-- /wp:cover -->

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"4px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:4px;padding-top:1rem;padding-bottom:1rem">
<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">3つの“お届け”サービス</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html -->
<div class="jqs-service-badge" style="width:90px;height:90px;border-radius:999px;background-color:#ff99cc;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
	<h4 style="margin:0;color:#ffffff;">1</h4>
</div>
<!-- /wp:html -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_1 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":5,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h5 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">スポットチャーター</h5>
<!-- /wp:heading -->

<!-- wp:heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-heading has-text-align-center">緊急輸送</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">急遽発生する配送ニーズにも、全国1,000社のネットワークを活用し、当日納品を実現いたします。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html -->
<div class="jqs-service-badge" style="width:90px;height:90px;border-radius:999px;background-color:#ff99cc;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
	<h4 style="margin:0;color:#ffffff;">2</h4>
</div>
<!-- /wp:html -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_2 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":5,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h5 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">BtoC</h5>
<!-- /wp:heading -->

<!-- wp:heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-heading has-text-align-center">宅配サービス</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">EC市場の成長に伴う宅配ニーズに、20年にわたり業界をリードし、進化し続けるサービスをご提供いたします。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html -->
<div class="jqs-service-badge" style="width:90px;height:90px;border-radius:999px;background-color:#ff99cc;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
	<h4 style="margin:0;color:#ffffff;">3</h4>
</div>
<!-- /wp:html -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $service_image_3 . '" alt="" /></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":5,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h5 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">企業専属チャーター</h5>
<!-- /wp:heading -->

<!-- wp:heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-heading has-text-align-center">定期便</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">お客様の専用車両として自由度が高く、配送プラスアルファのサービスをご提供いたします。</p>
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

	$call_pic = esc_url(home_url('/wp-content/uploads/2026/04/call_pic.png'));
	$callstaff_pic = esc_url(home_url('/wp-content/uploads/2026/04/callstaff_pic.png'));
	$track_pic = esc_url(home_url('/wp-content/uploads/2026/04/track_pic.png'));
	$chokusou_pic = esc_url(home_url('/wp-content/uploads/2026/04/chokusou_pic.png'));
	$two_container_pic = esc_url(home_url('/wp-content/uploads/2026/04/two-container_pic.png'));
	$allow_right_pic = esc_url(home_url('/wp-content/uploads/2026/04/allow_right_pic.png'));

	$service_flow_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"4px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:4px;padding-top:1rem;padding-bottom:1rem">
<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">サービス</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"35%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%">
<!-- wp:heading {"level":5,"textAlign":"center","textColor":"vivid-cyan-blue","style":{"border":{"color":"#3b58b7","width":"1px","style":"solid"},"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"},"margin":{"top":"0","bottom":"0"}}}} -->
<h5 class="wp-block-heading has-text-align-center" style="border-style:solid;border-color:#3b58b7;border-width:2px;color:#3b58b7;margin-top:0;margin-bottom:0;padding-top:0.5rem;padding-bottom:0.5rem">スポットチャーター</h5>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"65%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
<!-- wp:heading {"level":4,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<h4 class="wp-block-heading" style="margin-top:0;margin-bottom:0">緊急輸送</h4>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false} -->
<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile">
<!-- wp:column {"width":"16%"} -->
<div class="wp-block-column" style="flex-basis:16%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $call_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center" style="font-weight:700">発注</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"3%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:3%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $allow_right_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"16%"} -->
<div class="wp-block-column" style="flex-basis:16%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $callstaff_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center" style="font-weight:700">受注</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"3%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:3%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $allow_right_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"16%"} -->
<div class="wp-block-column" style="flex-basis:16%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $track_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">集荷</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"3%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:3%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $allow_right_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"14%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:14%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $chokusou_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"3%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:3%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $allow_right_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"16%"} -->
<div class="wp-block-column" style="flex-basis:16%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $two_container_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">お届け</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"color":{"background":"#ffffa0"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background" style="background-color:#ffffa0;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">24h365日対応　お客様が必要とする時に、<span style="color:#ff99cc;">迅速なサービスをご提供</span>いたします</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/service-flow-spot-charter',
		[
			'title'       => __('Service Flow - Spot Charter', 'default'),
			'description' => __('Service process flow section with editable pictograms and text.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $service_flow_pattern_content,
		]
	);
}
add_action('init', 'jqs_register_home_patterns');

/**
 * Runtime styles for service section corrections.
 */
function jqs_home_patterns_runtime_styles() {
	wp_register_style('jqs-home-patterns-runtime', false, [], null);
	wp_enqueue_style('jqs-home-patterns-runtime');
	wp_add_inline_style(
		'jqs-home-patterns-runtime',
		'[style*="background-color:#3359d3"] { background-color: #344da8 !important; } .jqs-service-overview .jqs-service-badge { width: 90px !important; height: 90px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 1rem auto !important; } .jqs-service-overview .jqs-service-badge > * { margin: 0 !important; line-height: 1 !important; } .wp-block-column > p.has-background.has-white-color.has-text-align-center:has(+ figure.wp-block-image) { width: 90px !important; height: 90px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 1rem auto !important; padding: 0 !important; line-height: 1 !important; background-color: #ff99cc !important; }'
	);
}
add_action('wp_enqueue_scripts', 'jqs_home_patterns_runtime_styles', 30);
