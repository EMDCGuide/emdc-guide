<?php
namespace com\cminds\registration\model;

use com\cminds\registration\App;

class ProfileField extends PostType {
	
	const POST_TYPE = 'cmreg_profile_field';
	
	const META_MAX_LENGTH = 'cmreg_max_len';
	const META_REQUIRED = 'cmreg_required';
	const META_ROLES = 'cmreg_roles';
	const META_FIELD_TYPE = 'cmreg_type';
	const META_SUBTYPE = 'cmreg_subtype';
	const META_TOOLTIP = 'cmreg_tooltip';
	const META_PLACEHOLDER = 'cmreg_placeholder';
	const META_CSS_CLASS = 'cmreg_css_class';
	const META_TEXTAREA_ROWS = 'cmreg_textare_rows';
	const META_NUMBER_MIN = 'cmreg_number_min';
	const META_NUMBER_MAX = 'cmreg_number_max';
	const META_NUMBER_STEP = 'cmreg_number_step';
	const META_MULTIPLE_SELECTION = 'cmreg_multiple_selection';
	const META_OPTIONS_VALUES = 'cmreg_options_values';
	const META_SHOW_IN_REGISTRATION_FORM = 'cmreg_show_in_registration_form';
	const META_SHOW_IN_USER_PROFILE = 'cmreg_show_in_user_profile';
	const META_REGISTRATION_FORM_ROLE = 'cmreg_registration_form_role';
	
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_EMAIL = 'email';
	const FIELD_TYPE_PASSWORD = 'password';
	const FIELD_TYPE_TEXTAREA = 'textarea';
	const FIELD_TYPE_NUMBER = 'number';
	const FIELD_TYPE_SELECT = 'select';
	const FIELD_TYPE_RADIO_GROUP = 'radio-group';
	const FIELD_TYPE_CHECKBOX_GROUP = 'checkbox-group';
	const FIELD_TYPE_DATE = 'date';
	
	const CONTEXT_REGISTRATION_FORM = 'cmreg_registration_form';
	const CONTEXT_USER_PROFILE = 'cmreg_user_profile';
	
	const REGISTRATION_FORM_ROLE_EMAIL = 'email';
	const REGISTRATION_FORM_ROLE_PASSWORD = 'cmregpw';
	const REGISTRATION_FORM_ROLE_EMAIL_REPEAT = 'cmregemailrepeat';
	const REGISTRATION_FORM_ROLE_PASSWORD_REPEAT = 'cmregpwrepeat';
	const REGISTRATION_FORM_ROLE_DISPLAY_NAME = 'display_name';
	const REGISTRATION_FORM_ROLE_USERNAME = 'username';
	const REGISTRATION_FORM_ROLE_INVITATION_CODE = 'invitation_code';
		
	static protected $postTypeOptions = array(
		'label' => 'Profile Field',
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => false,
		'show_in_admin_bar' => false,
		'show_in_menu' => App::SLUG,
		'hierarchical' => false,
		'supports' => array('title'),
		'has_archive' => false,
		//'taxonomies' => array(Category::TAXONOMY),
	);
	
	static protected function getPostTypeLabels() {
		$singular = ucfirst('Profile field');
		$plural = ucfirst('Profile fields');
		return array(
			'name' => $plural,
			'singular_name' => $singular,
			'add_new' => sprintf(__('Add %s', App::SLUG), $singular),
			'add_new_item' => sprintf(__('Add New %s', App::SLUG), $singular),
			'edit_item' => sprintf(__('Edit %s', App::SLUG), $singular),
			'new_item' => sprintf(__('New %s', App::SLUG), $singular),
			'all_items' => $plural,
			'view_item' => sprintf(__('View %s', App::SLUG), $singular),
			'search_items' => sprintf(__('Search %s', App::SLUG), $plural),
			'not_found' => sprintf(__('No %s found', App::SLUG), $plural),
			'not_found_in_trash' => sprintf(__('No %s found in Trash', App::SLUG), $plural),
			'menu_name' => App::getPluginName()
		);
	}
	
