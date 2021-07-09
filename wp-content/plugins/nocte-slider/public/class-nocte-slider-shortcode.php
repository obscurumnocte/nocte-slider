<?php
/**
 *  Create the shortcode for the carousel
 */

defined('ABSPATH') || exit;

class Nocte_Slider_Shortcode {

    /**
     *  Class constructor to set up instance
     */
    public function __construct(){
        //  Create shortcode to display the carousel
        add_shortcode('nocte_slider_carousel', array( $this, 'nocte_slider_carousel_func') );

        //  Set up filters
        add_filter('filter_owl_options_meta', array( $this, 'adjust_owl_options'), 11 );
        add_filter('owl_options_config', array( $this, 'create_js_config_string_for_owl_carousel') );
    }

    /**
     *  Set up shortcode to display the carousel
     */
    function nocte_slider_carousel_func( $atts, $content = "" ){
        global $post;

        extract( shortcode_atts(array(
            'nsc_id'        => 0,
            'id'           => '',
            'classes'      => ''
        ), $atts ));

        if( $id ) $id = ' id="'. esc_attr( $id ) .'"';
        if( $classes ) $classes = ' '. trim( esc_attr( $classes ) );
        //  Check for carousel id
        if( !empty( $nsc_id ) ){
            $carousel_id = intval( $nsc_id );

        } else {
            //  If no carousel id specified, display error message
            return '<div class="nsc-error">'. __('Error: No carousel ID specified for Nocte Slider', 'ns') .'</div>';
        }

        //  Get carousel data from field
        $slides = apply_filters('get_slides_meta', $carousel_id );
        $owl_options = apply_filters('get_owl_options_meta', $carousel_id );
        //  Allow modification of option data
        $owl_options = apply_filters('filter_owl_options_meta', $owl_options );
        //print'<pre>slides = '.print_r($slides,true).'</pre>';
        //print'<pre>owl options = '.print_r($owl_options,true).'</pre>';
        //print'<pre>owl options = ';var_dump($owl_options);print'</pre>';

        //  Check for slides
        if( empty( $slides['slides'] ) ){
            //  Return an error message
            return '<div class="nsc-error">'. __('Error: No slides in Nocte Slider carousel.', 'ns') .'</div>';
        }

        //  Start output buffering as result needs to be returned
        ob_start();
    ?>
    <?php //  Add markup for carousel ?>
    <div<?php echo $id; ?> class="ns-carousel-wrapper-<?php echo $carousel_id . $classes; ?>">
        <div class="owl-carousel owl-theme">
        <?php
            $markup = '';
            foreach( $slides['slides'] as $a_slide ):
                //  Check slide has image
                if( !empty( $a_slide['image'] ) ):
                    //  Get image data to build owl carousel slide
                    $image = wp_get_attachment_image_src( $a_slide['image'], 'full', false );
                    if( !empty( $image ) ){
                        list( $src, $width, $height ) = $image;
                        $image_attr = array(
                            'src'       => $src,
                            'width'     => intval( $width ),
                            'height'    => intval( $height ),
                            'alt'       => trim( strip_tags( get_post_meta( $a_slide['image'], '_wp_attachment_image_alt', true ) ) ),
                        );
                        $image_attributes = '';
                        foreach( $image_attr as $name => $value ){
                            $image_attributes .= $name .'="'. esc_attr( $value ) .'" ';
                        }
                        $markup .= '<div class="slide"><img '. $image_attributes .'></div>';
                    }
                endif;
            endforeach;
            $markup = apply_filters('ns_carousel_markup', $markup, $slides, $carousel_id );
            //  Display slides
            echo $markup;
        ?>
        </div>
    </div>
    <script type="text/javascript">
    ;(function($){
        $(document).ready(function(){
            $('.ns-carousel-wrapper-<?php echo $carousel_id; ?> .owl-carousel').owlCarousel(
                <?php echo apply_filters('owl_options_config', $owl_options ); ?>
            );
        });
    })(jQuery);
    </script>
    <?php
        return ob_get_clean();
    }


