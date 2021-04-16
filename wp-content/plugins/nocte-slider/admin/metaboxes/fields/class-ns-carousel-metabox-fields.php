<?php
/**
 * NS Carousel metabox fields
 * Utitlity class to construct the field for metaboxes
 */

defined('ABSPATH') || exit;

/**
 * Features Class.
 */
class NS_Carousel_Metabox_Fields {

    public $fields;

    /**
     * Set up class
     */
    public function __construct(){
        $this->fields = array();
    }

    /**
     * Create field markup
     */
    public function add_markup( $options ){
        if( empty( $options ) ) return;

        //  Add fieldset wrapper
        ?>
        <div class="fieldset-wrapper">
        <?php
            // Loop through options and add fields
            foreach( $options as $field_name => $field_options ):
                $this->check_options_method( $field_name, $field_options );
            endforeach;
        ?>
        </div>
        <?php
        // Add action to allow fields to add common code i.e. SVGs
        do_action('ns_carousel_metabox_fields_common_code');
    }


    /**
     * Check field options are valid and check field method exists
     */
    public function check_options_method( $field_name, $field_options ){
        //  Check field_name, empty, lowercase, no spaces
        if( empty( $field_name ) || is_numeric( $field_name ) ){
            //  Display error for invalid field name
            $this->display_error( __('Invalid field name.', 'ns') );
            return false;
        }
        //  Check field type
        if( !isset( $field_options['type'] ) ){
            //  Display error for invalid field data
            $this->display_error( __('Invalid field data (type):', 'ns') .' '. $field_name );
            return false;
        }
        //  Create field specific class name, check it exists and to field list
        $class_name = 'NS_Carousel_Field_'. ucfirst( $field_options['type'] );
        if( !class_exists( $class_name ) ){
            //  Display error for unrecognised field type
            $this->display_error( __('Field type not recognised:', 'ns') .' '. $field_name );
            return false;
        }
        //  Call field type method
        $this->fields[] = new $class_name( $field_name, $field_options, $post->ID );
        $this->fields[ count( $this->fields ) - 1 ]->display_field();

        return true;
    }

    /**
     * Save field data
     */
    public function save_fields( $post_id ){
        if( empty( $post_id ) ){
            return false;
        }

        //print'<pre>fields = '.print_r($this->fields,true).'</pre>';//exit;
        //  Loop through fields, validate, sanitise and save data
        foreach( $this->fields as $meta_field ){
            $meta_field->save_value( $post_id );
        }
        //exit;
    }
}
