jQuery(document).ready(function() {

    jQuery('body').on('click', '.cmreg-input-type-trigger', function (e) {
        e.preventDefault();
        var input_el = jQuery(this).closest('.cmreg-password-block').find('input');
        var icon_el = jQuery(this).find('.dashicons');
        if(input_el.attr("type") == "password"){
            input_el.attr("type", "text");
            icon_el.removeClass("dashicons-hidden").addClass('dashicons-visibility');
        }else{
            input_el.attr("type", "password");
            icon_el.removeClass("dashicons-visibility").addClass('dashicons-hidden');
        }
    });

	 jQuery('body').on('click', '.cmreg-input-type-trigger-re', function (e) {
        e.preventDefault();
        var input_el = jQuery(this).closest('.cmreg-password-block-re').find('input');
        var icon_el = jQuery(this).find('.dashicons');
        if(input_el.attr("type") == "password"){
            input_el.attr("type", "text");
            icon_el.removeClass("dashicons-hidden").addClass('dashicons-visibility');
        }else{
            input_el.attr("type", "password");
            icon_el.removeClass("dashicons-visibility").addClass('dashicons-hidden');
        }
    });

    jQuery('a').each(function() {
		var athat = this;
		var cmregahref = jQuery(this).attr('href');
		if (typeof cmregahref != 'undefined') {
			if(cmregahref.indexOf('?redirect_to') != -1) {
				var cmregahref_array = cmregahref.split("?");
				jQuery(this).attr('href', cmregahref_array[0]);
				var cmregparam_array = cmregahref_array[1].split("&");
				jQuery.each( cmregparam_array, function( key, value ) {
					var cmregval_array = value.split("=");
					jQuery(athat).attr(cmregval_array[0], decodeURI(cmregval_array[1]));
				});
			}
		}
	});
	
	// hide_show_optional => hide on first radio button and show on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.hide_show_optional input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).removeClass('show').addClass('hide');
				});
			} else {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					jQuery(this).removeClass('hide').addClass('show');
				});
			}
		}
	});

	// hide_show_required => hide on first radio button and show on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.hide_show_required input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).find('input[type="text"]').removeAttr('required');
					jQuery(this).find('input[type="number"]').removeAttr('required');
					jQuery(this).find('select').removeAttr('required');
					jQuery(this).find('textarea').removeAttr('required');
					jQuery(this).removeClass('show').addClass('hide');
				});
			} else {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					jQuery(this).find('input[type="text"]').attr('required', 'required');
					jQuery(this).find('input[type="number"]').attr('required', 'required');
					jQuery(this).find('select').attr('required', 'required');
					jQuery(this).find('textarea').attr('required', 'required');
					jQuery(this).removeClass('hide').addClass('show');
				});	
			}
		}
	});

	// show_hide_optional => show on first radio button and hide on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.show_hide_optional input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					jQuery(this).removeClass('hide').addClass('show');
				});
			} else {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).removeClass('show').addClass('hide');
				});	
			}
		}
	});

	// show_hide_required => show on first radio button and hide on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.show_hide_required input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					jQuery(this).find('input[type="text"]').attr('required', 'required');
					jQuery(this).find('input[type="number"]').attr('required', 'required');
					jQuery(this).find('select').attr('required', 'required');
					jQuery(this).find('textarea').attr('required', 'required');
					jQuery(this).removeClass('hide').addClass('show');
				});
			} else {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).find('input[type="text"]').removeAttr('required');
					jQuery(this).find('input[type="number"]').removeAttr('required');
					jQuery(this).find('select').removeAttr('required');
					jQuery(this).find('textarea').removeAttr('required');
					jQuery(this).removeClass('show').addClass('hide');
				});
			}
		}
	});
	
	// hide_show_required_by_class => hide on first radio button and show on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.hide_show_required_by_class input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).find('input[type="text"]').removeAttr('required');
					jQuery(this).find('input[type="number"]').removeAttr('required');
					jQuery(this).find('select').removeAttr('required');
					jQuery(this).find('textarea').removeAttr('required');
					jQuery(this).removeClass('show').addClass('hide');
				});
			} else {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					if(jQuery(this).find('input[type="text"]').hasClass('required')) {
						jQuery(this).find('input[type="text"]').attr('required', 'required');
					} else {
						jQuery(this).find('input[type="text"]').removeAttr('required');
					}
					if(jQuery(this).find('input[type="number"]').hasClass('required')) {
						jQuery(this).find('input[type="number"]').attr('required', 'required');
					} else {
						jQuery(this).find('input[type="number"]').removeAttr('required');
					}
					if(jQuery(this).find('select').hasClass('required')) {
						jQuery(this).find('select').attr('required', 'required');
					} else {
						jQuery(this).find('select').removeAttr('required');
					}
					if(jQuery(this).find('textarea').hasClass('required')) {
						jQuery(this).find('textarea').attr('required', 'required');
					} else {
						jQuery(this).find('textarea').removeAttr('required');
					}
					jQuery(this).removeClass('hide').addClass('show');
				});	
			}
		}
	});

	// show_hide_required_by_class => hide on first radio button and show on second radio button
	jQuery('body').on('click', '.cmreg-registration-field.show_hide_required_by_class input[type="radio"]', function(){
		var indexcounter = jQuery(this).attr('indexcounter');
		if (typeof indexcounter != 'undefined') {
			if(indexcounter == 0) {
				jQuery(".cmreg-registration-field.hide").each(function(index) {
					if(jQuery(this).find('input[type="text"]').hasClass('required')) {
						jQuery(this).find('input[type="text"]').attr('required', 'required');
					} else {
						jQuery(this).find('input[type="text"]').removeAttr('required');
					}
					if(jQuery(this).find('input[type="number"]').hasClass('required')) {
						jQuery(this).find('input[type="number"]').attr('required', 'required');
					} else {
						jQuery(this).find('input[type="number"]').removeAttr('required');
					}
					if(jQuery(this).find('select').hasClass('required')) {
						jQuery(this).find('select').attr('required', 'required');
					} else {
						jQuery(this).find('select').removeAttr('required');
					}
					if(jQuery(this).find('textarea').hasClass('required')) {
						jQuery(this).find('textarea').attr('required', 'required');
					} else {
						jQuery(this).find('textarea').removeAttr('required');
					}
					jQuery(this).removeClass('hide').addClass('show');
				});	
			} else {
				jQuery(".cmreg-registration-field.show").each(function(index) {
					jQuery(this).find('input[type="text"]').removeAttr('required');
					jQuery(this).find('input[type="number"]').removeAttr('required');
					jQuery(this).find('select').removeAttr('required');
					jQuery(this).find('textarea').removeAttr('required');
					jQuery(this).removeClass('show').addClass('hide');
				});
			}
		}
	});

	jQuery('body').on('change', '#cmreg_membership_package', function() {
		jQuery('#cmreg_membership_package_role').val(jQuery(this).find('option:selected').attr('role'));
	});

});

