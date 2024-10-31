<?php

if ( !defined( 'MSK_PROJECTOR_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * Nice print
 */

function msk_p($arr) {
	echo "<pre style='z-index:9999;position:relative;background:#b9e0f5;padding:1em;margin:-1em 0 1em 0;border-radius:4px;overflow-x:scroll;'>";
	print_r($arr);
	echo "</pre>";
}


/**
 * Prepare WIP data
 */
function msk_projector_prepare_wip_data() {
	global $post;

	$data = array();

	/*
	 * General variables
	 */
	$data['post'] = $post;
	$data['id'] = $id = $post->ID;
	$data['title'] = $post->post_title;
	$data['content'] = $post->post_content;
	$data['slug'] = $post->post_name;
	$data['password'] = get_post_meta($id, '_wip_settings_password', true);

	/*
	 * WIP content
	 */

	$wip_content = get_post_meta($id, '_wip_content', true);

	$data['wip_content'] = array();

	foreach ($wip_content as $wip_item) {
		unset($wip_item_content);

		$wip_item_content = array(
			'title' => $wip_item['_title'],
			'desc' => $wip_item['_desc']
		);

		// Save images as IDs, full src and thumb src
		$wip_item_content['images'] = array();

		for ($i = 1; $i <= get_option('msk_projector_global_settings_img_per_wip'); $i++) {
			$field_id = '_image' . $i;

			if (isset($wip_item[$field_id]) && $wip_item[$field_id] != '') {
				$img_full = wp_get_attachment_image_src($wip_item[$field_id], 'full');
				$img_thumb = wp_get_attachment_image_src($wip_item[$field_id], 'thumbnail');

				$wip_item_content['images'][] = array(
					'id' =>  $wip_item[$field_id],
					'full' => $img_full[0],
					'thumb' => $img_thumb[0]
				);
			}
		}

		$data['wip_content'][] = $wip_item_content;
	}

	return $data;
}


/**
 * Conditional tag : is password protected ?
 */
function is_msk_projector_password_protected() {
	global $post;
	global $_POST;

	$password = get_post_meta($post->ID, '_wip_settings_password', true);

	if (!isset($password) OR $password == '') {
		return false;
	} else {
		$user_password = esc_html($_POST['msk_password']);

		if (isset($user_password) && $user_password == $password) return false;
		else return true;
	}
}


/**
 * Outputs password form HTML
 */
function msk_projector_show_password_form() {
	global $_POST;
	global $post;

	$html = '<div class="msk-projector-protected">';
	$html .= '<form id="msk-projector-protected-form" action="' . get_permalink($post->ID) .  '" method="POST">';

	$html .= get_option('msk_projector_message_settings_password_pre_text');

	$html .= '<input type="password" class="msk-password" name="msk_password" placeholder="' . __('Password', 'msk-projector') . '" />';
	$html .= '<input type="submit" class="btn button msk-submit" name="msk_submit" value="' . __('Submit', 'msk-projector') . '" />';

	if (isset($_POST['msk_password'])) $html .= get_option('msk_projector_message_settings_password_error');

	$html .= '</form>';
	$html .= '</div>';

	echo $html;
}