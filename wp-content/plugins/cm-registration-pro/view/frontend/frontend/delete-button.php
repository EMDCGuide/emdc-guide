<?php
use com\cminds\registration\model\Labels;
use com\cminds\registration\model\Settings;
?>
<a href="<?php echo add_query_arg( 'cmreg-delete-account', 'yes', home_url( '/' ) ) ?>" onclick="return confirm('<?php echo $deleteButtonConfirmText; ?>')" class="cmreg-delete-button<?php if($extraClass != '') { echo ' '.$extraClass; } ?>"><?php echo $deleteButtonText; ?></a>