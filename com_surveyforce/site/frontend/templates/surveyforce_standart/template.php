<?php
/**
 * Survey Force component for Joomla
 * @version $Id: template.php 2009-11-16 17:30:15
 * @package Survey Force
 * @subpackage template.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');



if (!class_exists('surveyforce_template_class')) {

    class surveyforce_template_class {
        /*         * *********************************************************************************************************************

          {SURVEY_NAME}  surveys name will be placed there
          {BACKGROUND_IMAGE} - text for background image will be placed there
          {PROGRESS_BAR} - progress bar will be placed there
          {ERROR_MESSAGE_TOP} - first (top) error message will be placed there
          {ERROR_MESSAGE_BOTTOM}- second (bottom) error massage will be placed there
          {SURVEY_BODY}- survey will be placed there -  surveys start page (description), questions and answers, final page
          (in function `SF_SurveyBody` you can define a place for each item - question text(description), answers, importance scale)

          {START_BUTTON}{PREV_BUTTON}{NEXT_BUTTON}{FINISH_BUTTON}  surveys control buttons will be placed there

         * ********************************************************************************************************************* */

        public static function SF_MainLayout() {

			self::getJsCssTmpl();
            $return_str = <<<EOF_RES
			<!-- DON'T FORGET CHANGE HREF '-->
			<div class="contentpane surveyforce">

			<div class="componentheading"><h2>{SURVEY_NAME}</h2></div>
			<table class="contentpane" id="survey_container_tbl" style="min-height:250px; height:auto !important; height:250px; width:100%;{BACKGROUND_IMAGE}" cellpadding="0" cellspacing="0" border="0" >
			<tr><td id="sf_progressbar" colspan="3" align="left">
				{PROGRESS_BAR}
			</td></tr>
			<tr><td id="sf_error_message" colspan="3" align="center">
				{ERROR_MESSAGE_TOP}
			</td></tr>
			<tr><td id="sf_survey_body" colspan="3" valign="top">
				{SURVEY_BODY}
			</td></tr>
			<tr><td id="sf_error_message" colspan="3" align="center">
				{ERROR_MESSAGE_BOTTOM}
			</td></tr>
			<tr><td colspan="3" align="center" id="td_survey_task">
					{START_BUTTON}{PREV_BUTTON}{NEXT_BUTTON}{FINISH_BUTTON}
			</td></tr>
			</table>
			</div>
EOF_RES;
            return $return_str;
        }
        
        public static function SF_BodyQuestion(){
            $body = <<<EOFTMPL
			<div align="left" style="padding-left:10px;text-align:left;">{QUESTION_TEXT}</div>
			<div>{ANSWERS}</div>
			{IMPORTANCE_SCALE}				
EOFTMPL;
            
            //remove new line characters
            $body = str_replace("\n", '', $body);
            $body = str_replace("\r", '', $body);
            return $body;
        }
        
        public static function getJsCssTmpl(){
            $document = JFactory::getDocument();
            $document->addStyleSheet(JUri::base().'components/com_surveyforce/templates/surveyforce_standart/css/surveyforce.css');
        }

        public static function SF_GetStartButton() {
            return '<input type="button" id="sf_start_button"  value="' . JText::_('COM_SURVEYFORCE_START') . '" class="button" onclick="javascript: sf_StartSurveyOn()"/>';
        }

    }

    //class..
}//if 
