<?php
use com\cminds\registration\App;
?>
<p><strong>Notice: shortcodes are case-sensitive</strong></p>
<?php echo apply_filters('cmreg_shortcodes_content_before', ''); ?>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>Login, logout and registration links</h4>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Parameters</h5>
		<ul>
			<li><strong>redirect_to</strong> - URL to the redirect page. If passed then it will auto redirect after login</li>
			<li><strong>after_login</strong> - If you want to make a simple button instead of logout button after logged-in (0 or 1). Default: 0.</li>
			<li><strong>after_text</strong> - Button text. It will use if after_login set 1</li>
		</ul>
		<p>To show the login or registration form overlay after clicking a link, just set the URL address of the link to the following value:</p>
		<ul>
			<li><kbd>#cmreg-login-click</kbd> - to show the login and registration form side by side</li>
			<li><kbd>#cmreg-only-login-click</kbd> - to show the login form only</li>
			<li><kbd>#cmreg-only-registration-click</kbd> - to show the registration form only</li>
		</ul>
		<p>To create the logout link use the following href attribute:</p>
		<ul>
			<li><kbd>#cmreg-logout-click</kbd> - this will be changed into the logout URL</li>
		</ul>
		<h5>Example</h5>
		<p><kbd>&lt;a href="#cmreg-login-click">Click here to login&lt;/a></kbd></p>
		<p><kbd>&lt;a href="#cmreg-login-click?redirect_to=/dashborad">Click here to login&lt;/a></kbd></p>
		<p><kbd>&lt;a href="#cmreg-login-click?redirect_to=/dashborad&after_login=1&after_text=AnyText">Click here to login&lt;/a></kbd></p>
		<p><kbd>&lt;a href="#cmreg-logout-click">Click here to logout&lt;/a></kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-login]</h4>
		<span>Displays the login button</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Parameters</h5>
		<ul>
			<li><strong>redirect-to</strong> - URL to the redirect page. If passed then it will auto redirect after login</li>
			<li><strong>after-login</strong> - If you want to make a simple button instead of logout button after logged-in (0 or 1). Default: 0.</li>
			<li><strong>after-text</strong> - Button text. It will use if after-login set 1</li>
		</ul>
		<h5>Shortcode content</h5>
		<p>Optionally you can pass the text that will appear on the button.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-login]</kbd></p>
		<p><kbd>[cmreg-login redirect-to="/dashboard"]</kbd></p>
		<p><kbd>[cmreg-login redirect-to="/dashboard" after-login="1" after-text="Any Text"]</kbd></p>
		<p><kbd>[cmreg-login]Click here to login[/cmreg-login]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-login-form]</h4>
		<span>Displays the login form</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Parameters</h5>
		<ul>
			<li><strong>registration-url</strong> - URL to the registration page (optional). If passed then it will show a link to the registration page.</li>
			<li><strong>registration-link</strong> - text for the registration page link (optional). If passed then it will show a link to the registration page.</li>
			<li><strong>social-login</strong> - whether to show the social login buttons (0 or 1). Default: 1.</li>
			<li><strong>redirect-to</strong> - URL to the redirect page. If passed then it will auto redirect after login</li>
		</ul>
		<h5>Shortcode content</h5>
		<p>Optionally you can pass text or HTML that will be displayed for the logged-in users instead of the login form by putting it between the shortcode tags.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-login-form]</kbd></p>
		<p><kbd>[cmreg-login-form redirect-to="/dashboard"]</kbd></p>
		<p><kbd>[cmreg-login-form]You are already logged-in[/cmreg-login-form]</kbd></p>
		<p><kbd>[cmreg-login-form registration-url="/register" registration-link="Click here to register"]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-registration-form]</h4>
		<span>Displays the registration form</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Parameters</h5>
		<ul>
			<li><strong>login-url</strong> - URL to the login page (optional). If passed then it will show a link to the login page.</li>
			<li><strong>login-link</strong> - text for the login page link (optional). If passed then it will show a link to the login page.</li>
			<?php if (App::isPro()): ?>
				<li><strong>role</strong> - set a role that the new user will be registered with (optional).</li>
				<li><strong>social-login</strong> - whether to show the social login buttons (0 or 1). Default: 1.</li>
			<?php endif; ?>
		</ul>
		<h5>Shortcode content</h5>
		<p>Optionally you can pass text or HTML that will be displayed for the logged-in users instead of the registration form by putting it between the shortcode tags.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-registration-form]</kbd></p>
		<p><kbd>[cmreg-registration-form]You are already logged-in[/cmreg-registration-form]</kbd></p>
		<p><kbd>[cmreg-registration-form login-url="/login" login-link="Click here to login"]</kbd></p>
		<?php if (App::isPro()): ?>
			<p><kbd>[cmreg-registration-form role=contributor]</kbd></p>
			<p><kbd>[cmreg-registration-form social-login=0]</kbd></p>
		<?php endif; ?>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-registration-btn]</h4>
		<span>Displays the registration button</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<p>Shortcode displays the registration button and the registration form is being opened in the overlay after clicking the button.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-registration-btn]</kbd></p>
		<p>You can also set the button's text:</p>
		<p><kbd>[cmreg-registration-btn]Click here[/cmreg-registration-btn]</kbd></p>
	</div>
