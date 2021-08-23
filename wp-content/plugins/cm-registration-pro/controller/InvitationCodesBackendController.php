<?php
namespace com\cminds\registration\controller;

use com\cminds\registration\model\User;
use com\cminds\registration\model\Settings;
use com\cminds\registration\model\InvitationCode;
use com\cminds\registration\helper\CSVHelper;
use com\cminds\registration\model\S2MembersLevels;

class InvitationCodesBackendController extends Controller {
	
	const ACTION_DOWNLOAD_INVITED_USERS_CSV = 'cmreg_download_invited_users_csv';
	const ACTION_DELETE_ALL_INVITATION_CODES = 'cmreg_delete_all_invitation_codes';
	const ACTION_DOWNLOAD_CSV = 'cmreg_download_csv';
	const COLUMN_INVITATION_CODE = 'cmreg_invit_code';
	const PARAM_ACTION = 'cmreg_action';
	const PARAM_USERS_INVIT_CODE = 'cmreg_invitation_code_id';
	
	static $filters = array(
		'manage_users_columns',
		'manage_users_custom_column' => array('args' => 3),
		'posts_search' => array('args' => 2),
		'post_row_actions' => array('args' => 2),
	);
	static $actions = array(
		'edit_user_profile' => array('args' => 1, 'method' => 'show_user_profile'),
		'show_user_profile' => array('args' => 1),
		'admin_init',
		'pre_get_users' => array('args' => 1),
		'in_admin_footer',
	);
	
	
	static function bootstrap() {
		parent::bootstrap();
		add_filter('manage_edit-' . InvitationCode::POST_TYPE .'_columns', array(__CLASS__, 'adminColumnsHeader'));
		add_action('manage_' . InvitationCode::POST_TYPE . '_posts_custom_column', array(__CLASS__, 'adminColumns'), 10, 2);
	}
		
	
	static function manage_users_columns($columns) {
		if (Settings::getOption(Settings::OPTION_DASHBOARD_USERS_COLUMN_INVIT_CODE_ENABLE)) {
			$columns[static::COLUMN_INVITATION_CODE] = 'Invitation Code';
		}
		return $columns;
	}
	
	
	static function manage_users_custom_column($val, $columnName, $userId) {
		if (static::COLUMN_INVITATION_CODE == $columnName AND Settings::getOption(Settings::OPTION_DASHBOARD_USERS_COLUMN_INVIT_CODE_ENABLE)) {
			if ($code = InvitationCode::getByUser($userId)) {
				$val = static::loadBackendView('user-column-invitation-code', compact('code'));
			} else {
				$val = '--';
			}
		}
		return $val;
	}
	
	
	static function show_user_profile($user) {
		$userId = $user->ID;
		$code = InvitationCode::getByUser($userId);
		if ($code) {
			$content = static::loadBackendView('user-column-invitation-code', compact('code'));;
		} else {
			$content = 'No invitation code used during registration.';
		}
		echo static::loadBackendView('user-profile-invitation-code', compact('code', 'userId', 'content'));
	}
	
	
	static function posts_search($search, \WP_Query $wp_query) {
		global $pagenow, $wpdb;
		
		if (is_admin() AND $pagenow == 'edit.php' AND InvitationCode::POST_TYPE == filter_input(INPUT_GET, 'post_type')) {
			$s = $wp_query->get('s');
			if (strlen($s)) {
			
				$pos = strpos($search, ') OR (');
				if ($pos !== false) {
					// Add new search condition - search for a code string in post meta value
					$condition = $wpdb->prepare("cmreg_meta_code.meta_value = %s", $s);
					$search = substr($search, 0, $pos) . ') OR (' . $condition . substr($search, $pos, strlen($search));
				}

				// Add join filter
				$posts_join = function($join, \WP_Query $wp_query) use ($wpdb, &$posts_join) {
					$join .= PHP_EOL . $wpdb->prepare(" JOIN $wpdb->postmeta cmreg_meta_code
							ON cmreg_meta_code.post_id = ID AND cmreg_meta_code.meta_key = %s ", InvitationCode::META_CODE_STRING);
					remove_filter('posts_join', $posts_join, 10, 2);
					return $join;
				};
				add_filter('posts_join', $posts_join, 10, 2);
				
			}
			
		}
		return $search;
	}
	
	/**
	 * Filter users by invitation code
	 * @param \WP_User_Query $query
	 */
	static function pre_get_users(\WP_User_Query $query) {
		global $pagenow;
		if (is_admin() AND $pagenow == 'users.php' AND !empty($_GET[static::PARAM_USERS_INVIT_CODE])) {
			$query->set('meta_key', User::META_INVITATION_CODE);
			$query->set('meta_value', $_GET[static::PARAM_USERS_INVIT_CODE]);
		}
	}
	
	
	static function admin_init() {
		
		$action = filter_input(INPUT_GET, static::PARAM_ACTION);
		switch ($action) {
			case static::ACTION_DOWNLOAD_INVITED_USERS_CSV:
				static::downloadInvitedUsersCSV();
				break;
			case static::ACTION_DOWNLOAD_CSV:
				static::downloadInvitationCodesCSV();
				break;
			case static::ACTION_DELETE_ALL_INVITATION_CODES:
				static::deleteAllInvitationCodes();
				break;
		}
		
	}
	
	
	static protected function deleteAllInvitationCodes() {
		global $wpdb;
		$wpdb->query("DELETE posts,pt,pm FROM $wpdb->posts as posts LEFT JOIN $wpdb->term_relationships as pt ON pt.object_id = posts.ID LEFT JOIN $wpdb->postmeta as pm ON pm.post_id = posts.ID WHERE posts.post_type = 'cmreg_invitcode'");
	}


	static protected function downloadInvitedUsersCSV() {
		
		global $wpdb;
		$sql = $wpdb->prepare("SELECT
				IFNULL(IFNULL(cm_str.meta_value, um_codestr.meta_value), '[deleted]') AS invitation_code_string,
				u.ID AS user_id, u.user_email, u.display_name, urole.meta_value AS user_role
				
				FROM $wpdb->users u
				
				/* User Role */
				LEFT JOIN $wpdb->usermeta urole ON urole.user_id = u.ID AND urole.meta_key = 'role'
				
				/* Invitation Code */
				JOIN $wpdb->usermeta um_code ON um_code.user_id = u.ID AND um_code.meta_key = %s
				LEFT JOIN $wpdb->posts c ON c.ID = um_code.meta_value AND c.post_type = %s
				LEFT JOIN $wpdb->postmeta cm_str ON c.ID = cm_str.post_id AND cm_str.meta_key = %s
				
				/* Code string backup (optional) */
				LEFT JOIN $wpdb->usermeta um_codestr ON um_codestr.user_id = u.ID AND um_codestr.meta_key = %s
				
				",
				User::META_INVITATION_CODE,
				InvitationCode::POST_TYPE,
				InvitationCode::META_CODE_STRING,
				User::META_INVITATION_CODE_STRING
				);
		
		$users = $wpdb->get_results($sql, ARRAY_A);
		
		$columns = array('invitation_code_string', 'user_id', 'user_email', 'display_name', 'user_role', 'registration_date');
		$data = array_map(function($row) { return array_values($row); }, $users);
		
		$fdata = array();
		if(count($data) > 0) {
			$counter = 0;
			foreach($data as $d) {
				if($d[0] != '') {
					$results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = '".$d[0]."'", ARRAY_A );
					if($results) {
						
						//$post_id = $results[0]['post_id'];
						//$post = get_post($post_id);
						//$post_status = $post->post_status;
						
						$userdata = get_userdata($d[1]);
						//echo "<pre>"; print_r($userdata);

						$user_roles = $userdata->roles;
						$user_registered_datetime = $userdata->data->user_registered;
						$user_registered_date = date('Y-m-d', strtotime($user_registered_datetime));
						$user_registered_time = date('H:i:s', strtotime($user_registered_datetime));
						
						$fdata[$counter][0] = $d[0];
						$fdata[$counter][1] = $d[1];
						$fdata[$counter][2] = $d[2];
						$fdata[$counter][3] = $d[3];
						$fdata[$counter][4] = implode(", ", $user_roles);
						$fdata[$counter][5] = $user_registered_datetime;
						$counter++;
					}
				}
			}
		}
		
		if(count($fdata) > 0) {
			if($_GET['cmreg_download_invited_users_role'] !='') {
				foreach($fdata as $key=>$fd) {
					$roles = explode(", ", $fd['4']);
					if(!in_array($_GET['cmreg_download_invited_users_role'], $roles)) {
						unset($fdata[$key]);
					}
				}
			}
			if($_GET['cmreg_download_invited_users_dateto'] !='') {
				foreach($fdata as $key=>$fd) {
					$datetime = explode(" ", $fd['5']);
					$date = $datetime[0];
					if ($date <= $_GET['cmreg_download_invited_users_dateto']) {
						unset($fdata[$key]);
					}
				}
			}
			if($_GET['cmreg_download_invited_users_datefrom'] !='') {
				foreach($fdata as $key=>$fd) {
					$datetime = explode(" ", $fd['5']);
					$date = $datetime[0];
					if ($date >= $_GET['cmreg_download_invited_users_datefrom']) {
						unset($fdata[$key]);
					}
				}
			}
		}
		

		$final_data = array_merge(array($columns), $fdata);

		//echo "<pre>"; print_r($_GET); echo "</pre>";
		//echo "<pre>"; print_r($final_data); echo "</pre>"; die;

		CSVHelper::downloadCSV($final_data, 'registered-users-' . Date('YmdHis'));
		exit;
		
	}
	
	
	static function post_row_actions($actions, $post) {
		if ( $post->post_type === InvitationCode::POST_TYPE AND $code = InvitationCode::getInstance($post) ) {
			$url = add_query_arg(static::PARAM_USERS_INVIT_CODE, $code->getId(), admin_url('users.php'));
			$actions['cmreg_invited_users'] = sprintf('<a href="%s">%s</a>', esc_attr($url), 'Registered Users');
		}
		return $actions;
	}
	
	
	static protected function downloadInvitationCodesCSV() {
		
		$taxonomy = filter_input(INPUT_GET, 'taxonomy');
		$term = filter_input(INPUT_GET, 'term');
		$query = array();
		
		if ($taxonomy AND strlen($term) > 0) {
			$query = array(
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => $term,
					)
				)
			);
		}
		
