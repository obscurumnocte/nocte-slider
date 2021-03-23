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
            $this->display_error( __('Subfields not found in field data:', 'ns') .' '. $this->name );
            return;
        }
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        //  Check field description
        $field_desc = !empty( $this->desc ) ? '<span class="field-desc"> - '. $this->desc .'</span>' : '';

        //  Get instance of metabox fields to add subfields
        $metabox_fields = new NS_Carousel_Metabox_Fields();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                <div class="subfield-wrapper">
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
