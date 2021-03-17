<?php
/**
 * NS Carousel Posttype - Add tiles metabox and fields
 */

defined('ABSPATH') || exit;

/**
 * Features Class.
 */
class NS_Carousel_Tiles_Meta {

	/**
	 * Constructor.
	 */
	public function __construct() {
        add_action('add_meta_boxes', array( $this, 'ns_carousel_tiles_metabox') );
        add_action('save_post', array( $this, 'ns_carousel_tiles_save_custom_meta') );
	}

    /**
     * Add Meta Box and custom fields
     */
    function ns_carousel_tiles_metabox(){
        global $post;

        if( empty( $post ) ) return;

    	add_meta_box('ns-carousel-tiles',
    		__('Tiles', 'ns'),
    		array( $this, 'ns_carousel_tiles_fields'),
    		'ns_carousel',
    		'advanced'
    	);
    }


    /**
     * Displays fields event closedir
     */
    function ns_carousel_tiles_fields(){
    	global $post;
    	// Use nonce for verification
    	wp_nonce_field( basename( __FILE__ ), 'tiles_meta_box_nonce');
        //  Get stored values
        $tiles = get_post_meta( $post->ID, 'ns-carousel-tiles', true );
	?>
		<div class="tiles-wrapper">
            <input type="hidden" id="ns-carousel-tiles" name="ns-carousel-tiles" value="<?php echo esc_attr( json_encode( $tiles ) ); ?>">
	    </div>
    <?php
    }

    /**
     *  Save the custom field data
     */
    function ns_carousel_tiles_save_custom_meta( $post_id ){
        global $post;

		// Verify the nonce before proceeding.
        if( !isset( $_POST['tiles_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['tiles_meta_box_nonce'], basename( __FILE__ ) ) ) return;

        // Stop WP from clearing custom fields on autosave
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

        if( !current_user_can('edit_post', $post_id ) )
    		return $post_id;

        if( $post->post_type != 'ns_carousel' ) return;

		// ------------------------------  TILES  ------------------------------ //
	    if( !empty( $_POST['ns-carousel-tiles'] ) && $this->is_json( $_POST['ns-carousel-tiles'] ) ){
	        //  If specified, checkbox is checked, set meta data
	        update_post_meta( $post_id, 'ns-carousel-tiles', $_POST['ns-carousel-tiles'] );

	    } else {
			delete_post_meta( $post_id, 'ns-carousel-tiles');
		}
    }


	/**
	 * Utility function to check for valid json
	 */
	private function is_json( $json_data ){
		json_decode( $json_data );
		return ( ( json_last_error() == JSON_ERROR_NONE ) && !is_numeric( $json_data ) );
	}

}

new NS_Carousel_Tiles_Meta();
