<?php
namespace com\cminds\registration\addon\approvenewusers\lib;

class Email {
	
	static function send($receivers, $subject, $body, array $vars = array(), array $headers = array()) {
		
		$hasReceivers = false;
		if (!is_array($receivers)) {
			$mailTo = $receivers;
			$hasReceivers = true;
		} else {
			$mailTo = null;
			if (count($receivers) == 1) {
				$mailTo = reset($receivers);
				$hasReceivers = true;
			}
			else foreach ($receivers as $email) {
				$email = trim($email);
				if (is_email($email)) {
					$headers[] = ' Bcc: '. $email;
					$hasReceivers = true;
				}
			}
		}
		
		if ($hasReceivers) {
			return wp_mail($mailTo, strtr($subject, $vars), strtr($body, $vars), $headers);
		} else {
			return false;
		}
		
	}
	
	
	static function getBlogVars() {
		return array(
			'[blogname]' => get_bloginfo('blogname'),
			'[siteurl]' => site_url(),
			'[wploginurl]' => wp_login_url(),
		);
	}
	
	
	static function getUserVars($userId) {
		if ($user = get_userdata($userId)) {
			$user_data = array(
				'[userdisplayname]' => $user->display_name,
				'[userlogin]' => $user->user_login,
				'[useremail]' => $user->user_email,
				'[userrole]' => implode(', ', $user->roles),
				'[userfirstname]' => $user->first_name,
				'[userlastname]' => $user->last_name,
			);
			
			$profile_user_data = array();
			$cmreg_profile_field_posts = get_posts(array(
				'post_type' => 'cmreg_profile_field',
				'post_status' => 'publish',
				'posts_per_page' => -1
			));
			if(count($cmreg_profile_field_posts) > 0) {
				foreach($cmreg_profile_field_posts as $field) {
					if($field->post_excerpt != '') {
						$post_excerpt_value = get_user_meta($user->ID, $field->post_excerpt, true);
						if(is_array($post_excerpt_value)) {
							$profile_user_data['['.$field->post_excerpt.']'] = print_r($post_excerpt_value, true);
						} else {
							$profile_user_data['['.$field->post_excerpt.']'] = ($post_excerpt_value != '')?ucwords($post_excerpt_value):'';
						}
					}
				}
			}
			$user_data = array_merge($user_data, $profile_user_data);

			return $user_data;
		} else {
			return array();
		}
	}
	
}
