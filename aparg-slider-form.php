<?php
defined('ABSPATH') or die('No script kiddies please!');
/*
 * Adding slider admin page
 */
add_action('admin_menu', 'apsl_slider_menu');

function apsl_slider_menu() {
    add_menu_page(__("Slider", "aparg-slider"), __('Slider', "aparg-slider"), "manage_options", 'apargslider', 'apsl_flex_slider', plugins_url('/images/logo.png', __FILE__));
}

/*
 * Function for outputing view
 */

function apsl_flex_slider() {
    /*
     * setting default values for settings fileds
     */
    $desc_bg_color = "#DB3256";
    $desc_text_color = "#ffffff";
    $auto_check_val = 0;
    $auto_checked = '';
    $rand_check_val = 0;
    $rand_checked = '';
    $wtd_val = '';
    $wdt_def = 1;
    $wtd_check = 'checked="checked"';
    $wtd_disabl = 'disabled';
    $hei_def = 1;
    $hei_check = 'checked="checked"';
    $hei_disabl = 'disabled';
    $hei_val = '';
    $carus_check_val = 0;
    $carus_checked = '';
    $control_checked = 'checked="checked"';
    $control_check_val = 1;
    $pause_checked = 'checked="checked"';
    $pause_check_val = 1;
    $direct_checked = 'checked="checked"';
    $direct_check_val = 1;
    $slideSpeed = 7000;
    $desc_dur = 1000;
    $anim_speed = 600;
    $car_ite_wtd = 210;
    $animation_val = "slide";
    $aparg_slider = isset($_GET['slider']) ? sanitize_text_field($_GET['slider']) : '';
    $get_id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $slider_tabs = apsl_get_all_sliders();
    foreach ($slider_tabs as $id => $slider) {
        if ($id == 0) {
            $first_slider_id = $slider->slider_id;
        }
        $last_tab_id = $slider->slider_id;
    }
    if ($get_id && count($slider_tabs) > 0) {
        $slider_id = intval($_GET['id']);
    } else if (!isset($_GET['id']) && count($slider_tabs) > 0) {
        $slider_id = $first_slider_id;
    } else if (count($slider_tabs) == 0 && $aparg_slider == "new") {
        $slider_id = 1;
    } else {
        $slider_id = 0;
    }
    if (isset($_POST["save_slider_" . $slider_id . ""])) {
        $apsl_nonce = sanitize_text_field($_POST['apsl_nonce']);
        if (!wp_verify_nonce($apsl_nonce, 'aparg-slider'))
            return;

        $uploadfiles = isset($_POST['img']) ? $_POST['img'] : '';
        $youtubelink = isset($_POST['tubelink']) ? $_POST['tubelink'] : '';
        $slides_titles = isset($_POST['title']) ? $_POST['title'] : '';
        $desc_array = array();

        $settings = $_POST['slide_options'];
        $settings['slider_width'] = (isset($_POST['slide_options']['slider_width_def'])) ? "" : sanitize_text_field(trim($_POST['slide_options']['slider_width']));
        $settings['slider_height'] = (isset($_POST['slide_options']['slider_height_def'])) ? "" : sanitize_text_field(trim($_POST['slide_options']['slider_height']));
        $settings['slider_width_def'] = (isset($_POST['slide_options']['slider_width_def'])) ? 1 : 0;
        $settings['slider_height_def'] = (isset($_POST['slide_options']['slider_height_def'])) ? 1 : 0;
        $settings['carousel'] = (isset($_POST['slide_options']['carousel'])) ? 1 : 0;
        $settings['animation'] = (isset($_POST['slide_options']['carousel'])) ? sanitize_text_field($_POST['slide_options']['animation']) : "slide";
        $settings['animation'] = (!isset($_POST['slide_options']['carousel']) && $_POST['slide_options']['animation'] == 'fade') ? sanitize_text_field($_POST['slide_options']['animation']) : "slide";
        $settings['randomize'] = (isset($_POST['slide_options']['randomize'])) ? 1 : 0;
        $settings['controlNav'] = (isset($_POST['slide_options']['controlNav'])) ? 1 : 0;
        $settings['directionNav'] = (isset($_POST['slide_options']['directionNav'])) ? 1 : 0;
        $settings['pauseOnHover'] = (isset($_POST['slide_options']['pauseOnHover'])) ? 1 : 0;
        $settings['autoPlay'] = (isset($_POST['slide_options']['autoPlay'])) ? 1 : 0;

        if (isset($_POST['desc']) && !empty($_POST['desc'])) {
            foreach ($_POST['desc'] as $key => $desc) {
                $desc_array[$key] = implode('%APARG%', $desc);
            }
            $slider_data = array();
            global $check;
            global $wpdb;
            foreach ($uploadfiles as $key => $slide_images):
                $slider_data[$key]["slide_url"] = str_replace(get_site_url(), "", $slide_images);
                $slider_data[$key]["slide_title"] = sanitize_text_field($slides_titles[$key]);
                $slider_data[$key]["description"] = (array_key_exists($key, $desc_array)) ? $desc_array[$key] : "NULL";
                $slider_data[$key]['youtubelink'] = (isset($youtubelink[$key])) ? $youtubelink[$key] : "";
            endforeach;
            $j = 0;
            foreach ($settings as $key => $slider_option):
                $slider_options[$j]["slider_option_name"] = $key;
                $slider_options[$j]["slider_option"] = $slider_option;
                $j++;
            endforeach;
            $get_content = apsl_get_slider_data($slider_id);

            apsl_add_slides($table_name = $wpdb->prefix . "aparg_flexslider", $slider_data, $slider_id);
            apsl_add_slider_options($tablename = $wpdb->prefix . "aparg_flexslider_options", $slider_options, $slider_id);
        }
    }
    $slider_settings = apsl_get_slider_settings($slider_id);
    ?>
    <div class='wrap'>
        <h2><?php _e('Aparg Slider Plugin', 'aparg-slider'); ?> 
            <div class="developed_by"><?php _e('Developed by', 'aparg-slider') ?> <a href="http://www.aparg.com" target="_blank">Aparg</a> </div>
        </h2>
        <div id="submit_error_msg" class="submit_error_msg" title=" <?php _e('Submit Notifications', 'aparg-slider'); ?>" style="display:none">
            <p><?php _e('You must upload at least 2 items, before submiting!', 'aparg-slider'); ?></p>
        </div>

        <div id="add_slider_msg" class="submit_error_msg" title="<?php _e('Add Slider Notifications', 'aparg-slider'); ?>" style="display:none">
            <p><?php _e('You should add slider by clicking on ' + ' tab.', 'aparg-slider'); ?></p>
        </div>

        <div id="delete_slide_msg" class="delete_slide_msg" title="<?php _e('Delete Slide Notifications', 'aparg-slider'); ?>" style="display:none">
            <p><?php _e('Are you sure to delete this slide?', 'aparg-slider'); ?></p>
        </div>

        <div id="delete_description_msg" class="delete_description_msg" title="<?php _e('Delete Description Notifications', 'aparg-slider'); ?>" style="display:none">
            <p><?php _e('Are you sure to delete this slide description?', 'aparg-slider'); ?></p>
        </div>

        <div id="descriptions_limit_msg" class="descriptions_limit_msg" title="<?php _e('Limit Description Count', 'aparg-slider'); ?>" style="display:none">
            <p><?php _e('Sorry, but you pass descriptions limit(4 descriptions of each slide).', 'aparg-slider'); ?></p>
        </div>
        <div class="nav-tabs-nav" id="aparg_slider_tabs">
            <div class="nav-tabs-wrapper">
                <div class="nav-tabs" >
                    <?php
                    $tabs = '';
                    $slider_tabs = apsl_get_all_sliders();
                    foreach ($slider_tabs as $id => $slider) {
                        if ($id == 0) {
                            $first_slider_id = $slider->slider_id;
                        }
                        $last_tab_id = $slider->slider_id;
                        if ($slider->slider_id == count($slider_tabs)) {
                            $active_tab = "nav-tab-active";
                        } else {
                            $active_tab = "";
                        }
                        $tabs.= '<a href="' . 'admin.php?page=apargslider&id=' . $slider->slider_id . '" id="' . $slider->slider_id . '" class="tabs"><span class="nav-tab ' . $active_tab . '" id="slide_N_' . $slider->slider_id . '">' . $slider->slider_name . '</span></a>';
                    }
                    echo $tabs;
                    if ($get_id && count($slider_tabs) > 0)
                        $page_id = $_GET['id'];
                    else if (!isset($_GET['id']) && count($slider_tabs) > 0)
                        $page_id = $first_slider_id;
                    else
                        $page_id = 0;
                    ?>
                    <a href="admin.php?page=apargslider&slider=new&id=<?php echo (count($slider_tabs) > 0) ? ($last_tab_id + 1) : 1; ?>" class="nav-tab menu-add-new" id="add-new-slider">
                        <abbr title="<?php _e('Add slide', 'aparg-slider'); ?>">+</abbr>
                    </a>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function () {
                jQuery('.nav-tab-active').removeClass('nav-tab-active');
                id = "<?php echo $page_id; ?>";
                jQuery('#slide_N_' + id).addClass('nav-tab-active');
                jQuery("#submit_error_msg,#add_slider_msg,#descriptions_limit_msg").dialog({
                    autoOpen: false,
                    buttons: {
                        "Ok": function () {
                            jQuery(this).dialog("close");
                        }
                    }
                });

                var param = window.location.search.substr(1);
                var params_array = param.split("&");
                var params = {};

                for (var i = 0; i < params_array.length; i++) {
                    var temp_array = params_array[i].split("=");
                    params[temp_array[0]] = temp_array[1];
                }
                var sliders_id = params.id;

                if (sliders_id === "undefined" || typeof sliders_id === "undefined")
                {
                    slider_id = 0;
                    jQuery('#AddImage').on('click', function (e) {
                        jQuery('#add_slider_msg').dialog('open');
                    });
                }
                else {
                    slider_id = sliders_id;
                }
                jQuery('#submit_' + slider_id).click(function (e) {
                    if (slider_id == 0)
                    {
                        jQuery('#add_slider_msg').dialog('open');
                        e.preventDefault();
                    }
                    else
                    {
                        if (jQuery('.row').size() < 2)
                        {
                            jQuery('#submit_error_msg').dialog('open');
                            e.preventDefault();
                        }
                    }
                });

            });
        </script>

        <form class="slider_form" method="post" name="slider_form_<?php echo $page_id; ?>" id="frm_<?php echo $page_id; ?>">
            <?php wp_nonce_field('aparg-slider', 'apsl_nonce'); ?>
            <?php if ($page_id != 0): ?>

                <div id="popupblock" class="media-modal">
                    <button type="button" class="button-link media-modal-close close" ><span class="media-modal-icon"><span class="screen-reader-text"><?php _e('Close media panel', 'aparg-slider') ?></span></span></button>
                    <div class="media-frame-title">
                        <h1 id="apargTitle"><?php _e('Add Video', 'aparg-slider'); ?></h1>
                    </div>
                    <div class="media-frame-router"></div>

                    <div class="media-frame-content">
                        <img class="imgcode" src="<?php echo esc_url(plugins_url('/images/ajax_loader.gif', __FILE__)); ?>">
                        <label for="iframe"><input id="video_input" type="text" name="youtubelink" placeholder="<?php _e('Youtube/Vimeo URL', 'aparg-slider') ?>"></label>
                    </div>
                    <div class="media-frame-toolbar">
                        <div class="media-toolbar">
                            <div class="media-toolbar-primary">
                                <input type="button" class="button media-button button-primary button-large media-button-select set" value="<?php _e('Add', 'aparg-slider'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="media-modal-backdrop" id="aparg-back" style="display: none;"></div>
                <div class="slider_container">
                    <div class="left">
                        <table class="widefat sortable" id="slide_img_container">
                            <thead>
                                <tr>
                                    <th>
                                        <?php _e('Slides', 'aparg-slider'); ?>
                                        <button id="AddImage" class="button alignright add-slide" data-editor="content"  title="<?php _e('Add Image', 'aparg-slider'); ?>">
                                            <span></span><?php _e('Add Image', 'aparg-slider'); ?></button> 
                                        <button id="AddVideo" rel="popupblock" data-width="600" data-editor="content" class="button alignright add-slide" data-editor="content"  title="<?php _e('Add Video', 'aparg-slider'); ?>">
                                            <span></span><?php _e('Add Video', 'aparg-slider'); ?></button> 
                            <div style="display:none;" class="close_url"><?php echo esc_url(plugins_url('images/close_delete.png', __FILE__)); ?></div>		
                            </th>
                            </tr>
                            </thead>
                            <tbody id="img_cont" class="ui-sortable">
                                <?php
                                if (isset($slider_settings) && !empty($slider_settings)) {
                                    $get_content = apsl_get_slider_data($slider_id);
                                    if (isset($get_content) && !empty($get_content)) {
                                        foreach ($get_content as $key => $value) {
                                            $thumb_url = $value->slide_url;
                                            $description = explode('%APARG%', $value->description);
                                            $youtubelink = $value->youtubelink;
                                            ?>
                                            <tr class="row sortable-row" id="row_<?php echo esc_attr($key) ?>" width="100%" style="background-color:#F9F9F9"><td width="99%" height="99%"><table class="table_<?php echo esc_attr($key) ?>"   width="100%" >

                                                        <tr width="100%"><td width="22%"><a href="#" style="background-image:url('<?php echo esc_url(get_option('siteurl')) . $thumb_url ?>')" class="current_img" alt="<?php echo $value->slide_title ?>"><span><?php _e("Click to change image", "aparg-slider") ?></span></a>
                                                                <input type="hidden" name="img[<?php echo esc_attr($key) ?>]" value="<?php echo esc_attr($value->slide_url) ?>" class="hidden_img" >
                                                                <input type="hidden" name="title[<?php echo esc_attr($key) ?>]" value="<?php echo esc_attr($value->slide_title) ?>" class="hidden_title"></td>
                                                            <td class="addinput" width="73%" id="<?php echo esc_attr($key) ?>" ><button class="button addDescription" name="addDescription" ><span></span><?php _e("Add Description", "aparg-slider") ?></button>&nbsp;&nbsp;
                                                                <?php $button_style = ($description[0] === "NULL") ? 'style="display:none;"' : ''; ?>
                                                                <button class="button empty_desc" name="empty_desc" <?php echo esc_attr($button_style) ?> id="delete_desc_row_<?php echo esc_attr($key) ?>"><span></span><?php _e("Delete Descriptions", "aparg-slider") ?></button>
                                                                <?php if ($youtubelink && $youtubelink != 'NULL') { ?>
                                                                    <br><input type="text" class="hidden_url" name="youtubelink[<?php echo $key ?>]" value="<?php echo $value->youtubelink ?>" data-url = "<?php echo $value->youtubelink ?>"><input type="button" class="button change" value='<?php _e("Set", "aparg-slider") ?>'>
                                                                    <input type="hidden" class="hidden_tube"  name="tubelink[<?php echo $key ?>]" value="<?php echo $value->youtubelink ?>">
                                                                    <img class="waiting" src="<?php echo esc_url(plugins_url('/images/ajax_loader.gif', __FILE__)) ?>">
                                                                    <div class="validyoutube"><?php _e("Please insert a valid video link", "aparg-slider") ?></div>
                                                                    <?php
                                                                }
                                                                foreach ($description as $k => $val):
                                                                    if ($val != "NULL") {
                                                                        ?>
                                                                        <p id="current_desc_<?php echo esc_attr($k) ?>" class="current_description" width="100%"><input type="text" class="desc" id="desc_<?php echo $key . $k ?>" name="desc[<?php echo $key . '][' . $k ?>]" value="<?php echo htmlentities($val) ?>" placeholder='<?php _e("Type a description", "aparg-slider") ?>' width="80%"/>
                                                                            <a href="#" class="delete_desc" remove_desc="<?php echo esc_attr($k) ?>" style="background-image:url('<?php echo esc_url(plugins_url('images/trash_can_delete.png', __FILE__)) ?>'); display: inline;"></a></p>
                                                                        <?php
                                                                    }
                                                                endforeach;
                                                                ?>
                                                            <td width="5%"><a href="#" deleted_row_id="<?php echo esc_attr($key) ?>" class="delete_img" ><img src="<?php echo esc_url(plugins_url('images/close_delete.png', __FILE__)) ?>"></a></td>
                                                            </td></tr></table></td></tr>
                                            <?php
                                        }
                                    }
                                    foreach ($slider_settings['slider_options'] as $key => $value) {
                                        switch ($key) {
                                            case 'slider_width_def':
                                                if ($value == "0") {
                                                    $wdt_def = 0;
                                                    $wtd_check = '';
                                                    $wtd_disabl = '';
                                                }
                                                break;
                                            case 'slider_width':
                                                $wtd_val = $value;
                                                break;
                                            case 'slider_height_def':
                                                if ($value == "0") {
                                                    $hei_def = 0;
                                                    $hei_check = '';
                                                    $hei_disabl = '';
                                                }
                                                break;
                                            case 'slider_height':
                                                $hei_val = $value;
                                                break;
                                            case 'desc_bg_color':
                                                $desc_bg_color = $value;
                                                break;
                                            case 'desc_text_color':
                                                $desc_text_color = $value;
                                                break;
                                            case 'directionNav':
                                                if ($value == 0) {
                                                    $direct_checked = '';
                                                    $direct_check_val = 0;
                                                }
                                                break;
                                            case 'pauseOnHover':
                                                if ($value == 0) {
                                                    $pause_checked = '';
                                                    $pause_check_val = 0;
                                                }
                                                break;
                                            case 'controlNav':
                                                if ($value == 0) {
                                                    $control_checked = '';
                                                    $control_check_val = 0;
                                                }
                                                break;
                                            case 'autoPlay':
                                                if ($value == 1) {
                                                    $auto_checked = 'checked="checked"';
                                                    $auto_check_val = 1;
                                                }
                                                break;
                                            case 'randomize':
                                                if ($value == 1) {
                                                    $rand_checked = 'checked="checked"';
                                                    $rand_check_val = 1;
                                                }
                                                break;
                                            case 'carousel':
                                                if ($value == 1) {
                                                    $carus_checked = 'checked="checked"';
                                                    $carus_check_val = 1;
                                                }
                                                break;
                                            case 'slideshowSpeed':
                                                $slideSpeed = $value;
                                                break;
                                            case 'desc_duration':
                                                $desc_dur = $value;
                                                break;
                                            case 'animationSpeed':
                                                $anim_speed = $value;
                                                break;
                                            case 'carousel_item_width':
                                                $car_ite_wtd = $value;
                                                break;
                                            case 'animation':
                                                $animation_val = $value;
                                                break;
                                        }
                                    }

                                    if ($aparg_slider == "delete") {
                                        foreach ($slider_tabs as $id => $slider) {
                                            if ($id == 0) {
                                                $first_slider_id = $slider->slider_id;
                                            }
                                            $last_tab_id = $slider->slider_id;
                                        }
                                        apsl_delete_slider($slider_id);
                                        if ($slider_id == $last_tab_id && count($tabs) != 1) {
                                            $slide_id = '&id=' . $first_slider_id;
                                        } else if (($slider_id + 1) != $last_tab_id && count($tabs) != 1) {
                                            $slide_id = '&id=' . $last_tab_id;
                                        } else if ($slider_id == $last_tab_id && count($tabs) == 1) {
                                            $slide_id = "";
                                        } else {
                                            $slide_id = '&id=' . ($slider_id + 1);
                                        }
                                        echo '<script>
							window.location.href="admin.php?page=apargslider' . $slide_id . '"
					</script>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <div class="slider_settings" id="slider_settings_<?php echo $page_id; ?>">
                <?php if ($page_id != 0): ?>
                    <table class="widefat">
                        <thead>
                            <tr style="width: 100%;">
                                <th  colspan="2">
                                    <?php _e('Settings', 'aparg-slider'); ?>
                                    <input class="button button-primary save-settings" type="submit" name="save_slider_<?php echo esc_attr($page_id); ?>" id="submit_<?php echo esc_attr($page_id); ?>" class="saveslider"  value="<?php _e('Save', 'aparg-slider'); ?>">	
                                </th>	
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="67%" class="slider_w">
                                    <label for="slider_width"><?php _e('Slider Width', 'aparg-slider'); ?>: </label>
                                    <p><?php _e('(Format: in px or %)', 'aparg-slider'); ?></p>
                                </td>
                                <td width="33%">
                                    <div class="slider_size">
                                        <?php
                                        ?>
                                        <input type="text" id="slider_width" name="slide_options[slider_width]" value="<?php echo $wtd_val; ?>" <?php echo $wtd_disabl; ?> class="defNumber"/>
                                        <p>
                                            <input type="checkbox" id="slider_width_def" name="slide_options[slider_width_def]" value='<?php echo $wdt_def; ?>' <?php echo $wtd_check; ?> />	
                                            <span>100%</span>										
                                        </p>	
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="67%" class="slider_h">
                                    <label for="slider_height"><?php _e('Slider Height', 'aparg-slider'); ?>: </label>
                                    <p>(<?php _e('Format: in px or', 'aparg-slider'); ?> %)</p>	
                                </td>
                                <td width="33%">
                                    <div class="slider_size" >
                                        <input type="text" id="slider_height" name="slide_options[slider_height]" value="<?php echo esc_attr($hei_val); ?>" <?php echo esc_attr($hei_disabl); ?> class="defNumber" />
                                        <p>
                                            <input type="checkbox" id="slider_height_def" name="slide_options[slider_height_def]" value='<?php echo esc_attr($hei_def); ?>' <?php echo $hei_check; ?>>
                                            <span><?php _e('auto', 'aparg-slider'); ?></span>
                                        </p>	
                                    </div>	
                                </td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="slideshowSpeed"><?php _e('Slide Show Speed', 'aparg-slider'); ?>:</label></td>
                                <td width="33%"><input type="text" id="slideshowSpeed" name="slide_options[slideshowSpeed]" value="<?php echo esc_attr($slideSpeed); ?>" class="number"/></td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="desc_duration"><?php _e('Description Speed', 'aparg-slider'); ?>:</label></td>
                                <td width="33%"><input type="text" id="desc_duration" name="slide_options[desc_duration]" value="<?php echo esc_attr($desc_dur); ?>" class="number"></td>	
                            </tr>
                            <tr>
                                <td width="67%"><label><?php _e('Description Background Color', 'aparg-slider'); ?>:</label></td>
                                <td width="33%" ><div class="choose_color"><input type="text" id="desc_bg_color" name="slide_options[desc_bg_color]" autocomplete="off" value="<?php echo esc_attr($desc_bg_color); ?>"><div id="current_bg_color"   data-color="<?php echo esc_attr($desc_bg_color); ?>" style='background-color: <?php echo $desc_bg_color; ?>'></div></div></td>

                            </tr>
                            <tr>
                                <td width="67%"><label><?php _e('Description Text Color', 'aparg-slider'); ?>:</label></td>
                                <td width="33%" ><div class="choose_color"><input type="text" id="desc_text_color" name="slide_options[desc_text_color]" autocomplete="off" value="<?php echo esc_attr($desc_text_color); ?>" ><div id="current_text_color" data-color="<?php echo esc_attr($desc_text_color); ?>" style='background-color: <?php echo $desc_text_color; ?>'></div></div></td>  
                            </tr>
                            <tr>
                                <td width="67%"><label for="animation"><?php _e('Animation', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <select type="text" id="animation" name="slide_options[animation]" value="<?php echo $animation_val; ?>">
                                        <option hidden="true"><?php echo esc_html($animation_val); ?></option>
                                        <option value="fade"><?php _e('Fade', 'aparg-slider'); ?></option>
                                        <option value="slide"><?php _e('Slide', 'aparg-slider'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="animationSpeed"><?php _e('Animation Speed', 'aparg-slider') ?>:</label></td>
                                <td width="33%"><input type="text" id="animationSpeed" name="slide_options[animationSpeed]" value="<?php echo esc_attr($anim_speed); ?>" class="number"></br><span id="animeNoteMessage"><?php _e('Please set Animation Speed</br> less than Slide Show Speed', 'aparg-slider') ?> </span></td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="carousel"><?php _e('Carousel', 'aparg-slider'); ?>: </label></td>
                                <td width="33%">
                                    <input type="checkbox" id="carousel" value="<?php echo esc_attr($carus_check_val); ?>" <?php echo $carus_checked; ?> name="slide_options[carousel]" >
                                </td>
                            </tr>

                            <tr>
                                <td width="67%"><label for="carousel_item_width"><?php _e('Carousel Item Width', 'aparg-slider'); ?>: </label></td>
                                <td width="33%"><input type="text" id="carousel_item_width" name="slide_options[carousel_item_width]" value="<?php echo esc_attr($car_ite_wtd); ?>" class="number"/></td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="randomize"><?php _e('Randomize', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <input type="checkbox" id="randomize" name="slide_options[randomize]" <?php echo $rand_checked; ?> value="<?php echo esc_attr($rand_check_val); ?>">
                                </td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="controlNav"><?php _e('Paging Navigation', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <input type="checkbox" id="controlNav" name="slide_options[controlNav]" value="<?php echo esc_attr($control_check_val); ?>" <?php echo $control_checked; ?>>
                                </td>
                            </tr>

                            <tr>
                                <td width="67%"><label for="directionNav"><?php _e('Direction Navigation', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <input type="checkbox" id="directionNav" value="<?php echo esc_attr($direct_check_val) ?>" name="slide_options[directionNav]"  <?php echo $direct_checked; ?>>
                                </td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="pauseOnHover"><?php _e('Pause On Hover', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <input type="checkbox" id="pauseOnHover" value="<?php echo esc_attr($pause_check_val); ?>" name="slide_options[pauseOnHover]" <?php echo $pause_checked ?>>
                                </td>
                            </tr>
                            <tr>
                                <td width="67%"><label for="autoPlay"><?php _e('Autoplay', 'aparg-slider'); ?>:</label></td>
                                <td width="33%">
                                    <input type="checkbox" id="autoPlay" value="<?php echo esc_attr($auto_check_val); ?>" <?php echo $auto_checked; ?> name="slide_options[autoPlay]" >
                                </td>
                            </tr>
                            <tr>
                                <td id="delApargSlider"><input type="button" onClick="window.onbeforeunload = function () {
                                                };
                                                location.href = '<?php echo esc_url('admin.php?page=apargslider&slider=delete&id=' . $page_id); ?>'" class="button" id="delete-slide" value="<?php _e('Delete This Slider', 'aparg-slider'); ?>"></td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="widefat" id="aparg_slider_usage">
                        <thead>
                            <tr>
                                <th><?php _e('Usage', 'aparg-slider'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="highlight"><?php _e('Shortcode', 'aparg-slider'); ?></td>
                            </tr>
                            <tr>
                                <td>[aparg_slider id=<?php echo $page_id; ?>]</td>
                            </tr>
                            <tr>
                                <td class="highlight"><?php _e('Template Include', 'aparg-slider'); ?></td>
                            </tr>
                            <tr>
                                <td>do_shortcode(&quot;[aparg_slider id=<?php echo $page_id; ?>]&quot;)</td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </form>
        <?php if ($page_id == 0): ?>
            <div class="but_wrap">
                <input type="button" onClick="location.href = '<?php echo esc_url('admin.php?page=apargslider&slider=new&id=1'); ?>'" class="button" id="add-first-slider" value="<?php _e('Add My First Slider', 'aparg-slider'); ?>">
            </div>
            <?php
        endif;
        ?>
    </div>

    <?php
    if (!isset($slider_settings) || empty($slider_settings)) {
        if ($aparg_slider == "new") {
            echo "<script>
						window.location.href='admin.php?page=apargslider&id=" . $slider_id . "'
					</script>";
            apsl_addnewslider($slider_id);
        } else if ($aparg_slider == "delete") {

            $tabs = apsl_get_all_sliders();
            foreach ($tabs as $id => $slider) {
                if ($id == 0) {
                    $first_slider_id = $slider->slider_id;
                }
                $last_tab_id = $slider->slider_id;
            }
            if ($slider_id == $last_tab_id && count($tabs) != 1) {
                $slide_id = '&id=' . $first_slider_id;
            } else if (($slider_id + 1) != $last_tab_id && count($tabs) != 1) {
                $slide_id = '&id=' . $last_tab_id;
            } else if ($slider_id == $last_tab_id && count($tabs) == 1) {
                $slide_id = "";
            } else {
                $slide_id = '&id=' . ($slider_id + 1);
            }
            echo '<script>
						window.location.href="admin.php?page=apargslider' . $slide_id . '"
					</script>';
            apsl_delete_slider($slider_id);
        }
    }
}
