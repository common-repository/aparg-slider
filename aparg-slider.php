<?php

/*
  Plugin Name: Aparg Slider
  Description: Aparg Slider is image and video slider with descriptions for each slide and smooth animations.
  Version:     2.7
  Author:      Aparg
  Author URI:  http://aparg.com/
  License:     GPL2
  Text Domain: aparg-slider
  Domain Path: /languages/  
​
  This plugin is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as active by
  the Free Software Foundation, either version 2 of the License, or
  any later version.
​
  This plugin is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
​
  You should have received a copy of the GNU General Public License
  along with this plugin. If not, see https://wordpress.org/about/gpl/.
 */
defined('ABSPATH') or die('No script kiddies please!');
require_once "aparg-dbase.php";
require_once("aparg-slider-form.php");

/*
 * Creating tables for slider images and options in DB 
 */
global $apsl_db_version;
$apsl_db_version = 1.1;

function apsl_addmyplugin() {
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    global $wpdb;
    global $apsl_db_version;
    $table_name = $wpdb->prefix . "aparg_flexslider";
    $options_table_name = $wpdb->prefix . "aparg_flexslider_options";
    $sliders_table_name = $wpdb->prefix . "aparg_flexslider_sliders";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name ( 
			  id int(3) NOT NULL AUTO_INCREMENT,
			  slide_id int(3) NOT NULL,
			  slide_url varchar(255) NOT NULL,
			  slide_title varchar(255) NOT NULL,
			  description text NOT NULL,
                          youtubelink varchar(255) NOT NULL,
			  PRIMARY KEY  (id)
		) $charset_collate;";

    /* Including dbDelta function for working with DB */
    dbDelta($sql);

    $sql = "CREATE TABLE $options_table_name (
			   id int(9) NOT NULL AUTO_INCREMENT,
				slider_id int(3) NOT NULL,
				slider_option_name varchar(30) NOT NULL,
				slider_option varchar(30) NOT NULL,
				PRIMARY KEY  (id)
		) $charset_collate;";
    /* Including dbDelta function for working with DB */
    dbDelta($sql);

    $sql = "CREATE TABLE $sliders_table_name (	
				slider_id int(3) NOT NULL,
				slider_name varchar(255) NOT NULL,
				PRIMARY KEY  (slider_id)
		) $charset_collate;";
    
    dbDelta($sql);
    
    global $apsl_db_version;
    update_option('apsl_db_version', $apsl_db_version);
}


register_activation_hook( __FILE__, 'apsl_activate' );

function apsl_activate( $networkwide ) {
      global $wpdb;
      if (function_exists( 'is_multisite' ) && is_multisite() ) {
         //check if it is network activation if so run the activation function for each id
         if( $networkwide ) {
            $old_blog =  $wpdb->blogid;
            //Get all blog ids
            $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
               switch_to_blog($blog_id);
               //Create database table if not exists
               apsl_addmyplugin();
            }
            switch_to_blog( $old_blog );
            return;
         }
      }
      //Create database table if not exists
     apsl_addmyplugin();
}


add_action( 'wpmu_new_blog', 'apsl_create_subscribe_table_mu');
/**
 * Create subscribe table for new multisite created
 *
 */
function apsl_create_subscribe_table_mu( $blog_id ) {
    if ( is_plugin_active_for_network( 'aparg-slider/aparg-slider.php' ) ) {
        switch_to_blog( $blog_id );
        apsl_addmyplugin();
        restore_current_blog();
    }
}

add_filter( 'wpmu_drop_tables', 'delete_subscribe_table_mu' );
/**
 * Delete subscribe table when multisite blog is deleted
*/

  function delete_subscribe_table_mu($tables) {
      
      $apsl_all_table_name = array(
      "aparg_flexslider",
      "aparg_flexslider_options",
      "aparg_flexslider_sliders"
      );
     global $wpdb;
     foreach($apsl_all_table_name as $table_name){
     $tables[] = $wpdb->prefix . $table_name;
     }
     return $tables;
 }

/*
 * Checking old versons of slider, adding youtube link into db.
 */

function apsl_youtube_db() {
    global $apsl_db_version;
    if (get_option('apsl_db_version') != $apsl_db_version) {
        apsl_addmyplugin();
    }
}

