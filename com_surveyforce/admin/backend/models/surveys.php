<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelSurveys extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 's.id',
                'sf_name', 's.sf_name',
                'published', 's.published',
                'sf_cat', 's.sf_cat',
                'username', 'u.username',
                'sf_date_started', 's.sf_date_started',
				'sf_date_expired', 's.sf_date_expired',
                );
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
        $query->select('c.`sf_catname`, s.`id`, s.`sf_name`, s.`sf_descr`, s.`sf_image`, s.`sf_cat`, s.`sf_lang`, s.`sf_date_started`,s.`sf_date_expired`, s.`sf_author`, s.`sf_public`, s.`sf_invite`, s.`sf_reg`, s.`published`, s.`sf_fpage_type`, s.`sf_fpage_text`, s.`sf_special`, s.`sf_auto_pb`, s.`sf_progressbar`');
        $query->from('`#__survey_force_survs` as s');
        $query->join('left', '`#__survey_force_cats` as c ON c.`id` = s.`sf_cat`');
		//$query->join('left', '`#__users` as u ON u.`id` = s.`sf_author`');
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->Quote('%' . $db->Escape($search, true) . '%');
            $query->where('s.`sf_name` LIKE ' . $search);
        }
        $orderCol = $this->state->get('list.ordering', 's.`sf_name`');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        
		
	
		
		return $query;
    }

    function delete($cid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__survey_force_survs');
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

        $query = "UPDATE #__survey_force_survs"
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
	
	public function getMyAuthor($author = array()) {
		
		$db		= JFactory::getDbo();
		
		
		foreach ($author as $i => $item){
			$query = $db->getQuery(true);
			$query->select('`name`');
			$query->from('`#__users`');
			$query->where($db->quoteName('id') . ' IN ('.implode(',',json_decode($item->sf_author)).')');
			$db->setQuery($query);
			$myauthor =  $db->loadColumn(); 
			
			$item->username = implode(',',$myauthor);
		
		}
		
		return $item->username;
		
		
	}
	

}
