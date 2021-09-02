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
				<div class="copr-form-error"></div>
				<form action="$formAction" method="post" data-error-message="$formError">
					<input type="hidden" name="action" value="copr_save_answer" />
					<input type="hidden" name="section_tag" value="$tag" />
					<input type="hidden" name="section_title" value="$title" />
					<input type="hidden" name="section_url" value="$url" />
					<input type="hidden" name="question_number" value="$questionNumber" />
					<input type="hidden" name="question_type" value="$questionType" />
					<input type="hidden" name="question_choices" value="$questionChoices" />
					<input type="hidden" name="question" value="$question" />
					<input type="hidden" name="group_id" value="11" />
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
		return '<div class="copr-questions-wrapper">' . $this->getQuestionsContent() . '</div>';
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
				$formElement = '<textarea name="answer" class="copr-answer-textarea" rows="10">' . $answer . '</textarea>';
			} else if (strtolower($pieces[1]) === 'choice') {
				if (count($pieces) < 3) {
					/**
					 * Missing choices
					 */
					continue;
				}
				$questionType = 'choice';
				$questionChoices = $pieces[2];
				$formElement = '<div class="copr-answer-choices">';
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
		$repo =  new AnswerRepository($wpdb, $wpdb->prefix);
		$results = $repo->findAllBySectionTag($tag, 11, $userId);
		$answers = [];
		foreach ($results as $result) {
			$answers[$result->unique_hash] = $result->answer;
		}
		return $answers;
	}
}

new COPR_SEQuestionForm;
