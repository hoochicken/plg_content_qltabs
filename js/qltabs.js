/**
 * @package        plg_content_qltabs
 * @copyright      Copyright (C) 2022 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
jQuery(document).ready(function () {
    // TABS set first child active to be displayed
    jQuery('.qltabs_container > .qltabs_head').find('li:first').addClass('active');

    // TAB CONTENTS set first child active to be displayed
    jQuery('.qltabs_container > .qltabs').children('.qltab_content:first-child').addClass('active');
    jQuery('.qltabs_container.default > .qltabs').children('.qltab_content:first-child').fadeIn();
    jQuery('.qltabs_container.fadein > .qltabs').children('.qltab_content:first-child').fadeIn();
    jQuery('.qltabs_container.slidedown > .qltabs').children('.qltab_content:first-child').slideDown();
    jQuery('.qltabs_container.qltabs_accordeon > .qltabs').children('.qltab_content:first-child').slideDown();

    qlSetActiveTabl();

    jQuery('.qltabs_container.default > .qltabs_head .qltab_head').click(function (el) {
        let elementClicked = jQuery(el.currentTarget);

        // adjust table_head
        elementClicked.siblings('.qltab_head').removeClass('active');
        elementClicked.addClass('active');

        // adjust tabs with content = display tab clicked on
        let contentTabs = elementClicked.closest('.qltabs_container > .qltabs > .qltab_content');
        contentTabs.removeClass('active').css('display', 'none');
        let classs = jQuery(this).attr('id') + '_content';
        jQuery('#' + classs).addClass('active').css('display', 'block');
        return false;
    });

    // default => just display and hide by class 'active'
    jQuery('.qltabs_accordeon > .qltabs_head .qltab_head').click(function () {
        let strDisplay = jQuery(this).next('.qltab_content').css('display');
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

    jQuery('.qltabs_container.fadein.default > .qltabs_head .qltab_head').click(function () {
        jQuery(this).parent('div').next('div').children('div.qltab_content').css('display', 'none');
        jQuery(this).parent('div').next('div').children('div.qltab_content.active').fadeIn('slow');
        return false;
    });

    jQuery('.qltabs_container.slidedown.default > .qltabs_head .qltab_head').click(function () {
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
    let tabs = qlGetUrlParam('qltab');
    if ('' === tabs || false === tabs) {
        return;
    }

    let tabsToOpen = tabs.split(",");
    for (let i = 0; i < tabsToOpen.length; i++) {
        let idTabHeader = tabsToOpen[i];
        if (jQuery('#' + idTabHeader).qlExists()) {
            let tabsFamily = tabs.split("-");
            tabsFamily = tabsFamily[0];
            let idTabContainer = idTabHeader + '_content';

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
    let query = window.location.search.substring(1);
    let vars = query.split("&");
    for (let i = 0; i < vars.length; i++) {
        let getVar = vars[i].split("=");
        if (getVar[0] == variable) {
            return getVar[1];
        }
    }
    return (false);
}