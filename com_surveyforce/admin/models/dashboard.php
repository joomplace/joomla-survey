<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelDashboard extends JModelList
{
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array();
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);

        $this->setState('list.start', 0);
        $this->setState('list.limit', 0);
    }

    protected function getListQuery()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('`#__survey_force_dashboard_items` AS `di`');
        $query->where('`di`.published=1');

        return $query;
    }

    public function getCurrDate()
    {
        $params = JComponentHelper::getParams('com_surveyforce');

        if (strtotime("+2 month", strtotime($params->get('curr_date'))) <= strtotime(JFactory::getDate())) {
            return true;
        } else {
            return false;
        }
    }
}
