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

    public function __construct() {
        return true;
    }
    
     public static function onGetAdminOptions($data, $lists) {

         $database = JFactory::getDBO();
         $row = $data['item'];
         $q_om_type = $row->sf_qtype;
         $sf_num_options = $row->sf_num_options;
         $sessions = JFactory::getSession();
         $query = $database->getQuery(true);
         $id = (isset($data['id'])) ? $data['id'] : '';

         $is_return = $sessions->get('is_return_sf') > 0 ? true : false;


         $lists['sf_fields_rule'] = array();
         $query = "SELECT b.ftext, c.sf_qtext, c.id as next_quest_id, a.priority, d.ftext as alt_ftext  "
             . "\n FROM #__survey_force_rules as a, #__survey_force_fields as b, #__survey_force_quests as c, #__survey_force_fields as d"
             . "\n WHERE a.quest_id = '" . $row->id . "' and a.answer_id = b.id and a.next_quest_id = c.id and a.alt_field_id = d.id";
         $database->SetQuery($query);
         $result = $database->LoadObjectList();
         $lists['sf_fields_rule'] = ($result == null ? array() : $result);

         $lists['sf_fields'] = array();
         $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' AND is_main = 0 ORDER BY ordering";
         $database->SetQuery($query);
         $lists['sf_fields'] = $database->LoadObjectList();
         if ($is_return) {
             $lists['sf_fields'] = array();
             $sf_fields = $sessions->get('sf_fields_sf');
             $sf_field_ids = $sessions->get('sf_field_ids_sf');
             for ($i = 0, $n = count($sf_fields); $i < $n; $i++) {
                 $tmp = new stdClass();
                 $tmp->id = $sf_field_ids[$i];
                 $tmp->ftext = $sf_fields[$i];
                 $lists['sf_fields'][] = $tmp;
             }
         }
         $list_fields = JHtmlSelect::genericlist($lists['sf_fields'], 'sf_field_list', 'class="text_area" id="sf_field_list" size="1" ', 'ftext', 'ftext', 0);
         $lists['sf_list_fields'] = $list_fields;

         $lists['sf_alt_fields'] = array();
         $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' AND is_main = 1 ORDER BY ordering";
         $database->SetQuery($query);
         $lists['sf_alt_fields'] = $database->LoadObjectList();
         if ($is_return) {
             $lists['sf_alt_fields'] = array();
             $sf_alt_fields = $sessions->get('sf_alt_fields_sf');
             $sf_alt_field_ids = $sessions->get('sf_alt_field_ids_sf');
             for ($i = 0, $n = count($sf_alt_fields); $i < $n; $i++) {
                 $tmp = new stdClass();
                 $tmp->id = $sf_alt_field_ids[$i];
                 $tmp->ftext = $sf_alt_fields[$i];
                 $lists['sf_alt_fields'][] = $tmp;
             }
         }
         $list_fields = JHtmlSelect::genericlist($lists['sf_alt_fields'], 'sf_alt_field_list', 'class="text_area" id="sf_alt_field_list" size="1" ', 'ftext', 'ftext', 0);
         $lists['sf_alt_field_list'] = $list_fields;

         $sf_fields = $sf_fields_full = array();
         $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' and is_main = 0 ORDER BY ordering";
         $database->SetQuery($query);
         $sf_fields = $database->LoadObjectList();
         $ii = 0;
         foreach ($sf_fields as $qrow) {
             $sf_fields_full[$ii]= new stdClass();
             $sf_fields_full[$ii]->id = $qrow->id;
             $sf_fields_full[$ii]->quest_id = $qrow->quest_id;
             $sf_fields_full[$ii]->ftext = $qrow->ftext;
             $sf_fields_full[$ii]->alt_field_id = $qrow->alt_field_id;
             $database->SetQuery("SELECT ftext FROM #__survey_force_fields WHERE is_main = 1 and quest_id = '" . $qrow->quest_id . "' and `id` = '" . $qrow->alt_field_id . "'");
             $sf_fields_full[$ii]->alt_field_full = $database->LoadResult();
             $sf_fields_full[$ii]->is_main = $qrow->is_main;
             $sf_fields_full[$ii]->is_true = $qrow->is_true;
             $ii++;
         }

         $lists['sf_fields'] = $sf_fields_full;
         if ($is_return) {
             $lists['sf_fields'] = array();
             $sf_fields = $sessions->get('sf_fields_sf');
             $sf_field_ids = $sessions->get('sf_field_ids_sf');
             $sf_alt_fields = $sessions->get('sf_alt_fields_sf');
             $sf_alt_field_ids = $sessions->get('sf_alt_field_ids_sf');
             for ($i = 0, $n = count($sf_fields); $i < $n; $i++) {
                 $tmp = new stdClass();
                 $tmp->ftext = $sf_fields[$i];
                 $tmp->id = $sf_field_ids[$i];
                 $tmp->alt_field_full = $sf_alt_fields[$i];
                 $tmp->alt_field_id = $sf_alt_field_ids[$i];
                 $lists['sf_fields'][] = $tmp;
             }
         }

         $lists['sf_fields_rule'] = array();
         $query = "SELECT b.ftext, c.sf_qtext, c.id as next_quest_id, a.priority, d." . 'f' . "text as alt_ftext "
             . "\n FROM  #__survey_force_fields as b, #__survey_force_quests as c, #__survey_force_rules as a LEFT JOIN " . "#__survey_force_fields as d " . " ON a.alt_field_id = d.id "
             . "\n WHERE a.quest_id = '" . $row->id . "' and a.answer_id <> 9999997 and a.answer_id = b.id and a.next_quest_id = c.id ";
         $database->setQuery($query);
         $result = $database->LoadObjectList();
         $lists['sf_fields_rule'] = ($result == null ? array() : $result);
         $database->getQuery(true);
         $query = "SELECT a.*, c.sf_qtext, c.sf_qtype, c.id AS qid,  d.ftext AS aftext, e.stext AS astext, b.ftext AS qoption, b.id AS bid, d.id AS fdid, e.id AS sdid FROM  #__survey_force_fields AS b, #__survey_force_quests AS c, #__survey_force_quest_show AS a LEFT JOIN #__survey_force_fields AS d ON a.ans_field = d.id LEFT JOIN #__survey_force_scales AS e ON a.ans_field = e.id WHERE a.quest_id = '" . $row->id . "' AND a.answer = b.id AND a.quest_id_a = c.id ";
         $database->setQuery($query);
         $result = $database->LoadObjectList();
         $lists['quest_show'] = ($result == null ? array() : $result);

         $query = "SELECT id AS value, sf_qtext AS text"
             . "\n FROM #__survey_force_quests WHERE id <> '" . $id . "' AND sf_qtype <> 8 "
             . ($row->sf_survey ? "\n and sf_survey = '" . $row->sf_survey . "'" : '')
             . "\n ORDER BY ordering, id "
         ;
         $database->setQuery($query);
         $result = $database->LoadObjectList();
         $quests = ($result == null ? array() : $result);
         $i = 0;
         while ($i < count($quests)) {
             $quests[$i]->text = strip_tags($quests[$i]->text);
             if (strlen($quests[$i]->text) > 55)
                 $quests[$i]->text = mb_substr($quests[$i]->text, 0, 55) . '...';
             $quests[$i]->text = $quests[$i]->value . ' - ' . $quests[$i]->text;
             $i++;
         }
         $lists['quests'] = JHtmlSelect::genericlist($quests, 'sf_quest_list', 'class="text_area" id="sf_quest_list" size="1" onchange="javascript: showOptions(this.value);" ', 'value', 'text', 0);


         $query = "SELECT next_quest_id "
             . "\n FROM #__survey_force_rules WHERE quest_id = '" . $row->id . "' and answer_id = 9999997 ";
         $database->setQuery($query);
         $squest = (int) $database->loadResult();
         $quest = JHtmlSelect::genericlist($quests, 'sf_quest_list2', 'class="text_area" id="sf_quest_list2" size="1" ', 'value', 'text', $squest);
         $lists['quests2'] = $quest;
         $lists['checked'] = '';
         if ($squest)
             $lists['checked'] = ' checked = "checked" ';

         ob_start();
         include_once(JPATH_SITE . "/plugins/survey/shortanswer/admin/js/shortanswer.js.php");
         include_once(JPATH_SITE . "/plugins/survey/shortanswer/admin/options/shortanswer.php");
         $options = ob_get_clean();


         return $options;
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
        $result = $database->LoadObjectList();
        $f_answ_data = ($result == null ? array() : $result);

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
		if (strpos($q_data->sf_qtext,'{x}') !== 0 || strpos($q_data->sf_qtext,'{y}') !== 0) {
			$inp = mb_substr_count($q_data->sf_qtext, '{x}')+mb_substr_count($q_data->sf_qtext, '{y}');
		}

        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
            $qsection_t = ($database->loadObject());
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
            $result = $database->LoadObjectList();
            $f_iscale_data = ($result == null ? array() : $result);            
        } 

        if (!(count($f_answ_data) > 0)) {
            $query = "SELECT * FROM #__survey_force_def_answers WHERE quest_id = '" . $q_data->id . "'  ";
            $database->SetQuery($query);
            $result = $database->LoadObjectList();
            $f_answ_data = ($result == null ? array() : $result);
        }
		$iscale['answ_txt']= array();
		
		$answers_text = array();
        if (count($f_answ_data) > 0) {
            $ret_str .= "\t" . '<answers>' . "\n";

            $reg_voting = $data['survey']->sf_reg_voting;

            foreach ($f_answ_data as $answer) {

                switch ($reg_voting) {
                    case (2):
                    case (3):
                        $query = "SELECT ans_txt FROM #__survey_force_user_ans_txt WHERE id = '" . $answer->answer . "' and start_id = '" . $start_id . "' ";
                        $database->SetQuery($query);
                        $ans_txt = $database->loadResult();
                        break;
                    default:
                        $ans_txt = ' ';
                }
				
				if (strlen($ans_txt) < 1)
					$ans_txt = ' ';
				array_push($iscale['answ_txt'], $ans_txt);
				$answers_text[$answer->quest_id] = $ans_txt;
				$ret_str .= "\t\t" . '<ans_txt><![CDATA[' . $ans_txt . ']]></ans_txt>' . "\n";
					
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
        $result = $database->LoadObjectList();
        $f_answ_imp_data = ($result == null ? array() : $result);

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

        $class_name::$answer = $answers_text;
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

	public static function onGetAdminReport($question, $start_data)
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
			$ans_inf_data = ($database->LoadObjectList());
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