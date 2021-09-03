<?php
require_once(plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'autoloader.php');

use MissionalDigerati\CommunityProfile\Repositories\AnswerRepository;

/**
 * A custom Divi module for displaying questions for the SE journey.
 */
class COPR_SEQuestionForm extends ET_Builder_Module {

	/**
	 * The slug for this module
	 *
	 * @var string
	 */
	public $slug       = 'copr_se_question_form';

	/**
	 * The compatibility level of the module
	 *
	 * @var string
	 * @link https://www.elegantthemes.com/documentation/developers/divi-module/compatibility-levels/
	 */
	public $vb_support = 'on';

	/**
	 * Who is the author of the Divi Module
	 * @var [type]
	 */
	protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'Missional Digerati',
		'author_uri' => 'https://missionaldigerati.org/',
	);

	/**
	 * A form template for each individual question
	 *
	 * @var string
	 */
	protected $formTemplate = '
		<div class="copr-wrapper-$uniqueId$wrapperClasses" data-number="$questionNumber">
			<div class="copr-question-field-wrapper">
				<form action="$formAction" method="post" data-error-message="$formError">
					<input type="hidden" name="action" value="copr_save_answer" />
					<input type="hidden" name="section_tag" value="$tag" />
					<input type="hidden" name="section_title" value="$title" />
					<input type="hidden" name="section_url" value="$url" />
					<input type="hidden" name="question_number" value="$questionNumber" />
					<input type="hidden" name="question_type" value="$questionType" />
					<input type="hidden" name="question_choices" value="$questionChoices" />
					<input type="hidden" name="question" value="$question" />
					<input type="hidden" name="group_id" value="$groupId" />
					$nounce
					<div class="form-element-wrapper">
						<label>$questionNumber) $question</label>
						$formElement
						<p class="copr-hidden copr-answer-error copr-error-message"></p>
					</div>
					<div class="copr-form-buttons">
						<div class="copr-width-50">
							<a href="#" class="copr-previous"><span class="dashicons dashicons-arrow-left-alt2"></span> $prevLabel</a> |
							<a href="#" class="copr-next">$nextLabel <span class="dashicons dashicons-arrow-right-alt2"></span></a>
						</div>
						<div class="copr-width-50 copr-align-right submit">
							<input type="submit" name="submit" value="$save" data-save="$save" data-saving="$saving" />
						</div>
						<div class="copr-fixed"></div>
					</div>
				</form>
			</div>
		</div>';

	/**
	 * The group selector and add form
	 *
	 * @var string
	 */
	protected $groupSelectorTemplate = '
	<div class="copr-group-selector-wrapper" data-bp-available="$hasBP">
		<h3 class="simp-simple-header-heading">$title</h3>
		<p>$content</p>
		<div class="copr-align-center">
			$selector
			<p>
				<a href="#" class="copr-add-group">
					<span class="dashicons dashicons-plus"></span> $addGroupText
				</a>
			</p>
		</div>
		<div class="copr-add-group-form-wrapper">
		<form action="$formAction" class="copr-add-group-form" method="post" data-error-message="$formError">
			<input type="hidden" name="action" value="copr_add_group" />
			$nounce
			<div class="form-element-wrapper">
				<label>$nameLabel</label>
				<input type="text" name="group_name" value="$name" class="$nameInputClasses" />
				<p class="copr-group_name-error copr-error-message$nameErrorClasses">$nameError</p>
			</div>
			<div class="form-element-wrapper">
				<label>$descLabel</label>
				<textarea type="text" name="group_desc" rows="10" class="$descInputClasses">$desc</textarea>
				<p class="copr-group_desc-error copr-error-message$descErrorClasses">$descError</p>
			</div>
			<div class="form-element-wrapper">
				<label>$typeLabel</label>
				<div class="copr-radio-option"><input type="radio" name="group_type" value="public"$publicChecked /><label>$optionPublic</label></div>
				<div class="copr-radio-option"><input type="radio" name="group_type" value="private"$privateChecked /><label>$optionPrivate</label></div>
				<div class="copr-radio-option"><input type="radio" name="group_type" value="hidden"$hiddenChecked /><label>$optionHidden</label></div>
				<p class="copr-hidden copr-group_type-error copr-error-message"></p>
			</div>
			<div class="copr-align-right submit">
				<input type="submit" name="submit" value="$save" data-save="$save" data-saving="$saving" />
			</div>
		</form>
		</div>
	</div>';

	protected $groupSelectorForm = '
		<form action="$formAction" class="copr-select-group-form" method="post" data-error-message="$formError" data-required-message="$requiredMessage">
			<span class="dashicons dashicons-groups"></span>
			<select name="group_id" class="copr-group-selector">$options</select>
			<input type="hidden" name="action" value="copr_select_group" />
			$nounce
			<div class="submit copr-js-hide">
				<input type="submit" name="submit" value="$save" />
			</div>
		</form>
	';

	/**
	 * Initialize the module.
	 *
	 * @return void
	 */
	public function init()
	{
		$this->name = esc_html__( 'SE Question Form', 'copr-community-profile' );
	}

	/**
	 * Hide specific advanced fields
	 *
	 * @return array 	The fields to hide
	 */
	public function get_advanced_fields_config()
	{
		return array();
	}

	/**
	 * Get the fields for the Divi builder dialog
	 *
	 * @return array 	The fields for this module
	 */
	public function get_fields()
	{
		return array(
			'gs_title'	=>	array(
				'label'				=>	esc_html__('Group Selection Title', 'copr-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'default'           =>  esc_html__('Select a Group', 'copr-my-extension'),
				'description'		=>	esc_html__('The title for the group selector section.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'gs_content'	=>	array(
				'label'				=>	esc_html__('Group Selection Content', 'copr-my-extension'),
				'type'				=>	'tiny_mce',
				'option_category'	=>	'basic_option',
				'default'			=>	esc_html__('In order to begin, please select a group or add a new group.  Your answers will be saved in the group\'s community profile.', 'copr-my-extension'),
				'description'		=>	esc_html__('Content placed under the title but above the group selector.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'title'	=>	array(
				'label'				=>	esc_html__('Section Title', 'copr-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('The title for this section in the condition.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'tag'	=>	array(
				'label'				=>	esc_html__('The Curriculum Tag (ie. C1-A)', 'copr-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('The tag for this section.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'url'	=>	array(
				'label'				=>	esc_html__('Section Link', 'copr-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('The link to this section\'s page.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'questions'	=>	array(
				'label'				=>	esc_html__('The Questions', 'copr-my-extension'),
				'type'				=>	'textarea',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('Each question should be followed by a | and either the word text or scale.  Text will allow text answers.  Scale will provide a scale of options to choose from.', 'copr-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
		);
	}

	/**
	 * Render in the front end where users can see it.
	 *
	 * @param  array  $attrs       The current attributes
	 * @param  string $content     The current content
	 * @param  string $render_slug The slug of the triggering page?
	 * @return string              What to render
	 */
	public function render( $attrs, $content = null, $render_slug )
	{
		$selector = $this->getGroupSelector();
		$groupId = $this->getCurrentGroupId();
		$addGroupWrapClass = ($groupId !== -1) ? ' copr-hidden' : '';
		$addQuestionWrapClass = ($groupId === -1) ? ' copr-hidden' : '';
		$tag = strtolower(esc_html($this->props['tag']));
		return '
			<div class="copr-question-form" data-ajax-url="' . admin_url( 'admin-ajax.php' ) . '" data-section-tag="' . $tag . '">
				<div class="copr-form-error"></div>
				<div class="copr-group-selector-wrapper' . $addGroupWrapClass . '">
					' . $this->getGroupSelectorContent($selector) . '
				</div>
				<div class="copr-questions-wrapper' . $addQuestionWrapClass . '">
					<div class="copr-align-right">
						' . $selector . '
					</div>
					' . $this->getQuestionsContent() . '
				</div>
			</div>
		';
	}

	/**
	 * Get the current selected group id.
	 *
	 * @var integer	The selected group (-1 if not set)
	 */
	protected function getCurrentGroupId()
	{
		if (isset($_COOKIE) && isset($_COOKIE[COPR_GROUP_ID_COOKIE])) {
			return intval($_COOKIE[COPR_GROUP_ID_COOKIE]);
		} else {
			return -1;
		}
	}

	/**
	 * Get the group selector.
	 *
	 * @return string 	The selector for the user groups.
	 */
	protected function getGroupSelector()
	{
		if (!function_exists('bp_version')) {
			return '';
		}
		$userId = get_current_user_id();
		$groupIds = groups_get_user_groups($userId);
		$currentGroupId = $this->getCurrentGroupId();
		$groups = [];
		foreach ($groupIds['groups'] as $id) {
			$group = groups_get_group( array( 'group_id' => $id) );
			$groups[] = array(
				'id'	=>	intval($id),
				'name'	=>	$group->name,
			);
		}
		$columns = array_column($groups, 'name');
		array_multisort($columns, SORT_ASC, $groups);
		$defaultSelected = '';
		if ($currentGroupId === -1) {
			$defaultSelected = ' selected="selected"';
		}
		$options = '<option value="-1"' . $defaultSelected . '>' . __( 'Select a Group', 'copr-my-extension' ) . '</option>';
		foreach ($groups as $key => $group) {
			$selected = '';
			if ($group['id'] === $currentGroupId) {
				$selected = ' selected="selected"';
			}
			$options .= '<option value="' . $group['id'] . '"' . $selected . '>' . $group['name'] . '</option>';
		}
		$vars = array(
			'$formAction'		=>	admin_url( 'admin-ajax.php' ),
			'$formError'		=>	__( 'Sorry, we were unable to select the group.  Please try again later.', 'copr-my-extension' ),
			'$nounce'			=>	wp_nonce_field( 'select_group' ),
			'$options'			=>	$options,
			'$requiredMessage'	=>	__( 'You must select a valid group.', 'copr-my-extension' ),
			'$save'				=>	__( 'Select', 'copr-my-extension' ),
		);
		return strtr($this->groupSelectorForm, $vars);
	}

	/**
	 * Create the group selector content
	 *
	 * @param 	string	The HTML for the group selector
	 * @return 	string 	The HTMl for the group selector
	 */
	protected function getGroupSelectorContent($selector = '')
	{
		$hasBP = (function_exists('bp_version')) ? 'true' : 'false';
		$errors = (isset($_GET['error_fields'])) ? $_GET['error_fields'] : '';
		$name = (isset($_GET['group_name'])) ? urldecode($_GET['group_name']) : '';
		$desc = (isset($_GET['group_desc'])) ? urldecode($_GET['group_desc']) : '';
		$groupType = (isset($_GET['group_type'])) ? urldecode($_GET['group_type']) : '';
		$hiddenChecked = '';
		$privateChecked = '';
		$publicChecked = '';
		switch ($groupType) {
			case 'private':
				$privateChecked = ' checked';
				break;
			case 'hidden':
				$hiddenChecked = ' checked';
				break;
			default:
				$publicChecked = ' checked';
				break;
		}
		$errorsArray = explode(',', $errors);
		$descError = '';
		$descErrorClasses = ' copr-hidden';
		$nameError = '';
		$nameErrorClasses = ' copr-hidden';
		$nameInputClasses = '';
		$descInputClasses = '';
		if (in_array('group_name', $errorsArray)) {
			$nameError = __('The group name cannot be blank!', 'copr-my-extension');
			$nameErrorClasses = '';
			$nameInputClasses = 'copr-errored';
		}
		if (in_array('group_desc', $errorsArray)) {
			$descError = __('The group description cannot be blank!', 'copr-my-extension');
			$descErrorClasses = '';
			$descInputClasses = 'copr-errored';
		}
		$vars = array(
			'$addGroupText'		=>	__( 'Add a New Group', 'copr-my-extension' ),
			'$content'			=>	$this->props['gs_content'],
			'$desc'				=>	$desc,
			'$descErrorClasses'	=>	$descErrorClasses,
			'$descError'		=>	$descError,
			'$descInputClasses'	=>	$descInputClasses,
			'$descLabel'		=>	__( 'Group Description', 'copr-my-extension' ),
			'$formAction'		=>	admin_url( 'admin-ajax.php' ),
			'$formError'		=>	__( 'Sorry, we were unable to add the group. Please try again later.', 'copr-my-extension' ),
			'$hasBP'			=>	$hasBP,
			'$hiddenChecked'	=>	$hiddenChecked,
			'$name'				=>	$name,
			'$nameErrorClasses'	=> 	$nameErrorClasses,
			'$nameError'		=>	$nameError,
			'$nameInputClasses'	=>	$nameInputClasses,
			'$nameLabel'		=>	__( 'Group Name','copr-my-extension' ),
			'$nounce'			=>	wp_nonce_field( 'add_new_group' ),
			'$optionHidden'		=>  __( 'Hidden','copr-my-extension' ),
			'$optionPublic'		=>	__( 'Public','copr-my-extension' ),
			'$optionPrivate'	=>	__( 'Private','copr-my-extension' ),
			'$privateChecked'	=>	$privateChecked,
			'$publicChecked'	=>	$publicChecked,
			'$save'				=>	__( 'Save', 'copr-my-extension' ),
			'$saving'			=>	__( 'Saving', 'copr-my-extension' ),
			'$selector'			=>	$selector,
			'$title'			=>	esc_html( $this->props['gs_title'] ),
			'$typeLabel'		=>	__( 'Group Type','copr-my-extension' ),
		);
		return strtr($this->groupSelectorTemplate, $vars);
	}

	/**
	 * Get the questions content
	 *
	 * @return string 	The html for all the questions
	 */
	protected function getQuestionsContent()
	{
		$lines = explode("<br />", $this->props['questions']);
		$html = '';
		$tag = strtolower(esc_html($this->props['tag']));
		$url = esc_html($this->props['url']);
		$answers = $this->getUserAnswers($tag);
		$title = esc_html($this->props['title']);
		$nextLabel = esc_html__('Next', 'copr-my-extension');
		$prevLabel = esc_html__('Previous', 'copr-my-extension');
		foreach ($lines as $key => $line) {
			$pieces = explode('|', $line);
			if (count($pieces) < 2) {
				/**
				 * Missing details
				 */
				continue;
			}
			$question = trim($pieces[0]);
			$hash = md5($question);
			$answer = (isset($answers[$hash])) ? $answers[$hash] : '';
			$formElement = '';
			$questionType = 'text';
			$questionChoices = '';
			if (strtolower($pieces[1]) === 'text') {
				$formElement = '<textarea name="answer" class="copr-answer-textarea copr-question-textarea" rows="10" data-question-hash="' . $hash . '">' . $answer . '</textarea>';
			} else if (strtolower($pieces[1]) === 'choice') {
				if (count($pieces) < 3) {
					/**
					 * Missing choices
					 */
					continue;
				}
				$questionType = 'choice';
				$questionChoices = $pieces[2];
				$formElement = '<div class="copr-answer-choices" data-question-hash="' . $hash . '">';
				$choices = explode(',', $questionChoices);
				foreach ($choices as $choiceKey => $choice) {
					$checked = '';
					if ((strtolower($choice) === strtolower($answer)) || (($answer === '') && ($choiceKey === 0))) {
						$checked = ' checked';
					}
					$formElement .= '<div><input type="radio" name="answer" value="' . $choice .'"' . $checked . ' /><label>' . $choice .'</label></div>';
				}
				$formElement .= '</div>';
			}
			$questionNumber = $key + 1;
			$questionLabel = 'question-' . $tag . '-' . $questionNumber;
			$wrapperClasses = '';
			if ($key !== 0) {
				$wrapperClasses = ' copr-hidden';
			}
			$vars = array(
				'$formAction'		=>	admin_url('admin-ajax.php'),
				'$formElement'		=>	$formElement,
				'$formError'		=>	__( 'Sorry, we were unable to save your answer. Please try again later.', 'copr-my-extension' ),
				'$groupId'			=>	$this->getCurrentGroupId(),
				'$nextLabel'		=>	$nextLabel,
				'$nounce'			=>	wp_nonce_field('submit_answers'),
				'$prevLabel'		=>	$prevLabel,
				'$question'			=>	$question,
				'$questionChoices'	=>	$questionChoices,
				'$questionNumber'	=>	$questionNumber,
				'$questionType'		=>	$questionType,
				'$save'				=>	esc_html__('Save', 'copr-my-extension'),
				'$saving'			=>	esc_html__('Saving', 'copr-my-extension'),
				'$tag'				=>	$tag,
				'$title'			=>	$title,
				'$uniqueId'			=>	$questionLabel,
				'$url'				=>	$url,
				'$wrapperClasses'	=>	$wrapperClasses,
			);
			$html .= strtr($this->formTemplate, $vars);
		}
		return $html;
	}

	/**
	 * Get the user's answers.
	 *
	 * @param  string 	$tag 	The section tag
	 * @return array      		An array whose key is the question.unique_hash and value is the answer
	 */
	protected function getUserAnswers($tag)
	{
		global $wpdb;
		$userId = get_current_user_id();
		$selectedGroup = $this->getCurrentGroupId();
		if ($selectedGroup === -1) {
			return [];
		}
		$repo =  new AnswerRepository($wpdb, $wpdb->prefix);
		$results = $repo->findAllBySectionTag($tag, $selectedGroup, $userId);
		$answers = [];
		foreach ($results as $result) {
			$answers[$result->unique_hash] = $result->answer;
		}
		return $answers;
	}
}

new COPR_SEQuestionForm;
