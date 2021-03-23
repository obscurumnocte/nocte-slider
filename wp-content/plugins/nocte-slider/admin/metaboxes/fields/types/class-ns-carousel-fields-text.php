<?php
/**
 * NS Carousel text field class
 */

defined('ABSPATH') || exit;

/**
 * Text field class
 */
class NS_Carousel_Field_Text extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config, $post_id ){
        parent::__construct( $field_name, $field_config, $post_id );
    }

}
