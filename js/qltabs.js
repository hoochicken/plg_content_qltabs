/**
 * @package        plg_content_qltabs
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
jQuery(document).ready(function () {
    jQuery('.qltabs_container .qltabs_head').children('div:first').addClass('active');
    jQuery('.qltabs_container .qltabs').children('div:first').addClass('active');
    jQuery('.qltabs_container.fadein .qltabs').children('div:first').css('display', 'block');
    jQuery('.qltabs_container.slidedown .qltabs').children('div:first-child').css('display', 'block');
    jQuery('.qltabs_accordeon').find('.qltab_content').css('display', 'none');

    qlSetActiveTabl();

    jQuery('.qltabs_container.default .qltab_head').click(function () {
        var classs = jQuery(this).attr('id') + '_content';
        jQuery(this).parent('div').next('div').children('div.qltab_content').removeClass('active');
        jQuery('#' + classs).attr('class', 'qltab_content active');
        jQuery(this).parent('div').children('.qltab_head').removeClass('active');
        jQuery(this).attr('class', jQuery(this).attr('class') + ' active');
        return false;
    });

    jQuery('.qltabs_accordeon .qltab_head').click(function () {
        //jQuery(this).parent('div').siblings('div').children('div').removeClass('active');
        var strDisplay = jQuery(this).next('.qltab_content').css('display');
        if('block' === strDisplay) {
            jQuery(this).removeClass('active');
            jQuery(this).next('.qltab_content').removeClass('active');
        } else {
            jQuery(this).addClass('active');
            jQuery(this).next('.qltab_content').addClass('active');
        }
        jQuery(this).next('.qltab_content').slideToggle();
        return false;
    });

    jQuery('.qltabs_container.fadein.default .qltab_head').click(function () {
        jQuery(this).parent('div').next('div').children('div.qltab_content').css('display', 'none');
        jQuery(this).parent('div').next('div').children('div.qltab_content.active').fadeIn('slow');
        return false;
    });
    jQuery('.qltabs_container.slidedown.default .qltab_head').click(function () {
        jQuery(this).parent('div').next('div').children('div.qltab_content').slideUp();
        jQuery(this).parent('div').next('div').children('div.qltab_content.active').slideDown();
        return false;
    });
});

/**
 * qlSetActiveTabl()
 * function to set a tab into active if set so by get-params in url
 */
function qlSetActiveTabl() {
    var tabs = qlGetUrlParam('qltab');
    if ('' == tabs) {
        return;
    }

    var tabsToOpen = tabs.split(",");
    for (var i = 0; i < tabsToOpen.length; i++) {
        var idTabHeader = tabsToOpen[i];
        if (jQuery('#' + idTabHeader).qlExists()) {
            var tabsFamily = tabs.split("-");
            tabsFamily = tabsFamily[0];
            var idTabContainer = idTabHeader + '_content';

            jQuery('#' + idTabHeader).parent('div').children('.qltab_head').removeClass('active');
            jQuery('#' + idTabHeader).attr('class', jQuery('#' + idTabHeader).attr('class') + ' active');
            jQuery('.' + tabsFamily).children('.qltab_content').hide(0);
            jQuery('.' + tabsFamily).children('.qltab_content').removeClass('active');
            jQuery('#' + idTabContainer).show(0);
            jQuery('#' + idTabContainer).attr('class', jQuery('#' + idTabContainer).attr('class') + ' active');
        }
    }
    return (false);
}

/**
 * function to check, whether an html exists or not
 */
jQuery.fn.qlExists = function () {
    return this.length > 0;
}

/**
 * getUrlParam
 * @param variable whose value is to be returned
 * @return {*}
 */
function qlGetUrlParam(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var getVar = vars[i].split("=");
        if (getVar[0] == variable) {
            return getVar[1];
        }
    }
    return (false);
}