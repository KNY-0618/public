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

<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"4px"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:4px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:heading {"level":2,"textAlign":"center","style":{"color":{"text":"#3b58b7"}}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#3b58b7">3つの“お届け”サービス</h2>
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
<div class="jqs-service-badge" style="width:70px;height:70px;border-radius:999px;background-color:#ff76ba;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
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
<div class="jqs-service-badge" style="width:70px;height:70px;border-radius:999px;background-color:#ff76ba;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
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
<div class="jqs-service-badge" style="width:70px;height:70px;border-radius:999px;background-color:#ff76ba;margin:0 auto 1rem auto;display:flex;align-items:center;justify-content:center;">
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
	$buy_icon_pic = esc_url(home_url('/wp-content/uploads/2026/04/buy_icon.png'));
	$packing_icon_pic = esc_url(home_url('/wp-content/uploads/2026/04/packing_icon.png'));
	$delivery_time_pic = esc_url(home_url('/wp-content/uploads/2026/04/delivary_pic.png'));
	$collect_icon_pic = esc_url(home_url('/wp-content/uploads/2026/04/collect_pic.png'));
	$recruitment_pic = esc_url(home_url('/wp-content/uploads/2026/04/recruitment_pic.png'));
	$education_pic = esc_url(home_url('/wp-content/uploads/2026/04/education_pic.png'));
	$timeshift_pic = esc_url(home_url('/wp-content/uploads/2026/04/timeshift_pic.png'));
	$management_pic = esc_url(home_url('/wp-content/uploads/2026/04/management_pic.png'));
	$track_big_pac = esc_url(home_url('/wp-content/uploads/2026/04/track_big_pac.png'));
	$track_middle_pic = esc_url(home_url('/wp-content/uploads/2026/04/track_middle_pic.png'));
	$track_small_pic = esc_url(home_url('/wp-content/uploads/2026/04/track_small_pic.png'));
	$recruit_bg_pic = esc_url(home_url('/wp-content/uploads/2026/04/recruit_bg.png'));
	$recruit_newgrad_pic = esc_url(home_url('/wp-content/uploads/2026/04/recruit_newgrad.png'));
	$recruit_driver_pic = esc_url(home_url('/wp-content/uploads/2026/04/recruit_driver.png'));
	$about_us_pic = esc_url(home_url('/wp-content/uploads/2026/04/about_us_pic.png'));
	$about_us_link = esc_url(home_url('/about/'));
	$recruit_newgrad_link = esc_url(home_url('/recruit-newgraduate/'));
	$recruit_driver_link = esc_url(home_url('/recruit-driver/'));
	$link_pic_hd_logo = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_hd_logo.png'));
	$link_pic_br04 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br04.jpg'));
	$link_pic_br05 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br05.jpg'));
	$link_pic_br06 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br06.png'));
	$link_pic_mynavi = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_mynavi.png'));
	$link_pic_insta = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_insta.png'));
	$link_pic_enga = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_enga.png'));
	$footer_logo_links = jqs_get_footer_logo_links();
	$link_url_hd_logo = esc_url($footer_logo_links['hd_logo']);
	$link_url_br04 = esc_url($footer_logo_links['br04']);
	$link_url_br05 = esc_url($footer_logo_links['br05']);
	$link_url_br06 = esc_url($footer_logo_links['br06']);
	$link_url_mynavi = esc_url($footer_logo_links['mynavi']);
	$link_url_insta = esc_url($footer_logo_links['insta']);
	$link_url_enga = esc_url($footer_logo_links['enga']);
	$office_map_tokyo = esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode('東京都荒川区南千住3-5-20'));
	$office_map_osaka = esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode('大阪府吹田市南金田2-5-30'));
	$office_map_nagoya = esc_url('https://www.google.com/maps/search/?api=1&query=' . rawurlencode('愛知県名古屋市中村区岩塚町字高道1-1 ロジポート名古屋 A棟316号'));

	$service_flow_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"4px"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:4px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:heading {"level":2,"textAlign":"center","style":{"color":{"text":"#3b58b7"}}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#3b58b7">サービス</h2>
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
<!-- wp:heading {"level":5,"textAlign":"center","style":{"border":{"color":"#3b58b7","width":"2px","style":"solid"},"color":{"text":"#3b58b7"},"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"},"margin":{"top":"0","bottom":"0"}}}} -->
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
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
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
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
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
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">集荷</p>
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
<!-- wp:group {"style":{"border":{"color":"#ffffff","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#ffffff;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $chokusou_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
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
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">お届け</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background" style="background-color:#fff5dc;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">24h365日対応　お客様が必要とする時に、<span style="color:#ff76ba;">迅速なサービスをご提供</span>いたします</p>
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

	$service_flow_btoc_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"35%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%">
<!-- wp:heading {"level":5,"textAlign":"center","style":{"border":{"color":"#3b58b7","width":"2px","style":"solid"},"color":{"text":"#3b58b7"},"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"},"margin":{"top":"0","bottom":"0"}}}} -->
<h5 class="wp-block-heading has-text-align-center" style="border-style:solid;border-color:#3b58b7;border-width:2px;color:#3b58b7;margin-top:0;margin-bottom:0;padding-top:0.5rem;padding-bottom:0.5rem">BtoC</h5>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"65%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
<!-- wp:heading {"level":4,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<h4 class="wp-block-heading" style="margin-top:0;margin-bottom:0">宅配サービス</h4>
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
<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $buy_icon_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">購入</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $packing_icon_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">梱包</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $track_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">集荷</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $delivery_time_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">お届け</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $collect_icon_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">回収</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background" style="background-color:#fff5dc;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">当日お届けサービスによる、<span style="color:#ff76ba;">快適なお買い物</span>をご提供いたします</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/service-flow-btoc',
		[
			'title'       => __('Service Flow - BtoC', 'default'),
			'description' => __('BtoC delivery service flow section with editable pictograms and text.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $service_flow_btoc_pattern_content,
		]
	);

	$service_flow_dedicated_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"35%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%">
<!-- wp:heading {"level":5,"textAlign":"center","style":{"border":{"color":"#3b58b7","width":"2px","style":"solid"},"color":{"text":"#3b58b7"},"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"},"margin":{"top":"0","bottom":"0"}}}} -->
<h5 class="wp-block-heading has-text-align-center" style="border-style:solid;border-color:#3b58b7;border-width:2px;color:#3b58b7;margin-top:0;margin-bottom:0;padding-top:0.5rem;padding-bottom:0.5rem">企業専属チャーター</h5>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"65%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
<!-- wp:heading {"level":4,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<h4 class="wp-block-heading" style="margin-top:0;margin-bottom:0">定期便</h4>
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
<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $recruitment_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">採用</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $education_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">教育</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $timeshift_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">シフト</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $management_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">JQS一括管理</p>
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

<!-- wp:column {"width":"17%"} -->
<div class="wp-block-column" style="flex-basis:17%">
<!-- wp:group {"style":{"border":{"color":"#3b58b7","width":"2px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#3b58b7;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $track_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">専属車両</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-background" style="background-color:#fff5dc;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">お客様の代理という高い意識で、お取引先様へお届けいたします<br>お客様の物流にかかわるトータルコスト削減につなげてまいります</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/service-flow-dedicated-charter',
		[
			'title'       => __('Service Flow - Dedicated Charter', 'default'),
			'description' => __('Dedicated charter regular service flow section with editable pictograms and text.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $service_flow_dedicated_pattern_content,
		]
	);

	$vehicle_types_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-vehicle-types","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-vehicle-types has-white-background-color has-background">
<!-- wp:columns {"verticalAlignment":"center","className":"jqs-vehicle-types__header","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-vehicle-types__header">
<!-- wp:column {"width":"10%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:10%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-vehicle-title-icon"} -->
<figure class="wp-block-image size-full jqs-vehicle-title-icon"><img src="' . $track_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"90%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:90%">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">車両紹介</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"700"}}} -->
<p style="font-weight:700">お客様のご用途に合わせた3タイプ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"center","className":"jqs-vehicle-row","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-vehicle-row">
<!-- wp:column {"width":"55.45%","className":"jqs-vehicle-row__left"} -->
<div class="wp-block-column jqs-vehicle-row__left" style="flex-basis:55.45%">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.4rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-label","style":{"color":{"background":"#27b397","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-label has-text-color has-background" style="color:#ffffff;background-color:#27b397;font-weight:700">積載量</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-level","style":{"color":{"background":"#ff76ba","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-level has-text-color has-background" style="color:#ffffff;background-color:#ff76ba;font-weight:700">大</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-vehicle-photo"} -->
<figure class="wp-block-image size-full jqs-vehicle-photo"><img src="' . $track_big_pac . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"44.55%","className":"jqs-vehicle-row__right"} -->
<div class="wp-block-column jqs-vehicle-row__right" style="flex-basis:44.55%">
<!-- wp:group {"style":{"border":{"color":"#d6d6d6","width":"2px"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#d6d6d6;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:heading {"level":4,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h4 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">幌車両/パネル車両</h4>
<!-- /wp:heading -->
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontWeight":"700"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="font-weight:700">かさばる荷物に最適</h3>
<!-- /wp:heading -->
<!-- wp:group {"className":"jqs-vehicle-spec-wrap","style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"0.6rem","right":"0.6rem","bottom":"0.6rem","left":"0.6rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-vehicle-spec-wrap has-background" style="background-color:#fff5dc;padding-top:0.6rem;padding-right:0.6rem;padding-bottom:0.6rem;padding-left:0.6rem">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">サイズ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>高さ150cm、幅120cm、奥行180cm</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">ご利用例</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>生花、展示会資材などの輸送</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"jqs-vehicle-separator"} -->
<hr class="wp-block-separator jqs-vehicle-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:columns {"verticalAlignment":"center","className":"jqs-vehicle-row","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-vehicle-row">
<!-- wp:column {"width":"55.45%","className":"jqs-vehicle-row__left"} -->
<div class="wp-block-column jqs-vehicle-row__left" style="flex-basis:55.45%">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.4rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-label","style":{"color":{"background":"#27b397","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-label has-text-color has-background" style="color:#ffffff;background-color:#27b397;font-weight:700">積載量</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-level","style":{"color":{"background":"#ff76ba","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-level has-text-color has-background" style="color:#ffffff;background-color:#ff76ba;font-weight:700">中</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-vehicle-photo"} -->
<figure class="wp-block-image size-full jqs-vehicle-photo"><img src="' . $track_middle_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"44.55%","className":"jqs-vehicle-row__right"} -->
<div class="wp-block-column jqs-vehicle-row__right" style="flex-basis:44.55%">
<!-- wp:group {"style":{"border":{"color":"#d6d6d6","width":"2px"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#d6d6d6;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:heading {"level":4,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h4 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">タウン車両</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">車高制限対応2.1m<br>セキュリティ万全</p>
<!-- /wp:paragraph -->
<!-- wp:group {"className":"jqs-vehicle-spec-wrap","style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"0.6rem","right":"0.6rem","bottom":"0.6rem","left":"0.6rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-vehicle-spec-wrap has-background" style="background-color:#fff5dc;padding-top:0.6rem;padding-right:0.6rem;padding-bottom:0.6rem;padding-left:0.6rem">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">サイズ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>高さ110cm、幅120cm、奥行180cm</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">ご利用例</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>宅配、機密文書、社内メールなどの輸送</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"jqs-vehicle-separator"} -->
<hr class="wp-block-separator jqs-vehicle-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:columns {"verticalAlignment":"center","className":"jqs-vehicle-row","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-vehicle-row">
<!-- wp:column {"width":"55.45%","className":"jqs-vehicle-row__left"} -->
<div class="wp-block-column jqs-vehicle-row__left" style="flex-basis:55.45%">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.4rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-label","style":{"color":{"background":"#27b397","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-label has-text-color has-background" style="color:#ffffff;background-color:#27b397;font-weight:700">積載量</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%">
<!-- wp:paragraph {"align":"center","className":"jqs-capacity-level","style":{"color":{"background":"#ff76ba","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-capacity-level has-text-color has-background" style="color:#ffffff;background-color:#ff76ba;font-weight:700">小</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-vehicle-photo"} -->
<figure class="wp-block-image size-full jqs-vehicle-photo"><img src="' . $track_small_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"44.55%","className":"jqs-vehicle-row__right"} -->
<div class="wp-block-column jqs-vehicle-row__right" style="flex-basis:44.55%">
<!-- wp:group {"style":{"border":{"color":"#d6d6d6","width":"2px"},"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1rem","left":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color" style="border-color:#d6d6d6;border-width:2px;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:heading {"level":4,"textAlign":"center","textColor":"white","style":{"color":{"background":"#3b58b7"}}} -->
<h4 class="wp-block-heading has-text-align-center has-white-color has-text-color has-background" style="background-color:#3b58b7">ワンボックス車両</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center" style="font-weight:700">車高制限対応2.1m<br>空調対応可</p>
<!-- /wp:paragraph -->
<!-- wp:group {"className":"jqs-vehicle-spec-wrap","style":{"color":{"background":"#fff5dc"},"spacing":{"padding":{"top":"0.6rem","right":"0.6rem","bottom":"0.6rem","left":"0.6rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-vehicle-spec-wrap has-background" style="background-color:#fff5dc;padding-top:0.6rem;padding-right:0.6rem;padding-bottom:0.6rem;padding-left:0.6rem">
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">サイズ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>高さ110cm、幅120cm、奥行160cm</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph {"align":"center","className":"jqs-spec-tag","style":{"color":{"background":"#3b58b7","text":"#ffffff"},"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center jqs-spec-tag has-text-color has-background" style="color:#ffffff;background-color:#3b58b7;font-weight:700">ご利用例</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>宅配、医薬品などの輸送</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/vehicle-introduction-types',
		[
			'title'       => __('Vehicle Introduction Types', 'default'),
			'description' => __('Vehicle introduction section with 3 vehicle type rows.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $vehicle_types_pattern_content,
		]
	);

	$news_links_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-news-links","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-news-links has-white-background-color has-background">
<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"2rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"25%","style":{"border":{"right":{"color":"#d3d3d3","width":"1px"}},"spacing":{"padding":{"right":"2rem"}}}} -->
<div class="wp-block-column" style="border-right-color:#d3d3d3;border-right-width:1px;padding-right:2rem;flex-basis:25%">
<!-- wp:heading {"level":5,"className":"jqs-news-links__label","style":{"color":{"text":"#000000"},"typography":{"fontSize":"16pt","fontWeight":"700","lineHeight":"1.2"}}} -->
<h5 class="wp-block-heading jqs-news-links__label has-text-color" style="color:#000000;font-size:16pt;font-style:normal;font-weight:700;line-height:1.2">お知らせ</h5>
<!-- /wp:heading -->
<!-- wp:heading {"level":3,"textColor":"blue","style":{"typography":{"fontWeight":"900","lineHeight":"1.1"},"color":{"text":"#22326e"}}} -->
<h3 class="wp-block-heading has-blue-color has-text-color" style="color:#22326e;font-style:normal;font-weight:900;line-height:1.1">NEWS</h3>
<!-- /wp:heading -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"75%","className":"jqs-news-links__list"} -->
<div class="wp-block-column jqs-news-links__list" style="flex-basis:75%">
<!-- wp:columns {"isStackedOnMobile":false} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%">
<!-- wp:paragraph -->
<p>2024.7.30</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%">
<!-- wp:paragraph -->
<p>プライバシーマークの更新が完了しました。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"isStackedOnMobile":false} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%">
<!-- wp:paragraph -->
<p>2024.7.11</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%">
<!-- wp:paragraph -->
<p>2024年7月24日（水）<a href="https://example.com/recruit-fair" target="_blank" rel="noopener" style="color:#2f4de0;">「東京都建設運輸業界就職フェア」</a>に参加しました。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"isStackedOnMobile":false} -->
<div class="wp-block-columns is-not-stacked-on-mobile">
<!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%">
<!-- wp:paragraph -->
<p>2024.2.1</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%">
<!-- wp:paragraph -->
<p>AZ-COM丸和グループ　<a href="https://example.com/hotline" target="_blank" rel="noopener" style="color:#2f4de0;">「パートナー企業様ホットライン」</a>を設置しました。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/news-links-list',
		[
			'title'       => __('News Links List', 'default'),
			'description' => __('News list section with blue external text links.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $news_links_pattern_content,
		]
	);

	$recruitment_links_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-recruitment-links","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-recruitment-links has-white-background-color has-background">
<!-- wp:cover {"url":"' . $recruit_bg_pic . '","dimRatio":0,"isUserOverlayColor":true,"minHeight":280,"minHeightUnit":"px","style":{"spacing":{"margin":{"bottom":"0"}}}} -->
<div class="wp-block-cover" style="min-height:280px;margin-bottom:0"><img class="wp-block-cover__image-background" alt="" src="' . $recruit_bg_pic . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:heading {"level":1,"textAlign":"center","textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color">求人情報</h1>
<!-- /wp:heading -->
</div></div>
<!-- /wp:cover -->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":2,"textAlign":"center","textColor":"vivid-pink","style":{"color":{"text":"#e63888"}}} -->
<h2 class="wp-block-heading has-text-align-center has-vivid-pink-color has-text-color" style="color:#e63888">共に成長するスタッフ募集</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">当社は、AZ-COM丸和ホールディングス株式会社を中心としたAZ-COM丸和グループの一員として、東京都荒川区を拠点に、貨物軽自動車運送事業を行っています。桃太郎のおとぎ話になぞらえた「より速く、より正確に、より安全に」をモットーにした「桃太郎便」による、荷物の輸配送やさまざまなサービスを提供しています。</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"36px"} -->
<div style="height:36px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"isStackedOnMobile":false,"className":"jqs-recruitment-cards","style":{"spacing":{"blockGap":{"left":"0.6rem"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile jqs-recruitment-cards">
<!-- wp:column {"className":"jqs-recruitment-card"} -->
<div class="wp-block-column jqs-recruitment-card">
<!-- wp:image {"width":"400px","sizeSlug":"full","linkDestination":"none","className":"jqs-recruitment-card-image","style":{"spacing":{"margin":{"left":"auto","right":"auto"}}}} -->
<figure class="wp-block-image size-full is-resized jqs-recruitment-card-image" style="margin-right:auto;margin-left:auto"><img src="' . $recruit_newgrad_pic . '" alt="" style="width:400px"/></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"jqs-recruitment-card-button","style":{"color":{"background":"#e63888"},"spacing":{"padding":{"top":"0.85rem","bottom":"0.85rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-recruitment-card-button has-background" style="background-color:#e63888;padding-top:0.85rem;padding-bottom:0.85rem">
<!-- wp:paragraph {"align":"center","textColor":"white","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-weight:700">新卒採用</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:html -->
<a class="jqs-recruitment-card-link-overlay" href="' . $recruit_newgrad_link . '" aria-label="新卒採用"></a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"jqs-recruitment-card"} -->
<div class="wp-block-column jqs-recruitment-card">
<!-- wp:image {"width":"400px","sizeSlug":"full","linkDestination":"none","className":"jqs-recruitment-card-image","style":{"spacing":{"margin":{"left":"auto","right":"auto"}}}} -->
<figure class="wp-block-image size-full is-resized jqs-recruitment-card-image" style="margin-right:auto;margin-left:auto"><img src="' . $recruit_driver_pic . '" alt="" style="width:400px"/></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"jqs-recruitment-card-button","style":{"color":{"background":"#e63888"},"spacing":{"padding":{"top":"0.85rem","bottom":"0.85rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-recruitment-card-button has-background" style="background-color:#e63888;padding-top:0.85rem;padding-bottom:0.85rem">
<!-- wp:paragraph {"align":"center","textColor":"white","style":{"typography":{"fontWeight":"700"}}} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-weight:700">ドライバー採用</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:html -->
<a class="jqs-recruitment-card-link-overlay" href="' . $recruit_driver_link . '" aria-label="ドライバー採用"></a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/recruitment-links',
		[
			'title'       => __('Recruitment Links', 'default'),
			'description' => __('Recruitment banner with two linked cards.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $recruitment_links_pattern_content,
		]
	);

	$about_us_banner_pattern_content = '
<!-- wp:group {"align":"full","style":{"color":{"background":"#f1f1f1"},"spacing":{"padding":{"top":"2rem","bottom":"2rem"}}},"className":"jqs-about-us-banner","layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group alignfull jqs-about-us-banner has-background" style="background-color:#f1f1f1;padding-top:2rem;padding-bottom:2rem">
<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $about_us_link . '","className":"jqs-about-us-banner-image"} -->
<figure class="wp-block-image size-full jqs-about-us-banner-image"><a href="' . $about_us_link . '"><img src="' . $about_us_pic . '" alt="" /></a></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/about-us-banner-link',
		[
			'title'       => __('About Us Banner Link', 'default'),
			'description' => __('900px banner on light gray background with page link.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $about_us_banner_pattern_content,
		]
	);

	$office_list_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-office-list","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-office-list has-white-background-color has-background">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">株式会社ジャパンクイックサービス</h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"0.8rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"22%"} -->
<div class="wp-block-column" style="flex-basis:22%">
<!-- wp:paragraph {"textColor":"white","style":{"color":{"background":"#22326e"},"spacing":{"padding":{"top":"0.65rem","right":"1rem","bottom":"0.65rem","left":"1rem"}},"typography":{"fontWeight":"700"}}} -->
<p class="has-white-color has-text-color has-background" style="background-color:#22326e;padding-top:0.65rem;padding-right:1rem;padding-bottom:0.65rem;padding-left:1rem;font-weight:700">東京営業所<br>EC関東事業所</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%">
<!-- wp:paragraph -->
<p>東京都荒川区南千住3-5-20</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph -->
<p>TEL / 03-3807-1000　FAX / 03-3807-1019</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:html -->
<a href="' . $office_map_tokyo . '" target="_blank" rel="noopener" style="display:block;background:#b3b3b3;color:#fff;text-align:center;text-decoration:none;padding:0.6rem 0.4rem;">GoogleMAP</a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"0.8rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"22%"} -->
<div class="wp-block-column" style="flex-basis:22%">
<!-- wp:paragraph {"textColor":"white","style":{"color":{"background":"#22326e"},"spacing":{"padding":{"top":"0.65rem","right":"1rem","bottom":"0.65rem","left":"1rem"}},"typography":{"fontWeight":"700"}}} -->
<p class="has-white-color has-text-color has-background" style="background-color:#22326e;padding-top:0.65rem;padding-right:1rem;padding-bottom:0.65rem;padding-left:1rem;font-weight:700">大阪営業所<br>EC関西事業所</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%">
<!-- wp:paragraph -->
<p>大阪府吹田市南金田2-5-30</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph -->
<p>TEL / 06-6369-1441　FAX / 06-6369-0113</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:html -->
<a href="' . $office_map_osaka . '" target="_blank" rel="noopener" style="display:block;background:#b3b3b3;color:#fff;text-align:center;text-decoration:none;padding:0.6rem 0.4rem;">GoogleMAP</a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"0.8rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"22%"} -->
<div class="wp-block-column" style="flex-basis:22%">
<!-- wp:paragraph {"textColor":"white","style":{"color":{"background":"#22326e"},"spacing":{"padding":{"top":"0.65rem","right":"1rem","bottom":"0.65rem","left":"1rem"}},"typography":{"fontWeight":"700"}}} -->
<p class="has-white-color has-text-color has-background" style="background-color:#22326e;padding-top:0.65rem;padding-right:1rem;padding-bottom:0.65rem;padding-left:1rem;font-weight:700">大阪営業所 名古屋デポ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%">
<!-- wp:paragraph -->
<p>愛知県名古屋市中村区岩塚町字高道1-1<br>ロジポート名古屋 A棟316号</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph -->
<p>TEL / 052-211-9700　FAX / 052-211-9701</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:html -->
<a href="' . $office_map_nagoya . '" target="_blank" rel="noopener" style="display:block;background:#b3b3b3;color:#fff;text-align:center;text-decoration:none;padding:0.6rem 0.4rem;">GoogleMAP</a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"0.8rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"width":"22%"} -->
<div class="wp-block-column" style="flex-basis:22%">
<!-- wp:paragraph {"textColor":"white","style":{"color":{"background":"#22326e"},"spacing":{"padding":{"top":"0.65rem","right":"1rem","bottom":"0.65rem","left":"1rem"}},"typography":{"fontWeight":"700"}}} -->
<p class="has-white-color has-text-color has-background" style="background-color:#22326e;padding-top:0.65rem;padding-right:1rem;padding-bottom:0.65rem;padding-left:1rem;font-weight:700">管理部</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%">
<!-- wp:paragraph -->
<p>東京都荒川区南千住3-5-20</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"28%"} -->
<div class="wp-block-column" style="flex-basis:28%">
<!-- wp:paragraph -->
<p>TEL / 03-6806-5520　FAX / 03-3807-1715</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:html -->
<a href="' . $office_map_tokyo . '" target="_blank" rel="noopener" style="display:block;background:#b3b3b3;color:#fff;text-align:center;text-decoration:none;padding:0.6rem 0.4rem;">GoogleMAP</a>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/company-office-list',
		[
			'title'       => __('Company Office List', 'default'),
			'description' => __('Office list with Google Map links opened in new tab.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $office_list_pattern_content,
		]
	);

	$link_logo_grid_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-footer-link-logos","layout":{"type":"constrained","contentSize":"1100px"},"style":{"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem"}}}} -->
<div class="wp-block-group alignfull jqs-footer-link-logos" style="padding-top:1.5rem;padding-bottom:1.5rem">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-group">
<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_hd_logo . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_hd_logo . '"><img src="' . $link_pic_hd_logo . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_br05 . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_br05 . '"><img src="' . $link_pic_br05 . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_br06 . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_br06 . '"><img src="' . $link_pic_br06 . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_br04 . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_br04 . '"><img src="' . $link_pic_br04 . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"1.6rem"} -->
<div style="height:1.6rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-group">
<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_mynavi . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_mynavi . '"><img src="' . $link_pic_mynavi . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_insta . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_insta . '"><img src="' . $link_pic_insta . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","href":"' . $link_url_enga . '"} -->
<figure class="wp-block-image size-full"><a href="' . $link_url_enga . '"><img src="' . $link_pic_enga . '" alt="" style="height:50px;width:auto"/></a></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/link-logo-grid',
		[
			'title'       => __('Footer Link Logo Grid', 'default'),
			'description' => __('Footer link image grid in screenshot order, with unified logo height.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $link_logo_grid_pattern_content,
		]
	);
}
add_action('init', 'jqs_register_home_patterns');

/**
 * Footer logo link setting definitions.
 *
 * @return array<string, string>
 */
function jqs_footer_logo_link_definitions() {
	return [
		'hd_logo' => 'AZ-COM MARUWA GROUP',
		'br04'    => 'JLD（日本物流開発株式会社）',
		'br06'    => 'AZ-COM データセキュリティ',
		'br05'    => 'AZ-COM NET',
		'mynavi'  => 'マイナビ',
		'insta'   => 'Instagram',
		'enga'    => 'engage',
	];
}

/**
 * Default footer logo links.
 *
 * @return array<string, string>
 */
function jqs_footer_logo_links_default_urls() {
	return [
		'hd_logo' => '#',
		'br04'    => '#',
		'br06'    => '#',
		'br05'    => '#',
		'mynavi'  => '#',
		'insta'   => '#',
		'enga'    => '#',
	];
}

/**
 * Get merged footer logo links.
 *
 * @return array<string, string>
 */
function jqs_get_footer_logo_links() {
	$defaults = jqs_footer_logo_links_default_urls();
	$saved = get_option('jqs_footer_logo_links', []);

	if (! is_array($saved)) {
		$saved = [];
	}

	$merged = wp_parse_args($saved, $defaults);

	foreach (array_keys($defaults) as $key) {
		$value = isset($merged[$key]) ? trim((string) $merged[$key]) : '';
		$merged[$key] = $value !== '' ? $value : '#';
	}

	return $merged;
}

/**
 * Sanitize footer logo link settings.
 *
 * @param mixed $input Raw setting value.
 * @return array<string, string>
 */
function jqs_sanitize_footer_logo_links($input) {
	$defaults = jqs_footer_logo_links_default_urls();
	$sanitized = [];

	if (! is_array($input)) {
		return $defaults;
	}

	foreach ($defaults as $key => $default_value) {
		$raw = isset($input[$key]) ? trim(wp_unslash((string) $input[$key])) : '';

		if ($raw === '') {
			$sanitized[$key] = '#';
			continue;
		}

		$url = esc_url_raw($raw, ['http', 'https']);
		$sanitized[$key] = $url ? $url : $default_value;
	}

	return $sanitized;
}

/**
 * Section description for Settings > General.
 */
function jqs_render_footer_logo_links_section() {
	echo '<p>フッター上段のロゴリンクURLをここで変更できます。未入力の場合はリンクなし（#）になります。</p>';
}

/**
 * Render one footer logo link field.
 *
 * @param array<string, string> $args Field args.
 */
function jqs_render_footer_logo_link_field($args) {
	$key = isset($args['key']) ? (string) $args['key'] : '';
	$links = jqs_get_footer_logo_links();
	$value = isset($links[$key]) ? $links[$key] : '#';
	$display_value = $value === '#' ? '' : $value;

	printf(
		'<input type="url" class="regular-text code" name="jqs_footer_logo_links[%1$s]" value="%2$s" placeholder="https://example.com/" />',
		esc_attr($key),
		esc_attr($display_value)
	);
}

/**
 * Register Settings > General fields for footer logo links.
 */
function jqs_register_footer_logo_link_settings() {
	register_setting(
		'general',
		'jqs_footer_logo_links',
		[
			'type'              => 'array',
			'sanitize_callback' => 'jqs_sanitize_footer_logo_links',
			'default'           => jqs_footer_logo_links_default_urls(),
		]
	);

	add_settings_section(
		'jqs_footer_logo_links_section',
		'Footer ロゴリンク設定',
		'jqs_render_footer_logo_links_section',
		'general'
	);

	foreach (jqs_footer_logo_link_definitions() as $key => $label) {
		add_settings_field(
			'jqs_footer_logo_links_' . $key,
			$label,
			'jqs_render_footer_logo_link_field',
			'general',
			'jqs_footer_logo_links_section',
			[
				'key' => $key,
			]
		);
	}
}
add_action('admin_init', 'jqs_register_footer_logo_link_settings');

/**
 * Extra footer link-logo bar above Blocksy footer rows.
 */
function jqs_render_extra_footer_link_logos() {
	$link_pic_hd_logo = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_hd_logo.png'));
	$link_pic_br04 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br04.jpg'));
	$link_pic_br05 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br05.jpg'));
	$link_pic_br06 = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_br06.png'));
	$link_pic_mynavi = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_mynavi.png'));
	$link_pic_insta = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_insta.png'));
	$link_pic_enga = esc_url(home_url('/wp-content/uploads/2026/04/link_pic_enga.png'));

	$footer_logo_links = jqs_get_footer_logo_links();
	$link_url_hd_logo = esc_url($footer_logo_links['hd_logo']);
	$link_url_br04 = esc_url($footer_logo_links['br04']);
	$link_url_br05 = esc_url($footer_logo_links['br05']);
	$link_url_br06 = esc_url($footer_logo_links['br06']);
	$link_url_mynavi = esc_url($footer_logo_links['mynavi']);
	$link_url_insta = esc_url($footer_logo_links['insta']);
	$link_url_enga = esc_url($footer_logo_links['enga']);

	echo '<div class="jqs-extra-footer-links" aria-label="Footer partner links">';
	echo '<div class="ct-container">';
	echo '<div class="jqs-extra-footer-links__row">';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_hd_logo . '" target="_blank" rel="noopener"><img src="' . $link_pic_hd_logo . '" alt=""></a>';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_br05 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br05 . '" alt=""></a>';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_br06 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br06 . '" alt=""></a>';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_br04 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br04 . '" alt=""></a>';
	echo '</div>';
	echo '<div class="jqs-extra-footer-links__row">';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_mynavi . '" target="_blank" rel="noopener"><img src="' . $link_pic_mynavi . '" alt=""></a>';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_insta . '" target="_blank" rel="noopener"><img src="' . $link_pic_insta . '" alt=""></a>';
	echo '<a class="jqs-extra-footer-links__item" href="' . $link_url_enga . '" target="_blank" rel="noopener"><img src="' . $link_pic_enga . '" alt=""></a>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
add_action('blocksy:footer:before', 'jqs_render_extra_footer_link_logos', 5);

/**
 * Runtime styles for service section corrections.
 */
function jqs_home_patterns_runtime_styles() {
	wp_register_style('jqs-home-patterns-runtime', false, [], null);
	wp_enqueue_style('jqs-home-patterns-runtime');
	$css = 'p { letter-spacing: -0.01em !important; } '
		. '[style*="background-color:#3359d3"] { background-color: #344da8 !important; } '
		. '.jqs-service-overview .jqs-service-badge { width: 70px !important; height: 70px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 1rem auto !important; } '
		. '.jqs-service-overview .jqs-service-badge > * { margin: 0 !important; line-height: 1 !important; } '
		. '.wp-block-column > p.has-background.has-white-color.has-text-align-center:has(+ figure.wp-block-image) { width: 70px !important; height: 70px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 1rem auto !important; padding: 0 !important; line-height: 1 !important; background-color: #ff76ba !important; } '
		. '.wp-block-group.alignfull.has-white-background-color.has-background > .wp-block-group.has-border-color[style*="border-width:4px"] { width: min(100%, 1100px) !important; max-width: 1100px !important; margin-left: auto !important; margin-right: auto !important; box-sizing: border-box !important; } '
		. '.wp-block-column.is-vertically-aligned-center:has(img[src*="allow_right_pic.png"]) { display: flex !important; align-items: center !important; justify-content: center !important; } '
		. '.wp-block-image:has(img[src*="allow_right_pic.png"]) { margin-top: 0 !important; margin-bottom: 0 !important; transform: translateY(-22px) !important; } '
		. '.wp-block-image img[src*="allow_right_pic.png"] { display: block !important; margin: 0 auto !important; height: auto !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-title-icon img { width: 120px !important; max-width: 120px !important; transform: scaleX(-1); } '
		. '.jqs-vehicle-types .jqs-vehicle-types__header > .wp-block-column:last-child { padding-left: 0.45rem !important; overflow: visible !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-types__header .wp-block-heading { font-size: 2rem !important; line-height: 1.25 !important; margin: 0 !important; padding-left: 0.06em !important; overflow: visible !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-types__header p { font-size: 1.2rem !important; line-height: 1.4 !important; margin: 0.35rem 0 0 0 !important; padding-left: 0.06em !important; overflow: visible !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row { gap: 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__left > .wp-block-columns { display: inline-flex !important; width: auto !important; gap: 0.35rem !important; align-items: center !important; margin: 0 0 0.5rem 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__left > .wp-block-columns > .wp-block-column { flex: 0 0 auto !important; margin: 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-photo { margin: 0 !important; width: 100% !important; max-width: 610px !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-photo img { width: 100% !important; max-width: 610px !important; height: auto !important; } '
		. '.jqs-vehicle-types .wp-block-heading, .jqs-vehicle-types p { font-weight: 700 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__right > .wp-block-group { display: flex !important; flex-direction: column !important; justify-content: center !important; min-height: 300px !important; gap: 0.75rem !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__right > .wp-block-group > .wp-block-heading.has-background { margin: 0 !important; padding: 0.6rem 0.8rem !important; line-height: 1.25 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__right > .wp-block-group > h3.wp-block-heading { margin: 0 !important; line-height: 1.3 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-row__right > .wp-block-group > p { margin: 0 !important; line-height: 1.3 !important; } '
		. '.jqs-vehicle-types .jqs-capacity-label { width: fit-content !important; max-width: 70px !important; margin: 0 !important; padding: 0.12rem 0.28rem !important; line-height: 1.1 !important; font-size: 1.1rem !important; background-color: #3b58b7 !important; } '
		. '.jqs-vehicle-types .jqs-capacity-level { width: 54px !important; height: 54px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 !important; line-height: 1 !important; padding: 0 !important; font-size: 1.3rem !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-separator { border-color: #c9c9c9 !important; color: #c9c9c9 !important; background: #c9c9c9 !important; opacity: 1 !important; } '
		. '.jqs-vehicle-types .jqs-spec-tag { margin: 0 !important; padding: 0.25rem 0.35rem !important; line-height: 1.1 !important; letter-spacing: 0 !important; font-size: 0.92rem !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-spec-wrap .wp-block-columns { margin: 0 0 0.35rem 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-spec-wrap .wp-block-columns:last-child { margin-bottom: 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-spec-wrap p { margin-top: 0 !important; margin-bottom: 0 !important; } '
		. '.jqs-vehicle-types .jqs-vehicle-spec-wrap .wp-block-columns > .wp-block-column:last-child p { font-size: 0.9rem !important; letter-spacing: -0.01em !important; white-space: nowrap !important; } '
		. '.jqs-news-links .wp-block-column:first-child > p:empty { display: none !important; margin: 0 !important; } '
		. '.jqs-news-links > .wp-block-columns > .wp-block-column:first-child { flex-basis: 25% !important; } '
		. '.jqs-news-links > .wp-block-columns > .wp-block-column.jqs-news-links__list { flex-basis: 75% !important; } '
		. '.jqs-news-links .wp-block-column:first-child > h5.jqs-news-links__label { color: #000 !important; font-size: 16pt !important; font-weight: 700 !important; margin: 0 0 0.15rem 0 !important; line-height: 1.2 !important; } '
		. '.jqs-news-links .wp-block-column:first-child > h3 { color: #22326e !important; font-size: 36px !important; font-weight: 900 !important; margin: 0 !important; line-height: 1.1 !important; } '
		. '.jqs-news-links .jqs-news-links__list > .wp-block-columns { margin: 0 0 0.35rem 0 !important; } '
		. '.jqs-news-links .jqs-news-links__list > .wp-block-columns:last-child { margin-bottom: 0 !important; } '
		. '.jqs-news-links .jqs-news-links__list p { font-size: 16px !important; line-height: 1.25 !important; margin-top: 0 !important; margin-bottom: 0 !important; } '
		. '.jqs-news-links .jqs-news-links__list > .wp-block-columns > .wp-block-column:first-child p { font-weight: 400 !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card-image { max-width: 400px !important; margin-left: auto !important; margin-right: auto !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card-image img { width: 400px !important; max-width: 100% !important; height: auto !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card-button { width: 400px !important; max-width: 100% !important; margin-left: auto !important; margin-right: auto !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card-button p { margin: 0 !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-cards { max-width: 900px !important; width: 100% !important; margin-left: auto !important; margin-right: auto !important; gap: 0.6rem !important; } '
		. '.jqs-recruitment-links .wp-block-cover__background { opacity: 0 !important; background: transparent !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card { position: relative !important; transition: opacity 0.2s ease !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card:hover { opacity: 0.7 !important; } '
		. '.jqs-recruitment-links .jqs-recruitment-card-link-overlay { position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; z-index: 10 !important; display: block !important; text-indent: -9999px !important; overflow: hidden !important; } '
		. '.jqs-office-list > h3.wp-block-heading { margin-bottom: 0 !important; } '
		. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(2), .jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(3) { display: flex !important; align-items: center !important; } '
			. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(2) p, .jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(3) p { font-size: 16px !important; line-height: 1.45 !important; margin: 0 !important; } '
			. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(4) { display: flex !important; align-items: center !important; padding-left: 2rem !important; } '
			. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(4) a { width: 100% !important; min-height: 42px !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 16px !important; line-height: 1.2 !important; } '
			. '.jqs-footer-link-logos .is-layout-flex { gap: 2rem !important; } '
			. '.jqs-footer-link-logos .wp-block-image { margin: 0 !important; } '
			. '.jqs-footer-link-logos .wp-block-image img { height: 50px !important; width: auto !important; max-width: 100% !important; object-fit: contain !important; display: block !important; } '
			. '.jqs-extra-footer-links { padding: 1.5rem 0 !important; } '
			. '.jqs-extra-footer-links__row { display: flex !important; align-items: center !important; justify-content: center !important; gap: 2rem !important; flex-wrap: wrap !important; } '
			. '.jqs-extra-footer-links__row + .jqs-extra-footer-links__row { margin-top: 1.6rem !important; } '
			. '.jqs-extra-footer-links__item { display: inline-flex !important; align-items: center !important; justify-content: center !important; } '
			. '.jqs-extra-footer-links__item img { height: 50px !important; width: auto !important; max-width: 100% !important; object-fit: contain !important; display: block !important; }';
	wp_add_inline_style(
		'jqs-home-patterns-runtime',
		$css
	);
}
add_action('wp_enqueue_scripts', 'jqs_home_patterns_runtime_styles', 30);
