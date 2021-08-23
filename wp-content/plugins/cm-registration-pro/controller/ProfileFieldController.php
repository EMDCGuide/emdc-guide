<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\Labels;
use com\cminds\registration\model\User;
use com\cminds\registration\model\Settings;
use com\cminds\registration\App;
use com\cminds\registration\model\ProfileField;

class ProfileFieldController extends Controller {
	
	const POST_FIELDS_ARR = 'cmreg_extra_field';
	const FILTER_PROCESSING_ENABLED = 'cmreg_profile_fields_processing_enabled';
	
	static $actions = array(
		'register_form' => array('args' => 1, 'priority' => 100),
		'cmreg_register_form' => array('args' => 2, 'method' => 'register_form', 'priority' => 100),
		//show terms of service field after invitation
		array('name' => 'cmreg_register_form', 'args' => 2, 'method' => 'register_form_terms_of_service', 'priority' => 300),
		'register_post' => array('args' => 3),
		'age_verification' => array('name' => 'register_post', 'method' => 'age_verification', 'args' => 3),
		'register_new_user' => array('args' => 1),
		'cmreg_profile_edit_form' => array('args' => 1),
		'cmreg_user_profile_edit_save' => array('args' => 1),
	);

	/**
	 * Display terms of service field on the registration form.
	 *
	 * @param string $place
	 */
	static function register_form_terms_of_service($place = null, $atts = array()) {
		if (!App::isLicenseOk()) return;

		if (!isset($atts['role'])) $atts['role'] = null;

		// ToS
		$toc = Settings::getOption(Settings::OPTION_TERMS_OF_SERVICE_CHECKBOX_TEXT);
		if (strlen(strip_tags($toc)) > 0) {
			echo static::loadFrontendView('toc', compact('toc'));
		}
	}

	/**
	 * Display extra fields on the registration form.
	 *
	 * @param string $place
	 */
	static function register_form($place = null, $atts = array()) {

		if (!App::isLicenseOk()) return;
		
		if (!isset($atts['role'])) $atts['role'] = null;
		
		if (Settings::getOption(Settings::OPTION_REGISTER_ORGANIZATION_ENABLE)) {
			
			if(!is_admin()) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$label = Labels::getLocalized('field_organization')!=''?Labels::getLocalized('field_organization'):'Organization';
			echo '<div class="cmreg-registration-field organization_rowcontainer ">';
				if (Settings::getOption(Settings::OPTION_FORM_FIELD_LABEL_ENABLE)):
					echo '<label class="cmreg-label">'.$label.'</label>';
				endif;
				if ( is_plugin_active( 'cm-business-directory-pro/cm-business-directory-pro.php' ) ) {
					$organization_args = array(
						'numberposts' => -1,
						'post_type' => 'cm-business',
						'post_status' => 'publish',
						'order' => 'ASC',
						'orderby' => 'title',
					);
					$organizations = get_posts($organization_args);
					echo '<select name="cmreg_extra_field[organization]" id="cmreg_organization" class="form-control" required="">';
					echo '<option value="">'.$label.'</option>';
					if(count($organizations) > 0) {
						foreach($organizations as $organization) {
							echo '<option value="'.$organization->ID.'">'.$organization->post_title.'</option>';
						}
					}
					echo '</select>';
				} else {
					echo '<input type="text" name="cmreg_extra_field[organization]" id="cmreg_organization" placeholder="'.$label.'" class="form-control" required="">';
				}
				echo '<span class="cmreg-field-description"></span>';
			echo '</div>';
		}

		/*
		$fields = ProfileField::getJSData();
		echo '<div id="cmreg-register-profile-fields-wrap"></div>';
		echo '<script>
		window.$ = jQuery;
		jQuery(function($) {
			$("#cmreg-register-profile-fields-wrap").formRender({
			    dataType: "json",
			    formData: '. json_encode(json_encode($fields)) .'
			  });
			});
		</script>';
		*/

		// Extra fields
		// $fields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);
		$fields = ProfileField::getAll();
		if (is_array($fields)) {
			foreach ($fields as $i => $field) {
				/** @var $field ProfileField */
				if ($field->canShow(ProfileField::CONTEXT_REGISTRATION_FORM, $atts['role'])) {
					
					if (ProfileField::REGISTRATION_FORM_ROLE_INVITATION_CODE == $field->getRegistrationFormRole()) {
						echo InvitationCodesController::getInvitationCodeField(Labels::getLocalized($field->getLabel()), Labels::getLocalized($field->getPlaceholder()), $field->getTooltip());
					} else {
						echo static::loadFrontendView('registration', compact('field', 'atts'));
					}
					
				}
			}
		}
		
		// ToS
		//moved to register_form_terms_of_service
		/*$toc = Settings::getOption(Settings::OPTION_TERMS_OF_SERVICE_CHECKBOX_TEXT);
		if (strlen(strip_tags($toc)) > 0) {
			echo static::loadFrontendView('toc', compact('toc'));
		}*/
	}
	