var CMREG_Frontend = {
	debug: (location.hash == '#cmdebug'),
	defaults: {
		registrationAfterToastDuration: CMREG_FrontendFieldsSettings.toastMessageTimeForRegister,
	},
	log: function(msg) {
		if (CMREG_Frontend.debug) {
			console.log(msg);
		}
	}
};

CMREG_Frontend.LOGIN_BTN_SELECTOR = '.cmreg-login-click, a[href="#cmreg-login-click"], .cmreg-only-login-click, a[href="#cmreg-only-login-click"]';
CMREG_Frontend.REGISTER_BTN_SELECTOR = '.cmreg-only-registration-click, a[href="#cmreg-only-registration-click"]';

CMREG_Frontend.LOGIN_BTN_TEXT_SELECTOR = 'a.cmreg-login-click, .cmreg-login-click a, a[href="#cmreg-login-click"], '
				+ 'a.cmreg-only-login-click, .cmreg-only-login-click a, a[href="#cmreg-only-login-click"]';
CMREG_Frontend.REGISTER_BTN_TEXT_SELECTOR = 'a.cmreg-only-registration-click, .cmreg-only-registration-click a, a[href="#cmreg-only-registration-click"]';

CMREG_Frontend.LOGOUT_BTN_SELECTOR = 'a[href$="#cmreg-logout-click"], .cmreg-logout-click';

/**
 * Stop event
 */
CMREG_Frontend.stopEvent = function(ev) {
	ev.preventDefault();
	ev.stopPropagation();
};

