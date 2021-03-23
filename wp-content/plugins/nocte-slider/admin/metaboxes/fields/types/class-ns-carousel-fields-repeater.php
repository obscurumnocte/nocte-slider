<?php
/**
 * NS Carousel repeater field class
 */

defined('ABSPATH') || exit;

/**
 * Repeater field class
 */
class NS_Carousel_Field_Repeater extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config, $post_id ){
        parent::__construct( $field_name, $field_config, $post_id );


    }

    /**
     *  Check data and create markup
     */
    public function display_field(){
        //  Check for subfields
        if( empty( $this->subfields ) ){
            //  Display error for missing subfields
            $this->display_error( __('Subfields not found in repeater field data:', 'ns') .' '. $this->name );
            return;
        }
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $this->desc ) ? '<span class="field-desc"> - '. $this->desc .'</span>' : '';

        //  Check values and display repeater groups with existing saved values. Add a template of repeater fields to be used for new data.
        $field_values = isset( $this->value ) && is_array( $this->value ) ? $this->value : array();

        //  Get instance of metabox fields to add subfields
        $metabox_fields = new NS_Carousel_Metabox_Fields();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                <?php // Check values and loop through to display saved data
                    if( !empty( $field_values ) ):
                        foreach( $field_values as $repeater_values ):
                            //  Set up values on subfields
                            foreach( $repeater_values as $repeater_val_key => $repeater_val_value ){
                                if( isset( $this->subfields[ $field_name .'[]['. $repeater_val_key .']'] ) ){
                                    $this->subfields[ $field_name .'[]['. $repeater_val_key .']']['value'] = $repeater_val_value;
                                }
                            }
                ?>
                            <div class="repeater-wrapper">
                            <?php //  Loop through subfields and add individually
                                foreach( $this->subfields as $subfield_name => $subfield_options ){
                                    $metabox_fields->check_options_method( $subfield_name, $subfield_options );
                                    //  Clear previously set values
                                    unset( $this->subfields[ $subfield_name ]['value'] );
                                }
                            ?>
                            </div>
                <?php
                        endforeach;
                    endif;
                ?>
                <div class="repeater-template repeater-wrapper">
                <?php //  Loop through subfields and add individually
                    foreach( $this->subfields as $subfield_name => $subfield_options ){
                        $metabox_fields->check_options_method( $subfield_name, $subfield_options );
                    }
                ?>
                </div>
            </div>
        <?php
    }

}
