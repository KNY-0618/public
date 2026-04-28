<?php
/**
 * Plugin Name: JQS Home Patterns
 * Description: Registers reusable homepage block patterns for Blocksy.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Editor font-family presets.
 *
 * @return array<int, array<string, string>>
 */
function jqs_get_editor_font_families() {
	return [
		[
			'name'       => __('Noto Sans JP', 'default'),
			'slug'       => 'jqs-noto-sans-jp',
			'fontFamily' => '"Noto Sans JP", sans-serif',
		],
		[
			'name'       => __('System Sans', 'default'),
			'slug'       => 'jqs-system-sans',
			'fontFamily' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans JP", sans-serif',
		],
	];
}

/**
 * Enable editable font-size presets (8px-64px) in block editor.
 */
function jqs_enable_editor_font_sizes() {
	add_theme_support('appearance-tools');
	add_theme_support('custom-units');
	add_theme_support('custom-line-height');
	add_theme_support('custom-spacing');

	$font_sizes = [];
	for ($size = 8; $size <= 64; $size++) {
		$font_sizes[] = [
			'name' => sprintf(__('%spx', 'default'), $size),
			'size' => $size,
			'slug' => 'jqs-' . $size,
		];
	}

	add_theme_support('editor-font-sizes', $font_sizes);
	add_theme_support('editor-font-families', jqs_get_editor_font_families());
}
add_action('after_setup_theme', 'jqs_enable_editor_font_sizes', 20);

/**
 * Force 8px-64px font-size options in all block editor screens.
 *
 * @param array $settings Editor settings.
 * @return array
 */
function jqs_force_editor_font_sizes_all($settings) {
	$font_sizes = [];
	for ($size = 8; $size <= 64; $size++) {
		$font_sizes[] = [
			'name' => sprintf(__('%spx', 'default'), $size),
			'slug' => 'jqs-' . $size,
			'size' => $size,
		];
	}
	$font_families = jqs_get_editor_font_families();

	$settings['fontSizes'] = $font_sizes;
	$settings['fontFamilies'] = $font_families;
	$settings['enableCustomFontSizes'] = true;
	$settings['enableCustomFontFamilies'] = true;
	$settings['enableCustomLineHeight'] = true;
	$settings['enableCustomFontWeight'] = true;
	$settings['spacingSizes'] = $settings['spacingSizes'] ?? [];
	$settings['__experimentalFeatures']['typography']['fontSizes']['theme'] = $font_sizes;
	$settings['__experimentalFeatures']['typography']['fontFamilies']['theme'] = $font_families;
	$settings['__experimentalFeatures']['typography']['customFontSize'] = true;
	$settings['__experimentalFeatures']['typography']['customFontFamily'] = true;
	$settings['__experimentalFeatures']['typography']['customFontWeight'] = true;

	return $settings;
}
add_filter('block_editor_settings_all', 'jqs_force_editor_font_sizes_all', 99);

/**
 * Ensure typography font-size controls are available on common blocks.
 *
 * @param array  $args       Block type args.
 * @param string $block_name Block name.
 * @return array
 */
function jqs_force_block_fontsize_support($args, $block_name) {
	$targets = [
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/quote',
		'core/table',
		'core/button',
		'core/pullquote',
		'core/group',
		'core/columns',
		'core/column',
		'core/details',
	];

	if (! in_array($block_name, $targets, true)) {
		return $args;
	}

	if (! isset($args['supports']) || ! is_array($args['supports'])) {
		$args['supports'] = [];
	}
	if (! isset($args['supports']['typography']) || ! is_array($args['supports']['typography'])) {
		$args['supports']['typography'] = [];
	}

	$args['supports']['typography']['fontSize'] = true;
	$args['supports']['typography']['lineHeight'] = true;
	$args['supports']['typography']['fontFamily'] = true;
	$args['supports']['typography']['fontWeight'] = true;
	$args['supports']['typography']['__experimentalFontFamily'] = true;
	$args['supports']['typography']['__experimentalFontWeight'] = true;

	return $args;
}
add_filter('register_block_type_args', 'jqs_force_block_fontsize_support', 99, 2);

/**
 * Force typography controls visibility via theme.json settings at runtime.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function jqs_force_theme_json_typography_controls($theme_json) {
	if (! is_object($theme_json) || ! method_exists($theme_json, 'update_with')) {
		return $theme_json;
	}

	$font_families = jqs_get_editor_font_families();
	$font_sizes = [];
	for ($size = 8; $size <= 64; $size++) {
		$font_sizes[] = [
			'name' => sprintf(__('%spx', 'default'), $size),
			'slug' => 'jqs-' . $size,
			'size' => $size,
		];
	}

	$data = [
		'version'  => 2,
		'settings' => [
			'appearanceTools' => true,
			'typography'      => [
				'customFontSize'   => true,
				'customFontFamily' => true,
				'fontFamilies'     => [
					'theme' => $font_families,
				],
				'fontSizes'        => [
					'theme' => $font_sizes,
				],
				'defaultControls'  => [
					'fontSize'      => true,
					'lineHeight'    => true,
					'fontFamily'    => true,
					'fontWeight'    => true,
					'fontStyle'     => true,
					'letterSpacing' => true,
					'textTransform' => true,
					'textDecoration'=> true,
				],
			],
		],
	];

	$theme_json->update_with($data);
	return $theme_json;
}
add_filter('wp_theme_json_data_theme', 'jqs_force_theme_json_typography_controls', 99);

/**
 * Add a fallback inspector control for font weight in block editor.
 */
function jqs_enqueue_font_weight_fallback_control() {
	$script = <<<'JS'
(function (wp) {
	if (!wp || !wp.hooks || !wp.compose || !wp.element || !wp.blockEditor || !wp.components) {
		return;
	}

	var addFilter = wp.hooks.addFilter;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;

	var allowedBlocks = {
		'core/paragraph': true,
		'core/heading': true,
		'core/list': true,
		'core/quote': true,
		'core/table': true,
		'core/button': true,
		'core/pullquote': true,
		'core/group': true,
		'core/columns': true,
		'core/column': true,
		'core/details': true
	};

	var withFontWeightControl = createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (!props || !props.isSelected || !allowedBlocks[props.name]) {
				return wp.element.createElement(BlockEdit, props);
			}

			var style = props.attributes && props.attributes.style ? props.attributes.style : {};
			var typography = style.typography ? style.typography : {};
			var currentWeight = typography.fontWeight ? parseInt(typography.fontWeight, 10) : undefined;
			var safeWeight = Number.isFinite(currentWeight) ? currentWeight : undefined;

			function onChangeFontWeight(next) {
				var nextStyle = Object.assign({}, style);
				var nextTypography = Object.assign({}, typography);

				if (!next) {
					delete nextTypography.fontWeight;
				} else {
					nextTypography.fontWeight = String(next);
				}

				nextStyle.typography = nextTypography;
				props.setAttributes({ style: nextStyle });
			}

			return wp.element.createElement(
				Fragment,
				null,
				wp.element.createElement(BlockEdit, props),
				wp.element.createElement(
					InspectorControls,
					null,
					wp.element.createElement(
						PanelBody,
						{ title: 'タイポグラフィ（拡張）', initialOpen: false },
						wp.element.createElement(RangeControl, {
							label: 'fontの太さ',
							min: 100,
							max: 900,
							step: 100,
							allowReset: true,
							value: safeWeight,
							onChange: onChangeFontWeight
						})
					)
				)
			);
		};
	}, 'withFontWeightControl');

	addFilter('editor.BlockEdit', 'jqs/font-weight-fallback-control', withFontWeightControl);
})(window.wp);
JS;

	wp_add_inline_script('wp-block-editor', $script, 'after');
}
add_action('enqueue_block_editor_assets', 'jqs_enqueue_font_weight_fallback_control', 100);

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
				'label' => __('TOPページ', 'default'),
			]
		);

		register_block_pattern_category(
			'jqs-company',
			[
				'label' => __('会社概要', 'default'),
			]
		);

		register_block_pattern_category(
			'jqs-newgraduate',
			[
				'label' => __('新人採用', 'default'),
			]
		);

		register_block_pattern_category(
			'jqs-recruit',
			[
				'label' => __('採用情報', 'default'),
			]
		);

		register_block_pattern_category(
			'jqs-independent',
			[
				'label' => __('独立開業', 'default'),
			]
		);

		register_block_pattern_category(
			'jqs-contact',
			[
				'label' => __('お問い合わせ', 'default'),
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
	$recruit_independent_banner_pic = esc_url('https://9109.com/wp-content/uploads/2022/11/recruit_img02-1.png');
	$recruit_independent_flow_pic = esc_url('https://9109.com/wp-content/uploads/2022/11/recruit_img03.jpg');
	$recruit_independent_support_pic = esc_url('https://9109.com/wp-content/uploads/2022/11/recruit_img03_02.png');
	$recruit_flow_pc_pic = esc_url(home_url('/wp-content/uploads/2026/04/recruit_flow_pc.png'));
	$contact_banner_pic = esc_url(home_url('/wp-content/uploads/2026/04/1_Image_Architectural-services-Banner.jpg'));
		$about_us_pic = esc_url(home_url('/wp-content/uploads/2026/04/about_us_pic.png'));
		$about_bg_pic = esc_url(home_url('/wp-content/uploads/2026/04/about_bg.jpg'));
		$about_greeting_bg_pic = esc_url(home_url('/wp-content/uploads/2026/04/abot_bg01.jpg'));
		$about_img01_pic = esc_url(home_url('/wp-content/uploads/2026/04/about_img01.jpg'));
		$about_img02_pic = esc_url(home_url('/wp-content/uploads/2026/04/about_img02.jpg'));
		$about_map_pic = esc_url(home_url('/wp-content/uploads/2026/04/about-map.png'));
		$about_us_link = esc_url(home_url('/about/'));
	$recruit_newgrad_link = esc_url(home_url('/recruit-newgraduate/'));
	$recruit_driver_link = esc_url(home_url('/recruit-driver/'));
	$recruit_independent_entry_link = esc_url('https://9109.com/entry/franchise/');
	$recruit_independent_contact_link = esc_url('https://9109.com/inquiry/franchise/');
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

	$independent_opening_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-independent-startup","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-independent-startup has-white-background-color has-background">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-independent-startup__banner"} -->
<figure class="wp-block-image size-full jqs-independent-startup__banner"><img src="' . $recruit_independent_banner_pic . '" alt="独立開業情報バナー" /></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">独立開業情報</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">配送のプロとして独立を目指す方向けに、募集概要・契約内容・開業までの流れをまとめた編集可能パターンです。</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"28px"} -->
<div style="height:28px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white","className":"jqs-independent-startup__section-title","style":{"color":{"background":"#3b58b7"}}} -->
<h3 class="wp-block-heading has-text-align-center jqs-independent-startup__section-title has-white-color has-text-color has-background" style="background-color:#3b58b7">ビジネスの特徴</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">・少ない資金で開業が可能<br>・安定した収入を目指せる案件構成<br>・開業前研修を実施<br>・未経験者でも安心してスタート可能</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white","className":"jqs-independent-startup__section-title","style":{"color":{"background":"#3b58b7"}}} -->
<h3 class="wp-block-heading has-text-align-center jqs-independent-startup__section-title has-white-color has-text-color has-background" style="background-color:#3b58b7">募集概要</h3>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":true,"className":"jqs-independent-startup__table"} -->
<figure class="wp-block-table jqs-independent-startup__table"><table><tbody><tr><th>募集形態</th><td>業務委託オーナードライバー</td></tr><tr><th>主な配送エリア</th><td>東京・神奈川・大阪・愛知（詳細は説明会でご案内）</td></tr><tr><th>応募資格</th><td>普通自動車免許（AT限定可）／学歴・経験不問</td></tr><tr><th>車両</th><td>車両持ち込み・リースの双方に対応</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white","className":"jqs-independent-startup__section-title","style":{"color":{"background":"#3b58b7"}}} -->
<h3 class="wp-block-heading has-text-align-center jqs-independent-startup__section-title has-white-color has-text-color has-background" style="background-color:#3b58b7">契約内容</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-independent-startup__contract-flow"} -->
<figure class="wp-block-image size-full jqs-independent-startup__contract-flow"><img src="' . $recruit_flow_pc_pic . '" alt="説明会から開業までの流れ" /></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white","className":"jqs-independent-startup__section-title","style":{"color":{"background":"#3b58b7"}}} -->
<h3 class="wp-block-heading has-text-align-center jqs-independent-startup__section-title has-white-color has-text-color has-background" style="background-color:#3b58b7">独立までの流れ</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-independent-startup__flow"} -->
<figure class="wp-block-image size-full jqs-independent-startup__flow"><img src="' . $recruit_independent_flow_pic . '" alt="独立までの流れ" /></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-independent-startup__flow"} -->
<figure class="wp-block-image size-full jqs-independent-startup__flow"><img src="' . $recruit_independent_support_pic . '" alt="サポート体制" /></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white","className":"jqs-independent-startup__section-title","style":{"color":{"background":"#3b58b7"}}} -->
<h3 class="wp-block-heading has-text-align-center jqs-independent-startup__section-title has-white-color has-text-color has-background" style="background-color:#3b58b7">説明会について</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>まずは説明会エントリーまたはお問い合わせからご連絡ください。日程・契約条件・収支イメージなどを個別にご案内します。</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#3b58b7"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-background wp-element-button" href="' . $recruit_independent_entry_link . '" style="background-color:#3b58b7">説明会エントリー</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline","style":{"color":{"text":"#3b58b7"}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-text-color wp-element-button" href="' . $recruit_independent_contact_link . '" style="color:#3b58b7">お問い合わせ</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/independent-opening-info',
		[
			'title'       => __('独立開業情報 セクション', 'default'),
			'description' => __('独立開業情報をセクション分割して編集できるパターン。', 'default'),
			'categories'  => ['jqs-independent'],
			'content'     => $independent_opening_pattern_content,
		]
	);

	$independent_contract_flow_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-independent-startup","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-independent-startup">
<!-- wp:group {"className":"jqs-independent-startup__contract-flow","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-independent-startup__contract-flow">
<!-- wp:columns {"isStackedOnMobile":false,"className":"jqs-independent-flow","style":{"spacing":{"blockGap":{"left":"12px"}}}} -->
<div class="wp-block-columns is-not-stacked-on-mobile jqs-independent-flow">
<!-- wp:column {"className":"jqs-independent-flow__item","style":{"color":{"background":"#e8e6cd"},"spacing":{"padding":{"top":"0.75rem","right":"1rem","bottom":"0.75rem","left":"1rem"}}}} -->
<div class="wp-block-column jqs-independent-flow__item has-background" style="background-color:#e8e6cd;padding-top:0.75rem;padding-right:1rem;padding-bottom:0.75rem;padding-left:1rem">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">説明会</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>個別での説明会。<br>報酬や働き方などざっくばらんに<br>お話しできればと思います。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"jqs-independent-flow__item","style":{"color":{"background":"#e8e6cd"},"spacing":{"padding":{"top":"0.75rem","right":"1rem","bottom":"0.75rem","left":"1rem"}}}} -->
<div class="wp-block-column jqs-independent-flow__item has-background" style="background-color:#e8e6cd;padding-top:0.75rem;padding-right:1rem;padding-bottom:0.75rem;padding-left:1rem">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">ご契約</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>報酬や仕事内容など<br>ご理解・ご納得いただいた後、<br>契約を締結します。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"jqs-independent-flow__item","style":{"color":{"background":"#e8e6cd"},"spacing":{"padding":{"top":"0.75rem","right":"1rem","bottom":"0.75rem","left":"1rem"}}}} -->
<div class="wp-block-column jqs-independent-flow__item has-background" style="background-color:#e8e6cd;padding-top:0.75rem;padding-right:1rem;padding-bottom:0.75rem;padding-left:1rem">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">研　修</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>報酬や仕事内容など<br>ご理解・ご納得いただいた後、<br>契約を締結します。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"jqs-independent-flow__item","style":{"color":{"background":"#e8e6cd"},"spacing":{"padding":{"top":"0.75rem","right":"1rem","bottom":"0.75rem","left":"1rem"}}}} -->
<div class="wp-block-column jqs-independent-flow__item has-background" style="background-color:#e8e6cd;padding-top:0.75rem;padding-right:1rem;padding-bottom:0.75rem;padding-left:1rem">
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">開　業</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>単独での稼動</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/independent-contract-flow-only',
		[
			'title'       => __('独立開業 契約内容フロー（単体）', 'default'),
			'description' => __('独立開業ページの契約内容フロー画像だけを差し込める単体パターン。', 'default'),
			'categories'  => ['jqs-independent'],
			'content'     => $independent_contract_flow_pattern_content,
		]
	);

	$newgraduate_job_accordion_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-newgrad-job-accordion","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-newgrad-job-accordion has-white-background-color has-background">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">職種概要</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>各職種の仕事内容や求める人物像をご確認いただけます。本文は管理画面から自由に編集できます。</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"jqs-newgrad-job-accordion__grid","layout":{"type":"default"}} -->
