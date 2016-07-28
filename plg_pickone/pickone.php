<?php
/**
 * Survey Force Deluxe Pickone Plugin for Joomla 3
 * @package Joomla.Plugin
 * @subpackage Survey.pickone
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyPickone {

    var $name = 'Pickone';
    var $_name = 'pickone';
    var $_type = 'survey';

    public function plgSurveyPickone() {
        return true;
    }

    public function onGetDefaultForm($lists){

        ob_start();
        ?>
        <div>
        <table width='100%' class='table table-striped'>
        <tr>
            <th valign="top" class="title" colspan="2"><?php echo $lists['row']->sf_qtext?></th>
        </tr>
        <?php foreach($lists['main_data'] as $main) {
                $selected = '';
                
                if(count($lists['answer_data']))
                if (isset($lists['answer_data'][$main->id]))
                    $selected = ' checked="checked" ';
         ?>
            <tr><td width='20px' align='left'>
                    <input type='radio' name='quest_radio' <?php echo $selected?> value='<?php echo $main->id?>'>
                </td>
                <td align='left'><?php echo $main->ftext?><br></td>
            </tr>
        <?php } ?>
        </table>
        </div>
        <?php

        $content = ob_get_contents();
        ob_clean();

        return $content;

    }

    public function onSaveDefault(&$data){

        
        $database = JFactory::getDBO();
        $ans_id = $data['quest_radio'];

        $query = "INSERT INTO `#__survey_force_def_answers` (`survey_id`, `quest_id`, `answer`, `ans_field`) VALUES (".$data['survey_id'].", ".$data['id'].", ".$ans_id.", 0)";
        $database->setQuery($query);
        $database->execute();

        return true;
        
    }

    public function onSaveQuestion(&$data) {

        $database = JFactory::getDbo();
        $mainframe = JFactory::getApplication();
        $rules_ar = $data['rules_ar'];

        $field_order = 0;
        $other_option_cb = intval(JFactory::getApplication()->input->get('other_option_cb', 0));

        $sf_hid_fields = (!empty($_POST['sf_hid_fields'])) ? $_POST['sf_hid_fields'] : array();
        $sf_hid_field_ids = JFactory::getApplication()->input->get('sf_hid_field_ids', '', 'array', array(0));
        $old_sf_hid_field_ids = JFactory::getApplication()->input->get('old_sf_hid_field_ids', '', 'array', array(0));
        $old_sf_hid_field_ids = @array_merge(array(0 => 0), $old_sf_hid_field_ids);

        for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++) {
            if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
                unset($old_sf_hid_field_ids[$i]);
        }
        
        if(count($old_sf_hid_field_ids)){
            $query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '".$data['qid']."' AND ( id IN ( ".implode(', ', $old_sf_hid_field_ids)." ) ".($other_option_cb != 2? ' OR is_main = 0 ': '')." )";
            $database->setQuery($query);
            $database->execute();
        }

        for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++) {
            
            $f_row = $sf_hid_fields[$i];
            $new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());
            if ($sf_hid_field_ids[$i] > 0 ) {
                $new_field->id = $sf_hid_field_ids[$i];
            }
            $new_field->quest_id = $data['qid'];
            $new_field->ftext = SurveyforceHelper::SF_processGetField($f_row);
            $new_field->alt_field_id = 0;
            $new_field->is_main = 1;
            $new_field->ordering = $field_order;
            $new_field->is_true = 1;


            if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
            if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
            $j = 0;
            while ($j < $data['rules_count']) {         
                if ($rules_ar[$j]->rul_txt == $new_field->ftext) {              
                    $rules_ar[$j]->answer_id = $new_field->id;
                }
                $j++;
            }
            $field_order ++ ;
        }

        if ($other_option_cb == 2) {

            $other_text = $_POST['other_option'];
            $other_id = JFactory::getApplication()->input->get('other_op_id', 0);

            $new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());
            if ($other_id > 0 ) {
                $new_field->id = $other_id;
            }
            $new_field->quest_id = $data['qid'];
            $new_field->ftext = SurveyforceHelper::SF_processGetField($other_text);
            $new_field->alt_field_id = 0;
            $new_field->is_main = 0;
            $new_field->ordering = $field_order;
            $new_field->is_true = 1;
            if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
            if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
            $j = 0;
            while ($j < $data['rules_count']) {         
                if ($rules_ar[$j]->rul_txt == $new_field->ftext) {              
                    $rules_ar[$j]->answer_id = $new_field->id;
                }
                $j++;
            }
        }

        $data['rules_ar'] = $rules_ar;
        if ($database->getErrorMsg()) {

            return $database->getErrorMsg();
        }
        else
            return $data;
    }

    public function onGetQuestionData(&$data) {

        $database = JFactory::getDbo();

        $q_data = $data['q_data'];
        $start_id = $data['start_id'];

        $ret_str = '';
        $query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '" . $q_data->id . "' and is_main = '1' ORDER BY ordering";
        $database->SetQuery($query);
        $f_main_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        $query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '" . $q_data->id . "' and is_main = '0' ORDER BY ordering";
        $database->SetQuery($query);
        $f_alt_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        shuffle($f_alt_data);

        // add answers section for prev/next
        $query = "SELECT * FROM `#__survey_force_user_answers` WHERE `quest_id` = '" . $q_data->id . "' AND start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
        $f_answ_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
        $q_text = $q_data->sf_qtext;


        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
            $qsection_t = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
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
            $f_iscale_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        }

        if (!(count($f_answ_data) > 0)) {
            $query = "SELECT * FROM #__survey_force_def_answers WHERE quest_id = '" . $q_data->id . "'  ";
            $database->SetQuery($query);
            $f_answ_data = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        }

        if (count($f_answ_data) > 0) {
            $ret_str .= "\t" . '<answers>' . "\n";


            $query = "SELECT ans_txt FROM #__survey_force_user_ans_txt WHERE id = '" . $f_answ_data[0]->ans_field . "' and start_id = '" . $start_id . "' ";
            $database->SetQuery($query);
            $ans_txt = $database->loadResult();
            if (strlen($ans_txt) < 1)
                $ans_txt = '!!!---!!!';
            $ret_str .= "\t\t" . '<ans_txt><![CDATA[' . $ans_txt . ']]></ans_txt>' . "\n";
            $ret_str .= "\t\t" . '<a_quest_id>' . $f_answ_data[0]->answer . '</a_quest_id>' . "\n";

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

        // Copy this code for new plugin
        $tmpl_name = SurveyforceHelper::getTemplate($data);
        $class_name = 'SF_' . ucfirst($data['quest_type']) . 'Template';

       
         if (!class_exists($class_name))
            if (file_exists(JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php'))
                include_once JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php';

        $iscale = array();
        $iscale['impscale_name'] = (isset($f_iscale_data) && count($f_iscale_data)) ? $f_iscale_data[0]->iscale_name : '';
        $iscale['ans_imp_id'] = (isset($f_answ_imp_data) && count($f_answ_imp_data)) ? $f_answ_imp_data[0]->iscalefield_id : 0;
        $iscale['ans_imp_count'] = intval(count($f_answ_imp_data));
        $iscale['alt_fields_count'] = intval(count($f_alt_data));
        $iscale['main_fields_count'] = intval(count($f_main_data));
        $iscale['ans_count'] = intval(count($f_answ_data));
        $iscale['isfield'] = array();
        $iscale['afield'] = array();
        $iscale['answers'] = array();
        $iscale['mfield'] = array();

        if (isset($f_iscale_data) && count($f_iscale_data))
            foreach ($f_iscale_data as $is_row) {
                array_push($iscale['isfield'], array(
                    'isfield_text' => $is_row->isf_name,
                    'isfield_id' => $is_row->id,
                ));
            }

        foreach ($f_alt_data as $is_row) {
            array_push($iscale['afield'], array(
                'afield_text' => $is_row->ftext,
                'afield_id' => $is_row->id,
            ));
        }

        foreach ($f_answ_data as $is_row) {
            array_push($iscale['answers'], array(
                'a_quest_id' => $is_row->answer,
                'ans_id' => $is_row->ans_field,
            ));
        }

        foreach ($f_main_data as $is_row) {
            array_push($iscale['mfield'], array(
                'mfield_text' => $is_row->ftext,
                'mfield_is_true' => $is_row->is_true,
                'mfield_id' => $is_row->id,
            ));
        }

        $class_name::$question = $q_data;
        $class_name::$iscale = $iscale;
        $html = $class_name::getQuestion();

        $ret_str .= '<html><![CDATA[' . $html . ']]></html>';

        //End

        return $ret_str;
    }

    //Administration part

    public function onGetAdminOptions($data, $lists) {

        $my = JFactory::getUser();
        $database = JFactory::getDBO();
        $row = $data['item'];
        
        $q_om_type = $row->sf_qtype;
        $mainframe = JFactory::getApplication();
        $sessions = JFactory::getSession();

        $is_return = $sessions->get('is_return_sf') > 0 ? true : false;
        $id = (isset($data['id'])) ? $data['id'] : '';

        $lists['sf_fields_scale'] = array();
        $query = "SELECT * FROM `#__survey_force_scales` WHERE `quest_id` = '" . $row->id . "' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['sf_fields_scale'] = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());

        $fields_scale = JHtmlSelect::genericlist($lists['sf_fields_scale'], 'sf_list_scale_fields', 'class="text_area" size="1" id="sf_list_scale_fields"', 'stext', 'stext', 0);
        $lists['sf_list_scale_fields'] = $fields_scale;

        if ($is_return) {
            $lists['sf_fields_scale'] = array();
            $sf_hid_scale = $sessions->get('sf_hid_scale_sf');
            $sf_hid_scale_id = $sessions->get('sf_hid_scale_id_sf');
            for ($i = 0, $n = count($sf_hid_scale); $i < $n; $i++) {
                $tmp = new stdClass();
                $tmp->id = $sf_hid_scale_id[$i];
                $tmp->ordering = 0;
                $tmp->quest_id = 0;
                $tmp->stext = $sf_hid_scale[$i];
                $lists['sf_fields_scale'][] = $tmp;
            }
        }

        $lists['sf_fields'] = array();
        $query = "SELECT * FROM `#__survey_force_fields` WHERE quest_id = '" . $row->id . "' ORDER BY ordering";
        $database->SetQuery($query);
        $lists['sf_fields'] = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList());
        if ($is_return) {
            $lists['sf_fields'] = array();
            $sf_hid_fields = $sessions->get('sf_hid_fields_sf');
            $sf_hid_field_ids = $sessions->get('sf_hid_field_ids_sf');
            for ($i = 0, $n = count($sf_hid_fields); $i < $n; $i++) {
                $tmp = new stdClass();
                $tmp->id = $sf_hid_field_ids[$i];
                $tmp->ftext = $sf_hid_fields[$i];
                $lists['sf_fields'][] = $tmp;
            }
            if ($sessions->get('other_option_cb_sf') == 2)
                $lists['other_option'] = 1;
            else
                $lists['other_option'] = 0;

            $tmp = new stdClass();
            $tmp->id = $sessions->get('other_op_id_sf');
            $tmp->ftext = $sessions->get('other_option_sf');
            $tmp->is_main = 0;
            $lists['sf_fields'][] = $tmp;
        }

        $list_fields = JHtmlSelect::genericlist($lists['sf_fields'], 'sf_field_list', 'class="text_area" id="sf_field_list" size="1" ', 'ftext', 'ftext', 0);
        $lists['sf_list_fields'] = $list_fields;

        ob_start();
        include_once(JPATH_SITE . "/plugins/survey/pickone/admin/js/pickone.js.php");
        include_once(JPATH_SITE . "/plugins/survey/pickone/admin/options/pickone.php");
        $options = ob_get_contents();
        ob_clean();

        return $options;
    }

    public function onAdminSaveOptions(&$data) {

        return true;
    }

    public function onGetAdminAddLists(&$data) {



        return true;
    }

	public function onGetAdminReport($question, $start_data)
	{
		$database = JFactory::getDbo();
		$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 2 ) 
		WHERE c.published = 1 AND a.quest_id = '".$question->id."' AND a.survey_id = '".$question->sf_survey."' AND a.start_id = '".$start_data->id."' AND c.id = a.quest_id ";
		$database->SetQuery( $query );
		$ans_inf = ($database->LoadObjectList() == null? array(): $database->LoadObjectList());

		$result = array();
		$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '".$question->id."'"
			. "\n ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp_data = ($database->LoadObjectList() == null? array(): $database->LoadObjectList());
		$j = 0;
		while ( $j < count($tmp_data) ) {
			$result[$j] = array();
			$result[$j]['num'] = $j;
			$result[$j]['f_id'] = $tmp_data[$j]->id;
			$result[$j]['f_text'] = $tmp_data[$j]->ftext;
			$result[$j]['alt_text'] = '';
			if (count($ans_inf) > 0 && $ans_inf[0]->answer == $tmp_data[$j]->id) {
				$result[$j]['f_text'] = $tmp_data[$j]->ftext.($ans_inf[0]->ans_txt != '' ?' ('.$ans_inf[0]->ans_txt.')':'');
				$result[$j]['alt_text'] = '1';
				$result[$j]['alt_id'] = $ans_inf;
			}
			$j ++;
		}

		return $result;
	}

    public function onGetAdminQuestionData(&$data) {

        return true;
    }

    public function onGetAdminCsvData(&$data) {



        return true;
    }

}