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
class SurveyforceViewSurvey extends JViewLegacy {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

    public function display($tpl = null) {

		$this->state = $this->get('State');
		$this->menuItemParams = $this->state->get('parameters.menu');

		if (isset($this->menuItemParams))
		{
			$surveyId = $this->menuItemParams->get('surv_id');
			if (!empty($surveyId))
				$menuParamsSurveyId = (int) $surveyId;
		}

		if ( !empty($menuParamsSurveyId) )
			$this->surveyId = $menuParamsSurveyId;
		else
			$this->surveyId = (int)JFactory::getApplication()->input->get('id');

		$user = JFactory::getUser();
		$assetName = 'com_surveyforce.survey.'.$this->surveyId;
		
		$helper = new SurveyforceHelper();

		if (JFactory::getApplication()->input->getString('invite'))
			$init_array = $helper->SF_ShowSurvey_Invited();
		else
			$init_array = $helper->SF_ShowSurvey( $this->surveyId );
		
        $this->survey = $init_array['survey'];
        $this->sf_config = $init_array['sf_config'];
        $this->is_invited = $init_array['is_invited'];
        $this->invite_num = $init_array['invite_num'];
        $this->rules = $init_array['rules'];
        $this->preview = $init_array['preview'];

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}
