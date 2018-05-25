<?php

/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Joomla.Component
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewPassed_survey extends JViewLegacy {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

	public function display($tpl = null) {
		
		$this->user = JFactory::getUser(); // Need ?
		$this->items = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage($this->get('Errors'), 'error');
			return false;
		}
		parent::display($tpl);
	}

}
