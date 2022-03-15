<?php
namespace com\cminds\siteaccessrestriction\controller;

use com\cminds\siteaccessrestriction\helper\RestrictedResource;
use com\cminds\siteaccessrestriction\helper\PostTypeRestriction;
use com\cminds\siteaccessrestriction\helper\RestrictionSettings;
use com\cminds\siteaccessrestriction\metabox\PostAccessBox;
use com\cminds\siteaccessrestriction\helper\PostRestriction;
use com\cminds\siteaccessrestriction\model\Settings;
use com\cminds\siteaccessrestriction\model\Post;
use com\cminds\siteaccessrestriction\App;

class PostController extends Controller {
	
	static $actions = array(
		'template_redirect',
		'plugins_loaded',
		'manage_posts_custom_column' => array('args' => 2),
		//'bulk_edit_custom_box' => array('args' => 2, 'method' => 'quick_edit_custom_box'),
		'quick_edit_custom_box' => array('args' => 2),
		'save_post' => array('args' => 2),
		'edit_attachment' => array('args' => 1)
	);

	static $filters = array(
		'cmsar_single_content' => array('priority' => PHP_INT_MAX ),
		'the_content' => array('priority' => PHP_INT_MAX ),
		'the_excerpt' => array('priority' => PHP_INT_MAX ),
		'manage_posts_columns' => array('args' => 2),
	);
	
	static $ajax = array(
		//'cmacc_save_bulk_edit',
		'cmacc_get_post_restriction',
	);
	
	static function plugins_loaded() {
		$postTypes = Settings::getPostTypesOptions();
		foreach ($postTypes as $postType => $label) {
			add_action('wp_ajax_save_bulk_edit_' . $postType, array(get_called_class(), 'saveBulkEdit'));
		}
	}
	
	static function saveBulkEdit() {
		$post_ids = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
		//var_dump($post_ids);
	}
	
	/**
	 * Redirects from the restricted post if post is restricted and the URL has been defined in settings.
	 */
	static function template_redirect() {
		global $wp_query;

		$restrictMessageForArchive = Settings::getOption(Settings::OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE);

		if($restrictMessageForArchive) {
			if (App::isLicenseOk() AND !is_admin() AND $wp_query AND $wp_query->is_singular() AND !empty($wp_query->posts)) {
				if ($post = reset($wp_query->posts) AND $postObj = Post::getInstance($post)) {
					$postRestriction = new PostRestriction($postObj);
					if (!$postRestriction->canViewCategory()){
						static::accessDeniedRedirect();
					}
					if (!$postRestriction->canViewDays()) {
						static::accessDeniedRedirect('days');
					}
					if (!$postRestriction->canView()) {
						static::accessDeniedRedirect();
					}
				}
			}
		} else {
			if (App::isLicenseOk() AND !is_admin() AND $wp_query AND !empty($wp_query->posts)) {
				if ($post = reset($wp_query->posts) AND $postObj = Post::getInstance($post)) {
					$postRestriction = new PostRestriction($postObj);
					if (!$postRestriction->canViewCategory()){
						static::accessDeniedRedirect();
					}
					if (!$postRestriction->canViewDays()) {
						//static::accessDeniedRedirect('days');
					}
					if (!$postRestriction->canView()) {
						static::accessDeniedRedirect();
					}
				}
			}
		}
		
	}
	
