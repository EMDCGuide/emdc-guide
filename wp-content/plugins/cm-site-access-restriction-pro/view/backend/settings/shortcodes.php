<?php
use com\cminds\siteaccessrestriction\shortcode\ContentShortcode;
use com\cminds\siteaccessrestriction\App;
?>
<article class="cmsar-shortcode-desc">
	<header>
		<h4>[<?php echo ContentShortcode::SHORTCODE_NAME; ?>]</h4>
		<span>Show the content only to specific users</span>
	</header>
	<div class="cmsar-shortcode-desc-inner">
		<h5>Attributes:</h5>
		<ul>
			<li><strong>role</strong> - Wordpress role key that user must have to display the content (optional)</li>
			<li><strong>cap</strong> - Wordpress user capability that user must have to display the content (optional)</li>
			<li><strong>userid</strong> - User IDs to display the content to (optional), e.g. "2", "2,69", "2,69,158"</li>
			<li><strong>guests</strong> - if set to 1 - then show the content only to guests (optional)</li>
			<li><strong>login</strong> - if set to 1 - then show the content only to logged in users (optional)</li>
			<li><strong>deniedtext</strong> - show the specified text to users that are not allowed to see the content (optional)</li>
			<li><strong>deniedtext_html</strong> - show the specified HTML (gets from options) to users that are not allowed to see the content (optional)</li>
			<li><strong>reverse</strong> - logical reverse the access condition: display content when conditions are not met (optional)</li>
			<li><strong>doshortcode</strong> - if set to 1 then process all shortcodes in the restricted content and also in the deniedtext.
				Set to 0 to disable processing the shortcodes. Default is 1. (optional)</li>
            <li><strong>blacklist</strong> - if added list of nicknames - then show the content for all users with the exception of users from this list and not logged in users (optional)</li>
            <li><strong>whitelist</strong> - if added list of nicknames - then show the content users from this list only (optional)</li>
		</ul>
		<h5>Examples</h5>
		<p><kbd>[access login=1]This will be displayed only to logged in users[/access]</kbd></p>
		<p><kbd>[access guests=1]This will be displayed only to guests[/access]</kbd></p>
		<p><kbd>[access role=editor deniedtext="You have to be Editor"]This will be displayed only to Editors[/access]</kbd></p>
		<p><kbd>[access role=editor deniedtext_html]This will be displayed HTML from options[/access]</kbd></p>
		<p><kbd>[access cap="publish_posts"]You're allowed to publish posts so please publish this post immediately[/access]</kbd></p>
		<p><kbd>[access userid="123"]Please review the links in this article[/access]</kbd></p>
		<p><kbd>[access userid="2,123"]Please review the links in this article[/access]</kbd></p>
		<p><kbd>[access role=administrator reverse=1]Anyone but administrator will see this[/access]</kbd></p>
		<p><kbd>[access role=subscriber cap="custom_cap"]Only subscribers with a "custom_cap" capability will see this[/access]</kbd></p>
		<p><kbd>[access cap="publish_posts"][access role="author" reverse=1]You can also nest the shortcodes to create more complex conditions such as:
			everyone that can publish posts instead of authors will see this[/access][/access]</kbd></p>
		<p><kbd>[access doshortcode=0]The shortcode inside this block such as [cma-questions] won't be processed[/access]</kbd></p>
        <p><kbd>[access blacklist="user1,user2"]This will be displayed for all users with exception of users from this list and not logged in users[/access]</kbd></p>
        <p><kbd>[access whitelist="user1,user2"]This will be displayed for users from this list only[/access]</kbd></p>
	</div>
</article>
<style>
.cmsar-shortcode-desc {margin: 2em 0;}
.cmsar-shortcode-desc header {background: #f0f0f0; padding: 0.5em; display: flex;}
.cmsar-shortcode-desc header h4 {font-size: 150%; flex: 0 0 1; margin: 0; padding: 0;}
.cmsar-shortcode-desc span {flex: 1; text-align: right;}
.cmsar-shortcode-desc-inner {margin: 0 2em;}
.cmsar-shortcode-desc-inner h5 {font-size: 150%; font-weight: normal; border-bottom: 1px dashed #c0c0c0; padding-bottom: 0.2em; margin: 1em 0;}
.cmsar-shortcode-desc-inner ul li {margin-left: 2em; list-style-type: disc;}
.cmsar-shortcode-desc-inner p {margin: 1em 0;}
</style>