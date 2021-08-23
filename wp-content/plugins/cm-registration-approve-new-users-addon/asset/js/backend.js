jQuery(function($) {
	
	$('.cmreganu_category_icon_choose').click(function() {
		var btn = $(this);
		btn.parents('.cmreganu_category_icon').find('.cmreganu_category_icon_list').show();
		$('.cmreganu_category_icon_list img').css('cursor', 'pointer');
	});
	
	$('.cmreganu_category_icon_list img').click(function() {
		var obj = $(this);
		obj.parents('.cmreganu_category_icon').find('.cmreganu_category_icon_list').hide();
		obj.parents('.cmreganu_category_icon').find('.cmreganu_category_icon_image').attr('src', obj.attr('src'));
		obj.parents('.cmreganu_category_icon').find('input[name=cmreganu_category_icon]').val(obj.attr('src'));
	});
	
	// Settings tabs handler
	$('.cmreganu-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmreganu-settings-tabs a.current').removeClass('current');
		$('.cmreganu-settings-tabs a[href="#tab-'+ match[1] +'"]').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmreganu-settings-tabs a[href="'+ location.hash +'"]').click();
	} else {
		$('.cmreganu-settings-tabs li:first-child a').click();
	}
	
	
	// Access custom cap handler
	var settingsAccessCustomCapListener = function() {
		var obj = $(this);
		var nextField = obj.parents('tr').first().next();
		if ('cmreganu_capability' == obj.val()) {
			nextField.show();
		} else {
			nextField.hide();
		}
	};
	$('select[name^=cmreganu_access_map_]').change(settingsAccessCustomCapListener);
	$('select[name^=cmreganu_access_map_]').change();
	
	$('.cmreganu-admin-notice .cmreganu-dismiss').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var btn = $(this);
		var data = {action: btn.data('action'), nonce: btn.data('nonce'), id: btn.data('id')};
		$.post(btn.attr('href'), data, function(response) {
			btn.parents('.cmreganu-admin-notice').fadeOut('slow');
		});
	});
	
	
	$('.cmreganu-code-generate').click(function() {
		var input = $(this).parents('.cmreganu-code').first().find('.cmreganu-code-input');
		var code = Math.floor(Math.random()*9007199254740991).toString(26);
		input.val(code);
	});
	
	
	$('.cmreganu-extra-fields-add-btn').click(function() {
		var btn = $(this);
		var wrapper = btn.parents('td').first();
		var template = wrapper.find('.cmreganu-extra-field').first().clone(true);
		var last = wrapper.find('.cmreganu-extra-field').last().find('input').first();
		var lastName = last.attr('name');
		var lastNumber = parseInt(lastName.match(/\[([0-9]+)\]/)[1]);
		var newNumber = lastNumber + 1;
		template.find('input, select').each(function() {
			var input = $(this);
			var name = input.attr('name');
			name = name.replace('[0]', '['+ (newNumber) +']');
			input.attr('name', name);
		});
		btn.before(template);
	});
	
	
	$('.cmreganu-extra-field-delete-btn').click(function() {
		var btn = $(this);
		var item = btn.parents('.cmreganu-extra-field').first();
		item.fadeOut(function() {
			item.remove();
		});
	});
	
	
	$('#cmreganu-invitation-form').submit(function() {
		var iframe = $("#" + $(this).attr('target'));
		$('html,body').animate({
	        scrollTop: iframe.offset().top
	       },
	       'fast');
		var submit = $(this).find('input[type=submit]');
		submit.hide();
		var loader = $('<div/>', {"class":"cmreg-loader"});
		submit.after(loader);
		iframe.on('load', function() {
			loader.remove();
			submit.show();
		});
	});
	
	
	$('.cmreganu-dont-send-email input').change(function() {
		var emailBody = $('.cmreganu-email-body');
		if (this.checked) {
			emailBody.hide();
		} else {
			emailBody.show();
		}
	});
	
	
	$('.cmreg-confirm-btn').click(function(ev) {
		return confirm('Are you sure?');
	});
	
	
});