<div class="wp-block-group jqs-newgrad-job-accordion__grid">
<!-- wp:details {"summary":"物流管理","className":"jqs-newgrad-job-accordion__item"} -->
<details class="wp-block-details jqs-newgrad-job-accordion__item"><summary>物流管理</summary>
<!-- wp:group {"className":"jqs-newgrad-job-accordion__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-job-accordion__body">
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>職種</strong><br>物流管理</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>業務内容</strong><br>物流センター内でヒト・モノ・カネを管理し、センター全体が安全かつ効率的に運営できるよう管理します。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務地</strong><br>神奈川県横浜市戸塚区、平塚市<br>大阪府大阪市淀川区、茨木市<br>兵庫県明石市、神戸市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->

<!-- wp:details {"summary":"配車","className":"jqs-newgrad-job-accordion__item"} -->
<details class="wp-block-details jqs-newgrad-job-accordion__item"><summary>配車</summary>
<!-- wp:group {"className":"jqs-newgrad-job-accordion__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-job-accordion__body">
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>職種</strong><br>配車</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>業務内容</strong><br>配送ドライバーの選定や打診、配送進捗管理などを担当します。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務地</strong><br>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->

<!-- wp:details {"summary":"品質管理","className":"jqs-newgrad-job-accordion__item"} -->
<details class="wp-block-details jqs-newgrad-job-accordion__item"><summary>品質管理</summary>
<!-- wp:group {"className":"jqs-newgrad-job-accordion__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-job-accordion__body">
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>職種</strong><br>品質管理</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>業務内容</strong><br>配送品質と安全性を高めるため、データ分析、ルール整備、教育支援を行います。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務地</strong><br>東京都荒川区<br>大阪府吹田市<br>愛知県名古屋市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->

<!-- wp:details {"summary":"営業","className":"jqs-newgrad-job-accordion__item"} -->
<details class="wp-block-details jqs-newgrad-job-accordion__item"><summary>営業</summary>
<!-- wp:group {"className":"jqs-newgrad-job-accordion__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-job-accordion__body">
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>職種</strong><br>営業</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>業務内容</strong><br>お客様の課題に対して最適な物流サービスを提案し、既存フォローと新規開拓を行います。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務地</strong><br>東京都荒川区<br>大阪府吹田市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-job-accordion__row"} -->
<p class="jqs-newgrad-job-accordion__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/newgraduate-job-accordion',
		[
			'title'       => __('新人採用 職種アコーディオン', 'default'),
			'description' => __('2x2 accordion layout for new graduate job categories.', 'default'),
			'categories'  => ['jqs-newgraduate'],
			'content'     => $newgraduate_job_accordion_pattern_content,
		]
	);

	$newgraduate_job_tabs_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-newgrad-job-tabs","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-newgrad-job-tabs has-white-background-color has-background">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">職種概要</h2>
<!-- /wp:heading -->

<!-- wp:buttons {"className":"jqs-newgrad-job-tabs__nav"} -->
<div class="wp-block-buttons jqs-newgrad-job-tabs__nav">
<!-- wp:button {"className":"jqs-newgrad-job-tabs__tab is-active","url":"#jqs-job-tab-logistics"} -->
<div class="wp-block-button jqs-newgrad-job-tabs__tab is-active"><a class="wp-block-button__link wp-element-button" href="#jqs-job-tab-logistics">物流管理</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"jqs-newgrad-job-tabs__tab","url":"#jqs-job-tab-haisha"} -->
<div class="wp-block-button jqs-newgrad-job-tabs__tab"><a class="wp-block-button__link wp-element-button" href="#jqs-job-tab-haisha">配車</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"jqs-newgrad-job-tabs__tab","url":"#jqs-job-tab-quality"} -->
<div class="wp-block-button jqs-newgrad-job-tabs__tab"><a class="wp-block-button__link wp-element-button" href="#jqs-job-tab-quality">品質管理</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"jqs-newgrad-job-tabs__tab","url":"#jqs-job-tab-sales"} -->
<div class="wp-block-button jqs-newgrad-job-tabs__tab"><a class="wp-block-button__link wp-element-button" href="#jqs-job-tab-sales">営業</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:group {"anchor":"jqs-job-tab-logistics","className":"jqs-newgrad-job-tabs__panel is-active","layout":{"type":"constrained"}} -->
<div id="jqs-job-tab-logistics" class="wp-block-group jqs-newgrad-job-tabs__panel is-active">
<!-- wp:table {"hasFixedLayout":true,"className":"jqs-newgrad-job-tabs__table"} -->
<figure class="wp-block-table jqs-newgrad-job-tabs__table"><table><tbody><tr><th>職種</th><td>物流管理</td></tr><tr><th>業務内容</th><td>物流センター内でヒト・モノ・カネを管理し、センター全体が安全かつ効率的に運営・管理します。</td></tr><tr><th>勤務地</th><td>神奈川県横浜市戸塚区、平塚市<br>大阪府大阪市淀川区、茨木市<br>兵庫県明石市、神戸市</td></tr><tr><th>勤務時間</th><td>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</td></tr><tr><th>給与</th><td>総合職 226,500円（大学院・大卒）<br>一般職 213,500円（大学院・大卒）207,500円（短大・専門卒）</td></tr><tr><th>諸手当</th><td>通勤手当、時間外勤務手当、資格手当、家族手当 ほか</td></tr><tr><th>昇給</th><td>年1回（6月）</td></tr><tr><th>賞与</th><td>年2回（7月・12月）<br>※業績により決算賞与有り</td></tr><tr><th>休日休暇</th><td>年間休日120日<br>週休2日制（※職種や営業所により曜日が異なります）</td></tr><tr><th>福利厚生</th><td>健康保険、厚生年金保険、雇用保険、労災保険、退職金制度 ほか</td></tr><tr><th>教育制度</th><td>社内研修・社外研修あり</td></tr></tbody></table></figure>
<!-- /wp:table -->
</div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"jqs-job-tab-haisha","className":"jqs-newgrad-job-tabs__panel","layout":{"type":"constrained"}} -->
<div id="jqs-job-tab-haisha" class="wp-block-group jqs-newgrad-job-tabs__panel">
<!-- wp:table {"hasFixedLayout":true,"className":"jqs-newgrad-job-tabs__table"} -->
<figure class="wp-block-table jqs-newgrad-job-tabs__table"><table><tbody><tr><th>職種</th><td>配車</td></tr><tr><th>業務内容</th><td>配送ドライバーの選定や打診、配送進捗管理などを担当します。</td></tr><tr><th>勤務地</th><td>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</td></tr><tr><th>勤務時間</th><td>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</td></tr><tr><th>給与</th><td>総合職 226,500円（大学院・大卒）<br>一般職 213,500円（大学院・大卒）207,500円（短大・専門卒）</td></tr><tr><th>諸手当</th><td>通勤手当、時間外勤務手当、資格手当、家族手当 ほか</td></tr><tr><th>昇給</th><td>年1回（6月）</td></tr><tr><th>賞与</th><td>年2回（7月・12月）<br>※業績により決算賞与有り</td></tr><tr><th>休日休暇</th><td>年間休日120日<br>週休2日制（※職種や営業所により曜日が異なります）</td></tr><tr><th>福利厚生</th><td>健康保険、厚生年金保険、雇用保険、労災保険、退職金制度 ほか</td></tr><tr><th>教育制度</th><td>社内研修・社外研修あり</td></tr></tbody></table></figure>
<!-- /wp:table -->
</div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"jqs-job-tab-quality","className":"jqs-newgrad-job-tabs__panel","layout":{"type":"constrained"}} -->
<div id="jqs-job-tab-quality" class="wp-block-group jqs-newgrad-job-tabs__panel">
<!-- wp:table {"hasFixedLayout":true,"className":"jqs-newgrad-job-tabs__table"} -->
<figure class="wp-block-table jqs-newgrad-job-tabs__table"><table><tbody><tr><th>職種</th><td>品質管理</td></tr><tr><th>業務内容</th><td>配送品質と安全性を高めるため、データ分析、ルール整備、教育支援を行います。</td></tr><tr><th>勤務地</th><td>東京都荒川区<br>大阪府吹田市<br>愛知県名古屋市</td></tr><tr><th>勤務時間</th><td>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</td></tr><tr><th>給与</th><td>総合職 226,500円（大学院・大卒）<br>一般職 213,500円（大学院・大卒）207,500円（短大・専門卒）</td></tr><tr><th>諸手当</th><td>通勤手当、時間外勤務手当、資格手当、家族手当 ほか</td></tr><tr><th>昇給</th><td>年1回（6月）</td></tr><tr><th>賞与</th><td>年2回（7月・12月）<br>※業績により決算賞与有り</td></tr><tr><th>休日休暇</th><td>年間休日120日<br>週休2日制（※職種や営業所により曜日が異なります）</td></tr><tr><th>福利厚生</th><td>健康保険、厚生年金保険、雇用保険、労災保険、退職金制度 ほか</td></tr><tr><th>教育制度</th><td>社内研修・社外研修あり</td></tr></tbody></table></figure>
<!-- /wp:table -->
</div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"jqs-job-tab-sales","className":"jqs-newgrad-job-tabs__panel","layout":{"type":"constrained"}} -->
<div id="jqs-job-tab-sales" class="wp-block-group jqs-newgrad-job-tabs__panel">
<!-- wp:table {"hasFixedLayout":true,"className":"jqs-newgrad-job-tabs__table"} -->
<figure class="wp-block-table jqs-newgrad-job-tabs__table"><table><tbody><tr><th>職種</th><td>営業</td></tr><tr><th>業務内容</th><td>お客様の案件ごとに配送の条件や料金交渉を行い、お客様と当社の橋渡し役となります。</td></tr><tr><th>勤務地</th><td>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</td></tr><tr><th>勤務時間</th><td>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</td></tr><tr><th>給与</th><td>総合職 226,500円（大学院・大卒）<br>一般職 213,500円（大学院・大卒）207,500円（短大・専門卒）</td></tr><tr><th>諸手当</th><td>通勤手当、時間外勤務手当、資格手当、家族手当 ほか</td></tr><tr><th>昇給</th><td>年1回（6月）</td></tr><tr><th>賞与</th><td>年2回（7月・12月）<br>※業績により決算賞与有り</td></tr><tr><th>休日休暇</th><td>年間休日120日<br>週休2日制（※職種や営業所により曜日が異なります）</td></tr><tr><th>福利厚生</th><td>健康保険、厚生年金保険、雇用保険、労災保険、退職金制度 ほか</td></tr><tr><th>教育制度</th><td>社内研修・社外研修あり</td></tr></tbody></table></figure>
<!-- /wp:table -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/newgraduate-job-tabs',
		[
			'title'       => __('新人採用 職種タブ', 'default'),
			'description' => __('Editable 4-tab layout for recruit job summaries.', 'default'),
			'categories'  => ['jqs-newgraduate'],
			'content'     => $newgraduate_job_tabs_pattern_content,
		]
	);

	$newgraduate_tab2_table_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-newgrad-tab2-table","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-newgrad-tab2-table has-white-background-color has-background">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">職種概要（配車）</h3>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":true,"className":"jqs-newgrad-tab2-table__table"} -->
