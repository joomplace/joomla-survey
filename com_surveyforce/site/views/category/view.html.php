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
class SurveyforceViewCategory extends JViewLegacy {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

    public function display($tpl = null) {

		$database = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;

		$this->state = $this->get('State');
		$this->menuItemParams = $this->state->get('parameters.menu');

		if (isset($this->menuItemParams))
		{
			$categoryId = $this->menuItemParams->get('cat_id');
			if (!empty($categoryId))
				$menuParamsCategoryId = (int) $categoryId;
		}

		if ($menuParamsCategoryId != 0)
			$this->categoryId = $menuParamsCategoryId;
		else
			$this->categoryId = $jinput->get('id');

		$this->item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}
