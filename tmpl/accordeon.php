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
/** @var array $arrTabAttributes */
/** @var array $arr */
?>
<div id="qltab<?php echo $intCounter; ?>" class="qltabs_container qltabs_accordeon <?php echo $arrTabAttributes['class']; ?>" style="<?php echo $arrTabAttributes['style']; ?>">
    <?php foreach ($arr as $k => $v) : ?>
        <div class="section">
            <div role="button" tabindex="0" class="qltab<?php echo $intCounter; ?> qltab_head" id="<?php echo $v['id']; ?>"><a href="#"><?php echo $v['title']; ?></a></div>
            <div tabindex="-1" class="qltab_content" id="<?php echo $v['id']; ?>_content">
                <?php echo $v['content']; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
