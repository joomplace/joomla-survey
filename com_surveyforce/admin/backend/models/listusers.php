<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelListusers extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id','listname','survey_id', 'date_created', 'date_invited', 'date_remind', 'is_invited', 'sf_author_id');
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null) {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        parent::populateState();
    }

    protected function getListQuery() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id, a.listname, a.survey_id, a.date_created, a.date_invited, a.date_remind, a.is_invited, b.sf_name as survey_name, count(c.id) as users_count, d.name AS author, e.user_id AS starts');
        $query->from('#__survey_force_listusers a LEFT JOIN #__survey_force_survs b ON b.id = a.survey_id LEFT JOIN #__survey_force_users c ON c.list_id = a.id LEFT JOIN #__users AS d ON d.id = a.sf_author_id LEFT JOIN #__survey_force_user_starts AS e ON c.id=e.user_id');
        $query->group('a.id');
        /*$query->group('a.id, a.listname, a.survey_id, a.date_created, a.date_invited, a.date_remind, a.is_invited, b.sf_name, e.user_id');*/
        $query->order('a.listname');
        
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->Quote('%' . $db->Escape($search, true) . '%');
            $query->where('`listname` LIKE ' . $search);
        }
        $orderCol = $this->state->get('list.ordering', '`listname`');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        return $query;
    }
    
    
    function delete($cid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_listusers');
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $db->execute();  //Remove all
        
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_users');
        $query->where('list_id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $db->execute();  //Remove all
    }

   

}
