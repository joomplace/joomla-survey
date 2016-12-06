<?php

/**
 * SurveyForce Delux Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;
		$itemid = $input->post->get('Itemid', 0, 'INT');
		if(!$itemid){
			$itemid = $input->get->get('Itemid', 0, 'INT');
			if(!$itemid){
				$itemid = $input->get('Itemid', 0, 'INT');
			}
		}
		$input->post->set('Itemid',$itemid);
		$input->get->set('Itemid',$itemid);
		$input->set('Itemid',$itemid);

		$view = JFactory::getApplication()->input->get('view');
		$task = JFactory::getApplication()->input->get('task');

		$input = JFactory::getApplication()->input;
		if ($view == 'authoring' && !isset($_SESSION['view']))
		{
			$_SESSION['view'] = 'authoring';
		}
		elseif ($view != 'authoring' && (isset($_SESSION['view']) && $_SESSION['view'] != 'authoring'))
		{
			unset($_SESSION['view']);
		}

		if (isset($_SESSION['view']) && $_SESSION['view'] == 'authoring' && $view != 'survey' && $view != 'passed_survey' && $view != 'category')
		{
			$input->set('view', 'authoring');
		}
		else
		{
			if ($view != 'category' && $view != 'insert_survey' && $view != 'passed_survey') $input->set('view', 'survey');
		}

		if ($task == 'start_invited')
		{
			$input->set('view', 'survey');
		}

		if ($task == 'view_users')
		{
			$input->set('view', 'authoring');
		}

		parent::display($cachable);
	}
}