<?php

class LWP_DiviBreadcrumbs extends ET_Builder_Module {

	public $slug       = 'lwp_divi_breadcrumbs';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'http://www.learnhowwp.com/divi-breadcrumbs-module',
		'author'     => 'learnhowwp.com',
		'author_uri' => 'http://www.learnhowwp.com/',
	);

	public function init() {
		$this->name = esc_html__( 'Breadcrumbs', 'lwp-divi-breadcrumbs' );
		$this->icon = '=';
		$this->main_css_element = '%%order_class%%';			
	}

	public function get_fields() {

		$et_accent_color = et_builder_accent_color();			

		return array(			
			'home_text' => array(
				'default'         => esc_html__( 'Home', 'lwp-divi-breadcrumbs' ),
				'label'           => esc_html__( 'Home Text', 'lwp-divi-breadcrumbs' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The default Home text in the Breadcrumbs', 'lwp-divi-breadcrumbs' ),
				'toggle_slug'     => 'main_content',
			),
			'before_text' => array(
				'label'           => esc_html__( 'Before Text', 'lwp-divi-breadcrumbs' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The text before the Breadcrumbs', 'lwp-divi-breadcrumbs' ),
				'toggle_slug'     => 'main_content',
			),
			'font_icon' => array(
				'label'               => esc_html__( 'Separator Icon', 'lwp-divi-breadcrumbs' ),
				'type'                => 'et_font_icon_select',
				'renderer'            => 'et_pb_get_font_icon_list',
				'option_category'     => 'basic_option',
				'class'               => array( 'et-pb-font-icon' ),
				'toggle_slug'         => 'icon',
				'description'         => esc_html__( 'Choose the icon for the separator.', 'lwp-divi-breadcrumbs' ),
			),
			'use_before_icon' => array(
				'label'           => esc_html__( 'Add Before Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'         => 'icon',					
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
			),
			'before_icon' => array(
				'label'               => esc_html__( 'Separator Icon', 'lwp-divi-breadcrumbs' ),
				'type'                => 'et_font_icon_select',
				'renderer'            => 'et_pb_get_font_icon_list',
				'option_category'     => 'basic_option',
				'class'               => array( 'et-pb-font-icon' ),
				'toggle_slug'         => 'icon',
				'description'         => esc_html__( 'Choose the icon for the separator.', 'lwp-divi-breadcrumbs' ),
				'show_if'         => array(
					'use_before_icon' => 'on',
				),				
			),																
			'link_color' => array(
				'label'             => esc_html__('Link Color', 'lwp-divi-breadcrumbs' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for the links in the breadcrumbs.', 'lwp-divi-breadcrumbs' ),
				'toggle_slug'       => 'breadcrumbs',
				'hover'             => 'tabs',
				'tab_slug'        => 'advanced',
			),
			'separator_color' => array(
				'label'             => esc_html__('Separator Color', 'lwp-divi-breadcrumbs' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for the Separator in the breadcrumbs.', 'lwp-divi-breadcrumbs' ),
				'toggle_slug'       => 'breadcrumbs',
				'tab_slug'        => 'advanced',
			),
			'current_text_color' => array(
				'label'             => esc_html__('Current Text Color', 'lwp-divi-breadcrumbs' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for the Current Text in the breadcrumbs.', 'lwp-divi-breadcrumbs' ),
				'toggle_slug'       => 'breadcrumbs',
				'tab_slug'        => 'advanced',
			),															
		);
	}

	public function get_settings_modal_toggles() {
		return array(
		  'advanced' => array(
			'toggles' => array(
			  'breadcrumbs' => array(
				'priority' => 24,
				'title' => 'Breadcrumbs Styles',
			  ),
			),
		  ),
		);
	  }	

	public function render( $attrs, $content = null, $render_slug ) {

		$home_text			= esc_html($this->props['home_text']);
		$before_text		= esc_html($this->props['before_text']);
		$link_color			= $this->props['link_color'];
		$separator_color	= $this->props['separator_color'];
		$current_text_color	= $this->props['current_text_color'];
		$font_icon			= $this->props['font_icon'];
		$link_color_hover 	= $this->get_hover_value( 'link_color' );
		$before_icon			= $this->props['before_icon'];
		
		$before_html='';

		if($font_icon=='')	//If an Icon is not set then use the default icon
			$font_icon='%%24%%';
		
		$separator_icon=esc_attr( et_pb_process_font_icon($font_icon));	//Processing the Breacrumbs Icon

		if($before_icon!=''){
			$before_icon=esc_attr( et_pb_process_font_icon($before_icon));	//Processing the Before Icon
			$before_html=sprintf('<span class="before-icon et-pb-icon">%1$s</span>',$before_icon);
		}

		$breadcrumbs = lwp_get_hansel_and_gretel_breadcrumbs($home_text,$before_text,$separator_icon); //Generating the Breadcrumbs

		if($current_text_color!=''){
			$current_text_color='color:'.$current_text_color.';';
			
			//Styles for current text
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .lwp-breadcrumbs .current',
				'declaration' => sprintf('%1$s',esc_attr($current_text_color)),
			) );		
		}

		if($link_color!=''){
			$link_color='color:'.$link_color.';';
			
			//Styles for link
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .lwp-breadcrumbs a',
				'declaration' => sprintf('%1$s',esc_attr($link_color)),
			) );		
		}
		
		if($separator_color!=''){
			$separator_color='color:'.$separator_color.';';			
			//Styles for separator
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .lwp-breadcrumbs .separator',
				'declaration' => sprintf('%1$s',esc_attr($separator_color)),
			) );		
		}

		if($link_color_hover!=''){		
			$link_color_hover='color:'.$link_color_hover.';';
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .lwp-breadcrumbs a:hover',
				'declaration' => sprintf('%1$s',esc_attr($link_color_hover)),
			) );
		}		
		

		return sprintf( 
		'<div class="lwp-breadcrumbs">%2$s %1$s</div>'
		,$breadcrumbs,$before_html);
	}
}

new LWP_DiviBreadcrumbs;
