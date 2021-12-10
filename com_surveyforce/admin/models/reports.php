<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelReports extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'is_complete', 'sf_ust.is_complete',
                'usertype', 'sf_ust.usertype',
                'survey_name', 'survey_name');
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null) {

        $app = JFactory::getApplication();

        $is_complete = $this->getUserStateFromRequest('reports.filter.is_complete', 'filter_is_complete');
        $this->setState('filter.is_complete', $is_complete);

        $usertype = $this->getUserStateFromRequest('reports.filter.usertype', 'filter_usertype');
        $this->setState('filter.usertype', $usertype);

        $survey_name = $this->getUserStateFromRequest('reports.filter.survey_name', 'filter_survey_name');
        $this->setState('filter.survey_name', $survey_name);
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery() {
        $database = JFactory::getDbo();       

        // get the subset (based on limits) of required records
        $query = $database->getQuery(true);
        $query->select("sf_ust.*, sf_s.sf_name as survey_name, u.username as reg_username, u.name as reg_name, u.email as reg_email,"
                . "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email");
        $query->from("#__survey_force_user_starts as sf_ust");
        $query->leftJoin("#__survey_force_survs as sf_s ON sf_ust.survey_id = sf_s.id");
        $query->leftJoin("\n #__users as u ON u.id = sf_ust.user_id ");
        $query->leftJoin("\n #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id ");

        // Filter by is_complete.
        $is_complete = $this->getState('filter.is_complete', '');
        

        if ($is_complete !== '') {
            $query->where('sf_ust.is_complete = ' . (int) $is_complete);
        }
        
        // Filter by usertype.
        $usertype = $this->getState('filter.usertype', '');
        if ($usertype !== '') {
            $query->where('sf_ust.usertype = ' . (int) $usertype);
        }
        
        // Filter by list_id.
        $survey_name = $this->getState('filter.survey_name', '');
        if ($survey_name !== '') {
            $query->where('sf_ust.survey_id = ' . (int) $survey_name);
        }


        $orderCol = $this->state->get('list.ordering', 'sf_ust.sf_time');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($database->escape($orderCol . ' ' . $orderDirn));
        $database->setQuery($query);

        return $query;
    }

    function getSurvey_names() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Construct the query
        $query->select('id AS value, sf_name AS text');
        $query->from('#__survey_force_survs');
        $query->order('sf_name');

        // Setup the query
        $db->setQuery($query);

        // Return the result
        return $db->loadObjectList();
    }

    function getUsertype() {
        $array = array();

        $obj = new stdClass();
        $obj->value = 0;
        $obj->text = JText::_('COM_SURVEYFORCE_GUEST');
        array_push($array, $obj);

        $obj = new stdClass();
        $obj->value = 1;
        $obj->text = JText::_('COM_SURVEYFORCE_REGISTERED_USER');
        array_push($array, $obj);

        $obj = new stdClass();
        $obj->value = 2;
        $obj->text = JText::_('COM_SURVEYFORCE_INVITED_USER');
        array_push($array, $obj);

        return $array;
    }

    function getSf_status() {

        $array = array();

        $obj = new stdClass();
        $obj->value = 0;
        $obj->text = JText::_('COM_SURVEYFORCE_NOT_COMPLETED');
        array_push($array, $obj);

        $obj = new stdClass();
        $obj->value = 1;
        $obj->text = JText::_('COM_SURVEYFORCE_COMPLETED');
        array_push($array, $obj);

        return $array;
    }

    function delete($cid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_user_starts');
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $result = $db->execute();
        return $result;
    }

}
