<?php
/**
 * NS Carousel Posttype - Add Owl Carousel option metabox and fields
 * Self instantiated
 */

defined('ABSPATH') || exit;

/**
 * Features Class.
 */
class NS_Carousel_Owl_Options_Meta {

	/**
	 * Metabox fields
	 */
	protected $metabox_fields;

	/**
	 * Constructor.
	 */
	public function __construct(){
        add_action('add_meta_boxes', array( $this, 'ns_carousel_owl_options_metabox') );
        add_action('save_post', array( $this, 'ns_carousel_owl_options_save_custom_meta'), 10, 3 );

		//  Set up fields for processing
		$this->metabox_fields = new NS_Carousel_Metabox_Fields( $this->get_fields_config() );

		add_filter('get_owl_options_meta', array( $this, 'get_owl_options_meta') );
	}

    /**
     * Add Meta Box and custom fields
     */
    public function ns_carousel_owl_options_metabox(){
        global $post;
        if( empty( $post ) ) return;

		do_action('ns-carousel-load-meta-data', $post->ID );

    	add_meta_box('ns-carousel-owl-options',
    		__('Owl Carousel Options', 'ns'),
    		array( $this, 'ns_carousel_owl_options_fields'),
    		'ns_carousel',
    		'advanced'
    	);
    }


    /**
     * Displays fields for Owl Carousel Options
     */
    public function ns_carousel_owl_options_fields(){
    	// Use nonce for verification
    	wp_nonce_field( basename( __FILE__ ), 'owl_options_meta_box_nonce');

		//  Display fields
	?>
		<div id="ns_carousel_owl_options">
			<?php $this->metabox_fields->add_markup( $this->get_fields_config() ); ?>
		</div>
    <?php
    }

