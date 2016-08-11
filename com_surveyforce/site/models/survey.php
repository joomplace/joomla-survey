<?php

/**
 * Surveyforce Component for Joomla 3
 * @package Surveyforce
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Survey Model.
 */
class SurveyforceModelSurvey extends JModelItem {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

	public function populateState()
	{
		$params	= JFactory::getApplication()->getParams();
		$jinput = JFactory::getApplication()->input;

		$id	= $jinput->get('id', 0, 'INT');

		$this->setState('survey.id', $id);
		$this->setState('params', $params);
	}

    public function getSurveyParams() {
        $app = JFactory::getApplication();
        $params = $app->getParams();

        return $params;
    }

    public function getSurveyConfig() {

        $params = JComponentHelper::getParams('com_surveyforce');

        return $params;
    }

    public function getSurvey($survey_id = 0) {
        $database = JFactory::getDBO();
        if ($survey_id == 0) {
            $params = $this->getSurveyParams();
            $survey_id = $params->get('surv_id');
        }

        $query = $database->getQuery(true);
        $query->select('*');
        $query->from($database->quoteName('#__survey_force_survs'));
        $query->where($database->quoteName('id') . '=' . $survey_id);

        $database->setQuery($query);
        $survey = $database->LoadObject();

        if ($database->getErrorMsg()) {

            return $database->getErrorMsg();
        }
        else
		{

            return $survey;
		}
    }

}
