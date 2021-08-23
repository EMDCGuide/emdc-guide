jQuery(function($) {
	
	if (wp && wp.heartbeat) {
		wp.heartbeat.interval( location.href.indexOf('cminds') != -1 ? 'fast' : 'slow' );
		//wp.heartbeat.interval( 'fast' );
	}
	
	/*
	$(document).on('heartbeat-send', function(e, data) {
		data['cmppp_check_post'] = CMPPP_Logout_Hearbeat.postId;
	});
	*/
	
	$(document).on( 'heartbeat-tick', function(e, data) {
		if (data['wp-auth-check'] !== true) {
			location.reload();
		}
	});
	
});