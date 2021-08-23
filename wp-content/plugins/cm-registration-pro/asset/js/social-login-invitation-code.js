jQuery(function($) {
	
	var makeRequest = function(form, code) {
		
		var buttons = form.find('input[type=submit], input[type=button]');
		buttons.prop('disabled', true).css('visibility', 'hidden');
		
		var loader = $('<div>', {"class": "cmreg-loader-bar"});
		form.append(loader);
		
		if (typeof code == 'undefined') code = '';
		var data = {action: 'cmreg_social_login_invitation_code', nonce: form.data('nonce'), code: code, cacheKey: form.data('cacheKey')};
		$.post(form.attr('action'), data, function(response) {
			
			if (response.success != 1) {
				buttons.prop('disabled', false).css('visibility', 'visible');
			}
			
			loader.remove();
			
			CMREG.Utils.toast(response.msg);
			if (typeof response.redirectUrl == 'string') {
				location.href = response.redirectUrl;
			}
			
		});
		
	};
	
	$('.cmreg-social-login-invitcode-form').submit(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		
		var form = $(this);
		
		var code = form.find('.cmreg-invitation-code-field').val();
		makeRequest(form, code);
		
	});
	
});