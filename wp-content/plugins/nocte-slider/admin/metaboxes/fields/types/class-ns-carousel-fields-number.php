<?php
/**
 * NS Carousel number field class
 */

defined('ABSPATH') || exit;

/**
 * Number field class
 */
class NS_Carousel_Field_Number extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config, $post_id ){
        parent::__construct( $field_name, $field_config, $post_id );

        //  Filter default attributes to check for and add number field attributes
        add_filter('ns-carousel-field-attributes-'. $this->type, array( $this, 'filter_attributes'), 10, 2 );
    }

    /**
     *  Check field and attributes for number field specific attributes
     */
    public function filter_attributes( $attributes, $field ){
        //  Check field for Min/Max/Step
        if( isset( $field->min ) ){
            $attributes['min'] = $field->min % 1 == 0 ? intval( $field->min ) : floatval( $field->min );
        }
        if( isset( $field->max ) ){
            $attributes['max'] = $field->max % 1 == 0 ? intval( $field->max ) : floatval( $field->max );
        }
        if( isset( $field->step ) ){
            $attributes['step'] = $field->step % 1 == 0 ? intval( $field->step ) : floatval( $field->step );
        }

        return $attributes;
    }

}
