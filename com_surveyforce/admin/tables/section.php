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

        $database = \JFactory::getDBO();

        $post = \JFactory::getApplication()->input->post;
        $jform = $post->get('jform', array(), 'array');
        $questions = $post->get('sf_quest', array(), 'ARRAY');
        $id = $post->get('id', 0, 'INT');
        $isNew = !$id ? true : false;

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
        $row->sf_name = htmlspecialchars($jform['sf_name'], ENT_COMPAT, 'UTF-8');
        $row->addname = (int)$jform['addname'];
        $row->ordering = $ordering;
        $row->sf_survey_id = (int)$jform['sf_survey_id'];

        if($isNew){
            $database->insertObject('#__survey_force_qsections', $row, 'id');
            $id = $database->insertid();
        } else {
            $database->updateObject('#__survey_force_qsections', $row, 'id');
        }

        if(count($questions)){
            $query = "UPDATE `#__survey_force_quests` SET `sf_section_id` = 0 WHERE `sf_section_id` = {$id}";
            $database->setQuery($query)->execute();

            $query = "UPDATE `#__survey_force_quests` SET `sf_section_id` = {$id} WHERE id IN ( ".implode(',', $questions)." )";
            $database->setQuery($query)->execute();
        }

        return $id;
    }
    
}