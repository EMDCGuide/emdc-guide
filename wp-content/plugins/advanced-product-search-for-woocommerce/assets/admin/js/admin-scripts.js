jQuery( document ).ready( function($) {
	 "use strict";
		jQuery( this ).on( "click", ".apsw_radio_box label", function ( e ) {
			
			 $('.apsw_radio_box label').removeClass('active');
			 $(this).addClass('active');
		});

});