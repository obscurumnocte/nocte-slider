<?php
/**
 * NS Carousel subfields field class
 */

defined('ABSPATH') || exit;

/**
 * Subfields field class
 */
class NS_Carousel_Field_Subfields extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config ){
        parent::__construct( $field_name, $field_config );


        //  Check for subfields, is exist, set up fields
        if( !empty( $this->subfields ) ){
            //  Get instance of metabox fields to add subfields
            $this->metabox_fields = new NS_Carousel_Metabox_Fields( $this->subfields, false );
            //print'<pre>metabox_fields = '.print_r($this->metabox_fields->fields,true).'</pre>';
            foreach( $this->metabox_fields->fields as $a_field ){
                $a_field->input_name = 'ns-carousel-'. $this->name .'[]['. $a_field->name .']';
            }
        }
    }

    /**
     *  Check data and create markup
     */
    public function display_field(){
        //  Check for subfields
        if( empty( $this->subfields ) ){
            //  Display error for missing subfields
            $this->display_error( __('Subfields not found in field data:', 'ns') .' '. $this->name );
            return;
        }
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $this->desc ) ? '<span class="field-desc"> - '. $this->desc .'</span>' : '';

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                <div class="subfield-wrapper">
                <?php //  Loop through subfields and add individually
                    foreach( $this->metabox_fields->fields as $a_field ){
                        $a_field->clear_value();
                        if( array_key_exists( $a_field->name, $field_values ) ){
                            $a_field->set_value( $field_values[ $a_field->name ] );
                        }
                        $a_field->display_field();
                    }
                ?>
                </div>
            </div>
        <?php
    }

}
