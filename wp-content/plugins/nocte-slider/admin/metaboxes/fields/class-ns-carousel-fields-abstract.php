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

    public $errors;

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
        $this->input_name = 'ns-carousel-'. $field_name;
        $this->required = false;
        //  Loop through field_config and set field variables
        foreach( $field_config as $key => $value ){
            $this->$key = $value;
        }

        //  Load in any stored value
        add_action('ns-carousel-load-meta-data', array( $this, 'setup_post_id'), 10 );
        add_action('ns-carousel-load-meta-data', array( $this, 'load_stored_values'), 11 );
        add_action('ns-carousel-load-meta-data', array( $this, 'load_stored_errors'), 11 );
    }

    /**
     *  Set up post ID
     */
    public function setup_post_id( $carousel_id ){
        //  Check for specified carousel_id
        if( $carousel_id != 0 ){
            $this->post_id = $carousel_id;
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
        if( !empty( $stored_val ) || 0 === $stored_val || '0' === $stored_val ){
            $this->value = $stored_val;
        }
        return true;
    }

    /**
     *  Check for errors
     */
    public function load_stored_errors(){
        //  Initialize error array
        $this->errors = get_transient( $this->name .'--'. $this->post_id );
        //  Check transient and delete after setting
        if( empty( $this->errors ) ){
            $this->errors = array();

        } else {
            delete_transient( $this->name .'--'. $this->post_id );
        }
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
        $this->attributes['name'] = $this->input_name;
        $input_name = preg_replace('/[\[\]]/', '', $this->input_name );
        $this->attributes['id'] = empty( $this->attributes['id'] ) ? $input_name : $this->attributes['id'];
        $this->attributes['class'] = empty( $this->attributes['class'] ) ? $input_name : $this->attributes['class'];
        //  Check value and default
        $this->default = !empty( $this->default ) || 0 === $this->default ? $this->default : '';
        $this->attributes['value'] = !empty( $this->value ) || 0 === $this->value ? $this->value : $this->default;
        //  Check placeholder
        $this->attributes['placeholder'] = isset( $this->placeholder ) ? $this->placeholder : $this->default;

        $this->attributes = apply_filters('ns-carousel-field-attributes-'. $this->type, $this->attributes, $this );

        $field_errors = $this->get_validation_errors_markup();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <label>
                    <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                    <input <?php echo $this->input_attrs(); ?>>
                    <?php echo $field_errors; ?>
                </label>
            </div>
        <?php
    }

    /**
     *  Validate the submited data
     */
    public function validate_submission( $value ){
        //  Check value for required
        if( empty( $value ) && 0 !== $value && $this->required ){
            //  Need to give a validation error
            $this->validation_error( __('This field is required', 'ns') );
            return false;
        }
        //  Check value for type
        switch( $this->type ){
            case 'email':
                if( !is_email( $value ) ){
                    //  Need to give a validation error
                    $this->validation_error(  __('Not a valid email address', 'ns') );
                    return false;
                }
                break;

            case 'url':
                if( !wp_http_validate_url( $value ) ){
                    //  Need to give a validation error
                    $this->validation_error(  __('Not a valid URL', 'ns') );
                    return false;
                }
                break;

            case 'number':
                if( !is_numeric( $value ) ){
                    //  Need to give a validation error
                    $this->validation_error(  __('Not a valid number', 'ns') );
                    return false;
                }
                break;

            case 'text':
            case 'hidden':
            default:
                break;
        }
        return $value;
    }

    /**
     *  Format submited data for storage
     */
    public function format_data( $value ){
        switch( $this->type ){
            case 'text':
            case 'url':
                $value = sanitize_text_field( $value );
                break;
            case 'number':
                if( $value % 1 > 0 ){
                    $value = floatval( $value );

                } else {
                    $value = intval( $value );
                }
                break;
            case 'checkbox':
                if( empty( $value ) ){
                    $value = 0;
                } else {
                    $value = 1;
                }
                break;
            case 'email':
                $value = sanitize_email( $value );
                break;
            default:
                break;
        }

        return $value;
    }

    /**
     *  Set value
     */
    public function set_value( $value ){
        $this->value = $value;
    }

    /**
     *  Clear value
     */
    public function clear_value(){
        $this->value = '';
    }

    /**
     *  Get value
     */
    public function get_value(){
        switch( $this->type ){
            case 'text':
            case 'number':
                //  Check value is numeric
                if( !is_numeric( $this->value ) ){
                    //  If not set value
                    $value = $this->value;
                    break;
                }
                //  If numeric, check and convert
                if( $this->value % 1 > 0 ){
                    $value = floatval( $this->value );

                } else {
                    $value = intval( $this->value );
                }
                break;
            case 'checkbox':
                $value = $this->value == '1' ? true : false;
                break;
            default:
                $value = $this->value;
                break;
        }
        //  Return the formatted value
        return $value;
    }

    /**
     *  Get name
     */
    public function get_name(){
        return $this->name;
    }

    /**
     *  Save submited value
     */
    public function save_value( $post_id ){
        if( empty( $post_id ) ){
            return false;
        }
        $this->post_id = $post_id;

        //  Attempt to get value
        $value = '';
        if( isset( $_POST[ $this->input_name ] ) ){
            $value = $_POST[ $this->input_name ];
        }
        //  Validate the value
        $valid_submission = $this->validate_submission( $value );
        if( false === $valid_submission ){
            return;
        }
        //  Format the value
        $formatted_value = $this->format_data( $valid_submission );

        //  Save the formatted value
        if( empty( $formatted_value ) && 0 !== $formatted_value ){
            delete_post_meta( $this->post_id, $this->meta_key );

        } else {
            update_post_meta( $this->post_id, $this->meta_key, $formatted_value );
            //print'<pre>Updated value: post_id = '.$this->post_id.', meta_key = '.$this->meta_key.', value = '.print_r($formatted_value,true).'</pre>';
        }
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
     * Submitted validation errors
     */
    protected function validation_error( $msg ){
        if( empty( $msg ) ){
            return;
        }
        $this->errors[] = $msg;
        set_transient( $this->name .'--'. $this->post_id, $this->errors, 30 );
        return;
    }

    /**
     * Create the validation error markup
     */
    protected function get_validation_errors_markup(){
        $field_errors = '';
        if( !empty( $this->errors ) ){
            $field_errors .= '<div class="error-wrapper validation-errors"><ul>';
            foreach( $this->errors as $an_error ){
                $field_errors .= '<li>'. $an_error .'</li>';
            }
            $field_errors .= '</ul></div>';
        }
        return $field_errors;
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
