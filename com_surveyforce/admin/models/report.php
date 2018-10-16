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

class SurveyforceModelReport extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Report', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.report', 'report', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
        $form->bind($item);

        return $form;
    }
    
    public function getStartData($id){
        
        $database = JFactory::getDbo();        
        
        $query = "SELECT s.*,  u.username reg_username, u.name reg_name, u.email reg_email,"
                . "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
                . "\n FROM #__survey_force_user_starts as s"
                . "\n LEFT JOIN #__users as u ON u.id = s.user_id and s.usertype=1"
                . "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = s.user_id and s.usertype=2"
                . "\n WHERE s.id = '" . $id . "'";
        $database->SetQuery($query);
        $start_data = $database->LoadObject();
        
        return $start_data;
        
    }
    
    public function getQuestionsData($survey_id){
        $database = JFactory::getDbo();
        
        if (!$survey_id) {
            echo "<script> alert('" . JText::_('COM_SURVEYFORCE_NO_RESULTS_FOUND') . "'); window.history.go(-1);</script>\n";
            exit;
        }

        $query = "SELECT q.*"
                . "\n FROM #__survey_force_quests as q"
                . "\n WHERE q.published = 1 AND q.sf_survey = '" . $survey_id . "' AND sf_qtype NOT IN (8)"
                . "\n ORDER BY q.ordering, q.id ";
        $database->SetQuery($query);
        $questions_data = $database->loadObjectList();
        
        return $questions_data;
    }
    
    public function getSurveyData($survey_id){
        $my = JFactory::getUser();
        $database = JFactory::getDbo();
        
        $query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $survey_id . "' "
//                . (!in_array(8, $my->groups)? " AND sf_author = '{$my->id}' " : ' ');
            . (!isset($my->groups[8]) ? " AND sf_author = '{$my->id}' " : ' ');
        $database->SetQuery($query);
        $survey_data = $database->LoadObject();
        
        return $survey_data;
    }
    
    public function getQuestionHTML($question, $start_data){
        
        $database = JFactory::getDBO();
        $return = array();

        if($question){

            if (@$question->sf_impscale) {

                $imp_question = new stdClass;
                $query = "SELECT `iscale_name` FROM `#__survey_force_iscales` WHERE `id` = '".$question->sf_impscale."'";
                $database->SetQuery( $query );
                $imp_question->iscale_name = $database->loadResult();

                $query = "SELECT `iscalefield_id` FROM `#__survey_force_user_answers_imp`"
                . "\n WHERE `quest_id` = '".$question->id."' and survey_id = '".$question->sf_survey."'"
                . "\n AND iscale_id = '".$question->sf_impscale."'"
                . "\n and start_id = '".$start_data->id."'";
                $database->SetQuery( $query );
                $ans_inf = $database->LoadResult();
                
                $imp_question->answer_imp = array();
                $query = "SELECT * FROM `#__survey_force_iscales_fields` WHERE `iscale_id` = '".$question->sf_impscale."'"
                . "\n ORDER BY ordering";
                $database->SetQuery( $query );
                $tmp_data = $database->loadObjectList();

                $j = 0;
                while ( $j < count($tmp_data) ) {
                    $imp_question->answer_imp[$j]->num = $j;
                    $imp_question->answer_imp[$j]->f_id = $tmp_data[$j]->id;
                    $imp_question->answer_imp[$j]->f_text = $tmp_data[$j]->isf_name;
                    $imp_question->answer_imp[$j]->alt_text = '';
                    if ($ans_inf == $tmp_data[$j]->id) {
                        $imp_question->answer_imp[$j]->alt_text = '1';
                        $imp_question->answer_imp[$j]->alt_id = $ans_inf;
                    }
                    $j ++;
                }

                $return['imp_answers'] = $imp_question;
            }

            $return['answers'] = '';
			$type = SurveyforceHelper::getQuestionType($question->sf_qtype);
			JPluginHelper::importPlugin('survey', $type);
			$className = 'plgSurvey' . ucfirst($type);

			if (method_exists($className, 'onGetAdminReport'))
					$return['answers'] = $className::onGetAdminReport($question, $start_data);
        }
        
        return $return;
    }

}
