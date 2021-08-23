<?php
use com\cminds\siteaccessrestriction\controller\SettingsController;
use com\cminds\siteaccessrestriction\view\SettingsView;
use com\cminds\siteaccessrestriction\App;
use com\cminds\siteaccessrestriction\model\Settings;

if ( ! empty( $_GET['status'] ) and ! empty( $_GET['msg'] ) ) {
	printf( '<div id="message" class="%s"><p>%s</p></div>', ( $_GET['status'] == 'ok' ? 'updated' : 'error' ), esc_html( $_GET['msg'] ) );
}
$settingsView = new SettingsView();
$clearCacheUrl = $clearCacheUrl ? $clearCacheUrl : '';
?>
<form method="post" id="settings">
    <ul class="cmacc-settings-tabs">
		<?php
		$tabs = apply_filters( 'cmacc_settings_pages', Settings::$categories );
		foreach ( $tabs as $tabId => $tabLabel ) {
			printf( '<li><a href="#tab-%s">%s</a></li>', $tabId, $tabLabel );
		}
		?>
    </ul>
    <div class="inner">
        <?php echo $settingsView->render(); ?>
    </div>
    <p class="form-finalize">
        <?php if (!empty($clearCacheUrl)): ?>
            <a href="<?php echo esc_attr( $clearCacheUrl ); ?>" class="right button">Clear cache</a>
        <?php endif; ?>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( SettingsController::getMenuSlug() ); ?>"/>
        <input type="submit" value="Save" class="button button-primary"/>
    </p>
</form>