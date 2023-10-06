<?php
/**
 * @package        mod_qltabs
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');
/** @var int $intCounter */
/** @var array $tabAttributes */
/** @var array $attributes */
/** @var array $arr */
/** @var \Joomla\Registry\Registry $params */
?>
<div id="qltab<?= $intCounter; ?>" class="qltabs_container qltabs_accordeon <?= $tabAttributes['class']; ?> <?= $params->get('accordeonSingleton', 0) ? 'singleton' : 'multiton'; ?>" style="<?= $tabAttributes['style']; ?>">
    <?php foreach ($arr as $k => $v) : ?>
        <div class="section">
            <div class="qltab<?= $intCounter; ?> qltab_head" id="<?= $v['id']; ?>"><a href="#" tabindex="0" role="button" aria-label="<?= preg_replace('([^0-9a-zA-Z-_ ]*)', '', $v['title']); ?>" class="inner"><?= $v['title']; ?></a></div>
            <div tabindex="-1" class="qltab_content" id="<?= $v['id']; ?>_content">
                <?= $v['content']; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
