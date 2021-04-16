<?php
/**
 * NS Carousel checkbox field class
 */

defined('ABSPATH') || exit;

/**
 * Checkbox field class
 */
class NS_Carousel_Field_Checkbox extends NS_Carousel_Field {

    /**
     *  Field constructor to set up field variables and actions/filters
     */
    public function __construct( $field_name, $field_config ){
        parent::__construct( $field_name, $field_config );
    }

    /**
     *  Check data and create markup
     */
    public function display_field(){
        //  Check field label
        $field_label = !empty( $this->label ) ? '<span class="label-text">'. $this->label .'</span>' : '';
        $field_checkbox_label = !empty( $this->label ) ? '<span class="checkbox-label-text">'. $this->label .'</span>' : '';
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
        $this->attributes['value'] = 1;
        $this->default = isset( $this->default ) && $this->default == 1 ? 'checked' : '';
        $this->attributes['checked'] = isset( $this->value ) ? ( $this->value == 1 ? 'checked' : '' ) : $this->default;

        $this->attributes = apply_filters('ns-carousel-field-attributes-'. $this->type, $this->attributes, $this );

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <label>
                    <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                    <span class="checkbox-wrapper">
                        <input <?php echo $this->input_attrs(); ?>>
                        <?php echo $field_checkbox_label; ?>
                    </span>
                </label>
            </div>
        <?php
    }

}
