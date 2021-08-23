<?php
use com\cminds\registration\model\Settings;
use com\cminds\registration\view\SettingsView;
$settingsView = new SettingsView();
?>
<br>
<h3>Custom CSS</h3>
<p>Place your styles here to override any CSS rule of the plugin.</p>
<?php
$customCSSFieldName = Settings::OPTION_CUSTOM_CSS;
echo $settingsView->renderOptionControls($customCSSFieldName);
?>
<h3>Typical style modifications</h3>
<p>Click on the red value to change it. Press "Add" button to add custom CSS to the textarea. You can change it after adding and update.</p>
<div id="custom-css-editor">
    <h4>Change form heading text</h4>
    <pre><code class="no-highlight">.cmreg-form h2 { color:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
    <h4>Change form description text</h4>
    <pre><code class="no-highlight">.cmreg-form .cmreg-form-text { color:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
	<h4>Change form labels</h4>
    <pre><code class="no-highlight">.cmreg-form label { color:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
	<h4>Change form input (text, password, email, number) field</h4>
    <pre><code class="no-highlight">.cmreg-form input { background:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
	<h4>Change form textarea field</h4>
    <pre><code class="no-highlight">.cmreg-form textarea { background:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
	<h4>Change form button</h4>
    <pre><code class="no-highlight">.cmreg-form button { background:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
	<h4>Change form field description text</h4>
    <pre><code class="no-highlight">.cmreg-form .cmreg-field-description { color:<span>#141412 !important</span>; font-size:<span>13px !important;</span> }</code></pre>
</div>
<script type="text/javascript">
(function ($) {
	$(document).ready(function ($) {
		$('#custom-css-editor code').append('<button class="cmreg-button">Add</button>');
		$('#custom-css-editor code span').click(function () {
			var obj = $(this);
			if (obj.find('input').length)
				return;
			var value = obj.text();
			var input = document.createElement('input');
			input.setAttribute('value', value);
			input.style.width = value.length + "em";
			obj.html(input);
			input.select();
			$(input).blur(function () {
				obj.text($(this).val().length > 0 ? $(this).val() : value);
			});
		});
		$('#custom-css-editor code button').click(function () {
			var code = this.parentNode;
			code.removeChild(this);
			var textarea = $('textarea[name=<?php echo $customCSSFieldName; ?>]');
			if(textarea.val() != '') {
				textarea.val(textarea.val() + "\n" + $(code).text());
			} else {
				textarea.val($(code).text());
			}
			code.appendChild(this);
			return false;
		});		
		$('#custom-css-editor a').click(function (e) {
			e.preventDefault();
			return false;
		});
	});
})(jQuery);
</script>
<style type="text/css">
textarea[name="cmreg_custom_css"] { width: 100%; height: 200px; }
#custom-css-editor code {padding: 10px; display: block; background: #f0f0f0;}
#custom-css-editor .cmreg-button { float: right; cursor: pointer; }
#custom-css-editor span {color: red; cursor: pointer;}
</style>