	static function accessDeniedRedirect($restriction = 'global') {
		global $wp_query;

		// do not redirect if 'exerpt' option selected
		if (Settings::getOption(Settings::OPTION_RESTRICT_ACCESS_TYPE) == 'excerpt') {
			return;
		}

		if (!is_user_logged_in()) {
			// Login page URL
			$url = Settings::getOption(Settings::OPTION_LOGIN_REDIRECT_URL);
		}
        if ($restriction == 'days') {
            $post_id = Settings::getOption(Settings::OPTION_DAYS_ACCESS_DENIED_REDIRECT_URL);
			if($post_id != '' && $post_id != '0') {
				$url = get_permalink($post_id);
			}
        }
		if (empty($url)) {
			// Access denied page URL if no login page specified or user is already logged-in
			$url = Settings::getOption(Settings::OPTION_ACCESS_DENIED_REDIRECT_URL);
		}
		
		if(!$wp_query->is_singular()) {
			if (empty($url)) {
				$url = get_home_url();
			}
		}
		
		$restrict_homepage_latest_posts = Settings::getOption(Settings::OPTION_RESTRICT_HOMEPAGE_LATEST_POSTS);
		$show_on_front = get_option('show_on_front', 'posts');
		if (!empty($url)) {

			if($restrict_homepage_latest_posts == '1' && $show_on_front == 'posts' && (is_front_page() || is_home())) {
				// nothing
			} else {
				// Redirect only if any URL has been specified

				$url = apply_filters(App::prefix('_before_redirect'), ['url' => $url]);
				if(is_array($url)) {
					wp_safe_redirect(static::addParamsToUrl(implode("", $url)));
				} else {
					wp_safe_redirect(static::addParamsToUrl($url));
				}
				exit;
			}
		}
	}
	
	static function cmsar_single_content($content) {
		global $post;
		if (App::isLicenseOk() AND $post AND $postObj = Post::getInstance($post)) {

			$restrictMessageForArchive = Settings::getOption(Settings::OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE);

			$postRestriction = new PostRestriction($postObj);
            if (!$postRestriction->canViewCategory() || !$postRestriction->canViewDays() || !$postRestriction->canView()) {

				$postRestictMode = get_post_meta($post->ID, 'cmacc_restriction_view_mode', true);

				if (!$postRestictMode OR $postRestictMode == 'global')
					$postRestictMode = Settings::getOption(Settings::OPTION_RESTRICT_ACCESS_TYPE);

				if ($postRestictMode == 'full') {
					
					if(is_archive() && $restrictMessageForArchive) {

						/*
						$totalWordsCount = str_word_count($content);
						$excerpt = strip_shortcodes($content);
						$excerptLength = $totalWordsCount / 20; // 5% of post
						$excerpt = wp_trim_words( $excerpt, $excerptLength, '...' );
						*/

						$str = wpautop($content);
						$str = substr( $str, 0, strpos( $str, '</p>' ) + 4 );
						$str = strip_tags($str, '<a><strong><em>');
						$excerpt = '<p>' . $str . '</p>';

						$content = '<div class="cmacc-excerpt-for-archive">';
						$content .= $excerpt;
						$content .= self::accessDeniedView($restrictMessageForArchive);
						$content .= '</div>';
					} else {
						$content = self::accessDeniedView($restrictMessageForArchive);
					}

				} elseif ($postRestictMode == 'excerpt') {

					/*
					$totalWordsCount = str_word_count($content);
					$excerpt = strip_shortcodes($content);
					$excerptLength = $totalWordsCount / 5; // 20% of post
					$excerpt = wp_trim_words( $excerpt, $excerptLength, '...' );
					*/
					
					$str = wpautop($content);
					$str = substr( $str, 0, strpos( $str, '</p>' ) + 4 );
					$str = strip_tags($str, '<a><strong><em>');
					$excerpt = '<p>' . $str . '</p>';

					$content = '<div class="cmacc-excerpt">';
					$content .= $excerpt;
					$content .= '</div>';
					$content .= self::accessDeniedFadeView($restrictMessageForArchive);

				} elseif ($postRestictMode == 'shortcode') {

				}

            }
		}
		return $content;
	}

