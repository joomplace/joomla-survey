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
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewAuthoring extends JViewLegacy {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

    public function display($tpl = null) {
		$this->sf_config = JComponentHelper::getParams('com_surveyforce');
		$this->is_author = $this->get('is_author');

		$model = $this->getModel ();

		if ( $this->is_author )
		{
			$this->html = $model->getPage();
			if ( !$this->html || is_array($this->html) )
			{
				$this->html = SurveyforceTemplates::Survey_blocked(false,'_no_html');
			}
		}

		JFactory::getDocument()->addStyleSheet(JUri::base().'components/com_surveyforce/assets/css/surveyforce.css');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }
    
    

}
