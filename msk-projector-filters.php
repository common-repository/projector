<?php

if ( !defined( 'MSK_PROJECTOR_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}


/**
 * Outputs opening container HTML
 */
function msk_projector_before_outside_content() {
	$html = '<div id="main-content" class="main-content">
				<div id="primary" class="content-area">
					<div id="content" class="site-content" role="main">';

	echo apply_filters( 'msk_projector_html_opening_container', $html );
}
add_action( 'msk_projector_before_outside_content', 'msk_projector_before_outside_content');


/**
 * Outputs closing container HTML
 */
function msk_projector_after_outside_content() {
	$html = '</div><!-- #content -->
		</div><!-- #primary -->
	</div><!-- #main-content -->';

	echo apply_filters( 'msk_projector_html_closing_container', $html );
}
add_action( 'msk_projector_after_outside_content', 'msk_projector_after_outside_content');


/**
 * Outputs WIP content
 */
function msk_projector_wip_content($content, $item) {
	return '<div id="msk-projector-wip-content">' . wpautop($content) . '</div>';
}
add_filter('msk_projector_wip_content', 'msk_projector_wip_content', 10, 2);


/**
 * Outputs WIP item title
 */
function msk_projector_wip_item_title($title, $item) {
	return '<h5>' . $title . '</h5>';
}
add_filter('msk_projector_wip_item_title', 'msk_projector_wip_item_title', 10, 2);


/**
 * Outputs WIP item description
 */
function msk_projector_wip_item_desc($desc) {
	return '<div class="msk-projector-item-desc">' . wpautop($desc) . '</div>';
}
add_filter('msk_projector_wip_item_desc', 'msk_projector_wip_item_desc', 10, 2);


/**
 * Outputs WIP item image
 */
function msk_projector_wip_item_image($image, $item) {
	$image_post = get_post($image['id']);
	$image_post_caption = $image_post->post_excerpt;
	$title = ($image_post_caption != '') ? $image_post_caption : $item['title'];
	$rel = (get_option('msk_projector_global_settings_enable_zoom') == 1) ? 'rel="msk-lightbox"' : '';

	$html = '<a href="' . $image['full'] . '" ' . $rel . ' title="' . esc_attr($title) . '">';
	$html .= '<img src="' . $image['full'] . '" title="' . esc_attr($item['title']) . '" alt="' . esc_attr($item['title']) . '" />';
	$html .= '</a>';

	return $html;
}
add_filter('msk_projector_wip_item_image', 'msk_projector_wip_item_image', 10, 2);


/**
 * Starts container before each WIP item
 */
function msk_projector_before_wip_content() {
	echo '<div class="msk-projector-item">';
}
add_action('msk_projector_before_wip_content', 'msk_projector_before_wip_content');


/**
 * Ends container div after each WIP item
 */
function msk_projector_after_wip_content() {
	echo '</div>';
}
add_action('msk_projector_after_wip_content', 'msk_projector_after_wip_content');


/**
 * Starts container div before WIP item images
 */
function msk_projector_before_wip_content_image() {
	echo '<div class="msk-projector-item-images">';
}
add_action('msk_projector_before_wip_content_image', 'msk_projector_before_wip_content_image');


/**
 * Ends container div after WIP item images
 */
function msk_projector_after_wip_content_image() {
	echo '</div>';
}
add_action('msk_projector_after_wip_content_image', 'msk_projector_after_wip_content_image');


/**
 * Starts container div before WIP item txts
 */
function msk_projector_before_wip_content_txt() {
	echo '<div class="msk-projector-item-txt">';
}
add_action('msk_projector_before_wip_content_txt', 'msk_projector_before_wip_content_txt');


/**
 * Ends container div after WIP item txts
 */
function msk_projector_after_wip_content_txt() {
	echo '</div>';
}
add_action('msk_projector_after_wip_content_txt', 'msk_projector_after_wip_content_txt');