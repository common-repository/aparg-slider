<?php

defined('ABSPATH') or die('No script kiddies please!');
//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

$apsl_all_tables = array(
    "aparg_flexslider",
    "aparg_flexslider_options",
    "aparg_flexslider_sliders"
);
$apsl_all_options = array(
    'youtubedb',
    'apsl_db_version'
);

function apsl_delete_table($apsl_my_table) {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$apsl_my_table");
}

if (function_exists('is_multisite') && is_multisite()) {
    global $wpdb;
    $old_blog = $wpdb->blogid;
    //Get all blog ids
    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach ($blogids as $blog_id) {
        switch_to_blog($blog_id);
        foreach ($apsl_all_tables as $apsl_table) {
            apsl_delete_table($apsl_table);
        }
    }
    foreach ($apsl_all_options as $apsl_option) {
        delete_option($apsl_option);
    }

    switch_to_blog($old_blog);
} else {
    foreach ($apsl_all_tables as $apsl_table) {
        apsl_delete_table($apsl_table);
    }
    foreach ($apsl_all_options as $apsl_option) {
        delete_option($apsl_option);
    }
}


