/*
 * Make disable and take away clickablity from slider's description options 
 */
var switch_opt;
var warn_on_unload = "";
function apsl_switchopt() {
    if (jQuery(".desc").size() == 0) {
        switch_opt = "Off";
        jQuery("#desc_bg_color,#desc_text_color").attr("disabled", true);
        jQuery("#desc_bg_color,#desc_text_color").css("opacity", "0.5");
        jQuery("#current_bg_color,#current_text_color").css("opacity", "0.5");
    }
    else {
        jQuery(".desc").each(function () {
            if (jQuery(this).val() != "") {
                switch_opt = "On";
                jQuery("#desc_bg_color,#desc_text_color").attr("disabled", false);
                jQuery("#desc_bg_color,#desc_text_color").css("opacity", "1");
                jQuery("#current_bg_color,#current_text_color").css("opacity", "1");
                return false;
            }
            else {
                switch_opt = "Off";
                jQuery("#desc_bg_color,#desc_text_color").attr("disabled", true);
                jQuery("#desc_bg_color,#desc_text_color").css("opacity", "0.5");
                jQuery("#current_bg_color,#current_text_color").css("opacity", "0.5");
            }
        });
    }
}

function apsl_default_sizes(elem, el) {
    if (jQuery(elem).is(':checked')) {
        jQuery(elem).val(1);
        jQuery(elem).attr("checked", true);
        jQuery('#' + el).attr("disabled", true);
        jQuery('#' + el).val('');
    }
    else {
        jQuery(elem).val(0);
        jQuery(elem).attr("checked", false);
        jQuery('#' + el).attr("disabled", false);
    }
}


/*
 * Make disable and take away clickablity from slider's carousel item width options 
 */
function apsl_switch_carousel(elem) {
    if (jQuery(elem).is(":checked")) {
        jQuery(elem).val(1);
        jQuery(elem).attr("checked", true);
        jQuery('#autoPlay').attr("disabled", true);
        jQuery('#autoPlay').attr("checked", false);
        jQuery('#carousel_item_width').attr("disabled", false);
        jQuery("#animation option[value='slide']").attr('selected', true);

    }
    else {
        jQuery(elem).val(0);
        jQuery(elem).attr("checked", false);
        jQuery('#autoPlay').attr("disabled", false);
        jQuery('#carousel_item_width').attr("disabled", true);

    }
}
/*
 * Sorting Sliders
 */

function apsl_sorting_slides() {
    if (jQuery("#img_cont").size() > 0 && jQuery("#img_cont").children().size() >= 2)
    {
        var sortable_container = jQuery("#img_cont").height();

        jQuery("#slide_img_container tbody").sortable({
            items: "tr.sortable-row",
            cursor: "move",
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height());
                jQuery(this).children("tr.sortable-row").height(ui.item.height());
            },
            helper: function (e, ui) {
                ui.children().each(function () {
                    jQuery(this).width(jQuery(this).width());

                });
                ui.height(ui.height());

                return ui;
            },
            stop: function (event, ui) {
                jQuery(this).children("tr.sortable-row").height("auto");
            },
            update: function (e, ui)
            {
                jQuery(".row").each(function (row_id) {
                    jQuery(this).attr("id", "row_" + row_id);

                    jQuery(this).find("table").attr("id", "table_" + row_id);
                    jQuery(this).find(".hidden_img").attr("name", "img[" + row_id + "]");
                    jQuery(this).find(".hidden_title").attr("name", "title[" + row_id + "]");
                    jQuery(this).find(".hidden_url").attr("name", "youtubelink[" + row_id + "]");
                    jQuery(this).find(".hidden_tube").attr("name", "tubelink[" + row_id + "]");
                    jQuery(this).find(".addinput").attr("id", row_id);
                    jQuery(this).find(".desc").each(function (i)
                    {
                        jQuery(this).attr("id", "desc_" + row_id + "" + i);
                        jQuery(this).attr("name", "desc[" + row_id + "][" + i + "]");
                    });
                    jQuery(this).find(".delete_img").attr("deleted_row_id", row_id);
                });
                warn_on_unload = slider.leavingPage;
            }
        });
    }
}

