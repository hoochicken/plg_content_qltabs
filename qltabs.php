<?php
/**
 * @package        mod_qltabs
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');

jimport('joomla.plugin.plugin');


class plgContentQltabs extends JPlugin
{

    protected $strCallStart = 'qltabs';
    protected $strCallStart2 = 'qltab';
    protected $strCallEnd = '/qltabs';
    protected $arrStates = [];
    protected $arrTabAttributes = [];
    public $objParams;
    private $arrReplace = [];
    private $boolDebug = false;

    /**
     * onContentPrepare :: some kind of controller of plugin
     * @param $strContext
     * @param $objArticle
     * @param $objParams
     * @param int $numPage
     * @return bool
     * @throws Exception
     */
    public function onContentPrepare($strContext, &$objArticle, &$objParams, $numPage = 0)
    {
        //if search => ignore
        if ('com_finder.indexer' === $strContext) {
            return true;
        }
        $this->objParams = $this->params;

        $input = JFactory::getApplication()->input;
        $this->boolDebug = $input->getBool('ql_content_debug', false);

        // check session if styles already loaded
        $boolAlreadyLoadedStyles = defined('qltabs_styles');
        // check session if styles already loaded
        if (!$boolAlreadyLoadedStyles) {
            if (2 == $this->objParams->get('qltabsjsactive', 1)) {
                $this->includeScripts();
                define('qltabs_styles', true);
            }
            //include scripts
            if (1 == $this->objParams->get('style', 0)) {
                $this->getStylesHorizontal();
            }

            //include vertical styles
            if (1 == $this->objParams->get('verticalStyle', 0)) {
                $this->getStylesVertical();
            }
        }

        //if no plg tag in article => ignore
        if (false === strpos($objArticle->text, '{' . $this->strCallStart2) || false === strpos($objArticle->text, '{' . $this->strCallEnd . '}')) {
            return true;
        }

        // check session if styles already loaded
        if (!$boolAlreadyLoadedStyles) {
            $this->includeScripts();
            define('qltabs_styles', true);
        }

        //clear tags, tries to avoid code like <p><div> etc.
        $objArticle->text = $this->clearTags($objArticle->text);

        //replace tags
        $objArticle->text = $this->replaceStartTags($objArticle->text);
    }

    /**
     * replaces placeholder tags {qltabs ...} with actual html code
     * @param $strText
     * @return mixed
     * @internal param $text
     */
    private function replaceStartTags($strText)
    {
        //get matches
        $arrMatches = $this->getMatches($strText);

        //if no matches found (can't be, but just in case ...)
        if (0 === count($arrMatches) || !isset($arrMatches[0])) {
            return $strText;
        }

        //iterate through matches
        foreach ($arrMatches[0] as $numKey => $arrValue) {
            //get replacement array (written to class variable)
            $this->getArrayReplaces($numKey, $arrValue);

            //get html code
            $this->arrReplace[$numKey]['html'] = $this->getHtml($numKey, $this->arrReplace[$numKey], $this->arrTabAttributes[$numKey]);
            $strText = str_replace($arrValue, $this->arrReplace[$numKey]['html'], $strText);
        }
        //return text
        return $strText;
    }

    /**
     * @param $intCounter
     * @param $string
     * @return bool
     */
    private function getArrayReplaces($intCounter, $string)
    {
        $this->arrReplace[$intCounter] = [];
        $this->arrTabAttributes[$intCounter] = [];
        $strRegex = '~{' . $this->strCallStart . '(.*?)}~s';
        preg_match($strRegex, $string, $arrMatches);
        if (isset($arrMatches[1])) {
            $this->getTagAttributes($intCounter, $arrMatches[1]);
        } else {
            $this->getTagAttributes($intCounter);
        }
        $strRegex = '~{' . $this->strCallStart2 . ' title?=?"(.+?)"}(.+?)(?={(' . $this->strCallStart2 . '|' . $this->strCallEnd . '))~s';
        preg_match_all($strRegex, $string, $arrMatches);
        if (!isset($arrMatches[0])) {
            return false;
        }

        $strIdentifier = $intCounter;
        if (1 == $this->objParams->get('uniqueId', 0)) {
            $strIdentifier .= '-' . uniqid();
        }

        foreach ($arrMatches[0] as $k => $v) {
            $this->arrReplace[$intCounter][$k] = [];
            $this->arrReplace[$intCounter][$k]['title'] = $arrMatches[1][$k];
            $this->arrReplace[$intCounter][$k]['content'] = $arrMatches[2][$k];
            $this->arrReplace[$intCounter][$k]['id'] = 'qltab' . $strIdentifier . '-' . $k;
        }
        return $this->arrReplace;
    }

    /**
     * @param $intCounter
     * @param string $string
     */
    private function getTagAttributes($intCounter, $string = '')
    {
        //set default values
        $arrValue = ['class' => '', 'style' => '', 'id' => '', 'type' => '',];

        $arr = ['class', 'id', 'style', 'type', 'title',];
        $strRegex = '/(' . implode('|', $arr) . ')="(.*?)"/';
        preg_match_all($strRegex, $string, $arrMatches);
        foreach ($arrMatches[1] as $k => $v) {
            $arrValue[$v] = trim($arrMatches[2][$k]);
        }
        if (false === strpos($arrValue['class'], 'horizontal') && false === strpos($arrValue['class'], 'vertical')) {
            $arrValue['class'] .= ' ' . $this->objParams->get('defaultType', 'horizontal');
        }
        if (false && 'vertical' === $this->objParams->get('defaultType', 'horizontal')) {
            $numDefaultWidth = (int)$this->objParams->get('verticalWidthbuttons', 25);
            if (!preg_match('/width([0-9]{1,2})/i', $arrValue['class'], $match)) {
                $arrValue['class'] .= ' qltabsWidth' . $numDefaultWidth;
            }
        }
        if (0 == preg_match('/(plop|fadein|slidedown)/', $arrValue['class'])) {
            if (false !== strpos($arrValue['class'], 'horizontal')) {
                $arrValue['class'] .= ' ' . $this->objParams->get('displayEffect', 'plop');
            } elseif (false !== strpos($arrValue['class'], 'vertical')) {
                $arrValue['class'] .= ' ' . $this->objParams->get('verticalDisplayEffect', 'plop');
            }
        }
        //print_r($arrValue);die;
        $this->arrTabAttributes[$intCounter] = $arrValue;
        return;
    }

    /**
     * @param $string
     * @return array
     */
    private function getMatches($string)
    {
        //get matches to {qltabs}
        $strRegex = '~{' . $this->strCallStart . '(.*?)}(.+?){' . $this->strCallEnd . '}~s';
        preg_match_all($strRegex, $string, $arrMatches);
        return $arrMatches;
    }

    /**
     * method to get attributes
     * @param $string
     * @return array
     */
    private function getAttributes($string)
    {
        $strSelector = implode('|', $this->arrAttributes);
        preg_match_all('~(' . $strSelector . ')="(.+?)"~s', $string, $arrMatches);
        $arrAttributes = [];
        if (is_array($arrMatches)) {
            foreach ($arrMatches[0] as $k => $v) {
                if (isset($arrMatches[1][$k]) && isset($arrMatches[2][$k])) {
                    $arrAttributes[$arrMatches[1][$k]] = $arrMatches[2][$k];
                }
            }
        }
        return $arrAttributes;
    }

    /**
     * @param $strStart
     * @param $strEnd
     * @param $strHaystack
     * @return array
     */
    private function getMatchesInString($strStart, $strEnd, $strHaystack)
    {
        $needle = '~{' . $strStart . '(.*?)' . $strEnd . '}~s';
        preg_match_all($needle, $strHaystack, $arrMatches);
        return $arrMatches;
    }


    /**
     * method to clear tags
     * @param $str
     * @return mixed
     */
    private function clearTags($str)
    {
        $str = str_replace('<p>{' . $this->strCallEnd . '}', '{' . $this->strCallEnd . '}<p>', $str);
        $str = str_replace('{' . $this->strCallEnd . '}', '{' . $this->strCallEnd . '}', $str);
        $str = preg_replace('!<p>{' . $this->strCallStart . '}</p>!', '{' . $this->strCallStart . '}', $str);
        $str = preg_replace('!<p>{' . $this->strCallStart2 . '\s([^}]*?)}</p>!', '{' . $this->strCallStart2 . '}', $str);
        $str = preg_replace('!<p>{' . $this->strCallStart2 . '\s([^}]*?)}!', '{' . $this->strCallStart2 . ' $1}<p>', $str);
        $str = preg_replace('!<p>{' . $this->strCallStart2 . '\s([^}]*?)}!', '{' . $this->strCallStart2 . ' $1}<p>', $str);
        $this->debugPrintText($str);
        $str = preg_replace('!<p>{' . $this->strCallStart . '}{' . $this->strCallStart2 . '\s([^}]*?)}!', '{' . $this->strCallStart . '}{' . $this->strCallStart2 . ' $1}<p>', $str);
        $this->debugPrintText($str);
        return $str;
    }

    /**
     * @param $str
     */
    private function debugPrintText($str)
    {
        if (!$this->boolDebug) {
            return;
        }
        echo '<pre>' . htmlspecialchars($str) . '</pre>';
    }

    /**
     * @param $intCounter
     * @param $arr
     * @param $arrTabAttributes
     * @return string
     */
    private function getHtml($intCounter, $arr, $arrTabAttributes)
    {
        $objParams = $this->objParams;
        $arrAttributes = $this->arrStates;
        ob_start();
        if (false !== strpos($arrTabAttributes['class'], 'accordeon')) {
            $strLayoutFile = 'accordeon';
        } else {
            $strLayoutFile = 'default';
        }
        $strPathLayout = JPluginHelper::getLayoutPath('content', 'qltabs', $strLayoutFile);
        include $strPathLayout;
        $strHtml = ob_get_contents();
        ob_end_clean();
        return $strHtml;
    }

    /**
     * method to get matches according to search string
     * @internal param string $text haystack
     * @internal param string $searchString needle, string to be searched
     */
    private function getStylesHorizontal()
    {
        $numBorderWidth = $this->objParams->get('borderwidth');
        $strBorderColor = $this->objParams->get('bordercolor');
        $strBorderType = $this->objParams->get('bordertype');
        $strFontColor = $this->objParams->get('fontcolor');
        $strFontColorInactive = $this->objParams->get('inactivefontcolor');
        $numOpacity = $this->objParams->get('backgroundopacity');
        $strBackgroundColor = $this->getBgColor($this->objParams->get('backgroundcolor'), $numOpacity);
        $strBackgroundColorInactive = $this->getBgColor($this->objParams->get('inactivebackgroundcolor'), $numOpacity);

        $arrStyle = [];
        $arrStyle[] = '.qltabs_container.horizontal > .qltabs_head > .qltab_head {margin-bottom:-' . $numBorderWidth . 'px;border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';background:' . $strBackgroundColorInactive . ';color:' . $strFontColorInactive . ';}';
        $arrStyle[] = '.qltabs_container.horizontal > .qltabs_head > .qltab_head.active {border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';border-bottom:' . $numBorderWidth . 'px solid ' . $strBackgroundColor . ';background:' . $strBackgroundColor . ';color:' . $strFontColor . '}';
        $arrStyle[] = '.qltabs_container.horizontal > .qltab_content {background:' . $strBackgroundColor . ';}';
        $arrStyle[] = '.qltabs_container.horizontal > .qltabs {background:' . $strBackgroundColor . ';border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';}';
        $arrStyle[] = '.qltabs_container.horizontal > .qltabs > .qltab_content {display:none;background:' . $strBackgroundColor . ';color:' . $strFontColor . ';}';
        //$style[]='.qltabs_container.horizontal.plop .qltabs .qltab_content.active {display:block;}';
        $strStyle = implode("\n", $arrStyle);
        JFactory::getDocument()->addStyleDeclaration($strStyle);
    }

    /**
     * method to get matches according to search string
     * @internal param string $text haystack
     * @internal param string $searchString needle, string to be searched
     */
    private function getStylesVertical()
    {
        $style = [];
        $borderWidth = $this->objParams->get('verticalBorderwidth');
        $borderColor = $this->objParams->get('verticalBordercolor');
        $borderType = $this->objParams->get('verticalBordertype');
        $fontColor = $this->objParams->get('verticalFontcolor');
        $opacity = $this->objParams->get('verticalBackgroundopacity');
        $backgroundColor = $this->getBgColor($this->objParams->get('verticalBackgroundcolor'), $opacity);
        $backgroundColorInactive = $this->getBgColor($this->objParams->get('verticalInactivebackgroundcolor'), $opacity);
        $fontColorInactive = $this->objParams->get('verticalInactivefontcolor');

        $style[] = '.qltabs_container.vertical > .qltabs_head > {Xwidth:' . (int)$this->objParams->get('verticalWidthbuttons', 25) . '%;}';
        $style[] = '.qltabs_container.vertical > .qltabs_head > .qltab_head {border-bottom:' . $borderWidth . 'px ' . $borderType . ' ' . $borderColor . ';background:' . $backgroundColorInactive . ';color:' . $fontColorInactive . ';}';
        $style[] = '.qltabs_container.vertical > .qltabs_head > .qltab_head:last-child {border-bottom:0;}';
        $style[] = '.qltabs_container.vertical > .qltabs_head > .qltab_head.active {background:' . $backgroundColor . ';color:' . $fontColor . ';}';
        $style[] = '.qltabs_container.vertical > .qltabs {Xwidth:' . (100 - (int)$this->objParams->get('verticalWidthbuttons', 25)) . '%;background:' . $backgroundColor . ';}';
        $style[] = '.qltabs_container.vertical > .qltabs > .qltab_content {display:none;background:' . $backgroundColor . ';color:' . $fontColor . ';}';
        //$style[]='.qltabs_container.vertical.plop .qltabs .qltab_content.active {display:block;}';
        JFactory::getDocument()->addStyleDeclaration(implode("\n", $style));
    }

    /**
     *
     */
    private function includeScripts()
    {
        if (1 == $this->objParams->get('jquery')) {
            JHtml::_('jquery.framework');
        }
        JHtml::_('script', JUri::root() . 'media/plg_content_qltabs/js/qltabs.js');
        JHtml::_('stylesheet', JUri::root() . 'media/plg_content_qltabs/css/qltabs.css');
    }

    /**
     * @param $bg
     * @param $opacity
     * @return string
     */
    private function getBgColor($bg, $opacity)
    {
        include_once __DIR__ . '/php/clsPlgContentQltabsColor.php';
        $objColor = new clsPlgContentQltabsColor;
        $arr = $objColor->html2rgb($bg);
        $numOpacity = $opacity / 100;
        return 'rgba(' . implode(',', $arr) . ',' . $numOpacity . ')';
    }
}