<?php

/*
* @@ Badge
* @@ Last updated version 5.0.9
*/

class AWDP_badge
{

    public function awdp_sales_badge ( $discount_rules, $product_lists, $product, $thumbnail ) {

        $badge_settings = get_option('awdp_badge_settings') ? get_option('awdp_badge_settings') : [];
        $badge_rule     = array_key_exists('badge_rule', $badge_settings) ? $badge_settings['badge_rule'] : '';

        if ( array_key_exists('badge_enable', $badge_settings) && $badge_settings['badge_enable'] != '' && !is_cart() && !is_checkout() && array_search ( $badge_rule, array_column ( $discount_rules, 'id' ) ) !== false ) { 
            
            global $post;

            if ( is_a ( $product, 'WC_Product' ) ) {
				$product_id = $product->get_id();
			} elseif ( false === $product && isset( $post->ID ) ) {
				$product_id = $post->ID;
			} else {
				$product_id = $product;
			}
                
            // $post_slug = $post->post_name;
            $post_slug              = get_post_field ( 'post_name', $product_id );
            $custom_prd_list        = get_post_meta ( $badge_rule, 'discount_custom_pl', true ); 

            if ( $custom_prd_list ) {
                
                $prod_ids       = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'set_custom_list' ), 
                    array ( $badge_rule ) 
                );

            } else {

                $list_id        = get_post_meta ( $badge_rule, 'discount_product_list', true ); 
                // $product_lists  = $this->product_lists;
                $prod_ids       = $list_id != 0 ? $product_lists[$list_id] : '';  

            }
            $style          = ''; 

