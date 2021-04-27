<?php
/**
 * NS Carousel Posttype - Add slides metabox and fields
 * Self instantiated
 */

defined('ABSPATH') || exit;

/**
 * Features Class.
 */
class NS_Carousel_Slides_Meta {

	/**
	 * Metabox fields
	 */
	protected $metabox_fields;

	/**
	 * Constructor.
	 */
	public function __construct(){
        add_action('add_meta_boxes', array( $this, 'ns_carousel_slides_metabox') );
        add_action('save_post', array( $this, 'ns_carousel_slides_save_custom_meta') );

		//  Set up fields for processing
		$this->metabox_fields = new NS_Carousel_Metabox_Fields( $this->get_fields_config(), false );
	}

    /**
     * Add Meta Box and custom fields
     */
    public function ns_carousel_slides_metabox(){
        global $post;
        if( empty( $post ) ) return;

		do_action('ns-carousel-load-meta-data', $post );

    	add_meta_box('ns-carousel-slides',
    		__('Tiles', 'ns'),
    		array( $this, 'ns_carousel_slides_fields'),
    		'ns_carousel',
    		'advanced'
    	);
    }


    /**
     * Displays fields for Slide Options
     */
    public function ns_carousel_slides_fields(){
    	// Use nonce for verification
    	wp_nonce_field( basename( __FILE__ ), 'slides_meta_box_nonce');

		//  Display fields
	?>
		<div id="ns_carousel_tiles_options">
			<?php $this->metabox_fields->add_markup( $this->get_fields_config() ); ?>
		</div>
    <?php
	}

    /**
     *  Save the custom field data
     */
    public function ns_carousel_tiles_save_custom_meta( $post_id, $post, $update ){
		// Verify the nonce before proceeding.
        if( !isset( $_POST['slides_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['slides_meta_box_nonce'], basename( __FILE__ ) ) ){
			return;
		}

        // Stop WP from clearing custom fields on autosave
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return;
		}

        if( !current_user_can('edit_post', $post_id ) ){
    		return $post_id;
		}

        if( $post->post_type != 'ns_carousel' ){
			return;
		}

		//  Save fields
		$this->metabox_fields->save_fields( $post_id );
    }

	/**
	 * Set up the fields array
	 */
	private function get_fields_config(){
		return apply_filters(
			'ns-carousel-slide-options-fields',
			array(
				'slides'		=> array(
					'type'		=> 'repeater',
					'default'	=> false,
					'label'		=> __('Slide options', 'ns'),
					'desc'		=> __('Slide options for adding new tiles to the carousel.', 'ns'),
					'add_btn_text'	=> __('Add Slide', 'ns'),
					'subfields'	=> array(
						'image'		=> array(
							'type'		=> 'image',
							'default'	=> 0,
							'label'		=> __('Slide image', 'ns'),
							'desc'		=> __('Set the image to be used in the slide.', 'ns')
						),
					)
				)
			)
		);
	}

}
new NS_Carousel_Slides_Meta();