	/**
	 * Check if processing of the profile fields (validation + saving data) is enabled
	 * @return boolean
	 */
	static function isProcessingEnabled() {
		return apply_filters(static::FILTER_PROCESSING_ENABLED, true);
	}
	
	/**
	 * Validate required fields.
	 * 
	 * @param string $sanitized_user_login
	 * @param string $user_email
	 * @param \WP_Error $errors
	 */
	static function register_post($sanitized_user_login, $user_email, \WP_Error $errors) {
		
		if (!App::isLicenseOk()) return;
		if (!static::isProcessingEnabled()) return;
		
		$fields = ProfileField::getAll();
		$role = filter_input(INPUT_POST, 'role');
		if (is_array($fields)) {
			foreach ($fields as $i => $field) {
				
				/* @var $field ProfileField */
				
				$metaName = $field->getUserMetaKey();
				
				// Don't validate fields for other roles or context
				// $fieldRoles = $field->getRoles();
				// if (!empty($fieldRoles) AND is_array($fieldRoles) AND !in_array($role, $fieldRoles)) continue;
				if (!$field->canShow(ProfileField::CONTEXT_REGISTRATION_FORM, $role)) {
					continue;
				}
				
				// Get the value
				if (ProfileField::REGISTRATION_FORM_ROLE_INVITATION_CODE === $field->getRegistrationFormRole()) {
					$value = InvitationCodesController::getInputInvitationCode();
				} else if (!empty($_POST[static::POST_FIELDS_ARR][$metaName])) {
					$value = $_POST[static::POST_FIELDS_ARR][$metaName];
				} else {
					$value = '';
				}
				
				// Check if it's required
				if (!$field->validateValue($value)) {
					error_log($value);
					if(isset($_GET['action']) && $_GET['action'] == 'register') {
						if($metaName != 'username' && $metaName != 'email') {
							$errors->add('empty_extra_field', sprintf(Labels::getLocalized('register_empty_extra_field_error'), $field->getLabel()));
						}
					} else if (isset($_POST['action']) && $_POST['action'] == 'cmreg_self_register') {

					} else {
						$errors->add('empty_extra_field', sprintf(Labels::getLocalized('register_empty_extra_field_error'), $field->getLabel()));
					}
				}
			}
		}
	}
	
	/**
	 * Save fields
	 * 
	 * @param int $userId
	 */
	static function register_new_user($userId) {
		
		if (!App::isLicenseOk()) return;

		if (!static::isProcessingEnabled()) return;
		
		// Save profile fields
		$fields = ProfileField::getAll();
		
		if(isset($_POST[static::POST_FIELDS_ARR]['organization'])) {
			update_user_meta($userId, 'organization', $_POST[static::POST_FIELDS_ARR]['organization']);
		}

		//$fields = Settings::getOption(Settings::OPTION_REGISTER_EXTRA_FIELDS);

		if (is_array($fields)) {
			foreach ($fields as $i => $field) {
			
				/* @var $field ProfileField */
				
				// Don't save if this is a registraiton field
				if ($field->getRegistrationFormRole()) continue;
				
				$metaName = $field->getUserMetaKey();
				if (isset($_POST[static::POST_FIELDS_ARR]) AND isset($_POST[static::POST_FIELDS_ARR][$metaName])) {

					$value = $_POST[static::POST_FIELDS_ARR][$metaName];

					//if (!is_scalar($value)) $value = '';

					$maxlen = $field->getMaxLength();

					if (!empty($maxlen)) {
						$value = is_scalar($value) ? substr($value, 0, $maxlen) : '';
					}
					
					$field->setValueForUser($userId, $value);
					//User::setExtraField($userId, $metaName, $value);
				}
			}
		}
	}
	