add_action('plugins_loaded', 'apsl_youtube_db');

/*
 * Making plugin translation ready
 */
add_action('plugins_loaded', 'apsl_slider_text_domain');

function apsl_slider_text_domain() {
    load_plugin_textdomain('aparg-slider', dirname(__FILE__) . '/languages/', basename(dirname(__FILE__)) . '/languages/');
}

/*
 *  Slider Help Part
 */

function apsl_contextual_help($contextual_help) {
    global $current_screen;
    $cont_help = "<p>" . __("Hi, this is a Aparg Slider help", "aparg-slider") . ".</p>" .
            "<p>" . __("To use our slider plugin at first you should add a slider by clicking on  the '+' tab", "aparg-slider") . ".</p>" .
            "<p>" . __("Then you'll see default slider settings on right side and blank area on the left side where you can add slides(click 'Add Images') with their descriptions('Add Description')", "aparg-slider") . ".</p>" .
            "<p>" . __("After that save current slide information", "aparg-slider") . "</p>" .
            "<b>" . __("Our slider plugin advantages", "aparg-slider") . "</b>" .
            "<ol>" .
            "<li>" . __("Add 4 descriptions to each slide", "aparg-slider") . "</li>" .
            "<li>" . __("Change slide images by clicking on them", "aparg-slider") . "</li>" .
            "<li>" . __("Delete descriptions, slides, entire sliders", "aparg-slider") . "</li>" .
            "</ol>" .
            "<p><b>" . __("Note", "aparg-slider") . ":  </b>" . __("If all descriptions are empty their options(background and text color) are inactive", "aparg-slider") . ".</p>" .
            "<p>" . __("It not suggest to put slide show speed less than animation speed, it may cause slider undesirable  behavior", "aparg-slider") . ".</p>";
    switch ($current_screen->id) {
        case 'toplevel_page_apargslider' :

            get_current_screen()->add_help_tab(array(
                'id' => 'apargslider-help-tab',
                'title' => __('Aparg Slider Help', 'aparg-slider'),
                'content' => $cont_help
            ));

            break;
    }
    return $contextual_help;
}

add_filter('contextual_help', 'apsl_contextual_help');

/*
 * Including scripts and styles to admin page
 */

add_action('admin_init', 'apsl_my_plugin_scripts');

function apsl_my_plugin_scripts() {
    if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'apargslider') {
        global $wp_scripts;
        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');

        $ui = $wp_scripts->query('jquery-ui-core');
        wp_enqueue_style('jquery-dialog-style', plugins_url("css/jquery-ui/jquery-ui-1.10.4.css", __FILE__));

        wp_enqueue_media();
        wp_register_style('cpicker_styles', plugins_url('colorpicker/css/colorpicker.css', __FILE__));
        wp_enqueue_style('cpicker_styles');
        wp_register_style('custom_styles', plugins_url('css/plugin-styles.css', __FILE__), false, false, 'all');
        wp_enqueue_style('custom_styles');
        wp_register_script('cpicker_scripts', plugins_url('colorpicker/js/colorpicker.js', __FILE__));
        wp_enqueue_script('cpicker_scripts');
        wp_register_script('youtube_scripts', plugins_url('js/jsvideourlparser.js', __FILE__));
        wp_enqueue_script('youtube_scripts');
        wp_register_script('custom_scripts', plugins_url('js/aparg-slider.js', __FILE__), array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('custom_scripts');

        //Add script to use alternatively media query in IE8
        wp_register_script('respond_scripts', plugins_url('js/respond.min.js', __FILE__));
        wp_enqueue_script('respond_scripts');
        $nonce = wp_create_nonce('aparg-slider');
        $apsl_localize_array = array(
            'url' => admin_url('admin-ajax.php'),
            'leavingPage' => __("Leaving this page will cause any unsaved data to be lost.", "aparg-slider"),
            'urlErrMessage' => __('Please insert a valid video link.', "aparg-slider"),
            'loadImg' => plugins_url('/images/ajax_loader.gif', __FILE__),
            'clikChangeImg' => __('Click to change image', 'aparg-slider'),
            'addDescription' => __('Add Description', 'aparg-slider'),
            'delDescription' => __('Delete Description', 'aparg-slider'),
            'typeDescription' => __('Type Description', 'aparg-slider'),
            'set' => __('Set', 'aparg-slider'),
            'chooseResolution' => __('Choose high resolution image', 'aparg-slider'),
            'cantFindThumb' => __("Sorry can't find thumbnail for this video", 'aparg-slider'),
            'nonce' => $nonce
        );

        wp_localize_script('custom_scripts', 'slider', $apsl_localize_array);
    }
}

