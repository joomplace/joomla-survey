<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelEmails extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id','email_subject','email_body', 'email_reply', 'user_id');
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
        $query->select('`id`,`email_subject`,`email_body`,`email_reply`,`user_id`');
        $query->from('`#__survey_force_emails`');
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->Quote('%' . $db->Escape($search, true) . '%');
            $query->where('`email_subject` LIKE ' . $search);
        }
        $orderCol = $this->state->get('list.ordering', '`email_subject`');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        return $query;

    }

    function delete($cid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_emails');
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $result = $db->execute();
        return $result;
    }
}
