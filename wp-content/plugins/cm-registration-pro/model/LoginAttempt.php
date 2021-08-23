<?php
namespace com\cminds\registration\model;

/**
 * Single statistics log record for an event when a user (even partially) watched a video.
 *
 */
class LoginAttempt extends CommentType {
	
	const COMMENT_TYPE = 'cmreg_login_attempt';
	
	static function create($ip = null) {
		
		if (is_null($ip)) $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		
		$comment = new static(array(
			'comment_post_ID'      => 0,
			'comment_content'      => '',
			'comment_approved'     => 1,
			'comment_date'         => current_time('mysql'),
			'comment_type'         => static::COMMENT_TYPE,
			//'comment_parent'     => 0,
			//'comment_karma'      => 0,
			//'user_id'            => $userId,
			'comment_author_IP'    => $ip,
		));
		if ($comment->save()) {
			return $comment;
		}
	}
	
	static function isLimitExceeded($ip = null) {
		$count = static::getCurrentAttemptsNumber($ip);
		$max = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_NUMBER);
		return (intval($count) >= intval($max));
	}
	
	static function getCurrentAttemptsNumber($ip = null) {
		global $wpdb;
		if (is_user_logged_in()) { return 0; }
		if (is_null($ip)) $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		
		$interval = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_INTERVAL_MINUTES);
		$date = Date('Y-m-d H:i:s', strtotime(current_time('mysql')) - $interval * 60);
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_author_IP = %s AND comment_date > %s AND comment_type = %s",
				$ip, $date, static::COMMENT_TYPE);
		$count = $wpdb->get_var($sql);
		
		return $count;
		
	}
	
	static function whenTryAgainMinutes($ip = null) {
		global $wpdb;
		if (is_user_logged_in()) { return 0; }
		if (is_null($ip)) $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		
		$max = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_NUMBER);
		$sql = $wpdb->prepare("SELECT comment_date FROM $wpdb->comments WHERE comment_author_IP = %s AND comment_type = %s ORDER BY comment_ID DESC LIMIT %d",
				$ip, static::COMMENT_TYPE, $max);
		$dates = $wpdb->get_col($sql);
		
		$date = end($dates);
		$limitMinutes = Settings::getOption(Settings::OPTION_LOGIN_LIMIT_ATTEMPTS_INTERVAL_MINUTES);
		
		$min = ceil((strtotime($date) + $limitMinutes*60 - strtotime(current_time('mysql')))/60);
		return $min;
	}

}