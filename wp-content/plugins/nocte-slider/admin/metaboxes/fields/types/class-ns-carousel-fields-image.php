<?php
/**
 * NS Carousel image field class
 */

defined('ABSPATH') || exit;

/**
 * Text field class
 */
class NS_Carousel_Field_Image extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config ){
        parent::__construct( $field_name, $field_config );
        //  Make sure the media library code is loaded
        add_action('admin_enqueue_scripts', array( $this, 'load_media_library'), 10 );
    }

    /**
     *  Make sure the media library code is loaded to support upload of images.
     */
    public function load_media_library(){
        if( !did_action('wp_enqueue_media') ){
    		wp_enqueue_media();
    	}
    }

    /**
     *  Check data and create markup
     */
    public function display_field(){
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $this->desc ) ? '<span class="field-desc"> - '. $this->desc .'</span>' : '';
        //  Set up field attributes
        //  Add type attribute
        $this->attributes['type'] = 'hidden';
        //  Set up input name
        $this->attributes['name'] = $this->input_name;
        $input_name = preg_replace('/[\[\]]/', '', $this->input_name );
        $this->attributes['id'] = empty( $this->attributes['id'] ) ? $input_name : $this->attributes['id'];
        $this->attributes['class'] = empty( $this->attributes['class'] ) ? $input_name : $this->attributes['class'];
        //  Check value and default
        $this->default = !empty( $this->default ) || 0 === $this->default ? $this->default : '';
        $this->attributes['value'] = !empty( $this->value ) || 0 === $this->value ? $this->value : $this->default;

        $this->attributes = apply_filters('ns-carousel-field-attributes-'. $this->type, $this->attributes, $this );

        $field_errors = $this->get_validation_errors_markup();

        $add_img = plugins_url() .'/'. Nocte_Slider_Data::get_plugin_name() .'/admin/imgs/plus-circle.svg';
        $image = wp_get_attachment_image_src( $this->value );
        if( !empty( $image ) ){
            $image = $image[0];

        } else {
            $image = $add_img;
        }

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <label>
                    <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                    <a href="#" class="ns-upl" style="display:inline-block;" ns-media-title="<?php _e('Use Image', 'ns'); ?>" ns-media-btn-text="<?php _e('Use this image', 'ns'); ?>" title="<?php _e('Add Image', 'ns'); ?>"><img data-placeholder="<?php echo esc_url( $add_img ); ?>" src="<?php echo esc_url( $image ); ?>" width="200" /></a>
                    <a href="#" class="ns-rmv button" title="<?php _e('Remove image', 'ns'); ?>"><?php _e('Remove image', 'ns'); ?></a>
                    <input <?php echo $this->input_attrs(); ?>>
                    <?php echo $field_errors; ?>
                </label>
            </div>
        <?php
    }

}
