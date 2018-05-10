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

class SurveyforceTableSection extends JTable {

    function __construct(&$db) {
        parent::__construct('#__survey_force_qsections', 'id', $db);
    }

    function store($updateNulls = false){

    	$database = JFactory::getDBO();

    	$post = JRequest::get('post');
    	$questions = $post['sf_quest'];

    	$id = ($post['id']) ? $post['id'] : '';
    	$isNew = ($id == '') ? true : false;    	

    	if(!$isNew){
    		$database->setQuery("SELECT `ordering` FROM `#__survey_force_qsections` WHERE `id` = '".$id."'");
    		$ordering = $database->loadResult();
    	} else {
    		$database->setQuery("SELECT MAX(`ordering`) FROM `#__survey_force_qsections`");
    		$ordering = $database->loadResult();
    		$ordering++;
    	}

    	$row = new stdClass;
    	$row->id = $id;
    	$row->sf_name = $post['jform']['sf_name'];
    	$row->addname = $post['jform']['addname'];
    	$row->ordering = $ordering;
    	$row->sf_survey_id = $post['jform']['sf_survey_id'];

    	if($isNew){
    		$database->insertObject('#__survey_force_qsections', $row, 'id');
    		$id = $database->insertid();
    	} else {
    		$database->updateObject('#__survey_force_qsections', $row, 'id');
    	}

    	if(count($questions)){
			$query = "UPDATE `#__survey_force_quests` SET `sf_section_id` = 0 WHERE `sf_section_id` = {$id}";
			$database->setQuery( $query );
			$database->Query( );
			$query = "UPDATE `#__survey_force_quests` SET `sf_section_id` = {$id} WHERE id IN ( ".implode(',', $questions)." )";
			$database->setQuery( $query );
			$database->Query( );
		}

		return $id;
    }
    
}