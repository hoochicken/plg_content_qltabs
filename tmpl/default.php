<?php
/**
 * @package        mod_qltabs
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

defined('_JEXEC') || die ('Restricted Access');

/** @var int $intCounter */
/** @var array $tabAttributes */
/** @var array $tabsData */
/** @var array $attributes */
/** @var Registry $params */
?>
<div id="qltab<?= $intCounter; ?>" class="qltabs_container default <?= $tabAttributes['class']; ?>"
     style="<?= $tabAttributes['style']; ?>">
    <nav class="qltabs_head" role="navigation">
        <ul>
        <?php foreach ($tabsData as $k => $v) : ?>
            <li class="qltab<?= $intCounter; ?> qltab_head" id="<?= $v['id']; ?>">
                <a href="#" class="inner" role="button" aria-label="<?= preg_replace('([^0-9a-zA-Z-_ ]*)', '',$v['title']); ?>"><?= $v['title']; ?></a>
            </li>
        <?php endforeach; ?>
        </ul>
    </nav>
    <div class="qltabs">
        <?php foreach ($tabsData as $v) : ?>
            <div class="qltab_content" id="<?= $v['id']; ?>_content">
                <?= $v['content']; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
