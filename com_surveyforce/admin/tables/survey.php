<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class SurveyforceTableSurvey extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__survey_force_survs', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_surveyforce.survey.'.(int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->sf_name;
	}

	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_surveyforce');
		return $asset->id;
	}


	public function store($updateNulls = false) {

	    $jform = \JFactory::getApplication()->input->get('jform', array(), 'array');

		/*
		$asset = JTable::getInstance('Asset');
		$asset->loadByName($this->_getAssetName());
		
		$result = array();
		if($asset->id){
			$result = JAccess::getAssetRules($asset->id);
		}
		
		$asset->name = $this->_getAssetName();
		$asset->parent_id = $this->_getAssetParentId();
		$asset->title = $this->_getAssetTitle();
		
		$rules_form = $jform['rules'];
		$output2 = array();
		foreach ($rules_form as $key => $actions) {
			$output1 = array();
			foreach ($actions as $i => $value) {
				if($value == '') continue;
				$output1[$i] = $value;
			}
			$output2[$key] = $output1;
		}

		$rules = new JAccessRules(json_encode($output2));
		$rules->mergeCollection($result);
			
		$asset->rules = (string) $rules;
		
		if (!$asset->check() || !$asset->store()){
			$this->setError($asset->getError());
		}else{
			$this->asset_id = $asset->id;
		}
		*/

		$this->sf_author = $jform['sf_author'];
		if(!$this->sf_author){
		    $this->sf_author = JFactory::getUser()->id;
        }
		
		return parent::store($updateNulls);
	}
}