	static function init() {
		//static::$postTypeOptions['rewrite'] = array('slug' => Settings::getOption(Settings::OPTION_PERMALINK_PREFIX));
		parent::init();
	}
	
	/**
	 * Get instance
	 *
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\registration\model\ProfileField
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	static function getJSData() {
		$fields = static::getAll();
		$data = array();
		foreach ($fields as $field) {
			/* @var $field ProfileField */
			$roles = $field->getRoles();
			$data[] = array(
				'name' => $field->getUserMetaKey(),
				'label' => $field->getLabel(),
				'type' => $field->getFieldType(),
				'required' => $field->isRequired(),
				'maxlength' => $field->getMaxLength(),
				'placeholder' => $field->getPlaceholder(),
				'description' => $field->getTooltip(),
				'value' => $field->getDefaultValue(),
				'className' => $field->getCSSClass(),
				'subtype' => $field->getSubtype(),
				'rows' => $field->getTextareaRows(),
				'min' => $field->getNumberMin(),
				'max' => $field->getNumberMax(),
				'step' => $field->getNumberStep(),
				'multiple' => $field->isMultipleSelectionAllowed(),
				'values' => $field->getOptionsValues(),
				'access' => !empty($roles),
				'role' => (is_array($roles) ? implode(',', $roles) : ''),
				'showInRegistration' => (boolean)$field->showInRegistrationForm(),
				'showInProfile' => (boolean)$field->showInUserProfile(),
				'registrationFormRole' => $field->getRegistrationFormRole(),
			);
		}
		return $data;
	}
	
	static function getForRegistrationForm($formId) {
		$posts = get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'parent' => $formId,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		));
		return array_map(function($post) {
			return ProfileField::getInstance($post);
		}, $posts);
	}
	
	/**
	 * 
	 * @return ProfileField[]
	 */
	static function getAll() {
		$posts = get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		));
		return array_map(function($post) {
			return ProfileField::getInstance($post);
		}, $posts);
	}
	
	static function getAllIds() {
		return get_posts(array(
			'post_type' => ProfileField::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'fields' => 'ids',
		));
	}
	
	static function create($userMeta, $label, $type) {
		$post = array(
			'post_title' => strip_tags($label),
			'post_excerpt' => $userMeta,
			'post_author' => get_current_user_id(),
			'post_status' => 'publish',
			'post_type' => static::POST_TYPE,
			'comment_status' => 'closed',
			'ping_status' => 'closed',
		);
		$postId = wp_insert_post($post);
		if (is_numeric($postId)) {
			$field = static::getInstance($postId);
			$field->setFieldType($type);
			return $field;
		}
	}
	
	function getLabel() {
		return $this->getTitle();
	}
	
	function setLabel($val) {
		$this->post->post_title = $val;
		$this->save();
		return $this;
	}
	
	function setUserMetaKey($val) {
		$this->post->post_excerpt = $val;
		$this->save();
		return $this;
	}
	
	function getUserMetaKey() {
		return $this->getExcerpt();
	}
	
	function getMaxLength() {
		return $this->getPostMeta(static::META_MAX_LENGTH);
	}
	
	function setMaxLength($val) {
		return $this->setPostMeta(static::META_MAX_LENGTH, $val);
	}
	
	function getFieldType() {
		return $this->getPostMeta(static::META_FIELD_TYPE);
	}
	
	function setFieldType($val) {
		return $this->setPostMeta(static::META_FIELD_TYPE, $val);
	}
	
	function getSubtype() {
		return $this->getPostMeta(static::META_SUBTYPE);
	}
	
	function setSubtype($val) {
		return $this->setPostMeta(static::META_SUBTYPE, $val);
	}
	
	function getTooltip() {
		return $this->getPostMeta(static::META_TOOLTIP);
	}
	
	function setTooltip($val) {
		return $this->setPostMeta(static::META_TOOLTIP, $val);
	}
	
	function preventFromSavingDefaultValue() {
		return (strlen($this->getRegistrationFormRole()) > 0);
	}
	
	function getDefaultValue() {
		if ($this->preventFromSavingDefaultValue()) {
			return '';
		} else {
			return $this->getContent();
		}
	}
	
	function setDefaultValue($val) {
		if ($this->preventFromSavingDefaultValue()) {
			return $this;
		} else {
 			return $this->setContent($val)->save();
		}
	}
	
	function getPlaceholder() {
		return $this->getPostMeta(static::META_PLACEHOLDER);
	}
	
	function setPlaceholder($val) {
		return $this->setPostMeta(static::META_PLACEHOLDER, $val);
	}
	
	function getCSSClass() {
		return $this->getPostMeta(static::META_CSS_CLASS);
	}
	
	function setCSSClass($val) {
		return $this->setPostMeta(static::META_CSS_CLASS, $val);
	}
	
	function isRequired() {
		if (static::REGISTRATION_FORM_ROLE_INVITATION_CODE == $this->getRegistrationFormRole()
				AND Settings::INVITATION_CODE_REQUIRED == Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE)) {
			// In special case this is the invitation code field and if it's required
			return true;
		} else {
			return $this->getPostMeta(static::META_REQUIRED);
		}
	}
	
	function setRequired($val) {
		return $this->setPostMeta(static::META_REQUIRED, $val);
	}
	
	function getTextareaRows() {
		return $this->getPostMeta(static::META_TEXTAREA_ROWS);
	}
	
	function setTextareaRows($val) {
		return $this->setPostMeta(static::META_TEXTAREA_ROWS, $val);
	}
	
	function getNumberMin() {
		return $this->getPostMeta(static::META_NUMBER_MIN);
	}
	
	function setNumberMin($val) {
		return $this->setPostMeta(static::META_NUMBER_MIN, $val);
	}
	
	function getNumberMax() {
		return $this->getPostMeta(static::META_NUMBER_MAX);
	}
	
	function setNumberMax($val) {
		return $this->setPostMeta(static::META_NUMBER_MAX, $val);
	}
	
	function getNumberStep() {
		return $this->getPostMeta(static::META_NUMBER_STEP);
	}
	
	function setNumberStep($val) {
		return $this->setPostMeta(static::META_NUMBER_STEP, $val);
	}
	
	function isMultipleSelectionAllowed() {
		return $this->getPostMeta(static::META_MULTIPLE_SELECTION);
	}
	
	function setMultipleSelectionAllowed($val) {
		return $this->setPostMeta(static::META_MULTIPLE_SELECTION, $val);
	}
	
	function getOptionsValues() {
		return $this->getPostMeta(static::META_OPTIONS_VALUES);
	}
	
	function setOptionsValues($val) {
		return $this->setPostMeta(static::META_OPTIONS_VALUES, $val);
	}
	
	function getRoles() {
		return $this->getPostMeta(static::META_ROLES);
	}
	
	function setRoles($val) {
		return $this->setPostMeta(static::META_ROLES, $val);
	}
	
	function setShowInRegistrationForm($val) {
		return $this->setPostMeta(static::META_SHOW_IN_REGISTRATION_FORM, $val);
	}
	
	function showInRegistrationForm() {
		$val = $this->getPostMeta(static::META_SHOW_IN_REGISTRATION_FORM);
		if (is_null($val)) $val = true;
		return $val;
	}
	
	function setShowInUserProfile($val) {
		return $this->setPostMeta(static::META_SHOW_IN_USER_PROFILE, $val);
	}
	
	function showInUserProfile() {
		$val = $this->getPostMeta(static::META_SHOW_IN_USER_PROFILE);
		if (is_null($val)) $val = true;
		return $val;
	}
	
	function getRegistrationFormRole() {
		return $this->getPostMeta(static::META_REGISTRATION_FORM_ROLE);
	}
	
	function setRegistrationFormRole($val) {
		return $this->setPostMeta(static::META_REGISTRATION_FORM_ROLE, $val);
	}
	
	function setValueForUser($userId, $value) {
		$types = array(static::FIELD_TYPE_SELECT, static::FIELD_TYPE_CHECKBOX_GROUP);
		if (in_array($this->getFieldType(), $types) AND $this->isMultipleSelectionAllowed() AND !is_array($value)) {
			$value = array($value);
		}
		return update_user_meta($userId, $this->getUserMetaKey(), $value);
	}
	
	function getValueForUser($userId) {
		$value = get_user_meta($userId, $this->getUserMetaKey(), $single = true);
		
		// Make empty array
		$types = array(static::FIELD_TYPE_SELECT, static::FIELD_TYPE_CHECKBOX_GROUP, static::FIELD_TYPE_RADIO_GROUP);
		if (in_array($this->getFieldType(), $types)) {
			if ($this->isMultipleSelectionAllowed() AND is_scalar($value)) {
				if (strlen($value) > 0) $value = array($value);
				else $value = array();
			}
			if (empty($value)) {
				$value = array();
				$options = $this->getOptionsValues();
				foreach ($options as $option) {
					if (!empty($option['selected'])) {
						$value[] = $option['value'];
					}
				}
			}
		} else {
			if (strlen($value) == 0) {
				$value = $this->getDefaultValue();
			}
		}
		
		return $value;
	}
	
	function canShow($context, $role) {
		// Check context
		if (static::CONTEXT_REGISTRATION_FORM == $context AND !$this->showInRegistrationForm()) {
			return false;
		}
		if (static::CONTEXT_USER_PROFILE == $context AND !$this->showInUserProfile()) {
			return false;
		}
		// Check roles
		if ($roles = $this->getRoles()) {
			if (empty($role) OR (is_scalar($role) AND !in_array($role, $roles)) OR (is_array($role) AND !array_intersect($role, $roles))) {
				return false;
			}
		}
		return true;
	}
	
	function validateValue($value) {
		if ($this->isRequired() AND ((is_scalar($value) AND strlen($value) == 0) OR (!is_scalar($value) AND empty($value)))) {
			return false;
		} else {
			return true;
		}
	}

	static function getFieldByRegistrationFormRole($role) {
		$fields = static::getAll();
		foreach ($fields as $field) {
			if ($role == $field->getRegistrationFormRole()) {
				return $field;
			}
		}
	}
	
	/**
	 * Unlike the setMenuOrder() method, this will move the other posts to make a space.
	 * 
	 * @param int $position
	 */
	function setMenuPosition($position) {
		global $wpdb;
		$offset = 1;
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET menu_order = menu_order + %d WHERE post_type = %s AND menu_order >= %d",
				$offset, ProfileField::POST_TYPE, $position));
		return $this->setMenuOrder($position)->save();
	}
	
	static function recreateDefaultFields() {
		
		$createIfNotExists = function($role, $type, $label, $position, $required, $placeholder) {
			$field = ProfileField::getFieldByRegistrationFormRole($role);
			if (empty($field)) {
				$metaKey = $role;
				$field = ProfileField::create($metaKey, $label, $type);
				if ($field) {
					$field->setMenuPosition($position);
					$field->setRegistrationFormRole($role);
					$field->setShowInRegistrationForm(true);
					$field->setRequired($required);
					$field->setPlaceholder($placeholder);
				}
			}
			return $field;
		};
		
		
		// Email
		$createIfNotExists(
			$role = ProfileField::REGISTRATION_FORM_ROLE_EMAIL,
			$type = ProfileField::FIELD_TYPE_EMAIL,
			$label = (Labels::getLocalized('field_email') != 'field_email')?Labels::getLocalized('field_email'):'Email',
			$position = 1,
			$required = true,
			$placeholder = (Labels::getLocalized('field_email') != 'field_email')?Labels::getLocalized('field_email'):'Email'
		);

		// Repeat Email
		if (Settings::getOption(Settings::OPTION_REGISTER_REPEAT_EMAIL_ENABLE)) {
			$createIfNotExists(
				$role = ProfileField::REGISTRATION_FORM_ROLE_EMAIL_REPEAT,
				$type = ProfileField::FIELD_TYPE_EMAIL,
				$label = (Labels::getLocalized('field_repeat_email') != 'field_repeat_email')?Labels::getLocalized('field_repeat_email'):'Repeat Email',
				$position = 2,
				$required = true,
				$placeholder = (Labels::getLocalized('field_repeat_email') != 'field_repeat_email')?Labels::getLocalized('field_repeat_email'):'Repeat Email'
			);
		}
			
		// Login
		if (Settings::getOption(Settings::OPTION_REGISTER_LOGIN_ENABLE)) {
			$createIfNotExists(
				$role = ProfileField::REGISTRATION_FORM_ROLE_USERNAME,
				$type = ProfileField::FIELD_TYPE_TEXT,
				$label = (Labels::getLocalized('register_field_login') != 'register_field_login')?Labels::getLocalized('register_field_login'):'Login',
				$position = 3,
				$required = true,
				$placeholder = (Labels::getLocalized('register_field_login') != 'register_field_login')?Labels::getLocalized('register_field_login'):'Login'
			);
		}
			
		// Password
		if (Settings::getOption(Settings::OPTION_REGISTER_PASS_ENABLE)) {
			$createIfNotExists(
				$role = ProfileField::REGISTRATION_FORM_ROLE_PASSWORD,
				$type = ProfileField::FIELD_TYPE_PASSWORD,
				$label = (Labels::getLocalized('field_password') != 'field_password')?Labels::getLocalized('field_password'):'Password',
				$position = 4,
				$required = true,
				$placeholder = (Labels::getLocalized('field_password') != 'field_password')?Labels::getLocalized('field_password'):'Password'
			);
		}
			
		// Repeat Password
		if (Settings::getOption(Settings::OPTION_REGISTER_REPEAT_PASS_ENABLE)) {
			$createIfNotExists(
				$role = ProfileField::REGISTRATION_FORM_ROLE_PASSWORD_REPEAT,
				$type = ProfileField::FIELD_TYPE_PASSWORD,
				$label = (Labels::getLocalized('field_repeat_password') != 'field_repeat_password')?Labels::getLocalized('field_repeat_password'):'Repeat Password',
				$position = 5,
				$required = true,
				$placeholder = (Labels::getLocalized('field_repeat_password') != 'field_repeat_password')?Labels::getLocalized('field_repeat_password'):'Repeat Password'
			);
		}
		
		// Display name field
		if (Settings::getOption(Settings::OPTION_REGISTER_DISPLAY_NAME_ENABLE)) {
			$createIfNotExists(
					$role = ProfileField::REGISTRATION_FORM_ROLE_DISPLAY_NAME,
					$type = ProfileField::FIELD_TYPE_TEXT,
					$label = (Labels::getLocalized('field_display_name') != 'field_display_name')?Labels::getLocalized('field_display_name'):'Your Displayed Name',					
					$position = 6,
					$required = false,
					$placeholder = (Labels::getLocalized('field_display_name') != 'field_display_name')?Labels::getLocalized('field_display_name'):'Your Displayed Name'
			);
		}
		
		// Invitation code
		if (Settings::INVITATION_CODE_DISABLED != Settings::getOption(Settings::OPTION_REGISTER_INVIT_CODE)) {
			$createIfNotExists(
					$role = ProfileField::REGISTRATION_FORM_ROLE_INVITATION_CODE,
					$type = ProfileField::FIELD_TYPE_TEXT,
					$label = (Labels::getLocalized('field_invitation_code') != 'field_invitation_code')?Labels::getLocalized('field_invitation_code'):'Invitation Code',					
					$position = 99,
					$required = false,
					$placeholder = (Labels::getLocalized('field_invitation_code') != 'field_invitation_code')?Labels::getLocalized('field_invitation_code'):'Invitation Code'
				);
		}
		
	}
	
	static function getRegistrationFormRolesList() {
		return array(
			"" => "Meta Field",
			static::REGISTRATION_FORM_ROLE_PASSWORD => "Password",
			static::REGISTRATION_FORM_ROLE_PASSWORD_REPEAT => "Repeat Password",
			static::REGISTRATION_FORM_ROLE_USERNAME => "Username",
			static::REGISTRATION_FORM_ROLE_EMAIL => "Email",
			static::REGISTRATION_FORM_ROLE_EMAIL_REPEAT => "Repeat Email",
			static::REGISTRATION_FORM_ROLE_DISPLAY_NAME => "Display Name",
			static::REGISTRATION_FORM_ROLE_INVITATION_CODE => "Invitation Code",
		);
	}

}