	/**
	 * Age verification.
	 *
	 * @param unknown $sanitized_user_login
	 * @param unknown $user_email
	 * @param \WP_Error $errors
	 */
	static function age_verification($sanitized_user_login, $user_email, \WP_Error $errors) {
		
		if (!App::isLicenseOk()) return;
		if (!static::isProcessingEnabled()) return;
		
		$minAge = Settings::getOption(Settings::OPTION_REGISTER_MINIMUM_AGE);
		if (!empty($minAge)) {
			$fields = ProfileField::getAll();
			$metaKey = Settings::getOption(Settings::OPTION_REGISTER_BIRTH_DATE_FIELD_META_KEY);
			foreach ($fields as $field) {
				if ($field->getUserMetaKey() == $metaKey) {
					
					// Get value
					if (!empty($_POST[static::POST_FIELDS_ARR][$metaKey])) {
						$birthTime = strtotime($_POST[static::POST_FIELDS_ARR][$metaKey]);
					} else {
						$birthTime = time();
					}
					
					// Verify
					if ($birthTime > strtotime("midnight $minAge years ago")) {
						$errors->add('age_verification_error', sprintf(Labels::getLocalized('register_age_verification_error'), $field->getLabel()));
					}
					
				}
			}
		}
		
	}
	
	static function cmreg_profile_edit_form($userId) {
		$fields = ProfileField::getAll();
		if (is_array($fields)) foreach ($fields as $i => $field) {
			if ($field->canShow(ProfileField::CONTEXT_USER_PROFILE, User::getUserRoles($userId))) {
				echo static::loadFrontendView('profile-edit-form-field', compact('field', 'atts', 'userId'));
			}
		}
	}
	
	static function cmreg_user_profile_edit_save($userId) {
		global $wpdb;
		$extraFieldsValues = (isset($_POST[static::POST_FIELDS_ARR]) ? $_POST[static::POST_FIELDS_ARR] : array());
		$fields = ProfileField::getAll();
		if (is_array($fields)) {
			foreach ($fields as $i => &$field) {
				if ($field->canShow(ProfileField::CONTEXT_USER_PROFILE, User::getUserRoles($userId))) {
					$metaName = $field->getUserMetaKey();
					$value = (isset($extraFieldsValues[$metaName]) ? $extraFieldsValues[$metaName] : '');
					if ($field->validateValue($value)) {

						if($metaName == 'username') {
							$user_name_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."users where user_login='".$value."' AND ID != '".$userId."'" );
							if($user_name_count == '0') {
								//$userdata = User::getUserData($userId);
								//$userdata->user_login = $value;
								//User::updateUserData($userdata);
								$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."users SET user_login='".$value."' WHERE ID='".$userId."'"));
							} else {
								$response['success'] = 0;
								$response['msg'] = Labels::getLocalized('user_profile_username_used');
								header('content-type: application/json');
								echo json_encode($response);
								exit;
							}
						}

						if($metaName == 'email') {
							$user_email_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."users where user_email='".$value."' AND ID != '".$userId."'" );
							if($user_email_count == '0') {
								//$userdata = User::getUserData($userId);
								//$userdata->user_email = $value;
								//User::updateUserData($userdata);
								$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."users SET user_email='".$value."' WHERE ID='".$userId."'"));
							} else {
								$response['success'] = 0;
								$response['msg'] = Labels::getLocalized('user_profile_email_used');
								header('content-type: application/json');
								echo json_encode($response);
								exit;
							}
						}

						if($metaName == 'display_name') {
							$userdata = User::getUserData($userId);
							$userdata->display_name = $value;
							User::updateUserData($userdata);
						}

						$field->setValueForUser($userId, $value);
					}
				}
			}
		}
	}
		
}