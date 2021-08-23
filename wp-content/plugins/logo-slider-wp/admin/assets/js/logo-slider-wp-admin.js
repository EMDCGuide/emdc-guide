if ( window.jQuery ) {
	jQuery( document ).ready( function ($) {
		'use strict';


		/***************************** Function Definition ****************************/





		/***************************** End Function Definition ****************************/









		let lgx_logo_slider_shortcode_copy_btn = $( '.lgx_logo_slider_shortcode_copy_btn' );
		let $wpListTable = $( 'table.wp-list-table tbody' );

		lgx_logo_slider_shortcode_copy_btn.on( 'click', function (event) {
			event.preventDefault();
			let $this = $( this );
			let shortCodeString = $this.siblings( '.lgx_logo_slider_shortcode_copy_text' ).val();
			const element = document.createElement('textarea' );
			

			element.value = shortCodeString;
			element.setAttribute( 'readonly', '' );
			element.style.position = 'absolute';
			element.style.left = '-9999px';
			document.body.appendChild( element );
			element.select();
			document.execCommand( 'copy' );
			document.body.removeChild( element );

			$this.html( '<i class="lgxicon lgx-icon-copy"></i> Copied' );

			if ( 'object' === typeof alertify ) {
				alertify.success( '<i class="lgxicon lgx-icon-copy"></i> Copy to clipboard successfully' );
			}

		} );


		$('.logosliderwp_settings_bgimage_url-text').click(function(e) {
			e.preventDefault();

			var $_this = $(this);
			var image = wp.media({
				title: wpnpaddon.add_leftimg_title,
				multiple: false
			}).open()
				.on('select', function(e) {

					var uploaded_image = image.state().get('selection').first();
					var image_url = uploaded_image.toJSON().url;

					$('.logosliderwp_settings_bgimage_url-text').val(image_url);

					$_this.next('.description').find('.lgxwp_bg_img').removeClass('hidden');
					$('#lgxwp_bg_previousimg').attr('src', image_url).removeClass('hidden');

				});
		});

		$wpListTable.sortable( {
			placeholder: 'placeholder-highlight',
			start: function(event, ui) {
			},
			sort: function(event, ui) {
				ui.item[0].style.backgroundColor = 'white';
			},
			stop: function(event, ui) {
				ui.item.removeAttr( 'style' );
				$.ajax( {
					type : 'post',
					dataType : 'json',
					url : wpnpaddon.ajax_url,
					data : {
						action: 'lgx_ls_admin_lswp_reorder',
						post_id_serialize : $( '#the-list' ).sortable( 'serialize' ),
						nonce: wpnpaddon.check_nonce
					},
					success: function(response) {
						if( response.type === 'success' ) {
							
						}
						else {
							alert( response.message )
						}
					}
				} )
			}
		} )


	} );// Init jQuery
} else {
	console.log( 'jQuery not loaded.' )
}