            if ( $prod_ids == '' || in_array ( $product_id, $prod_ids ) ) { 

                $badge_status       = $badge_settings['badge_enable'];
                $badge_label_color  = $badge_settings['badge_label_color'];
                $badge_color        = $badge_settings['badge_color'];
                $badge_position     = $badge_settings['badge_position'];
                $badge_style        = $badge_settings['badge_style'];
                $badge_label        = $badge_settings['badge_label'];
                $css                = $badge_settings['badge_label_color'] ? "color:".$badge_label_color.";" : '';
                $css               .= $badge_settings['badge_color'] ? "background:".$badge_color.";" : '';

                $blockonecss        = ( $badge_position == 'left' ) ? "border-right: none; border-left: 65px solid ".$badge_color."; border-bottom: 65px solid transparent;" : "border-left: none; border-right: 65px solid ".$badge_color."; border-bottom: 65px solid transparent;";

                if( $badge_status === true ) {

                    if ( $badge_style == 'badgeone' ) {
                        if ( $badge_position == 'left' ) {
                            // $style = '<span class="wdp-ribbon-one left"><span style="'.$css.'">'.$badge_label.'</span></span>';
                            $style = '<span class="wdp-ribbon-one left"><span class="wdp-blockOne"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
                        } else {
                            $style = '<span class="wdp-ribbon-one"><span class="wdp-blockOne"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
                        }
                    }
                    if ( $badge_style == 'badgetwo' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-two left" style="'.$css.'">'.$badge_label.'</span>';
                        } else {
                            $style = '<span class="wdp-ribbon-two" style="'.$css.'">'.$badge_label.'</span>';
                        }
                    }
                    if ( $badge_style == 'badgethree' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-three left" style="'.$css.'">'.$badge_label.'</span>';
                        } else {
                            $style = '<span class="wdp-ribbon-three" style="'.$css.'">'.$badge_label.'</span>';
                        }
                    }
                    if ( $badge_style == 'badgefour' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-four left" style="'.$css.'">'.$badge_label.'</span>';
                        } else {
                            $style = '<span class="wdp-ribbon-four" style="'.$css.'">'.$badge_label.'</span>';
                        }
                    }
                    if ( $badge_style == 'badgefive' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-five left" style="'.$css.'"><span>'.$badge_label.'</span></span>';
                        } else {
                            $style = '<span class="wdp-ribbon-five" style="'.$css.'"><span>'.$badge_label.'</span></span>';
                        }
                    }
                    if ( $badge_style == 'badgesix' ) {
                        if ( $badge_position == 'left' ) {
                            // $style = '<span class="wdp-ribbon-six left" style="'.$css.'"><span>'.$badge_label.'</span></span>';
                            $style = '<span class="wdp-ribbon-six left"><span class="wdp-blockOne" style="'.$blockonecss.'"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
                        } else {
                            $style = '<span class="wdp-ribbon-six"><span class="wdp-blockOne" style="'.$blockonecss.'"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
                        }
                    }
                    if ( $badge_style == 'badgeseven' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-seven left" style="'.$css.'">'.$badge_label.'</span>';
                        } else {
                            $style = '<span class="wdp-ribbon-seven" style="'.$css.'">'.$badge_label.'</span>';
                        }
                    }
                    if ( $badge_style == 'badgeeight' ) {
                        if ( $badge_position == 'left' ) {
                            $style = '<span class="wdp-ribbon-eight left" style="'.$css.'">'.$badge_label.'</span>';
                        } else {
                            $style = '<span class="wdp-ribbon-eight" style="'.$css.'">'.$badge_label.'</span>';
                        }
                    }
                }

            // } else if ( in_array( $post_slug, $gifts ) && ( array_key_exists ( 'badge_enable', $badge_settings ) && $badge_settings['badge_enable'] != '' ) ) {

            //     $badge_status       = $badge_settings['badge_free']; // Gift Enable
            //     $badge_label_color  = $badge_settings['badge_label_color'];
            //     $badge_color        = $badge_settings['badge_color'];
            //     $badge_position     = $badge_settings['badge_position'];
            //     $badge_style        = $badge_settings['badge_style'];
            //     // $badge_label        = $badge_settings['badge_label'] ? $badge_settings['badge_label'] : 'Sale';
            //     $badge_label        = 'Free*';
            //     $css                = $badge_settings['badge_label_color'] ? "color:".$badge_label_color.";" : '';
            //     $css               .= $badge_settings['badge_color'] ? "background:".$badge_color.";" : '';

            //     $blockonecss        = ( $badge_position == 'left' ) ? "border-right: none; border-left: 65px solid ".$badge_color."; border-bottom: 65px solid transparent;" : "border-left: none; border-right: 65px solid ".$badge_color."; border-bottom: 65px solid transparent;";

            //     if( $badge_status === true ) {
            //         if ( $badge_style == 'badgeone' ) {
            //             if ( $badge_position == 'left' ) {
            //                 // $style = '<span class="wdp-ribbon-one left"><span style="'.$css.'">'.$badge_label.'</span></span>';
            //                 $style = '<span class="wdp-ribbon-one left"><span class="wdp-blockOne"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-one"><span class="wdp-blockOne"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgetwo' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-two left" style="'.$css.'">'.$badge_label.'</span>'; 
            //             } else {
            //                 $style = '<span class="wdp-ribbon-two" style="'.$css.'">'.$badge_label.'</span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgethree' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-three left" style="'.$css.'">'.$badge_label.'</span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-three" style="'.$css.'">'.$badge_label.'</span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgefour' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-four left" style="'.$css.'">'.$badge_label.'</span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-four" style="'.$css.'">'.$badge_label.'</span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgefive' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-five left" style="'.$css.'"><span>'.$badge_label.'</span></span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-five" style="'.$css.'"><span>'.$badge_label.'</span></span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgesix' ) {
            //             if ( $badge_position == 'left' ) {
            //                 // $style = '<span class="wdp-ribbon-six left" style="'.$css.'"><span>'.$badge_label.'</span></span>';
            //                 $style = '<span class="wdp-ribbon-six left"><span class="wdp-blockOne" style="'.$blockonecss.'"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-six"><span class="wdp-blockOne" style="'.$blockonecss.'"></span><span class="wdp-blockTwo"></span><span class="wdp-blockText" style="'.$css.'">'.$badge_label.'</span></span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgeseven' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-seven left" style="'.$css.'">'.$badge_label.'</span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-seven" style="'.$css.'">'.$badge_label.'</span>';
            //             }
            //         }
            //         if ( $badge_style == 'badgeeight' ) {
            //             if ( $badge_position == 'left' ) {
            //                 $style = '<span class="wdp-ribbon-eight left" style="'.$css.'">'.$badge_label.'</span>';
            //             } else {
            //                 $style = '<span class="wdp-ribbon-eight" style="'.$css.'">'.$badge_label.'</span>';
            //             }
            //         }
            //     }
            }

            echo $thumbnail.$style;

        } else {

            echo $thumbnail;

        }

    }

}