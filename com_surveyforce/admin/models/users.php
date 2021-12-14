<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelUsers extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('id', 'name', 'lastname', 'email');
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();
	}

	protected function getListQuery()
	{
		$id = JFactory::getApplication()->input->getInt('id', 0);

		if (empty($id) || $id == 0) {
            return false;
        }

        $db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__survey_force_users');
		$query->where('list_id=' . $id);

		$orderCol = $this->state->get('list.ordering', 'name');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	function delete($cid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__survey_force_users');
		$query->where('id IN (' . implode(',', $cid) . ')');
		$db->setQuery($query);
        $result = $db->execute();
        return $result;
	}
}
