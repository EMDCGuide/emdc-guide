<?php
use com\cminds\siteaccessrestriction\controller\SettingsController;
use com\cminds\siteaccessrestriction\App;

$cminds_plugin_config = array(
	'plugin-is-pro'						=> App::isPro(),
	'plugin-is-addon'					=> FALSE,
	'plugin-version'					=> App::VERSION,
	'plugin-abbrev'						=> App::PREFIX,
    'plugin-affiliate'					=> '',
    'plugin-redirect-after-install'		=> admin_url( 'admin.php?page=' . SettingsController::getMenuSlug() ),
	'plugin-settings-url'				=> admin_url( 'admin.php?page=' . SettingsController::getMenuSlug() ),
	//'plugin-show-guide'				=> TRUE,
    'plugin-guide-text'					=> '<div style="display:block">
        <ol>
            <li>Go to <strong>"Plugin Settings"</strong> and configure the desired behavior of the login and logout</li>
            <li>Use the css class <strong>"cmacc-login-click"</strong> and add it to a link in your site navigation</li>
            <li>Alternatively use the shortcode  <strong>"[cmacc-login]Login[/cmacc-login]" </strong> in your site side bar widget.</li>
            <li><strong>Troubleshooting:</strong> Make sure your site does not have any JavaScript error which might prevent registraion popup from appearing</li>
        </ol>
    </div>',
    'plugin-guide-video-height'      => 240,
    'plugin-guide-videos'            => array(
		//array( 'title' => 'Installation tutorial', 'video_id' => '158514902' ),
    ),
	'plugin-show-shortcodes'	 => TRUE,
	'plugin-shortcodes'			 => '<p>You can use the following available shortcodes.</p>',
	'plugin-shortcodes-action'	 => 'cmacc_display_available_shortcodes',
	'plugin-parent-abbrev'		 => '',
	'plugin-file'				 => App::getPluginFile(),
	'plugin-dir-path'			 => plugin_dir_path( App::getPluginFile() ),
	'plugin-dir-url'			 => plugin_dir_url( App::getPluginFile() ),
	'plugin-basename'			 => plugin_basename( App::getPluginFile() ),
	'plugin-icon'				 => '',
	'plugin-name'				 => App::getPluginName(true),
	'plugin-license-name'		 => App::getPluginName(true),
	'plugin-slug'				 => App::SLUG,
	'plugin-short-slug'			 => App::PREFIX,
	'plugin-parent-short-slug'	 => '',
	'plugin-menu-item'			 => App::SLUG,
	'plugin-textdomain'			 => '',
	'plugin-userguide-key'		 => '637-cm-site-access-restriction',
	'plugin-store-url'			 => 'https://www.cminds.com/store/cm-site-access-restriction-and-invitation-codes-plugin-for-wordpress/',
	'plugin-support-url'		 => 'https://wordpress.org/support/plugin/cm-site-access-restriction',
	'plugin-review-url'			 => 'http://wordpress.org/support/view/plugin-reviews/cm-site-access-restriction',
	'plugin-changelog-url'		 => 'https://www.cminds.com/store/cm-site-access-restriction-and-invitation-codes-plugin-for-wordpress/#changelog',
	'plugin-licensing-aliases'	 => App::getLicenseAdditionalNames(),
	'plugin-compare-table'       => '<div class="pricing-table" id="pricing-table">
		<ul>
			<li class="heading">Current Edition</li>
			<li class="price">$0.00</li>
			<li class="noaction"><span>Free Download</span></li>
		   <li>Login and Registration PopUp</li>
			<li>Ajax login or registration</li>
			<li>Stay on the same page after login</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			<li>X</li>
			  <li class="price">$0.00</li>
			<li class="noaction"><span>Free Download</span></li>
		</ul>
		<ul>
			<li class="heading">Pro</li>
			<li class="price">$29.00</li>
			<li class="action"><a href="https://www.cminds.com/store/cm-site-access-restriction-and-invitation-codes-plugin-for-wordpress/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=88183&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" target="_blank">Buy Now</a></li>
		   <li>Login and Registration PopUp</li>
			<li>Ajax login or registration</li>
			<li>Stay on the same page after login</li>
			<li>Invitation codes support</li>
			<li>Email verification</li>
			<li>Email templates</li>
			<li>reCaptcha support</li>
			<li>Change any label easily</li>
			<li>Remove users who did not verify email</li>
			<li>Membership plugins integration</li>
			<li>Set user role while registrating</li>
			<li>Add/Edit registration fields</li>
			<li>Restrict registration fields per Role</li>
			<li>Create Role based Registration forms</li>
			<li>Export All User Data To CSV</li>
		   <li class="price">$29.00</li>
			<li class="action"><a href="https://www.cminds.com/store/cm-site-access-restriction-and-invitation-codes-plugin-for-wordpress/" style="background-color:darkblue;" target="_blank">More Info</a> &nbsp;&nbsp;<a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=88183&wp_referrer=https://www.cminds.com/checkout/&edd_options[price_id]=1" target="_blank">Buy Now</a></li>
		</ul>
	</div>',
);
?>