CMREG_Frontend.setupButtonHandlers = function() {
	
	var $ = jQuery;
	
	CMREG_Frontend.log('CMREG setup');
		
	/**
	 * Check each click on every element because of the bugs on the mobile versions
	 */
	$('body').click(function(ev) {
		
		// Check if this is click on the CMREG button
		var btn = CMREG_Frontend.getTheButton(ev.target);
		if (btn.length == 0) return true;
				
		if (CMREG_Settings.isUserLoggedIn == '1') return true;
		else CMREG_Frontend.stopEvent(ev);
		
		CMREG_Frontend.log('CMREG click success')
		
		var overlayReady = function() {
			$('.cmreg-overlay').removeClass('cmreg-only-login').removeClass('cmreg-only-registration');
			if (btn.hasClass('cmreg-only-login-click') || btn.attr('href') == '#cmreg-only-login-click') {
				$('.cmreg-overlay').addClass('cmreg-only-login');
			}
			if (btn.hasClass('cmreg-only-registration-click') || btn.attr('href') == '#cmreg-only-registration-click') {
				$('.cmreg-overlay').addClass('cmreg-only-registration');
			}
			var redirect_to = '';
			if (typeof btn.attr('redirect_to') != 'undefined') {
				redirect_to = btn.attr('redirect_to');
			}
			var p_id = '';
			if (typeof btn.attr('p_id') != 'undefined') {
				p_id = btn.attr('p_id');
			}
			var p_role = '';
			if (typeof btn.attr('p_role') != 'undefined') {
				p_role = btn.attr('p_role');
			}
			CMREG_Frontend.loginClick(redirect_to, p_id, p_role);
		};
		
		if ($('.cmreg-overlay').length == 0) {
			// Load overlay by AJAX
			var loader = $('<div/>', {"class": 'cmreg-overlay cmreg-loader-overlay'});
			$('body').append(loader);
			window.CMREG.Utils.fadeIn(loader, 'fast', function() {
				CMREG_Frontend.loadOverlay(function() {
					if (typeof $('.modal') != 'undefined' && typeof modal !== 'undefined' && $.isFunction(modal)) { $('.modal').modal('hide'); }
					overlayReady();
				});
			});
		} else {
			if (typeof $('.modal') != 'undefined' && typeof modal !== 'undefined' && $.isFunction(modal)) { $('.modal').modal('hide'); }
			overlayReady();
		}
	});
	
};

CMREG_Frontend.loadOverlay = function(callback) {
	var $ = jQuery;

	if (typeof $('.modal') != 'undefined' && typeof modal !== 'undefined' && $.isFunction(modal)) { $('.modal').modal('hide'); }

	//setTimeout(function() {
	var post_page_id = 0;
	if (typeof $('.cmregAutoPopupTrigger').attr('data-postpageid') != 'undefined') {
		post_page_id = $('.cmregAutoPopupTrigger').attr('data-postpageid');
	}

	$.post(CMREG_Settings.ajaxUrl, {action: "cmreg_login_overlay", post_page_id: post_page_id}, function(response) {
		var overlay = $('.cmreg-overlay');
		if (overlay.length == 0) {
			CMREG_Frontend.log('append new overlay');
			overlay = $(response);
			overlay = overlay.find('link').remove();
			overlay = overlay.find('script').remove();
			$('body').append(overlay);
		} else {
			CMREG_Frontend.log('replace overlay')
			overlay.html($(response).html());
		}
		CMREG_Frontend.initOverlayHandlers(overlay);
		if (callback) callback();
	});
	//}, 2000);

};

CMREG_Frontend.getTheButton = function(elem) {
	var obj = jQuery(elem);
	var selector = CMREG_Frontend.LOGIN_BTN_SELECTOR + ', ' + CMREG_Frontend.REGISTER_BTN_SELECTOR;
	var test = obj.is(selector);
	//CMREG_Frontend.log('CMREG button test = ', test);
	if (test) return obj;
	else return obj.parents(selector).first();
};

/**
 * Called after the login button click when the overlay is ready
 */
CMREG_Frontend.loginClick = function(redirect_to, p_id, p_role) {
	var $ = jQuery;
	$('body').addClass('cmreg-overlay-visible');
	var elem = $('.cmreg-overlay').first();
	var that = this;
	
	var completed = function() {
		if(redirect_to != '') {
			$('.cmreg-overlay .cmreg-login-form input[name=cmreg_redirect_url]').val(redirect_to);
			$('.cmreg-overlay .cmreg-registration-form input[name=cmreg_redirect_url]').val(redirect_to);
		}
		if(p_id != '') {
			$('#cmreg_membership_package').val(p_id);
		} else {
			$('#cmreg_membership_package > option:eq(0)').attr('selected', true);
		}
		if(p_role != '') {
			$('#cmreg_membership_package_role').val(p_role);
		} else {
			if($('#cmreg_membership_package').length > 0) {
				$('#cmreg_membership_package_role').val($('#cmreg_membership_package > option:eq(0)').attr('role'));
			} else {
				$('#cmreg_membership_package_role').val('');
			}
		}
		$('.cmreg-overlay .cmreg-login-form input[type=email]').focus();
		//$('.cmreg-wrapper', that).trigger('cmreg:init');
		$('.cmreg-wrapper').trigger('cmreg:init');
	};
	
	if (elem.hasClass('cmreg-loader-overlay')) {
		elem.removeClass('cmreg-loader-overlay')
		completed();
	} else {
		CMREG_Frontend.log('fadein')
		window.CMREG.Utils.fadeIn(elem, 'fast', function() {
			completed();
		});
	}
	
};