/*
 * Including scripts and styles to front end
 */
add_action('wp_enqueue_scripts', 'apsl_load_custom_files');

function apsl_load_custom_files() {
    if (!is_admin()) {
        wp_enqueue_script('jquery');
        // **** //
        wp_register_style('flexslider_style', plugins_url('css/flexslider.css', __FILE__));
        wp_enqueue_style('flexslider_style');
        wp_register_style('flexslider_custom_style', plugins_url('css/flexsliderstyles.css', __FILE__));
        wp_enqueue_style('flexslider_custom_style');
        wp_register_script('flexslider_scripts', plugins_url('js/jquery.flexslider.js', __FILE__), array('jquery'));
        wp_enqueue_script('flexslider_scripts');
        wp_register_script('bigSlider_scripts', plugins_url('js/aparg-big-slider.js', __FILE__));
        wp_enqueue_script('bigSlider_scripts');
        wp_register_script('youtube_scripts', plugins_url('js/jsvideourlparser.js', __FILE__));
        wp_enqueue_script('youtube_scripts');
       
    }
}

/*
 * Function for generating random id for each slider
 */

function apsl_RandomString() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 5; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/*
 * Generating Shortcode for each slider 
 */
add_shortcode('aparg_slider', 'apsl_slider_func');

function apsl_slider_func($atts) {
    $slide_id = apsl_RandomString();
    add_action('wp_head', 'load_custom_files');
    $sliders = apsl_get_all_sliders();
    $get_slides_content = apsl_get_slider_data($atts['id']);
    $slide_option = apsl_get_slider_settings($atts['id']);
    $output = '';
    $img = '';
    $dsc = '';
    $link = '';
    $str = '';
    $img_titles = array();
    if (!empty($slide_option) && !empty($get_slides_content)) {
        foreach ($get_slides_content as $key => $value) {
            if ($key != count($get_slides_content) - 1) {
                $img.= $value->slide_url . '*';
                $dsc.= $value->description . '*';
                $link.=$value->youtubelink . '*';
            } else {
                $img.= $value->slide_url;
                $dsc.= $value->description;
                $link.=$value->youtubelink;
            }
            $img_titles[$key] = $value->slide_title;
        }
        foreach ($slide_option['slider_options'] as $key => $value):
            $str.= '' . $key . '=' . $value . ' ';
        endforeach;

        extract(shortcode_atts(array(
            'wrapper_class' => 'apargSlider',
            'images' => '' . $img . '',
            'desc' => '' . $dsc,
            'link' => '' . $link,
            'options' => '' . rtrim($str) . '',
                        ), $atts));
        $images = explode('*', $images);
        $descs = explode('*', $desc);
        $links = explode('*', $link);
        $opt = explode(' ', $options);
        $slider_settings = array();
        foreach ($opt as $val) {
            $s = explode('=', $val);
            $slider_settings[$s[0]] = $s[1];
        }
        $slider_settings['carousel_item_width'] = isset($slider_settings['carousel_item_width']) ? $slider_settings['carousel_item_width'] : '';
        $slider_settings['desc_bg_color'] = isset($slider_settings['desc_bg_color']) ? $slider_settings['desc_bg_color'] : '';
        $slider_settings['desc_text_color'] = isset($slider_settings['desc_text_color']) ? $slider_settings['desc_text_color'] : '';
        $slider_settings['desc_duration'] = isset($slider_settings['desc_duration']) ? $slider_settings['desc_duration'] : '';
        $slider_width = (isset($slider_settings['slider_width']) && $slider_settings['slider_width'] != "") ? $slider_settings['slider_width'] : "100%";
        $slider_height = (isset($slider_settings['slider_height']) && $slider_settings['slider_height'] != "") ? $slider_settings['slider_height'] : "auto";
        $animationLoop = ($slider_settings['carousel'] == "1") ? "false" : "true";
        $autoPlay = ($slider_settings['autoPlay'] == "0" ? "false" : "true");
        $pauseOnHover = ($slider_settings['pauseOnHover'] == "0") ? "false" : "true";
        $controlNav = ($slider_settings['controlNav'] == "0") ? "false" : "true";
        $directionNav = ($slider_settings['directionNav'] == "0") ? "false" : "true";
        $randomize = ($slider_settings['randomize'] == "0") ? "false" : "true";
        $itemWidth = (($slider_settings['carousel_item_width'] != "") ? 'itemWidth:' . $slider_settings['carousel_item_width'] . ',' : '');
        $item_margin = 'itemMargin:' . (($slider_settings['carousel'] == "1") ? 10 : 0);
        $smoothHeight = ((preg_match('/(px)/', $slider_height, $result)) > 0) ? 'false' : 'true';

        $output.="<script>jQuery(window).load(function() {";
        if ($slider_settings['animation'] == "fade") {
            $output.="jQuery('." . $wrapper_class . "#" . $slide_id . " .flexslider .slides li').addClass('fixHeight');";
        }
        $output.="var slidshow = jQuery('." . $wrapper_class . "#" . $slide_id . " .flexslider').flexslider({
						animation: '" . $slider_settings['animation'] . "',
						animationLoop: " . $animationLoop . ",
						" . $itemWidth . "
						" . $item_margin . ",
						controlNav: " . $controlNav . ", 
						touch: false,	
						keyboard: true,
						smoothHeight: " . $smoothHeight . ", 
						randomize: " . $randomize . ",
						bigSliderDuration: " . $slider_settings['desc_duration'] . ",	
						pauseOnHover: " . $pauseOnHover . ",
                                                pauseOnAction: true,
						directionNav:" . $directionNav . ",
						slideshowSpeed: " . $slider_settings['slideshowSpeed'] . ",
						animationSpeed: " . $slider_settings['animationSpeed'] . ",
						descBgColor: '" . $slider_settings['desc_bg_color'] . "',
						descTextColor: '" . $slider_settings['desc_text_color'] . "',
						bigSliderWrapper:'" . $wrapper_class . "',
						sliderId:'" . $slide_id . "',
						start: apsl_sliderStart,
						before: apsl_sliderBefore,
						after: apsl_sliderAfter,
						});
                                                
                                                jQuery('." . $wrapper_class . "#" . $slide_id . " .flexslider .videoPlayImage').on('click', function () {
                                                jQuery(slidshow).data('flexslider').pause();
                                                });
                                                var onCarousel =  jQuery('." . $wrapper_class . "#" . $slide_id . "').attr('data-carousel');
                                                jQuery('." . $wrapper_class . "#" . $slide_id . " .flexslider').on('hover', function(){
                                                   if (typeof onCarousel !== typeof undefined && onCarousel !== false) {
                                                        if(jQuery(this).find('iframe').length > 0){
                                                            jQuery(slidshow).data('flexslider').pause();
                                                        }
                                                   }else{
                                                           var l = jQuery('." . $wrapper_class . "#" . $slide_id . " .flexslider .flex-active-slide').find('iframe').length;
                                                           if (l > 0) {
                                                                jQuery(slidshow).data('flexslider').pause();
                                                            }
                                                        }                                          
                                                 });
                                       });";

        $output.=""
                . "</script>";
        $output.='<div class="apargSlider"  id="' . $slide_id . '"  data-height="' . $slider_height . '"  style="width:' . $slider_width . ';  ' . (($slider_settings['animation'] == "fade" && $slider_height == "auto") ? '' : 'height:' . $slider_height . ';') . ' ' . (($slider_settings['animation'] == "fade") ? 'overflow:hidden;"' : '"');
        if ($slider_settings['autoPlay'] == '1') {
            $output.= ' data-autoplay = "true"';
        }
        if ($slider_settings['pauseOnHover'] == '1') {
            $output.= ' data-hover = "true"';
        }
        if ($slider_settings['carousel'] == '1') {
            $output.= ' data-carousel = "true"';
        }
        if ($slider_settings['animation'] == "fade") {
            $output.= ' data-fade = "true"';
        }

        $output.='"><div class="flexslider"><ul class="slides">';
        foreach ($images as $key => $image) {

            $tempLinks = $links[$key];
            if ($tempLinks && $tempLinks !== "NULL") {
                $output.= '<li ' . (($slider_height == "auto") ? 'style ="' : 'style = "max-height:100%;height: ' . $slider_height . ';') . (($slider_settings['autoPlay'] == '1' ) ? 'background-color:black;"' : 'height:auto;"') . '><img src="' . get_option('siteurl') . "" . $image . '" alt="' . $img_titles[$key] . '" data-link="' . $tempLinks . '" class="slide_n" ' . (($slider_settings['autoPlay'] == '1' ) ? 'style="visibility:hidden;"' : '"') . '><div class="captionWrapper" >';
                ;
            } else {
                $output.= '<li ' . (($slider_height == "auto") ? '' : 'style = "max-height:100%"') . '><img src="' . get_option('siteurl') . "" . $image . '" alt="' . $img_titles[$key] . '" class="slide_n"><div class="captionWrapper" >';
            }
            $tempDescs = explode('%APARG%', $descs[$key]);

            if ($tempDescs[0] !== "NULL") {
                foreach ($tempDescs as $tempDesc) {
                    if ($tempDesc != "") {
                        $output.='<div class="flex-caption">' . $tempDesc . '</div>';
                    }
                }
            }

            $output.= '</div>';

            if ($tempLinks && $tempLinks !== "NULL" && $slider_settings['autoPlay'] != "1") {
                $tempLinks = str_replace("watch?v=", "v/", $tempLinks);

                $output.= '<img src="' . plugins_url('/images/play.png', __FILE__) . '" class="videoPlayImage">';
            }

            $output.= '</li>';
        }

        $output.= '</ul></div></div>';
    }

    return $output;
}