/* **** */

jQuery(document).ready(function () {
    var check_changes = false;


    /* **** */
    var addimg_uploader;

    jQuery('#AddImage').click(function (e) {

        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (addimg_uploader) {
            addimg_uploader.open();
            return;
        }
        //Extend the wp.media object
        addimg_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: 'add'
        });

        /*
         * When a file is selected, grab the URL
         */
        addimg_uploader.on('select', function () {

            attachment = addimg_uploader.state().get('selection').toJSON();
            jQuery.each(attachment, function (i, item) {
                var allowedType = ['image/jpeg', 'image/png'];
                if (!(jQuery.inArray(item.mime, allowedType) > -1)) {
                    return true;
                }
                var rows = jQuery('.row').size();
                var url = jQuery('.close_url').text();
                var addContent = jQuery('#img_cont');
                var section_id = jQuery('.addinput').attr('id');
                var str = '<tr class="row sortable-row" id="row_' + rows + '" width="100%" style="background-color:#F9F9F9"><td width="99%" height="99%"><table id="table_' + rows + '" width="100%">';
                str += '<tr width="100%"><td width="25%"><a href="#" style="background-image:url(' + item.url + ')" class="current_img" alt="' + item.alt + '"><span>' + slider.clikChangeImg + '</span></a>';
                str += '<input type="hidden" class="hidden_img" name="img[' + rows + ']" value="' + item.url + '"><input type="hidden" class="hidden_title" name="title[' + rows + ']" value="' + item.alt + '"></td>';
                str += '<td width="68%" class="addinput" id="' + rows + '"><button class="button addDescription" name="addDescription" ><span></span>' + slider.addDescription + '</button>&nbsp;&nbsp;';
                str += '<button class="button empty_desc" name="empty_desc" id="delete_desc_row_' + rows + '"><span></span>' + slider.delDescription + '</button>';
                str += '<p id="curr_desc_0" class="current_description"><input type="text" class="desc" id="desc_' + rows + '0" name="desc[' + rows + '][0]" value="" placeholder="' + slider.typeDescription + '" />';
                str += '<a href="#" class="delete_desc" remove_desc="0" style="background-image:url(' + url.replace("close_delete.png", "") + 'trash_can_delete.png);"></a></p>';
                str += '</td><td width="5%"><a href="#" deleted_row_id="' + rows + '" class="delete_img" ><img src="' + url + '"></a></td></tr></table></td></tr>';
                jQuery(str).appendTo(addContent);

                section_id++;
                rows++;
            });

            /* **** */
            if (jQuery("#img_cont .row.sortable-row").size() >= 1) {
                apsl_switchopt();
                jQuery(".desc").on("blur", function () {
                    apsl_switchopt();
                });
            }

            apsl_sorting_slides()
            /* **** */

            return false;

        });
        addimg_uploader.on('close', function () {
            jQuery('#add_slider_msg').dialog('close');
        });
        //Open the uploader dialog
        addimg_uploader.open();
        warn_on_unload = slider.leavingPage;
    });
    // addVideo open pop up for youtube link
    jQuery('#AddVideo').click(function (e) {
        e.preventDefault();
        jQuery('#hideme').empty();
        var popID = jQuery(this).attr('rel');
        var dim = jQuery(this).attr('data-width');
        var popWidth = dim[0].split('=')[1];
        jQuery('#' + popID).fadeIn(200).css({'width': Number(popWidth)});
        jQuery('#aparg-back').fadeIn(200);
        return false;
    });

    // Close popup
    jQuery(document).on('click', 'button.close, #aparg-back', function () {
        jQuery('#aparg-back , #popupblock').fadeOut();
    });

    /*
     *  ajax call to my attach_image_url function
     */

    function apsl_youtubelink(x) {
        jQuery('#hideme').empty();
        var attachmentReplace;
        var validMsg = '<td class="hideme" id="hideme" align="center" width="99%" height="99%">' + slider.urlErrMessage + '</td></tr>';
        var attachment = urlParser.parse(jQuery('#video_input').val());
        console.log(attachment)
        if (attachment && (attachment.provider == 'youtube' || attachment.provider == 'vimeo')) {
            if (attachment.provider == 'youtube')
                attachmentReplace = 'http://i1.ytimg.com/vi/' + attachment.id + '/' + x + '.jpg';
            if (attachment.provider == 'vimeo')
                attachmentReplace = attachment.id;

        } else {
            jQuery(jQuery('#video_input')).after(validMsg);
            attachmentReplace = '';
        }
        return attachmentReplace;
    }

    function apsl_create_youtube(attach, span) {
        var rows = jQuery('.row').size();
        var url = jQuery('.close_url').text();
        var addContent = jQuery('#img_cont');
        var section_id = jQuery('.addinput').attr('id');
        var str = '<tr class="row sortable-row" id="row_' + rows + '" width="100%" style="background-color:#F9F9F9"><td width="99%" height="99%"><table id="table_' + rows + '" width="100%">';
        str += '<tr width="100%"><td width="25%"><a href="#" style="background-image:url(' + attach + ')" class="current_img" alt="">' + span + '</a>';
        str += '<input type="hidden" class="hidden_img" name="img[' + rows + ']" value="' + attach + '"><input type="hidden" class="hidden_title" name="title[' + rows + ']" value=""></td>';
        str += '<td width="68%" class="addinput" id="' + rows + '"><button class="button addDescription" name="addDescription" ><span></span>' + slider.addDescription + '</button>&nbsp;&nbsp;';
        str += '<button class="button empty_desc" name="empty_desc" id="delete_desc_row_' + rows + '"><span></span>' + slider.delDescription + '</button>';
        str += '<br><input type="text" id="you_' + rows + '" class="hidden_url" name="youtubelink[' + rows + ']" value="' + jQuery('#video_input').val() + '" data-url = "' + jQuery('#video_input').val() + '"><input type="button" class="button change" value="' + slider.set + '">';
        str += '<input type="hidden" id = "tube_' + rows + '" class="hidden_tube" name="tubelink[' + rows + ']" value="' + jQuery('#video_input').val() + '">';
        str += '<img class="waiting" src="' + slider.loadImg + '">';
        str += '<div class="validyoutube">' + slider.urlErrMessage + '</div>';
        str += '<p id="curr_desc_0" class="current_description"><input type="text" class="desc" id="desc_' + rows + '0" name="desc[' + rows + '][0]" value="" placeholder="' + slider.typeDescription + '" />';
        str += '<a href="#" class="delete_desc" remove_desc="0" style="background-image:url(' + url.replace("close_delete.png", "") + 'trash_can_delete.png);"></a></p>';
        str += '</td><td width="5%"><a href="#" deleted_row_id="' + rows + '" class="delete_img" ><img src="' + url + '"></a></td></tr></table></td></tr>';
        jQuery(str).appendTo(addContent);
        jQuery('#video_input').val('');
        section_id++;
        rows++;
        apsl_sorting_slides();
    }
    jQuery(document).on('click', '.set', function () {
        jQuery('#hideme').empty();
        var val = apsl_youtubelink('maxresdefault');
        var validMsg = '<td class="hideme" id="hideme" align="center" width="99%" height="99%">' + slider.cantFindThumb + '</td></tr>';
        if (val != '') {
            var attach;
            var data = {
                'action': 'apsl_attach_image_url',
                'attachment': apsl_youtubelink('maxresdefault'),
                'nonce': slider.nonce
            };
            jQuery.ajax({
                type: 'POST',
                url: slider.url,
                dataType: "json",
                data: data,
                beforeSend: function () {
                    jQuery('#video_input').hide();
                    jQuery('.imgcode').show();
                },
                success: function (response) {
                    if (response.status == 'success')
                        attach = response.img;
                    else
                        attach = '';
                    if (attach == '' || !attach.match(/^http([s]?):\/\/.*/)) {
                        apsl_thumbSecondTry(validMsg)
                    } else {
                        jQuery(".imgcode").hide();
                        jQuery('#video_input').show();
                        apsl_create_youtube(attach, '<span>' + slider.clikChangeImg + '</span>');
                        jQuery('#aparg-back , #popupblock').fadeOut();

                    }

                },
                error: function () {
                    apsl_thumbSecondTry(validMsg);
                }
            });
        }
    });

    /*
     * Second Try to get Youtube Thumbnail 
     */
    function apsl_thumbSecondTry(msg) {
        var img;
        var youtubeLink = apsl_youtubelink(0);
        var data = {
            'action': 'apsl_attach_image_url',
            'attachment': youtubeLink,
            'nonce': slider.nonce

        };

        jQuery.ajax({
            type: 'POST',
            url: slider.url,
            dataType: "json",
            data: data,
            beforeSend: function () {
                jQuery('#video_input').hide();
                jQuery('.imgcode').show();
            },
            success: function (response) {
                if (response.status == 'success')
                    img = response.img;
                else
                    img = '';
                jQuery('#video_input').show();
                jQuery(".imgcode").hide();
                if (img == '' || !img.match(/^http([s]?):\/\/.*/)) {

                    var rows = jQuery('.row').size();
                    var url = jQuery('.close_url').text();
                    var addContent = jQuery('#img_cont');
                    var section_id = jQuery('.addinput').attr('id');
                    jQuery('#hideme').empty();
                    jQuery(jQuery('#video_input')).after(msg);
                    jQuery('#video_input').val('');

                } else {
                    jQuery(".imgcode").hide();
                    jQuery('#video_input').show();
                    apsl_create_youtube(img, '<span class="warn_color">' + slider.chooseResolution + '</span>');
                    jQuery('#aparg-back , #popupblock').fadeOut();
                }
            },
            error: function () {
                jQuery('#video_input').show();
                jQuery(".imgcode").hide();
                var rows = jQuery('.row').size();
                var url = jQuery('.close_url').text();
                var addContent = jQuery('#img_cont');
                var section_id = jQuery('.addinput').attr('id');
                jQuery('#hideme').empty();
                jQuery(jQuery('#video_input')).after(msg);
                jQuery('#video_input').val('');
            }
        });
    }
    /*
     *Second ajax for set youtube thumbnail
     */
    function apsl_setThumbSecondTry(link, youtube_url, real_link) {
        var img;
        var data = {
            'action': 'apsl_change_image',
            'attachment': apsl_changeLink('0', link),
            'nonce': slider.nonce
        };

        jQuery.ajax({
            type: 'POST',
            url: slider.url,
            dataType: "json", data: data,
            success: function (resp) {
                if (resp.status == 'success')
                    img = resp.img;
                else
                    img = '';

                if (img == '' || !img.match(/^http([s]?):\/\/.*/)) {
                    jQuery(link).parent().find('.waiting').css('display', 'none');
                    jQuery(link).parent().find('.validyoutube').text(slider.cantFindThumb);
                    jQuery(link).parent().find('.validyoutube').css('visibility', 'visible');
                    jQuery(link).parent().find('.hidden_url').val(youtube_url);
                } else {
                    jQuery(link).parent().find('.waiting').css('display', 'none');
                    var rows = jQuery('.row').size();
                    var url = jQuery('.close_url').text();
                    var addContent = jQuery('#img_cont');
                    var section_id = jQuery('.addinput').attr('id');
                    jQuery(link).parent().prev().find('.current_img').css('background-image', "url(" + img + ")")
                    jQuery(link).parent().prev().find('.current_img').find('span').text(slider.chooseResolution).addClass('warn_color');
                    jQuery(link).parent().prev().find('.hidden_img').val('' + img + '');
                    jQuery(link).parent().find('.hidden_tube').val(real_link);
                    jQuery(link).parent().find('.hidden_url').attr('data-url', real_link);
                }
            },
            error: function () {
                jQuery(link).parent().find('.waiting').css('display', 'none');
                jQuery(link).parent().find('.validyoutube').css('visibility', 'visible');
                jQuery(link).parent().find('.hidden_url').val(youtube_url);
            }
        });


    }
    /*
     *  Second ajax call for updating url and image
     */

    function apsl_changeLink(y, x) {
        var attachmentReplace;
        var attachment = urlParser.parse(jQuery(x).parent().find('.hidden_url').val());


        if (attachment && (attachment.provider == 'youtube' || attachment.provider == 'vimeo')) {
            if (attachment.provider == 'youtube')
                attachmentReplace = 'http://i1.ytimg.com/vi/' + attachment.id + '/' + y + '.jpg'
            if (attachment.provider == 'vimeo')
                attachmentReplace = attachment.id;

        } else {
            jQuery(x).parent().find('.validyoutube').css('visibility', 'visible');
            attachmentReplace = '';
        }

        return attachmentReplace;
    }
    jQuery(document).on('click', '.change', function () {
        var link = this;
        jQuery('.validyoutube').css('visibility', 'hidden');
        jQuery(link).parent().find('.validyoutube').text(slider.urlErrMessage);
        var youtube_url = jQuery(link).parent().find('.hidden_tube').val();
        var real_link = jQuery(link).parent().find('.hidden_url').val();
        var changeYutubeLink = apsl_changeLink('maxresdefault', link);
        if (changeYutubeLink != '') {
            var attach;
            var data = {
                'action': 'apsl_change_image',
                'attachment': changeYutubeLink,
                'nonce': slider.nonce
            };
            jQuery.ajax({
                type: 'POST',
                url: slider.url,
                dataType: "json",
                data: data,
                beforeSend: function () {
                    jQuery(link).parent().find('.waiting').css('display', 'inline-block');
                },
                success: function (resp) {
                    if (resp.status == 'success')
                        attach = resp.img;
                    else
                        attach = '';
                    if (attach == '' || !attach.match(/^http([s]?):\/\/.*/)) {
                        apsl_setThumbSecondTry(link, youtube_url, real_link);
                    }
                    else {

                        jQuery(link).parent().find('.waiting').css('display', 'none');
                        jQuery(link).parent().find('.validyoutube').css('visibility', 'hidden');
                        var rows = jQuery('.row').size();
                        var url = jQuery('.close_url').text();
                        var addContent = jQuery('#img_cont');
                        var section_id = jQuery('.addinput').attr('id');
                        jQuery(link).parent().prev().find('.current_img').find('span').text(slider.clikChangeImg).css('color', '#ffffff');
                        jQuery(link).parent().prev().find('.current_img').css('background-image', "url(" + attach + ")");
                        jQuery(link).parent().prev().find('.hidden_img').val('' + attach + '');
                        jQuery(link).parent().find('.hidden_tube').val(real_link);
                        jQuery(link).parent().find('.hidden_url').attr('data-url', real_link);

                    }
                },
                error: function () {
                    apsl_setThumbSecondTry(link, youtube_url, real_link);
                }
            });
        } else {
            jQuery(link).parent().find('.hidden_url').val(youtube_url);
        }
    });

    jQuery('#img_cont').on('click', '.addDescription', function () {
        var parent = jQuery(this).parent();
        var url = jQuery('.close_url').text();
        var i = jQuery(this).parent('.addinput').children("p").size();
        var current_section_id = parent.attr('id');
        var curent_section = jQuery(this).parent('#' + current_section_id);
        if (i >= 4)
        {
            jQuery('#descriptions_limit_msg').dialog('open');
        }
        else
        {
            jQuery('<p id="current_desc_' + i + '" class="current_description"><input type="text" class="desc" id="desc_' + current_section_id + '' + i + '" name="desc[' + current_section_id + '][' + i + ']" value="" placeholder="' + slider.typeDescription + '"  /><a href="#" class="delete_desc" remove_desc="' + i + '" style="background-image:url(' + url.replace("close_delete.png", "") + 'trash_can_delete.png)"></a></p>').appendTo(curent_section);
        }
        i++;

        if (i == 1)
        {
            jQuery('.empty_desc#delete_desc_row_' + current_section_id).show();
        }

        /* **** */
        apsl_switchopt();
        jQuery(".desc").on("blur", function () {
            apsl_switchopt();
        });
        /* **** */
        warn_on_unload = slider.leavingPage;
        return false;

    });

    jQuery('#img_cont').on('click', '.empty_desc', function (e) {
        e.preventDefault();
        var that = this;
        jQuery("#delete_description_msg").dialog({
            autoOpen: true,
            buttons: {
                "Yes": function () {
                    jQuery(that).parent().find('p').remove();
                    jQuery(that).hide();
                    /* **** */
                    apsl_switchopt();
                    /* **** */

                    jQuery(this).dialog("close");
                },
                "No": function () {
                    jQuery(this).dialog("close");
                }
            },
        });
        apsl_switchopt();
        warn_on_unload = slider.leavingPage;
    });
    jQuery('#desc_bg_color, #desc_text_color').ColorPicker({
        onSubmit: function (hsb, hex, rgb, el) {
            jQuery(el).val('#' + hex);
            if (jQuery(el).attr('id') == "desc_bg_color")
            {
                jQuery('#current_bg_color').css('background-color', '#' + hex);
            }
            else
            {
                jQuery('#current_text_color').css('background-color', '#' + hex);

            }
            jQuery(el).ColorPickerHide();
            warn_on_unload = slider.leavingPage;
        },
        onBeforeShow: function () {
            jQuery(this).ColorPickerSetColor(this.value);
        }
    }).bind('keyup', function () {
        jQuery(this).ColorPickerSetColor(this.value);
    });

