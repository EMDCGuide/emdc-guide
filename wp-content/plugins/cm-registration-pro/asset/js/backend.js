jQuery(function($) {
	
	$('.cmreg_category_icon_choose').click(function() {
		var btn = $(this);
		btn.parents('.cmreg_category_icon').find('.cmreg_category_icon_list').show();
		$('.cmreg_category_icon_list img').css('cursor', 'pointer');
	});
	
	$('.cmreg_category_icon_list img').click(function() {
		var obj = $(this);
		obj.parents('.cmreg_category_icon').find('.cmreg_category_icon_list').hide();
		obj.parents('.cmreg_category_icon').find('.cmreg_category_icon_image').attr('src', obj.attr('src'));
		obj.parents('.cmreg_category_icon').find('input[name=cmreg_category_icon]').val(obj.attr('src'));
	});
	
	// Settings tabs handler
	$('.cmreg-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmreg-settings-tabs a.current').removeClass('current');
		$('.cmreg-settings-tabs a[href="#tab-'+ match[1] +'"]').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmreg-settings-tabs a[href="'+ location.hash +'"]').click();
	} else {
		$('.cmreg-settings-tabs li:first-child a').click();
	}
	
	
	// Access custom cap handler
	var settingsAccessCustomCapListener = function() {
		var obj = $(this);
		var nextField = obj.parents('tr').first().next();
		if ('cmreg_capability' == obj.val()) {
			nextField.show();
		} else {
			nextField.hide();
		}
	};
	$('select[name^=cmreg_access_map_]').change(settingsAccessCustomCapListener);
	$('select[name^=cmreg_access_map_]').change();
	
	$('.cmreg-admin-notice .cmreg-dismiss').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var btn = $(this);
		var data = {action: btn.data('action'), nonce: btn.data('nonce'), id: btn.data('id')};
		$.post(btn.attr('href'), data, function(response) {
			btn.parents('.cmreg-admin-notice').fadeOut('slow');
		});
	});
	
	
	$('.cmreg-code-generate').click(function() {
		var input = $(this).parents('.cmreg-code').first().find('.cmreg-code-input');
		var code = Math.floor(Math.random()*9007199254740991).toString(26);
		input.val(code);
	});
	
	
	$('.cmreg-extra-fields-add-btn').click(function() {
		var btn = $(this);
		var wrapper = btn.parents('td').first();
		var template = wrapper.find('.cmreg-extra-field').first().clone(true);
		var last = wrapper.find('.cmreg-extra-field').last().find('input').first();
		var lastName = last.attr('name');
		var lastNumber = parseInt(lastName.match(/\[([0-9]+)\]/)[1]);
		var newNumber = lastNumber + 1;
		template.find('input, select').each(function() {
			var input = $(this);
			var name = input.attr('name');
			name = name.replace('[0]', '['+ (newNumber) +']');
			input.attr('name', name);
			input.val('');
		});
		btn.before(template);
	});
	
	
	$('.cmreg-extra-field-delete-btn').click(function() {
		var btn = $(this);
		var item = btn.parents('.cmreg-extra-field').first();
		item.fadeOut(function() {
			item.remove();
		});
	});
	
	
	$('.cmreg-manually-activation').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var btn = $(this);
		var loader = $('<div/>', {"class": "cmreg-loader"});
		btn.after(loader);
		btn.hide();
		var data = {action: 'cmreg_manually_activation', nonce: btn.data('nonce'), userId: btn.data('userId')};
		$.post(ajaxurl, data, function(response) {
			loader.remove();
			btn.show();
			location.reload();
			//$('.cmreg-manually-activation-response').text(response);
		});
	});


	$('.cmreg-resend-activation-email').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var btn = $(this);
		var loader = $('<div/>', {"class": "cmreg-loader"});
		btn.after(loader);
		btn.hide();
		var data = {action: 'cmreg_resend_activation_email', nonce: btn.data('nonce'), userId: btn.data('userId')};
		$.post(ajaxurl, data, function(response) {
			loader.remove();
			btn.show();
			$('.cmreg-resend-activation-email-response').text(response);
		});
	});
	
	// Settings list key-val
	$('.cmreg-list-key-value-row input[type=button]').click(function() {
		$(this).parents('.cmreg-list-key-value-row').remove();
	});
	$('.cmreg-list-key-value-add-btn').click(function() {
		var btn = $(this);
		var wrapper = btn.parents('.cmreg-list-key-value');
		var item = wrapper.find('.cmreg-list-key-value-row:first-child').clone(true);
		var num = parseInt(wrapper.find('.cmreg-list-key-value-row').last().attr('data-num')) + 1;
		btn.before(item);
		item.attr('data-num', num);
		item.find('select').each(function() {
			var select = $(this);
			var fieldName = wrapper.data('name') + '[' + num + ']' + select.attr('name').replace('template', '');
			select.attr('name', fieldName);
		});
	});

});