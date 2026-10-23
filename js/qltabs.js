/**
 * @package        plg_content_qltabs
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel info@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

document.addEventListener('DOMContentLoaded', function () {

  /**
   * Show an element with a vanilla JS animation.
   */
  function qlShow(element, effect = 'none') {
    if (!element) return;

    element.style.display = 'block';

    if (effect === 'fade') {
      element.animate(
        [{opacity: 0}, {opacity: 1}],
        {duration: 400, easing: 'ease', fill: 'none'}
      );
      return;
    }

    if (effect === 'slide') {
      const height = element.scrollHeight;

      const animation = element.animate(
        [
          { height: '0px', opacity: 0, overflow: 'hidden' },
          { height: height + 'px', opacity: 1, overflow: 'hidden' }
        ],
        { duration: 400, easing: 'ease', fill: 'none' }
      );

      animation.onfinish = function () {
        element.style.removeProperty('height');
        element.style.removeProperty('overflow');
        element.style.removeProperty('opacity');
      };
      return;
    }

  }

  /**
   * Hide an element.
   */
  function qlHide(element) {
    if (!element) {
      return
    }
    element.style.display = 'none';
  }

  /**
   * Determine the animation for a tab container.
   */
  function qlGetEffect(container) {
    if (container.classList.contains('fadein')) return 'fade';
    if (container.classList.contains('slidedown')) return 'slide';
    return 'none';
  }

  /**
   * Activate a regular tab.
   */
  function qlActivateTab(header, animate = true) {
    const container = header.closest('.qltabs_container');
    if (!container) return;

    const tabs = container.querySelector(':scope > .qltabs');
    if (!tabs) return;

    const panel = document.getElementById(header.id + '_content');

    if (!panel || !tabs.contains(panel)) return;

    container.querySelectorAll(
      ':scope > .qltabs_head .qltab_head'
    ).forEach(function (item) {
      item.classList.toggle('active', item === header);
    });

    tabs.querySelectorAll(':scope > .qltab_content').forEach(function (item) {
      item.classList.toggle('active', item === panel);
      qlHide(item);
    });

    if (animate) {
      qlShow(panel, qlGetEffect(container));
    } else {
      panel.style.display = 'block';
    }
  }

  /**
   * Initialize regular tabs.
   */
  document.querySelectorAll('.qltabs_container').forEach(function (container) {
    // Accordions are handled separately.
    if (container.querySelector('.qltabs_accordeon')) return;

    const headers = container.querySelectorAll(
      ':scope > .qltabs_head .qltab_head'
    );

    const tabs = container.querySelector(':scope > .qltabs');

    if (!headers.length || !tabs) return;

    const panels = tabs.querySelectorAll(':scope > .qltab_content');

    // Initialize all panels as hidden.
    panels.forEach(function (panel) {
      panel.classList.remove('active');
      qlHide(panel);
    });

    headers.forEach(function (header) {
      header.classList.remove('active');

      header.addEventListener('click', function (event) {
        event.preventDefault();
        qlActivateTab(header);
      });
    });

    // First tab active by default.
    qlActivateTab(headers[0], false);
  });

  /**
   * Initialize accordion tabs.
   */
  document.querySelectorAll(
    '.qltabs_accordeon.multiton, .qltabs_accordeon.singleton'
  ).forEach(function (accordion) {

    const allowMultiple = accordion.classList.contains('multiton');

    accordion.querySelectorAll('.qltab_head').forEach(function (header) {
      header.addEventListener('click', function (event) {
        event.preventDefault();

        const panel = header.nextElementSibling;
        const section = header.closest('.section');

        if (!panel || !panel.classList.contains('qltab_content')) {
          return;
        }

        if (!section) return;

        const isOpen = getComputedStyle(panel).display !== 'none';

        if (!allowMultiple) {
          accordion.querySelectorAll('.section').forEach(function (item) {
            item.classList.remove('active');

            const content = item.querySelector('.qltab_content');
            if (content) qlHide(content);
          });
        }

        if (isOpen) {
          section.classList.remove('active');
          qlHide(panel);
        } else {
          section.classList.add('active');
          qlShow(panel, 'slide');
        }
      });
    });
  });

  // Open tabs specified by the URL.
  qlSetActiveTabl();

});


/**
 * Activate tabs specified by ?qltab=header-id
 *
 * Multiple header IDs can be comma-separated.
 */
function qlSetActiveTabl() {
  const tabs = qlGetUrlParam('qltab');

  if (!tabs) return false;

  tabs.split(',').forEach(function (id) {
    const header = document.getElementById(id.trim());

    if (!header) return;

    const accordion = header.closest('.qltabs_accordeon');

    if (!accordion) {
      qlActivateTablFromUrl(header);
      return
    }

    const panel = header.nextElementSibling;
    const section = header.closest('.section');

    if (!panel || !panel.classList.contains('qltab_content')) {
      return;
    }

    const allowMultiple = accordion.classList.contains('multiton');

    if (!allowMultiple) {
      accordion.querySelectorAll('.section').forEach(function (item) {
        item.classList.remove('active');

        const content = item.querySelector('.qltab_content');
        if (content) qlHide(content);
      });
    }

    panel.style.display = 'block';
    if (section) section.classList.add('active');

  });

  return false;
}


/**
 * Activate a regular tab from the URL.
 */
function qlActivateTablFromUrl(header) {
  const container = header.closest('.qltabs_container');

  if (!container) return;

  const tabs = container.querySelector(':scope > .qltabs');
  if (!tabs) return;

  const panel = document.getElementById(header.id + '_content');

  if (!panel || !tabs.contains(panel)) return;

  container.querySelectorAll(
    ':scope > .qltabs_head .qltab_head'
  ).forEach(function (item) {
    item.classList.toggle('active', item === header);
  });

  tabs.querySelectorAll(':scope > .qltab_content').forEach(function (item) {
    item.classList.toggle('active', item === panel);
    qlHide(item);
  });

  panel.style.display = 'block';
}

/**
 * Get a URL query parameter.
 * Returns the parameter value, or false if it is missing.
 */
function qlGetUrlParam(variable) {

  const url = window.location.hash.slice(1) || false
  if (false === url || url.length === 0) {
    return false
  }
  const urlAnchors = new URLSearchParams(url)
  if (false === url || url.length === 0) {
    return false
  }
  return urlAnchors.has(variable)
    ? urlAnchors.get(variable)
    : false;
}
