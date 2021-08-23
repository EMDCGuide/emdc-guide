<?php
use com\cminds\registration\App;
?>
<div class="cm-licensing-box">
<?php
if (App::isPro()) {
	echo do_shortcode('[cminds_pro_ads id='. App::PREFIX .']');
} else {
	echo do_shortcode('[cminds_free_registration id="'. App::PREFIX .'"]');
}
?>
</div>