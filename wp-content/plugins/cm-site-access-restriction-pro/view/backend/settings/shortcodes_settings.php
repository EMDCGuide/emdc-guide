<?php
use com\cminds\siteaccessrestriction\model\Settings;
?>
<table>
    <caption>Shortcode [access]</caption>
    <tr valign="top">
        <th scope="row" valign="middle" align="left">Denied text HTML</th>
        <td>
			<?php wp_editor(
				get_option( Settings::OPTION_SHORTCODE_ACCESS_DENIED_TEXT, '' ),
				'cmacc_shortcode_access_denied_text',
				array(
					'wpautop'          => 1,
					'textarea_name'    => 'cmacc_shortcode_access_denied_text',
					'textarea_rows'    => 20,
					'media_buttons'    => 1,
					'tabindex'         => null,
					'editor_css'       => '',
					'editor_class'     => '',
					'teeny'            => 0,
					'dfw'              => 0,
					'tinymce'          => 1,
					'quicktags'        => 1,
					'drag_drop_upload' => false
				)
			); ?>
        </td>
        <td>
            Use shortcode [access] with param "deniedtext_html" to use this html instead of protected content:
            <br>
            <b>[access deniedtext_html] content here [/access]</b>
        </td>
    </tr>
</table>