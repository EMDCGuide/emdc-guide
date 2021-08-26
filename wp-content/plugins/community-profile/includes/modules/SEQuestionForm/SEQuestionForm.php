<?php
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
				<form action="$formAction" method="post">
					<input type="hidden" name="action" value="copr_save_answer" />
					<input type="hidden" name="tag" value="$tag" />
					<input type="hidden" name="question_number" value="$questionNumber" />
					<input type="hidden" name="question" value="$question" />
					$nounce
					<div class="form-element-wrapper">
						<label>$questionNumber) $question</label>
						$formElement
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
	public function init() {
		$this->name = esc_html__( 'SE Question Form', 'copr-community-profile' );
	}

	/**
	 * Hide specific advanced fields
	 *
	 * @return array 	The fields to hide
	 */
	public function get_advanced_fields_config() {
		return array();
	}

	/**
	 * Get the fields for the Divi builder dialog
	 *
	 * @return array 	The fields for this module
	 */
	public function get_fields() {
		return array(
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
	public function render( $attrs, $content = null, $render_slug ) {
		$lines = explode("<br />", $this->props['questions']);
		$html = '';
		$tag = strtolower(esc_html($this->props['tag']));
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
			$formElement = '';
			if (strtolower($pieces[1]) === 'text') {
				$formElement = '<textarea name="answer" class="copr-answer-textarea" rows="10"></textarea>';
			} else if (strtolower($pieces[1]) === 'choice') {
				if (count($pieces) < 3) {
					/**
					 * Missing choices
					 */
					continue;
				}
				$formElement = '<div class="copr-answer-choices">';
				$choices = explode(',', $pieces[2]);
				foreach ($choices as $choiceKey => $choice) {
					$checked = '';
					if ($choiceKey == 0) {
						$checked = ' checked';
					}
					$formElement .= '<div><input type="radio" name="answer" value="' . $choice .'"' . $checked . ' /><label>' . $choice .'</label></div>';
				}
				$formElement .= '</div>';
			}
			$question = $pieces[0];
			$questionNumber = $key + 1;
			$questionLabel = 'question-' . $tag . '-' . $questionNumber;
			$wrapperClasses = '';
			if ($key !== 0) {
				$wrapperClasses = ' copr-hidden';
			}
			$vars = array(
				'$formAction'		=>	admin_url('admin-ajax.php'),
				'$formElement'		=>	$formElement,
				'$nextLabel'		=>	$nextLabel,
				'$nounce'			=>	wp_nonce_field('submit_answers'),
				'$prevLabel'		=>	$prevLabel,
				'$question'			=>	$question,
				'$questionNumber'	=>	$questionNumber,
				'$save'				=>	esc_html__('Save', 'copr-my-extension'),
				'$saving'			=>	esc_html__('Saving', 'copr-my-extension'),
				'$tag'				=>	$tag,
				'$uniqueId'			=>	$questionLabel,
				'$wrapperClasses'	=>	$wrapperClasses,
			);
			$html .= strtr($this->formTemplate, $vars);
		}
		return '<div class="copr-questions-wrapper">' . $html . '</div>';
	}
}

new COPR_SEQuestionForm;
