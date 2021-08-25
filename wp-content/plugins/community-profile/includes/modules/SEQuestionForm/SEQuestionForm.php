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
	 * Initialize the module.
	 *
	 * @return void
	 */
	public function init() {
		$this->name = esc_html__( 'SE Question Form', 'copr-community-profile' );
	}

	/**
	 * Get the fields for the Divi builder dialog
	 *
	 * @return array 	The fields for this module
	 */
	public function get_fields() {
		return array(
			'title'	=>	array(
				'label'				=>	esc_html__('Section Title', 'myex-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('The title for this section in the condition.', 'myex-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'tag'	=>	array(
				'label'				=>	esc_html__('The Curriculum Tag (ie. C1-A)', 'myex-my-extension'),
				'type'				=>	'text',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('The tag for this section.', 'myex-my-extension'),
				'toggle_slug'		=>	'main_content',
			),
			'questions'	=>	array(
				'label'				=>	esc_html__('The Questions', 'myex-my-extension'),
				'type'				=>	'textarea',
				'option_category'	=>	'basic_option',
				'description'		=>	esc_html__('Hit return after each question.', 'myex-my-extension'),
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
		return sprintf( '<h1>%1$s</h1>', $this->props['content'] );
	}
}

new COPR_SEQuestionForm;
