<?php

/**
 * Surveyforce Component for Joomla 3
 * @package Surveyforce
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Category Model.
 *
 */
class SurveyforceModelCategory extends JModelItem {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

	public function populateState()
	{
		$params	= JFactory::getApplication()->getParams();
		$jinput = JFactory::getApplication()->input;

		$id	= $jinput->get('id', 0, 'INT');

		$this->setState('category.id', $id);
		$this->setState('params', $params);
	}

	public function getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('category.id');

				if (empty($id))
				{
					$params = $this->getState('params');
					$id = (int) $params->get('cat_id');
				}
			}

			if (empty($id)) return null;

			$database = JFactory::getDbo();

			$query = "SELECT * FROM `#__survey_force_cats` WHERE `id` = '$id'";
			$database->SetQuery( $query );
			$this->_item = $database->loadObject();
			$this->_item->surveys = array();

			$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_cat` = '$id' AND `published` = 1 AND `sf_date_started` < CAST(NOW() as date) AND `sf_date_expired` > CAST(NOW() as date)";
			$database->SetQuery( $query );
			$rows = $database->loadObjectList();

			if(is_array($rows) && count($rows))
				foreach($rows as $i=>$row){
					$row->surv_short_descr = trim( strip_tags($rows[$i]->surv_short_descr) );
					$this->_item->surveys[$i] = $row;
				}
		}

		return $this->_item;
	}

}