	/**
	 * Replace the post content if post has been restricted.
	 * 
	 * @param string $content
	 * @return string
	 */
	static function the_content($content) {
		$restrict_with_custom_filter = Settings::getOption(Settings::OPTION_RESTRICT_WITH_CUSTOM_FILTER);
		if($restrict_with_custom_filter == '1') {
			return $content;
		}
		global $post;
		if (App::isLicenseOk() AND $post AND $postObj = Post::getInstance($post)) {

			$restrictMessageForArchive = Settings::getOption(Settings::OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE);

			$postRestriction = new PostRestriction($postObj);
            if (!$postRestriction->canViewCategory() || !$postRestriction->canViewDays() || !$postRestriction->canView()) {

				$postRestictMode = get_post_meta($post->ID, 'cmacc_restriction_view_mode', true);

				if (!$postRestictMode OR $postRestictMode == 'global')
					$postRestictMode = Settings::getOption(Settings::OPTION_RESTRICT_ACCESS_TYPE);

				if ($postRestictMode == 'full') {
					
					if(is_archive() && $restrictMessageForArchive) {
						$totalWordsCount = str_word_count($content);
						$excerpt = strip_shortcodes($content);
						$excerptLength = $totalWordsCount / 20; // 5% of post
						$excerpt = wp_trim_words( $excerpt, $excerptLength, '...' );
						$content = '<div class="cmacc-excerpt-for-archive">';
						$content .= $excerpt;
						$content .= self::accessDeniedView($restrictMessageForArchive);
						$content .= '</div>';
					} else {
						$content = self::accessDeniedView($restrictMessageForArchive);
					}

				} elseif ($postRestictMode == 'excerpt') {
					
					$percent = Settings::getOption(Settings::OPTION_RESTRICT_PARTIALLY_CONTENT_PERCENT);
					if($percent === '' || $percent === 0) { $percent = 20; }

					$totalWordsCount = str_word_count($content);
					$excerpt = strip_shortcodes($content);
					//$excerptLength = $totalWordsCount / 5; // 20% of post
					$excerptLength = ($totalWordsCount * $percent / 100);
					$excerpt = wp_trim_words( $excerpt, $excerptLength, '...' );
					$content = '<div class="cmacc-excerpt">';
					$content .= $excerpt;
					$content .= '</div>';
					$content .= self::accessDeniedFadeView($restrictMessageForArchive);

				} elseif ($postRestictMode == 'shortcode') {

				}

            }
		}
		return $content;
	}
	
	static function the_excerpt($content) {
		$restrict_with_custom_filter = Settings::getOption(Settings::OPTION_RESTRICT_WITH_CUSTOM_FILTER);
		if($restrict_with_custom_filter == '1') {
			return $content;
		}
		global $post;
		if (App::isLicenseOk() AND $post AND $postObj = Post::getInstance($post)) {

			$restrictMessageForArchive = Settings::getOption(Settings::OPTION_RESTRICT_MESSAGE_FOR_ARCHIVE);

			$postRestriction = new PostRestriction($postObj);
            if (!$postRestriction->canViewCategory() || !$postRestriction->canViewDays() || !$postRestriction->canView()) {

				$postRestictMode = get_post_meta($post->ID, 'cmacc_restriction_view_mode', true);

				if (!$postRestictMode OR $postRestictMode == 'global')
					$postRestictMode = Settings::getOption(Settings::OPTION_RESTRICT_ACCESS_TYPE);

				if ($postRestictMode == 'full') {
					
					if(is_archive() && $restrictMessageForArchive) {
						$excerpt = $content;
						$content = '<div class="cmacc-excerpt-for-archive">';
						$content .= $excerpt;
						$content .= self::accessDeniedView($restrictMessageForArchive);
						$content .= '</div>';
					} else {
						$content = self::accessDeniedView($restrictMessageForArchive);
					}

				} elseif ($postRestictMode == 'excerpt') {
					
					$excerpt = $content;
					$content = '<div class="cmacc-excerpt">';
					$content .= $excerpt;
					$content .= '</div>';
					$content .= self::accessDeniedFadeView($restrictMessageForArchive);

				} elseif ($postRestictMode == 'shortcode') {

				}

            }
		}
		return $content;
	}

	/**
	 * Returns the access denied block.
	 * 
	 * @return string
	 */
	static function accessDeniedView($restrictMessageForArchive) {
		wp_enqueue_style('cmacc-frontend');
		return self::loadFrontendView('access-denied', compact('restrictMessageForArchive'));
	}

	/**
	 * Returns the access denied fade block.
	 * 
	 * @return string
	 */
	static function accessDeniedFadeView($restrictMessageForArchive) {
		wp_enqueue_style('cmacc-frontend');
		return self::loadFrontendView('access-denied-fade', compact('restrictMessageForArchive'));
	}

