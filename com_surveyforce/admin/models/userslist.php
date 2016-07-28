<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelUsersList extends JModelList
{
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState();
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('`user_id`')
			->from('`#__survey_force_authors`');
		$db->setQuery($query);
		$authors = $db->loadColumn();

		$query = $db->getQuery(true)
			->select('`us`.`id`, `us`.`name`, `us`.`username`, `us`.`email`, `us`.`lastvisitDate`')
			->from('`#__users` AS `us`')
			->innerJoin('`#__user_usergroup_map` AS `ug` ON `ug`.`user_id` = `us`.`id`')
			->where('`ug`.`group_id` != 8');

		if (count($authors))
		{
			$query->where('`us`.`id` NOT IN(' . implode(',', $authors) . ')');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(`us`.`name` LIKE ' . $search . ' OR `us`.`username` LIKE ' . $search . ' OR `us`.`email` LIKE ' . $search . ' OR `us`.`id` LIKE ' . $search . ')');
		}

		$orderCol = $this->state->get('list.ordering', 'us.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}