<figure class="wp-block-table jqs-newgrad-tab2-table__table"><table><tbody><tr><th>職種</th><td>配車</td></tr><tr><th>業務内容</th><td>配送ドライバーの選定や打診、配送進捗管理などを担当します。</td></tr><tr><th>勤務地</th><td>・東京都荒川区<br>・神奈川県横浜市南区<br>・大阪府吹田市<br>・愛知県名古屋市</td></tr><tr><th>勤務時間</th><td>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</td></tr><tr><th>給与</th><td>総合職 226,500円（大学院・大卒）<br>一般職 213,500円（大学院・大卒）207,500円（短大・専門卒）</td></tr><tr><th>諸手当</th><td>通勤手当、時間外勤務手当、資格手当、家族手当 ほか</td></tr><tr><th>昇給</th><td>年1回（6月）</td></tr><tr><th>賞与</th><td>年2回（7月・12月）<br>※業績により決算賞与有り</td></tr><tr><th>休日休暇</th><td>年間休日120日<br>週休2日制（※職種や営業所により曜日が異なります）<br>夏季休暇、年末年始休暇、年次有給休暇（初年度10日）、慶弔休暇、特別休暇、育児・介護休暇、リフレッシュ休暇、報恩感謝休暇 など</td></tr><tr><th>福利厚生</th><td>健康保険、厚生年金保険、雇用保険、労災保険、退職金制度、確定給付年金制度、社員持株会（株式会社AZ-COM丸和ホールディングス）、借上げ社宅制度、表彰制度、定期健康診断、団体定期保険、丸和親睦会、スポーツ施設、宿泊施設、従業員貸付金制度、資格取得支援制度、自己啓発支援制度</td></tr><tr><th>教育制度</th><td>・社内研修（丸和ロジスティクス大学、役員・部門経営者研修、部門長変革研修、新社員合宿研修、新社員フォロー研修、新春勉強会 など）<br>・社外研修（経営管理者研修（中小企業大学校）、物流技術管理士資格認定講座、資格取得補助制度 など）</td></tr></tbody></table></figure>
<!-- /wp:table -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/newgraduate-tab2-table',
		[
			'title'       => __('New Graduate Tab2 Table (Haisha)', 'default'),
			'description' => __('Table version of recruit tab2 (配車).', 'default'),
			'categories'  => ['jqs-newgraduate'],
			'content'     => $newgraduate_tab2_table_pattern_content,
		]
	);

	$newgraduate_four_tables_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-newgrad-four-tables","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-newgrad-four-tables has-white-background-color has-background">
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">職種概要</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"jqs-newgrad-four-tables__grid"} -->
<div class="wp-block-columns jqs-newgrad-four-tables__grid">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:details {"summary":"物流管理","className":"jqs-newgrad-four-tables__item"} -->
<details class="wp-block-details jqs-newgrad-four-tables__item"><summary>物流管理</summary>
<!-- wp:group {"className":"jqs-newgrad-four-tables__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-four-tables__body">
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>業務内容</strong><br>物流センター内でヒト・モノ・カネを管理し、センター全体が安全かつ効率的に運営・管理します。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務地</strong><br>神奈川県横浜市戸塚区、平塚市<br>大阪府大阪市淀川区、茨木市<br>兵庫県明石市、神戸市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:details {"summary":"配車","className":"jqs-newgrad-four-tables__item"} -->
<details class="wp-block-details jqs-newgrad-four-tables__item"><summary>配車</summary>
<!-- wp:group {"className":"jqs-newgrad-four-tables__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-four-tables__body">
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>業務内容</strong><br>配送ドライバーの選定や打診、配送進捗管理などを担当します。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務地</strong><br>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"jqs-newgrad-four-tables__grid"} -->
<div class="wp-block-columns jqs-newgrad-four-tables__grid">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:details {"summary":"品質管理","className":"jqs-newgrad-four-tables__item"} -->
<details class="wp-block-details jqs-newgrad-four-tables__item"><summary>品質管理</summary>
<!-- wp:group {"className":"jqs-newgrad-four-tables__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-four-tables__body">
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>業務内容</strong><br>ドライバーの指導・教育、配送現場巡回、運用マニュアル作成、問い合わせ対応を担当します。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務地</strong><br>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:details {"summary":"営業","className":"jqs-newgrad-four-tables__item"} -->
<details class="wp-block-details jqs-newgrad-four-tables__item"><summary>営業</summary>
<!-- wp:group {"className":"jqs-newgrad-four-tables__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-four-tables__body">
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>業務内容</strong><br>お客様の案件ごとに配送の条件や料金交渉を行い、お客様と当社の橋渡し役となります。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務地</strong><br>東京都荒川区<br>神奈川県横浜市南区<br>大阪府吹田市<br>愛知県名古屋市</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"jqs-newgrad-four-tables__row"} -->
<p class="jqs-newgrad-four-tables__row"><strong>勤務時間</strong><br>実働8時間・休憩60分のシフト制勤務<br>※営業所・職種によって異なる</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/newgraduate-four-job-tables',
		[
			'title'       => __('新人採用 職種4表（2列）', 'default'),
			'description' => __('Four job tables in 2-column layout from recruit tab1-tab4 content.', 'default'),
			'categories'  => ['jqs-newgraduate'],
			'content'     => $newgraduate_four_tables_pattern_content,
		]
	);

	$newgraduate_talent_requirements_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-newgrad-talent","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-newgrad-talent">
<!-- wp:group {"className":"jqs-newgrad-talent__inner","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-talent__inner">
<!-- wp:group {"className":"jqs-newgrad-talent__title-wrap","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-newgrad-talent__title-wrap">
<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">当社が求める「人財」</h3>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"className":"jqs-newgrad-talent__columns"} -->
<div class="wp-block-columns jqs-newgrad-talent__columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・自分を成長させたいと考えている方</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・チームワークを重視する方</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・明るく元気な対応ができる方</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・コミュニケーションを大切にしている方</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・とにかく負けず嫌いな方</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"jqs-newgrad-talent__item"} -->
<p class="jqs-newgrad-talent__item">・自分の殻を破りたい方</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/newgraduate-talent-requirements',
		[
			'title'       => __('新人採用 求める人財', 'default'),
			'description' => __('Talent requirements block with gray border and pink header.', 'default'),
			'categories'  => ['jqs-newgraduate'],
			'content'     => $newgraduate_talent_requirements_pattern_content,
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

	$company_overview_banner_pattern_content = '
<!-- wp:group {"align":"full","backgroundColor":"white","className":"jqs-company-overview-banner","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-company-overview-banner has-white-background-color has-background">
<!-- wp:cover {"url":"' . $about_bg_pic . '","id":0,"dimRatio":0,"minHeight":210,"minHeightUnit":"px","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-cover" style="margin-top:0;margin-bottom:0;min-height:210px"><img class="wp-block-cover__image-background" alt="" src="' . $about_bg_pic . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
<!-- wp:heading {"level":3,"textAlign":"center","textColor":"white"} -->
<h3 class="wp-block-heading has-text-align-center has-white-color has-text-color">会社概要</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">ABOUT US</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div></div>
<!-- /wp:cover -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-home/company-overview-banner',
		[
			'title'       => __('Company Overview Banner', 'default'),
			'description' => __('1100px company overview banner with H3 title and subtitle paragraph.', 'default'),
			'categories'  => ['jqs-company'],
			'content'     => $company_overview_banner_pattern_content,
		]
	);

	$company_greeting_full_bg_pattern_content = '
<!-- wp:cover {"url":"' . $about_greeting_bg_pic . '","id":0,"dimRatio":0,"minHeight":100,"minHeightUnit":"vh","align":"full","className":"jqs-company-greeting-bg","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-cover alignfull jqs-company-greeting-bg" style="margin-top:0;margin-bottom:0;min-height:100vh"><img class="wp-block-cover__image-background" alt="" src="' . $about_greeting_bg_pic . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group">
<!-- wp:heading {"level":2,"textAlign":"center","style":{"color":{"text":"#3b58b7"},"spacing":{"margin":{"top":"0"}},"typography":{"letterSpacing":"normal"}}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#3b58b7;margin-top:0;letter-spacing:normal">Greeting</h2>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","style":{"color":{"text":"#000000"},"spacing":{"margin":{"top":"0"}}}} -->
<h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#000000;margin-top:0">より速く・より正確に・より安全に</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"2.5rem"} -->
<div style="height:2.5rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#000000"},"typography":{"lineHeight":"2.3rem"}}} -->
<p class="has-text-align-center has-text-color" style="color:#000000;line-height:2.3rem">桃太郎のおとぎ話は、どなたでもご存じです。<br>犬（勇敢、敏速）猿（知恵と計画性）雉（空を飛んで情報収集と慎重性） これらをまとめて<br>システム化するコーディネーターとしての桃太郎の役割・・・。<br>そして宝物を運ぶ訳であります。<br>このような桃太郎のおとぎ話にあてはめ、「より速く、より正確に、より安全に」<br>をモットーとしたのが 「桃太郎便」であります。<br>また、この桃太郎のおとぎ話は「経営の先見三要素」として、<br>「犬は考働力」、 「猿は知識力」、 そして「雉は情報力」 を示しているもので<br>企業経営上からも最も重要な要素となります。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div></div>
<!-- /wp:cover -->
';

	register_block_pattern(
		'jqs-company/greeting-full-background',
		[
			'title'       => __('Greeting Full Background', 'default'),
			'description' => __('Full-screen greeting section with background image and centered text.', 'default'),
			'categories'  => ['jqs-company'],
			'content'     => $company_greeting_full_bg_pattern_content,
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

	$management_philosophy_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-management-philosophy","style":{"color":{"background":"#f1f1f1"}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-management-philosophy has-background" style="background-color:#f1f1f1">
<!-- wp:heading {"level":2,"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">経営理念</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"2rem"},"padding":{"bottom":"4.5rem"}}}} -->
<p class="has-text-align-center" style="margin-top:2rem;padding-bottom:4.5rem">“お客様第一義”を基本に<br>サードパーティロジスティクス業界のNo.1を目指し、<br>同志の幸福と豊かな社会づくりに貢献する。</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"jqs-management-philosophy__cards","style":{"spacing":{"blockGap":{"left":"1.6rem"}}}} -->
<div class="wp-block-columns jqs-management-philosophy__cards">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"jqs-management-philosophy__card","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-management-philosophy__card">
<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__number"} -->
<p class="has-text-align-center jqs-management-philosophy__number">1</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textAlign":"center","className":"jqs-management-philosophy__title"} -->
<h3 class="wp-block-heading has-text-align-center jqs-management-philosophy__title">勤倹、誠実こそ商いの基</h3>
<!-- /wp:heading -->

<!-- wp:separator {"className":"jqs-management-philosophy__line"} -->
<hr class="wp-block-separator has-alpha-channel-opacity jqs-management-philosophy__line"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__desc"} -->
<p class="has-text-align-center jqs-management-philosophy__desc">勤倹、倹約に努め、<br>真心を込めて考働すれば<br>信用、信頼される。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"jqs-management-philosophy__card","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-management-philosophy__card">
<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__number"} -->
<p class="has-text-align-center jqs-management-philosophy__number">2</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textAlign":"center","className":"jqs-management-philosophy__title"} -->
<h3 class="wp-block-heading has-text-align-center jqs-management-philosophy__title">忍耐、創造こそ繁栄の基</h3>
<!-- /wp:heading -->

<!-- wp:separator {"className":"jqs-management-philosophy__line"} -->
<hr class="wp-block-separator has-alpha-channel-opacity jqs-management-philosophy__line"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__desc"} -->
<p class="has-text-align-center jqs-management-philosophy__desc">常に忍耐力は必要である。<br>しかし、忍耐を想像力で<br>突破することが繁栄となる。</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"jqs-management-philosophy__card","layout":{"type":"constrained"}} -->
<div class="wp-block-group jqs-management-philosophy__card">
<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__number"} -->
<p class="has-text-align-center jqs-management-philosophy__number">3</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textAlign":"center","className":"jqs-management-philosophy__title"} -->
<h3 class="wp-block-heading has-text-align-center jqs-management-philosophy__title">報恩、感謝こそ幸福の基</h3>
<!-- /wp:heading -->

<!-- wp:separator {"className":"jqs-management-philosophy__line"} -->
<hr class="wp-block-separator has-alpha-channel-opacity jqs-management-philosophy__line"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","className":"jqs-management-philosophy__desc"} -->
<p class="has-text-align-center jqs-management-philosophy__desc">生かされていることの自覚と、<br>親、祖先を尊び、常に<br>「ありがとうございます」の<br>言葉を忘れずに</p>
<!-- /wp:paragraph -->
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
		'jqs-company/management-philosophy-cards',
		[
			'title'       => __('Management Philosophy Cards', 'default'),
			'description' => __('Management philosophy section with three numbered rounded cards.', 'default'),
			'categories'  => ['jqs-company'],
			'content'     => $management_philosophy_pattern_content,
		]
	);

	$company_profile_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-company-profile","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-company-profile">
<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"margin":{"bottom":"2.2rem"}}}} -->
<div class="wp-block-group" style="margin-bottom:2.2rem">
<!-- wp:heading {"level":2,"textAlign":"center","style":{"spacing":{"margin":{"bottom":"0"}}}} -->
<h2 class="wp-block-heading has-text-align-center" style="margin-bottom:0">会社概要</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"jqs-company-profile__subtitle","style":{"spacing":{"margin":{"top":"0.1rem"}}}} -->
<p class="has-text-align-center jqs-company-profile__subtitle" style="margin-top:0.1rem">Company Profile</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"verticalAlignment":"stretch","className":"jqs-company-profile__top","style":{"spacing":{"blockGap":{"left":"2.6rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-stretch jqs-company-profile__top">
<!-- wp:column {"width":"53%","verticalAlignment":"stretch"} -->
<div class="wp-block-column is-vertically-aligned-stretch" style="flex-basis:53%">
<!-- wp:html -->
<div class="jqs-company-profile__info">
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">会社名</p><p class="jqs-company-profile__value">株式会社ジャパンクイックサービス</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">代表者</p><p class="jqs-company-profile__value">代表取締役社長　和仁見次男</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">設　立</p><p class="jqs-company-profile__value">昭和63年2月</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">資本金</p><p class="jqs-company-profile__value">1000万円</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">売上高</p><p class="jqs-company-profile__value">61億7000万円（2025年3月期実績）</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">株　主</p><p class="jqs-company-profile__value">AZ-COM丸和ホールディングス株式会社</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">事業内容</p><p class="jqs-company-profile__value">貨物軽自動車運送事業</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">拠　点</p><p class="jqs-company-profile__value">東京、神奈川、愛知、大阪、兵庫</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">自社契約車両</p><p class="jqs-company-profile__value">約200台（フランチャイズ契約）</p></div>
	<div class="jqs-company-profile__info-row"><p class="jqs-company-profile__label">主要サービス</p><p class="jqs-company-profile__value">ネットスーパーサービス<br>当日宅配サービス<br>ECお届けサービス<br>定期ルート配送サービス<br>緊急輸送サービス</p></div>
</div>
<!-- /wp:html -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"47%","verticalAlignment":"stretch"} -->
<div class="wp-block-column is-vertically-aligned-stretch" style="flex-basis:47%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-company-profile__map"} -->
<figure class="wp-block-image size-full jqs-company-profile__map"><img src="' . $about_map_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"2.2rem"} -->
<div style="height:2.2rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"center","className":"jqs-company-profile__bottom","style":{"spacing":{"blockGap":{"left":"2.6rem"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center jqs-company-profile__bottom">
<!-- wp:column {"width":"56%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:56%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-company-profile__photo"} -->
<figure class="wp-block-image size-full jqs-company-profile__photo"><img src="' . $about_img01_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"44%","verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:44%">
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"jqs-company-profile__brand"} -->
<figure class="wp-block-image size-full jqs-company-profile__brand"><img src="' . $about_img02_pic . '" alt="" /></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-company/company-profile-overview',
		[
			'title'       => __('Company Profile Overview', 'default'),
			'description' => __('Company profile table with Japan map and two supporting images.', 'default'),
			'categories'  => ['jqs-company'],
			'content'     => $company_profile_pattern_content,
		]
	);

	$contact_banner_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-contact-page-banner","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-contact-page-banner">
<!-- wp:cover {"url":"' . $contact_banner_pic . '","dimRatio":45,"isUserOverlayColor":true,"minHeight":220,"minHeightUnit":"px"} -->
<div class="wp-block-cover" style="min-height:220px"><img class="wp-block-cover__image-background" alt="" src="' . $contact_banner_pic . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:heading {"textAlign":"center","level":2,"textColor":"white"} -->
<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color">お問い合わせ</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">CONTACT</p>
<!-- /wp:paragraph -->
</div></div>
<!-- /wp:cover -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-contact/contact-banner',
		[
			'title'       => __('お問い合わせ: バナー', 'default'),
			'description' => __('Contact page banner block.', 'default'),
			'categories'  => ['jqs-contact'],
			'content'     => $contact_banner_pattern_content,
		]
	);

	$contact_intro_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-contact-page-intro","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-contact-page-intro">
<!-- wp:paragraph -->
<p>会社・採用についてのお問い合わせはこちらへご連絡ください。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>お電話の場合（受付時間 月〜金 9:00〜18:00）<br>TEL.03-3807-1000（代表）</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-contact/contact-intro-text',
		[
			'title'       => __('お問い合わせ: 下文言', 'default'),
			'description' => __('Contact lead text block under banner.', 'default'),
			'categories'  => ['jqs-contact'],
			'content'     => $contact_intro_pattern_content,
		]
	);

	$contact_form_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-contact-page-form","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-contact-page-form">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">お問い合わせフォーム</h3>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[jqs_contact_form_embed provider="cf7" title="お問い合わせ（入力画面）"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-contact/contact-form-template',
		[
			'title'       => __('お問い合わせ: フォーム', 'default'),
			'description' => __('Contact form section using a WordPress shortcode template.', 'default'),
			'categories'  => ['jqs-contact'],
			'content'     => $contact_form_pattern_content,
		]
	);

	$contact_privacy_pattern_content = '
<!-- wp:group {"align":"full","className":"jqs-contact-page-privacy","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull jqs-contact-page-privacy">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"16px"}}} -->
<h3 class="wp-block-heading" style="font-size:16px">個人情報の取り扱いについて</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">下記事項をご確認の上、同意していただける場合は[同意する]にチェックを入れてください。</p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"border":{"color":"#bdbdbd","width":"1px"},"spacing":{"padding":{"top":"1.4rem","right":"1.4rem","bottom":"1.4rem","left":"1.4rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-color:#bdbdbd;border-width:1px;padding-top:1.4rem;padding-right:1.4rem;padding-bottom:1.4rem;padding-left:1.4rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">1. 個人情報は、適切、厳重に管理し、お客様の個人情報への不正アクセスや紛失、破壊、改ざん、漏えい等が起きないように安全対策を実施しております。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">■オーナードライバー応募における個人情報<br>採用のための個人情報の取得であり、それ以外の目的での利用はしません。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">■個人情報の委託・提供<br>当社では、取得した個人情報の外部委託は行いません。<br>また、法令に定めがある場合を除き、本人の同意を得ない限り第三者への提供は行いません。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">■個人情報の提供の任意性<br>応募者の個人情報の提供は任意ですが、必要な個人情報の一部または全部を提供されなかった場合は採用判断において不利に取り扱われる可能性があります。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">■開示等の求めに応じる手続き<br>応募者は、当社に対して自己の個人情報を開示するよう請求する権利、当該本人が識別される保有個人データの内容が事実でないときは訂正を請求する権利、自己の個人情報の削除を請求する権利を有します。<br>(1) 自らに関する個人情報の開示、訂正、削除等のお申し出があったときは、遅滞なくその調査を行い、訂正、削除の必要とする理由があるときは、原則5営業日内に、訂正、削除を行います。<br>(2) 個人情報に関する開示等の求めは、下記、お問い合わせ窓口に電話またはメールでご連絡ください。<br>(3) 個人情報の開示等の求めをする方が、ご本人であることを、ご本人のみが知りえる情報にて確認させていただきます。また代理人の場合、委任状等そのことを証明する文書を添付してください。<br>(4) 手続きに対して手数料は徴収いたしません。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">【お問い合わせ窓口】<br>個人情報に関するお問合せにつきましては、下記窓口で受け付けております。<br>東京都荒川区南千住3-5-20 丸和通運ビル2階<br>株式会社ジャパンクイックサービス<br>個人情報保護相談窓口責任者　PMS担当<br>mail：jqs-kanri@momotaro.co.jp<br>※土・日曜日、祝祭日、年末年始は翌営業日以降の対応とさせて頂きます。<br>※本窓口は個人情報に関するお問合せ窓口です。配送に関するお問い合わせはそれぞれのお問合せ窓口にお願いします。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px">■認定個人情報保護団体<br>当法人は、以下の認定個人情報保護団体の対象事業者です。<br>認定個人情報保護団体の名称及び、苦情の解決の申出先は以下のとおりです。<br>認定個人情報保護団体の名称：一般財団法人日本情報経済社会推進協会<br>苦情の解決の申出先：個人情報保護苦情相談室<br>〒106-0032 東京都港区六本木一丁目9番9号六本木ファーストビル内<br>TEL：03-5860-7565 / 0120-700-779</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
';

	register_block_pattern(
		'jqs-contact/contact-privacy-policy',
		[
			'title'       => __('お問い合わせ: 個人情報の取り扱い', 'default'),
			'description' => __('Privacy handling section for contact page.', 'default'),
			'categories'  => ['jqs-contact'],
			'content'     => $contact_privacy_pattern_content,
		]
	);

	$link_logo_grid_pattern_content = '
