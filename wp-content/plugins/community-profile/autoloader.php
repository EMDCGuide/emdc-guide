<?php
/**
 * A custom autoloader so the plugin does not depend on Composer
 * @link https://awhitepixel.com/blog/autoloader-namespaces-theme-plugin/
 */
spl_autoload_register('copr_autoloader');
function copr_autoloader($class) {
    $namespace = 'MissionalDigerati\CommunityProfile';

    if (strpos($class, $namespace) !== 0) {
        // Not in our namespace
        return;
    }
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    $directory = plugin_dir_path( __FILE__ );
    $path = $directory . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $class;

    if (file_exists($path)) {
        require_once($path);
    }
}
