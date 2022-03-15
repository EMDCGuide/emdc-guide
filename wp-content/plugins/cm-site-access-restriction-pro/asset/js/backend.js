jQuery(function($) {

	/*
	$('.cmacc-restriction-settings input[type=radio]').change(function() {
		var wrapper = $(this).parents('.cmacc-restriction-settings');
	 	var rolesContainer = wrapper.find('.cmacc-restrict-roles');
	 	if (this.value == 'roles') {
	 		rolesContainer.show();
	 	} else {
	 		rolesContainer.hide();
	 	}
	});
	*/	

	$('.cmacc-restriction-settings input[type=radio]').change(function() {
		var wrapper = $(this).parents('.cmacc-restriction-settings');
		wrapper.find('.cmacc-checkboxlist-container').hide();
		wrapper.find('.cmacc-restrict-' + this.value).show()
	});

	$('.cmacc_category_icon_choose').click(function() {
		var btn = $(this);
		btn.parents('.cmacc_category_icon').find('.cmacc_category_icon_list').show();
		$('.cmacc_category_icon_list img').css('cursor', 'pointer');
	});

	$('.cmacc_category_icon_list img').click(function() {
		var obj = $(this);
		obj.parents('.cmacc_category_icon').find('.cmacc_category_icon_list').hide();
		obj.parents('.cmacc_category_icon').find('.cmacc_category_icon_image').attr('src', obj.attr('src'));
		obj.parents('.cmacc_category_icon').find('input[name=cmacc_category_icon]').val(obj.attr('src'));
	});

	// Settings tabs handler
	$('.cmacc-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmacc-settings-tabs a.current').removeClass('current');
		$('.cmacc-settings-tabs a[href="#tab-'+ match[1] +'"]').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmacc-settings-tabs a[href="'+ location.hash +'"]').click();
	} else {
		$('.cmacc-settings-tabs li:first-child a').click();
	}

	// Access custom cap handler
	var settingsAccessCustomCapListener = function() {
		var obj = $(this);
		var nextField = obj.parents('tr').first().next();
		if ('cmacc_capability' == obj.val()) {
			nextField.show();
		} else {
			nextField.hide();
		}
	};
	$('select[name^=cmacc_access_map_]').change(settingsAccessCustomCapListener);
	$('select[name^=cmacc_access_map_]').change();

	$('.cmacc-admin-notice .cmacc-dismiss').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var btn = $(this);
		var data = {action: btn.data('action'), nonce: btn.data('nonce'), id: btn.data('id')};
		$.post(btn.attr('href'), data, function(response) {
			btn.parents('.cmacc-admin-notice').fadeOut('slow');
		});
	});

	$('.cmacc-code-generate').click(function() {
		var input = $(this).parents('.cmacc-code').first().find('.cmacc-code-input');
		var code = Math.floor(Math.random()*9007199254740991).toString(26);
		input.val(code);
	});

	$('.cmacc-extra-fields-add-btn').click(function() {
		var btn = $(this);
		var wrapper = btn.parents('td').first();
		var template = wrapper.find('.cmacc-extra-field').first().clone(true);
		var last = wrapper.find('.cmacc-extra-field').last().find('input').first();
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

	$('.cmacc-extra-field-delete-btn').click(function() {
		var btn = $(this);
		var item = btn.parents('.cmacc-extra-field').first();
		item.fadeOut(function() {
			item.remove();
		});
	});

	$('.cmacc-add-url-filter-btn').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var table = $('.cmacc-url-filters .wp-list-table');
		var lastIndex = table.find('tr[data-index]').last().attr('data-index');
		var nextIndex = parseInt(lastIndex)+1;
		var template = table.find('tr.cmacc-template').clone(true, true);
		template.attr('data-index', nextIndex);
		table.find('tbody').append(template);
		template.find('input').each(function() {
			this.name = this.name.replace(/\[[0-9]+\]/, '['+ nextIndex +']');
		});
		template.removeClass('cmacc-template');
	});

	$('body').on('click', '#bulk_edit', function() {
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );
		// get the selected post ids that are being edited
		var $post_ids = [];
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
		// get the data
		var $restriction = $bulk_row.find( '.cmacc-post-restriction input[name="cmacc_post_restriction]"]:checked' ).val();
		var $roles      = [];
		var days = 0;
		if ($restriction === 'roles') {
			var rolesInputs = $bulk_row.find( '.cmacc-post-restriction input[name="cmacc_post_roles[]"]:checked' );
			for (var i=0; i<rolesInputs.length; i++) {
				$roles.push(rolesInputs[i].value);
			}
		}
		if ($restriction === 'days') {
			days = $bulk_row.find( '.cmacc-post-restriction input[name="cmacc_post_days[]"]' );
		}
		var data = {
				action: 'cmacc_save_bulk_edit', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				restriction: $restriction,
				roles: $roles,
				days: days
			};
		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: data
		});
	});

	$('.wp-list-table .editinline').click(function() {
		var tableRow = $(this).parents('tr').first();
		var postId = tableRow.attr('id').split('-')[1];
		$.post(ajaxurl, {action: 'cmacc_get_post_restriction', postId: postId}, function(response) {
			var editRow = $('#edit-' + postId);
			var settingsWrapper = editRow.find('.cmacc-post-restriction');
			var restrictionCheckbox = editRow.find('input[name=cmacc_post_restriction][value="'+ response.restriction +'"]');
			restrictionCheckbox.click();
			for (var i=0; i<response.roles.length; i++) {
				editRow.find('.cmacc-restriction-settings input[name="cmacc_post_roles[]"][value="'+ response.roles[i] +'"]').click();
			}
			if ( response.days.length ) {
				editRow.find('.cmacc-restriction-settings input[name="cmacc_post_days"]').val(response.days);
			}
			settingsWrapper.show();
		});
	});

	$(document).ready(function() {
		$('form .cmacc-checkboxlist-container input').keydown(function(event){
			let input = $(event.target);
			if (input.hasClass('cmacc-user-search') && event.keyCode == 13) {
				if ($(event.target)) {
					getUsers($(event.target))
					event.preventDefault();
					return false;
				}
			}
		});
	});

	$('.cmacc-user-search-button').on('click', function (e) {
		function stopSubmit(e){
			e.preventDefault();
			e.stopPropagation();
		}
		$('form').on('submit', stopSubmit(e));
		var $this = $(this);
		var $input = $this.siblings('.cmacc-user-search');
		getUsers($input);
		$('form').off('submit', stopSubmit(e));
	});

	$('body').on('change', '.cmsar_islogouturl_checkbox', function() {
		if($(this).is(':checked') == true) {
			$(this).closest('.cmsar_menu_options').find('.blocker').css('height', '100px');
			$(this).closest('.cmsar_menu_options').find('.cmsar_visible_for_select').val('loggedin').trigger('change');
			$(this).closest('.cmsar_menu_options').find('.cmsar_all_selected_radio:first').trigger('click');
		} else {
			$(this).closest('.cmsar_menu_options').find('.blocker').css('height', '0px');
		}
	});

	$('body').on('change', '.cmsar_visible_for_select', function() {
		if($(this).val() == 'loggedin') {
			$(this).closest('.cmsar_menu_options').find('.cmsar_menu_all_selected_roles').show();
		} else {
			$(this).closest('.cmsar_menu_options').find('.cmsar_menu_all_selected_roles').hide();
		}
	});

	$('body').on('change', '.cmsar_all_selected_radio', function() {
		if($(this).val() == 'selected_roles') {
			$(this).closest('.cmsar_menu_options').find('.cmsar_menu_roles').show();
		} else {
			$(this).closest('.cmsar_menu_options').find('.cmsar_menu_roles').hide();
		}
	});

});

function getUsers($input) {
	var user = $input.val();
	var fieldName = $input.data('field') + '[]';
	if (user.length > 0) user = '*' + user + '*';
	jQuery.post(ajaxurl, {action: 'cmacc_search_users', user_nick: user}, function(response) {
		if (response.success){
			var wrapper = $input.closest('div');
			var usersContainer = jQuery(wrapper).children('div');
			var usersList = usersContainer.children('div');
			var ids = [];
			jQuery.each(usersList, function () {
				ids.push(jQuery(this).find('input[type=checkbox]').val());
			})
			var userBlock;
			for (var key in response.users){
				if (ids.indexOf(key) === -1){
					userBlock = '<div><label><input type="checkbox" name="' + fieldName + '" value="' + key + '">'
						+ response.users[key] + '</label></div>';
					usersContainer.append(userBlock);
				}
			}
		} else {
			alert('Users not found');
		}
	});
	$input.val('');
}