<?php
/**
 * @package        mod_qltabs
 * @copyright    Copyright (C) 2017 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');
?>
<div id="qltab<?php echo $counter; ?>" class="qltabs_container default<?php echo $arrTabAttributes['class']; ?>"
     style="<?php echo $arrTabAttributes['style']; ?>">
    <div class="qltabs_head">
        <?php foreach ($arr as $k => $v) : ?>
            <div class="qltab<?php echo $counter; ?> qltab_head"
                 id="<?php echo $v['id']; ?>"><?php echo $v['title']; ?></div>
        <?php endforeach; ?>
    </div>
    <div class="qltabs">
        <?php foreach ($arr as $k => $v) : ?>
            <div class="qltab_content" id="<?php echo $v['id']; ?>_content">
                <?php echo $v['content']; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
