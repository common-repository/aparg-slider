<?php

defined('ABSPATH') or die('No script kiddies please!');

function apsl_get_all_sliders() {
    global $wpdb;
    $sSQL = "SELECT * FROM " . $wpdb->prefix . "aparg_flexslider_sliders ORDER BY  slider_id";
    $get_sliders = $wpdb->get_results($sSQL);
    return $get_sliders;
}

function apsl_get_slider_data($slide_id) {
    global $wpdb;
    $sSQL = "SELECT * FROM " . $wpdb->prefix . "aparg_flexslider WHERE slide_id=" . $slide_id . " ORDER BY id";
    $get_slider_data = $wpdb->get_results($sSQL);
    return $get_slider_data;
}

function apsl_addnewslider($slider_id) {
    global $wpdb;
    $tblname = $wpdb->prefix . "aparg_flexslider_sliders";
    $field = "`slider_id`, `slider_name` ";
    $values = $slider_id . ", 'Slider " . $slider_id . "'";
    $sSQL = "INSERT INTO " . $tblname . " ($field) VALUES ($values) ON DUPLICATE KEY UPDATE `slider_name`='Slider " . $slider_id . "'";
    if ($wpdb->query($sSQL))
        return true;
    else
        return false;
}

function apsl_add_slides($tblname, $meminfo, $slide_id) {
    global $wpdb;
    global $check_db;
    $count = sizeof($meminfo);
    if ($count > 0) {
        $id = 0;
        $field = "slide_id";
        $values = "" . $slide_id . "";
        $wpdb->query("DELETE FROM `" . $tblname . "` WHERE slide_id=" . $slide_id);
        foreach ($meminfo as $k => $val):
            foreach ($val as $key => $v):
                if ($field == "") {
                    $field = "`" . $key . "`";
                    $values = "'" . $v . "'";
                } else {
                    $field = $field . ",`" . $key . "`";
                    $values = $values . ",'" . $v . "'";
                }
            endforeach;
            $sSQL = "INSERT INTO " . $tblname . " ($field) VALUES ($values)";
            $field = "slide_id";
            $values = "" . $slide_id . "";
            $wpdb->query($sSQL);
        endforeach;
        return true;
    }
}

function apsl_get_slider_settings($slider_id) {
    global $wpdb;
    $sSQL = "SELECT slider_option_name, slider_option FROM " . $wpdb->prefix . "aparg_flexslider_options WHERE slider_id=" . $slider_id;
    $get_slider_setings = $wpdb->get_results($sSQL);
    $settings = array();
    foreach ($get_slider_setings as $key => $opt):
        $settings['slider_options'][$opt->slider_option_name] = $opt->slider_option;
    endforeach;
    return $settings;
}

function apsl_add_slider_options($tblname, $meminfo, $slider_id) {
    global $wpdb;
    $field = "slider_id";
    $values = "" . $slider_id . "";
    $wpdb->query("DELETE FROM `" . $tblname . "` WHERE slider_id=" . $slider_id);
    $resp = true;
    foreach ($meminfo as $key => $val):
        foreach ($val as $k => $v):
            if ($field == "") {
                $field = "`" . $k . "`";
                $values = "'" . $v . "'";
            } else {
                $field = $field . ",`" . $k . "`";
                $values = $values . ",'" . $v . "'";
            }
        endforeach;
        $sSQL = "INSERT INTO " . $tblname . " ($field) VALUES ($values)";
        $field = "slider_id";
        $values = "" . $slider_id . "";
        $resp = $wpdb->query($sSQL);
        if (!$resp)
            return false;
    endforeach;
    return $resp;
}

function apsl_delete_slider($slider_id) {
    global $wpdb;
    $slide_table_name = $wpdb->prefix . "aparg_flexslider";
    $sliders_table_name = $wpdb->prefix . "aparg_flexslider_sliders";
    $options_table_name = $wpdb->prefix . "aparg_flexslider_options";
    $response = true;
    if (!($wpdb->query("DELETE FROM `" . $slide_table_name . "` WHERE slide_id=" . $slider_id)))
        $response = false;

    if (!($wpdb->query("DELETE FROM `" . $options_table_name . "` WHERE slider_id=" . $slider_id)))
        $response = false;

    if (!($wpdb->query("DELETE FROM `" . $sliders_table_name . "` WHERE slider_id=" . $slider_id)))
        $response = false;

    return $response;
}
