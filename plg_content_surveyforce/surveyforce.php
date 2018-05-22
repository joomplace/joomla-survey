<?php
/**
* SurveyForce mambot for Joomla
* @version $Id: surveyforce_content.php 2010-02-17 17:30:15
* @package SurveyForce
* @subpackage quizcont.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined( 'survey_version' )) define('survey_version', '3.1.0.002');

jimport('joomla.plugin.plugin');

class plgContentSurveyforce extends JPlugin
{

/**
* SurveyForce Content Mambot
*
* <b>Usage:</b>
* <code>{surveyforce id=6}</code>
*/
public function onContentPrepare( $context, &$row, &$params, $page=0 ) {
	
	JFactory::getLanguage()->load('com_surveyforce', JPATH_SITE, 'en-GB', true);

	// simple performance check to determine whether bot should process further
	
	if ( strpos( $row->text, 'surveyforce' ) === false ) {
		return true;
	}
	
	$option = \JFactory::getApplication()->input->get('option', '');
	if ($option == 'com_surveyforce'){
		return true;
	}

	// define the regular expression for the bot
	$regex = '/{surveyforce\s*.*?}/i';	

	// perform the replacement
	if (preg_match($regex, $row->text, $matches)){
		$replace = $this->botSurveyCode_replacer($matches);
		$row->text = preg_replace( $regex, $replace, $row->text);
	}
	return true;
}

/**
* Replaces the matched tags an image
* @param array An array of matches (see preg_match_all)
* @return string
*/
function botSurveyCode_replacer( &$matches ) {
	$text = $matches[0];
	
	$rres[1] = $matches[0];
	$rres[1] = str_replace('{surveyforce','', $rres[1]);
	$rres[1] = str_replace('}','', $rres[1]);
	$rres[1] = (int)str_replace('id=','', $rres[1]);
	
	$dir_ry = JPATH_SITE.'/components/com_surveyforce/'; 
	if(intval($rres[1]))
	{	
		
		$database = JFactory::getDBO();

		$database->setQuery("SELECT t.`sf_name` FROM `#__survey_force_templates` AS t LEFT JOIN `#__survey_force_survs` AS s ON s.`sf_template` = t.`id` WHERE s.`id` = '".intval($rres[1])."'");
		$template_name = $database->loadResult();

		$my = JFactory::getUser();
		$Itemid = \JFactory::getApplication()->input->getInt('Itemid', 0);
		include_once( $dir_ry ."helpers/surveyforce.php") ;

		SurveyforceHelper::SF_load_template($template_name);

		$helper = new SurveyforceHelper();
		$init_array = $helper->SF_ShowSurvey(intval($rres[1]));

		$this->survey = $init_array['survey'];
        $this->sf_config = $init_array['sf_config'];
        $this->is_invited = $init_array['is_invited'];
        $this->invite_num = $init_array['invite_num'];
        $this->rules = $init_array['rules'];
        $this->preview = $init_array['preview'];

		@ob_start();
		include_once( $dir_ry ."views/survey/tmpl/default.php") ;
		$text = @ob_get_contents();		
		@ob_end_clean();		
	}	
	
	return $text;
}

}
?>
