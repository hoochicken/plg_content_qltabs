<?php
/**
 * @package        mod_qltabs
 * @copyright    Copyright (C) 2017 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');

jimport('joomla.plugin.plugin');


class plgContentQltabs extends JPlugin
{

    protected $str_call_start = 'qltabs';
    protected $str_call_start2 = 'qltab';
    protected $str_call_end = '/qltabs';
    protected $states = array();
    protected $tabAttributes = array();
    public $params;

    /**
     * onContentPrepare :: some kind of controller of plugin
     * @param $context
     * @param $article
     * @param $params
     * @param int $page
     * @return bool
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ($context == 'com_finder.indexer') {
            return true;
        }
        if (strpos($article->text, '{' . $this->str_call_start2) === false OR strpos($article->text, '{' . $this->str_call_end . '}') === false) {
            return true;
        }
        $this->includeScripts();
        if (1 == $this->params->get('style',0)) {
            $this->getStylesHorizontal();
        }
        if (1 == $this->params->get('verticalStyle',0)) {
            $this->getStylesVertical();
        }
        $article->text = $this->clearTags($article->text);
        $article->text = $this->replaceStartTags($article->text);
    }

    /**
     * @param $text
     * @return mixed
     */
    function replaceStartTags($text)
    {
        $matches = $this->getMatches($text, $this->str_call_start2);
        if (count($matches) > 0 AND isset($matches[0])) {
            foreach ($matches[0] as $k => $v) {
                $this->getArrayReplaces($k, $v);
                $this->arr_replace[$k]['html'] = $this->getHtml($k, $this->arr_replace[$k], $this->tabAttributes[$k]);
                unset($arr);
                $text = str_replace($v, $this->arr_replace[$k]['html'], $text);
            }
        }
        return $text;
    }

    /**
     * @param $counter
     * @param $string
     * @return bool
     */
    function getArrayReplaces($counter, $string)
    {
        $this->arr_replace[$counter] = array();
        $this->tabAttributes[$counter] = array();
        $regex = '~{' . $this->str_call_start . '(.*?)}~s';
        preg_match($regex, $string, $matches);

        if (isset($matches[1])) {
            $this->getTagAttributes($counter, $matches[1]);
        } else {
            $this->getTagAttributes($counter);
        }
        $regex = '~{' . $this->str_call_start2 . ' title?=?"(.+?)"}(.+?)(?={(' . $this->str_call_start2 . '|' . $this->str_call_end . '))~s';
        //$regex='!{'.$this->start.'(.*?)}(.+?)(?={(' . $this->str_call_start2 . '|' . $this->str_call_end . '))!s';
        //echo '<pre>';print_r($this->tabAttributes);die;
        preg_match_all($regex, $string, $matches);
        if (!isset($matches[0])) {
            return false;
        }
        while (list($k, $v) = each($matches[0])) {
            $this->arr_replace[$counter][$k] = array();
            $this->arr_replace[$counter][$k]['title'] = $matches[1][$k];
            $this->arr_replace[$counter][$k]['content'] = $matches[2][$k];
            $this->arr_replace[$counter][$k]['id'] = 'qltab' . $counter . '-' . $k;
        }
        return $this->arr_replace;
    }

    /**
     * @param $counter
     * @param string $string
     */
    private function getTagAttributes($counter, $string = '')
    {
        //set default values
        $arrValue = array('class' => '', 'style' => '', 'id' => '','type'=>'',);

        $arr = array('class', 'id', 'style','type','title',);
        $strRegex = '/(' . implode('|', $arr) . ')="(.*?)"/';
        preg_match_all($strRegex, $string, $arrMatches);
        foreach ($arrMatches[1] as $k => $v) {
            $arrValue[$v] = trim($arrMatches[2][$k]);
        }
        if (false === strpos($arrValue['class'], 'horizontal') AND false === strpos($arrValue['class'], 'vertical')) {
            $arrValue['class'] .= ' ' . $this->params->get('defaultType', 'horizontal');
        }

        $numDefaultWidth = (int)$this->params->get('verticalWidthbuttons', 25);
        if (!preg_match('/width([0-9]{1,2})/i', $arrValue['class'], $match)) {
            $arrValue['class'] .= ' width' . $numDefaultWidth;
        }

        if (0 == preg_match('/(plop|fadein|slidedown)/', $arrValue['class'])) {
            if (false !== strpos($arrValue['class'], 'horizontal')) {
                $arrValue['class'] .= ' ' . $this->params->get('displayEffect', 'plop');
            } elseif (false !== strpos($arrValue['class'], 'vertical')) {
                $arrValue['class'] .= ' ' . $this->params->get('verticalDisplayEffect', 'plop');
            }
        }
        //print_r($arrValue);die;
        $this->tabAttributes[$counter] = $arrValue;
        return;
    }

    /**
     * @param $string
     * @return array
     */
    public function getMatches($string)
    {
        /**get matches to {qltabs}*/
        $strRegex = '~{' . $this->str_call_start2 . '(.*?)}(.+?){' . $this->str_call_end . '}~s';
        preg_match_all($strRegex, $string, $arrMatches1);
        foreach ($arrMatches1 as $k => $v) {
            $string = str_replace($v, '', $string);
        }
        //echo '<pre>';print_r($matches1);die;

        /**get matches to {qltab title="?"}*/
        $strRegex = '~{' . $this->str_call_start2 . ' title?=?"(.+?)"}(.+?){' . $this->str_call_end . '}~s';
        preg_match_all($strRegex, $string, $arrMatches2);
        /**merge arrays*/
        $arrMatches = [];
        $arrMatches[0] = array_merge($arrMatches1[0], $arrMatches2[0]);
        $arrMatches[1] = array_merge($arrMatches1[1], $arrMatches2[1]);
        return $arrMatches;
    }

    /**
     * method to get attributes
     * @param $string
     * @return array
     */
    function getAttributes($string)
    {
        $strSelector = implode('|', $this->arr_attributes);
        preg_match_all('~(' . $strSelector . ')="(.+?)"~s', $string, $arrMatches);
        $arrAttributes = array();
        if (is_array($arrMatches)) foreach ($arrMatches[0] as $k => $v) {
            if (isset($arrMatches[1][$k]) AND isset($arrMatches[2][$k])) $arrAttributes[$arrMatches[1][$k]] = $arrMatches[2][$k];
        }
        return $arrAttributes;
    }

    /**
     * @param $strStart
     * @param $strEnd
     * @param $strHaystack
     * @return array
     */
    function getMatchesInString($strStart, $strEnd, $strHaystack)
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
    function clearTags($str)
    {
        $str = str_replace('<p>{' . $this->str_call_end . '}', '{' . $this->str_call_end . '}', $str);
        $str = str_replace('{' . $this->str_call_end . '}</p>', '{' . $this->str_call_end . '}', $str);
        $str = str_replace('<p>{' . $this->str_call_start, '{' . $this->str_call_start, $str);
        $str = str_replace('<p>{' . $this->str_call_start2, '{' . $this->str_call_start2, $str);
        $strRegex = '!{' . $this->str_call_start . '\s(.*?)}</p>!';
        preg_match_all($strRegex, $str, $arrMatches, PREG_SET_ORDER);
        $strRegex = '!{' . $this->str_call_start2 . '\s(.*?)}</p>!';
        preg_match_all($strRegex, $str, $arrMatches, PREG_SET_ORDER);
        if (0 < count($arrMatches)) {
            foreach ($arrMatches as $k => $v) {
                $str = preg_replace('?' . $v[0] . '?', '{' . $this->str_call_start2 . $v[1] . '}', $str);
            }
        }
        return $str;
    }

    /**
     * @param $counter
     * @param $arr
     * @param $arrTabAttributes
     * @return string
     */
    function getHtml($counter, $arr, $arrTabAttributes)
    {
        $params = $this->params;
        $attributes = $this->states;
        ob_start();
        if('accordeon' === $arrTabAttributes['type'] || 'acco' === $arrTabAttributes['type']) {
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
    public function getStylesHorizontal()
    {
        $numBorderWidth = $this->params->get('borderwidth');
        $strBorderColor = $this->params->get('bordercolor');
        $strBorderType = $this->params->get('bordertype');
        $strFontColor = $this->params->get('fontcolor');
        $strFontColorInactive = $this->params->get('inactivefontcolor');
        $numOpacity = $this->params->get('backgroundopacity');
        $strBackgroundColor = $this->getBgColor($this->params->get('backgroundcolor'), $numOpacity);
        $strBackgroundColorInactive = $this->getBgColor($this->params->get('inactivebackgroundcolor'), $numOpacity);

        $arrStyle = array();
        $arrStyle[] = '.qltabs_container.horizontal .qltabs_head .qltab_head {margin-bottom:-' . $numBorderWidth . 'px;border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';background:' . $strBackgroundColorInactive . ';color:' . $strFontColorInactive . ';}';
        $arrStyle[] = '.qltabs_container.horizontal .qltabs_head .qltab_head.active {border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';border-bottom:' . $numBorderWidth . 'px solid ' . $strBackgroundColor . ';background:' . $strBackgroundColor . ';color:' . $strFontColor . '}';
        $arrStyle[] = '.qltabs_container.horizontal .qltab_content {background:' . $strBackgroundColor . ';}';
        $arrStyle[] = '.qltabs_container.horizontal .qltabs {background:' . $strBackgroundColor . ';border:' . $numBorderWidth . 'px ' . $strBorderType . ' ' . $strBorderColor . ';}';
        $arrStyle[] = '.qltabs_container.horizontal .qltabs .qltab_content {display:none;background:' . $strBackgroundColor . ';color:' . $strFontColor . ';}';
        //$style[]='.qltabs_container.horizontal.plop .qltabs .qltab_content.active {display:block;}';
        $strStyle = implode("\n", $arrStyle);
        JFactory::getDocument()->addStyleDeclaration($strStyle);
    }

    /**
     * method to get matches according to search string
     * @param $text string haystack
     * @param $searchString string needle, string to be searched
     */
    public function getStylesVertical()
    {
        $style = array();
        $borderWidth = $this->params->get('verticalBorderwidth');
        $borderColor = $this->params->get('verticalBordercolor');
        $borderType = $this->params->get('verticalBordertype');
        $fontColor = $this->params->get('verticalFontcolor');
        $opacity = $this->params->get('verticalBackgroundopacity');
        $backgroundColor = $this->getBgColor($this->params->get('verticalBackgroundcolor'), $opacity);
        $backgroundColorInactive = $this->getBgColor($this->params->get('verticalInactivebackgroundcolor'), $opacity);
        $fontColorInactive = $this->params->get('verticalInactivefontcolor');

        $style[] = '.qltabs_container.vertical .qltabs_head {Xwidth:' . (int)$this->params->get('verticalWidthbuttons', 25) . '%;}';
        $style[] = '.qltabs_container.vertical .qltabs_head .qltab_head {border-bottom:' . $borderWidth . 'px ' . $borderType . ' ' . $borderColor . ';background:' . $backgroundColorInactive . ';color:' . $fontColorInactive . ';}';
        $style[] = '.qltabs_container.vertical .qltabs_head .qltab_head:last-child {border-bottom:0;}';
        $style[] = '.qltabs_container.vertical .qltabs_head .qltab_head.active {background:' . $backgroundColor . ';color:' . $fontColor . ';}';
        $style[] = '.qltabs_container.vertical .qltabs {Xwidth:' . (100 - (int)$this->params->get('verticalWidthbuttons', 25)) . '%;background:' . $backgroundColor . ';}';
        $style[] = '.qltabs_container.vertical .qltabs .qltab_content {display:none;background:' . $backgroundColor . ';color:' . $fontColor . ';}';
        //$style[]='.qltabs_container.vertical.plop .qltabs .qltab_content.active {display:block;}';
        JFactory::getDocument()->addStyleDeclaration(implode("\n", $style));
    }

    /*
     * method to include scripts, e. g. jQuery
     */
    function includeScripts()
    {
        if (1 == $this->params->get('jquery')) {
            JHtml::_('jquery.framework');
        }
        JHtml::_('script', JUri::root() . 'media/plg_content_qltabs/js/qltabs.js');
        JHtml::_('stylesheet', JUri::root() . 'media/plg_content_qltabs/css/qltabs.css');
    }

    function getBgColor($bg, $opacity)
    {
        include_once __DIR__ . '/php/clsPlgContentQltabsColor.php';
        $obj_color = new clsPlgContentQltabsColor;
        $arr = $obj_color->html2rgb($bg);
        $opacity = $opacity / 100;
        return 'rgba(' . implode(',', $arr) . ',' . $opacity . ')';
    }
}
