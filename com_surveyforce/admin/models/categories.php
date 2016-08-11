<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelCategories extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id','sf_catname','sf_catdescr','published');
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
        $query->select('id,ordering,sf_catname,sf_catdescr,published');
        $query->from('#__survey_force_cats');
        
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $search . '%', true);
            $query->where('sf_catname LIKE ' . $search);
        }
        $orderCol = $this->state->get('list.ordering', 'sf_catname');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        return $query;
    }

    function delete($cid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_cats');
        $query->where('id IN (' . implode(',', $cid) . ')');
        $db->setQuery($query);
        $db->execute();  //Remove all milistones
    }

    public function publish($cid, $value = 1) {
        $database = JFactory::getDBO();
        $task = JFactory::getApplication()->input->getCmd('task');
        $state = ($task == 'publish') ? 1 : 0;

        if (!is_array($cid) || count($cid) < 1) {
            $action = ($task == 'publish') ? 'publish' : 'unpublish';
            echo "<script> alert('" . JText::_('COM_SURVEYFORCE_SELECT_AN_ITEM_TO') . " $action'); window.history.go(-1);</script>\n";
            exit();
        }

        $cids = implode(',', $cid);

        $query = "UPDATE #__survey_force_cats"
                . "\n SET published = " . intval($state)
                . "\n WHERE id IN ( $cids )"
        ;
        $database->setQuery($query);
        if (!$database->execute()) {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
            exit();
        }

        return true;
    }

}