		$data = array_map(function(InvitationCode $code) {
			return array($code->getCodeString());
		}, InvitationCode::getAll($query));
			
			$filename = 'invitation-codes-'. Date('YmdHis');
			
			CSVHelper::downloadCSV($data, $filename);
			exit;
			
	}
	
	
	static function in_admin_footer() {
		global $pagenow;
		if (isset($pagenow) AND $pagenow == 'edit.php' AND filter_input(INPUT_GET, 'post_type') == InvitationCode::POST_TYPE) {
			$downloadCSVUrl = add_query_arg(static::PARAM_ACTION, static::ACTION_DOWNLOAD_CSV, $_SERVER['REQUEST_URI']);
			$downloadInvitedUsersCSV = add_query_arg(static::PARAM_ACTION, static::ACTION_DOWNLOAD_INVITED_USERS_CSV, $_SERVER['REQUEST_URI']);
			$deleteAllInvitationCodes = add_query_arg(static::PARAM_ACTION, static::ACTION_DELETE_ALL_INVITATION_CODES, $_SERVER['REQUEST_URI']);
			echo static::loadBackendView('index-footer', compact('downloadCSVUrl', 'downloadInvitedUsersCSV','deleteAllInvitationCodes'));
		}
	}
	
	static function adminColumnsHeader($cols) {
		// 		$lastValue = end($cols);
		// 		$lastKey = key($cols);
		// 		array_pop($cols);
		$cols[InvitationCode::META_EXPIRATION] = 'Expiration';
		$cols[InvitationCode::META_USERS_LIMIT] = 'Users limit';
		if (Settings::getOption(Settings::OPTION_S2MEMBERS_ENABLE)) {
			$cols[InvitationCode::META_S2MEMBERS_LEVEL] = 'S2Members Level';
		}
		$cols[InvitationCode::META_USER_ROLE] = 'User role';
		$cols[InvitationCode::META_CODE_STRING] = 'Invitation code';
		// 		$cols[$lastKey] = $lastValue;
		return $cols;
	}
	
	
	static function adminColumns($columnName, $id) {
		if ($code = InvitationCode::getInstance($id)) {
			switch ($columnName) {
				case InvitationCode::META_CODE_STRING:
					printf('<input type="text" readonly value="%s" />', esc_attr($code->getCodeString()));
					break;
				case InvitationCode::META_S2MEMBERS_LEVEL:
					echo S2MembersLevels::getLevelName($code->getS2MembersLevel());
					break;
				case InvitationCode::META_EXPIRATION:
					if ($date = $code->getExpirationDate()) {
						echo Date('Y-m-d', $date) . ' 00:00:00';
					} else {
						echo 'never';
					}
					break;
				case InvitationCode::META_USERS_LIMIT:
					if ($limit = $code->getUsersLimit()) {
						echo $code->getUsersCount() .'/'. $limit;
					} else {
						echo 'unlimited';
					}
					break;
				case InvitationCode::META_USER_ROLE:
					echo $code->getUserRole();
					break;
			}
		}
	}
	
	
}