	/**
	 * Append the parameters to the URL address.
	 * 
	 * Replace the parameter's placeholders with its values.
	 * 
	 * @param string $url
	 * @return mixed
	 */
	static function addParamsToUrl($url) {
		return str_replace('%backlink%', urlencode(site_url($_SERVER['REQUEST_URI'])), $url);
	}
	
	static function manage_posts_columns($columns, $postType) {
		$columns['cmacc'] = 'Access';
		return $columns;
	}
	
	static function manage_posts_custom_column($columnName, $postId) {
		if ('cmacc' == $columnName) {
			if ($post = Post::getInstance($postId)) {
				$resource = new PostRestriction($post);
				echo esc_html($resource->getRestrictionLabel($short = true));
			}
		}
	}
	
	static function quick_edit_custom_box($columnName, $postType) {
		if ('cmacc' == $columnName) {
			$content = RestrictionSettings::displaySettings(
				$restrictedResource = null,
				$restrictionFieldName = 'cmacc_post_restriction',
				$roleFieldName = 'cmacc_post_roles',
				$allowedUsersFieldName = 'cmacc_post_allowed_users',
				$notAllowedUsersFieldName = 'cmacc_post_not_allowed_users',
				$daysFieldName = 'cmacc_post_days',
				$daysFromFirstAccessFieldName = 'cmacc_post_days_from_first_access',
				$fromDateFieldName = 'cmacc_post_from_date',
				$toDateFieldName = 'cmacc_post_to_date',
				new PostTypeRestriction($postType)
			);
			echo self::loadBackendView('quick-edit', compact('content'));
		}
	}
	
	static function cmacc_save_bulk_edit() {
		if (!empty($_POST['post_ids'])) {
			
			$postIds = $_POST['post_ids'];
			$restriction = filter_input(INPUT_POST, 'restriction') ?: RestrictedResource::RESTRICTION_GLOBAL;
			$roles = (empty($_POST['roles']) ? array() : $_POST['roles']);

			$allowedUsers = (empty($_POST['allowed_users']) ? array() : $_POST['allowed_users']);
			$notAllowedUsers = (empty($_POST['not_allowed_users']) ? array() : $_POST['not_allowed_users']);

            $days = (empty($_POST['days']) ? 0 : $_POST['days']);
            $daysFromFirstAccess = (empty($_POST['days_from_first_access']) ? 0 : $_POST['days_from_first_access']);
            $fromDate = (empty($_POST['from_date']) ? date('Y-m-d') : $_POST['from_date']);
            $toDate = (empty($_POST['to_date']) ? date('Y-m-d') : $_POST['to_date']);
			
			foreach ($postIds as $postId) {
				//var_dump($postId);
				if ($post = Post::getInstance($postId)) {
					$resource = new PostRestriction($post);
					//var_dump($restriction);var_dump($roles);exit;
					$resource->setRestriction($restriction);
					$resource->setAllowedRoles($roles);

					$resource->setAllowedUsers($allowedUsers);
					$resource->setNotAllowedUsers($notAllowedUsers);

					$resource->setRestrictedDays($days);
					$resource->setRestrictedDaysFromFirstAccess($daysFromFirstAccess);
					$resource->setRestrictedFromDate($fromDate);
					$resource->setRestrictedToDate($toDate);
				}
			}
		}
	}
	
