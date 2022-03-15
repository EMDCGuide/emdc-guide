jQuery(document).ready(function($){

	var editor_css = ace.edit("wsp-loader-opt[custom_css]"),
	    textarea_css = $('textarea[name="wsp-loader-opt[custom_css]"]').hide();
	    editor_css.setTheme("ace/theme/monokai");

	   	editor_css.$blockScrolling = Infinity;
	   	
	   	editor_css.getSession().setMode("ace/mode/css");		
		editor_css.getSession().setValue(textarea_css.val());
		editor_css.getSession().on('change', function(){
		  textarea_css.val(editor_css.getSession().getValue());
		});

	var editor_html = ace.edit("wsp-loader-opt[custom_animation]"),
	    textarea_html = $('textarea[name="wsp-loader-opt[custom_animation]"]').hide();
	    editor_html.setTheme("ace/theme/monokai");

	    editor_html.$blockScrolling = Infinity;
	   	
	   	editor_html.getSession().setMode("ace/mode/html");		
		editor_html.getSession().setValue(textarea_html.val());
		editor_html.getSession().on('change', function(){
		  textarea_html.val(editor_html.getSession().getValue());
		});
	   
	    
	var loader_image = $('#loader-img'),
		preview_image = $('#loader-preview');
	
	wsp_init();

	loader_image.on('change',function(){
		wsp_init();	
	});

	function wsp_init(){
		var value =loader_image.val(),
			block ='<div class="smart-page-loader">';

		if( value != "" ){
			$('.wsp-loader-block').show();
				if(value == "Loader 1") {
					block += '<div class="wp-smart-loader smart-loader-one">Loading...</div>';
				} else if(value == "Loader 2") {
					block += '<div class="wp-smart-loader smart-loader-two"> <span></span> <span></span> <span></span> <span></span> </div>';
				} else if(value == "Loader 3") {
					block += ' <div class="wp-smart-loader smart-loader-three"> <span></span> <span></span> <span></span> <span></span> <span></span> </div>';
				} else if(value == "Loader 4") {
					block += ' <div class="wp-smart-loader smart-loader-four"> <span class="spinner-cube spinner-cube1"></span> <span class="spinner-cube spinner-cube2"></span> <span class="spinner-cube spinner-cube3"></span> <span class="spinner-cube spinner-cube4"></span> <span class="spinner-cube spinner-cube5"></span> <span class="spinner-cube spinner-cube6"></span> <span class="spinner-cube spinner-cube7"></span> <span class="spinner-cube spinner-cube8"></span> <span class="spinner-cube spinner-cube9"></span> </div>';
				} else if(value == "Loader 5") {
					block += ' <div class="wp-smart-loader smart-loader-five"> <span class="spinner-cube-1 spinner-cube"></span> <span class="spinner-cube-2 spinner-cube"></span> <span class="spinner-cube-4 spinner-cube"></span> <span class="spinner-cube-3 spinner-cube"></span> </div>';
				} else if(value == "Loader 6") {
					block += ' <div class="wp-smart-loader smart-loader-six"> <span class=" spinner-cube-1 spinner-cube"></span> <span class=" spinner-cube-2 spinner-cube"></span> </div>';
				} else if(value == "Custom Animation") {
					block += '';
					$('.wsp-loader-block').hide();
				}
				block += "</div>";
				preview_image.html(block);
		}
	}
});