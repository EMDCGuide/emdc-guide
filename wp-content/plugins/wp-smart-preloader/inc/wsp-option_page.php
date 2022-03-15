<?php
/**
 *  WP_smart_preloader  a php class for creating option page with sub menu
 */
class WP_smart_preloader{
	/**
    * Holds the values to be used in the fields callbacks
    */
	private $options;

	/**
	* [__construct class contructer]
	*/
	
	public function __construct() {
		 add_action( 'admin_menu', array( $this, 'wsp_add_submenu_option' ) );
		 add_action( 'admin_init', array( $this, 'wsp_register_settings' ) );

		 add_filter('wp_enqueue_scripts',array($this,'wsp_enqueue_styles'),0);
		 add_action('admin_enqueue_scripts',array($this,'wsp_enqueue_styles'));

		 if( !is_admin() ) {		 	
		 	add_action( 'wp_footer', array($this,'wsp_custom_css_front'),0);
		 }

		add_filter( 'body_class', array($this,'wsp_add_class_body') );
		 
	}

	public function wsp_enqueue_styles($hook_suffix){

        if( is_admin() ){           
        	if('settings_page_wsp-loader' == $hook_suffix ){
	            wp_register_script('wsp-admin-editor', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js', '', SMART_PRELOADER_VERSION );
				wp_enqueue_script( 'wsp-admin-editor' );
				
	            wp_register_script( 'wsp-admin-script',SMART_PRELOADER_URL.'assets/js/wsp-admin-script.js' , array('jquery','wsp-admin-editor'), SMART_PRELOADER_VERSION );
	            wp_enqueue_script( 'wsp-admin-script' );
        	}

            wp_register_style( 'wsp-admin-style', SMART_PRELOADER_URL.'assets/css/wsp-admin-preloader.css', false, SMART_PRELOADER_VERSION );
            wp_enqueue_style( 'wsp-admin-style' );

        } else{

            wp_register_style( 'wsp-main-style', SMART_PRELOADER_URL.'assets/css/wsp-front-preloader.css', false, SMART_PRELOADER_VERSION );
            wp_enqueue_style( 'wsp-main-style' );

        	/*if(!wp_script_is('jquery','enqueued')){
        		wp_enqueue_script( 'jquery' );
        	}    */   

            wp_register_script( 'wsp-main-script',SMART_PRELOADER_URL.'assets/js/wsp-main-script.js' , array('jquery'), SMART_PRELOADER_VERSION );
            wp_enqueue_script( 'wsp-main-script');

            // Localize the script with new data
            $localized_array = $this->wsp_localized_script();
            wp_localize_script( 'wsp-main-script', 'wsp_obj', $localized_array );

        }
        
        wp_register_style( 'wsp-style', SMART_PRELOADER_URL.'assets/css/wsp-smart-preload-style.css', false, SMART_PRELOADER_VERSION );
        wp_enqueue_style( 'wsp-style' );
	}

	/**
	 * [wsp_add_class_body description]
	 * @param  [array] $classes [list of body classes]
	 * @return [array]          [list of body classes]
	 */
	public function wsp_add_class_body($classes){		
		$wsp = get_option( 'wsp-loader-opt' );
		if( isset($wsp['homepage']) && $wsp['homepage'] == "1"){
			if( is_home() || is_front_page() ) {
				$classes[] = 'wp-smart-body';
			}
		} else {
			$classes[] = 'wp-smart-body';
		}
		return $classes;
	}

	public function wsp_localized_script(){
		$wsp = get_option( 'wsp-loader-opt' );
		return $wsp;
	}

	public function wsp_custom_css_front(){
		$wsp = get_option( 'wsp-loader-opt' );
		$style = '<style type="text/css" media="all">';
		$style .= $wsp['custom_css'];
		$style .= '</style>';
		echo $style;
	}

	public function wsp_add_submenu_option(){
		add_submenu_page( 
						'options-general.php', // parent slug
						'WP Smart Preloader', // page title
						'WP Smart Preloader', // menu title
						'manage_options', // capability
						'wsp-loader', // menu slug
						array($this,'wsp_submenu_callback_fn') // Callback
						);
	}

	/**
	 * [wsp_submenu_callback_fn callback function of option page]
	 */
	public function wsp_submenu_callback_fn(){
		// Set class property
	      $this->options = get_option( 'wsp-loader-opt' );
	     
	      ?>
	      <div class="wrap">	              
	          <form method="post" action="options.php">
	          <?php
	              // This prints out all hidden setting fields
	              settings_fields( 'wsp_loader' ); //option group

	              do_settings_sections( 'wsp-loader' ); // option name

	              submit_button(); 
	          ?>
	          </form>
	      </div>
      <?php
	}

	/**
	 * [wsp_register_settings register setting fields ]
	 */
	public function wsp_register_settings(){
			// Register the settings with Validation callback
			   	register_setting( 
							    'wsp_loader', // option group
							    'wsp-loader-opt', // option name
						    	array($this,'wsp_sanitize') // sanitize 
							);
			   	// general info above
			   	add_settings_section(
			             'wsp_loader_id', // ID
			             'WP Smart Preloader', // Title
			             array( $this, 'wsp_section_info' ), // Callback			             
			             'wsp-loader' // Page (same as submenu page menu_slug)
			         	);  
			   	// adding fields
			   	add_settings_field(
			   	           	'wsp-loader-select', // ID
			   	           	'Select Preloader', // Title 
			   	           	array( $this, 'wsp_preloader_list' ), // Callback
			   	          	'wsp-loader', // Page (same as submenu page menu_slug)
			   	           	'wsp_loader_id' // Section id
			   	       	);
			   	       	
   	       	  add_settings_field(
		   	           	'wsp-loader-home-page', // ID
		   	           	'Show only on Home Page',  // Title
		   	           	array( $this, 'wsp_preloader_homepage' ),  //callback
		   	           	'wsp-loader', // Page (same as submenu page menu_slug)
		   	           	'wsp_loader_id' // Section id
		   	       	);
		   	       	
			   	// custom animation       	
	   	       	add_settings_field(
	   	       			'wsp_loader_custom-animation', // ID
	   	       			'Custom Animation', // Title
	   	       			array($this,'wsp_custom_animation'), // Callback
	   	       			'wsp-loader', // Page (same as submenu page menu_slug)
		   	           	'wsp_loader_id' // Section id
	   	       		);	

	   	       	add_settings_field(
	   	       			'wsp_loader_custom-css', // ID
	   	       			'Custom CSS', // Title
	   	       			array($this,'wsp_loader_custom_css'), // Callback
	   	       			'wsp-loader', // Page (same as submenu page menu_slug)
		   	           	'wsp_loader_id' // Section id
	   	       		);	

	   	       	add_settings_field(
	   	       			'wsp_loader_duration', // ID
	   	       			'Duration to show Loader', // Title
	   	       			array($this,'wsp_loader_delay'), // Callback
	   	       			'wsp-loader', // Page (same as submenu page menu_slug)
		   	           	'wsp_loader_id' // Section id
	   	       		);

	   	       	add_settings_field(
	   	       			'wsp_loader_fadeout', // ID
	   	       			'Loader to Fade Out', // Title
	   	       			array($this,'wsp_loader_fadeOut'), // Callback
	   	       			'wsp-loader', // Page (same as submenu page menu_slug)
		   	           	'wsp_loader_id' // Section id
	   	       		);
	}

	/**
	 * setting functions
	 */
	public function wsp_preloader_list(){
		// wsp-loader-opt

		$loader = array('Loader 1','Loader 2','Loader 3','Loader 4','Loader 5','Loader 6','Custom Animation');

		$select  = "<select id='loader-img' name='wsp-loader-opt[loader]'>";
		$select .= "<option value=''>Select Loader</option>";
		foreach($loader as $load){
			$sel = ( isset( $this->options['loader'] ) && $this->options['loader'] == $load ) ? 'selected="selected"' : '';
			$select .= '<option value="'.$load.'" '.$sel.'>'.$load.'</option>';
		}
		$select .= "</select><div class='wsp-loader-block'><div class='wsp-loader-table'><div id='loader-preview'> <span>Loading...</span> </div></div></div>";
		_e($select,"wp-smart-preloader");
	}

	/**
	 * setting function for home page
	 */
	public function wsp_preloader_homepage(){
		printf(__('<input type="checkbox" name="%s" value="1" %s />','wp-smart-preloader'),"wsp-loader-opt[homepage]",(isset($this->options['homepage']) && $this->options['homepage'] == "1")?'checked="checked"':'' );
	}

	/**
	 * setting function fadeout
	 */ 
	public function wsp_loader_fadeOut(){
		printf(__('<input type="text" name="%s" value="%s"  /><div>%s</div>','wp-smart-preloader'),"wsp-loader-opt[fadeout]",(isset($this->options['fadeout']) )?$this->options['fadeout']:'',__('Default:2500<br />1 second => 1000',"wp-smart-preloader") );
	}

	/**
	 * setting function delay
	 */ 
	public function wsp_loader_delay(){
		printf(__('<input type="text" name="%s" value="%s"  /><div>%s</div>','wp-smart-preloader'),"wsp-loader-opt[delay]",(isset($this->options['delay']) )?$this->options['delay']:'',__('Default:1500<br />1 second => 1000',"wp-smart-preloader") );
	}

	/**
	 * setting function custom css
	 */ 
	public function wsp_loader_custom_css(){
		printf("Want to change Look and feel. Add your desired css here :)","wp-smart-preloader");
		echo "<br />";
		printf("Or add CSS for your Custom html5 animation :)","wp-smart-preloader");
        echo "<br/><br/>";
		printf(__('<textarea rows="12" cols="70" name="%s">%s</textarea><div class="editor" id="%s"></div>','wp-smart-preloader'),"wsp-loader-opt[custom_css]",isset($this->options['custom_css'])? esc_attr( $this->options['custom_css'] ):'',"wsp-loader-opt[custom_css]" );
	}
	
	public function wsp_custom_animation(){
		printf("Want to add you own custom html5 animation? Enter your html code here.","wp-smart-preloader");
        echo "<br/><br/>";
		printf(__('<textarea rows="12" cols="70" name="%s">%s</textarea><div class="editor" id="%s"></div>','wp-smart-preloader'),"wsp-loader-opt[custom_animation]",isset($this->options['custom_animation'])? esc_attr( $this->options['custom_animation'] ):'',"wsp-loader-opt[custom_animation]" );
	
	}

	/**
	 * [wsp_sanitize sanitize function for setting]
	 */
	public function wsp_sanitize( $input ){
	 	foreach($input as $k => $v) {
		   $newinput[$k] = trim($v);
		   
		   	// Check the input is a letter or a number
		   	if(!preg_match('/^[A-Z0-9 _]*$/i', $v)) {
		    	$newinput[$k] = '';
		   	}
	 	}
		return $input;
	}

	/**
	 * [wsp_section_info function for section info]
	 * @return [echo] [display info]
	 */
	public function wsp_section_info(){
		_e('WP Smart Preloader Setting:','wp-smart-preloader');
	}


	/**
	 * [wsp_uninstall to delete options after uninstall]	 
	 */
	public function wsp_uninstall(){
		delete_option( 'wsp-loader-opt' );
	}


}



// if( is_admin() ){
    $wp_smart_preloader = new WP_smart_preloader();
// }