</article>
<?php if (App::isPro()): ?>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-lost-password]</h4>
		<span>Displays the password reset form for not logged-in users</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Parameters</h5>
		<ul>
			<li><strong>showforusers</strong> - whether to show the reset password form also for logged-in users (0 or 1). Default is 0.</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[cmreg-lost-password]</kbd></p>
		<p><kbd>[cmreg-lost-password showforusers=1]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-reset-password]</h4>
		<span>Displays the reset password form</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<p>Displays the form that user can use to change his password.</p>
		<h5>Parameters</h5>
		<ul>
			<li><strong>showheader</strong> - whether to show the header (0 or 1). Default is 0.</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[cmreg-reset-password]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-edit-profile]</h4>
		<span>Displays the edit profile form</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<p>Displays the form that user can use to edit his profile data eg. display name, email address and the extra fields defined in the CM Registration plugin settings.</p>
		<h5>Parameters</h5>
		<ul>
			<li><strong>showheader</strong> - whether to show the header (0 or 1). Default is 1.</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[cmreg-edit-profile]</kbd></p>
		<p><kbd>[cmreg-edit-profile showheader=0]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-change-password]</h4>
		<span>Displays the change password form</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<p>Displays the form that user can use to change his password.</p>
		<h5>Parameters</h5>
		<ul>
			<li><strong>showheader</strong> - whether to show the header (0 or 1). Default is 0.</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[cmreg-change-password]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-social-login]</h4>
		<span>Displays the social login buttons</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Examples</h5>
		<p><kbd>[cmreg-social-login]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-create-invitation-code]</h4>
		<span>Allow users to invite friends</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<p>Displays a form for the regular users to create the invitation codes with the specified parameters
			or send an invitation directly to the email address given by a user.</p>
		<h5>Parameters</h5>
		<ul>
			<li><strong>expiration</strong> - set the expiration date (<kbd>YYYY-MM-DD HH:MM:SS</kbd>) or time period that the invitation will be valid for
				in English words e.g. <kbd>30 days</kbd>. You can use the following English words: minutes, hours, days, weeks, months or years.</li>
			<li><strong>userslimit</strong> - number of users that can use a single created invitation code.</li>
			<li><strong>verifyemail</strong> - whether to verify the email address of a user that will register with the created invitation code (0 or 1).
				Default value (global) depends on the plugin settings.</li>
			<li><strong>role</strong> - set a role that the new user will be registered with (optional).</li>
			<li><strong>showparams</strong> - whether to show the invitation parameters (such as expiration date, email verification etc.) to the user that want to invite his friends.</li>
			<li><strong>emailinput</strong> - whether to show the email address input field for the invited user (0 or 1). If enabled, the invitation will be
				automatically send to the invited user by email.</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[cmreg-create-invitation-code expiration="2 weeks" verifyemail=1]</kbd></p>
		<p><kbd>[cmreg-create-invitation-code role=contributor userslimit=1]</kbd></p>
		<p><kbd>[cmreg-create-invitation-code emailinput=1 showparams=1]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-list-users-invitations]</h4>
		<span>Displays the invitations created by current user</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Examples</h5>
		<p><kbd>[cmreg-list-users-invitations]</kbd></p>
	</div>
</article>
<?php endif; ?>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-logout-btn]</h4>
		<span>Displays the logout button</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Shortcode content</h5>
		<p>Optionally you can pass the text that will appear on the button.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-logout-btn]</kbd></p>
		<p><kbd>[cmreg-logout-btn]Click here to logout[/cmreg-logout-btn]</kbd></p>
	</div>
</article>
<article class="cmreg-shortcode-desc">
	<header>
		<h4>[cmreg-delete-account]</h4>
		<span>Displays the delete account button</span>
	</header>
	<div class="cmreg-shortcode-desc-inner">
		<h5>Shortcode content</h5>
		<p>Optionally you can pass the text that will appear on the button.</p>
		<h5>Examples</h5>
		<p><kbd>[cmreg-delete-account]</kbd></p>
		<p><kbd>[cmreg-delete-account]Delete Account[/cmreg-delete-account]</kbd></p>
	</div>
</article>
<?php echo apply_filters('cmreg_shortcodes_content_after', ''); ?>