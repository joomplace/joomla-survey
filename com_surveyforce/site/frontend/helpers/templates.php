<?php
/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Surveyforce Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Template class for the Surveyforce Deluxe Component
 */
class SurveyforceTemplates {

    public function __construct($template_name) {

        if (file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_surveyforce' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template_name . DIRECTORY_SEPARATOR . 'template.php')) {
            require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_surveyforce' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template_name . DIRECTORY_SEPARATOR . 'template.php');
        } else {
            require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_surveyforce' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'surveyforce_standart' . DIRECTORY_SEPARATOR . 'template.php');
        }

        return true;
    }

    public static function processTemplate($template, $sf_config, $survey, $show_description) {

        $template = str_replace('{SURVEY_NAME}', $survey->sf_name, $template);

        if ($survey->sf_image) {
            $template = preg_replace('/\{BACKGROUND_IMAGE\}/', "background: url(images/com_surveyforce/" . $survey->sf_image . ");", $template, 1);
        }
        $template = preg_replace('/\{PROGRESS_BAR\}/', SurveyforceTemplates::get_progress_bar($sf_config), $template, 1);
        $template = preg_replace('/\{ERROR_MESSAGE_TOP\}/', SurveyforceTemplates::get_error_message_top($sf_config), $template, 1);
        $template = preg_replace('/\{ERROR_MESSAGE_BOTTOM\}/', SurveyforceTemplates::get_error_message_bottom($sf_config), $template, 1);

        $template = str_replace('{SURVEY_BODY}', SurveyforceTemplates::get_container($survey->sf_descr, $show_description), $template);

        $count = substr_count($template, '{START_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{START_BUTTON\}/', '<span class="start_bt_container" id="start_' . $i . '">' . surveyforce_template_class::SF_GetStartButton() . '</span>', $template, 1);
        }

        $count = substr_count($template, '{PREV_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{PREV_BUTTON\}/', SurveyforceTemplates::get_button('prev', $i), $template, 1);
        }

        $count = substr_count($template, '{NEXT_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{NEXT_BUTTON\}/', SurveyforceTemplates::get_button('next', $i), $template, 1);
        }

        $count = substr_count($template, '{FINISH_BUTTON}');
        for ($i = 0; $i < $count; $i++) {
            $template = preg_replace('/\{FINISH_BUTTON\}/', SurveyforceTemplates::get_button('finish', $i), $template, 1);
        }

        $regex = '/{.*?}/i';
        $template = preg_replace($regex, '', $template);

        return $template;
    }

    public static function get_progress_bar($sf_config) {

        $tag = JFactory::getLanguage()->getTag();
        $lang = JFactory::getLanguage();
        $lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

        $tag = JFactory::getLanguage()->getTag();
        $lang = JFactory::getLanguage();
        $lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

        $str = '<div id="progress" style="display:none">
					<div style="border: 1px solid ' . $sf_config->get('color_border') . '; width:100%; background-color: ' . $sf_config->get('color_uncompleted') . '; height:15px;text-align:left;">
						<div id="progress_bar" style="text-align:left !important; width:0%; background-color: ' . $sf_config->get('color_completed') . '; color: ' . $sf_config->get('color_text') . '; height:15px;">&nbsp;</div>
					</div>
					<div id="progress_bar_txt"  style="float: center; position:relative; top:-16px;color: ' . $sf_config->get('color_text') . '; ">' . JText::_('COM_SURVEYFORCE_PROGRESS') . ' 0%</div>
				</div>';

        return $str;
    }

    public static function get_error_message_top() {
        $str = '<span id="error_messagebox" style="visibility:hidden;">Error text here</span>';

        return $str;
    }

    public static function get_error_message_bottom() {
        $str = '<span id="error_messagebox2" style="visibility:hidden;">Error text here</span>';

        return $str;
    }

    public static function get_container($text, $show_description = 1) {
        $str = ' <div id="survey_container" ><div id="start_div">';
        if (!$show_description) {
            $str .= '<script language="javascript" type="text/javascript">sf_StartSurveyOn();</script>';
        } else {
            $str .= $text;
        }
        $str .= '<br/></div></div>';

        return $str;
    }

    public static function get_button($button, $i = 0) {
        $str = '<span class="' . $button . '_bt_container" id="' . $button . '_' . $i . '" style="display:none;"></span>';
        return $str;
    }

    public static function Survey_blocked($sf_config = false, $message = '') {

        $tag = JFactory::getLanguage()->getTag();
        $lang = JFactory::getLanguage();
        $lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);
        
		$livePath = JUri::root();
		if ( !$sf_config )
			$sf_config = JComponentHelper::getParams('com_surveyforce');

		$COM_SURVEYFORCE_NOT_AVAIL = JText::_('COM_SURVEYFORCE_NOT_AVAIL'.strtoupper($message));
$blocked_html= <<<HTML
		
        <script language="JavaScript" src="{$livePath}components/com_surveyforce/assets/js/bits_message.js" type="text/javascript"></script>

        <script language="JavaScript" src="{$livePath}components/com_surveyforce/assets/js/pagination.js" type="text/javascript"></script>
        <script language="JavaScript" type="text/javascript">
            <!--//--><![CDATA[//><!--
                var mes_not_avail = '{$COM_SURVEYFORCE_NOT_AVAIL}';
            function sf_AddEvent(obj, evType, fn) {
                if (obj.addEventListener) {
                    obj.addEventListener(evType, fn, true);
                    return true;
                } else if (obj.attachEvent) {
                    var r = obj.attachEvent("on" + evType, fn);
                    return r;
                } else {
                    return false;
                }
            }
            function do_show_mes() {
                ShowMessage('error_messagebox', 1, mes_not_avail);
            }
            function sf_SetTimer() {
                timerID = setTimeout("do_show_mes()", 300);
            }
            sf_AddEvent(window, 'load', sf_SetTimer);
            //--><!]]>
        </script>

        <table id="survey_container_tbl" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td colspan="3" align="center">
                    <br />
                    <span id="error_messagebox" style="visibility:hidden;">Error text here</span>
                    <br /><br />
                </td></tr>
HTML;
            if ($sf_config->get('sf_show_dev_info', 1)) {	
                $blocked_html.='<tr><td colspan="3" align="center" style="text-align:center "><div style="text-align:center;">Powered by <span title="JoomPlace"><a target="_blank" title="JoomPlace" href="http://www.joomplace.com/e-learning/surveyforce-deluxe.html" rel="nofollow">Joomla component</a></span> SurveyForce Deluxe Software</div></td></tr>';
             }
        $blocked_html.='</table>';

		return $blocked_html;

    }

    public static function showCategory($cat, $rows, $sf_config) {

        surveyforce_template_class::showCategoryView($cat, $rows);


        $word = 'component';
        if (intval(md5(JPATH_SITE . 'survey')) % 2 == 0)
            $word = 'extension';

        if ($sf_config->get('sf_show_dev_info', 1)) {
            ?><br/><div style="text-align:center;">Powered by <span title='JoomPlace'><a target='_blank' title='JoomPlace' href='http://www.joomplace.com/e-learning/surveyforce-deluxe.html' rel="nofollow">Joomla <?php echo $word; ?></a></span> SurveyForce Deluxe Software</div><br/><?php
        }
    }

     

}
