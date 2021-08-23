<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\InvitationCode;

/**
 * This class integrates with Gravity Forms Registration Add-on.
 *
 * Since the addon uses pure wp_create_user() function to create new users
 * we have to call some methods manually.
 *
 */
class GravityFormsRegistrationController extends Controller {


	static $filters = array(
// 		'gform_disable_registration' => array('args' => 3), // integrates with Gravity Forms Registration Add-on
		'gform_user_registration_validation' => array('args' => 3),
		'gform_userregistration_feed_settings_fields' => array('args' => 2)
	);

	static $actions = array(
        'gform_activate_user' => array('args' => 3),
        //'register_post' => array('args' => 3, 'priority' => 999)
    );

    const FIELD_NAME = 'cmreg_invit_code';

    static function user_registration_signup_meta( $meta, $form, $entry, $feed ) {
        if( $fieldId = static::getFieldId(static::FIELD_NAME, $feed ) ) {
			$code = static::getInvitationCodeString( $entry, $feed );
			$instance = InvitationCode::getByCode($code);
			$instance->incrementUsersCount();
        }
        return $meta;
    }

    static function gform_activate_user( $userId, $user_data, $meta ) {
        $field = static::FIELD_NAME;
        $invitationCodeString = $meta[$field];
        add_filter(InvitationCodesController::FILTER_GET_INPUT_INVITATION_CODE, function($str) use ($invitationCodeString) {
            return $invitationCodeString;
        });
        do_action( 'register_new_user', $userId );
    }


	/**
	 * Validates the registration and performs pre-register actions.
	 *
	 * @param array $form
	 * @param array $feed
	 * @param unknown $submitted_page
	 * @return array
	 */
	static function gform_user_registration_validation($form, $feed, $submitted_page) {

		// Run only if admin has chosen the invitation code field for this feed
		if ($fieldId = static::getFieldId(static::FIELD_NAME, $feed)) {
			$entry = \GFFormsModel::get_current_lead();

			$username = static::getEntryValue('username', $entry, $feed);
			$email = static::getEntryValue('email', $entry, $feed);
			$invitationCodeString = static::getInvitationCodeString($entry, $feed);

			// Create filter to return this code later when it's needed
			add_filter(InvitationCodesController::FILTER_GET_INPUT_INVITATION_CODE, function($str) use ($invitationCodeString) {
				return $invitationCodeString;
			});

			// Call pre-registration hook - it performs validation in our plugin:
			$wp_error = new \WP_Error();
			do_action('register_post', $username, $email, $wp_error);

			if (!empty($wp_error->errors)) {
				// Display the validation errors in the gravity form
				$form = static::makeInvitationCodeFieldInvalid($form, $feed);
				foreach ($wp_error->errors as $errorKey => $error) {
					add_filter('gform_validation_message', function($msg, $form) use ($errorKey, $error) {
						foreach ($error as $e) {
							$msg .= sprintf('<div class="validation_error cmreg-error" data-error="%s">%s</div>', esc_attr($errorKey), $e);
						}
						return $msg;
					}, 20, 2);
				}
			}

			// Add action called after user has been registred - call the standard WP hook because our plugin is hooked into it:
			add_action('gform_user_registered', function($userId, $feed, $entry, $pass) {
				do_action('register_new_user', $userId);
			}, 100, 4);

            //for user activation
            $forms = \RGFormsModel::get_forms( 1, 'title' );
            foreach ( $forms as $f ) {
                add_filter( 'gform_user_registration_signup_meta_' . $f->id, array( get_class(), 'user_registration_signup_meta' ), 10, 4 );
            }
			
// 			var_dump($entry);
// 			var_dump($form);
// 			var_dump($feed);
// 			var_dump($submitted_page);
// 			exit;
		}
		return $form;
	}
	
	
	protected static function getFieldId($name, $feed) {
		if (isset($feed['meta'][$name])) {
			$fieldId = $feed['meta'][$name];
			return $fieldId;
		}
	}
	
	
	protected static function getEntryValue($name, $entry, $feed) {
		if ($fieldId = static::getFieldId($name, $feed)) {
			if (isset($entry[$fieldId])) {
				return $entry[$fieldId];
			}
		}
	}
	
	
// 	protected static function getInvitationCodeFieldIndex($form, $feed) {
// 		foreach ($form['fields'] as $i => $field) {
// 			if (static::FIELD_NAME == $field->adminLabel) {
// 				return $i;
// 			}
// 		}
// 	}
	
	
// 	protected static function getInvitationCodeFieldId($form, $feed) {
// 		if ($index = static::getInvitationCodeFieldIndex($form, $feed)) {
// 			$field = $form['fields'][$index];
// 			return $field->id;
// 		}
// 	}
	
	
	protected static function getInvitationCodeString($entry, $feed) {
		return static::getEntryValue(static::FIELD_NAME, $entry, $feed);
	}
	
	
	protected static function makeInvitationCodeFieldInvalid($form, $feed) {
		if ($fieldId = static::getFieldId(static::FIELD_NAME, $feed)) {
			foreach ($form['fields'] as &$field) {
				if ($field->id == $fieldId) {
					$field->failed_validation = true;
// 					$field->validation_message = 'error';
				}
			}
// 			$form['fields'][$fieldIndex]->failed_validation = true;
// 			$form['fields'][$fieldIndex]->validation_message = 'error';
		}
		return $form;
	}
	
	
	/**
	 * Add new option into the Registration form settings in Gravity Forms plugin
	 * 
	 * @param array $fields
	 * @param array $form
	 */
	static function gform_userregistration_feed_settings_fields($fields, $form) {
		$fields['user_settings']['fields'][] = array(
			'name'     => static::FIELD_NAME,
			'label'    => 'Invitation code',
			'type'     => 'field_select',
			'args'     => array(
// 				'callback' => array( $this, 'is_applicable_field_for_field_select' )
			),
			'class'    => 'medium',
			'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Invitation Code', 'gravityformsuserregistration' ), esc_html__( 'Select the form field that should be used for the invitation code used by the CM Registration plugin.', 'gravityformsuserregistration' ) )
		);
		return $fields;
	}
	
	
}
