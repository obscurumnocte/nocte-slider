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
    public function __construct( $option_fields, $include_groups = true ){
        $this->fields = array();

        if( $include_groups ){
            //  Set up fields from provided options
            foreach( $option_fields as $group_fields ){
                foreach( $group_fields['field_list'] as $field_name => $field_options ){
                    $this->check_options_method( $field_name, $field_options );
                }
            }

        } else {
            foreach( $option_fields as $field_name => $field_options ){
                $this->check_options_method( $field_name, $field_options );
            }
        }
    }

    /**
     * Create field markup
     */
    public function add_markup( $options ){
        if( empty( $options ) ) return;

        //  Options are grouped, add group markup
        $group_count = 0;
        foreach( $options as $option_group ):
            $group_count++;
            $closed_class = $group_count > 1 ? ' group-closed' : '';
            $desc = !empty( $option_group['desc'] ) ? '<p>'. $option_group['desc'] .'</p>' : '';
        ?>
            <div class="group-wrapper group-<?php echo esc_attr( $option_group['name'] ); echo $closed_class; ?>">
                <header class="group-header">
                    <h3><?php echo $option_group['label']; ?></h3>
                    <?php echo $desc; ?>
                </header>

                <div class="fieldset-wrapper">
                <?php
                    // Loop through options and add fields
                    foreach( $this->fields as $a_field ):
                        if( array_key_exists( $a_field->name, $option_group['field_list'] ) ){
                            $a_field->display_field();
                        }
                    endforeach;
                ?>
                </div>
            </div>
        <?php
        endforeach;

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
        $this->fields[] = new $class_name( $field_name, $field_options );

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


	/**
	 * Utility function to check for valid json
	 */
	private function is_json( $json_data ){
		json_decode( $json_data );
		return ( ( json_last_error() == JSON_ERROR_NONE ) && !is_numeric( $json_data ) );
	}
}
