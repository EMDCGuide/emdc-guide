<?php
/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://logichunt.com
 * @since      1.0.0
 *
 * @package    logosliderwpcarousel
 * @subpackage logosliderwpcarousel/admin/partials
 */
if (!defined('WPINC')) {
    die;
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php _e('Logo Slider WP: Usage & Help', 'logoslider-domain'); ?></h2>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside lgx-settings-inside">
                            <h3><?php _e('Usage Guidelines', 'logo-slider-wp'); ?></h3>
                            <ol>
                                <li><?php _e('To add new logo, go to Logo Slider WP >> Add New', 'logo-slider-wp'); ?></li>
                                <li><?php _e('Add company name, link and image. To get best result please try to use logo with same dimension.', 'logo-slider-wp'); ?></li>
                                <li><?php _e('To display multiple Slider, create categories under "Logo Slider WP >> Logo Categories" and create categories according your demand.', 'logo-slider-wp'); ?></li>
                                <li><?php _e('Use the shortcode on any post, page, widget or theme to display the Logo Slider WP.', 'logo-slider-wp'); ?></li>
                                <li><?php _e('Set default options from plugin settings menu & enjoy.', 'logo-slider-wp'); ?></li>
                            </ol>

                            <br>

                            <div class="lgx_row" style="width: 100%;">
                                <div class="lgx_logo_slider_shortcode_copy_box" >
                                    <h3 class="lgx_logo_showcase_header">Shortcode</h3>
                                    <p style="padding:0 5%;"> Copy and paste this shortcode into your posts or pages. Also you can customize your shortcode param and copy from here.</p>
                                    <div class="lgx_logo_slider_shortcode_copy_wrap">
                                        <input id="logosliderwp_shortcode_copy" class="regular-text lgx_logo_slider_shortcode_copy_text" type="text" value='&#91;logo-slider&#93;' >
                                        <button class="button button-secondary lgx_logo_slider_shortcode_copy_btn" ><i class="lgxicon lgx-icon-copy"></i> <?php _e('Copy', 'logo-slider-wp'); ?></button>
                                    </div>
                                    <p><span style="font-weight: 700;"><?php _e('Important:', 'logo-slider-wp'); ?> </span> <?php _e('All plugin default settings will be override by this shortcode parameter.', 'logo-slider-wp'); ?></p>
                                </div> <!-- // BOX -->

                                <div class="lgx_logo_slider_shortcode_copy_box" >
                                    <h3 class="lgx_logo_showcase_header">Template PHP file</h3>
                                    <p style="padding:0 5%;"> Copy and paste this shortcode into your posts or pages. Also you can customize your shortcode param and copy from here.</p>
                                    <div class="lgx_logo_slider_shortcode_copy_wrap">
                                        <input id="logosliderwp_shortcode_copy" class="regular-text lgx_logo_slider_shortcode_copy_text" type="text" value="&lt;?php echo do_shortcode&#40;'&#91;logo-slider&#93;'); ?&gt;" >
                                        <button class="button button-secondary lgx_logo_slider_shortcode_copy_btn" ><i class="lgxicon lgx-icon-copy"></i> <?php _e('Copy', 'logo-slider-wp'); ?></button>
                                    </div>
                                    <p><span style="font-weight: 700;"><?php _e('Important:', 'logo-slider-wp'); ?> </span> <?php _e('All plugin default settings will be override by this shortcode parameter.', 'logo-slider-wp'); ?></p>
                                </div> <!-- // BOX -->

                            </div>


                            <p style="margin: 150px 0 15px 0; text-align: center"> <a style="width: 65%;text-align: center; padding: 5px;" class="button button-primary" href="https://logichunt.com/support/" target="_blank"><?php _e('Hire us to Install or Customize any WordPress Themes & Plugins', 'logo-slider-wp'); ?></a></p>
                            <p style="margin: 0px 0 50px 0; text-align: center"> <a style="width: 65%;text-align: center; padding: 5px;" class="button " href="https://bit.ly/3dJdrzN" target="_blank"><?php _e('Visit our Themeforest Profile for Premium Themes & Templates. ', 'logo-slider-wp'); ?></a></p>



                            <h3><?php _e('Support', 'logo-slider-wp'); ?></h3>
                            <p><?php _e("If you need any helps, please don't hesitate to contact with us.", 'logo-slider-wp'); ?></p>

                            <a class="button button-secondary" href="https://logichunt.com/product/wordpress-logo-slider/" target="_blank"><?php _e('Product Page', 'logo-slider-wp'); ?></a>
                            <a class="button button-primary" href="https://bit.ly/31lHGaW" target="_blank"><?php _e('Get Support', 'logo-slider-wp'); ?></a>
                            <a class="button " style="background:#feb700; color:#003087;border-color: #feb700;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=vaspal%2ekt%40gmail%2ecom&lc=US&item_name=LogicHunt&item_number=wp&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted" target="_blank"><?php _e('Donate Us :)', 'logo-slider-wp'); ?></a>

                        </div> <!-- .inside -->
                    </div> <!-- .postbox -->
                </div> <!-- .meta-box-sortables .ui-sortable -->
            </div> <!-- post-body-content -->
            <?php
            include('sidebar.php');
            ?>

        </div> <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div> <!-- #poststuff -->

</div> <!-- .wrap -->

<script type="text/javascript">

    /*
    *  Copy Shortcode to Clipboard
    * */


    function LogoSliderwpShortcodeCopy(itemId) {

        let copyText = document.getElementById(itemId);

        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        alert('Copied');
    }



    jQuery(document).ready(function($) {
        //if need any js code here
    });

</script>
