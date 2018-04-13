<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modeladmin');

class SurveyforceModelSection extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Section', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_surveyforce.edit.section.data', array());

        if (empty($data)) {
            $data = $this->getItem();
            
            $ordering = $this->getOrdering();
            // Prime some default values.
            if ($this->getState('section.id') == 0) {
                $app = JFactory::getApplication();
                $id = $app->getUserState('com_surveyforce.edit.section.id');
                if ($id) $data->set('id', JFactory::getApplication()->input->getInt('id', $id));
            }
        }
        
        return $data;
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.section', 'section', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }

    public function getItem($pk = null)
    {
        $result = parent::getItem($pk);
        return $result;
    }

    public function getQuestions($survey_id = null, $section_id = null){
        $db = JFactory::getDBO();

        $quest_choosen = array();
        if(empty($section_id))
            $section_id = JFactory::getApplication()->input->get('id');
        
 
        if($section_id){
            $db->setQuery("SELECT `id` FROM `#__survey_force_quests` WHERE `sf_section_id` = '".$section_id."'");
            $quest_choosen = $db->loadColumn();
            if(empty($survey_id)) {
                $item = $this->getItem();
                $survey_id = $item->sf_survey_id;
            }
        } elseif(empty($survey_id)) {
            $survey_id = JFactory::getApplication()->input->get('surv_id');
        }
        
        $db->setQuery("SELECT `id` as `value`, `sf_qtext` as `text` FROM `#__survey_force_quests` WHERE `sf_survey`=".$survey_id);
        $questions = $db->loadObjectList();

        if(count($questions)){
            foreach ($questions as &$question) {
                $question->text = trim(strip_tags($question->text));
                $question->text = mb_substr($question->text, 0, 100)."...";
            }
        }

        $lists = array();
        $lists['questions'] = JHTML::_('select.genericlist', $questions, 'sf_quest[]', 'class="text_area" style="max-width: 300px;" size="8" multiple="multiple"', 'value', 'text', $quest_choosen );

        return $lists;

    }

    public function save($data = null){

        $database = JFactory::getDBO();

        $section_id = JFactory::getApplication()->input->get('id');
        $surv_id = JFactory::getApplication()->input->get('surv_id');

        $row = JTable::getInstance('Section', 'SurveyforceTable', array());
        $row->load($section_id);

        if (!$id = $row->store()) {
            echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
            exit();
        }

        return $id;

    }
    
    public function delete($sids) {
        $result = false;
        if(parent::delete($sids)) {
            if (count($sids) > 1) {
                $toQuery = ' IN (' . implode(',', ArrayHelper::toInteger($sids)) . ')';
            } else {
                $toQuery = '=' . $sids[0];
            }
            $db = JFactory::getDbo();
            $db->setQuery("UPDATE `#__survey_force_quests` SET `sf_section_id` = 0 WHERE `sf_section_id`".$toQuery);
            $db->execute();
            $result = true;
        }
        
        return $result;
    }

}