/**
 * Login and registration form common handler
 */
CMREG_Frontend.formSubmitHandler = function(ev, callback) {
	CMREG_Frontend.stopEvent(ev);
	var $ = jQuery;
	var form = $(this);
	var btn = form.find('button[type=submit]');
	var loader = $('<div/>', {"class": "cmreg-loader-inline"});
	loader.width(btn.width());
	loader.height(btn.height());
	loader.css('padding', btn.css('padding'));
	btn.hide();
	btn.after(loader);
	$('.cmreg-2fa-field').attr('required',false);
	$('.cmreg-2fa-field').hide();
	$.post(form.data('ajax-url'), form.serialize(), function(response) {
		callback(response, form);
	});
};
	
/**
 * Called after the overlay is ready
 */
CMREG_Frontend.initOverlayHandlers = function(wrapper) {
	
	var $ = jQuery;

	/**
	 * Close overlay when clicked at the background
	 */
	if(CMREG_Settings.loginAuthenticationPopupForce == '1') {
		$('.cmreg-overlay').off('click.cmreg').on('click.cmreg', function(ev) {
			if (ev.target !== this) return;
			CMREG_Frontend.stopEvent(ev);
			var elem = $(this); //.fadeOut('fast');
			window.CMREG.Utils.fadeOut(elem, 'fast');
			$('body').removeClass('cmreg-overlay-visible');
		});
	}
	
	/**
	 * Close overlay button click
	 */
	$('.cmreg-overlay-close').click(function() {
		var elem = $(this).parents('.cmreg-overlay'); //.fadeOut('fast');
		window.CMREG.Utils.fadeOut(elem, 'fast');
		$('body').removeClass('cmreg-overlay-visible');
	});
	
	/**
	 * After submit the login form
	 */
	$('.cmreg-login-form', wrapper).submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			if (response.success) {
				var elem = form.parents('.cmreg-overlay'); //.fadeOut('fast');
				window.CMREG.Utils.fadeOut(elem, 'fast');
				$('body').removeClass('cmreg-overlay-visible');
				if (response.redirect && response.redirect != 'reload') {
					location.href = response.redirect;
				} else {
					location.reload();
				}
			} else {
				form.find('.cmreg-loader-inline').remove();
				form.find('button[type=submit]').show();
				
				if (typeof response.msg == 'string') {
					var str = response.msg;
					var res = str.match(/TFA/g);
					if(res !== null) {
						$('.cmreg-2fa-field').attr('required',true);
						$('.cmreg-2fa-field').show();
					}
				}

				if (typeof response.showCaptcha == 'string') {
					CMREG_Frontend.log('adding captcha login');
					if (form.find('.cmreg-recaptcha').length == 0) {
						form.find('.cmreg-buttons-field').before(response.showCaptcha);
						CMREG_Frontend.initCaptcha(form);
					}
				}
				
				form.find('.cmreg-recaptcha').each(function() {
					CMREG_Frontend.log('captcha reset login');
					grecaptcha.reset($(this).data('recaptchaResetId'));
				});
			}
		});
	});
	
	/**
	 * After submit the registration form
	 */
	$('.cmreg-registration-form', wrapper).submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			form.find('.cmreg-loader-inline').remove();
			form.find('button[type=submit]').show();
			if (response.success) {
				var callbackFunction = null;
				var elem = form.parents('.cmreg-overlay');
				window.CMREG.Utils.fadeOut(elem, 'fast');
				$('body').removeClass('cmreg-overlay-visible');
				if (response.redirect == 'reload') {
					callbackFunction = function() { location.reload(); };
				}
				else if (response.redirect && response.redirect.length > 0) {
					callbackFunction = function() { location.href = response.redirect; };
				} else {
					//location.reload();
				}
				window.CMREG.Utils.toast(response.msg, null, CMREG_Frontend.defaults.registrationAfterToastDuration, callbackFunction);
			} else {
				window.CMREG.Utils.toast(response.msg);
				form.find('.cmreg-recaptcha').each(function() {
					CMREG_Frontend.log('captcha reset reg');
					grecaptcha.reset($(this).data('recaptchaResetId'));
				});
			}
		});
	});
	
	/**
	 * After submit the lost password form
	 */
	$('.cmreg-lost-password-form', wrapper).submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			form.find('.cmreg-loader-inline').remove();
			if(response.success == true) {
				form.find('input[type=email]').val('');
			}
			form.find('button[type=submit]').show();
		});
	});
	
	$('.cmreg-self-register-form', wrapper).submit(function(ev) {
		CMREG_Frontend.formSubmitHandler.call(this, ev, function(response, form) {
			window.CMREG.Utils.toast(response.msg);
			form.find('.cmreg-loader-inline').remove();
			if(response.success == true) {
				//alert(response.success);
			}
			form.find('button[type=submit]').show();
		});
	});

	/**
	 * Show the lost password form
	 */
	$('.cmreg-lost-password-link a', wrapper).click(function(ev) {
		CMREG_Frontend.stopEvent(ev);
		$(this).hide();
		var form = $(this).parents('.cmreg-login').find('.cmreg-lost-password-form');
		form.show();
		form.find('input[type=email]').focus();
	});
	
	$('.cmreg-self-register-link a', wrapper).click(function(ev) {
		CMREG_Frontend.stopEvent(ev);
		$(this).hide();
		var form = $(this).parents('.cmreg-login').find('.cmreg-self-register-form');
		form.show();
	});

	/**
	 * Show the invitation form
	 */
	$('.cmreg-invitation-code-field a.cmreg_ainvlink', wrapper).click(function(ev) {
		CMREG_Frontend.stopEvent(ev);
		$(this).hide();
		$(this).parents('div').find('.cmreg_ainvlink_con').show();
		$(this).parents('div').find('.cmreg_ainvlink_con input').focus();
	});

};

