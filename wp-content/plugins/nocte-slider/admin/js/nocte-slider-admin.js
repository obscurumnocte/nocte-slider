;(function($){
	'use strict';

	 //$( window ).load(function(){});
	$(document).ready(function(){

		//  Find how many repeater fields sets there are to track naming counter
		var $repeater_values = $('.repeater-values-wrapper');
		if( $repeater_values.length > 0 ){
			$repeater_values.each(function(){
				$(this).attr('data-count', $(this).children().length );
			});
		}

		// Field Repeater functions
		//  Generic add item button
		var $add_item = $('.add-repeater-item');
		if( $add_item.length > 0 ){
			$add_item.on('click', function(e){
				e.preventDefault();
				var $item = $(this).parent().siblings('.repeater-template');
				var $new_item = $item.clone().removeClass('repeater-template');
				var $repeater_values_wrap = $item.siblings('.repeater-values-wrapper');
				var current_count = $repeater_values_wrap.attr('data-count');
				// Need to update field names for repeater
				$new_item.find('input,textarea,select').each(function(){
					var $this = $(this);
					var input_name = $this.attr('name');
					var pos_bracket = input_name.indexOf('[');
					if( pos_bracket != -1 ){
						var input_name_start = input_name.substring( 0, pos_bracket +1 );
						var input_name_end = input_name.substring( pos_bracket +1 );
						$this.attr('name', input_name_start + current_count + input_name_end );
					}
				});
				$repeater_values_wrap.append( $new_item );
				$repeater_values_wrap.attr('data-count', parseInt( current_count ) +1 );
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


	//  Image upload field buttons
	// on upload button click
	$(document).on( 'click', '.ns-upl', function(e){
		e.preventDefault();

		var $button = $(this);
		var custom_uploader = wp.media({
			title: $button.attr('ns-media-title'),
			library : {
				uploadedTo : wp.media.view.settings.post.id, // attach to the current post
				type : 'image'
			},
			button: {
				text: $button.attr('ns-media-btn-text') // button label text
			},
			multiple: false

		}).on('select', function(){ // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$button.find('img').attr('src', attachment.url );
			$button.siblings('input').val( attachment.id )
				   .siblings('.ns-rmv').show();
		}).open();

	});

	// on remove button click
	$(document).on('click', '.ns-rmv', function(e){
		e.preventDefault();

		var $button = $(this),
			$upl_btn_img = $button.siblings('.ns-upl').children('img');
		$button.siblings('input').val(''); // emptying the hidden field
		$button.hide();
		$upl_btn_img.attr('src', $upl_btn_img.attr('data-placeholder') );
	});

})( jQuery );