    /**
     *  Save the custom field data
     */
    public function ns_carousel_owl_options_save_custom_meta( $post_id, $post, $update ){
		// Verify the nonce before proceeding.
        if( !isset( $_POST['owl_options_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['owl_options_meta_box_nonce'], basename( __FILE__ ) ) ){
			return;
		}

        // Stop WP from clearing custom fields on autosave
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return;
		}

        if( !current_user_can('edit_post', $post_id ) ){
    		return $post_id;
		}

        if( $post->post_type != 'ns_carousel' ){
			return;
		}

		//  Save fields
		$this->metabox_fields->save_fields( $post_id );
    }

	/**
	 *  Return the owl options meta data
	 */
	public function get_owl_options_meta( $carousel_id = 0 ){
		if( $carousel_id == 0 ){
			return array();
		}
		//  Set up field data for current carousel
		do_action('ns-carousel-load-meta-data', $carousel_id );
		//  Return array of field names to values
		return $this->metabox_fields->get_fields_data();
	}

	/**
	 * Set up the fields array
	 */
	private function get_fields_config(){
		return apply_filters(
			'ns-carousel-owl-options-fields',
			array(
				'group-general' => array(
					'name'		=> 'general',
					'label'		=> __('General Settings', 'ns'),
					'desc'		=> __('Settings generally used when creating a carousel.', 'ns'),
					'field_list'	=> array(
						'responsive'		=> array(
							'type'		=> 'repeater',
							'default'	=> false,
							'label'		=> __('Responsive options', 'ns'),
							'desc'		=> __('Responsive options can be set at different screen widths i.e. from 0 width show 1 item, from 768 width show 2 items, etc.', 'ns'),
							'add_btn_text'	=> __('Add breakpoint', 'ns'),
							'subfields'	=> array(
								'breakpoint'		=> array(
									'type'		=> 'number',
									'default'	=> 0,
									'label'		=> __('Breakpoint', 'ns'),
									'desc'		=> __('The pixel width of the screen when the following settings should be used (above this value, requires a 0 breakpoint).', 'ns'),
									'min'		=> 0
								),
								'items'		=> array(
									'type'		=> 'number',
									'default'	=> 3,
									'label'		=> __('Items', 'ns'),
									'desc'		=> __('The number of items you want to see on the screen.', 'ns'),
									'min'		=> 1
								),
								'margin'		=> array(
									'type'		=> 'number',
									'default'	=> 0,
									'label'		=> __('Margin', 'ns'),
									'desc'		=> __('The margin-right in pixels on an item - numerical value only.', 'ns'),
									'min'		=> 0
								),
								'center'		=> array(
									'type'		=> 'checkbox',
									'default'	=> 0,
									'label'		=> __('Center', 'ns'),
									'desc'		=> __('Center current item horizontally.', 'ns')
								),
								'stagePadding'		=> array(
									'type'		=> 'number',
									'default'	=> 0,
									'label'		=> __('Stage Padding', 'ns'),
									'desc'		=> __('Add padding to the left and right of the carousel to see next and previous slides.', 'ns'),
									'min'		=> 0
								),
								'startPosition'		=> array(
									'type'		=> 'number',
									'default'	=> 1,
									'label'		=> __('Start Position', 'ns'),
									'desc'		=> __('Position of item to start with (0 is first item)', 'ns'),
									'min'		=> 0
								),
								'nav'		=> array(
									'type'		=> 'checkbox',
									'default'	=> 0,
									'label'		=> __('Navigation Arrows', 'ns'),
									'desc'		=> __('Show next and revious arrow buttons.', 'ns')
								),
								'dots'		=> array(
									'type'		=> 'checkbox',
									'default'	=> 1,
									'label'		=> __('Dots/Pagination', 'ns'),
									'desc'		=> __('Show dots navigation.', 'ns')
								),
								'rewind'		=> array(
									'type'		=> 'checkbox',
									'default'	=> 1,
									'label'		=> __('Rewind', 'ns'),
									'desc'		=> __('Go backwards when the boundary has been reached i.e. clicking next on last item will slide back through all items to the first.', 'ns')
								),
								'slideBy'		=> array(
									'type'		=> 'text',
									'default'	=> 'page',
									'label'		=> __('Slide By', 'ns'),
									'desc'		=> __('Number of items to slide by when navigating. \'page\' can be set to slide all visible items to reveal the next set. NOTE: if there are not enough items, the navigation willnot slide e.g. displaying 3 of 5 items will refuse to slide by 3 as there are only 2 items remaining.', 'ns')
								)
							)
						),
						'items'		=> array(
							'type'		=> 'number',
							'default'	=> 3,
							'label'		=> __('Items', 'ns'),
							'desc'		=> __('The number of items you want to see on the screen.', 'ns'),
							'min'		=> 1
						),
						'nav'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Navigation Arrows', 'ns'),
							'desc'		=> __('Show next and previous arrow buttons.', 'ns')
						),
						'dots'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Dots/Pagination', 'ns'),
							'desc'		=> __('Show dots navigation.', 'ns')
						),
						'loop'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Loop', 'ns'),
							'desc'		=> __('Infinity loop the carousel.', 'ns')
						),
						'center'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Center', 'ns'),
							'desc'		=> __('Center current item horizontally.', 'ns')
						),
						'margin'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Margin', 'ns'),
							'desc'		=> __('The margin-right in pixels on an item - numerical value only.', 'ns')
						),
						'stagePadding'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Stage Padding', 'ns'),
							'desc'		=> __('Add padding to the left and right of the carousel to see next and previous slides.', 'ns'),
							'min'		=> 0
						),
						'startPosition'		=> array(
							'type'		=> 'number',
							'default'	=> 1,
							'label'		=> __('Start Position', 'ns'),
							'desc'		=> __('Position of item to start with (0 is first item)', 'ns'),
							'min'		=> 1
						),
						'autoWidth'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Auto Width', 'ns'),
							'desc'		=> __('Allow width of item to be dictated by content.', 'ns')
						),
						'rewind'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Rewind', 'ns'),
							'desc'		=> __('Go backwards when the boundary has been reached i.e. clicking next on last item will slide back through all items to the first.', 'ns'),
						),
						'navText'		=> array(
							'type'		=> 'subfields',
							'default'	=> array('<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'),
							'label'		=> __('Navigation Text', 'ns'),
							'desc'		=> __('Specify the labels for the next and previous arrows.', 'ns'),
							'subfields'	=> array(
								'prev'	=> array(
									'type'		=> 'text',
									'default'	=> __('<span aria-label="Previous"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></span>', 'ns'),
									'label'		=> __('Previous', 'ns'),
									'desc'		=> ''
								),
									'next'	=> array(
										'type'		=> 'text',
										'default'	=> __('<span aria-label="Next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg></span>', 'ns'),
										'label'		=> __('Next', 'ns'),
										'desc'		=> ''
									)
							)
						),
						'slideBy'		=> array(
							'type'		=> 'text',
							'default'	=> 1,
							'label'		=> __('Slide By', 'ns'),
							'desc'		=> __('Number of items to slide by when navigating. \'page\' can be set to slide all visible items to reveal the next set.', 'ns')
						),
						'lazyLoad'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Lazyload', 'ns'),
							'desc'		=> __('Lazyload images.', 'ns')
						),
						'autoplay'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Autoplay', 'ns'),
							'desc'		=> __('Autoplay carousel slides.', 'ns')
						),
						'autoplayTimeout'		=> array(
							'type'		=> 'number',
							'default'	=> 5000,
							'label'		=> __('Autoplay Timeout', 'ns'),
							'desc'		=> __('The delay in milliseconds between autoplay slides.', 'ns'),
							'min'		=> 0
						),
						'autoplayHoverPause'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Autoplay Hover Pause', 'ns'),
							'desc'		=> __('Pause autoplay when the mouse is over the carousel.', 'ns')
						),
						'autoplaySpeed'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Autoplay Speed', 'ns'),
							'desc'		=> __('The time taken in milliseconds to slide the carousel to its new position when autoplay is enabled. 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'navSpeed'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Navigation Speed', 'ns'),
							'desc'		=> __('The time taken in milliseconds to slide the carousel to its new position when navigating. 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'dotsSpeed'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Dots Speed', 'ns'),
							'desc'		=> __('The time taken in milliseconds to slide the carousel to its new position when using the dots. 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'video'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Video', 'ns'),
							'desc'		=> __('Enable fetching of YouTube/Vimeo/Vzaar videos.', 'ns')
						),
						'videoHeight'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Video Height', 'ns'),
							'desc'		=> __('Set the height for videos. Set to 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'videoWidth'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Video Width', 'ns'),
							'desc'		=> __('Set the width for videos. Set to 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'rtl'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Right to Left (rtl)', 'ns'),
							'desc'		=> __('Set if the slider should work from right to left.', 'ns'),
							'min'		=> 0
						),
					)
				),
				'group-advanced'	=> array(
					'name'		=> 'advanced',
					'label'		=> __('Advanced Settings', 'ns'),
					'desc'		=> __('Settings for more advanced customisation of the carousel', 'ns'),
					'field_list'	=> array(
						'merge'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Merge', 'ns'),
							'desc'		=> __('Merge items to show a number of items as one (NOTE: individual slide options required for functionality).', 'ns')
						),
						'mergeFit'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Merge Fit', 'ns'),
							'desc'		=> __('Fit merged items if screen is smaller than items value.', 'ns')
						),
						'dotsEach'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Items per Dot', 'ns'),
							'desc'		=> __('The number of items a dot should represent (Set to 0 to use default).', 'ns'),
							'min'		=> 0
						),
						'slideTransition'		=> array(
							'type'		=> 'select',
							'default'	=> '',
							'label'		=> __('Slide Transition', 'ns'),
							'desc'		=> __('Select the transition used when navigating.', 'ns'),
							'options'	=> array(
								''			=> __('Linear', 'ns')
							)
						),
						'lazyLoadEager'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Lazyload Eager', 'ns'),
							'desc'		=> __('Eagerly pre-load images to the right (and left when loop is enabled) based on how many items you want to perload.', 'ns'),
							'min'		=> 0
						),
						'responsiveRefreshRate'		=> array(
							'type'		=> 'number',
							'default'	=> 200,
							'label'		=> __('Responsive Refresh Rate', 'ns'),
							'desc'		=> __('Time in milliseconds after the screen resizes that the carousel is refreshed to fit the new screen size.', 'ns'),
							'min'		=> 0
						),
						'mouseDrag'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Mouse Drag', 'ns'),
							'desc'		=> __('Enable mouse drag of carousel.', 'ns')
						),
						'touchDrag'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Touch Drag', 'ns'),
							'desc'		=> __('Enable touch drag of carousel.', 'ns')
						),
						'pullDrag'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Pull Drag', 'ns'),
							'desc'		=> __('Stage pull to edge.', 'ns')
						),
						'freeDrag'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Free Drag', 'ns'),
							'desc'		=> __('Item pull to edge.', 'ns')
						),
						'URLhashListener'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('URL Hash Listener', 'ns'),
							'desc'		=> __('Enable carousel to monitor the URL for hash ids to show a certain item e.g. #item-2 (NOTE: individual item setting required for reference)', 'ns')
						),
						'navElement'		=> array(
							'type'		=> 'text',
							'default'	=> 'button type="button" role="presentation"',
							'label'		=> __('Navigation Element', 'ns'),
							'desc'		=> __('The DOM element type for a single directional navigation link i.e. div', 'ns')
						),
						'responsiveBaseElement'		=> array(
							'type'		=> 'text',
							'default'	=> 'window',
							'label'		=> __('Responsive Base Element', 'ns'),
							'desc'		=> __('Selector for element to be monitored for size changes before updating the carousel i.e. window, #page-content', 'ns')
						),
						'itemElement'		=> array(
							'type'		=> 'text',
							'default'	=> 'div',
							'label'		=> __('Item Element', 'ns'),
							'desc'		=> __('Specify the DOM Element type for the generated owl-item e.g. div', 'ns')
						),
						'stageElement'		=> array(
							'type'		=> 'text',
							'default'	=> 'div',
							'label'		=> __('Stage Element', 'ns'),
							'desc'		=> __('Specify the DOM Element type for the generated owl-stage e.g. div', 'ns')
						),
						'navContainer'		=> array(
							'type'		=> 'text',
							'default'	=> '',
							'label'		=> __('Navigation Container', 'ns'),
							'desc'		=> __('Specify your own navigation container element.', 'ns')
						),
						'dotsContainer'		=> array(
							'type'		=> 'text',
							'default'	=> '',
							'label'		=> __('Dots Container', 'ns'),
							'desc'		=> __('Specify your own dots container element.', 'ns')
						),
						/*'dotsData'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Dots Data', 'ns'),
							'desc'		=> __('Used by data-dot content.', 'ns')
						),*/
						/*'smartSpeed'		=> array(
							'type'		=> 'number',
							'default'	=> 250,
							'label'		=> __('Smart Speed', 'ns'),
							'desc'		=> __('Speed calculate - more info to come...', 'ns')
						),*/
						/*'fluidSpeed'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 0,
							'label'		=> __('Fluid Speed', 'ns'),
							'desc'		=> __('Speed calculate - more info to come...', 'ns')
						),*/
						'dragEndSpeed'		=> array(
							'type'		=> 'number',
							'default'	=> 0,
							'label'		=> __('Drag End Speed', 'ns'),
							'desc'		=> __('Drag end speed. 0 to use default.', 'ns'),
							'min'		=> 0
						),
						'callbacks'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Callbacks', 'ns'),
							'desc'		=> __('Enable callback events.', 'ns')
						),
						'animateOut'		=> array(
							'type'		=> 'select',
							'default'	=> '',
							'label'		=> __('Animate Out', 'ns'),
							'desc'		=> __('Animation for item change finish.', 'ns'),
							'options'	=> array(
								''			=> __('Default', 'ns')
							)
						),
						'animateIn'		=> array(
							'type'		=> 'select',
							'default'	=> '',
							'label'		=> __('Animate In', 'ns'),
							'desc'		=> __('Animation for item change start.', 'ns'),
							'options'	=> array(
								''			=> __('Default', 'ns')
							)
						),
						'fallbackEasing'		=> array(
							'type'		=> 'text',
							'default'	=> 'swing',
							'label'		=> __('Fallback Easing', 'ns'),
							'desc'		=> __('Easing for old browsers and devices.', 'ns')
						),
						/*'info'		=> array(
							'type'		=> 'textarea',
							'default'	=> '',
							'label'		=> __('Information callback function', 'ns'),
							'desc'		=> __('Enter JavaScript/jQuery code to add custom functionality. Code can access \'args\' with current item/pages/widths data and \'$owl\' to access the current carousel DOM Element.', 'ns')
						),*/
						/*'nestedItemSelector'		=> array(
							'type'		=> 'string',
							'default'	=> '',
							'label'		=> __('Nested Item Selector', 'ns'),
							'desc'		=> __('Use if owl items are deep nested inside some generated content e.g. \'youritem\'. Do not use a dot before the class name.', 'ns')
						),*/
						'checkVisible'		=> array(
							'type'		=> 'checkbox',
							'default'	=> 1,
							'label'		=> __('Check Visible', 'ns'),
							'desc'		=> __('Disable this option is you know that the carousel will always be visible i.e. not hidden in an accordion.', 'ns')
						),
					)
				)
			)
		);
	}

}

new NS_Carousel_Owl_Options_Meta();
