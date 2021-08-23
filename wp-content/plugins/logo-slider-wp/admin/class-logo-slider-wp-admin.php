<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin
 * @author     LogicHunt <info.logichunt@gmail.com>
 */
class Logo_Slider_WP_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * @var Lgx_Carousel_Settings_API
     */
    private $settings_api;

    /**
     * The plugin plugin_base_file of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string plugin_base_file The plugin plugin_base_file of the plugin.
     */
    protected $plugin_base_file;


    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */

    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */

    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings_api = new Lgx_Carousel_Settings_API($plugin_name, $version);


        $this->plugin_base_file = plugin_basename(plugin_dir_path(__FILE__).'../' . $this->plugin_name . '.php');

    }





    /**
     * Declare Custom Post Type For Carousal
     * @since 1.0.0
     */

    public function logosliderwp_initialize() {

        //custom post type labels
        $labels_logosliderwp = array(
            'name'               => _x('Logos', 'Logo Slider', 'logo-slider-wp'),
            'singular_name'      => _x('Logo Item', 'Slider Items', 'logo-slider-wp'),
            'menu_name'          => __('Logo Slider', 'logo-slider-wp'),
            'all_items'          => __('All Logos', 'logo-slider-wp'),
            'view_item'          => __('View Item', 'logo-slider-wp'),
            'add_new_item'       => __('Add New Logo', 'logo-slider-wp'),
            'add_new'            => __('Add New Logo', 'logo-slider-wp'),
            'edit_item'          => __('Edit Item', 'logo-slider-wp'),
            'update_item'        => __('Update Item', 'logo-slider-wp'),
            'search_items'       => __('Search Logo', 'logo-slider-wp'),
            'not_found'          => __('No Logo items found', 'logo-slider-wp'),
            'not_found_in_trash' => __('No Logo items found in trash', 'logo-slider-wp')
        );

        $args_logosliderwp   = array(
            'label'               => __('Logo Slider', 'logo-slider-wp'),
            'description'         => __('Logo Slider WP Post Type', 'logo-slider-wp'),
            'labels'              => $labels_logosliderwp,
            'supports'            => array( 'title', 'thumbnail'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20, // OLD 5
            'menu_icon'   			=> 'dashicons-images-alt',
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );


        //declare custom post type logosliderwp
        register_post_type( 'logosliderwp', $args_logosliderwp);


        // Register Taxonomy
        $logosliderwp_cat_args = array(
            'hierarchical'   => true,
            'label'          => __('Categories', 'logo-slider-wp'),
            'show_ui'        => true,
            'query_var'      => true,
            'show_admin_column' => true,
            'singular_label' => __('Logo Category', 'logo-slider-wp'),
        );
        register_taxonomy('logosliderwpcat', array('logosliderwp'), $logosliderwp_cat_args);
    }


    /**
     * Add metabox for custom post type
     *
     * @since    1.0.0
     */
    public function add_meta_boxes_metabox() {

        //portfoliopro meta box
        add_meta_box(
            'v_logo_metabox_logosliderwp', __( 'Company Information', $this->plugin_name ), array(
            $this,
            'metabox_logosliderwp_display'
        ), 'logosliderwp', 'normal', 'high'
        );

    }


    /**
     * Render Metabox under logosliderwp
     *
     * logosliderwp meta field
     *
     * @param $post
     *
     * @since 1.0
     *
     */

    public function metabox_logosliderwp_display( $post ) {

        $fieldValues = get_post_meta( $post->ID, '_logosliderwpmeta', true );

        wp_nonce_field( 'metaboxlogosliderwp', 'metaboxlogosliderwp[nonce]' );

        echo '<div id="logosliderwp_metabox_wrapper">';

        $company_url        = isset( $fieldValues['company_url'] ) ? $fieldValues['company_url'] : '';
        $company_name       = isset( $fieldValues['company_name'] ) ? $fieldValues['company_name'] : '';
        $company_desc        = isset( $fieldValues['company_desc'] ) ? $fieldValues['company_desc'] : '';
        ?>


        <table class="form-table">
            <tbody>

            <?php do_action( 'logosliderwp_meta_fields_before_start', $fieldValues ); ?>

            <tr valign="top">
                <td><?php _e( 'Brand Name', $this->plugin_name ) ?></td>
                <td>
                    <input type="text" name="metaboxlogosliderwp[company_name]" value='<?php echo $company_name; ?>'/>
                </td>
            </tr>

            <tr valign="top">
                <td><?php _e( 'Brand URL', $this->plugin_name ) ?></td>
                <td>
                    <input type="url" name="metaboxlogosliderwp[company_url]" value='<?php echo $company_url; ?>'/>
                </td>
            </tr>

            <tr valign="top">
                <td><?php _e( 'Brand Description', $this->plugin_name ) ?></td>
                <td>
                    <textarea rows="5" cols="45"  name="metaboxlogosliderwp[company_desc]" placeholder="Brand description"><?php echo $company_desc; ?></textarea>
                </td>
            </tr>

            <?php
            //allow others to show more custom fields at end
            do_action( 'logosliderwp_meta_fields_after_start', $fieldValues );
            ?>

            </tbody>
        </table>

        <?php
        echo '</div>';


    }


    /**
     * Determines whether or not the current user has the ability to save meta data associated with this post.
     *
     * Save portfoliopro Meta Field
     *
     * @param        int $post_id //The ID of the post being save
     * @param         bool //Whether or not the user has the ability to save this post.
     */
    public function save_post_metabox_logosliderwp( $post_id, $post ) {

        $post_type = 'logosliderwp';

        // If this isn't a 'book' post, don't update it.
        if ( $post_type != $post->post_type ) {
            return;
        }

        if ( ! empty( $_POST['metaboxlogosliderwp'] ) ) {

            $postData = $_POST['metaboxlogosliderwp'];

            $saveableData = array();

            if ( $this->user_can_save( $post_id, 'metaboxlogosliderwp', $postData['nonce'] ) ) {

                $saveableData['company_url']  = esc_url( $postData['company_url'] );
                $saveableData['company_name']  = sanitize_text_field( $postData['company_name'] );
                $saveableData['company_desc']  = sanitize_textarea_field( $postData['company_desc'] );

                update_post_meta( $post_id, '_logosliderwpmeta', $saveableData );
            }
        }
    }// End  Meta Save


    /**
     * Determines whether or not the current user has the ability to save meta data associated with this post.
     *
     * user_can_save
     *
     * @param        int $post_id // The ID of the post being save
     * @param        bool /Whether or not the user has the ability to save this post.
     *
     * @since 1.0
     */
    public function user_can_save( $post_id, $action, $nonce ) {

        $is_autosave    = wp_is_post_autosave( $post_id );
        $is_revision    = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $nonce ) && wp_verify_nonce( $nonce, $action ) );

        // Return true if the user is able to save; otherwise, false.
        return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;

    }




    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        $this->plugin_screen_hook_suffix  = add_submenu_page('edit.php?post_type=logosliderwp', __('Logo Slider Default Settings', 'logo-slider-wp'), __('Default Settings', 'logo-slider-wp'), 'manage_options', 'logosliderwpsettings', array($this, 'display_plugin_admin_settings'));
        $this->plugin_screen_hook_suffix  = add_submenu_page('edit.php?post_type=logosliderwp', __('Usage & Help', 'logo-slider-wp'), __('Usage & Help', 'logo-slider-wp'), 'manage_options', 'logosliderwphelpuage', array($this, 'display_plugin_admin_usage_help'));

    }

    /**
     * Change Feature iamge input Position
     */
    public  function logo_slider_wp_img_box(){
        remove_meta_box( 'postimagediv', 'logosliderwp', 'side' );
        add_meta_box('postimagediv', __('Brand Logo'), 'post_thumbnail_meta_box', 'logosliderwp', 'normal', 'high');
    }



    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_links_admin_plugin_page_title( $links ) {
        return array_merge( array(
            'settings' => '<a style="color:#00a500; font-weight: bold;" href="' . admin_url( 'edit.php?post_type=logosliderwp&page=logosliderwpsettings' ) . '">' . esc_html__( 'Settings', 'logo-slider-wp') . '</a>',
            // 'demo' => '<a href="' .esc_url('http://logichunt.net/wpdemo/wordpress-logo-slider/') . '" target="_blank">' . esc_html__( 'Demo', 'logo-slider-wp') . '</a>',
            //'support' => '<a style="color:#ff4b39; font-weight: bold;" target="_blank" href="' .esc_url('https://logichunt.com/support/') . '" target="_blank">' . esc_html__( 'Get Support', 'logo-slider-wp') . '</a>',
            'usage' => '<a style="color:#ff4b39; font-weight: bold;"  href="' . admin_url( 'edit.php?post_type=logosliderwp&page=logosliderwphelpuage' ) . '">' . esc_html__( 'Usage', 'logo-slider-wp') . '</a>',
        ), $links );

    }//end plugin_listing_setting_link



    /**
     * Add support link to plugin description in /wp-admin/plugins.php
     *
     * @param  array  $plugin_meta
     * @param  string $plugin_file
     *
     * @return array
     */
    public function logo_slider_wp_support_link($plugin_meta, $plugin_file) {

        if ($this->plugin_base_file == $plugin_file) {
            $plugin_meta[] = sprintf(
                '<a href="%s">%s</a>', 'https://logichunt.com/support/', __('Get Support', 'logo-slider-wp')
            );
        }

        return $plugin_meta;
    }



    function display_plugin_admin_usage_help() {
        global $wpdb;

        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . '/../' . $this->plugin_base_file);

        include('partials/admin-usage-help.php');
    }



    public function display_plugin_admin_settings() {
        /*	$test = $this->settings_api->get_option('logosliderwp_settings_cat', 'logosliderwp_config', 'test');
            var_dump($test);*/

        global $wpdb;

        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . '/../' . $this->plugin_base_file);

        include('partials/admin-settings-display.php');
    }

    /**
     * Settings init
     */
    public function logo_slider_wp_setting_init() {
        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();

        //$role = get_role('administrator');
    }




    /**
     * Ensure post thumbnail support is turned on.
     */
    public function add_thumbnail_support_logo_slider() {
        if ( ! current_theme_supports( 'post-thumbnails' ) ) {
            add_theme_support( 'post-thumbnails' );
        }
        add_post_type_support( 'logosliderwp', 'thumbnail' );
    }


    /**
     * Setings Sections
     * @return array|mixed|void
     */

    public function get_settings_sections() {

        $sections = array(
            array(
                'id'    => 'logosliderwp_basic',
                'title' => __('Basic Settings', 'logo-slider-wp'),
            ),
            array(
                'id'    => 'logosliderwp_style',
                'title' => __('Style Settings', 'logosliderwp-domain'),
            ),

            array(
                'id'    => 'logosliderwp_responsive',
                'title' => __('Responsive Control', 'logo-slider-wp'),
            ),
            array(
                'id'    => 'logosliderwp_config',
                'title' => __('Slider Options', 'logo-slider-wp'),
            ),

            array(
                'id'    => 'logosliderwp_adv',
                'title' => __('Advanced Settings', 'logo-slider-wp'),
            ),


        );

        $sections = apply_filters('logo_slider_settings_sections', $sections);

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public  function get_settings_fields() {


        $settings_fields = array(

            'logosliderwp_basic' => array(


                array(
                    'name'     => 'logosliderwp_settings_show_company',
                    'label'         => __('Show Brand Name', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'default'  => 'no',
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),


                array(
                    'name'     => 'logosliderwp_settings_show_company_desc',
                    'label'         => __('Show Brand Description', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'default'  => 'no',
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),


                array(
                    'name'     => 'logosliderwp_settings_height',
                    'label'    => __('Logo Height(px)', 'logo-slider-wp'),
                    'desc'     => __('Set Maximum Logo Height in px', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '350',
                    'desc_tip' => true,
                ),


                array(
                    'name'     => 'logosliderwp_settings_width',
                    'label'    => __('Logo Width(px)', 'logo-slider-wp'),
                    'desc'     => __('Set Maximum Logo Width in px', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '350',
                    'desc_tip' => true,
                )


            ),// Single


            'logosliderwp_adv' => array(

                array(
                    'name'     => 'logosliderwp_settings_cat',
                    'label'    => __('Default Categories(slug)', 'logo-slider-wp'),
                    'desc'     => __('Please input category slug with comma( , ). Example: categoey1, category2 ', 'logo-slider-wp'),
                    'type'     => 'text',
                    'default'  => '',
                    'desc_tip' => true,
                ),

                array(
                    'name'             => 'logosliderwp_settings_order',
                    'label'            => __('Item Order', 'logo-slider-wp'),
                    'desc'             => __('Direction to sort item.', 'logo-slider-wp'),
                    'type'             => 'select',
                    'default'          => 'DESC',
                    'options'          => array(
                        'ASC' => __( 'Ascending', 'logo-slider-wp' ),
                        'DESC'   => __( 'Descending', 'logo-slider-wp' ),
                    ),
                ),

                array(
                    'name'             => 'logosliderwp_settings_orderby',
                    'label'            => __('Item Order By', 'logo-slider-wp'),
                    'desc'             => __('Sort retrieved item.', 'logo-slider-wp'),
                    'type'             => 'select',
                    'default'          => 'date',
                    'options'          => array(
                        'date'      => __( 'Date', 'logo-slider-wp' ),
                        'ID'        => __( 'ID', 'logo-slider-wp' ),
                        'title'     => __( 'Title', 'logo-slider-wp' ),
                        'modified'  => __( 'Modified', 'logo-slider-wp' ),
                        'rand'      => __( 'Random', 'logo-slider-wp' ),
                    ),
                ),


                array(
                    'name'     => 'logosliderwp_settings_limit',
                    'label'    => __('Item Limit', 'logo-slider-wp'),
                    'desc'     => __('Please input total number of item, that want to display front end. -1 means all published post.', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '-1',
                    'desc_tip' => true,
                ),

            ),// Single


            // Style Settings
            'logosliderwp_style' => array(

                array(
                    'name'     => 'logosliderwp_settings_nav_position',
                    'label'         => __('Nav Position', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'default'       => 'b-center',
                    'options' => array(
                        'b-center' => __('Bottom Center','logo-slider-wp'),
                        'v-mid' => __('Vertically Middle','logo-slider-wp'),
                        'v-mid-hover' => __('Vertically Middle (On Over)','logo-slider-wp'),

                    )
                ),

                array(
                    'name'     => 'logosliderwp_settings_hover_type',
                    'label'         => __('Hover Effect', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'default'       => 'default',
                    'options' => array(
                        'default' => __('Default','logo-slider-wp'),
                        'grayscale'   => __('Gray Scale','logo-slider-wp'),
                        'hblur'   => __('Blur','logo-slider-wp'),
                        'zoomin'  => __('Zoom In','logo-slider-wp'),
                        'none'    => __('None','logo-slider-wp'),
                    )
                ),

                array(
                    'name'     => 'logosliderwp_settings_bgcolor_en',
                    'label'         => __('Enabled  Background Color', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                array(
                    'name'    => 'logosliderwp_settings_bgcolor',
                    'label'   => __('Background  Color', 'lgxcarousel-domain'),
                    'desc'    => __('Please select Carousel Background color.', 'lgxcarousel-domain'),
                    'type'    => 'color',
                    'default' => '#f1f1f1'
                ),

                array(
                    'name'     => 'logosliderwp_settings_border_en',
                    'label'         => __('Enabled Border', 'logo-slider-wp'),
                    'type'          => 'radio',
                    'required'      => false,
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                array(
                    'name'    => 'logosliderwp_settings_bordercolor',
                    'label'   => __('Border Color', 'lgxcarousel-domain'),
                    'type'    => 'color',
                    'default' => '#d02c21'
                ),

            ),// Single

            //Responsive Settings
            'logosliderwp_responsive' => array(

                // View Port Large Desktop
                array(
                    'name'     => 'logosliderwp_settings_largedesktop_item',
                    'label'    => __('Item in Large Desktops', 'logo-slider-wp'),
                    'desc'     => __('Item in Large Desktops Devices (1200px and Up)', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '5',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_largedesktop_nav',
                    'label'         => __('Show Nav(Large Desktops)', 'logo-slider-wp'),
                    'desc'          => __( 'Show Nav in Large Desktops', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Enabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                // View Port Desktop
                array(
                    'name'     => 'logosliderwp_settings_desktop_item',
                    'label'    => __('Item in Desktops', 'logo-slider-wp'),
                    'desc'     => __('Item in Desktops Devices (Desktops 992px).', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '4',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_desktop_nav',
                    'label'         => __('Show Nav(Desktops)', 'logo-slider-wp'),
                    'desc'          => __( 'Show Nav in Desktops', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Enabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                // View Port Tab
                array(
                    'name'     => 'logosliderwp_settings_tablet_item',
                    'label'    => __('Item in Tablets', 'logo-slider-wp'),
                    'desc'     => __('Item in Tablets Devices (768px and Up)', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '3',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_tablet_nav',
                    'label'         => __('Enabled largedesktop Nav', 'logo-slider-wp'),
                    'desc'          => __( 'Show Nav(Tablet)', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Show Nav in Large Tablet','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),


                // View Port Mobile
                array(
                    'name'     => 'logosliderwp_settings_mobile_item',
                    'label'    => __('Item in Mobile', 'logo-slider-wp'),
                    'desc'     => __('Item in Mobile Devices (Less than 768px)', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '2',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_mobile_nav',
                    'label'         => __('Show Nav(Mobile)', 'logo-slider-wp'),
                    'desc'          => __( 'Show next/prev buttons.', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Show Nav in Mobile"','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),


            ),


            // OWL CONFIG
            'logosliderwp_config'   => array(

                array(
                    'name'     => 'logosliderwp_settings_loop',
                    'label'         => __('Enabled Loop', 'logo-slider-wp'),
                    'desc'          => __( 'Infinity loop. Duplicate last and first items to get loop illusion.', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Enabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                array(
                    'name'     => 'logosliderwp_settings_dots',
                    'label'         => __('Enabled Dots', 'logo-slider-wp'),
                    'desc'          => __( 'Show dots navigation.', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Enabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),


                array(
                    'name'     => 'logosliderwp_settings_margin',
                    'label'    => __('Margin', 'logo-slider-wp'),
                    'desc'     => __('margin-right(px) on item.', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '10',
                    'desc_tip' => true,
                ),


                array(
                    'name'     => 'logosliderwp_settings_autoplay',
                    'label'         => __('Enabled Autoplay', 'logo-slider-wp'),
                    'desc'          => __( 'Carousel item autoplay by default.', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Enabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'yes',
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),

                array(
                    'name'     => 'logosliderwp_settings_autoplay_timeout',
                    'label'    => __('Autoplay Timeout', 'logo-slider-wp'),
                    'desc'     => __('autoplayTimeout', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '2000',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_hover_pause',
                    'label'         => __('Autoplay Hover Pause', 'logo-slider-wp'),
                    'desc'          => __('Pause on mouse hover.', 'logo-slider-wp' ),
                    'type'          => 'radio',
                    'tooltip'       => __('Disabled by default','logo-slider-wp'),
                    'required'      => false,
                    'default'       => 'no',
                    'options' => array(
                        'yes' => __('Yes','logo-slider-wp'),
                        'no' => __('No','logo-slider-wp')
                    )
                ),




                array(
                    'name'     => 'logosliderwp_settings_autoplay_slidespeed',
                    'label'    => __('Slide Speed', 'logo-slider-wp'),
                    'desc'     => __('Set Slide Speed', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '200',
                    'desc_tip' => true,
                ),


                array(
                    'name'     => 'logosliderwp_settings_autoplay_paginationspeed',
                    'label'    => __('Pagination Speed', 'logo-slider-wp'),
                    'desc'     => __('Set Pagination Speed', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '800',
                    'desc_tip' => true,
                ),

                array(
                    'name'     => 'logosliderwp_settings_autoplay_rewindspeed',
                    'label'    => __('Rewind Speed', 'logo-slider-wp'),
                    'desc'     => __('Set Rewind Speed', 'logo-slider-wp'),
                    'type'     => 'number',
                    'default'  => '1000',
                    'desc_tip' => true,
                )


            ),//single


        );//Filed

        $settings_fields = apply_filters('logo_slider_settings_fields', $settings_fields);

        return $settings_fields;
    }


/**
     * Modified get post for post type order
     */
    public function modify_query_get_posts($query) {

        if ( ! is_admin() && ( isset( $query->query_vars['post_type'] ) &&  ( is_array( $query->query_vars['post_type'] ) && in_array( 'logosliderwp', $query->query_vars['post_type'] ) ) ) ) {

            //$order  =   isset( $query->query_vars['order'] )  ?  $query->query_vars['order'] : '';
            
            //var_dump( '<pre>', $query );
            //wp_die(  );
            $query->set( 'orderby', 'menu_order' );
            $query->set( 'order' , 'ASC' );
            //$query->set( 'posts_per_page' , 2 );

        } elseif ( is_admin() ) {
            if ( $query->is_main_query() ) {
                $currentScreen = get_current_screen();
                if ( is_object( $currentScreen ) && $currentScreen->id == 'edit-logosliderwp' && $currentScreen->post_type == 'logosliderwp' ) {
                    $query->set( 'post_type', 'logosliderwp' );
                    $query->set( 'orderby', 'menu_order' );
                    $query->set( 'order' , 'ASC' );
                }
            }
        }
    }



    /**
     * Filters the columns displayed in the Posts list table for a specific post type.
     *
     * apply_filters( "manage_{$post_type}_posts_columns", string[] $post_columns )
     * Deafult Value : cb, title, taxonomy-logosliderwpcat, date
     * @param $default_columns
     */
    public function add_new_column_head_for_logosliderwp($default_columns) {

        // unset( $default_columns['date'] );

        $new_columns['lgx_ls_brand']        = __( 'Brand Name', 'logo-slider-wp' );
        $new_columns['lgx_ls_logo']         = __( 'Brand Logo Image', 'logo-slider-wp' );

        return array_slice( $default_columns, 0, 2, true ) + $new_columns + array_slice( $default_columns, 1, null, true );

    }

    /**
     * Fires for each custom column of a specific post type in the Posts list table.
     * do_action( "manage_{$post->post_type}_posts_custom_column", string $column_name, int $post_id )]
     *
     * @param $column
     * @param $post_id
     */
    public function define_admin_column_value_for_logosliderwp($column, $post_id) {
        switch ($column) {
            case 'lgx_ls_brand':
                $metavalues         = get_post_meta( $post_id, '_logosliderwpmeta', true );
                echo  ( (!empty($metavalues['company_name'] ) ? $metavalues['company_name']: '' ));
                break;

            case 'lgx_ls_logo':
                if( function_exists('the_post_thumbnail') ){

                    $post_thumbnail_id = get_post_thumbnail_id($post_id);
                    $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                    $post_thumbnail_img= $post_thumbnail_img[0];
                    if($post_thumbnail_img !='')
                        echo '<img src="' . $post_thumbnail_img . '" />';
                    else
                        echo 'No logo added.';
                }
                else{
                    echo 'No logo added.';
                }

                break;

            default:
                break;
        }
    }


    /**
     *  Save post for re ordering
     * @since    2.3.0
     */
    
    public function save_post_reorder_for_logosliderwp() {
        global $wpdb;
        $result = array(
                'type' => 'error',
                'message' => 'Action required.',
        );

        $result_json = json_encode( $result );

        if ( ! wp_verify_nonce( $_REQUEST['nonce'], "save_logosliderwp_nonce")) {
            $result['type'] = 'error';
            $result['message'] = 'WP nonce verification failed.';
        }

        try {
            parse_str( stripslashes_deep( $_POST['post_id_serialize'] ), $post_data );
            //$wpdb->queries( 'START TRANSACTION' );

            if ( ! is_array( $post_data ) || count( $post_data ) < 1 ) {
                $result['message'] = 'Available data not found.';
            } else {
                foreach ( $post_data['post'] as $menu_order => $post_id ) {
                    $wpdb->update( $wpdb->posts, array( 'menu_order' => (int)$menu_order ), array( 'ID' => (int)$post_id ) );
                }
            }

            //$wpdb->queries( 'COMMIT' );
            $result['type'] = 'success';
            $result['message'] = 'Reorder has been successful';
        } catch (Exception $exception) {
            //$wpdb->queries( 'ROLLBACK' );
            $result['message'] = $exception->getMessage();
        }

        $result_json = json_encode( $result );
        echo $result_json;
        wp_die();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Logo_Slider_WP_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Logo_Slider_WP_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $currentScreen = get_current_screen();

        if( $currentScreen->id === "edit-logosliderwp" && $currentScreen->post_type == 'logosliderwp' ) {
            wp_enqueue_style( 'admin-icon-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/lgx-icon.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'alertify-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/alertify.css', array(), $this->version, 'all' );

            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/logo-slider-wp-admin.css', array(), $this->version, 'all' );
        }

    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Logo_Slider_WP_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Logo_Slider_WP_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/logo-slider-wp-admin.js', array( 'jquery' ), $this->version, false );





        $currentScreen = get_current_screen();

        if( $currentScreen->id === "edit-logosliderwp" && $currentScreen->post_type == 'logosliderwp' ) {
            $translation_array = array(
                'add_leftimg_title'  => __('Add Previous Arrow Image', 'wpnextpreviouslinkaddon'),
                'add_rightimg_title' => __('Add Next Arrow Image', 'wpnextpreviouslinkaddon'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'check_nonce' => wp_create_nonce('save_logosliderwp_nonce')
            );

            wp_register_script( 'admin-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/logo-slider-wp-admin.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, true );
            wp_localize_script( 'admin-' . $this->plugin_name, 'wpnpaddon', $translation_array);
            wp_enqueue_script( 'admin-' . $this->plugin_name );
            wp_enqueue_script( 'alertify-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/alertify.min.js', array(), $this->version, true );

            if ( ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }
        }
    }


    /**
     * @param $plugin_array
     * @return mixed
     */


    public function lgx_owl_register_tinymce_plugin($plugin_array) {
        $plugin_array['lgx_logo_button'] = plugin_dir_url( __FILE__ ) . 'assets/js/logo-slider-wp-tinymce.js';
        return $plugin_array;
    }

    public function lgx_owl_add_tinymce_button($buttons) {
        $buttons[] = "lgx_logo_button";
        return $buttons;
    }



    /**
     * For checking the pro version of plugin is activated or not
     * @param        string $plugin // slug of free version
     * @param        string $network_activation // network activation
     */

    public function pro_version_activation_checking_admin_init($plugin, $network_activation) {
        $plugin_pro = 'logo-slider-wp-pro/logo-slider-wp-pro.php';
        set_transient( 'lswp_plugin_clicked', $plugin );

        if ( is_plugin_active( $plugin_pro ) ) {
            set_transient( 'lswp_pro_active', true );
        }

    }

    public function pro_version_activation_checking_notice_warning() {
        $plugin_base = LSWP_PLUGIN_BASE_NAME;
        $plugin_free = 'logo-slider-wp/logo-slider-wp.php';
        $plugin_pro = 'logo-slider-wp-pro/logo-slider-wp-pro.php';
        $lswp_pro_active = get_transient( 'lswp_pro_active' );
        $lswp_plugin_clicked = get_transient( 'lswp_plugin_clicked' );
        delete_transient( 'lswp_pro_active' );
        delete_transient( 'lswp_plugin_clicked' );

        if ( true == $lswp_pro_active && $lswp_plugin_clicked == $plugin_pro ) {
            deactivate_plugins( $plugin_free );
            remove_filter('plugin_action_links_' . $plugin_base, array( $this, 'add_links_admin_plugin_page_title' ) );
        } elseif ( true == $lswp_pro_active && $lswp_plugin_clicked == $plugin_free ) {
            deactivate_plugins( $plugin_free );
            remove_filter('plugin_action_links_' . $plugin_base, array( $this, 'add_links_admin_plugin_page_title' ) );

            unset( $_GET['activate'] );
            $class = 'notice notice-warning is-dismissible';
            $message = __( 'Logo Slider Pro version already activated. For more please contact our support at <a href="https://logichunt.com/support/" target="_blank">LogicHunt.com.</a>', LSWP_PLUGIN_TEXT_DOMAIN );

            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
        }


    }

}