<!-- wp:shortcode -->
[jqs_footer_link_grid]
<!-- /wp:shortcode -->
';

	register_block_pattern(
		'jqs-home/link-logo-grid',
		[
			'title'       => __('フッターリンクロゴグリッド', 'default'),
			'description' => __('Footer link image grid in screenshot order, with unified logo height.', 'default'),
			'categories'  => ['jqs-home'],
			'content'     => $link_logo_grid_pattern_content,
		]
	);

	register_block_pattern(
		'jqs-home/widget-footer-link-logo-grid',
		[
			'title'       => __('ウィジェット用フッターリンクロゴグリッド', 'default'),
			'description' => __('Widget-friendly footer link logo grid (shortcode based).', 'default'),
			'categories'  => ['text', 'jqs-home'],
			'content'     => $link_logo_grid_pattern_content,
		]
	);
}
add_action('init', 'jqs_register_home_patterns');

/**
 * Contact form embed shortcode with plugin fallback.
 *
 * Usage:
 * [jqs_contact_form_embed provider="mwform|cf7|wpforms|fluent" id="123" key="471" title="お問い合わせフォーム"]
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function jqs_contact_form_embed_shortcode($atts) {
	$atts = shortcode_atts(
		[
			'provider' => 'cf7',
			'id'       => '',
			'key'      => '',
			'title'    => 'お問い合わせフォーム',
		],
		(array) $atts,
		'jqs_contact_form_embed'
	);

	$provider = sanitize_key((string) $atts['provider']);
	$form_id = preg_replace('/[^0-9]/', '', (string) $atts['id']);
	$form_key = preg_replace('/[^0-9a-zA-Z_-]/', '', (string) $atts['key']);
	$title = sanitize_text_field((string) $atts['title']);

	if ($form_key === '' && $form_id !== '') {
		$form_key = $form_id;
	}

	if ($provider === 'cf7' && $form_id === '' && $title !== '') {
		$resolved_id = jqs_find_cf7_form_id_by_title($title);
		if ($resolved_id > 0) {
			$form_id = (string) $resolved_id;
		}
	}

	if (
		in_array($provider, ['mwform', 'mw-wp-form', 'mwwpform'], true)
		&& shortcode_exists('mwform_formkey')
		&& $form_key !== ''
	) {
		return do_shortcode('[mwform_formkey key="' . $form_key . '"]');
	}

	if ($provider === 'cf7' && shortcode_exists('contact-form-7') && $form_id !== '') {
		return do_shortcode('[contact-form-7 id="' . $form_id . '" title="' . esc_attr($title) . '"]');
	}

	if ($provider === 'wpforms' && shortcode_exists('wpforms') && $form_id !== '') {
		return do_shortcode('[wpforms id="' . $form_id . '" title="false" description="false"]');
	}

	if ($provider === 'fluent' && shortcode_exists('fluentform') && $form_id !== '') {
		return do_shortcode('[fluentform id="' . $form_id . '"]');
	}

	return '<div class="jqs-contact-form-placeholder">'
		. '<p><strong>' . esc_html__('フォームが未接続です。', 'default') . '</strong></p>'
		. '<p>' . esc_html__('ショートコードの provider / id を設定し、対応フォームプラグインを有効化してください。', 'default') . '</p>'
		. '<p><code>[jqs_contact_form_embed provider="mwform" key="471"]</code></p>'
		. '</div>';
}
add_shortcode('jqs_contact_form_embed', 'jqs_contact_form_embed_shortcode');

/**
 * Auto-recover CF7 shortcode when the referenced form ID/hash is stale.
 *
 * If `[contact-form-7 ...]` returns "Contact form not found", retry by exact title.
 *
 * @param string               $output Shortcode output.
 * @param string               $tag    Shortcode tag.
 * @param array<string,string> $attr   Shortcode attributes.
 * @return string
 */
function jqs_cf7_autofix_not_found_shortcode_output($output, $tag, $attr) {
	if ($tag !== 'contact-form-7') {
		return $output;
	}

	if (! is_string($output) || $output === '') {
		return $output;
	}

	// Retry only when CF7 explicitly says the form cannot be found.
	if (
		strpos($output, 'wpcf7-not-found') === false
		&& strpos($output, 'コンタクトフォームが見つかりません') === false
		&& stripos($output, 'Contact form not found') === false
	) {
		return $output;
	}

	$title = isset($attr['title']) ? sanitize_text_field((string) $attr['title']) : '';
	if ($title === '') {
		return $output;
	}

	$form_id = jqs_find_cf7_form_id_by_title($title);
	if ($form_id <= 0) {
		return $output;
	}

	static $is_retrying = false;
	if ($is_retrying) {
		return $output;
	}

	$is_retrying = true;
	$fixed = do_shortcode('[contact-form-7 id="' . (int) $form_id . '" title="' . esc_attr($title) . '"]');
	$is_retrying = false;

	return is_string($fixed) && $fixed !== '' ? $fixed : $output;
}
add_filter('do_shortcode_tag', 'jqs_cf7_autofix_not_found_shortcode_output', 20, 3);

/**
 * Disable CF7 auto <p>/<br> wrapping for form markup only.
 *
 * This avoids layout breakage in custom grid/flex form structures.
 *
 * @param bool               $autop   Current autoplay flag.
 * @param array<string,mixed> $options CF7 options.
 * @return bool
 */
function jqs_cf7_disable_form_autop($autop, $options) {
	$for = isset($options['for']) ? (string) $options['for'] : 'form';
	if ($for === 'form') {
		return false;
	}
	return (bool) $autop;
}
add_filter('wpcf7_autop_or_not', 'jqs_cf7_disable_form_autop', 20, 2);

/**
 * Find CF7 form id by exact title.
 *
 * @param string $title Form title.
 * @return int
 */
function jqs_find_cf7_form_id_by_title($title) {
	$posts = get_posts([
		'post_type'      => 'wpcf7_contact_form',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	]);

	if (empty($posts)) {
		return 0;
	}

	foreach ($posts as $post_id) {
		$post_title = get_the_title((int) $post_id);
		if ((string) $post_title === (string) $title) {
			return (int) $post_id;
		}
	}

	return 0;
}

/**
 * Upsert a Contact Form 7 form.
 *
 * @param string               $title      Form title.
 * @param string               $form_body  Form markup.
 * @param array<string,mixed>  $mail         Mail settings.
 * @param array<string,string> $messages     Message settings.
 * @param int                  $preferred_id Preferred existing form ID.
 * @return int
 */
function jqs_upsert_cf7_form($title, $form_body, $mail, $messages = [], $preferred_id = 0) {
	if (! class_exists('WPCF7_ContactForm')) {
		return 0;
	}

	$form_id = (int) $preferred_id;
	if ($form_id > 0) {
		$ContactForm = WPCF7_ContactForm::get_instance($form_id);
		if (! $ContactForm) {
			$form_id = 0;
		}
	}

	if ($form_id <= 0) {
		$form_id = jqs_find_cf7_form_id_by_title($title);
	}

	if ($form_id > 0) {
		$ContactForm = WPCF7_ContactForm::get_instance($form_id);
	} else {
		$ContactForm = WPCF7_ContactForm::get_template([
			'title'  => $title,
			'locale' => 'ja',
		]);
	}

	if (! $ContactForm) {
		return 0;
	}

	$ContactForm->set_title($title);
	$ContactForm->set_locale('ja');

	$props = (array) $ContactForm->get_properties();
	$props['form'] = trim((string) $form_body);
	$props['mail'] = wp_parse_args((array) $mail, (array) ($props['mail'] ?? []));

	if (! empty($messages)) {
		$props['messages'] = wp_parse_args((array) $messages, (array) ($props['messages'] ?? []));
	}

	$ContactForm->set_properties($props);
	return (int) $ContactForm->save();
}

/**
 * Upsert a page by slug (safe: overwrite only if empty or jqs marker exists).
 *
 * @param string $slug    Page slug.
 * @param string $title   Page title.
 * @param string $content Page content.
 * @return int
 */
function jqs_upsert_page_by_slug($slug, $title, $content) {
	$existing = get_page_by_path($slug, OBJECT, 'page');
	$marker = '<!-- jqs-cf7-flow -->';
	$content = $marker . "\n" . trim((string) $content);

	if (! $existing) {
		return (int) wp_insert_post([
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		]);
	}

	$post_id = (int) $existing->ID;
	$old = (string) $existing->post_content;
	$can_overwrite = trim($old) === '' || strpos($old, $marker) !== false;

	if ($can_overwrite) {
		wp_update_post([
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
		]);
	}

	return $post_id;
}

/**
 * Auto-setup CF7 multi-step contact flow:
 * 1) input page/form
 * 2) confirm page/form
 * 3) thanks page
 *
 * Manual re-run:
 * /wp-admin/?jqs_setup_cf7_flow=1
 */
function jqs_setup_cf7_multistep_flow() {
	if (! is_admin() || ! current_user_can('manage_options')) {
		return;
	}

	$setup_version = 10;
	$force = isset($_GET['jqs_setup_cf7_flow']) && (string) $_GET['jqs_setup_cf7_flow'] === '1';
	$stored_version = (int) get_option('jqs_cf7_multistep_setup_version', 0);

	if (! $force && $stored_version >= $setup_version) {
		return;
	}

	if (! class_exists('WPCF7_ContactForm')) {
		return;
	}

	$input_url = trailingslashit(home_url('/contact-input/'));
	$confirm_url = trailingslashit(home_url('/contact-confirm/'));
	$thanks_url = trailingslashit(home_url('/contact-thanks/'));
	$step_name = 'ms-471';

	$input_form_body = <<<EOT
<div class="jqs-cf7-multistep-input jqs-cf7-contact-table">
	<div class="jqs-cf7-contact-table__header">個人情報・ご連絡先</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">お名前 必須</div>
		<div class="jqs-cf7-contact-table__field">[text* your-name]</div>
	</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">ふりかな 必須</div>
		<div class="jqs-cf7-contact-table__field">[text* your-kana]</div>
	</div>

		<div class="jqs-cf7-contact-table__row">
			<div class="jqs-cf7-contact-table__label">生年月日 必須</div>
			<div class="jqs-cf7-contact-table__field">[date* your-birth default:today]</div>
		</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">性別 必須</div>
			<div class="jqs-cf7-contact-table__field jqs-cf7-contact-table__field--radios">[radio your-gender use_label_element default:1 "男性" "女性"]</div>
	</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">住 所</div>
		<div class="jqs-cf7-contact-table__field">
			<div class="jqs-cf7-zip">
				<span class="jqs-cf7-zip-mark">〒</span>
				<span class="jqs-cf7-zip-part">[text your-zip1 maxlength:3]</span>
				<span class="jqs-cf7-zip-hyphen">-</span>
				<span class="jqs-cf7-zip-part">[text your-zip2 maxlength:4]</span>
			</div>
			[text your-address1]
			[text your-address2]
			[text your-address3]
		</div>
	</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">電話番号 必須</div>
		<div class="jqs-cf7-contact-table__field">[tel* your-tel]</div>
	</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">メールアドレス 必須</div>
		<div class="jqs-cf7-contact-table__field">[email* your-email]</div>
	</div>

	<div class="jqs-cf7-contact-table__row">
		<div class="jqs-cf7-contact-table__label">自由項目 必須</div>
		<div class="jqs-cf7-contact-table__field">[textarea* your-message placeholder "お気軽にお問い合わせください。"]</div>
	</div>

	<div class="jqs-cf7-consent-row">[acceptance your-consent]個人情報の取扱いに同意する[/acceptance]</div>
	<p class="jqs-cf7-note">[入力内容の確認画面へ] ボタンをクリックして入力内容をご確認をお願いします。<br>ご入力、誠にありがとうございました。</p>
	<div class="jqs-cf7-submit-wrap">[submit class:jqs-cf7-primary "入力内容確認"]</div>
	[multistep {$step_name} first_step "{$confirm_url}"]
</div>
EOT;

	$confirm_form_body = <<<EOT
<div class="jqs-cf7-multistep-confirm">
	<div class="jqs-cf7-confirm-table">
		<p><strong>お名前：</strong> [multiform "your-name"]</p>
		<p><strong>ふりかな：</strong> [multiform "your-kana"]</p>
		<p><strong>生年月日：</strong> [multiform "your-birth"]</p>
		<p><strong>性別：</strong> [multiform "your-gender"]</p>
		<p><strong>郵便番号：</strong> [multiform "your-zip1"] - [multiform "your-zip2"]</p>
		<p><strong>住所：</strong> [multiform "your-address1"] [multiform "your-address2"] [multiform "your-address3"]</p>
		<p><strong>電話番号：</strong> [multiform "your-tel"]</p>
		<p><strong>メールアドレス：</strong> [multiform "your-email"]</p>
		<p><strong>自由項目：</strong><br>[multiform "your-message"]</p>
	</div>
	<div class="jqs-cf7-confirm-buttons">[previous "入力画面に戻る"] [submit "この内容で送信"]</div>
[multistep {$step_name} last_step send_email]
</div>
EOT;

	$mail_settings = [
		'subject'            => '【お問い合わせ】[your-name] 様',
		'sender'             => '[_site_title] <[_site_admin_email]>',
		'recipient'          => '[_site_admin_email]',
		'additional_headers' => 'Reply-To: [your-email]',
		'body'               => "お名前: [your-name]\n"
			. "ふりがな: [your-kana]\n"
			. "生年月日: [your-birth]\n"
			. "性別: [your-gender]\n"
			. "郵便番号: [your-zip1]-[your-zip2]\n"
			. "住所: [your-address1] [your-address2] [your-address3]\n"
			. "電話番号: [your-tel]\n"
			. "メールアドレス: [your-email]\n"
			. "同意: [your-consent]\n\n"
			. "お問い合わせ内容:\n[your-message]\n",
		'use_html'           => 0,
		'exclude_blank'      => 0,
	];

	$setup_data = get_option('jqs_cf7_multistep_setup_data', []);
	$existing_input_id = isset($setup_data['input_form_id']) ? (int) $setup_data['input_form_id'] : 0;
	$existing_confirm_id = isset($setup_data['confirm_form_id']) ? (int) $setup_data['confirm_form_id'] : 0;

	$input_form_id = jqs_upsert_cf7_form('お問い合わせ（入力画面）', $input_form_body, $mail_settings, [], $existing_input_id);
	$confirm_form_id = jqs_upsert_cf7_form('お問い合わせ（入力結果画面）', $confirm_form_body, $mail_settings, [], $existing_confirm_id);

	if (! $input_form_id || ! $confirm_form_id) {
		return;
	}

	$input_page_id = jqs_upsert_page_by_slug(
		'contact-input',
		'お問い合わせ（入力画面）',
		'[contact-form-7 id="' . $input_form_id . '" title="お問い合わせ（入力画面）"]'
	);

	$confirm_page_id = jqs_upsert_page_by_slug(
		'contact-confirm',
		'お問い合わせ（入力結果画面）',
		'[contact-form-7 id="' . $confirm_form_id . '" title="お問い合わせ（入力結果画面）"]'
	);

	$thanks_page_id = jqs_upsert_page_by_slug(
		'contact-thanks',
		'お問い合わせありがとうございました',
		'<p>ありがとうございました。内容を送信しました。</p>'
	);

	update_option('jqs_cf7_multistep_setup_done', 1, false);
	update_option('jqs_cf7_multistep_setup_version', $setup_version, false);
	update_option('jqs_cf7_multistep_setup_data', [
		'input_form_id'   => $input_form_id,
		'confirm_form_id' => $confirm_form_id,
		'input_page_id'   => $input_page_id,
		'confirm_page_id' => $confirm_page_id,
		'thanks_page_id'  => $thanks_page_id,
		'input_url'       => $input_url,
		'confirm_url'     => $confirm_url,
		'thanks_url'      => $thanks_url,
		'updated_at'      => current_time('mysql'),
	], false);

	set_transient('jqs_cf7_multistep_setup_notice', 1, 120);
}
add_action('admin_init', 'jqs_setup_cf7_multistep_flow');

/**
 * Setup completion admin notice.
 */
function jqs_cf7_multistep_setup_notice() {
	if (! is_admin() || ! current_user_can('manage_options')) {
		return;
	}

	if (! get_transient('jqs_cf7_multistep_setup_notice')) {
		return;
	}

	delete_transient('jqs_cf7_multistep_setup_notice');
	$data = get_option('jqs_cf7_multistep_setup_data', []);
	$input_url = isset($data['input_url']) ? (string) $data['input_url'] : home_url('/contact-input/');

	echo '<div class="notice notice-success is-dismissible"><p>'
		. 'お問い合わせの3画面フロー（入力→確認→完了）を自動作成しました。'
		. ' 入力画面: <a href="' . esc_url($input_url) . '" target="_blank" rel="noopener">確認する</a>'
		. ' / 再実行: <a href="' . esc_url(admin_url('?jqs_setup_cf7_flow=1')) . '">ここをクリック</a>'
		. '</p></div>';
}
add_action('admin_notices', 'jqs_cf7_multistep_setup_notice');