/*
 * Ajax for attaching image to Yutube and Vimeo
 */

add_action('wp_ajax_apsl_attach_image_url', 'apsl_attach_image_url');

function apsl_attach_image_url($desc = null) {
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'aparg-slider')) {
        die();
    }
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    $file = sanitize_text_field($_POST['attachment']);


    $post_id = 0;
    $response = array(
        'status' => '',
        'img' => ''
    );
    if (!empty($file)) {
        $check = substr($file, 0, 1);
        if ($check != 'h') {
            $image = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$file.php"));
            $file = $image[0]['thumbnail_large'];
        }
        $tmp = download_url($file);
        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
        $file_array['name'] = basename($matches[0]);
        $file_array['tmp_name'] = $tmp;
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
            return $response;
        }
        $id = media_handle_sideload($file_array, $post_id, $desc);
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $response;
        }
        $img = wp_get_attachment_image_src($id, 'full');
        if (!$img)
            $response['status'] = 'false';
        else
            $response['status'] = 'success';
        $response['img'] = $img[0];
    }
    echo json_encode($response);
    wp_die();
}

/*
 * Ajax for changing set image
 */
add_action('wp_ajax_apsl_change_image', 'apsl_change_image');

function apsl_change_image($desc = null) {
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'aparg-slider')) {
        die();
    }
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    $file = sanitize_text_field($_POST['attachment']);
    $response = array(
        'status' => '',
        'img' => ''
    );
    if (!empty($file)) {
        $check = substr($file, 0, 1);
        if ($check != 'h') {
            $image = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$file.php"));
            $file = $image[0]['thumbnail_large'];
        }
        $tmp = download_url($file);
        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
        $file_array['name'] = basename($matches[0]);
        $file_array['tmp_name'] = $tmp;
        $post_id = 0;
        $id = media_handle_sideload($file_array, $post_id, $desc);
        $img = wp_get_attachment_image_src($id, 'full');
        if (!$img)
            $response['status'] = 'false';
        else
            $response['status'] = 'success';
        $response['img'] = $img[0];
    }
    echo json_encode($response);
    wp_die();
}
