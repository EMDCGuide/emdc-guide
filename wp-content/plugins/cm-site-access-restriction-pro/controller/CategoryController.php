<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\helper\FormHtml;
use com\cminds\siteaccessrestriction\model\Settings;

class CategoryController extends Controller {
	
	static $actions = array(
		'init'
		/*
		array(
			'name' => 'category_add_form_fields',
			'method' => 'addCustomFieldCreate'
		),
		array(
			'name' => 'category_edit_form_fields',
			'method' => 'addCustomFieldEdit',
			'args' => 2
		),
		array(
			'name' => 'edited_category',
			'method' => 'saveCustomFields',
			'args' => 2,
		),
		array(
			'name' => 'create_category',
			'method' => 'saveCustomFields',
			'args' => 2,
		)
		*/
	);

	static $filters = array(

	);

	static function init() {
	    $args = array(
            'public'   => true
        );
        $post_types = get_post_types($args);
        foreach ($post_types as $post_type){
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            if (is_array($taxonomies) && count($taxonomies) > 0){
                foreach ($taxonomies as $taxonomy){
                    add_action($taxonomy->name . '_add_form_fields', array(__CLASS__, 'addCustomFieldCreate'));
                    add_action($taxonomy->name . '_edit_form_fields', array(__CLASS__, 'addCustomFieldEdit'), 10, 2);
                    add_action( 'edited_' . $taxonomy->name, array(__CLASS__, 'saveCustomFields'), 10, 2);
                    add_action( 'create_' . $taxonomy->name, array(__CLASS__, 'saveCustomFields'), 10, 2);
                }
            }
        }
    }

    static function addCustomFieldEdit($tag, $taxonomy) {
        $roles = Settings::getRolesOptions();
        $selectedRoles = get_term_meta($tag->term_id, 'cmsar_allowed_roles', TRUE);
        $selectedRoles = is_array($selectedRoles) ? $selectedRoles : array();
        ob_start();
        ?>
        <tr class="form-field term-allow-roles">
            <th scope="row">
                <label for="description">Allow viewing this category:</label>
            </th>
            <td>
                <?php echo FormHtml::checkboxList('cmsar_allowed_roles[]',$roles, $selectedRoles ); ?>
            </td>
        </tr>
        <?php
        $content = ob_get_clean();
        echo $content;
    }

    static function addCustomFieldCreate() {
        $roles = Settings::getRolesOptions();
        $selectedRoles = array();
        ob_start();
        ?>
        <div class="form-field term-description-wrap">
            <label for="tag-description">Allow viewing this category:</label>
            <?php echo FormHtml::checkboxList('cmsar_allowed_roles[]',$roles, $selectedRoles ); ?>
        </div>
        <?php
        $content = ob_get_clean();
        echo $content;
    }

	static function saveCustomFields($term_id, $tt_id) {
        //if (!isset($_POST['cmsar_allowed_roles'])) { return; }
        update_term_meta($term_id, 'cmsar_allowed_roles', $_POST['cmsar_allowed_roles']);
    }
	
}