CMREG_Frontend.initCaptcha = function(target) {
	var $ = jQuery;
	setTimeout(function() {
		// give some time for grecaptcha object to load
		$('.cmreg-recaptcha', target).each(function() {
				CMREG_Frontend.log('init captcha', this);
				var container = $(this);
				var parameters = {"sitekey" : container.data('sitekey')};
				try {
					if (typeof grecaptcha == 'undefined') {
						var script   = document.createElement("script");
						script.type  = "text/javascript";
						script.src   = "https://www.recaptcha.net/recaptcha/api.js";
						document.body.appendChild(script);
					}
					setTimeout(function() {
						var id = grecaptcha.render(container[0], parameters);
						container.data('recaptchaResetId', id);
					}, 500);
				} catch (e) {
					CMREG_Frontend.log(e);
				}
			});
	}, 500);
};

// Setup handler after new node added
//document.addEventListener('DOMNodeInserted', function(ev) {
//	CMREG_Frontend.setupButtonHandlers(jQuery(ev.target));
//}, false);
	
jQuery(function($) {
	
	/**
	 * Change the login button into logout button
	 */
	if (CMREG_Settings.isUserLoggedIn == '1') {
		jQuery($(CMREG_Frontend.LOGIN_BTN_TEXT_SELECTOR)).each(function() {
			var redirect_to = $(this).attr('redirect_to');
			var after_login = $(this).attr('after_login');
			var after_text = $(this).attr('after_text');
			if(after_login == "1") {
				$(this).attr('href', redirect_to).text(after_text);
			} else {
				$(this).attr('href', CMREG_Settings.logoutUrl).text(CMREG_Settings.logoutButtonLabel);
			}
		});
		$(CMREG_Frontend.REGISTER_BTN_TEXT_SELECTOR).hide();
	}
	
	/**
	 * Logout buttons
	 */
	if (CMREG_Settings.isUserLoggedIn == '1') {
		$(CMREG_Frontend.LOGOUT_BTN_SELECTOR).attr('href', CMREG_Settings.logoutUrl);
	} else {
		$(CMREG_Frontend.LOGOUT_BTN_SELECTOR).hide();
	}
	
	$(document).on('cmreg:init', function(ev) {
		CMREG_Frontend.log('cmreg:init');
		var target = $(ev.target);
		// Init recaptcha
		CMREG_Frontend.initCaptcha(target);
	});
	
	// Setup button handler immidiately
	CMREG_Frontend.setupButtonHandlers();
	
	// Init in case that some elements has been already added to the page
	CMREG_Frontend.initOverlayHandlers($('body'));
	
	// Init recaptcha for existing shortcodes
	setTimeout(function() {
		$('.cmreg-wrapper').trigger('cmreg:init');
	}, 1000);
	
	// Preload the overlay if needed
	if (CMREG_Settings.isUserLoggedIn != '1' && CMREG_Settings.overlayPreload == '1') {
		CMREG_Frontend.loadOverlay();
	}
	// Opens popup box when page is loaded and has a parametr ?cm_popup_open=1
	var url_string = window.location.href;
	var url = new URL(url_string);
	var open = url.searchParams.get("cmreg_popup_open");
	var btn = $('.cmreg-login-button');

	if (open == 1 && CMREG_Settings.isUserLoggedIn != '1' && btn.length) {
		var overlayReady = function() {
			$('.cmreg-overlay').removeClass('cmreg-only-login').removeClass('cmreg-only-registration');
			if ($('body').hasClass('cmreg-only-login-click') || $('body').attr('href') == '#cmreg-only-login-click') {
				$('.cmreg-overlay').addClass('cmreg-only-login');
			}
			if ($('body').hasClass('cmreg-only-registration-click') || $('body').attr('href') == '#cmreg-only-registration-click') {
				$('.cmreg-overlay').addClass('cmreg-only-registration');
			}
			CMREG_Frontend.loginClick('', '');
		};
		CMREG_Frontend.loadOverlay(function() {
			if (typeof $('.modal') != 'undefined' && typeof modal !== 'undefined' && $.isFunction(modal)) { $('.modal').modal('hide'); }
			overlayReady();
		});
	}
	
});

