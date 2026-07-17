<?php
declare(strict_types=1);

/**
 * @package     plg_content_qltabs
 * @copyright   Copyright (C) 2026 Mareike Riegel
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') || die;

class clsPlgContentQltabsColor
{
    public function html2rgb(string $strColor = ''): array
    {
        // strip '#' in beginning of string given
        if ('#' === $strColor[0]) {
            $strColor = substr($strColor, 1);
        }

        //if 6 long, list to strings
        if (6 === strlen($strColor)) {
            list($strR, $strG, $strB) = [
                $strColor[0] . $strColor[1],
                $strColor[2] . $strColor[3],
                $strColor[4] . $strColor[5]
            ];
        }
        //if 3 long, list to strings
         elseif (3 === strlen($strColor)) {
            list($strR, $strG, $strB) = [
                $strColor[0] . $strColor[0],
                $strColor[1] . $strColor[1],
                $strColor[2] . $strColor[2]
            ];
        }
        //use default
        else {
            //set ffffff as default
            $strR = 'ff';
            $strG = 'ff';
            $strB = 'ff';
        }

        //turn hex into dec
        $intR = hexdec($strR);
        $intG = hexdec($strG);
        $intB = hexdec($strB);

        //create return array
        $arrRgb = [$intR, $intG, $intB];

        //return result
        return $arrRgb;
    }
}
