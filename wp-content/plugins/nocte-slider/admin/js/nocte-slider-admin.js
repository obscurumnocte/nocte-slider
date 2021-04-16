;(function($){
	'use strict';

	 //$( window ).load(function(){});
	$(document).ready(function(){

		// Field Repeater functions
		//  Generic add item button
		var $add_item = $('.add-repeater-item');
		if( $add_item.length > 0 ){
			$add_item.on('click', function(e){
				e.preventDefault();
				var $item = $(this).parent().siblings('.repeater-template');
				$item.siblings('.repeater-values-wrapper').append( $item.clone().removeClass('repeater-template') );
			});
		}

		//  Repeater Item Delete button - dynamic through document clicks
		$(document).on('click', '.repeater-delete', function(e){
			e.preventDefault();
			$(this).closest('.repeater-wrapper').slideUp( 400, function(){
				$(this).remove();
			});
		});

		//  Event handle the form submit to remove the repeater template fields to prevent issues with submissions
		$(document).on('submit', '.wp-admin.post-php form#post', function(e){
			//  Get repeater templates and remove them
			$('.repeater-template').remove();
		});

		//  Repeater Item Accordion button - dynamic through document clicks
		$(document).on('click', '.repeater-wrapper .collapse-btn', function(e){
			e.preventDefault();
			var $item = $(this).closest('.repeater-wrapper');
			//  Check for open or close
			if( $item.hasClass('closed') ){
				//  If closed, show content + delete button + remove header
				$item.children('.collapsable-content').slideDown( 400, function(){
					$item.find('.controls-wrapper .repeater-delete').fadeIn( 200 );
				});
				$item.children('.collapsed-header').slideUp( 400, function(){
					$(this).remove();
				});
				$item.removeClass('closed');

			} else {
				//  If open, create 'header' element from first field, hide content + delete button
				var $content = $item.children('.collapsable-content');
				var $header = $content.children().first().clone();
				var value = $header.find('input, textarea').val();
				$header.addClass('collapsed-header').hide();
				$header.find('.label-text').after('<span class="accordion-header-value">'+ value +'</span>');
				$header.find('.field-desc, input, textarea').remove();
				//  Add in and animate
				$item.prepend( $header );
				$content.slideUp( 400 );
				$item.find('.controls-wrapper .repeater-delete').fadeOut( 200 );
				$header.slideDown( 400 );
				$item.addClass('closed');
			}
		});
	});

})( jQuery );
