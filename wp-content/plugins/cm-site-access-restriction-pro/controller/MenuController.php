<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\App;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\Labels;

class MenuController extends Controller {
	
	static $actions = array(
		'wp_nav_menu_item_custom_fields' => array('method' => 'nav_menu_fields', 'args' => 4),
		'wp_update_nav_menu_item' => array('method' => 'nav_menu_fields_update', 'args' => 2),
	);

	static $filters = array(
		'wp_get_nav_menu_items' => array('method' => 'nav_menu_show'),
		'wp_setup_nav_menu_item' => array('method' => 'setup_nav_menu'),
	);
	
	static function setup_nav_menu($item) {
		if(!is_admin()) {
			$menuitem = get_post_meta($item->ID, 'cmsar_menu_items', true);
			if($menuitem == '') {
				$menuitem = array(
					'islogouturl' => 'no',
					'visible_for' => 'everyone',
					'all_selected' => 'all_roles',
					'roles' => array()
				);
			}
			if($menuitem['islogouturl'] == 'yes' && $item->type == 'custom') {
				$item->url = wp_logout_url();
			}
		}
		return $item;
	}

	static function nav_menu_show($menuitems=array()) {
		
		if(!is_admin()) {
			foreach($menuitems as $itemkey=>$itemval) {

				$item = get_post_meta($itemval->ID, 'cmsar_menu_items', true);
				if($item == '') {
					$item = array(
						'islogouturl' => 'no',
						'visible_for' => 'everyone',
						'all_selected' => 'all_roles',
						'roles' => array()
					);
				}

				if($item['visible_for'] == 'loggedin') {
					// Logged In Users
					if($item['all_selected'] == 'selected_roles') {
						// Selected roles only
						if(is_user_logged_in()) {
							$roleflag = false;
							foreach($item['roles'] as $role) {
								if(current_user_can($role)) {
									$roleflag = true;
									break;
								}
							}
							if($roleflag == false) {
								unset($menuitems[$itemkey]);
							}
						} else {
							unset($menuitems[$itemkey]);
						}
					} else {
						// All roles
						if(!is_user_logged_in()) {
							unset($menuitems[$itemkey]);
						}
					}
				} else if($item['visible_for'] == 'loggedout') {
					// Logged Out Users
					if(is_user_logged_in()) {
						unset($menuitems[$itemkey]);
					}
				} else {
					// Everyone
				}

			}
		}

		return $menuitems;
	}

	static function nav_menu_fields($item_id, $item, $depth, $args) {
		global $wp_roles;
		$roles = $wp_roles->role_names;
		$visiblefor = array(
			'everyone'  => 'Everyone',
			'loggedin'  => 'Logged In Users',	
			'loggedout' => 'Logged Out Users',
		);
		$menuitem = get_post_meta($item_id, 'cmsar_menu_items', true);
		if($menuitem == '') {
			$menuitem = array(
				'islogouturl' => 'no',
				'visible_for' => 'everyone',
				'all_selected' => 'all_roles',
				'roles' => array()
			);
		}
		?>
		<div class="cmsar_menu_options">
			<?php
			if($item->type == 'custom') {
				?>
				<div class="is_logout_url">
					<?php if($menuitem['islogouturl'] == 'yes') { ?>
						<input type="checkbox" name="cmsar_menu_item[<?php echo $item->ID; ?>][islogouturl]" value="yes" class="cmsar_islogouturl_checkbox" checked="checked" /> Make a logout URL
					<?php } else { ?>
						<input type="checkbox" name="cmsar_menu_item[<?php echo $item->ID; ?>][islogouturl]" value="yes" class="cmsar_islogouturl_checkbox" /> Make a logout URL
					<?php } ?>
				</div>
				<?php
			}
			$height = '0px';
			if($menuitem['islogouturl'] == 'yes') {
				$height = '100px';
			}
			?>
			<div class="blocker" style="height:<?php echo $height; ?>;"></div>
			<div class="visible_for">
				<div><label>Visible for</label></div>
				<div>
					<select name="cmsar_menu_item[<?php echo $item->ID; ?>][visible_for]" class="cmsar_visible_for_select">
						<?php
						foreach($visiblefor as $key=>$val) {
							if($menuitem['visible_for'] == $key) {
								echo '<option value="'.$key.'" selected="selected">'.$val.'</option>';
							} else {
								echo '<option value="'.$key.'">'.$val.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<?php
			$display = 'none';
			if($menuitem['visible_for'] == 'loggedin') {
				$display = 'block';
			}
			?>
			<div style="display:<?php echo $display; ?>" class="cmsar_menu_all_selected_roles">
				<div>
					<?php if($menuitem['all_selected'] == 'all_roles') { ?>
						<input type="radio" name="cmsar_menu_item[<?php echo $item->ID; ?>][all_selected]" value="all_roles" class="cmsar_all_selected_radio" checked="checked" /> All roles
					<?php } else { ?>
						<input type="radio" name="cmsar_menu_item[<?php echo $item->ID; ?>][all_selected]" value="all_roles" class="cmsar_all_selected_radio" /> All roles
					<?php } ?>
				</div>
				<div>
					<?php if($menuitem['all_selected'] == 'selected_roles') { ?>
						<input type="radio" name="cmsar_menu_item[<?php echo $item->ID; ?>][all_selected]" value="selected_roles" class="cmsar_all_selected_radio" checked="checked" /> Specific roles only
					<?php } else { ?>
						<input type="radio" name="cmsar_menu_item[<?php echo $item->ID; ?>][all_selected]" value="selected_roles" class="cmsar_all_selected_radio" /> Specific roles only
					<?php } ?>
				</div>
				<?php
				$displayroles = 'none';
				if($menuitem['all_selected'] == 'selected_roles') {
					$displayroles = 'block';
				}
				?>
				<div style="display:<?php echo $displayroles; ?>" class="cmsar_menu_roles">
					<?php
					foreach($roles as $rolekey=>$roleval) {
						echo '<div>';
							if(in_array($rolekey, $menuitem['roles'])) {
								echo '<input type="checkbox" name="cmsar_menu_item['.$item->ID.'][roles][]" value="'.$rolekey.'" checked="checked" /> '.$roleval;
							} else {
								echo '<input type="checkbox" name="cmsar_menu_item['.$item->ID.'][roles][]" value="'.$rolekey.'" /> '.$roleval;
							}
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
    }
	
	static function nav_menu_fields_update($menu_id, $item_id) {
		$item = $_POST['cmsar_menu_item'][$item_id];
		if($item) {
			if(isset($item['islogouturl'])) {
				$item['islogouturl'] = 'yes';
			} else {
				$item['islogouturl'] = 'no';
			}
			if($item['visible_for'] == 'everyone' || $item['visible_for'] == 'loggedout') {
				$item['all_selected'] = 'all_roles';
			}
			if($item['all_selected'] == 'all_roles') {
				$item['roles'] = array();
			} else {
				$item['roles'] = array_unique($item['roles']);
			}
			update_post_meta($item_id, 'cmsar_menu_items', $item);
		}
	}
}