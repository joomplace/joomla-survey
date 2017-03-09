<?php

/**
 * SurveyForce Multiple Choice Plugin for Joomla
 * @version $Id: likertscole.php 2011-03-03 17:30:15
 * @package SurveyForce
 * @subpackage likertscole.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyPagebreak {

    var $name = 'Pagebreak';
    var $_name = 'pagebreak';
    var $_type = 'survey';

    public function plgSurveyPagebreak() {
        return true;
    }

    public function onSaveQuestion(&$data) {

        $database = JFactory::getDbo();
        $sf_survey = $data['item']->sf_survey;
        
        $query = "SELECT MAX(ordering) FROM #__survey_force_quests WHERE sf_survey = {$sf_survey}";
        $database->SetQuery($query);
        $max_ord = $database->LoadResult();

        $query = "INSERT INTO #__survey_force_quests (sf_survey, sf_qtype, sf_compulsory, sf_qtext, ordering, published, is_final_question ) VALUES ($sf_survey, 8, 0, 'Page Break', " . ($max_ord + 1) . ", 1, 0) ";
        $database->setQuery($query);
        $database->query();
    }

    public function onGetScriptJs() {

        $document = JFactory::getDocument();
        $document->addScript(JUri::root()."/plugins/survey/pagebreak/js/pagebreak.js");
    }

    public function onGetQuestionData(&$data) {

        $database = JFactory::getDbo();

        $q_data = $data['q_data'];
        $start_id = $data['start_id'];

        $ret_str = '';
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $q_data->id . "' and is_main = '1' ORDER BY ordering";
        $database->SetQuery($query);
        $result = $database->LoadObjectList();
        $f_main_data = ($result == null ? array() : $result);
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $q_data->id . "' and is_main = '0' ORDER BY ordering";
        $database->SetQuery($query);
        $result = $database->LoadObjectList();
        $f_alt_data = ($result == null ? array() : $result);


        // add answers section for prev/next
        $query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $q_data->id . "' AND start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
        $result = $database->LoadObjectList();
        $f_answ_data = ($result == null ? array() : $result);

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
        $q_text = $q_data->sf_qtext;


        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
            $result = $database->LoadObjectList();
            $qsection_t = ($result == null ? array() : $result);
            if (isset($qsection_t[0]->addname) && intval($qsection_t[0]->addname) > 0) {
                $q_text = '<div class="sf_section_name">' . $qsection_t[0]->sf_name . "</div><br/>" . $q_text;
            }
        }
        $ret_str .= "\t\t" . '<quest_inp_count>' . $inp . '</quest_inp_count>' . "\n";
        $ret_str .= "\t" . '<quest_text><![CDATA[' . SurveyforceHelper::sfPrepareText($q_text) . '&nbsp;]]></quest_text>' . "\n";
        $ret_str .= "\t" . '<quest_id>' . $q_data->id . '</quest_id>' . "\n";
        $ret_str .= "\t" . '<default_hided>' . (int) $q_data->sf_default_hided . '</default_hided>' . "\n";
        $ret_str .= "\t" . '<main_fields_count>' . count($f_main_data) . '</main_fields_count>' . "\n";
        $ret_str .= "\t" . '<compulsory>' . $q_data->sf_compulsory . '</compulsory>' . "\n";
        $ret_str .= "\t" . '<sf_qstyle>' . (int) $q_data->sf_qstyle . '</sf_qstyle>' . "\n";
        $ret_str .= "\t" . '<factor_name><![CDATA[' . $q_data->sf_fieldtype . '&nbsp;]]></factor_name>' . "\n";
        $ret_str .= "\t" . '<sf_num_options>' . (int) $q_data->sf_num_options . '</sf_num_options>' . "\n";

        if (count($f_main_data) > 0) {
            $ret_str .= "\t" . '<main_fields>' . "\n";
            foreach ($f_main_data as $f_row) {
                $ret_str .= "\t\t" . '<main_field><mfield_text><![CDATA[' . stripslashes($f_row->ftext) . '&nbsp;]]></mfield_text>' . "\n";
                $ret_str .= "\t\t\t" . '<mfield_is_true>' . $f_row->is_true . '</mfield_is_true>' . "\n";
                $ret_str .= "\t\t\t" . '<mfield_id>' . $f_row->id . '</mfield_id></main_field>' . "\n";
                if ($f_row->is_true == 2) {
                    $query = "SELECT a.ans_txt FROM #__survey_force_user_ans_txt AS a, #__survey_force_user_answers AS b WHERE b.quest_id = '" . $q_data->id . "' AND b.start_id = '" . $start_id . "' AND b.answer = '" . $f_row->id . "' AND a.id = b.next_quest_id";
                    $database->SetQuery($query);
                    $ans_txt = $database->LoadResult();
                    if (strlen($ans_txt) < 1)
                        $ans_txt = '!!!---!!!';
                    $ret_str .= "\t\t\t" . '<ans_txt>' . $ans_txt . '</ans_txt>' . "\n";
                }
            }
            $ret_str .= "\t" . '</main_fields>' . "\n";
        }
        $ret_str .= "\t" . '<alt_fields_count>' . count($f_alt_data) . '</alt_fields_count>' . "\n";
        if (count($f_alt_data) > 0) {
            $ret_str .= "\t" . '<alt_fields>' . "\n";
            foreach ($f_alt_data as $f_row) {
                $ret_str .= "\t\t" . '<alt_field><afield_text><![CDATA[' . stripslashes($f_row->ftext) . '&nbsp;]]></afield_text>' . "\n";
                $ret_str .= "\t\t\t" . '<afield_id>' . $f_row->id . '</afield_id></alt_field>' . "\n";
            }
            $ret_str .= "\t" . '</alt_fields>' . "\n";
        }

        $f_iscale_data = array();
        if ($q_data->sf_impscale) { //important scale is SET
            $query = "SELECT a.iscale_name, b.* FROM #__survey_force_iscales as a, #__survey_force_iscales_fields as b WHERE a.id = '" . $q_data->sf_impscale . "' AND a.id = b.iscale_id ORDER BY b.ordering";
            $database->SetQuery($query);
            $result = $database->LoadObjectList();
            $f_iscale_data = ($result == null ? array() : $result);
            $ret_str .= "\t" . '<impscale_fields_count>' . count($f_iscale_data) . '</impscale_fields_count>' . "\n";
            if (count($f_iscale_data) > 0) {
                $ret_str .= "\t" . '<impscale_name><![CDATA[' . stripslashes($f_iscale_data[0]->iscale_name) . '&nbsp;]]></impscale_name>' . "\n";
                $ret_str .= "\t" . '<impscale_fields>' . "\n";
                foreach ($f_iscale_data as $is_row) {
                    $ret_str .= "\t\t" . '<impscale_field><isfield_text><![CDATA[' . stripslashes($is_row->isf_name) . '&nbsp;]]></isfield_text>' . "\n";
                    $ret_str .= "\t\t\t" . '<isfield_id>' . $is_row->id . '</isfield_id></impscale_field>' . "\n";
                }
                $ret_str .= "\t" . '</impscale_fields>' . "\n";
            }
        } else {
            $ret_str .= "\t" . '<impscale_fields_count>0</impscale_fields_count>' . "\n";
        }

        if (!(count($f_answ_data) > 0)) {
            $query = "SELECT * FROM #__survey_force_def_answers WHERE quest_id = '" . $q_data->id . "'  ";
            $database->SetQuery($query);
            $result = $database->LoadObjectList();
            $f_answ_data = ($result == null ? array() : $result);
        }

        if (count($f_answ_data) > 0) {
            $ret_str .= "\t" . '<answers>' . "\n";

            foreach ($f_answ_data as $answer) {
                $ret_str .= "\t\t" . '<a_quest_id>' . $answer->answer . '</a_quest_id>' . "\n";
                $ret_str .= "\t\t" . '<ans_id>' . $answer->ans_field . '</ans_id>' . "\n";
            }

            $ret_str .= "\t" . '</answers>' . "\n";
        }

        $ret_str .= "\t" . '<ans_count>' . intval(count($f_answ_data)) . '</ans_count>' . "\n";
        $query = "SELECT * FROM #__survey_force_user_answers_imp WHERE quest_id = '" . $q_data->id . "' and start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
        $result = $database->LoadObjectList();
        $f_answ_imp_data = ($result == null ? array() : $result);

        $ret_str .= "\t" . '<ans_imp_count>' . intval(count($f_answ_imp_data)) . '</ans_imp_count>' . "\n";

        if (count($f_answ_imp_data) > 0) {
            $ret_str .= "\t" . '<answers_imp>' . "\n";
            $ret_str .= "\t\t" . '<ans_imp_id>' . $f_answ_imp_data[0]->iscalefield_id . '</ans_imp_id>' . "\n";
            $ret_str .= "\t" . '</answers_imp>' . "\n";
        }

              
        $html = '';        

        $ret_str .= '<html><![CDATA[' . $html . ']]></html>';

        return $ret_str;
    }

   
    public function onNextPreviewQuestion(&$data) {

        return true;
    }

    
    

}