	/**
	 * Quick-edit save post
	 * 
	 * @param int $post_id
	 * @param object $post
	 */
	static function save_post($postId, $post) {
		// don't save for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		// dont save for revisions
		if ( isset( $post->post_type ) && $post->post_type == 'revision' ) return;
		
		$restriction = filter_input(INPUT_POST, 'cmacc_post_restriction') ?: RestrictedResource::RESTRICTION_GLOBAL;
		$roles = (empty($_POST['cmacc_post_roles']) ? array() : $_POST['cmacc_post_roles']);

		$allowedUsers = (empty($_POST['cmacc_post_allowed_users']) ? array() : $_POST['cmacc_post_allowed_users']);
		$notAllowedUsers = (empty($_POST['cmacc_post_not_allowed_users']) ? array() : $_POST['cmacc_post_not_allowed_users']);

        $days = (empty($_POST['cmacc_post_days']) ? array() : $_POST['cmacc_post_days']);
        $daysFromFirstAccess = (empty($_POST['cmacc_post_days_from_first_access']) ? array() : $_POST['cmacc_post_days_from_first_access']);
        $fromDate = (empty($_POST['cmacc_post_from_date']) ? array() : $_POST['cmacc_post_from_date']);
        $toDate = (empty($_POST['cmacc_post_to_date']) ? array() : $_POST['cmacc_post_to_date']);
		
		if(isset($_POST['cmacc_post_days'])) {
			if ($post = Post::getInstance($postId)) {
				$resource = new PostRestriction($post);
				$resource->setRestriction($restriction);
				$resource->setAllowedRoles($roles);
				$resource->setAllowedUsers($allowedUsers);
				$resource->setNotAllowedUsers($notAllowedUsers);
				$resource->setRestrictedDays($days);
				$resource->setRestrictedDaysFromFirstAccess($daysFromFirstAccess);
				$resource->setRestrictedFromDate($fromDate);
				$resource->setRestrictedToDate($toDate);
			}
		}
	}

	static function edit_attachment($postId) {
		$post = get_post($postId);
		// don't save for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		// dont save for revisions
		if ( isset( $post->post_type ) && $post->post_type == 'revision' ) return;
		
		$restriction = filter_input(INPUT_POST, 'cmacc-restriction') ?: RestrictedResource::RESTRICTION_GLOBAL;
		$roles = (empty($_POST['cmacc-roles']) ? array() : $_POST['cmacc-roles']);

		$allowedUsers = (empty($_POST['cmacc-allowed-users']) ? array() : $_POST['cmacc-allowed-users']);
		$notAllowedUsers = (empty($_POST['cmacc-not-allowed-users']) ? array() : $_POST['cmacc-not-allowed-users']);

        $days = (empty($_POST['cmacc-days']) ? array() : $_POST['cmacc-days']);
        $daysFromFirstAccess = (empty($_POST['cmacc-days-from-first-access']) ? array() : $_POST['cmacc-days-from-first-access']);
        $fromDate = (empty($_POST['cmacc-from-date']) ? array() : $_POST['cmacc-from-date']);
        $toDate = (empty($_POST['cmacc-to-date']) ? array() : $_POST['cmacc-to-date']);
		
		if(isset($_POST['cmacc-days'])) {
			if ($post = Post::getInstance($postId)) {
				$resource = new PostRestriction($post);
				$resource->setRestriction($restriction);
				$resource->setAllowedRoles($roles);

				$resource->setAllowedUsers($allowedUsers);
				$resource->setNotAllowedUsers($notAllowedUsers);

				$resource->getRestrictedDays($days);
				$resource->getRestrictedDaysFromFirstAccess($daysFromFirstAccess);
				$resource->getRestrictedFromDate($fromDate);
				$resource->getRestrictedToDate($toDate);
			}
		}
	}
	
	static function cmacc_get_post_restriction() {
		$postId = filter_input(INPUT_POST, 'postId');
		if ($postId AND $post = Post::getInstance($postId)) {
			$response = array();
			$resource = new PostRestriction($post);
			$response['restriction'] = $resource->getRestriction();
			$response['roles'] = $resource->getAllowedRoles();
			$response['allowed_users'] = $resource->getAllowedUsers();
			$response['not_allowed_users'] = $resource->getNotAllowedUsers();
			$response['days'] = $resource->getRestrictedDays();
			$response['days_from_first_access'] = $resource->getRestrictedDaysFromFirstAccess();
			$response['from_date'] = $resource->getRestrictedFromDate();
			$response['to_date'] = $resource->getRestrictedToDate();
			header('content-type: application/json');
			echo json_encode($response);
			exit;
		} else {
			http_response_code(500);
			exit;
		}
	}
	
}