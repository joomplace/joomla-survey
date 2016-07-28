<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelInsert_survey extends JModelList {

    protected function populateState($ordering = null, $direction = null) {        
        parent::populateState();
    }

    protected function getListQuery() {
        
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('`#__survey_force_survs`');        
        
        // Filter by search in title
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$search = $this->getState('filter.search');
	
		if (!empty($search)) {
			$query->where('(sf_name LIKE "%'.$search.'%")');
		}

        $orderCol = $this->state->get('list.ordering', 'sf_name');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        return $query;
    }

}
