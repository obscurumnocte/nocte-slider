<?php
/**
 * NS Carousel select field class
 */

defined('ABSPATH') || exit;

/**
 * Select field class
 */
class NS_Carousel_Field_Select extends NS_Carousel_Field {

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
        //  Check for field options
        if( empty( $this->options ) ){
            //  Display error for missing options
            $this->display_error( __('Field options not found in field data:', 'ns') .' '. $this->name );
            return;
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
        $field_value = !empty( $this->value ) || 0 === $this->value ? $this->value : $this->default;

        $this->attributes = apply_filters('ns-carousel-field-attributes-'. $this->type, $this->attributes, $this );

        $field_errors = $this->get_validation_errors_markup();

        //  Add markup
        ?>
            <div class="field-wrapper <?php echo esc_attr( $this->type ); ?>-field-wrapper">
                <label>
                    <p><?php echo $field_label; ?><?php echo $field_desc; ?></p>
                    <select <?php echo $this->input_attrs( $field_atts ); ?>>
                    <?php //  Loop through options
                        foreach( $this->options as $option_val => $option_label ):
                            //  Check for selected
                            $option_selected = $field_value == $option_val ? ' selected="selected"' : '';
                    ?>
                        <option value="<?php echo esc_attr( $option_val ); ?>"<?php echo $option_selected; ?>><?php echo $option_label; ?></option>
                    <?php
                        endforeach;
                    ?>
                    </select>
                    <?php echo $field_errors; ?>
                </label>
            </div>
        <?php
    }

}
