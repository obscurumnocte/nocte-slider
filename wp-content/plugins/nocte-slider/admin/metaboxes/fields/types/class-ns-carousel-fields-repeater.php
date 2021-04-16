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
    public function __construct( $field_name, $field_config ){
        parent::__construct( $field_name, $field_config );

        if( !has_action('ns_carousel_metabox_fields_common_code', 'NS_Carousel_Field_Repeater::add_svgs') ){
            add_action('ns_carousel_metabox_fields_common_code', 'NS_Carousel_Field_Repeater::add_svgs');
        }
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

        $add_btn_text = !empty( $this->add_btn_text ) ? $this->add_btn_text : __('Add Item', 'ns');
        $accordion_btn_text = !empty( $this->accordion_btn_text ) ? $this->accordion_btn_text : __('Open/Close Item', 'ns');
        $trash_btn_text = !empty( $this->trash_btn_text ) ? $this->trash_btn_text : __('Delete Item', 'ns');

        //  Check values and display repeater groups with existing saved values. Add a template of repeater fields to be used for new data.
        $field_values = isset( $this->value ) && is_array( $this->value ) ? $this->value : array();

        //  Get instance of metabox fields to add subfields
        $metabox_fields = new NS_Carousel_Metabox_Fields();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                <div class="repeater-values-wrapper collapsable-fields">
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
                                <div class="collapsable-content">
                                    <?php //  Loop through subfields and add individually
                                        foreach( $this->subfields as $subfield_name => $subfield_options ){
                                            $metabox_fields->check_options_method( $subfield_name, $subfield_options );
                                            //  Clear previously set values
                                            unset( $this->subfields[ $subfield_name ]['value'] );
                                        }
                                    ?>
                                </div>
                                <div class="controls-wrapper">
                                    <div class="collapse-controls">
                                        <button class="collapse-btn"><span class="screen-reader-text"><?php echo $accordion_btn_text; ?></span><svg viewBox="0 0 1382 882" class="accordion-chevron"><use xlink:href="#nsc-accordion-chevron"/></svg></button>
                                    </div>
                                    <button class="repeater-delete"><span class="screen-reader-text"><?php echo $trash_btn_text; ?></span><svg viewBox="0 0 875 1000" class="repeater-trash"><use xlink:href="#nsc-trash"/></svg></button>
                                </div>
                            </div>
                <?php
                        endforeach;
                    endif;
                ?>
                </div>
                <div class="repeater-controls-wrapper">
                    <button class="add-repeater-item button"><?php echo $add_btn_text; ?></button>
                </div>

                <div class="repeater-template repeater-wrapper">
                    <div class="collapsable-content">
                    <?php //  Loop through subfields and add individually
                        foreach( $this->subfields as $subfield_name => $subfield_options ){
                            $metabox_fields->check_options_method( $subfield_name, $subfield_options );
                        }
                    ?>
                    </div>
                    <div class="controls-wrapper">
                        <div class="collapse-controls">
                            <button class="collapse-btn"><span class="screen-reader-text"><?php echo $accordion_btn_text; ?></span><svg viewBox="0 0 1382 882" class="accordion-chevron"><use xlink:href="#nsc-accordion-chevron"/></svg></button>
                        </div>
                        <button class="repeater-delete"><span class="screen-reader-text"><?php echo $trash_btn_text; ?></span><svg viewBox="0 0 875 1000" class="repeater-trash"><use xlink:href="#nsc-trash"/></svg></button>
                    </div>
                </div>
            </div>
        <?php
    }


    /**
     *  Add the SVG code needed for the functionality of the repeater field
     *  Static function to hook only once and add SVG code only once
     */
    public static function add_svgs(){
    ?>
        <div class="repeater-svgs-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1382 882" enable-background="new 0 0 1382 1662" xml:space="preserve" style="display:none;">
                <defs>
                    <g id="nsc-accordion-chevron" viewBox="0 0 1382 1662">
                        <path d="M863.98,336.626L726.921,473.685l347.587,347.587c51.905,51.908,136.368,51.908,188.274,0l42.5-42.499   c51.908-51.906,51.908-136.367,0-188.274L917.57,202.788C918.614,255.221,898.335,302.272,863.98,336.626z"/>
                        <path d="M828.059,300.705c25.022-25.021,38.804-58.452,38.804-94.137c0-64.66-37.283-92.616-81.301-136.638   c-51.899-51.899-136.36-51.914-188.275,0L76.719,590.499c-51.908,51.906-51.908,136.367,0,188.274l42.499,42.499   c51.905,51.908,136.368,51.908,188.274,0L828.059,300.705z"/>
                    </g>
                </defs>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" height="1000" width="875" style="display:none;">
                <defs>
                    <g id="nsc-trash" viewBox="0 0 875 1000">
                        <path d="M0 281.296l0 -68.355q1.953 -37.107 29.295 -62.496t64.449 -25.389l93.744 0l0 -31.248q0 -39.06 27.342 -66.402t66.402 -27.342l312.48 0q39.06 0 66.402 27.342t27.342 66.402l0 31.248l93.744 0q37.107 0 64.449 25.389t29.295 62.496l0 68.355q0 25.389 -18.553 43.943t-43.943 18.553l0 531.216q0 52.731 -36.13 88.862t-88.862 36.13l-499.968 0q-52.731 0 -88.862 -36.13t-36.13 -88.862l0 -531.216q-25.389 0 -43.943 -18.553t-18.553 -43.943zm62.496 0l749.952 0l0 -62.496q0 -13.671 -8.789 -22.46t-22.46 -8.789l-687.456 0q-13.671 0 -22.46 8.789t-8.789 22.46l0 62.496zm62.496 593.712q0 25.389 18.553 43.943t43.943 18.553l499.968 0q25.389 0 43.943 -18.553t18.553 -43.943l0 -531.216l-624.96 0l0 531.216zm62.496 -31.248l0 -406.224q0 -13.671 8.789 -22.46t22.46 -8.789l62.496 0q13.671 0 22.46 8.789t8.789 22.46l0 406.224q0 13.671 -8.789 22.46t-22.46 8.789l-62.496 0q-13.671 0 -22.46 -8.789t-8.789 -22.46zm31.248 0l62.496 0l0 -406.224l-62.496 0l0 406.224zm31.248 -718.704l374.976 0l0 -31.248q0 -13.671 -8.789 -22.46t-22.46 -8.789l-312.48 0q-13.671 0 -22.46 8.789t-8.789 22.46l0 31.248zm124.992 718.704l0 -406.224q0 -13.671 8.789 -22.46t22.46 -8.789l62.496 0q13.671 0 22.46 8.789t8.789 22.46l0 406.224q0 13.671 -8.789 22.46t-22.46 8.789l-62.496 0q-13.671 0 -22.46 -8.789t-8.789 -22.46zm31.248 0l62.496 0l0 -406.224l-62.496 0l0 406.224zm156.24 0l0 -406.224q0 -13.671 8.789 -22.46t22.46 -8.789l62.496 0q13.671 0 22.46 8.789t8.789 22.46l0 406.224q0 13.671 -8.789 22.46t-22.46 8.789l-62.496 0q-13.671 0 -22.46 -8.789t-8.789 -22.46zm31.248 0l62.496 0l0 -406.224l-62.496 0l0 406.224z"/>
                    </g>
                </defs>
            </svg>
        </div>
    <?php
    }

}