// Fix for the Gallery plugin
if (typeof ajaxurl == 'undefined') {
	ajaxurl = CMREG_Settings.ajaxUrl;
}

if(CMREG_Settings.isUserLoggedIn == "0") {
	if(CMREG_Settings.globalSiteAccess == "1") {
		var excludeRedirectUrls = CMREG_Settings.excludeRedirectUrl;
		jQuery('a').each(function() {
			var siteAccessRedirectUrl = CMREG_Settings.siteHomePageRedirectUrl;
			if(CMREG_Settings.customRedirectUrl != '') {
				siteAccessRedirectUrl = CMREG_Settings.customRedirectUrl;
			}
			currentAhref = jQuery(this).attr('href');
			if(excludeRedirectUrls.indexOf(currentAhref) == -1) {
				jQuery(this).attr('ohref', currentAhref);
				jQuery(this).attr('href', siteAccessRedirectUrl);
			}
		});
	}
}

/*
if(CMREG_Settings.isUserLoggedIn == "0") {
	if(CMREG_Settings.loginAuthenticationPopupEnable == "1" || (CMREG_Settings.loginAuthenticationInviteEnable == "1" && CMREG_Settings.loginAuthenticationInvite != '')) {
		if(CMREG_Settings.loginAuthenticationPopup == '1') {
			jQuery("body").append("<a href='#cmreg-only-login-click' data-postpageid='"+CMREG_Settings.loginAuthenticationPopupPostID+"' class='cmregAutoPopupTrigger'></a>");
		} else if(CMREG_Settings.loginAuthenticationPopup == '2') {
			jQuery("body").append("<a href='#cmreg-only-registration-click' data-postpageid='"+CMREG_Settings.loginAuthenticationPopupPostID+"' class='cmregAutoPopupTrigger'></a>");
		} else {
			jQuery("body").append("<a href='#cmreg-login-click' data-postpageid='"+CMREG_Settings.loginAuthenticationPopupPostID+"' class='cmregAutoPopupTrigger'></a>");
		}
		setTimeout(function() {
			jQuery(".cmregAutoPopupTrigger").trigger('click');
			
			setTimeout(function() {
				if(CMREG_Settings.loginAuthenticationInvite != '') {
					jQuery("form.cmreg-registration-form").find("input[name='cmreg_invit_code']").val(CMREG_Settings.loginAuthenticationInvite);
				}
			}, 2000);

			if(CMREG_Settings.loginAuthenticationPopupForce == '0') {
				setTimeout(function() {
					jQuery(".cmreg-overlay").off('click.cmreg');
					jQuery(".cmreg-overlay .cmreg-overlay-close").remove();
				}, 2000);
			}
	
		}, 1000);
	}
}
*/