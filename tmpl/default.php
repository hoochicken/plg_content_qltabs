<?php
/**
 * @package        mod_qltabs
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');
/** @var int $intCounter */
/** @var array $arrTabAttributes */
/** @var array $arr */
?>
<div id="qltab<?php echo $intCounter; ?>" class="qltabs_container default <?php echo $arrTabAttributes['class']; ?>"
     style="<?php echo $arrTabAttributes['style']; ?>">
    <nav class="qltabs_head" role="navigation">
        <ul>
        <?php foreach ($arr as $k => $v) : ?>
            <li class="qltab<?php echo $intCounter; ?> qltab_head"
                id="<?php echo $v['id']; ?>">
                <a href="#" role="button" tabindex="0"><?php echo $v['title']; ?></a></li>
        <?php endforeach; ?>
        </ul>
    </nav>
    <div class="qltabs">
        <?php foreach ($arr as $k => $v) : ?>
            <div class="qltab_content" id="<?php echo $v['id']; ?>_content">
                <?php echo $v['content']; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
