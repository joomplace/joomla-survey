<?php

/**
 * Survey Force Delux Short Answer Plugin for Joomla 3
 * @package Joomla.Plugin
 * @subpackage Survey.shortanswer
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyShortanswer {

    var $name = 'Shortanswer';
    var $_name = 'shortanswer';
    var $_type = 'survey';

    public function plgSurveyShortanswer() {
        return true;
    }
    
     public function onGetAdminOptions($data) {
        return false;
     }

    public function onSaveQuestion(&$data) {
        return true;
    }
    
    public function onGetAdminJavaScript() {

        return false;
    }

    public function onGetScriptJs() {

        $document = JFactory::getDocument();
        $document->addScript(JUri::root()."/plugins/survey/shortanswer/js/shortanswer.js");
    }

    public function onGetQuestionData(&$data) {

        $database = JFactory::getDbo();

        $q_data = $data['q_data'];
        $start_id = $data['start_id'];
		$iscale = array();
        $ret_str = '';

        // add answers section for prev/next
        $query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $q_data->id . "' AND start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
        $f_answ_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
		if (strpos($q_data->sf_qtext,'{x}') !== 0 || strpos($q_data->sf_qtext,'{y}') !== 0) {
			$inp = mb_substr_count($q_data->sf_qtext, '{x}')+mb_substr_count($q_data->sf_qtext, '{y}');
		}

        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
            $qsection_t = ($database->loadObject() == null ? array() : $database->loadObject());
            if (isset($qsection_t->addname) && intval($qsection_t->addname) > 0) {
				$q_data->sf_qtext = '<div class="sf_section_name">' . $qsection_t->sf_name . "</div><br/>" . $q_data->sf_qtext;
            }
        }

        $ret_str .= "\t\t" . '<quest_inp_count>' . $inp . '</quest_inp_count>' . "\n";
		if ( !$inp )
		{
			$ret_str .= "\t" . '<quest_text><![CDATA[' . SurveyforceHelper::sfPrepareText($q_data->sf_qtext) . '&nbsp;]]></quest_text>' . "\n";
		}

        $ret_str .= "\t" . '<quest_id>' . $q_data->id . '</quest_id>' . "\n";
        $ret_str .= "\t" . '<default_hided>' . (int) $q_data->sf_default_hided . '</default_hided>' . "\n";
        $ret_str .= "\t" . '<main_fields_count>0</main_fields_count>' . "\n";
        $ret_str .= "\t" . '<compulsory>' . $q_data->sf_compulsory . '</compulsory>' . "\n";
        $ret_str .= "\t" . '<sf_qstyle>' . (int) $q_data->sf_qstyle . '</sf_qstyle>' . "\n";
        $ret_str .= "\t" . '<factor_name><![CDATA[' . $q_data->sf_fieldtype . '&nbsp;]]></factor_name>' . "\n";
        $ret_str .= "\t" . '<sf_num_options>' . (int) $q_data->sf_num_options . '</sf_num_options>' . "\n";

        $f_iscale_data = array();
        if ($q_data->sf_impscale) { //important scale is SET
            $query = "SELECT a.iscale_name, b.* FROM #__survey_force_iscales as a, #__survey_force_iscales_fields as b WHERE a.id = '" . $q_data->sf_impscale . "' AND a.id = b.iscale_id ORDER BY b.ordering";
            $database->SetQuery($query);
            $f_iscale_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());            
        } 

        if (!(count($f_answ_data) > 0)) {
            $query = "SELECT * FROM #__survey_force_def_answers WHERE quest_id = '" . $q_data->id . "'  ";
            $database->SetQuery($query);
            $f_answ_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        }
		$iscale['answ_txt']= array();
				
        if (count($f_answ_data) > 0) {
            $ret_str .= "\t" . '<answers>' . "\n";

            foreach ($f_answ_data as $answer) {
					
                if ( $answer->ans_field > 0) {
                    $query = "SELECT ans_txt FROM #__survey_force_user_ans_txt WHERE id = '" . $answer->answer . "' and start_id = '" . $start_id . "' ";
                    $database->SetQuery($query);
                    $ans_txt = $database->loadResult();
					
                    if (strlen($ans_txt) < 1)
                        $ans_txt = ' ';
				array_push($iscale['answ_txt'], $ans_txt);
				$ret_str .= "\t\t" . '<ans_txt><![CDATA[' . $ans_txt . ']]></ans_txt>' . "\n";
                }
                $ret_str .= "\t\t" . '<a_quest_id>' . $answer->answer . '</a_quest_id>' . "\n";
            }
            if (!isset($ans_txt)) {
                $ans_txt = '!!!---!!!';
                $ret_str .= "\t\t" . '<ans_txt><![CDATA[' . $ans_txt . ']]></ans_txt>' . "\n";
            }


            $ret_str .= "\t" . '</answers>' . "\n";
        }

        $ret_str .= "\t" . '<ans_count>' . intval(count($f_answ_data)) . '</ans_count>' . "\n";
        $query = "SELECT * FROM #__survey_force_user_answers_imp WHERE quest_id = '" . $q_data->id . "' and start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
        $f_answ_imp_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());

        $ret_str .= "\t" . '<ans_imp_count>' . intval(count($f_answ_imp_data)) . '</ans_imp_count>' . "\n";

        if (count($f_answ_imp_data) > 0) {
            $ret_str .= "\t" . '<answers_imp>' . "\n";
            $ret_str .= "\t\t" . '<ans_imp_id>' . $f_answ_imp_data[0]->iscalefield_id . '</ans_imp_id>' . "\n";
            $ret_str .= "\t" . '</answers_imp>' . "\n";
        }

        $tmpl_name = SurveyforceHelper::getTemplate($data);
        $class_name = 'SF_' . ucfirst($data['quest_type']) . 'Template';

        if (!class_exists($class_name))
            if (file_exists(JPATH_SITE . '/plugins/survey/' . $data['quest_type'] . '/tmpl/' . $tmpl_name . '/template.php'))
                include_once JPATH_SITE . '/plugins/survey/' . $data['quest_type'] . '/tmpl/' . $tmpl_name . '/template.php';


        
        $iscale['impscale_name'] = (isset($f_iscale_data) && count($f_iscale_data)) ? $f_iscale_data[0]->iscale_name : '';
        $iscale['ans_imp_id'] = (isset($f_answ_imp_data) && count($f_answ_imp_data)) ? $f_answ_imp_data[0]->iscalefield_id : '';
        $iscale['ans_imp_count'] = intval(count($f_answ_imp_data));
		
		$iscale['isfield'] = array();

        if(isset($f_iscale_data) && count($f_iscale_data))
            foreach (@$f_iscale_data as $is_row) {
                array_push($iscale['isfield'], array(
                    'isfield_text' => $is_row->isf_name,
                    'isfield_id' => $is_row->id,
                ));
            }

        $class_name::$question = $q_data;
        $class_name::$iscale = $iscale;
        $html = $class_name::getQuestion();

		$q_data->sf_qtext = $class_name::$question->sf_qtext;

		if ( $inp )
			$ret_str .= "\t" . '<quest_text><![CDATA[' . SurveyforceHelper::sfPrepareText($q_data->sf_qtext) . '&nbsp;]]></quest_text>' . "\n";

        $ret_str .= '<html><![CDATA[' . $html . ']]></html>';

        return $ret_str;
    }
   

    public function onTotalScore(&$data) {       

        return true;
    }

    public function onScoreByCategory(&$data) {
       
        return true;
    }

    public function onNextPreviewQuestion(&$data) {
        
    }

    public function onReviewQuestion(&$data) {
        
    }

    public function onGetResult(&$data) {

        return true;
    }

    public function onStatisticContent(&$data) {
        return true;
    }

	public function onGetAdminReport($question, $start_data)
	{
		$database = JFactory::getDbo();
		$n = mb_substr_count($question->sf_qtext, "{x}")+mb_substr_count($question->sf_qtext, "{y}");
		if ($n > 0) {
			$query = "SELECT b.ans_txt, a.ans_field FROM #__survey_force_user_answers as a
						LEFT JOIN #__survey_force_user_ans_txt as b ON a.answer = b.id
				WHERE a.quest_id = '".$question->id."'
				AND a.survey_id = '".$question->sf_survey."'
				AND a.start_id = '".$start_data->id."' ORDER BY a.ans_field ";
			$database->SetQuery( $query );
			$ans_inf_data = ($database->LoadObjectList() == null? array(): $database->LoadObjectList());
			$result['answer'] = $ans_inf_data;
			$result['answer_count'] = $n;
		}
		else {
			$query = "SELECT b.ans_txt FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b
				WHERE a.quest_id = '".$question->id."'
				AND a.survey_id = '".$question->sf_survey."'
				AND a.start_id = '".$start_data->id."' AND a.answer = b.id";
			$database->SetQuery( $query );
			$ans_inf_data = $database->LoadResult();
			$result['answer'] = ($ans_inf_data == '')? JText::_('COM_SURVEYFORCE_NO_ANSWER'):$ans_inf_data;
		}

		return $result;
	}
    

}