<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.modeladmin');

class SurveyforceModelSet_default extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Defanswers', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getOptions(){

        $database = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
    
        $id = $mainframe->input->get('id', '');
        $row = JTable::getInstance('Question', 'SurveyforceTable', array());
        $row->load($id);
        
        $lists = array();
        $lists['answer_data'] = array();

        $query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$row->id."' AND is_main = '1' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['main_data'] = $database->loadObjectList();

        $query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '".$row->id."' AND is_main = '0' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['second_data'] = $database->loadObjectList();

        $query = "SELECT * FROM `#__survey_force_scales` WHERE `quest_id` = '".$row->id."' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['scale_data'] = $database->loadObjectList();

		$byKey = false;
		if ( $row->sf_qtype <= 3 )
			$byKey = 'answer';

        $query = "SELECT * FROM `#__survey_force_def_answers` WHERE `quest_id` = '".$row->id."' ";
        $database->SetQuery($query);
		$lists['answer_data'] = $database->loadAssocList('answer');

		$return = array();
		$data = array();

		$lists['sf_qtype'] = $row->sf_qtype;
		$lists['row'] = $row;

		$data['id'] = $row->id;

        $type = SurveyforceHelper::getQuestionType($row->sf_qtype);

        JPluginHelper::importPlugin('survey', $type);
        $className = 'plgSurvey' . ucfirst($type);

        if (method_exists($className, 'onGetSetDefault'))
                $return = $className::onGetSetDefault($data);

        if(count($return)){
            $lists = array_merge($lists, $return);
        }

        return $lists;
    }

    public function getForm($data = array(), $loadData = true) {
        
        return true;
    }

}