/**
 * Extract MW WP Form shortcode fields from given HTML/content.
 *
 * @param string $content Raw form content.
 * @return array<int, array{name:string,type:string}>
 */
function jqs_mwform_extract_fields_from_content($content) {
	$fields = [];
	if (! is_string($content) || $content === '') {
		return $fields;
	}

	if (! preg_match_all('/\[(mwform_[a-z0-9_]+)\b([^\]]*)\]/iu', $content, $matches, PREG_SET_ORDER)) {
		return $fields;
	}

	foreach ($matches as $match) {
		$type = strtolower((string) ($match[1] ?? ''));
		$attrs = (string) ($match[2] ?? '');
		if ($type === '' || $attrs === '') {
			continue;
		}

		if (! preg_match('/\bname\s*=\s*"([^"]+)"/u', $attrs, $name_match)) {
			continue;
		}

		$name = sanitize_key((string) ($name_match[1] ?? ''));
		if ($name === '') {
			continue;
		}

		$fields[] = [
			'name' => $name,
			'type' => $type,
		];
	}

	return $fields;
}

/**
 * Build required/format schema for MW WP Form 471 from its post content.
 *
 * @return array{required:array<int,string>,email:array<int,string>,tel:array<int,string>,consent:array<int,string>}
 */
function jqs_mwform_471_validation_schema() {
	static $cached = null;
	if (is_array($cached)) {
		return $cached;
	}

	$schema = [
		'required' => [],
		'email'    => [],
		'tel'      => [],
		'consent'  => [],
	];

	$form_content = (string) get_post_field('post_content', 471);
	if ($form_content === '') {
		$cached = $schema;
		return $schema;
	}

	$all_fields = jqs_mwform_extract_fields_from_content($form_content);
	if (! empty($all_fields)) {
		foreach ($all_fields as $field) {
			$name = $field['name'];
			$type = $field['type'];

			if (strpos($type, 'email') !== false || preg_match('/mail|email/i', $name)) {
				$schema['email'][] = $name;
			}
			if (strpos($type, 'tel') !== false || preg_match('/tel|phone/i', $name)) {
				$schema['tel'][] = $name;
			}
			if (preg_match('/agree|consent|privacy|doui|kiyaku/i', $name)) {
				$schema['consent'][] = $name;
			}
		}
	}

	if (preg_match_all('/<tr\b[^>]*>(.*?)<\/tr>/isu', $form_content, $row_matches) && ! empty($row_matches[1])) {
		foreach ($row_matches[1] as $row_html) {
			$row_text = preg_replace('/\s+/u', '', wp_strip_all_tags((string) $row_html));
			$row_fields = jqs_mwform_extract_fields_from_content((string) $row_html);
			if (empty($row_fields)) {
				continue;
			}

			$row_names = array_values(array_unique(array_map(static function ($field) {
				return (string) $field['name'];
			}, $row_fields)));

			$has_required = strpos((string) $row_text, '必須') !== false;
			if ($has_required) {
				$schema['required'] = array_merge($schema['required'], $row_names);
			}

			if (strpos((string) $row_text, 'メール') !== false) {
				$schema['email'] = array_merge($schema['email'], $row_names);
			}
			if (strpos((string) $row_text, '電話') !== false) {
				$schema['tel'] = array_merge($schema['tel'], $row_names);
			}
			if (
				strpos((string) $row_text, '同意') !== false
				|| strpos((string) $row_text, '個人情報') !== false
			) {
				$schema['consent'] = array_merge($schema['consent'], $row_names);
			}
		}
	}

	foreach ($schema as $key => $names) {
		$schema[$key] = array_values(array_unique(array_filter(array_map('sanitize_key', $names))));
	}

	$cached = $schema;
	return $schema;
}

/**
 * Apply MW WP Form server-side validation set for Contact Form key 471.
 *
 * @param MW_WP_Form_Validation $Validation Validation object.
 * @param array<string,mixed>   $data       Posted values.
 * @param MW_WP_Form_Data       $Data       Data object clone.
 * @return MW_WP_Form_Validation
 */
function jqs_apply_mwform_validation_471($Validation, $data, $Data) {
	$schema = jqs_mwform_471_validation_schema();

	foreach ($schema['required'] as $name) {
		$Validation->set_rule($name, 'noempty');
	}

	foreach ($schema['consent'] as $name) {
		$Validation->set_rule($name, 'noempty');
	}

	foreach ($schema['email'] as $name) {
		$Validation->set_rule($name, 'mail');
	}

	foreach ($schema['tel'] as $name) {
		$Validation->set_rule($name, 'tel');
	}

	return $Validation;
}
add_filter('mwform_validation_mw-wp-form-471', 'jqs_apply_mwform_validation_471', 10, 3);

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
 * Register an extra widget area that will be injected as top-most footer row.
 */
function jqs_register_footer_middle2_sidebar() {
	register_sidebar(
		[
			'name'          => 'フッター最上段（ロゴ）',
			'id'            => 'jqs-footer-middle2',
			'description'   => 'Blocksyフッターの最上部に表示される追加ウィジェットエリアです。',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		]
	);
}
add_action('widgets_init', 'jqs_register_footer_middle2_sidebar');

/**
 * Output the extra top footer row markup (later moved into footer via JS).
 */
function jqs_render_footer_middle2_row_placeholder() {
	if (! is_active_sidebar('jqs-footer-middle2')) {
		return;
	}

	echo '<div id="jqs-footer-middle2-row" class="jqs-footer-middle2-row" data-row="top2" hidden>';
	echo '<div class="ct-container">';
	echo '<div data-column="jqs-footer-middle2">';
	dynamic_sidebar('jqs-footer-middle2');
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
add_action('blocksy:footer:after', 'jqs_render_footer_middle2_row_placeholder', 1);

/**
 * Move the extra footer row to the very top of footer.
 */
function jqs_move_footer_middle2_row_script() {
	if (! is_active_sidebar('jqs-footer-middle2')) {
		return;
	}
	?>
	<script>
	(function () {
		var inject = function () {
			var row = document.getElementById('jqs-footer-middle2-row');

			if (!row) {
				return;
			}

			var footer = document.querySelector('footer.ct-footer');

			if (!footer) {
				return;
			}

			var topRow = footer.querySelector('[data-row="top"]');

			if (topRow && topRow.parentNode === footer) {
				footer.insertBefore(row, topRow);
			} else {
				footer.insertBefore(row, footer.firstChild);
			}

			row.querySelectorAll('a[href]').forEach(function (link) {
				link.setAttribute('target', '_blank');
				link.setAttribute('rel', 'noopener');
			});

			row.hidden = false;
		};

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', inject);
		} else {
			inject();
		}
	})();
	</script>
	<?php
}
add_action('wp_footer', 'jqs_move_footer_middle2_row_script', 999);

/**
 * Build footer link-logo grid markup.
 *
 * @return string
 */
function jqs_get_extra_footer_link_logos_markup() {
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

	$markup = '';
	$markup .= '<div class="jqs-extra-footer-links" aria-label="Footer partner links">';
	$markup .= '<div class="jqs-extra-footer-links__inner">';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_hd_logo . '" target="_blank" rel="noopener"><img src="' . $link_pic_hd_logo . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_br05 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br05 . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_br06 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br06 . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_br04 . '" target="_blank" rel="noopener"><img src="' . $link_pic_br04 . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_mynavi . '" target="_blank" rel="noopener"><img src="' . $link_pic_mynavi . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_insta . '" target="_blank" rel="noopener"><img src="' . $link_pic_insta . '" alt=""></a>';
	$markup .= '<a class="jqs-extra-footer-links__item" href="' . $link_url_enga . '" target="_blank" rel="noopener"><img src="' . $link_pic_enga . '" alt=""></a>';
	$markup .= '</div>';
	$markup .= '</div>';

	return $markup;
}

/**
 * Shortcode for footer link-logo grid.
 *
 * Usage: [jqs_footer_link_grid]
 *
 * @return string
 */
function jqs_footer_link_grid_shortcode() {
	return jqs_get_extra_footer_link_logos_markup();
}
add_shortcode('jqs_footer_link_grid', 'jqs_footer_link_grid_shortcode');

/**
 * Sanitize contact form header font size.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function jqs_sanitize_cf7_header_font_size($value) {
	$size = absint($value);
	if ($size < 12) {
		$size = 12;
	}
	if ($size > 64) {
		$size = 64;
	}
	return $size;
}

/**
 * Get contact form header font size in px.
 *
 * @return int
 */
function jqs_get_cf7_header_font_size() {
	return jqs_sanitize_cf7_header_font_size(get_theme_mod('jqs_cf7_contact_header_font_size', 28));
}

/**
 * Get after-sent button label for CF7 confirm page.
 *
 * @return string
 */
function jqs_get_cf7_after_sent_button_label() {
	$label = sanitize_text_field((string) get_theme_mod('jqs_cf7_after_sent_button_label', 'TOPに戻る'));
	return $label !== '' ? $label : 'TOPに戻る';
}

/**
 * Get after-sent button URL for CF7 confirm page.
 *
 * @return string
 */
function jqs_get_cf7_after_sent_button_url() {
	$default_url = home_url('/');
	$url = esc_url_raw((string) get_theme_mod('jqs_cf7_after_sent_button_url', $default_url));
	return $url !== '' ? $url : $default_url;
}

/**
 * Register Customizer control for CF7 contact form header font size.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function jqs_register_contact_form_customizer_controls($wp_customize) {
	if (! ($wp_customize instanceof WP_Customize_Manager)) {
		return;
	}

	$wp_customize->add_section(
		'jqs_contact_form_style',
		[
			'title'       => __('お問い合わせフォーム', 'default'),
			'priority'    => 160,
			'description' => __('「個人情報・ご連絡先」のフォントサイズを調整します。', 'default'),
		]
	);

	$wp_customize->add_setting(
		'jqs_cf7_contact_header_font_size',
		[
			'type'              => 'theme_mod',
			'default'           => 28,
			'sanitize_callback' => 'jqs_sanitize_cf7_header_font_size',
			'transport'         => 'refresh',
		]
	);

	$wp_customize->add_control(
		'jqs_cf7_contact_header_font_size',
		[
			'label'       => __('個人情報・ご連絡先 フォントサイズ(px)', 'default'),
			'section'     => 'jqs_contact_form_style',
			'type'        => 'number',
			'input_attrs' => [
				'min'  => 12,
				'max'  => 64,
				'step' => 1,
			],
		]
	);

	$wp_customize->add_setting(
		'jqs_cf7_after_sent_button_label',
		[
			'type'              => 'theme_mod',
			'default'           => 'TOPに戻る',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		]
	);

	$wp_customize->add_control(
		'jqs_cf7_after_sent_button_label',
		[
			'label'   => __('送信後ボタン 文言', 'default'),
			'section' => 'jqs_contact_form_style',
			'type'    => 'text',
		]
	);

	$wp_customize->add_setting(
		'jqs_cf7_after_sent_button_url',
		[
			'type'              => 'theme_mod',
			'default'           => home_url('/'),
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		]
	);

	$wp_customize->add_control(
		'jqs_cf7_after_sent_button_url',
		[
			'label'   => __('送信後ボタン URL', 'default'),
			'section' => 'jqs_contact_form_style',
			'type'    => 'url',
		]
	);
}
add_action('customize_register', 'jqs_register_contact_form_customizer_controls');

/**
 * CF7 form styles block for Customizer > Additional CSS migration.
 *
 * @return string
 */
