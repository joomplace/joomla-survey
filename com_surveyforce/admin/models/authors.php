<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelAuthors extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('id', 'user_id');
		}
		parent::__construct($config);
	}

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
			->select('`b`.`id` AS `id`, `us`.`name`, `us`.`username`, `us`.`email`, `us`.`lastvisitDate`')
			->from('`#__users` AS `us`')
			->innerJoin('`#__survey_force_authors` AS `b`')
			->where('`us`.`id` = `b`.`user_id`');

		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->quote('%' . $search . '%', true);
			$query->where('`us`.`name` LIKE ' . $search . ' OR `us`.`username` LIKE ' . $search . ' OR `us`.`email` LIKE ' . $search . ' OR `us`.`id` LIKE ' . $search);
		}

		$orderCol = $this->state->get('list.ordering', 'us.name');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function add($cid)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->insert('`#__survey_force_authors`')
			->columns(
				array(
					$db->quoteName('user_id')
				)
			);
		for ($i = 0, $n = count($cid); $i < $n; $i++)
		{
			$query->values($cid[$i]);
		}
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	public function delete($cid)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->delete('#__survey_force_authors')
			->where('id IN (' . implode(',', $cid) . ')');
		$db->setQuery($query);
        $result = $db->execute();
        return $result;
	}

}
