<?php
/**
 * Abstract NS Carousel field class
 */

defined('ABSPATH') || exit;

/**
 * Generic field class
 */
abstract class NS_Carousel_Field {

    /**
     *  Declare key variables for field creation
     */
    public $name;

    public $post_id;
    public $meta_key;
    public $value;

    public $type;
    public $required;
    public $label;
    public $desc;

    public $attributes;

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config ){
        //  Check field_name and field_config
        if( empty( $field_name ) || empty( $field_config ) || !is_array( $field_config ) ){
            return;
        }

        //  Set up field variables
        $this->name = $field_name;
        $this->meta_key = 'ns-carousel-options--'. $field_name;
        //  Loop through field_config and set field variables
        foreach( $field_config as $key => $value ){
            $this->$key = $value;
        }

        //  Load in any stored value
        $this->load_stored_values();

        if( !empty( $this->type ) ){
            //  Set up hooks for submission processing
            add_filter('ns-carousel-validate-'. $this->type .'-field', array( $this, 'validate_submission') );
            add_filter('ns-carousel-format-data-'. $this->type .'-field', array( $this, 'format_data') );
        }
    }

    /**
     *  Load stored data into field
     */
    public function load_stored_values(){
        if( empty( $this->post_id ) ){
            return false;
        }
        //  Retrieve stored data and check if value should be set
        $stored_val = get_post_meta( $this->post_id, $this->meta_key, true );
        if( !empty( $stored_val ) || 0 === $stored_val ){
            $this->value = $stored_val;
        }
        return true;
    }

    /**
     *  Check data and create markup
     */
    public function display_field(){
        //  Check type
        if( !in_array( $this->type, array('text', 'number', 'hidden', 'email', 'url') ) ){
            $this->display_error( __('Field type not supported:', 'ns') .' '. $this->name );
            return false;
        }
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $this->desc ) ? '<span class="field-desc"> - '. $this->desc .'</span>' : '';
        //  Set up field attributes
        //  Add type attribute
        $this->attributes['type'] = $this->type;
        //  Set up input name
        $input_name = 'ns-carousel-'. $this->name;
        $this->attributes['name'] = $input_name;
        $input_name = preg_replace('/[\[\]]/', '', $input_name );
        $this->attributes['id'] = empty( $this->attributes['id'] ) ? $input_name : $this->attributes['id'];
        $this->attributes['class'] = empty( $this->attributes['class'] ) ? $input_name : $this->attributes['class'];
        //  Check value and default
        $this->default = !empty( $this->default ) || 0 === $this->default ? $this->default : '';
        $this->attributes['value'] = !empty( $this->value ) || 0 === $this->value ? $this->value : $this->default;
        //  Check placeholder
        $this->attributes['placeholder'] = isset( $this->placeholder ) ? $this->placeholder : $this->default;

        $this->attributes = apply_filters('ns-carousel-field-attributes-'. $this->type, $this->attributes, $this );

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <label>
                    <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                    <input <?php echo $this->input_attrs(); ?>>
                </label>
            </div>
        <?php
    }

    /**
     *  Validate the submited data
     */
    public function validate_submission( $value ){
        if( empty( $value ) && $this->required ){
            //  Need to give a validation error
            return false;
        }
        switch ($this->type){
            case 'email':
                if( !is_email( $value ) ){
                    //  Need to give a validation error
                    return false;
                }
                break;

            case 'url':
                if( !wp_http_validate_url( $value ) ){
                    //  Need to give a validation error
                    return false;
                }
                break;

            case 'number':
                if( !is_numeric( $value ) ){
                    //  Need to give a validation error
                    return false;
                }
                break;

            case 'text':
            case 'hidden':
                break;
        }
        return true;
    }

    /**
     *  Format submited data for storage
     */
    public function format_data( $value ){
        switch( $this->type ){
            case 'text':
                $value = sanitize_text_field( $value );
                break;
            case 'hidden':
            case 'email':
            case 'url':
            case 'number':
                break;
        }

        return $value;
    }

    /**
     * Add markup to display an error message where the field would appear
     */
    protected function display_error( $error_msg ){
    ?>
        <div class="ns-carousel-error">
            <p><strong><?php _e('ERROR:', 'ns'); ?></strong> <?php echo $error_msg; ?></p>
        </div>
    <?php
    }

    /**
     * Create input attributes for markup
     */
    protected function input_attrs(){
        if( empty( $this->attributes ) ){
            return '';
        }
        $field_atts = array();
        foreach( $this->attributes as $key => $val ){
            if( empty( $val ) && 0 !== $val ) continue;
            $field_atts[ $key ] = $key .'="'. esc_attr( $val ) .'"';
        }
        return implode(' ', $field_atts );
    }

}
