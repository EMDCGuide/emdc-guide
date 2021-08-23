<?php
namespace com\cminds\registration\shortcode;

use com\cminds\registration\model\Labels;

class InvitedByInfoShortcode extends Shortcode {
	
	const SHORTCODE_NAME = 'cmreg-Invited-by-info';

	static function shortcode($atts) {

		$atts = shortcode_atts(array(
			'show-label' => 1,
			'label-text' => 'Invited by: ',
		), $atts);

		if (is_user_logged_in()) {
			$current_user_id = get_current_user_id();
			$current_user_meta = get_user_meta($current_user_id);
			$output = '';
			if($atts['show-label'] == 1) {
				$output .= '<strong>'.$atts['label-text'].'</strong>';
			}
			if(isset($current_user_meta['cmreg_invitation_code'][0]) && $current_user_meta['cmreg_invitation_code'][0] != '') {
				$post = get_post($current_user_meta['cmreg_invitation_code'][0]);
				if($post) {
					$user = get_user_by('id', $post->post_author);
					if($user->data->display_name != '') {
						$output .= $user->data->display_name;
					} else {
						$output .= $user->data->user_nicename;
					}
				} else {
					$output .= 'Invitation code not exist!';
				}
			} else {
				$output .= 'Invitation code not found!';
			}
			return $output;
		}

	}

}