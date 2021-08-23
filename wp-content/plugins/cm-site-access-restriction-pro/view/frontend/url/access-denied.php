<?php
use com\cminds\siteaccessrestriction\model\Labels;
?>
<div class="cmacc-access-denied cmacc-access-denied-url">
	<?php
	if(is_archive()) {
		echo Labels::getLocalized('access_denied_text_for_archive_page');
	} else {
		echo Labels::getLocalized('access_denied_text');
	}
	?>
</div>