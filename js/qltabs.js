/**
 * @package        plg_content_qltabs
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel info@ql.de
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
    let contentTabs = elementClicked.closest('.qltabs_container').children('.qltabs').children('.qltab_content');
    contentTabs.removeClass('active').hide();
    let classs = jQuery(this).attr('id') + '_content';
    elementClicked.closest('.qltabs_container').children('.qltabs').children('#' + classs).addClass('active').show();
    return false;
  });

  jQuery('.qltabs_accordeon.multiton .qltab_head').click(function () {
    let display = jQuery(this).next('.qltab_content').css('display');
    if ('block' === display) {
      jQuery(this).next('.qltab_content').slideUp();
      jQuery(this).closest('.section').removeClass('active');
    } else {
      jQuery(this).next('.qltab_content').slideDown();
      jQuery(this).closest('.section').addClass('active');
    }
    return false;
  });

  jQuery('.qltabs_accordeon.singleton .qltab_head').click(function () {
    let display = jQuery(this).next('.qltab_content').css('display');
    jQuery(this).closest('.qltabs_accordeon.singleton').find('.section').removeClass('active');
    jQuery(this).closest('.qltabs_accordeon').find('.qltab_content').slideUp();
    if ('block' === display) {
      jQuery(this).next('.qltab_content').slideUp();
      jQuery(this).closest('.section').removeClass('active');
    } else {
      jQuery(this).next('.qltab_content').slideDown();
      jQuery(this).closest('.section').addClass('active');
    }
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
    if (getVar[0] === variable) {
      return getVar[1];
    }
  }
  return (false);
}