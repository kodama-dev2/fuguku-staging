<?php

/*
* @@ Timer
* @@ Last updated version 5.0.9
*/

class AWDP_timer
{

    public function awdp_countdown_timer ( $discount_rules, $product_lists ) {


        $timer_settings = get_option('awdp_timer_settings') ? get_option('awdp_timer_settings') : [];
        $timer_rule     = array_key_exists('timer_rule', $timer_settings) ? $timer_settings['timer_rule'] : [];

        if ( array_key_exists('timer_enable', $timer_settings) && $timer_settings['timer_enable'] === true && array_search ( $timer_rule, array_column ( $discount_rules, 'id' ) ) !== false ) { 

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
            if( $timezone_string ) { 
                $datenow = new DateTime(current_time('mysql'), new DateTimeZone($timezone_string));
            } else { 
                $min    = 60 * get_option('gmt_offset'); 
                $sign   = $min < 0 ? "-" : "+";
                $absmin = abs($min); 
                $tz     = sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60); 
                $datenow = new DateTime(current_time('mysql'), new DateTimeZone($tz)); 
            }
            // $datenow->setTimezone(new DateTimeZone('+000')); // Converting to UTC+000 (moment isoString timezone)
            $datenow = $datenow->format('Y-m-d H:i:s');
            $day = date("l");
            // End Time

            // $timer_rule         = $timer_settings['timer_rule'];
            // $discount_rules     = $this->discount_rules;
            $current_prod       = get_the_ID();
            
            $custom_prd_list    = get_post_meta ( $timer_rule, 'discount_custom_pl', true ); 
            
            if ( $custom_prd_list ) {
                
                $prod_ids       = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'set_custom_list' ), 
                    array ( $timer_rule ) 
                );
                
            } else {
                
                // $product_lists  = $this->product_lists;
                $rule_index     = array_search ( $timer_rule, array_column ( $discount_rules, 'id' ) );
                $list_id        = $discount_rules[$rule_index]['product_list'] != 0 ? $discount_rules[$rule_index]['product_list'] : '';
                $prod_ids       = ( $list_id != '' ) ? $product_lists[$list_id] : '';

            }

            $timer_style    = $timer_settings['timer_style'];
            $timer_position = $timer_settings['timer_position'];
            $days_label     = $timer_settings['timer_label_days'] ? $timer_settings['timer_label_days'] : 'Days';
            $hours_label    = $timer_settings['timer_label_hours'] ? $timer_settings['timer_label_hours'] : 'Hrs';
            $mins_label     = $timer_settings['timer_label_minutes'] ? $timer_settings['timer_label_minutes'] : 'Mins';
            $secs_label     = $timer_settings['timer_label_seconds'] ? $timer_settings['timer_label_seconds'] : 'Secs';
            $timer_color    = $timer_settings['timer_color'];
            $label_color    = $timer_settings['timer_label_color'];
            $timer_text     = $timer_settings['timer_text'];
            $timer          = '';

            if ( array_key_exists ( 'timer_start_enable', $timer_settings) && ( $timer_settings['timer_start_enable'] == 0 || $timer_settings['timer_start_enable'] == '' ) ) {
                // Use Schedule
                $schedules      = unserialize(get_post_meta($timer_rule, 'discount_schedules', true)); 
                $weekday_status = get_post_meta($timer_rule, 'discount_schedule_weekday', true);
                $weekdays       = unserialize(get_post_meta($timer_rule, 'discount_schedule_days', true));

                if ( $schedules ) { 
                    foreach ( $schedules as $schedule ) { 
                        $mn_start_time      = date('H:i' , strtotime($schedule['start_date']));
                        $mn_end_time        = date('H:i' , strtotime($schedule['end_date']));
                        $current_time       = strtotime(gmdate('H:i'));  
                        $awdp_start_date    = $schedule['start_date'];
                        $awdp_end_start     = $schedule['end_date'] ? $schedule['end_date'] : '';
                        if ( ( $awdp_start_date <= $datenow ) && ( $awdp_end_start >= $datenow ) ) { 
                            if ( $weekday_status == 1 ) {
                                $wk_start_time  = strtotime(get_post_meta($timer_rule, 'discount_start_time', true));
                                $wk_end_time    = strtotime(get_post_meta($timer_rule, 'discount_end_time', true)); 
                                if ( ( in_array($day, $weekdays) && ( $current_time >= $wk_start_time && $current_time <= $wk_end_time ) ) || ( empty($weekdays) || ( !empty($weekdays) && ( $weekdays[0] == 'null' || $weekdays[0] == '' ) ) ) ){
                                    $time_start = date('Y-m-d'). ' ' .get_post_meta($timer_rule, 'discount_start_time', true);
                                    $time_end   = date('Y-m-d'). ' ' .get_post_meta($timer_rule, 'discount_end_time', true);
                                    $start_date = date_format(date_create($time_start),"Y-m-d H:i:s");
                                    $end_date   = date_format(date_create($time_end),"Y-m-d H:i:s");
                                }
                            } else {
                                $time_start = $schedule['start_date'];
                                $time_end   = $schedule['end_date'];
                                $start_date = date_format(date_create($time_start),"Y-m-d H:i:s");
                                $end_date   = date_format(date_create($time_end),"Y-m-d H:i:s");
                            }
                        } else {
                            return '';
                        }
                    }
                } else {
                    return '';
                }

            } else {

                $time_start = $timer_settings['timer_start_time'];
                $time_end   = $timer_settings['timer_end_time'];
                $start_date = date_format(date_create($time_start),"Y-m-d H:i:s");
                $end_date   = date_format(date_create($time_end),"Y-m-d H:i:s");

            }

            if ( $time_end != '' && ( ( strtotime($datenow) >= strtotime($start_date) ) && $discount_rules != false && ( strtotime($datenow) <= strtotime($end_date) ) ) && ( $prod_ids == '' || ( $prod_ids != '' && in_array ( $current_prod, $prod_ids ) ) ) ) {
                $time = '<div id="timer" class="'. $timer_style.'" data-time="'.$time_end.'">
                    <div id="days" class="timer_blocks" data-label="'.$days_label.'"></div>
                    <div id="hours" class="timer_blocks" data-label="'.$hours_label.'"></div>
                    <div id="minutes" class="timer_blocks" data-label="'.$mins_label.'"></div>
                    <div id="seconds" class="timer_blocks" data-label="'.$secs_label.'"></div>
                </div>';
                $timer = '<div class="wdp_countdown outter-'.$timer_style.'">'.str_replace ( '{timer}', $time, $timer_text ).'</div>';
            }

            return $timer;

        } else {

            return '';

        }

    }

}