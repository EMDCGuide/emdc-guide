<?php
use com\cminds\siteaccessrestriction\model\Labels;
?>
<div class="cmacc-access-denied cmacc-access-denied-post">
	<?php
	if(is_archive()) {
		if($restrictMessageForArchive == '1') {
			echo Labels::getLocalized('access_denied_text_for_archive_page');
		}
	} else {
		echo Labels::getLocalized('access_denied_text');
	}
	?>
</div>