    /**
     * Filter the owl options data to adjust the responsive data structure
     */
    public function adjust_owl_options( $owl_options ){
        if( empty( $owl_options ) ){
            return $owl_options;
        }

        //  Check through options and remove NULL values
        //  Also, customise the structure of the 'navText' option and the 'responsive' option
        $navText = array();
        $responsive = array();
        foreach( $owl_options as $option_key => $option ){
            //  Check for subfields and repeaters
            if( is_array( $option ) ){
                $res_break = 0;
                foreach( $option as $sub_option_key => $sub_option ){
                    //  Check for repeaters
                    if( is_array( $sub_option ) ){
                        foreach( $sub_option as $repeater_option_key => $repeater_option ){
                            //  Check and remove NULL value options
                            if( $repeater_option === NULL ){
                                unset( $owl_options[ $option_key ][ $sub_option_key ][ $repeater_option_key ] );
                            }
                            //  Check for responsive options
                            if( 'responsive' == $option_key ){
                                if( 'breakpoint' == $repeater_option_key ){
                                    $res_break = intval( $repeater_option );
                                    $responsive[ $res_break ] = array();

                                } else {
                                    $responsive[ $res_break ][ $repeater_option_key ] = $repeater_option;
                                }
                            }
                        }
                    } else {
                        if( 'navText' == $option_key ){
                            $navText[] = $sub_option;

                        } elseif( $sub_option === NULL ){
                            unset( $owl_options[ $option_key ][ $sub_option_key ] );
                        }
                    }
                }
                //  Update modified values
                if( 'navText' == $option_key ){
                    $owl_options['navText'] = $navText;

                } elseif( 'responsive' == $option_key ){
                    $owl_options['responsive'] = $responsive;
                    //  If responsive options are set, global options need to be removed
                    //  items (broken)
                    if( !empty( $responsive ) ){
                        unset(
                            $owl_options['items'],
                            $owl_options['margin'],
                            $owl_options['center'],
                            $owl_options['stagePadding'],
                            $owl_options['startPosition'],
                            $owl_options['nav'],
                            $owl_options['dots'],
                            $owl_options['rewind'],
                            $owl_options['slideBy'],
                            $owl_options['slideTransition']
                        );
                    }
                }

            } else {
                if( $option === NULL ){
                    unset( $owl_options[ $option_key ] );
                }
            }
        }

        //  Check if dotsSpeed and navSpeed are set to 0 and remove them if so
        if( empty( $owl_options['dotsSpeed'] ) ){
            unset( $owl_options['dotsSpeed'] );
        }
        if( empty( $owl_options['navSpeed'] ) ){
            unset( $owl_options['navSpeed'] );
        }
        //  Check for dotsEach - if 0 unset to use default of false
        if( empty( $owl_options['dotsEach'] ) ){
            unset( $owl_options['dotsEach'] );
        }

        return $owl_options;
    }

    /**
     *  Create the JS config output for OwlCarousel
     */
    public function create_js_config_string_for_owl_carousel( $owl_options ){
        //  Set up JS config
        $owl_config = json_encode( $owl_options );

        //  Check for responsiveBaseElement value - if window or document, adjust output
        //  Check responsiveBaseElement exists
        if( !empty( $owl_options['responsiveBaseElement'] ) ){
            //  Check window
            if( 'window' == $owl_options['responsiveBaseElement'] ){
                $owl_config = preg_replace('/"responsiveBaseElement":"window"/', '"responsiveBaseElement":window', $owl_config );
            }
            //  Check document
            if( 'document' == $owl_options['responsiveBaseElement'] ){
                $owl_config = preg_replace('/"responsiveBaseElement":"document"/', '"responsiveBaseElement":document', $owl_config );
            }
        }

        return $owl_config;
    }
}
//  Instantiate class
new Nocte_Slider_Shortcode();
