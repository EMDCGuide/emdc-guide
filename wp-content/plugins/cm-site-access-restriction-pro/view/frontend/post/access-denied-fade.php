<?php
use com\cminds\siteaccessrestriction\model\Labels;
use com\cminds\siteaccessrestriction\model\Settings;
?>
<div class="cmacc-access-denied">
	<?php
	if(is_archive()) {
		if($restrictMessageForArchive == '1') {
			echo Labels::getLocalized('access_denied_text_for_archive_page');
		}
	} else {
		echo Labels::getLocalized('access_denied_text_fade');
	}
	$loginUrl = Settings::getOption(Settings::OPTION_LOGIN_REDIRECT_URL);
	if ($loginUrl) {
		echo '<div class="cmacc-access-denied-login-link"><a class="button cmacc-access-denied-login-button" href="'.$loginUrl.'">Login</a></div>';
	}
	?>
</div>