// **** //
    // use ColorPicker plugin to choose color
    jQuery('#current_bg_color').ColorPicker({
        color: jQuery('#current_bg_color').attr('data-color'),
        onShow: function (colpkr, el) {
            if (switch_opt == "On") {
                jQuery(colpkr).fadeIn(500);
            }
            return false;
        },
        onHide: function (colpkr) {
            jQuery(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb, el) {
            jQuery(el).val('#' + hex);
            jQuery(el).ColorPickerHide();
        },
        onSubmit: function (hsb, hex, rgb, el) {
            jQuery(el).css('background-color', '#' + hex);
            jQuery('#current_bg_color').attr('data-color', '#' + hex);
            jQuery('#desc_bg_color').val('#' + hex);
            jQuery(el).ColorPickerHide();
            warn_on_unload = slider.leavingPage;
        }
    });

    jQuery('#current_text_color').ColorPicker({
        color: jQuery('#current_text_color').attr('data-color'),
        onShow: function (colpkr) {
            if (switch_opt == "On") {
                jQuery(colpkr).fadeIn(500);
            }
            return false;
        },
        onHide: function (colpkr) {
            jQuery(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb, el) {
            jQuery(el).val('#' + hex);
            jQuery(el).ColorPickerHide();
        },
        onSubmit: function (hsb, hex, rgb, el) {
            jQuery(el).css('background-color', '#' + hex);
            jQuery('#current_text_color').attr('data-color', '#' + hex);
            jQuery('#desc_text_color').val('#' + hex);
            jQuery(el).ColorPickerHide();
            warn_on_unload = slider.leavingPage;
        }
    });

// **** // 

    jQuery('#randomize, #controlNav, #pauseOnHover, #directionNav, #autoPlay').click(function () {
        if (jQuery(this).attr('checked') == "checked")
        {
            jQuery(this).attr('checked', true);
            jQuery(this).val(1);
        }
        else
        {
            jQuery(this).attr('checked', false);
            jQuery(this).val(0);
        }
        warn_on_unload = slider.leavingPage;
    });

    jQuery('#img_cont').on('click', '.delete_img', function () {
        deleted_id = jQuery(this).attr('deleted_row_id');
        jQuery("#delete_slide_msg").dialog({
            autoOpen: true,
            buttons: {
                "Yes": function () {
                    jQuery('.row').each(function () {
                        row_id = jQuery(this).attr('id').replace("row_", "");
                        if (row_id > deleted_id)
                        {
                            row_id--;
                            jQuery(this).attr('id', 'row_' + row_id);

                            jQuery(this).find('table').attr('id', 'table_' + row_id);
                            jQuery(this).find('.hidden_img').attr('name', 'img[' + row_id + ']');
                            jQuery(this).find('.hidden_url').attr('name', 'youtubelink[' + row_id + ']');
                            jQuery(this).find('.hidden_tube').attr('name', 'tubelink[' + row_id + ']');
                            jQuery(this).find('.hidden_title').attr('name', 'title[' + row_id + ']');
                            jQuery(this).find('.addinput').attr('id', row_id);
                            jQuery(this).find('.desc').each(function (i) {
                                jQuery(this).attr('id', 'desc_' + row_id + '' + i);
                                jQuery(this).attr('name', 'desc[' + row_id + '][' + i + ']');
                            });
                            jQuery(this).find('.delete_img').attr('deleted_row_id', row_id);
                        }
                    });
                    jQuery('#row_' + deleted_id).remove();
                    jQuery(this).dialog("close");

                    /* **** */
                    apsl_switchopt();
                    /* **** */
                },
                "No": function () {
                    jQuery(this).dialog("close");
                }
            },
        });
        warn_on_unload = slider.leavingPage;
        return false;
    });
    jQuery('#img_cont').on('click', '.delete_desc', function () {
        deleted_id = jQuery(this).attr('remove_desc');
        deleted_cont = jQuery(this).parent();
        deleted_item = jQuery(this);
        row_id = jQuery(this).parent().parent().attr('id');
        jQuery("#delete_description_msg").dialog({
            autoOpen: true,
            buttons: {
                "Yes": function () {
                    deleted_item.parent().parent().find('.desc').each(function (i) {
                        el = jQuery(this);
                        if (i == deleted_id)
                        {
                            deleted_cont.remove();
                        }
                        else if (i > deleted_id)
                        {
                            i--;
                            el.attr('id', 'desc_' + row_id + '' + i);
                            el.attr('name', 'desc[' + row_id + '][' + i + ']');
                            el.next().attr('remove_desc', i);
                            el.parent().attr('id', 'current_desc_' + i);
                        }
                        if (jQuery('.addinput#' + row_id + ' p').size() == 0)
                        {
                            jQuery('.empty_desc#delete_desc_row_' + row_id).hide();
                        }
                        /* **** */
                        apsl_switchopt();
                        /* **** */
                    });
                    jQuery(this).dialog("close");
                },
                "No": function () {
                    jQuery(this).dialog("close");
                }
            },
        });

        warn_on_unload = slider.leavingPage;
        return false;
    });
    var custom_uploader;
    jQuery('#img_cont').on('click', '.current_img', function (e) {

        var current_img = jQuery(this);
        e.preventDefault(this);
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL
        custom_uploader.on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowedType = ['image/jpeg', 'image/png'];
            if (!(jQuery.inArray(attachment.mime, allowedType) > -1)) {
                current_img.addClass('invalid')

            } else {
                current_img.removeClass('invalid');
                current_img.css('background-image', 'url(' + attachment.url + ')');
                current_img.attr('alt', attachment.alt);
                current_img.next().val(attachment.url);
                current_img.parent().find('.hidden_title').val(attachment.alt);
                current_img.find('span').text(slider.clikChangeImg).css('color', '#fff');
                warn_on_unload = slider.leavingPage;
            }
        });
        custom_uploader.on('close', function () {
            jQuery('#add_slider_msg').dialog('close');
        });
        //Open the uploader dialog
        custom_uploader.open();
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
        slider_id = 1;
    }
    else {
        slider_id = sliders_id;
    }
    jQuery('input:text,input:checkbox,select').on('change', function ()
    {
        warn_on_unload = slider.leavingPage;
    });


    jQuery('#carousel').on('click', function () {
        apsl_switch_carousel(jQuery(this));
    });

    jQuery('#slider_width_def').on('click', function () {
        apsl_default_sizes(jQuery(this), 'slider_width');
    });

    jQuery('#slider_height_def').on('click', function () {
        apsl_default_sizes(jQuery(this), 'slider_height');
    });

    jQuery('#animation').on('change', function () {
        if (jQuery(this).val() == "fade") {
            jQuery('#carousel').val(0);
            jQuery('#carousel').attr("checked", false);
            jQuery('#carousel_item_width').attr("disabled", true);
        }

    });


    /*
     * validaton for input boxes
     */
    jQuery('.save-settings').on('click', function () {
        warn_on_unload = "";

        var checked = true;
        var check = true;
        var result = true;
        var animeSpeed = jQuery('#animationSpeed').val();
        var slideSpeed = jQuery('#slideshowSpeed').val();
        if ((!isNaN(animeSpeed) && !isNaN(slideSpeed)) && parseInt(animeSpeed, 10) >= parseInt(slideSpeed, 10)) {
            jQuery('#animeNoteMessage').css('display', 'inline');
            check = false;
        } else {
            jQuery('#animeNoteMessage').css('display', 'none');
        }
        jQuery('input[type="text"]').each(function () {
            var value = jQuery(this).val();
            var checked = true;

            if (jQuery(this).hasClass('number')) {
                result = apsl_checkNumber(value, checked);
            }
            if (jQuery(this).hasClass('defNumber')) {
                checked = jQuery(this).parents('td').find('input[type="checkbox"]').is(":checked") ? false : true;
                result = apsl_PixPrCheck(value, checked);
            }

            if (!result) {
                jQuery(this).addClass('invalid');
                check = false;
                result = true;
            } else {
                jQuery(this).removeClass('invalid');

            }


        });
        if (!check)
            return false;
    });


    function apsl_saIsPositiveInteger(val) {
        return val == "0" || ((val | 0) > 0 && val % 1 == 0);
    }

    function apsl_PixPrCheck(val, check) {
        if (!check)
            return true;
        var px_index = val.lastIndexOf('px');
        var percent_index = val.lastIndexOf('%');

        var int_val = false;

        if (val.length > 2 && px_index == val.length - 2) {
            int_val = val.replace(/px$/, '');
        } else if (val.length > 1 && percent_index == val.length - 1) {
            int_val = val.replace(/%$/, '');
        } else {
            return false;
        }

        return apsl_saIsPositiveInteger(int_val);
    }

    function apsl_checkNumber(val, checked) {
        if (checked) {
            return  !val.match(/^[1-9]\d*$/g) ? false : true;

        } else {
            return !val || val.match(/^[1-9]\d*$/g) ? true : false;

        }
    }

});

/*
 * Making sure that settings are saved befor leave page
 */

window.onbeforeunload = function () {
    if (warn_on_unload != '')
    {
        return warn_on_unload;

    }
}
/* **** */
jQuery(window).load(function () {

    apsl_sorting_slides();
    apsl_switch_carousel(jQuery('#carousel'));
    apsl_switchopt();

    jQuery(".desc").on("blur", function () {
        apsl_switchopt();
    });
    jQuery(document).bind("keyup keypress", "input", function (e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

});
