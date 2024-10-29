function apsl_resizeCapions(slider) {
    
    var sliderWidth = jQuery('#' + slider.sliderId).outerWidth(true);
    var maxSliderWidthToResize = 800;
    var margin_bottom = 10;
    var font_size = 16;
    var padding = 12;
    var line_height = 20;
    var captionFactor = (sliderWidth < maxSliderWidthToResize) ? sliderWidth / maxSliderWidthToResize : 1;
    jQuery('#' + slider.sliderId + ' .flexslider .flex-caption').each(function (i) {
        jQuery(this).css('margin-bottom', margin_bottom * captionFactor + 'px');
        jQuery(this).css('font-size', font_size * captionFactor + 'px');
        jQuery(this).css('padding', padding * captionFactor + 'px');
        jQuery(this).css('line-height', line_height * captionFactor + 'px');
        jQuery(this).css('text-transform', 'uppercase');
        jQuery('#' + slider.sliderId + ' .captionWrapper').css('line-height', line_height * captionFactor + 'px');
    });
}
/*
 * Slider Callback START
 */
function apsl_sliderStart(slider) {
    var _that = this;
    var wrap_class = jQuery('#' + this.sliderId);
    var attr = jQuery(wrap_class).attr('data-autoplay');
    var link = jQuery(wrap_class).find('.flex-active-slide  .slide_n').attr('data-link');
    var vid = jQuery(wrap_class).find('.slide_n[data-link^="http"]').length;
    var img_height = jQuery(wrap_class).find('.flex-active-slide  .slide_n').height();
    if (link) {
        var attachment = urlParser.parse(link);
        var autolink = 'https://www.youtube.com/embed/' + attachment.id + '?rel=0&amp;autoplay=1';
        if (attachment.provider == 'vimeo') {
            autolink = 'http://player.vimeo.com/video/' + attachment.id + '?api=1&autoplay=1';
        }
    }
    if (vid) {
        jQuery(wrap_class).find('.flex-control-nav').css({
            'backface-visibility': 'hidden',
            'transition': 'bottom 2000ms',
            '-webkit-transition': 'bottom 2000ms',
            '-moz-transition': 'bottom 2000ms',
            '-o-transition': 'bottom 2000ms',
            'width': '50%',
            'left': '0',
            'right': '0',
            'margin': '0 auto'});
    }
    if (typeof attr !== typeof undefined && attr !== false) {

        if (typeof link !== typeof undefined && link !== false) {
            slider.pause();
            jQuery(wrap_class).find('.flex-control-nav').css('bottom', '0px');
            jQuery(wrap_class).find('.flex-active-slide').append('<iframe width="100%" style="height:' + img_height + 'px" src="' + autolink + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
            jQuery(wrap_class).find('.flex-active-slide .slide_n').hide();
        }
    }
    jQuery(wrap_class).find('.flexslider .slides li').css('margin', (this.itemMargin / 2) + 'px');
    jQuery(wrap_class).find('.flexslider .flex-caption').css('background-color', this.descBgColor);
    jQuery(wrap_class).find('.flexslider .flex-caption').css('color', this.descTextColor);
    jQuery(wrap_class).find('.flexslider .captionWrapper .flex-caption').css('visibility', 'visible');
    jQuery('#' + this.sliderId + ' .flex-active-slide .captionWrapper .flex-caption').each(function (index) {
        var offset = jQuery('#' + _that.sliderId + ' .flex-active-slide .captionWrapper').position().left + jQuery(this).outerWidth(true);
        var animateTo = 0;
        jQuery(this).css('left', '-' + offset + 'px').show();
        var that = this;
        if (!attr) {
            setTimeout(function () {
                jQuery(that).animate({
                    'left': animateTo
                }, _that.bigSliderDuration * 0.45
                        );
            }, (index * _that.bigSliderDuration) * 0.45);
        } else {
            jQuery(this).css('display', 'none');
        }
    });
    jQuery(wrap_class).find('.flexslider .slides li').not('.flex-active-slide').find('.captionWrapper .flex-caption').hide();
    this.captionStyle = new Object();
    apsl_resizeCapions(this);
    var _that = this;
    jQuery(window).resize(function () {
        apsl_resizeCapions(_that);
    });
    if (_that.smoothHeight === false)
        jQuery('#' + this.sliderId + ' .flexslider, .' + this.bigSliderWrapper + '#' + this.sliderId + ' .flex-viewport').addClass('wholesized');
}
/*
 * Slider Callback BEFORE
 */
function apsl_sliderBefore(slider) {
    var delay = 0;
    var _that = this;
    var current_video_link = '';
    //Remove Iframe and show image navbar click
    var animateSlide = slider.slides.eq(slider.animatingTo);
    var animate = animateSlide.find('.slide_n');
    var height = jQuery(animate).height();
    var current_link = jQuery(animate).attr('data-link');
    var wrap_class = jQuery('#' + this.sliderId);
    var active_slide = jQuery(wrap_class).find('.flex-active-slide');
    var active_link = jQuery(active_slide).find('.slide_n').attr('data-link');

    if (current_link) {

        var attachment = urlParser.parse(current_link);
        current_video_link = 'https://www.youtube.com/embed/' + attachment.id + '?rel=0&amp;autoplay=1';
        if (attachment.provider == 'vimeo') {
            current_video_link = 'http://player.vimeo.com/video/' + attachment.id + '?api=1&autoplay=1';
        }

    }
    if (active_link) {
        var activ_link_pars = urlParser.parse(active_link);
        active_link = 'https://www.youtube.com/embed/' + activ_link_pars.id;
        if (activ_link_pars.provider == 'vimeo') {
            active_link = 'http://player.vimeo.com/video/' + activ_link_pars.id;
        }
    }
    var attr = wrap_class.attr('data-autoplay');
    var fade = wrap_class.attr('data-fade');
    var carousel = jQuery('#' + this.sliderId).attr("data-carousel");

    if (attr) {
        if (typeof fade == typeof undefined || fade == false) {
            if (typeof current_link !== typeof undefined && current_link !== false) {
                if (jQuery(active_slide).find('iframe').length > 0) {//video-video
                    jQuery(active_slide).find('iframe').remove();
                    jQuery(active_slide).append('<iframe width="100%" style="height:' + height + 'px" src="' + active_link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
                    jQuery(animateSlide).find('img').hide();
                    jQuery(animateSlide).find('iframe').remove();
                    jQuery(animateSlide).append('<iframe width="100%" style="height:' + height + 'px" src="' + current_video_link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
                    slider.pause();
                } else {//img-video
                    jQuery(animateSlide).find('iframe').remove();
                    slider.pause();
                    jQuery(animateSlide).find('img').hide();
                    jQuery(animateSlide).append('<iframe width="100%" style="height:' + height + 'px" src="' + current_video_link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');

                }

            } else {//video-img
                if (jQuery(active_slide).find('iframe').length > 0) {
                    jQuery(animateSlide).find('img').show();
                    jQuery(active_slide).find('iframe').remove();
                    jQuery(wrap_class).find('.flexslider .flex-control-nav').show();
                    jQuery(active_slide).append('<iframe width="100%" style="height:' + height + 'px" src="' + current_video_link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');

                }//img-img
            }
        } else {
            if (jQuery(active_slide).find('iframe').length > 0) {
                jQuery(active_slide).find('iframe').remove();
                jQuery(active_slide).find('.slide_n').show();
            }
        }
    } else {//without aotoplay options
        if (typeof carousel !== typeof undefined && carousel !== false) {
            jQuery(wrap_class).find('iframe').parent().find('img').fadeIn(100);
            jQuery(wrap_class).find('iframe').remove();
            jQuery(wrap_class).find('.flexslider .flex-control-nav').show();
        } else {

            jQuery(active_slide).find('iframe').remove();
            jQuery(active_slide).find('.slide_n').show();
            jQuery(active_slide).find('.videoPlayImage').show();
        }
    }

    var sliderWidth = jQuery(wrap_class).outerWidth(true);

    jQuery('#' + this.sliderId + ' .flex-active-slide .captionWrapper .flex-caption').each(function (index) {
        offset = jQuery('#' + _that.sliderId + ' .flex-active-slide .captionWrapper').position().left;
        animateTo = sliderWidth - offset;
        jQuery(this).css('left', '0px').show();
        var that = this;
        if (!attr) {
            setTimeout(function () {
                jQuery(that).animate({
                    'left': animateTo
                }, _that.bigSliderDuration
                        );
            }, index * _that.bigSliderDuration);
            delay = (index + 1) * _that.bigSliderDuration;
        } else {
            jQuery(this).css('display', 'none');
        }
    });
    return delay;
}

/*
 * Slider Callback AFTER
 */
function apsl_sliderAfter(slider) {
    var _that = this;
    var wrap_class = jQuery('#' + this.sliderId);
    var attr = wrap_class.attr('data-autoplay');
    var active_slide = jQuery(wrap_class).find('.flex-active-slide');
    var active_height = jQuery(active_slide).find('img').height();
    var active_link = jQuery(active_slide).find('img').attr('data-link');
    if (active_link) {
        var activ_link_pars = urlParser.parse(active_link);
        var active_video_link = 'https://www.youtube.com/embed/' + activ_link_pars.id + '?rel=0&amp;autoplay=1';
        if (activ_link_pars.provider == 'vimeo') {
            active_video_link = 'http://player.vimeo.com/video/' + activ_link_pars.id + '?api=1&autoplay=1';
        }
    }
    var hover = jQuery('#' + this.sliderId).attr("data-hover");
    var carousel = jQuery('#' + this.sliderId).attr("data-carousel");
    var fade = wrap_class.attr('data-fade');
    var attr = wrap_class.attr('data-autoplay');
    var img_h = (jQuery('.flexslider').find('.slides > li').eq(slider.currentSlide).find('img').innerHeight()) ? jQuery('.flexslider').find('.slides > li').eq(slider.currentSlide).find('img').innerHeight() : "280";
    var box = jQuery('.flexslider').find('.slides > li').eq(slider.currentSlide);
    if (typeof attr !== typeof undefined && attr !== false) {
        if (typeof fade !== typeof undefined && fade !== false) {
            if (typeof active_link !== typeof undefined && active_link !== false) {
                slider.pause();
                jQuery(active_slide).find('img').hide();
                jQuery(active_slide).find('iframe').remove();
                jQuery(active_slide).append('<iframe width="100%" style="height:' + active_height + 'px" src="' + active_video_link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
            }
        }
    }
 

    jQuery('#' + this.sliderId + ' .flex-active-slide .captionWrapper .flex-caption').each(function (index) {

        offset = jQuery('#' + _that.sliderId + ' .flex-active-slide .captionWrapper').position().left + jQuery(this).outerWidth();
        animateTo = 0;
        jQuery(this).css('left', '-' + offset + 'px').show();
        var that = this;
        if (!attr) {
            setTimeout(function () {
                jQuery(that).animate({
                    'left': animateTo
                }, _that.bigSliderDuration * 0.45
                        );
            }, (index * _that.bigSliderDuration) * 0.45);
        } else {
            jQuery(this).css('display', 'none');
        }
    });

}
jQuery(document).ready(function () {

    jQuery(document).on('click', '.apargSlider .videoPlayImage', function () {

        var parent = jQuery(this).parent();
        var bigParent = jQuery(parent).parent();
        if (jQuery(bigParent).find('iframe').length > 0) {
            jQuery(bigParent).find('iframe').remove();
            jQuery(parent).siblings().find('img').fadeIn(500);
            jQuery(this).hide();
            jQuery(parent).find('.slide_n').hide();
            jQuery(this).parents().find('.flexslider .flex-control-paging li').show();
            var link = jQuery(this).parent().find('.slide_n').attr('data-link');
            if (link) {
                var attachment = urlParser.parse(link);
                link = 'https://www.youtube.com/embed/' + attachment.id + '?rel=0&amp;autoplay=1';
                if (attachment.provider == 'vimeo') {
                    link = 'http://player.vimeo.com/video/' + attachment.id + '?api=1&autoplay=1';
                }
            }

            jQuery(parent).append('<iframe width="100%" style="height:' + jQuery(this).siblings('.slide_n').height() + 'px" src="' + link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
            jQuery(parent).find('embed').removeAttr('title');
            jQuery(parent).find('img,.flex-caption, .videoPlayImage').hide();
        } else {
            var link = jQuery(parent).find('.slide_n').attr('data-link');
            if (link) {
                var attachment = urlParser.parse(link);
                link = 'https://www.youtube.com/embed/' + attachment.id + '?rel=0&amp;autoplay=1';
                if (attachment.provider == 'vimeo') {
                    link = 'http://player.vimeo.com/video/' + attachment.id + '?api=1&autoplay=1';
                }
            }

            jQuery(parent).append('<iframe width="100%" style="height:' + jQuery(this).siblings('.slide_n').height() + 'px" src="' + link + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="apargIframe"></iframe>');
            jQuery(parent).find('.slide_n,.flex-caption').hide();
            jQuery(parent).find('.videoPlayImage').hide();
        }
        ;
    });
});