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

class plgSurveyRankingdropdown {

    var $name = 'rankingdropdown';
    var $_name = 'rankingdropdown';
    var $_type = 'survey';

    public function __construct() {
        return true;
    }

	public function onGetDefaultForm($lists){

		ob_start();
		?>
		<div>
			<table class='table table-striped' width="100%">
				<tr>
					<th valign="top" class="title" colspan="2"><?php echo $lists['row']->sf_qtext?></th>
				</tr>
				<?php foreach($lists['main_data'] as $k => $main) { ?>
					<tr>
						<td><?php echo $main->ftext?></td>
						<td>
							<input type="hidden" name="main_data[]" value="<?php echo $main->id; ?>" />
							<select name="query_select_<?php echo $main->id; ?>" style="width: 200px">
								<?php foreach($lists['second_data'] as $second) {
									$selected = '';
									if ( $lists['answer_data'][$main->id]['ans_field'] == $second->id )
										$selected = ' selected ';

									echo '<option value="'.$second->id.'" '.$selected.'>'.$second->ftext.'</option>'."\n";
									?>
								<?php } ?>
							</select>
						</td>
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
		if ( !empty($data['main_data']) )
		{
			foreach ($data['main_data'] as $main_id)
			{
				$ans_field = $data['query_select_'.$main_id];
				$query = "INSERT INTO `#__survey_force_def_answers` (`survey_id`, `quest_id`, `answer`, `ans_field`) VALUES (".$data['survey_id'].", ".$data['id'].", ".$main_id.", ".$ans_field.")";
				$database->setQuery($query);
				$database->execute();
			}
		}

		return true;

	}

	public function onGetQuestionData(&$data) {

        $database = JFactory::getDbo();

        $q_data = $data['q_data'];
        $start_id = $data['start_id'];

        $ret_str = '';
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $q_data->id . "' and is_main = '1' ORDER BY ordering";
        $database->SetQuery($query);
		$res = $database->LoadObjectList();
        $f_main_data = ($res == null ? array() : $res);
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $q_data->id . "' and is_main = '0' ORDER BY ordering";
        $database->SetQuery($query);
		$res = $database->LoadObjectList();
        $f_alt_data = ($res == null ? array() : $res);

		if ($q_data->is_shuffle) {
			shuffle($f_main_data);
			shuffle($f_alt_data);
		}

		// add answers section for prev/next
        $query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $q_data->id . "' AND start_id = '" . $start_id . "' ";
        $database->SetQuery($query);
		$res = $database->LoadObjectList();
        $f_answ_data = ($res == null ? array() : $res);

        $ret_str .= "\t" . '<quest_type>' . $q_data->sf_qtype . '</quest_type>' . "\n";
        $inp = 0;
        $q_text = $q_data->sf_qtext;
        if ($q_data->sf_qtype == 4) {
            if (strpos($q_text, '{x}') > 0 || strpos($q_text, '{y}') > 0) {
                $inp = mb_substr_count($q_text, '{x}') + mb_substr_count($q_text, '{y}');
            }
        }

        if ($q_data->sf_section_id > 0) {
            $query = "SELECT `addname`, `sf_name` FROM `#__survey_force_qsections` WHERE `id` = '" . $q_data->sf_section_id . "' ";
            $database->SetQuery($query);
			$res = $database->LoadObjectList();
            $qsection_t = ($res == null ? array() : $res);
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
			$res = $database->LoadObjectList();
            $f_iscale_data = ($res == null ? array() : $res);
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
			$res = $database->LoadObjectList();
            $f_answ_data = ($res == null ? array() : $res);
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
		$res = $database->LoadObjectList();
        $f_answ_imp_data = ($res == null ? array() : $res);

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
        $iscale['ans_imp_id'] = (isset($f_answ_imp_data) && count($f_answ_imp_data)) ? $f_answ_imp_data[0]->iscalefield_id : '';
        $iscale['ans_imp_count'] = intval(count($f_answ_imp_data));
        $iscale['alt_fields_count'] = intval(count($f_alt_data));
        $iscale['main_fields_count'] = intval(count($f_main_data));
        $iscale['ans_count'] = intval(count($f_answ_data));
        $iscale['isfield'] = array();
        $iscale['afield'] = array();
        $iscale['answers'] = array();
        $iscale['mfield'] = array();

        if(isset($f_iscale_data) && count($f_iscale_data)){
            foreach ($f_iscale_data as $is_row) {
                array_push($iscale['isfield'], array(
                    'isfield_text' => $is_row->isf_name,
                    'isfield_id' => $is_row->id,
                ));
            }
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

    public function onSaveQuestion(&$data) {

		$database = JFactory::getDbo();
		$mainframe = JFactory::getApplication();

		$field_order = 0;
			$sf_hid_fields_rank = (!empty($_POST['sf_hid_fields_rank'])) ? $_POST['sf_hid_fields_rank'] : array();
			$sf_hid_field_rank_ids = JFactory::getApplication()->input->get('sf_hid_field_rank_ids', '', 'array', array(0));
			$old_sf_hid_field_rank_ids = JFactory::getApplication()->input->get('old_sf_hid_field_rank_ids', '', 'array', array(0));
			$old_sf_hid_field_rank_ids = @array_merge(array(0 => 0), $old_sf_hid_field_rank_ids);
			
			for ($i = 0, $n = count($old_sf_hid_field_rank_ids); $i < $n; $i++) {
				if (in_array($old_sf_hid_field_rank_ids[$i], $sf_hid_field_rank_ids))
					unset($old_sf_hid_field_rank_ids[$i]);
			}

			if(count($old_sf_hid_field_rank_ids)){
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '".$data['qid']."' AND id IN ( ".implode(', ', $old_sf_hid_field_rank_ids)." )";
				$database->setQuery($query);
				$database->execute();
			}

			for ($i = 0, $n = count($sf_hid_fields_rank); $i < $n; $i++) {

				$r_row = $sf_hid_fields_rank[$i];
				$new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());
				if ($sf_hid_field_rank_ids[$i] > 0 ) {
					$new_field->id = $sf_hid_field_rank_ids[$i];
				}
				$new_field->quest_id = $data['qid'];
				$new_field->ftext = SurveyforceHelper::SF_processGetField($r_row);
				$new_field->alt_field_id = 0;
				$new_field->is_main = 0;
				$new_field->ordering = $field_order;
				$new_field->is_true = 1;

				if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }

				$field_order ++ ;
			}

		$field_order = 0;
			// FIELDS
			$sf_hid_fields = (!empty($_POST['sf_hid_fields'])) ? $_POST['sf_hid_fields'] : array();
			$sf_hid_field_ids = JFactory::getApplication()->input->get('sf_hid_field_ids', '', 'array', array(0));
			$old_sf_hid_field_ids = JFactory::getApplication()->input->get('old_sf_hid_field_ids', '', 'array', array(0));
			$old_sf_hid_field_ids = @array_merge(array(0 => 0), $old_sf_hid_field_ids);

			for ($i = 0, $n = count($old_sf_hid_field_ids); $i < $n; $i++) {
				if (in_array($old_sf_hid_field_ids[$i], $sf_hid_field_ids))
					unset($old_sf_hid_field_ids[$i]);
			}

			if(count($old_sf_hid_field_ids)){
				$query = "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '".$data['qid']."' AND id IN ( ".implode(', ', $old_sf_hid_field_ids)." )";
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
				$rules_ar = array();
				while ($j < $data['rules_count']) {
					if ($rules_ar[$j]->rul_txt == $new_field->ftext) {
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}
				$field_order ++ ;
			}

		if ($database->errorMsg()) {

			return $database->errorMsg();
		}
		else
			return $data;
    }

   
    public function onTotalScore(&$data) {

        return true;
    }

    public function onScoreByCategory(&$data) {

        return true;
    }

    public function onNextPreviewQuestion(&$data) {

        return true;
    }

    public function onReviewQuestion(&$data) {


        return true;
    }

    public function onGetResult(&$data) {

        return true;
    }

    //Administration part

     public static function onGetAdminOptions($data, $lists) {

		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$row = $data['item'];

		$q_om_type = $row->sf_qtype;
		$sf_num_options = $row->sf_num_options;
		$mainframe = JFactory::getApplication();
		$sessions = JFactory::getSession();

		$is_return = $sessions->get('is_return_sf') > 0 ? true : false;
		$id = (isset($data['id'])) ? $data['id'] : '';

		$lists['sf_fields_rank'] = array();
		$query = "SELECT * FROM `#__survey_force_fields` WHERE `quest_id` = '" . $row->id . "' AND is_main = 0 ORDER BY ordering";
		$database->SetQuery($query);
		$res = $database->LoadObjectList();
		$lists['sf_fields_rank'] = ($res == null ? array() : $res);

		$fields_rank = JHtmlSelect::genericlist($lists['sf_fields_rank'], 'sf_list_rank_fields', 'class="text_area" size="1" id="sf_list_rank_fields"', 'ftext', 'ftext', 0);
		$lists['sf_list_rank_fields'] = $fields_rank;

		if ($is_return) {
			$lists['sf_fields_rank'] = array();
			$sf_hid_rank = $sessions->get('sf_hid_rank_sf');
			$sf_hid_rank_id = $sessions->get('sf_hid_rank_id_sf');
			for ($i = 0, $n = count($sf_hid_rank); $i < $n; $i++) {
				$tmp = new stdClass();
				$tmp->id = $sf_hid_rank_id[$i];
				$tmp->ordering = 0;
				$tmp->quest_id = 0;
				$tmp->ftext = $sf_hid_rank[$i];
				$lists['sf_fields_rank'][] = $tmp;
			}
		}

		$lists['sf_fields'] = array();
		$query = "SELECT * FROM `#__survey_force_fields` WHERE quest_id = '" . $row->id . "' AND is_main = 1 ORDER BY ordering";
		$database->SetQuery($query);
		$res = $database->LoadObjectList();
		$lists['sf_fields'] = ($res == null ? array() : $res);
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

		// Surveys with linkscape
		$query = "SELECT id, sf_name FROM #__survey_force_survs";
		$database->SetQuery($query);
		$SurvsList = $database->loadAssocList();
		foreach ( $SurvsList as $k =>$survs )
		{
			$database->setQuery("SELECT id, sf_qtext FROM #__survey_force_quests WHERE sf_survey = ".$survs['id'].' AND sf_qtype = 1 AND id <> '.(int)$id);
			$quests = $database->loadAssocList();
			if ( !$quests )
				unset($SurvsList[$k]);
			else
				$SurvsList[$k]['quests'] = $quests;
		}

        ob_start();
        require_once(JPATH_SITE . "/plugins/survey/rankingdropdown/admin/js/rankingdropdown.js.php");
        require_once(JPATH_SITE . "/plugins/survey/rankingdropdown/admin/options/rankingdropdown.php");
        $options = ob_get_clean();


        return $options;
    }

    public function onGetAdminJavaScript() {

        $document = JFactory::getDocument();
        $document->addScript(JUri::root()."/plugins/survey/rankingdropdown/admin/js/rankingdropdown.js");
    }

    public function onAdminSaveOptions(&$data) {
        return true;
    }

    public function onGetAdminAddLists(&$data) {

        return true;
    }

	public static function onGetAdminReport($question, $start_data)
	{
		$database = JFactory::getDbo();

		$query = "SELECT a.* , b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c )
			LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.next_quest_id = b.id AND c.sf_qtype = 9 )
			WHERE c.published = 1 AND a.quest_id = '".$question->id."' AND a.survey_id = '".$question->sf_survey."'	AND a.start_id = '".$start_data->id."' AND c.id = a.quest_id";
		$database->SetQuery( $query );
		$ans_inf_data = ($database->LoadObjectList());

		$result['answer'] = array();
		$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '".$question->id."'"
			. "\n and is_main = 1 ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp_data = ($database->LoadObjectList());
		$j = 0;

		while ( $j < count($tmp_data) ) {
			$result['answer'][$j] = array();
			$result['answer'][$j]['num'] = $j;
			$result['answer'][$j]['f_id'] = $tmp_data[$j]->id;
			$result['answer'][$j]['f_text'] = $tmp_data[$j]->ftext;
			$result['answer'][$j]['alt_text'] = ($question->sf_qtype == 9?'':JText::_('COM_SURVEYFORCE_NO_ANSWER'));
			foreach ($ans_inf_data as $ans_data) {
				if ($ans_data->answer == $tmp_data[$j]->id) {
					$result['answer'][$j]['f_text'] = $tmp_data[$j]->ftext .($ans_data->ans_txt != '' ?' ('.$ans_data->ans_txt.')':'');
					$query = "SELECT * FROM #__survey_force_fields WHERE id = '".$ans_data->ans_field."'"
						. "\n and quest_id = '".$question->id."'"
						. "\n and is_main = 0 ORDER BY ordering";
					$database->SetQuery( $query );
					$alt_data = ($database->LoadObjectList());
					if (count($alt_data) > 0 ) {
						$result['answer'][$j]['alt_text'] = ($ans_data->ans_field==0?($question->sf_qtype == 9?'':JText::_('COM_SURVEYFORCE_NO_ANSWER')):$alt_data[0]->ftext);
						$result['answer'][$j]['alt_id'] = $ans_data->ans_field;
					}
				}
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