function jqs_get_cf7_form_custom_css_block() {
	$header_font_size = jqs_get_cf7_header_font_size();
	$css = <<<'CSS'
/* JQS CF7 FORM START */
.jqs-cf7-contact-table { width: min(100%, 1100px) !important; max-width: 1100px !important; margin-left: auto !important; margin-right: auto !important; border: 1px solid #d8d8d8 !important; background: #f0f0ee !important; font-size: 14px !important; line-height: 1.6 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__header { background: #3b58b7 !important; color: #ffffff !important; padding: 0.8rem 1.2rem !important; font-size: __JQS_CF7_HEADER_FONT_SIZE__px !important; line-height: 1.2 !important; font-weight: 400 !important; letter-spacing: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__row { display: grid !important; grid-template-columns: 180px minmax(0, 1fr) !important; border-top: 1px solid #d8d8d8 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__label { background: #3b58b7 !important; color: #ffffff !important; padding: 1.05rem 0.95rem !important; display: flex !important; align-items: center !important; font-size: 14px !important; line-height: 1.45 !important; font-weight: 700 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field { padding: 0.7rem 0.95rem !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field > .wpcf7-form-control-wrap { display: block !important; margin-bottom: 0.5rem !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field > .wpcf7-form-control-wrap:last-child { margin-bottom: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="text"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="email"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="tel"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="date"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea { width: 100% !important; border: 1px solid #cfcfcf !important; background: #ffffff !important; border-radius: 0 !important; padding: 0.72rem 0.75rem !important; font-size: 14px !important; line-height: 1.45 !important; box-sizing: border-box !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea { min-height: 160px !important; resize: vertical !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="text"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="email"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="tel"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="date"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea:focus { background-color: #fffce9 !important; outline: none !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-radio { display: inline-flex !important; flex-wrap: wrap !important; align-items: center !important; gap: 0.9rem !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-list-item { margin: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-list-item label { display: inline-flex !important; align-items: center !important; gap: 0.25rem !important; font-size: 14px !important; color: #222222 !important; }
.jqs-cf7-contact-table input[type="radio"], .jqs-cf7-contact-table input[type="checkbox"] { accent-color: #000000 !important; }
.jqs-cf7-contact-table .jqs-cf7-zip { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; gap: 0.35rem !important; margin-bottom: 0.5rem !important; }
.jqs-cf7-contact-table .jqs-cf7-zip-mark, .jqs-cf7-contact-table .jqs-cf7-zip-hyphen { font-size: 14px !important; color: #222222 !important; }
.jqs-cf7-contact-table .jqs-cf7-zip-part { display: inline-block !important; width: 160px !important; max-width: 100% !important; }
.jqs-cf7-contact-table .jqs-cf7-zip-part .wpcf7-form-control-wrap { display: block !important; width: 100% !important; margin: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-contact-table__field p { margin: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-zip > p { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; gap: 0.35rem !important; margin: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-zip > p > .jqs-cf7-zip-part { display: inline-block !important; width: 160px !important; max-width: 100% !important; }
.jqs-cf7-contact-table .jqs-cf7-zip br { display: none !important; }
.jqs-cf7-contact-table .jqs-cf7-consent-row { padding: 0.9rem 1rem 0 !important; text-align: center !important; font-size: 14px !important; }
.jqs-cf7-contact-table .jqs-cf7-consent-row .wpcf7-list-item { margin: 0 !important; }
.jqs-cf7-contact-table .jqs-cf7-consent-row label { display: inline-flex !important; align-items: center !important; gap: 0.35rem !important; color: #222222 !important; }
.jqs-cf7-contact-table .jqs-cf7-note { margin: 0.7rem 0 0 !important; padding: 0 1rem !important; text-align: center !important; font-size: 14px !important; font-weight: 400 !important; color: #444444 !important; line-height: 1.6 !important; }
.jqs-cf7-contact-table .jqs-cf7-submit-wrap { padding: 1rem 1rem 1.2rem !important; text-align: center !important; display: flex !important; justify-content: center !important; }
.jqs-cf7-contact-table .jqs-cf7-submit-wrap .wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; border: 0 !important; border-radius: 0 !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; cursor: pointer !important; }
.jqs-cf7-contact-table .jqs-cf7-submit-wrap input.jqs-cf7-primary.wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; border-radius: 0 !important; }
.jqs-cf7-contact-table .wpcf7-not-valid-tip { margin-top: 0.35rem !important; font-size: 12px !important; }
.jqs-cf7-contact-table .wpcf7-response-output { margin: 0.8rem 1rem 1rem !important; }
.jqs-cf7-multistep-confirm { max-width: 1100px !important; margin: 0 auto !important; font-size: 14px !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table { border: 1px solid #d8d8d8 !important; background: #f7f7f7 !important; padding: 1rem !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table p { margin: 0 0 0.45rem !important; line-height: 1.7 !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table p:last-child { margin-bottom: 0 !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 0.7rem !important; margin-top: 0.9rem !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control { flex: 0 1 320px !important; min-height: 46px !important; border: 0 !important; border-radius: 0 !important; font-size: 18px !important; font-weight: 500 !important; cursor: pointer !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons > p { margin: 0 !important; width: 100% !important; display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 0.7rem !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control-wrap { width: auto !important; display: inline-flex !important; justify-content: center !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons > p .wpcf7-form-control-wrap { flex: 0 1 auto !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons input.wpcf7-submit, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons input.wpcf7-previous { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons button.wpcf7-previous, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons button.wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 1.2 !important; padding: 0 !important; margin: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; text-align: center !important; box-sizing: border-box !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons p, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control-wrap { text-align: center !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-previous { background: #8a8a8a !important; color: #ffffff !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-submit { background: #e63888 !important; color: #ffffff !important; }
.jqs-cf7-contact-table .wpcf7-submit[disabled], .jqs-cf7-multistep-confirm .wpcf7-submit[disabled], .jqs-cf7-multistep-confirm .wpcf7-previous[disabled] { background: #ffbedc !important; color: #ffffff !important; cursor: not-allowed !important; opacity: 1 !important; }
.wpcf7 form.sent .wpcf7-response-output { border: none !important; }
.wpcf7 .wpcf7-response-output { max-width: 1100px !important; margin-left: auto !important; margin-right: auto !important; border: none !important; font-style: normal !important; font-size: 18px !important; font-weight: 600 !important; text-align: center !important; color: #000000 !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-after-sent-actions { margin-top: 1rem !important; text-align: center !important; }
.jqs-cf7-multistep-confirm .jqs-cf7-after-sent-link { width: 320px !important; max-width: 100% !important; min-height: 56px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border: 0 !important; border-radius: 0 !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; text-decoration: none !important; padding: 0 1rem !important; }
@media (max-width: 767px) {
	.jqs-cf7-contact-table .jqs-cf7-contact-table__header { font-size: 26px !important; }
	.jqs-cf7-contact-table .jqs-cf7-contact-table__row { grid-template-columns: 1fr !important; }
	.jqs-cf7-contact-table .jqs-cf7-contact-table__label { padding: 0.7rem 0.9rem !important; }
	.jqs-cf7-contact-table .jqs-cf7-contact-table__field { padding: 0.65rem 0.8rem !important; }
	.jqs-cf7-contact-table .jqs-cf7-zip-part { width: calc((100% - 2rem) / 2) !important; }
}
/* JQS CF7 FORM END */
CSS;
	return str_replace('__JQS_CF7_HEADER_FONT_SIZE__', (string) $header_font_size, $css);
}

/**
 * Sync CF7 form CSS from MU plugin to Customizer > Additional CSS.
 *
 * Run once automatically on admin screens, and manually via:
 * /wp-admin/?jqs_copy_form_css_to_customizer=1
 */
function jqs_copy_cf7_form_css_to_customizer() {
	if (! is_admin() || ! current_user_can('manage_options')) {
		return;
	}

	$sync_version = 8;
	$force = isset($_GET['jqs_copy_form_css_to_customizer']) && (string) $_GET['jqs_copy_form_css_to_customizer'] === '1';
	$stored_version = (int) get_option('jqs_cf7_form_css_sync_version', 0);
	if ($stored_version >= $sync_version && ! $force) {
		return;
	}

	if (! function_exists('wp_get_custom_css') || ! function_exists('wp_update_custom_css_post')) {
		return;
	}

	$block = trim(jqs_get_cf7_form_custom_css_block());
	if ($block === '') {
		return;
	}

	$current = (string) wp_get_custom_css();
	$pattern = '/\/\*\s*JQS CF7 FORM START\s*\*\/.*?\/\*\s*JQS CF7 FORM END\s*\*\//is';
	$next = preg_replace($pattern, '', $current);
	if (! is_string($next)) {
		$next = $current;
	}
	$next = rtrim($next) . "\n\n" . $block . "\n";

	wp_update_custom_css_post($next);
	update_option('jqs_cf7_form_css_copied_to_customizer', 1, false);
	update_option('jqs_cf7_form_css_sync_version', $sync_version, false);
	set_transient('jqs_cf7_form_css_copied_notice', 1, 120);
}
add_action('admin_init', 'jqs_copy_cf7_form_css_to_customizer');

/**
 * Notice for CF7 CSS copy completion.
 */
function jqs_cf7_form_css_copied_notice() {
	if (! is_admin() || ! current_user_can('manage_options')) {
		return;
	}

	if (! get_transient('jqs_cf7_form_css_copied_notice')) {
		return;
	}

	delete_transient('jqs_cf7_form_css_copied_notice');

	echo '<div class="notice notice-success is-dismissible"><p>'
		. 'CF7フォームCSSを「カスタマイズ → 追加CSS」に同期しました。'
		. ' 表示確認後にMU側CSSを削除します。'
		. '</p></div>';
}
add_action('admin_notices', 'jqs_cf7_form_css_copied_notice');

/**
 * Runtime styles for service section corrections.
 */
function jqs_home_patterns_runtime_styles() {
	wp_register_style('jqs-home-patterns-runtime', false, [], null);
	wp_enqueue_style('jqs-home-patterns-runtime');
	$header_font_size = jqs_get_cf7_header_font_size();
	$css = 'p { letter-spacing: -0.01em !important; } '
		. '.jqs-contact-page-form .jqs-contact-form-placeholder { border: 1px solid #d9d9d9 !important; background: #f7f7f7 !important; padding: 1rem !important; } '
		. '.jqs-contact-page-form .jqs-contact-form-placeholder p { margin: 0 0 0.5rem 0 !important; } '
		. '.jqs-contact-page-form .jqs-contact-form-placeholder p:last-child { margin-bottom: 0 !important; } '
		. '.jqs-cf7-contact-table { width: min(100%, 1100px) !important; max-width: 1100px !important; margin-left: auto !important; margin-right: auto !important; border: 1px solid #d8d8d8 !important; background: #f0f0ee !important; font-size: 14px !important; line-height: 1.6 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__header { background: #3b58b7 !important; color: #ffffff !important; padding: 0.8rem 1.2rem !important; font-size: ' . $header_font_size . 'px !important; line-height: 1.2 !important; font-weight: 400 !important; letter-spacing: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__row { display: grid !important; grid-template-columns: 180px minmax(0, 1fr) !important; border-top: 1px solid #d8d8d8 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__label { background: #3b58b7 !important; color: #ffffff !important; padding: 1.05rem 0.95rem !important; display: flex !important; align-items: center !important; font-size: 14px !important; line-height: 1.45 !important; font-weight: 700 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field { padding: 0.7rem 0.95rem !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field > .wpcf7-form-control-wrap { display: block !important; margin-bottom: 0.5rem !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field > .wpcf7-form-control-wrap:last-child { margin-bottom: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="text"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="email"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="tel"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="date"], .jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea { width: 100% !important; border: 1px solid #cfcfcf !important; background: #ffffff !important; border-radius: 0 !important; padding: 0.72rem 0.75rem !important; font-size: 14px !important; line-height: 1.45 !important; box-sizing: border-box !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea { min-height: 160px !important; resize: vertical !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="text"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="email"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="tel"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field input[type="date"]:focus, .jqs-cf7-contact-table .jqs-cf7-contact-table__field textarea:focus { background-color: #fffce9 !important; outline: none !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-radio { display: inline-flex !important; flex-wrap: wrap !important; align-items: center !important; gap: 0.9rem !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-list-item { margin: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field--radios .wpcf7-list-item label { display: inline-flex !important; align-items: center !important; gap: 0.25rem !important; font-size: 14px !important; color: #222222 !important; } '
		. '.jqs-cf7-contact-table input[type="radio"], .jqs-cf7-contact-table input[type="checkbox"] { accent-color: #000000 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; gap: 0.35rem !important; margin-bottom: 0.5rem !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip-mark, .jqs-cf7-contact-table .jqs-cf7-zip-hyphen { font-size: 14px !important; color: #222222 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip-part { display: inline-block !important; width: 160px !important; max-width: 100% !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip-part .wpcf7-form-control-wrap { display: block !important; width: 100% !important; margin: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-contact-table__field p { margin: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip > p { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; gap: 0.35rem !important; margin: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip > p > .jqs-cf7-zip-part { display: inline-block !important; width: 160px !important; max-width: 100% !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-zip br { display: none !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-consent-row { padding: 0.9rem 1rem 0 !important; text-align: center !important; font-size: 14px !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-consent-row .wpcf7-list-item { margin: 0 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-consent-row label { display: inline-flex !important; align-items: center !important; gap: 0.35rem !important; color: #222222 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-note { margin: 0.7rem 0 0 !important; padding: 0 1rem !important; text-align: center !important; font-size: 14px !important; font-weight: 400 !important; color: #444444 !important; line-height: 1.6 !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-submit-wrap { padding: 1rem 1rem 1.2rem !important; text-align: center !important; display: flex !important; justify-content: center !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-submit-wrap .wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; border: 0 !important; border-radius: 0 !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; cursor: pointer !important; } '
		. '.jqs-cf7-contact-table .jqs-cf7-submit-wrap input.jqs-cf7-primary.wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; border-radius: 0 !important; } '
		. '.jqs-cf7-contact-table .wpcf7-not-valid-tip { margin-top: 0.35rem !important; font-size: 12px !important; } '
		. '.jqs-cf7-contact-table .wpcf7-response-output { margin: 0.8rem 1rem 1rem !important; } '
		. '.jqs-cf7-multistep-confirm { max-width: 1100px !important; margin: 0 auto !important; font-size: 14px !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table { border: 1px solid #d8d8d8 !important; background: #f7f7f7 !important; padding: 1rem !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table p { margin: 0 0 0.45rem !important; line-height: 1.7 !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-table p:last-child { margin-bottom: 0 !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 0.7rem !important; margin-top: 0.9rem !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control { flex: 0 1 320px !important; min-height: 46px !important; border: 0 !important; border-radius: 0 !important; font-size: 18px !important; font-weight: 500 !important; cursor: pointer !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons > p { margin: 0 !important; width: 100% !important; display: flex !important; flex-wrap: wrap !important; justify-content: center !important; gap: 0.7rem !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control-wrap { width: auto !important; display: inline-flex !important; justify-content: center !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons > p .wpcf7-form-control-wrap { flex: 0 1 auto !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons input.wpcf7-submit, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons input.wpcf7-previous { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 56px !important; padding: 0 !important; margin: 0 !important; display: inline-block !important; text-align: center !important; vertical-align: middle !important; box-sizing: border-box !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons button.wpcf7-previous, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons button.wpcf7-submit { width: 320px !important; max-width: 100% !important; height: 56px !important; line-height: 1.2 !important; padding: 0 !important; margin: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; text-align: center !important; box-sizing: border-box !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons p, .jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control-wrap { text-align: center !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-form-control-wrap { display: inline-flex !important; justify-content: center !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons br { display: none !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-previous { background: #8a8a8a !important; color: #ffffff !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-confirm-buttons .wpcf7-submit { background: #e63888 !important; color: #ffffff !important; } '
		. '.jqs-cf7-contact-table .wpcf7-submit[disabled], .jqs-cf7-multistep-confirm .wpcf7-submit[disabled], .jqs-cf7-multistep-confirm .wpcf7-previous[disabled] { background: #ffbedc !important; color: #ffffff !important; cursor: not-allowed !important; opacity: 1 !important; } '
		. '.wpcf7 form.sent .wpcf7-response-output { border: none !important; } '
		. '.wpcf7 .wpcf7-response-output { max-width: 1100px !important; margin-left: auto !important; margin-right: auto !important; border: none !important; font-style: normal !important; font-size: 18px !important; font-weight: 600 !important; text-align: center !important; color: #000000 !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-after-sent-actions { margin-top: 1rem !important; text-align: center !important; } '
		. '.jqs-cf7-multistep-confirm .jqs-cf7-after-sent-link { width: 320px !important; max-width: 100% !important; min-height: 56px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border: 0 !important; border-radius: 0 !important; background: #e63888 !important; color: #ffffff !important; font-size: 18px !important; font-weight: 500 !important; text-decoration: none !important; padding: 0 1rem !important; } '
		. '@media (max-width: 767px) { .jqs-cf7-contact-table .jqs-cf7-contact-table__header { font-size: 26px !important; } .jqs-cf7-contact-table .jqs-cf7-contact-table__row { grid-template-columns: 1fr !important; } .jqs-cf7-contact-table .jqs-cf7-contact-table__label { padding: 0.7rem 0.9rem !important; } .jqs-cf7-contact-table .jqs-cf7-contact-table__field { padding: 0.65rem 0.8rem !important; } .jqs-cf7-contact-table .jqs-cf7-zip-part { width: calc((100% - 2rem) / 2) !important; } } '
		. '.jqs-mwform-wrap .jqs-mwform-error { margin: 0.45rem 0 0 0 !important; color: #d63638 !important; font-size: 14px !important; line-height: 1.4 !important; font-weight: 700 !important; } '
		. '.jqs-mwform-wrap .jqs-privacy-row .jqs-mwform-error { text-align: left !important; display: block !important; } '
		. '.jqs-mwform-wrap .jqs-mwform-invalid { border-color: #d63638 !important; } '
		. '.jqs-mwform-wrap input[type="text"], .jqs-mwform-wrap input[type="email"], .jqs-mwform-wrap input[type="tel"], .jqs-mwform-wrap textarea, .jqs-mwform-wrap select { border-radius: 0 !important; } '
		. '.jqs-mwform-wrap input[type="text"]:hover, .jqs-mwform-wrap input[type="email"]:hover, .jqs-mwform-wrap input[type="tel"]:hover, .jqs-mwform-wrap textarea:hover, .jqs-mwform-wrap select:hover, .jqs-mwform-wrap input[type="text"]:focus, .jqs-mwform-wrap input[type="email"]:focus, .jqs-mwform-wrap input[type="tel"]:focus, .jqs-mwform-wrap textarea:focus, .jqs-mwform-wrap select:focus { background-color: #fffce9 !important; } '
		. '[style*="background-color:#3359d3"] { background-color: #344da8 !important; } '
		. '.jqs-service-overview .jqs-service-badge { width: 70px !important; height: 70px !important; border-radius: 999px !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 1rem auto !important; } '
		. '.jqs-service-overview .jqs-service-badge > * { margin: 0 !important; line-height: 1 !important; } '
		. '@media (min-width: 1025px) { .jqs-service-overview > .wp-block-columns { align-items: stretch !important; } .jqs-service-overview > .wp-block-columns > .wp-block-column { display: flex !important; flex-direction: column !important; } .jqs-service-overview > .wp-block-columns > .wp-block-column > .wp-block-image { width: 350px !important; max-width: 350px !important; margin-left: auto !important; margin-right: auto !important; margin-bottom: 0 !important; } .jqs-service-overview > .wp-block-columns > .wp-block-column > .wp-block-image img { width: 350px !important; min-width: 350px !important; max-width: 350px !important; height: auto !important; display: block !important; } .jqs-service-overview > .wp-block-columns > .wp-block-column > h5, .jqs-service-overview > .wp-block-columns > .wp-block-column > h4, .jqs-service-overview > .wp-block-columns > .wp-block-column > p { width: 350px !important; max-width: 350px !important; margin-left: auto !important; margin-right: auto !important; } } '
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
		. '.jqs-vehicle-types .jqs-capacity-label { width: fit-content !important; max-width: 84px !important; margin: 0 !important; padding: 0.4rem 0.8rem !important; line-height: 1.1 !important; font-size: 1.1rem !important; background-color: #3b58b7 !important; } '
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
		. '.jqs-independent-startup { text-align: inherit !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__banner { margin: 0 auto !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__banner img { width: 100% !important; height: auto !important; display: block !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__section-title { width: 100% !important; margin: 0 auto 0.8rem auto !important; margin-left: auto !important; margin-right: auto !important; padding: 0.8rem 1rem !important; background-color: #3b58b7 !important; color: #ffffff !important; text-align: center !important; box-sizing: border-box !important; } '
		. '.jqs-independent-startup .has-text-align-center { text-align: center !important; } '
		. '.jqs-independent-startup ul, .jqs-independent-startup ol { padding-left: 1.4em !important; margin-left: 0 !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__table { margin: 0 auto !important; margin-left: auto !important; margin-right: auto !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__table table { width: 100% !important; border-collapse: collapse !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__table th, .jqs-independent-startup .jqs-independent-startup__table td { border-bottom: 1px solid #d8deef !important; padding: 0.72rem 0.85rem !important; vertical-align: top !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__table th { width: 180px !important; white-space: nowrap !important; font-weight: 700 !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__contract-flow { margin: 0 auto !important; } '
		. '.jqs-independent-startup .jqs-independent-flow { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 12px !important; } '
		. '.jqs-independent-startup .jqs-independent-flow__item { min-height: 170px !important; padding: 0.9rem 1rem !important; text-align: left !important; position: relative !important; clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%); } '
		. '.jqs-independent-startup .jqs-independent-flow__item:not(.has-background) { background-color: #e8e6cd !important; } '
		. '.jqs-independent-startup .jqs-independent-flow__item:last-child { clip-path: none !important; } '
		. '.jqs-independent-startup .jqs-independent-flow__item h4 { margin: 0 0 0.6rem 0 !important; padding-bottom: 0.55rem !important; border-bottom: 1px solid #222 !important; font-weight: 700 !important; line-height: 1.3 !important; text-align: left !important; } '
		. '.jqs-independent-startup .jqs-independent-flow__item p { margin: 0 !important; line-height: 1.6 !important; text-align: left !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__flow { margin-bottom: 0.8rem !important; } '
		. '.jqs-independent-startup .jqs-independent-startup__flow:last-child { margin-bottom: 0 !important; } '
		. '.jqs-independent-startup .wp-block-buttons { justify-content: center !important; margin-left: auto !important; margin-right: auto !important; } '
		. '@media (max-width: 900px) { .jqs-independent-startup .jqs-independent-flow { grid-template-columns: 1fr !important; gap: 10px !important; } .jqs-independent-startup .jqs-independent-flow__item { clip-path: none !important; min-height: auto !important; } } '
		. '.jqs-about-us-banner .jqs-about-us-banner-image { width: 100% !important; max-width: 900px !important; margin-left: auto !important; margin-right: auto !important; margin-bottom: 0 !important; } '
		. '.jqs-about-us-banner .jqs-about-us-banner-image img { width: 100% !important; height: auto !important; display: block !important; } '
		. '.jqs-newgrad-job-accordion { padding-top: 2rem !important; padding-bottom: 2rem !important; } '
		. '.jqs-newgrad-job-accordion > h2.wp-block-heading { margin-bottom: 0.8rem !important; } '
		. '.jqs-newgrad-job-accordion > p { margin-bottom: 1.1rem !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__grid { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 1rem !important; max-width: 980px !important; margin-left: auto !important; margin-right: auto !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item { border: 1px solid #d8d8d8 !important; border-radius: 0 !important; overflow: hidden !important; background: #ffffff !important; margin: 0 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item summary { list-style: none !important; cursor: pointer !important; font-weight: 700 !important; background: transparent !important; color: inherit !important; text-align: center !important; padding: 0.8rem 2.2rem 0.8rem 1rem !important; position: relative !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item summary::marker { content: "" !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item summary::-webkit-details-marker { display: none !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item summary::after { content: "+"; position: absolute !important; right: 0.85rem !important; top: 50% !important; transform: translateY(-50%) !important; color: inherit !important; font-size: 1.15rem !important; font-weight: 700 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item[open] summary { background: transparent !important; color: inherit !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__item[open] summary::after { content: "-"; color: inherit !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__body { margin: 0 !important; padding: 0 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__body > * { margin: 0 !important; padding: 0.72rem 0.82rem !important; border-top: 1px solid #e2e2e2 !important; font-size: 0.94rem !important; line-height: 1.6 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__body > *:first-child { border-top: 1px solid #e2e2e2 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__body strong { font-weight: 700 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table { margin: 0 !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table table { width: 100% !important; border-collapse: collapse !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table th, .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table td { border-top: 1px solid #e2e2e2 !important; padding: 0.7rem 0.8rem !important; vertical-align: top !important; font-size: 0.94rem !important; } '
		. '.jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table th { width: 105px !important; white-space: nowrap !important; background: transparent !important; text-align: left !important; font-weight: 700 !important; } '
		. '.jqs-newgrad-job-tabs { padding-top: 2rem !important; padding-bottom: 0 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__nav { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 0.65rem !important; margin-bottom: 1rem !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__tab { margin: 0 !important; width: 100% !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__tab .wp-block-button__link { width: 100% !important; text-align: center !important; background-color: #8ea1e0 !important; color: #ffffff !important; border: 0 !important; border-radius: 0 !important; font-weight: 700 !important; padding: 0.72rem 0.85rem !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__tab.is-active .wp-block-button__link { background-color: #3b58b7 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__panel { display: none !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__panel.is-active { display: block !important; } '
		. '.editor-styles-wrapper .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__panel { display: block !important; margin-bottom: 1rem !important; } '
		. '.editor-styles-wrapper .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__panel:last-child { margin-bottom: 0 !important; } '
		. '.editor-styles-wrapper .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__nav { margin-bottom: 0.8rem !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table { margin: 0 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table table { width: 100% !important; border-collapse: collapse !important; border: 0 !important; box-shadow: none !important; outline: 0 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table table, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table tbody, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table tr, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table th, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table td { border-left: 0 !important; border-right: 0 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table th, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table td { border-bottom: 1px solid #d8d8d8 !important; border-left: 0 !important; border-right: 0 !important; padding: 0.78rem 0.9rem !important; vertical-align: top !important; font-size: 14px !important; line-height: 1.6 !important; } '
		. '.jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table th { width: 140px !important; text-align: left !important; white-space: nowrap !important; font-weight: 700 !important; background: transparent !important; } '
		. '.jqs-newgrad-tab2-table { padding-top: 2rem !important; padding-bottom: 2rem !important; } '
		. '.jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table table { width: 100% !important; border-collapse: collapse !important; } '
		. '.jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table th, .jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table td { border-bottom: 1px solid #d8d8d8 !important; padding: 0.8rem 0.9rem !important; vertical-align: top !important; } '
		. '.jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table th { width: 170px !important; text-align: left !important; white-space: nowrap !important; font-weight: 700 !important; background: #fafafa !important; } '
		. '.jqs-newgrad-four-tables { padding-top: 2rem !important; padding-bottom: 0 !important; margin-bottom: 0 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__grid { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 1rem !important; margin-bottom: 1rem !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__grid:last-of-type { margin-bottom: 0 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item { border: 0 !important; margin: 0 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item summary { list-style: none !important; cursor: pointer !important; padding: 0.7rem 2.2rem 0.7rem 0.9rem !important; position: relative !important; font-size: 16px !important; font-weight: 700 !important; background: #3b58b7 !important; color: #ffffff !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item summary::marker { content: "" !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item summary::-webkit-details-marker { display: none !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item summary::after { content: "+"; position: absolute !important; right: 0.8rem !important; top: 50% !important; transform: translateY(-50%) !important; font-size: 1.65rem !important; font-weight: 700 !important; color: #ffffff !important; line-height: 1 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__item[open] summary::after { content: "-"; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__body { margin: 0 !important; padding: 0 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__body > * { margin: 0 !important; padding: 0.72rem 0.85rem !important; border-top: 1px solid #ffffff !important; font-size: 14px !important; line-height: 1.6 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__body > *:first-child { border-top: 1px solid #ffffff !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__body strong { font-weight: 700 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table { margin: 0 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table table { width: 100% !important; border-collapse: collapse !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table table, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table tbody, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table tr, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table th, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table td { border-color: #ffffff !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table tr { border-top: 1px solid #ffffff !important; border-bottom: 1px solid #ffffff !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table th, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table td { border-top: 1px solid #ffffff !important; border-left: 0 !important; border-right: 0 !important; padding: 0.75rem 0.85rem !important; vertical-align: top !important; font-size: 14px !important; line-height: 1.6 !important; } '
		. '.jqs-newgrad-four-tables .jqs-newgrad-four-tables__table th { width: 120px !important; text-align: left !important; white-space: nowrap !important; font-weight: 700 !important; background: #fafafa !important; } '
		. '.jqs-newgrad-talent { padding-top: 2rem !important; padding-bottom: 2rem !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__inner { border: 2px solid #bdbdbd !important; padding: 2rem 2rem 2.2rem 2rem !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__title-wrap { background: #ff99cc !important; border-radius: 0 !important; padding: 1.2rem 1rem !important; margin-bottom: 2rem !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__title-wrap h2 { margin: 0 !important; color: #ffffff !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__columns { gap: 1.5rem !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__item { margin: 0 !important; padding: 1rem 0.2rem !important; border-bottom: 2px dashed #666666 !important; } '
		. '.jqs-newgrad-talent .jqs-newgrad-talent__item:last-child { margin-bottom: 0 !important; } '
		. '@media (max-width: 767px) { .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__grid { grid-template-columns: 1fr !important; } .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table th, .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table td { display: block !important; width: 100% !important; } .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table th { border-bottom: 0 !important; padding-bottom: 0.2rem !important; } .jqs-newgrad-job-accordion .jqs-newgrad-job-accordion__table td { padding-top: 0 !important; } .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__nav { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; } .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table th, .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table td { display: block !important; width: 100% !important; } .jqs-newgrad-job-tabs .jqs-newgrad-job-tabs__table th { border-bottom: 0 !important; padding-bottom: 0.2rem !important; } .jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table th, .jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table td { display: block !important; width: 100% !important; } .jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table th { border-bottom: 0 !important; padding-bottom: 0.2rem !important; } .jqs-newgrad-tab2-table .jqs-newgrad-tab2-table__table td { padding-top: 0 !important; } .jqs-newgrad-four-tables .jqs-newgrad-four-tables__grid { grid-template-columns: 1fr !important; } .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table th, .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table td { display: block !important; width: 100% !important; } .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table th { border-bottom: 0 !important; padding-bottom: 0.2rem !important; } .jqs-newgrad-four-tables .jqs-newgrad-four-tables__table td { padding-top: 0 !important; } .jqs-newgrad-talent .jqs-newgrad-talent__inner { padding: 1.2rem !important; } .jqs-newgrad-talent .jqs-newgrad-talent__columns { gap: 0.8rem !important; } } '
		. '.jqs-office-list > h3.wp-block-heading { margin-bottom: 0 !important; } '
			. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(2), .jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(3) { display: flex !important; align-items: center !important; } '
				. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(2) p, .jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(3) p { font-size: 16px !important; line-height: 1.45 !important; margin: 0 !important; } '
				. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(4) { display: flex !important; align-items: center !important; padding-left: 2rem !important; } '
				. '.jqs-office-list .wp-block-columns.are-vertically-aligned-center > .wp-block-column:nth-child(4) a { width: 100% !important; min-height: 42px !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 16px !important; line-height: 1.2 !important; } '
				. '.jqs-management-philosophy { background-color: #f1f1f1 !important; padding-top: 2rem !important; padding-bottom: 2rem !important; } '
				. '.jqs-management-philosophy > h2.wp-block-heading { } '
				. '.jqs-management-philosophy .jqs-management-philosophy__cards { align-items: stretch !important; } '
				. '.jqs-management-philosophy .jqs-management-philosophy__card { position: relative !important; background-color: #3b58b7 !important; border-radius: 20px !important; padding: 4rem 1.5rem 1.8rem !important; min-height: 260px !important; box-sizing: border-box !important; overflow: visible !important; } '
				. '.jqs-management-philosophy .jqs-management-philosophy__number { position: absolute !important; top: -48px !important; left: 50% !important; transform: translateX(-50%) !important; width: 96px !important; height: 96px !important; border-radius: 999px !important; background-color: #3b58b7 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 !important; padding: 0 !important; line-height: 1 !important; font-size: 2.2rem !important; font-weight: 700 !important; z-index: 2 !important; } '
				. '.jqs-management-philosophy .jqs-management-philosophy__title { margin: 0 !important; color: #ffffff !important; line-height: 1.35 !important; } '
				. '.jqs-management-philosophy .jqs-management-philosophy__line { margin: 1.1rem 0 1rem 0 !important; border: none !important; border-top: 1px solid #ffffff !important; background: transparent !important; opacity: 1 !important; width: 100% !important; max-width: none !important; } '
				. '.jqs-management-philosophy .jqs-management-philosophy__desc { margin: 0 !important; color: #eaf7f9 !important; line-height: 1.55 !important; font-size: 16px !important; } '
				. '@media (max-width: 781px) { .jqs-management-philosophy .jqs-management-philosophy__card { min-height: 0 !important; padding-top: 3.6rem !important; } .jqs-management-philosophy .jqs-management-philosophy__number { width: 80px !important; height: 80px !important; top: -40px !important; font-size: 1.9rem !important; } } '
				. '.jqs-company-profile { padding-top: 2rem !important; padding-bottom: 2rem !important; } '
				. '.jqs-company-profile .jqs-company-profile__subtitle { margin: 0.1rem 0 0 0 !important; color: #22326e !important; font-size: 1.02rem !important; line-height: 1.15 !important; letter-spacing: 0.01em !important; } '
				. '.jqs-company-profile .jqs-company-profile__top { align-items: stretch !important; } '
				. '.jqs-company-profile .jqs-company-profile__info { width: 100% !important; } '
				. '.jqs-company-profile .jqs-company-profile__info-row { display: grid !important; grid-template-columns: 30% 70% !important; column-gap: 0 !important; border-bottom: 1px solid #bfbfbf !important; padding: 0.58rem 0 !important; } '
				. '.jqs-company-profile .jqs-company-profile__label, .jqs-company-profile .jqs-company-profile__value { margin: 0 !important; font-size: 1rem !important; line-height: 1.6 !important; color: #1f1f1f !important; } '
				. '.jqs-company-profile .jqs-company-profile__label { white-space: nowrap !important; } '
				. '.jqs-company-profile .jqs-company-profile__top > .wp-block-column:last-child { display: flex !important; align-items: stretch !important; } '
				. '.jqs-company-profile .jqs-company-profile__map, .jqs-company-profile .jqs-company-profile__photo, .jqs-company-profile .jqs-company-profile__brand { margin: 0 !important; } '
				. '.jqs-company-profile .jqs-company-profile__map { height: 100% !important; display: flex !important; align-items: stretch !important; } '
				. '.jqs-company-profile .jqs-company-profile__map img { width: 110% !important; max-width: 110% !important; height: 100% !important; object-fit: contain !important; display: block !important; margin-left: auto !important; margin-right: auto !important; } '
				. '.jqs-company-profile .jqs-company-profile__photo img, .jqs-company-profile .jqs-company-profile__brand img { width: 100% !important; height: 300px !important; object-fit: contain !important; display: block !important; } '
				. '@media (max-width: 960px) { .jqs-company-profile .jqs-company-profile__info-row { grid-template-columns: 32% 68% !important; } .jqs-company-profile .jqs-company-profile__label, .jqs-company-profile .jqs-company-profile__value { font-size: 0.95rem !important; } .jqs-company-profile .jqs-company-profile__map img { height: auto !important; } } '
				. '.ct-footer [data-row="top2"] { border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important; } '
				. '.ct-footer [data-row="top2"] > .ct-container { display: block !important; padding-top: 1rem !important; padding-bottom: 1rem !important; } '
				. '.ct-footer [data-row="top2"] [data-column="jqs-footer-middle2"] { min-height: 1px !important; width: 100% !important; max-width: 1280px !important; margin-left: auto !important; margin-right: auto !important; } '
				. '.jqs-footer-link-logos .is-layout-flex { gap: 2rem !important; } '
				. '.jqs-footer-link-logos .wp-block-image { margin: 0 !important; } '
				. '.jqs-footer-link-logos .wp-block-image img { height: 50px !important; width: auto !important; max-width: 100% !important; object-fit: contain !important; display: block !important; } '
				. '.jqs-extra-footer-links { padding: 1.5rem 0 !important; width: 100% !important; } '
				. '.ct-footer [data-row="top2"] [data-column="jqs-footer-middle2"] { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; justify-content: center !important; align-items: center !important; gap: 0.8rem 1rem !important; } '
				. '.ct-footer [data-row="top2"] [data-column="jqs-footer-middle2"] > * { margin: 0 !important; flex: 0 1 250px !important; max-width: 250px !important; display: flex !important; align-items: center !important; justify-content: center !important; } '
				. '.ct-footer [data-row="top2"] [data-column="jqs-footer-middle2"] > .jqs-extra-footer-links { width: 100% !important; max-width: 100% !important; } '
				. '.ct-footer [data-row="top2"] [data-column="jqs-footer-middle2"] img { height: 50px !important; width: auto !important; max-width: 100% !important; object-fit: contain !important; display: block !important; } '
				. '.jqs-extra-footer-links__inner { max-width: 1280px !important; margin-left: auto !important; margin-right: auto !important; display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; justify-content: center !important; align-items: center !important; gap: 1rem 1.2rem !important; } '
					. '.jqs-extra-footer-links__item { flex: 0 1 270px !important; max-width: 270px !important; width: 100% !important; display: flex !important; align-items: center !important; justify-content: center !important; } '
					. '.jqs-extra-footer-links__item img { height: 50px !important; width: auto !important; max-width: 100% !important; object-fit: contain !important; display: block !important; } '
					. '@media (max-width: 767px) { .jqs-extra-footer-links__inner { gap: 0.8rem !important; padding: 0 0.5rem !important; } .jqs-extra-footer-links__item { flex: 0 1 calc((100% - 0.8rem) / 2) !important; max-width: 220px !important; } }';

	// Frontend fallback for editor font-size presets (jqs-8 ... jqs-64).
	for ($size = 8; $size <= 64; $size++) {
		$css .= '.has-jqs-' . $size . '-font-size{font-size:' . $size . 'px !important;}';
	}
	$css .= '.has-jqs-noto-sans-jp-font-family{font-family:"Noto Sans JP",sans-serif !important;}';
	$css .= '.has-jqs-system-sans-font-family{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans JP",sans-serif !important;}';

	wp_add_inline_style(
		'jqs-home-patterns-runtime',
		$css
	);
}

/**
 * Runtime tab behavior for recruit job tabs pattern.
 */
function jqs_home_patterns_runtime_scripts() {
	wp_register_script('jqs-home-patterns-runtime', false, [], null, true);
	wp_enqueue_script('jqs-home-patterns-runtime');
	$after_sent_label = jqs_get_cf7_after_sent_button_label();
	$after_sent_url = jqs_get_cf7_after_sent_button_url();

	$js = <<<'JS'
(function () {
	var afterSentButtonLabel = __JQS_CF7_AFTER_SENT_BUTTON_LABEL__;
	var afterSentButtonUrl = __JQS_CF7_AFTER_SENT_BUTTON_URL__;

	function initContactFormValidation(root) {
		// Use MW WP Form server-side validation hooks for stability.
		return;

		var scope = root || document;
		var wrappers = scope.querySelectorAll('.mw_wp_form.mw_wp_form_input');
		if (!wrappers.length) {
			return;
		}

		function toArray(nodes) {
			return Array.prototype.slice.call(nodes || []);
		}

		function textOf(node) {
			return ((node && node.textContent) || '').replace(/\s+/g, '');
		}

		function clearErrors(form, context) {
			var base = context || form;
			toArray(base.querySelectorAll('.jqs-mwform-error')).forEach(function (node) {
				node.remove();
			});
			toArray(base.querySelectorAll('.jqs-mwform-invalid')).forEach(function (node) {
				node.classList.remove('jqs-mwform-invalid');
			});
		}

		function addError(anchor, controls, message) {
			if (!anchor) {
				return;
			}
			clearErrors(null, anchor);

			var error = document.createElement('p');
			error.className = 'jqs-mwform-error';
			error.textContent = message;
			anchor.appendChild(error);

			(controls || []).forEach(function (control) {
				if (control && control.classList) {
					control.classList.add('jqs-mwform-invalid');
				}
			});
		}

		function rowLabelNode(row) {
			return row.querySelector('th, .jqs-mwform-label, .jqs-form-label, td:first-child');
		}

		function rowFieldNode(row) {
			return row.querySelector('td:last-child, .jqs-mwform-field, .jqs-form-field') || row;
		}

		function isRequiredRow(row) {
			var label = rowLabelNode(row);
			if (!label) {
				return false;
			}
			return textOf(label).indexOf('必須') !== -1;
		}

		function rowControls(row) {
			return toArray(
				row.querySelectorAll(
					'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="image"]), textarea, select'
				)
			).filter(function (control) {
				return !control.disabled;
			});
		}

		function closestRow(node) {
			if (!node || !node.closest) {
				return null;
			}
			return node.closest('tr, .jqs-mwform-row, .mwform-row');
		}

		function isControlFilled(control) {
			if (!control) {
				return false;
			}

			var tagName = (control.tagName || '').toLowerCase();
			var type = (control.type || '').toLowerCase();

			if (type === 'radio' || type === 'checkbox') {
				return !!control.checked;
			}

			if (tagName === 'select') {
				return String(control.value || '').trim() !== '';
			}

			return String(control.value || '').trim() !== '';
		}

		function validateRequiredRow(row) {
			var controls = rowControls(row);
			if (!controls.length) {
				return true;
			}

			var grouped = {};
			var scalarControls = [];

			controls.forEach(function (control) {
				var type = (control.type || '').toLowerCase();
				if ((type === 'radio' || type === 'checkbox') && control.name) {
					if (!grouped[control.name]) {
						grouped[control.name] = [];
					}
					grouped[control.name].push(control);
					return;
				}
				scalarControls.push(control);
			});

			var scalarValid = scalarControls.every(isControlFilled);
			var groupNames = Object.keys(grouped);
			var groupedValid = groupNames.every(function (name) {
				return grouped[name].some(function (control) {
					return !!control.checked;
				});
			});

			if (scalarValid && groupedValid) {
				return true;
			}

			addError(rowFieldNode(row), controls, 'この項目は必須です。');
			return false;
		}

		function findConsent(form) {
			var labels = toArray(form.querySelectorAll('label'));
			var consentLabel = labels.find(function (label) {
				return textOf(label).indexOf('個人情報の取扱いに同意する') !== -1;
			});
			if (!consentLabel) {
				return null;
			}

			var input = null;
			if (consentLabel.htmlFor) {
				input = form.querySelector('#' + consentLabel.htmlFor);
			}
			if (!input) {
				input = consentLabel.querySelector('input[type="checkbox"]');
			}
			if (!input) {
				var parent = consentLabel.parentElement || form;
				input = parent.querySelector('input[type="checkbox"]');
			}
			if (!input) {
				return null;
			}

			var anchor = consentLabel.closest('.jqs-privacy-row, p, div, td, li') || consentLabel.parentElement || form;
			return { label: consentLabel, input: input, anchor: anchor };
		}

		function validateForm(form) {
			clearErrors(form);

			var rows = toArray(form.querySelectorAll('tr, .jqs-mwform-row, .mwform-row'));
			var requiredRows = rows.filter(isRequiredRow);
			var valid = true;

			requiredRows.forEach(function (row) {
				if (!validateRequiredRow(row)) {
					valid = false;
				}
			});

			var consent = findConsent(form);
			if (consent && !consent.input.checked) {
				addError(consent.anchor, [consent.input], '個人情報の取扱いへの同意が必要です。');
				valid = false;
			}

			if (!valid) {
				var firstError = form.querySelector('.jqs-mwform-error');
				if (firstError && typeof firstError.scrollIntoView === 'function') {
					firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
			}

			return valid;
		}

		wrappers.forEach(function (wrapper) {
			if (wrapper.dataset.jqsContactValidationReady === '1') {
				return;
			}

			var form = wrapper.querySelector('form');
			if (!form) {
				return;
			}

			wrapper.dataset.jqsContactValidationReady = '1';

			form.addEventListener('submit', function (event) {
				if (!validateForm(form)) {
					event.preventDefault();
					event.stopPropagation();
				}
			});

			form.addEventListener('input', function (event) {
				var row = closestRow(event.target);
				if (row) {
					clearErrors(form, row);
				}
			});

			form.addEventListener('change', function (event) {
				var row = closestRow(event.target);
				if (row) {
					clearErrors(form, row);
				}

				var consent = findConsent(form);
				if (consent && (event.target === consent.input || (consent.anchor && consent.anchor.contains(event.target)))) {
					clearErrors(form, consent.anchor);
				}
			});
		});
	}

	function initCf7ConsentGate(root) {
		var scope = root || document;
		var forms = scope.querySelectorAll('.jqs-cf7-contact-table form, form .jqs-cf7-contact-table');
		if (!forms.length) {
			return;
		}

		function toForm(node) {
			if (!node) {
				return null;
			}
			if (node.matches && node.matches('form')) {
				return node;
			}
			if (node.closest) {
				return node.closest('form');
			}
			return null;
		}

		function findConsent(form) {
			if (!form || !form.querySelector) {
				return null;
			}
			return form.querySelector('.jqs-cf7-consent-row input[type="checkbox"]');
		}

		function findSubmit(form) {
			if (!form || !form.querySelector) {
				return null;
			}
			return (
				form.querySelector('.jqs-cf7-submit-wrap input[type="submit"]')
				|| form.querySelector('.jqs-cf7-submit-wrap button[type="submit"]')
				|| form.querySelector('.jqs-cf7-submit-wrap .wpcf7-submit')
			);
		}

		function syncState(form) {
			var consent = findConsent(form);
			var submit = findSubmit(form);
			if (!consent || !submit) {
				return;
			}

			var enabled = !!consent.checked;
			submit.disabled = !enabled;
			submit.setAttribute('aria-disabled', enabled ? 'false' : 'true');
		}

		Array.prototype.slice.call(forms).forEach(function (entry) {
			var form = toForm(entry);
			if (!form || form.dataset.jqsCf7ConsentGateReady === '1') {
				return;
			}

			var consent = findConsent(form);
			var submit = findSubmit(form);
			if (!consent || !submit) {
				return;
			}

			form.dataset.jqsCf7ConsentGateReady = '1';
			syncState(form);

			consent.addEventListener('change', function () {
				syncState(form);
			});

			form.addEventListener('wpcf7invalid', function () {
				syncState(form);
			});
			form.addEventListener('wpcf7submit', function () {
				syncState(form);
			});
		});
	}

	function initCf7AfterSentButton(root, forceSentState) {
		var scope = root || document;
		var forms = scope.querySelectorAll('.jqs-cf7-multistep-confirm form.wpcf7-form, .jqs-cf7-multistep-confirm form');
		if (!forms.length) {
			return;
		}

		function ensureButton(form, isSent) {
			if (!form || !form.classList || !isSent) {
				return;
			}

			var wrapper = form.closest('.jqs-cf7-multistep-confirm') || form.parentElement;
			if (!wrapper) {
				return;
			}

			var response = wrapper.querySelector('.wpcf7-response-output');
			if (!response) {
				return;
			}

			var current = wrapper.querySelector('.jqs-cf7-after-sent-actions');
			if (current) {
				var existingLink = current.querySelector('.jqs-cf7-after-sent-link');
				if (existingLink) {
					existingLink.textContent = afterSentButtonLabel;
					existingLink.href = afterSentButtonUrl;
				}
				return;
			}

			var actions = document.createElement('div');
			actions.className = 'jqs-cf7-after-sent-actions';

			var link = document.createElement('a');
			link.className = 'jqs-cf7-after-sent-link';
			link.href = afterSentButtonUrl;
			link.textContent = afterSentButtonLabel;

			actions.appendChild(link);
			response.insertAdjacentElement('afterend', actions);
		}

		function cleanupButton(form) {
			if (!form || !form.closest) {
				return;
			}
			var wrapper = form.closest('.jqs-cf7-multistep-confirm') || form.parentElement;
			if (!wrapper) {
				return;
			}
			var actions = wrapper.querySelector('.jqs-cf7-after-sent-actions');
			if (actions && actions.parentNode) {
				actions.parentNode.removeChild(actions);
			}
		}

		Array.prototype.slice.call(forms).forEach(function (form) {
			if (!form || !form.classList) {
				return;
			}
			if (form.dataset.jqsCf7AfterSentButtonReady !== '1') {
				form.dataset.jqsCf7AfterSentButtonReady = '1';

				form.addEventListener('wpcf7mailsent', function () {
					ensureButton(form);
				});
				form.addEventListener('wpcf7invalid', function () {
					cleanupButton(form);
				});
				form.addEventListener('wpcf7submit', function () {
					setTimeout(function () {
						if (form.classList.contains('sent')) {
							ensureButton(form);
						} else {
							cleanupButton(form);
						}
					}, 10);
				});
			}

			var isSent = typeof forceSentState === 'boolean' ? forceSentState : form.classList.contains('sent');
			if (isSent) {
				ensureButton(form, true);
			} else {
				cleanupButton(form);
			}
		});
	}

	function initConfirmPageManualButtons(root, forceSentState) {
		var scope = root || document;
		var body = document.body;
		if (!body) {
			return;
		}

		var isConfirmPage = body.classList.contains('page-slug-contact-confirm')
			|| /\/contact-confirm\/?$/.test((window.location && window.location.pathname) || '');
		if (!isConfirmPage) {
			return;
		}

		var sent = typeof forceSentState === 'boolean'
			? forceSentState
			: !!scope.querySelector('.jqs-cf7-multistep-confirm form.wpcf7-form.sent, .jqs-cf7-multistep-confirm .wpcf7 form.sent');
		var manualBlocks = document.querySelectorAll('.entry-content .wp-block-buttons, .entry-content .wp-block-button');
		if (!manualBlocks.length) {
			return;
		}

		Array.prototype.slice.call(manualBlocks).forEach(function (block) {
			if (!block || !block.closest) {
				return;
			}
			if (block.closest('.wpcf7') || block.closest('.jqs-cf7-after-sent-actions') || block.closest('.jqs-cf7-confirm-buttons')) {
				return;
			}

			block.style.display = sent ? '' : 'none';
		});
	}

	function initTabs(root) {
		var scope = root || document;
		var containers = scope.querySelectorAll('.jqs-newgrad-job-tabs');
		if (!containers.length) {
			return;
		}

		containers.forEach(function (container) {
			if (container.dataset.jqsTabsReady === '1') {
				return;
			}

			var buttons = Array.prototype.slice.call(container.querySelectorAll('.jqs-newgrad-job-tabs__tab .wp-block-button__link'));
			var panels = Array.prototype.slice.call(container.querySelectorAll('.jqs-newgrad-job-tabs__panel'));
			if (!buttons.length || !panels.length) {
				return;
			}

			container.dataset.jqsTabsReady = '1';

			function activate(targetId) {
				panels.forEach(function (panel) {
					var isActivePanel = panel.id === targetId;
					panel.classList.toggle('is-active', isActivePanel);
				});

				buttons.forEach(function (link) {
					var isActiveButton = link.getAttribute('href') === '#' + targetId;
					var buttonWrap = link.closest('.jqs-newgrad-job-tabs__tab');
					if (buttonWrap) {
						buttonWrap.classList.toggle('is-active', isActiveButton);
					}
					link.setAttribute('aria-selected', isActiveButton ? 'true' : 'false');
				});
			}

			buttons.forEach(function (link) {
				link.addEventListener('click', function (event) {
					var href = link.getAttribute('href') || '';
					if (href.indexOf('#') !== 0) {
						return;
					}

					var targetId = href.slice(1);
					if (!container.querySelector('#' + targetId)) {
						return;
					}

					event.preventDefault();
					activate(targetId);
				});
			});

			var defaultId = '';
			var activeButton = container.querySelector('.jqs-newgrad-job-tabs__tab.is-active .wp-block-button__link');
			if (activeButton) {
				defaultId = (activeButton.getAttribute('href') || '').replace('#', '');
			}
			if (!defaultId && panels[0] && panels[0].id) {
				defaultId = panels[0].id;
			}
			if (defaultId) {
				activate(defaultId);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initContactFormValidation(document);
			initCf7ConsentGate(document);
			initCf7AfterSentButton(document);
			initConfirmPageManualButtons(document);
			initTabs(document);
		});
	} else {
		initContactFormValidation(document);
		initCf7ConsentGate(document);
		initCf7AfterSentButton(document);
		initConfirmPageManualButtons(document);
		initTabs(document);
	}

	document.addEventListener('wpcf7init', function (event) {
		initCf7ConsentGate((event && event.target) || document);
		initCf7AfterSentButton((event && event.target) || document);
		initConfirmPageManualButtons(document);
	});
	document.addEventListener('wpcf7submit', function (event) {
		var status = event && event.detail ? event.detail.status : '';
		var isSent = status === 'mail_sent';
		initCf7ConsentGate((event && event.target) || document);
		initCf7AfterSentButton((event && event.target) || document, isSent);
		initConfirmPageManualButtons(document, isSent);
	});
	document.addEventListener('wpcf7mailsent', function (event) {
		initCf7AfterSentButton((event && event.target) || document, true);
		initConfirmPageManualButtons(document, true);
	});

	if (window.wp && window.wp.data && window.wp.data.subscribe) {
		var timer = null;
		window.wp.data.subscribe(function () {
			clearTimeout(timer);
			timer = setTimeout(function () {
				initContactFormValidation(document);
				initCf7ConsentGate(document);
				initCf7AfterSentButton(document);
				initConfirmPageManualButtons(document);
				initTabs(document);
			}, 120);
		});
	}
})();
JS;

	$js = str_replace(
		['__JQS_CF7_AFTER_SENT_BUTTON_LABEL__', '__JQS_CF7_AFTER_SENT_BUTTON_URL__'],
		[wp_json_encode($after_sent_label), wp_json_encode($after_sent_url)],
		$js
	);

	wp_add_inline_script('jqs-home-patterns-runtime', $js, 'after');
}
add_action('wp_enqueue_scripts', 'jqs_home_patterns_runtime_styles', 30);
add_action('enqueue_block_editor_assets', 'jqs_home_patterns_runtime_styles', 30);
add_action('wp_enqueue_scripts', 'jqs_home_patterns_runtime_scripts', 31);
