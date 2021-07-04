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

        //  Make sure jQuery sortable and draggable are queued
        add_action('admin_enqueue_scripts', array( $this, 'que_drag_sort') );

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
     *  Make sure jQuery sortable and draggable are enqueued for moving repeater blocks
     */
    public function que_drag_sort(){
        wp_enqueue_script('jquery-ui-sortable');
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
        $move_btn_text = !empty( $this->move_btn_text ) ? $this->move_btn_text : __('Move Item', 'ns');

        //  Check values and display repeater groups with existing saved values. Add a template of repeater fields to be used for new data.
        $field_values = isset( $this->value ) && is_array( $this->value ) ? $this->value : array();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                <div class="repeater-values-wrapper collapsable-fields">
                <?php // Check values and loop through to display saved data
                    if( !empty( $field_values ) ):
                        $counter = 0;
                        foreach( $field_values as $repeater_values ):
                            //print'<pre>repeater_values = '.print_r($repeater_values,true).'</pre>';
                ?>
                            <div class="repeater-wrapper">
                                <div class="collapsable-content">
                                    <?php //  Loop through subfields and add individually
                                        foreach( $this->metabox_fields->fields as $a_field ):
                                            //  Set up field value
                                            //print'<pre>repeater field = '.print_r($a_field,true).'</pre>';
                                            $a_field->clear_value();
                                            if( array_key_exists( $a_field->name, $repeater_values ) ){
                                                //print'<pre>field value = '.print_r($repeater_values[ $a_field->name ],true).'</pre>';
                                                $a_field->set_value( $repeater_values[ $a_field->name ] );
                                            }
                                            //  Change field name to group subfields together correctly
                                            $tmp_field_name = $a_field->input_name;
                                            $a_field->input_name = 'ns-carousel-'. $this->name .'['. $counter .']['. str_replace( $this->name .'[]', '', $a_field->name ) .']';
                                            //print'<pre>'.$a_field->input_name.'</pre>';
                                            //  Display field
                                            $a_field->display_field();
                                            //  Clear preset value and reset field name
                                            $a_field->clear_value();
                                            $a_field->input_name = $tmp_field_name;
                                        endforeach;
                                    ?>
                                </div>
                                <div class="controls-wrapper">
                                    <div class="collapse-controls">
                                        <div class="move-btn"><span class="screen-reader-text"><?php echo $move_btn_text; ?></span><svg viewBox="40 0 20 100" class="repeater-move"><use xlink:href="#nsc-vert-move"/></svg></div>
                                        <button class="collapse-btn"><span class="screen-reader-text"><?php echo $accordion_btn_text; ?></span><svg viewBox="0 0 1382 882" class="accordion-chevron"><use xlink:href="#nsc-accordion-chevron"/></svg></button>
                                    </div>
                                    <button class="repeater-delete"><span class="screen-reader-text"><?php echo $trash_btn_text; ?></span><svg viewBox="0 0 875 1000" class="repeater-trash"><use xlink:href="#nsc-trash"/></svg></button>
                                </div>
                            </div>
                <?php
                            $counter++;
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
                        foreach( $this->metabox_fields->fields as $a_field ):
                            $a_field->clear_value();
                            $a_field->display_field();
                        endforeach;
                    ?>
                    </div>
                    <div class="controls-wrapper">
                        <div class="collapse-controls">
                            <div class="move-btn"><span class="screen-reader-text"><?php echo $move_btn_text; ?></span><svg viewBox="40 0 20 100" class="repeater-move"><use xlink:href="#nsc-vert-move"/></svg></div>
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
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" viewBox="0 0 100 100" xml:space="preserve" style="display:none;">
                <defs>
                    <g id="nsc-vert-move" viewBox="0 0 100 100">
                        <path fill="#323232" d="M42.917 75.294H32.79L50 92.5l17.21-17.206H57.083V50H42.917z"/>
                        <path fill="#666" d="M67.21 24.706L50 7.5 32.79 24.706h10.127V50h14.166V24.706z"/>
                    </g>
                </defs>
            </svg>
        </div>
    <?php
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

        //  Loop through the values and fields to validate and format the submitted data
        if( !empty( $value ) ):
            foreach( $value as $val_key => $repeater_values ):
                //print'<pre>repeater_values = '.print_r($repeater_values,true).'</pre>';
                //  Set up values on metabox fields
                foreach( $this->metabox_fields->fields as $a_field ){
                    if( array_key_exists( $a_field->name, $repeater_values ) ){
                        if( false !== $a_field->validate_submission( $repeater_values[ $a_field->name ] ) ){
                            $value[ $val_key ][ $a_field->name ] = $a_field->format_data( $repeater_values[ $a_field->name ] );

                        } else {
                            $value[ $val_key ][ $a_field->name ] = '';
                        }

                    } else {
                        //print'<pre>field data = '.print_r($a_field,true).'</pre>';
                        $value[ $val_key ][ $a_field->name ] = $a_field->format_data('');
                    }
                }
            endforeach;
        endif;

        //  Save the value
        if( empty( $value ) ){
            delete_post_meta( $this->post_id, $this->meta_key );

        } else {
            update_post_meta( $this->post_id, $this->meta_key, $value );
            //print'<pre>Updated value: post_id = '.$this->post_id.', meta_key = '.$this->meta_key.', value = '.print_r($value,true).'</pre>';
        }
    }

    /**
     *  Get value
     */
    public function get_value(){
        $formatted_data = $this->value;
        if( !empty( $this->value ) && is_array( $this->value ) ):
            $formatted_data = array();
            foreach( $this->value as $values_key => $repeater_values ):
                //  Loop through subfields and get correctly formatted values
                foreach( $this->metabox_fields->fields as $a_field ):
                    //  Set up field value
                    $a_field->clear_value();
                    if( array_key_exists( $a_field->name, $repeater_values ) ){
                        $a_field->set_value( $repeater_values[ $a_field->name ] );
                        if( !isset( $formatted_data[ $values_key ] ) ){
                            $formatted_data[ $values_key ] = array();
                        }
                        $formatted_data[ $values_key ][ $a_field->name ] = $a_field->get_value();
                    }
                endforeach;
            endforeach;
        endif;

        return $formatted_data;
    }

}
