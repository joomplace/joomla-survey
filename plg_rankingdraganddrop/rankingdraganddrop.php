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

class plgSurveyRankingdraganddrop {

	var $name = 'Rankingdraganddrop';
	var $_name = 'rankingdraganddrop';
	var $_type = 'survey';

	public function __construct()
    {
        return true;
    }

	public static function onGetDefaultForm($lists)
    {
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

	public static function onSaveDefault(&$data)
    {
		$database = JFactory::getDBO();
		if ( !empty($data['main_data']) ) {
			foreach ($data['main_data'] as $main_id) {
				$ans_field = $data['query_select_'.$main_id];
				$query = "INSERT INTO `#__survey_force_def_answers` (`survey_id`, `quest_id`, `answer`, `ans_field`) VALUES (".$data['survey_id'].", ".$data['id'].", ".$main_id.", ".$ans_field.")";
				$database->setQuery($query);
				$database->execute();
			}
		}

		return true;
	}

	public static function onGetQuestionData(&$data)
    {
		$database = JFactory::getDbo();

		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$color_cont = $sf_config->get('color_cont');
		$color_drag = $sf_config->get('color_drag');
		$color_highlight = $sf_config->get('color_highlight');

		$color_cont = ( empty($color_cont) ? '666666' : $color_cont);
		$color_drag = ( empty($color_drag) ? 'CCCCCC' : $color_drag);
		$color_highlight = ( empty($color_highlight) ? 'EEEEEE' : $color_highlight);

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

		if ($q_data->is_shuffle) {
			shuffle($f_main_data);
			shuffle($f_alt_data);
		}

		// add answers section for prev/next
		$query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $q_data->id . "' AND start_id = '" . $start_id . "' ";
		$database->SetQuery($query);
		$result = $database->LoadObjectList();
		$f_answ_data = ($result == null ? array() : $result);

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

		// Copy this code for new plugin
		$tmpl_name = SurveyforceHelper::getTemplate($data);
		$class_name = 'SF_' . ucfirst($data['quest_type']) . 'Template';

		if (!class_exists($class_name))
			if (file_exists(JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php'))
			{
				include_once JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php';
			}

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
		$iscale['config'] = array(
			'color_cont' => $color_cont,
			'color_drag' => $color_drag,
			'color_highlight' => $color_highlight,
		);

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

    public static function onSaveQuestion(&$data)
    {
        $database = JFactory::getDbo();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $query = $database->getQuery(true);
        $query->select('*');
        $query->from($database->quoteName('#__survey_force_quests'));
        $query->where('id=' . $data['qid']);
        $database->setQuery($query);

        $row = $database->loadObject();
         
        if ($row->id < 1) {
            $query = "SELECT MAX(ordering) FROM #__survey_force_quests WHERE sf_survey = {$row->sf_survey}";
            $database->SetQuery($query);
            $max_ord = $database->LoadResult();
            $row->ordering = $max_ord + 1;
        }

        $query = "SELECT count(*) FROM #__survey_force_user_answers WHERE quest_id = '" . $row->id . "'";
        $database->SetQuery($query);
        $ans_count = $database->LoadResult();
        $is_update = false;
        if ($ans_count > 0) {
            $is_update = true;
        }

        $qid = $row->id;

        $query = "DELETE FROM #__survey_force_rules WHERE quest_id = '" . $qid . "'";
        $database->setQuery($query);
        $database->query();

        $rules_ar = array();
        $rules_count = 0;

        $sf_hid_rule = $jinput->get('sf_hid_rule', array(), 'array');
        $sf_hid_rule_alt = $jinput->get('sf_hid_rule_alt', array(), 'array');
        $sf_hid_rule_quest = $jinput->get('sf_hid_rule_quest', array(), 'array');

        $query = "DELETE FROM #__survey_force_quest_show WHERE quest_id = '" . $qid . "'";
        $database->setQuery($query);
        $database->query();

        $sf_hid_rule2_id = $jinput->get('sf_hid_rule2_id', array(), 'array');
        $sf_hid_rule2_alt_id = $jinput->get('sf_hid_rule2_alt_id', array(), 'array');
        $sf_hid_rule2_quest_ids = $jinput->get('sf_hid_rule2_quest_ids', array(), 'array');

        if (is_array($sf_hid_rule2_quest_ids) && count($sf_hid_rule2_quest_ids)) {
            foreach ($sf_hid_rule2_quest_ids as $ij => $sf_hid_rule2_quest_id) {
                $query = "INSERT INTO `#__survey_force_quest_show` (quest_id, survey_id, quest_id_a, answer, ans_field)
				VALUES('" . $qid . "','" . $row->sf_survey . "', '" . $sf_hid_rule2_quest_id . "', '" . (isset($sf_hid_rule2_id[$ij]) ? $sf_hid_rule2_id[$ij] : 0) . "', '" . (isset($sf_hid_rule2_alt_id[$ij]) ? $sf_hid_rule2_alt_id[$ij] : 0) . "')";
                $database->setQuery($query);
                $database->query();
            }
        }

        $priority = $jinput->get('priority', array(), 'array');
        if (is_array($sf_hid_rule) && count($sf_hid_rule)) {
            foreach ($sf_hid_rule as $f_rule) {
                $rules_ar[$rules_count]->rul_txt = $database->quote($f_rule);
                $rules_ar[$rules_count]->answer_id = 0;
                $rules_ar[$rules_count]->rul_txt_alt = $database->quote((isset($sf_hid_rule_alt[$rules_count]) ? $sf_hid_rule_alt[$rules_count] : 0));
                $rules_ar[$rules_count]->answer_id_alt = 0;
                $rules_ar[$rules_count]->quest_id = isset($sf_hid_rule_quest[$rules_count]) ? $sf_hid_rule_quest[$rules_count] : 0;
                $rules_ar[$rules_count]->priority = isset($priority[$rules_count]) ? $priority[$rules_count] : 0;
                $rules_count++;
            }
        }


        $ii = 0;

        $sf_fields = $jinput->get('sf_fields', array(), 'array');
        $sf_field_ids = $jinput->get('sf_field_ids', array(0), 'array');
        $old_sf_field_ids = $jinput->get('old_sf_field_ids', array(), 'array');

        $sf_alt_fields = $jinput->get('sf_alt_fields', array(), 'array');
        $sf_alt_field_ids = $jinput->get('sf_alt_field_ids', array(), 'array');
        $old_sf_alt_field_ids = $jinput->get('old_sf_alt_field_ids', array(), 'array');

        for ($i = 0, $n = count($old_sf_field_ids); $i < $n; $i++) {
            if (in_array($old_sf_field_ids[$i], $sf_field_ids))
                unset($old_sf_field_ids[$i]);
        }
        for ($i = 0, $n = count($old_sf_alt_field_ids); $i < $n; $i++) {
            if (in_array($old_sf_alt_field_ids[$i], $sf_alt_field_ids))
                unset($old_sf_alt_field_ids[$i]);
        }

        $old_id = @array_merge(array(0 => 0), $old_sf_field_ids, $old_sf_alt_field_ids);
        if (!$data['issave2copy']) {
			$query = "DELETE FROM #__survey_force_fields WHERE quest_id = '" . $qid . "' AND id IN ( '" . ( (count($old_id)) ? implode('\', \'', $old_id) : 0 ) . "' )";
			$database->setQuery($query);
			$database->query();
		}


		$new_alt_field = array();

        for ($i = 0, $n = count($sf_alt_fields); $i < $n; $i++) {
            $f_row = $sf_alt_fields[$i];

			$new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());

			if ($sf_alt_field_ids[$i] > 0 && !$data['issave2copy']) {
				$new_field->id = $sf_alt_field_ids[$i];
			}

			$new_field->quest_id = $qid;
			$new_field->ftext = SurveyforceHelper::SF_processGetField($f_row);
			$new_field->alt_field_id = 0;
			$new_field->is_main = 0;
			$new_field->ordering = $ii;
			$new_field->is_true = 1;

			if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }

			$new_field_id = $new_field->id;

            if ($sf_alt_field_ids[$i] > 0 && !$data['issave2copy']) {
                $new_alt_field[$ii]->alt_field_id = $sf_alt_field_ids[$i];
            } else {
                $new_alt_field[$ii]->alt_field_id = $new_field_id;
            }

            $j = 0;
            while ($j < $rules_count) {
                if ($rules_ar[$j]->rul_txt_alt == $f_row) {
                    $rules_ar[$j]->answer_id_alt = $new_field_id;
                }
                $j++;
            }
            $ii++;
        }

        $field_order = 0;

        for ($i = 0, $n = count($sf_fields); $i < $n; $i++) {
            $f_row = $sf_fields[$i];

			$new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());

			if ($sf_field_ids[$i] > 0 && !$data['issave2copy']) {
				$new_field->id = $sf_field_ids[$i];
			}

			$new_field->quest_id = $qid;
			$new_field->ftext = SurveyforceHelper::SF_processGetField($f_row);
			$new_field->alt_field_id = $new_alt_field[$i]->alt_field_id;
			$new_field->is_main = 1;
			$new_field->ordering = $field_order;
			$new_field->is_true = 1;

			if (!$new_field->check()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }
			if (!$new_field->store()) { echo "<script> alert('".$new_field->getError()."'); window.history.go(-1); </script>\n"; exit(); }

			$new_field_id = $new_field->id;

            $field_order++;
            $j = 0;
            while ($j < $rules_count) {
                if ($rules_ar[$j]->rul_txt == $f_row) {
                    $rules_ar[$j]->answer_id = $new_field_id;
                }
                $j++;
            }
        }


        if (is_array($rules_ar) && count($rules_ar) > 0) {
            foreach ($rules_ar as $rule_one) {
                if ($rule_one->answer_id) {
                    $values = array($database->quote($qid), $database->quote($rule_one->quest_id),
                        $database->quote($rule_one->answer_id), $database->quote($rule_one->answer_id_alt), $database->quote($rule_one->priority)
                    );
                    $columns = array('quest_id', 'next_quest_id', 'answer_id', 'alt_field_id', 'priority');


                    $new_rule = $database->getQuery(true);
                    $new_rule->insert('#__survey_force_rules');
                    $new_rule->columns($database->quoteName($columns));
                    $new_rule->values(implode(',', $values));
                    $database->setQuery($new_rule);
                    $database->execute();
                }
            }
        }

        $super_rule = $jinput->getInt('super_rule', 0);
        $sf_quest_list2 = $jinput->getInt('sf_quest_list2', 0);

        if ($super_rule && $sf_quest_list2) {
            $values = array($database->quote($qid), $database->quote($sf_quest_list2),
                $database->quote(9999997), $database->quote(9999997), $database->quote(1000)
            );
            $columns = array('quest_id', 'next_quest_id', 'answer_id', 'alt_field_id', 'priority');


            $new_rule = $database->getQuery(true);
            $new_rule->insert('#__survey_force_rules');
            $new_rule->columns($database->quoteName($columns));
            $new_rule->values(implode(',', $values));
            $database->setQuery($new_rule);
            $database->execute();
        }

		if ($database->errorMsg()) {

			return $database->errorMsg();
		}
		else
			return $data;
    }

   
    public function onTotalScore(&$data)
    {
        return true;
    }

    public function onScoreByCategory(&$data)
    {
        return true;
    }

    public function onNextPreviewQuestion(&$data)
    {
        return true;
    }

    public function onReviewQuestion(&$data)
    {
        return true;
    }

    public function onGetResult(&$data)
    {
        return true;
    }

    //Administration part

    public static function onGetAdminOptions($data, $lists)
    {
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
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' AND is_main = 1 ORDER BY ordering";
        $database->SetQuery($query);
		$result = $database->LoadObjectList();
        $lists['sf_fields'] = ($result == null ? array() : $result);
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
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' AND is_main = 0 ORDER BY ordering";
        $database->SetQuery($query);
		$result = $database->LoadObjectList();
        $lists['sf_alt_fields'] = ($result == null ? array() : $result);
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
        $query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $row->id . "' and is_main = 1 ORDER BY ordering";
        $database->SetQuery($query);
		$result = $database->LoadObjectList();
        $sf_fields = ($result == null ? array() : $result);
        $ii = 0;
        foreach ($sf_fields as $qrow) {
            $sf_fields_full[$ii]->id = $qrow->id;
            $sf_fields_full[$ii]->quest_id = $qrow->quest_id;
            $sf_fields_full[$ii]->ftext = $qrow->ftext;
            $sf_fields_full[$ii]->alt_field_id = $qrow->alt_field_id;
            $database->SetQuery("SELECT ftext FROM #__survey_force_fields WHERE is_main = 0 and quest_id = '" . $qrow->quest_id . "' and id = '" . $qrow->alt_field_id . "'");
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
        include_once(JPATH_SITE . "/plugins/survey/rankingdraganddrop/admin/js/rankingdraganddrop.js.php");
        include_once(JPATH_SITE . "/plugins/survey/rankingdraganddrop/admin/options/rankingdraganddrop.php");
        $options = ob_get_clean();


        return $options;
    }

    public function onGetAdminJavaScript()
    {
        $document = JFactory::getDocument();
        $document->addScript(JUri::root()."/plugins/survey/rankingdraganddrop/admin/js/rankingdraganddrop.js");
    }

    public function onAdminSaveOptions(&$data)
    {
        return true;
    }

    public function onGetAdminAddLists(&$data)
    {
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

    public function onGetAdminQuestionData(&$data)
    {
        return true;
    }

    public function onGetAdminCsvData(&$data)
    {
        return true;
    }

}