<?php
/**
 * NS Carousel fields
 * Utitlity class to construct the field mark up required in metaboxes
 */

defined('ABSPATH') || exit;

/**
 * Features Class.
 */
class NS_Carousel_Fields {

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
    }


    /**
     * Check field options are valid and check field method exists
     */
    private function check_options_method( $field_name, $field_options ){
        //  Check field_name, empty, lowercase, no spaces
        if( empty( $field_name ) ){
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
        //  Create field specific method name, check it exists and call it
        $method_name = 'add_field_markup_'. $field_options['type'];
        if( !method_exists( $this, $method_name ) ){
            //  Display error for unrecognised field type
            $this->display_error( __('Field type not recognised:', 'ns') .' '. $field_name );
            return false;
        }
        //  Call field type method
        $this->$method_name( $field_name, $field_options );

        return true;
    }

    /**
     * Mark up for a text field
     */
    private function add_field_markup_text( $field_name, $field_options ){
        //  Check field label
        $field_label = !empty( $field_options['label'] ) ? '<span class="label-text">'. $field_options['label'] .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $field_options['desc'] ) ? '<p class="field-desc">'. $field_options['desc'] .'</p>' : '';
        //  Check for wrapper classes
        $field_wrapper_classes = !empty( $field_options['wrapper_classes'] ) ? trim( $field_options['wrapper_classes'] ) : '';
        if( is_array( $field_wrapper_classes ) ){
            $field_wrapper_classes = implode(' ', $field_wrapper_classes );
        }
        $field_wrapper_classes = !empty( $field_wrapper_classes ) ? ' '. $field_wrapper_classes : '';
        //  Set up field attributes
        $field_atts = array();
        //  Set up input name
        $input_name = 'ns-carousel-'. $field_name;
        $field_atts['id'] = $input_name;
        $field_atts['class'] = $input_name;
        $field_atts['name'] = $input_name;
        //  Check value and default
        $field_default = isset( $field_options['default'] ) ? $field_options['default'] : '';
        $field_atts['value'] = isset( $field_options['value'] ) ? $field_options['value'] : $field_default;
        //  Check placeholder
        $field_atts['placeholder'] = isset( $field_options['placeholder'] ) ? $field_options['placeholder'] : $field_default;
        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $field_options['type'] ); ?>-field-wrapper<?php echo esc_attr( $field_wrapper_classes ); ?>">
                <label>
                    <?php echo $field_label; ?>
                    <input type="text" <?php echo $this->input_attrs( $field_atts ); ?>>
                    <?php echo $field_desc; ?>
                </label>
            </div>
        <?php
    }

    /**
     * Mark up for a number field
     */
    private function add_field_markup_number( $field_name, $field_options ){
        //  Check field label
        $field_label = !empty( $field_options['label'] ) ? '<span class="label-text">'. $field_options['label'] .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $field_options['desc'] ) ? '<p class="field-desc">'. $field_options['desc'] .'</p>' : '';
        //  Check for wrapper classes
        $field_wrapper_classes = !empty( $field_options['wrapper_classes'] ) ? trim( $field_options['wrapper_classes'] ) : '';
        if( is_array( $field_wrapper_classes ) ){
            $field_wrapper_classes = implode(' ', $field_wrapper_classes );
        }
        $field_wrapper_classes = !empty( $field_wrapper_classes ) ? ' '. $field_wrapper_classes : '';
        //  Set up field attributes
        $field_atts = array();
        //  Set up input name
        $input_name = 'ns-carousel-'. $field_name;
        $field_atts['id'] = $input_name;
        $field_atts['class'] = $input_name;
        $field_atts['name'] = $input_name;
        //  Check value and default
        $field_default = isset( $field_options['default'] ) ? $field_options['default'] : '';
        $field_atts['value'] = isset( $field_options['value'] ) ? $field_options['value'] : $field_default;
        //  Check placeholder
        $field_atts['placeholder'] = isset( $field_options['placeholder'] ) ? $field_options['placeholder'] : $field_default;
        //  Check min/max/step
        $field_atts['min'] = isset( $field_options['min'] ) ? $field_options['min'] : '';
        $field_atts['max'] = isset( $field_options['max'] ) ? $field_options['max'] : '';
        $field_atts['step'] = isset( $field_options['step'] ) ? $field_options['step'] : '';
        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $field_options['type'] ); ?>-field-wrapper<?php echo esc_attr( $field_wrapper_classes ); ?>">
                <label>
                    <?php echo $field_label; ?>
                    <input type="number" <?php echo $this->input_attrs( $field_atts ); ?>>
                    <?php echo $field_desc; ?>
                </label>
            </div>
        <?php
    }

    /**
     * Mark up for a checkbox field
     */
    private function add_field_markup_checkbox( $field_name, $field_options ){
        //  Check field label
        $field_label = !empty( $field_options['label'] ) ? '<span class="label-text">'. $field_options['label'] .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $field_options['desc'] ) ? '<p class="field-desc">'. $field_options['desc'] .'</p>' : '';
        //  Check for wrapper classes
        $field_wrapper_classes = !empty( $field_options['wrapper_classes'] ) ? trim( $field_options['wrapper_classes'] ) : '';
        if( is_array( $field_wrapper_classes ) ){
            $field_wrapper_classes = implode(' ', $field_wrapper_classes );
        }
        $field_wrapper_classes = !empty( $field_wrapper_classes ) ? ' '. $field_wrapper_classes : '';
        //  Set up field attributes
        $field_atts = array();
        //  Set up input name
        $input_name = 'ns-carousel-'. $field_name;
        $field_atts['id'] = $input_name;
        $field_atts['class'] = $input_name;
        $field_atts['name'] = $input_name;
        //  Check value and default
        $field_atts['value'] = 1;
        $field_default = isset( $field_options['default'] ) && $field_options['default'] == 1 ? 'selected' : '';
        $field_atts['selected'] = isset( $field_options['value'] ) ? ( $field_options['value'] == 1 ? 'selected' : '' ) : $field_default;
        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $field_options['type'] ); ?>-field-wrapper<?php echo esc_attr( $field_wrapper_classes ); ?>">
                <label>
                    <span class="checkbox-wrapper">
                        <input type="checkbox" <?php echo $this->input_attrs( $field_atts ); ?>>
                        <?php echo $field_label; ?>
                    </span>
                    <?php echo $field_desc; ?>
                </label>
            </div>
        <?php
    }

    /**
     * Mark up for a select field
     */
    private function add_field_markup_select( $field_name, $field_options ){
        //  Check for field options
        if( empty( $field_options['options'] ) ){
            //  Display error for missing options
            $this->display_error( __('Field options not found in field data:', 'ns') .' '. $field_name );
            return;
        }
        //  Check field label
        $field_label = !empty( $field_options['label'] ) ? '<span class="label-text">'. $field_options['label'] .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $field_options['desc'] ) ? '<p class="field-desc">'. $field_options['desc'] .'</p>' : '';
        //  Check for wrapper classes
        $field_wrapper_classes = !empty( $field_options['wrapper_classes'] ) ? trim( $field_options['wrapper_classes'] ) : '';
        if( is_array( $field_wrapper_classes ) ){
            $field_wrapper_classes = implode(' ', $field_wrapper_classes );
        }
        $field_wrapper_classes = !empty( $field_wrapper_classes ) ? ' '. $field_wrapper_classes : '';
        //  Set up field attributes
        $field_atts = array();
        //  Set up input name
        $input_name = 'ns-carousel-'. $field_name;
        $field_atts['id'] = $input_name;
        $field_atts['class'] = $input_name;
        $field_atts['name'] = $input_name;
        //  Check value and default
        $field_default = isset( $field_options['default'] ) ? $field_options['default'] : '';
        $field_value = isset( $field_options['value'] ) ? $field_options['value'] : $field_default;
        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $field_options['type'] ); ?>-field-wrapper<?php echo esc_attr( $field_wrapper_classes ); ?>">
                <label>
                    <?php echo $field_label; ?>
                    <select <?php echo $this->input_attrs( $field_atts ); ?>>
                    <?php //  Loop through options
                        foreach( $field_options as $option_val => $option_label ):
                            //  Check for selected
                            $option_selected = $field_value == $option_val ? ' selected="selected"' : '';
                    ?>
                        <option value="<?php echo esc_attr( $option_val ); ?>"><?php echo $option_label; ?></option>
                    <?php
                        endforeach;
                    ?>
                    </select>
                    <?php echo $field_desc; ?>
                </label>
            </div>
        <?php
    }

    /**
     * Mark up for a subfields field
     */
    private function add_field_markup_subfields( $field_name, $field_options ){
        //  Check for subfields
        if( empty( $field_options['subfields'] ) ){
            //  Display error for missing subfields
            $this->display_error( __('Subfields not found in field data:', 'ns') .' '. $field_name );
            return;
        }
        //  Check field label
        $field_label = !empty( $field_options['label'] ) ? '<span class="label-text">'. $field_options['label'] .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $field_options['desc'] ) ? '<p class="field-desc">'. $field_options['desc'] .'</p>' : '';
        //  Check for wrapper classes
        $field_wrapper_classes = !empty( $field_options['wrapper_classes'] ) ? trim( $field_options['wrapper_classes'] ) : '';
        if( is_array( $field_wrapper_classes ) ){
            $field_wrapper_classes = implode(' ', $field_wrapper_classes );
        }
        $field_wrapper_classes = !empty( $field_wrapper_classes ) ? ' '. $field_wrapper_classes : '';

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $field_options['type'] ); ?>-field-wrapper<?php echo esc_attr( $field_wrapper_classes ); ?>">
                <?php echo $field_label; ?>
                <div class="subfield-wrapper">
                <?php //  Loop through subfields and add individually
                    foreach( $field_options['subfields'] as $subfield_name => $subfield_options ){
                        $this->check_options_method( $subfield_name, $subfield_options );
                    }
                ?>
                </div>
                <?php echo $field_desc; ?>
            </div>
        <?php
    }


    /**
     * Create input attributes for markup
     */
    private function input_attrs( $field_atts ){
        if( empty( $field_atts ) ){
            return '';
        }
        foreach( $field_atts as $key => $val ){
            if( empty( $val ) ) continue;
            $field_atts[ $key ] = $key .'="'. esc_attr( $val ) .'"';
        }
        return implode(' ', $field_atts );
    }


    /**
     * Add markup to display an error message where the field would appear
     */
    private function display_error( $error_msg ){
    ?>
        <div class="ns-carousel-error">
            <p><strong><?php _e('ERROR:', 'ns'); ?></strong> <?php echo $error_msg; ?></p>
        </div>
    <?php
    }


    /**
     * Load in stored field values
     */
    public function load_values( $option_fields, $post_id ){
        if( empty( $option_fields ) || empty( $post_id ) ){
            return $option_fields;
        }
        //  Loop through options and attempt to retrieve stored data
        foreach( $option_fields as $field_name => $field_options ){
            $option_fields[ $field_name ]['value'] = get_post_meta( $post->ID, 'ns-carousel-options--'. $field_name, true );
        }
        return $option_fields;
    }
}
