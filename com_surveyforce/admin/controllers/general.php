<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

class SurveyforceControllerGeneral extends JControllerLegacy
{
	//----------------------------------------------------------------------------------------------------
	public function get_latest_component_version()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/Snoopy.php");
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/MethodsForXml.php");

		// Making request.
		$snoopy = new Snoopy();
		$snoopy->read_timeout = 90;
		$snoopy->referer = JURI::root();
		@$snoopy->fetch("http://www.joomplace.com/version_check/componentVersionCheck.php?component=survey_deluxeex&current_version=".urlencode(SurveyforceHelper::getVersion()));
		
		$error = $snoopy->error;
		$status = $snoopy->status;
		
		$results = $snoopy->results;
		$results = json_decode($results);

		// Returning data.
		
		@ob_clean();
		header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/xml; charset=utf-8');
		
		$xml = array();
		
		$xml[] = "<\x3fxml version=\"1.0\" encoding=\"UTF-8\"\x3f>";
		$xml[] = '<root>';
		$xml[] = 	'<error>' . MethodsForXml::XmlEncode($error) . '</error>';
		$xml[] = 	'<status>' . MethodsForXml::XmlEncode($status) . '</status>';
		$xml[] = 	'<version>' . MethodsForXml::XmlEncode($results->version) . '</version>';
		$xml[] = 	'<changelog>' . MethodsForXml::XmlEncode($results->changelog) . '</changelog>';
		$xml[] = 	'<link>' . MethodsForXml::XmlEncode($results->link) . '</link>';
		$xml[] = '</root>';
		
		print(implode("", $xml));
		
		jexit();
	}
	//----------------------------------------------------------------------------------------------------
	public function get_latest_news()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/Snoopy.php");
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/MethodsForXml.php");
		
		// Making request.
		
		$snoopy = new Snoopy();
		$snoopy->read_timeout = 10;
		$snoopy->referer = JURI::root();
		@$snoopy->fetch("http://www.joomplace.com/news_check/componentNewsCheck.php?component=survey_deluxe");
		
		$error = $snoopy->error;
		$status = $snoopy->status;
		
		$content = $snoopy->results;
		
		// Returning data.
		
		@ob_clean();
		header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/xml; charset=utf-8');
		
		$xml = array();
		
		$xml[] = "<\x3fxml version=\"1.0\" encoding=\"UTF-8\"\x3f>";
		$xml[] = '<root>';
		$xml[] = 	'<error>' . MethodsForXml::XmlEncode($error) . '</error>';
		$xml[] = 	'<status>' . MethodsForXml::XmlEncode($status) . '</status>';
		$xml[] = 	'<content>' . MethodsForXml::XmlEncode($content) . '</content>';
		$xml[] = '</root>';
		
		print(implode("", $xml));
		
		jexit();
	}
	//----------------------------------------------------------------------------------------------------
	public function show_changelog()
	{
		@ob_clean;
		header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/html; charset=utf-8');
		
		jimport ('joomla.filesystem.file');
		
		echo '<h2>' . JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_CHANGELOG') . '</h2>';
		
		if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt'))
		{
			echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_CHANGELOG_NO_FILE');
		}
		else
		{
			echo '<pre style="font-size:12px;">';
			echo 	JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt');
			echo '</pre>';
		}
		
		jexit();
	}

	public function datedb()
	{
		$app = JFactory::getApplication();

		$params = JComponentHelper::getParams('com_surveyforce');
		$params->set('curr_date', date("Y-m-d"));

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update('`#__extensions`')
			->set('`params`= "' . addslashes($params->toString()) . '"')
			->where('`name` = "com_surveyforce"');
		$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			die($e->getMessage());
		}

		$app->close();
	}
}