<?php
printf('<a href="%s">%s</a>', esc_attr($code->getEditUrl()), esc_html($code->getTitle()));
printf('<div><input type="text" readonly value="%s"></div>', esc_attr($code->getCodeString()));
?>