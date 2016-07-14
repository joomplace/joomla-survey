<?php
/**
 * Survey Force Deluxe Likertscale Plugin for Joomla 3
 * @package Joomla.Plugin
 * @subpackage Survey.likertscale
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyLikertscale {

	var $name = 'Likertscale';
	var $_name = 'likertscale';
	var $_type = 'survey';

	public function plgSurveyLikertscale() {
		return true;
	}

	public function onGetDefaultForm($lists){

		ob_start();
		?>
		<div>
			<table  class='table table-striped' width="100%">
				<tr>
					<th valign="top" class="title" ><?php echo $lists['row']->sf_qtext?></th>
				</tr>
				<tr><td>
						<table border=1 cellpadding=3 cellspacing=0 class='adminform' style="width: auto;">
							<tr><td >&nbsp;</td>
								<?php foreach($lists['scale_data'] as $scale) { ?>
									<td align='center' style='text-align:center'><?php echo $scale->stext?></td>
								<?php } ?>
							</tr>
							<?php foreach($lists['main_data'] as $main) { ?>
								<tr><td align='left' style='text-align:left'>&nbsp;&nbsp;<?php echo $main->ftext?>
										<input type="hidden" name="scale_id[]" value="<?php echo $main->id?>" />
									</td>
									<?php foreach($lists['scale_data'] as $scale) {
										$selected = '';
										if ( $lists['answer_data'][$main->id]['ans_field'] == $scale->id )
										{
											$selected = ' checked="checked" ';
										}
										?>
										<td align='center' style='text-align:center'>
											<input type='radio' <?php echo $selected?> name='quest_radio_<?php echo $main->id?>' value='<?php echo $scale->id?>' />
										</td>
									<?php } ?>
								</tr>
							<?php } ?>
						</table>
					</td></tr>
			</table>
		</div>
		<?php

		$content = ob_get_contents();
		ob_clean();

		return $content;

	}

	public function onSaveDefault(&$data){

		$database = JFactory::getDBO();
		if ( !empty($data['scale_id']) )
		{
			foreach ($data['scale_id'] as $scale_id)
			{
				$ans_field = $data['quest_radio_'.$scale_id];
				$query = "INSERT INTO `#__survey_force_def_answers` (`survey_id`, `quest_id`, `answer`, `ans_field`) VALUES (".$data['survey_id'].", ".$data['id'].", ".$scale_id.", ".$ans_field.")";
				$database->setQuery($query);
				$database->execute();
			}
		}

		return true;

	}

	public function onSaveQuestion(&$data) {

		$database = JFactory::getDbo();
		$mainframe = JFactory::getApplication();
		$rules_ar = $data['rules_ar'];

		$field_order = 0;
		// _scales
		if ( !empty($_POST['is_likert_predefined']) && !empty($_POST['sf_likert_scale']) )
		{
			$database->setQuery( "SELECT * FROM #__survey_force_fields WHERE `quest_id` = '".$_POST['sf_likert_scale']."'" );
			$preDefinedFields =  $database->loadObjectList();

			$database->setQuery( "DELETE FROM `#__survey_force_fields` WHERE `quest_id` = ".$data['qid'] );
			$database->execute();

			foreach ( $preDefinedFields as $pd_field)
			{
				$new_field = JTable::getInstance('Fields', 'SurveyforceTable', array());
				$new_field->quest_id = $data['qid'];
				$new_field->ftext = SurveyforceHelper::SF_processGetField($pd_field->ftext);
				$new_field->alt_field_id = $pd_field->alt_field_id;
				$new_field->is_main = $pd_field->is_main;
				$new_field->ordering = $pd_field->ordering;
				$new_field->is_true = $pd_field->is_true;
				$new_field->store();
			}


			$database->setQuery( "SELECT * FROM #__survey_force_scales WHERE `quest_id` = '".$_POST['sf_likert_scale']."'" );
			$preDefinedScales =  $database->loadObjectList();

			$database->setQuery( "DELETE FROM `#__survey_force_scales` WHERE `quest_id` = ".$data['qid'] );
			$database->execute();

			foreach ( $preDefinedScales as $pd_scale)
			{
				$new_field = JTable::getInstance('Scales', 'SurveyforceTable', array());
				$new_field->quest_id = $data['qid'];
				$new_field->stext = SurveyforceHelper::SF_processGetField($pd_scale->stext);
				$new_field->ordering = $pd_scale->ordering;
				$new_field->store();
			}
		}
		else
		{
			$sf_hid_fields_scale = (!empty($_POST['sf_hid_fields_scale'])) ? $_POST['sf_hid_fields_scale'] : array();
			$sf_hid_field_scale_ids = JFactory::getApplication()->input->get('sf_hid_field_scale_ids', '', 'array', array(0));
			$old_sf_hid_field_scale_ids = JFactory::getApplication()->input->get('old_sf_hid_field_scale_ids', '', 'array', array(0));
			$old_sf_hid_field_scale_ids = @array_merge(array(0 => 0), $old_sf_hid_field_scale_ids);

			for ($i = 0, $n = count($old_sf_hid_field_scale_ids); $i < $n; $i++) {
				if (in_array($old_sf_hid_field_scale_ids[$i], $sf_hid_field_scale_ids))
					unset($old_sf_hid_field_scale_ids[$i]);
			}

			if(count($old_sf_hid_field_scale_ids)){
				$query = "DELETE FROM `#__survey_force_scales` WHERE `quest_id` = '".$data['qid']."' AND id IN ( ".implode(', ', $old_sf_hid_field_scale_ids)." )";
				$database->setQuery($query);
				$database->execute();
			}

			for ($i = 0, $n = count($sf_hid_fields_scale); $i < $n; $i++) {

				$s_row = $sf_hid_fields_scale[$i];
				$new_field = JTable::getInstance('Scales', 'SurveyforceTable', array());
				if ($sf_hid_field_scale_ids[$i] > 0 ) {
					$new_field->id = $sf_hid_field_scale_ids[$i];
				}
				$new_field->quest_id = $data['qid'];
				$new_field->stext = SurveyforceHelper::SF_processGetField($s_row);
				$new_field->ordering = $field_order;

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
				while ($j < $data['rules_count']) {
					if ($rules_ar[$j]->rul_txt == $new_field->ftext) {
						$rules_ar[$j]->answer_id = $new_field->id;
					}
					$j++;
				}

				$field_order ++ ;
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

		$query = "SELECT * FROM #__survey_force_scales WHERE quest_id = '".$q_data->id."' ORDER BY ordering";
		$database->SetQuery($query);
		$f_scale_data = ($database->LoadObjectList() == null? array(): $database->LoadObjectList());
		$ret_str .= "\t" . '<scale_fields_count>'.count($f_scale_data).'</scale_fields_count>' . "\n";
		if (count($f_scale_data) > 0) {
			$ret_str .= "\t" . '<scale_fields>' . "\n";
			foreach ($f_scale_data as $s_row) {
				$ret_str .= "\t\t" . '<scale_field><sfield_text><![CDATA['.stripslashes($s_row->stext).'&nbsp;]]></sfield_text>' . "\n";
				$ret_str .= "\t\t\t" . '<sfield_id>'.$s_row->id.'</sfield_id></scale_field>' . "\n";
			}
			$ret_str .= "\t" . '</scale_fields>' . "\n";
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


			foreach($f_answ_data as $answer){
				$ret_str .= "\t\t" . '<a_quest_id>' . $answer->answer. '</a_quest_id>' . "\n";
				$ret_str .= "\t\t" . '<ans_id>' . $answer->ans_field . '</ans_id>' . "\n";
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

		// Copy this code for new plugin
		$tmpl_name = SurveyforceHelper::getTemplate($data);
		$class_name = 'SF_' . ucfirst($data['quest_type']) . 'Template';


		if (!class_exists($class_name))
			if (file_exists(JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php'))
				include_once JPATH_SITE . '/plugins/survey/'.$data['quest_type'].'/tmpl/' . $tmpl_name . '/template.php';

		$iscale = array();
		$iscale['factor_name'] = $q_data->sf_fieldtype;
		$iscale['impscale_name'] = (isset($f_iscale_data) && count($f_iscale_data)) ? $f_iscale_data[0]->iscale_name : '';
		$iscale['ans_imp_id'] = (isset($f_answ_imp_data) && count($f_answ_imp_data)) ? $f_answ_imp_data[0]->iscalefield_id : '';
		$iscale['ans_imp_count'] = intval(count($f_answ_imp_data));
		$iscale['alt_fields_count'] = intval(count($f_alt_data));
		$iscale['main_fields_count'] = intval(count($f_main_data));
		$iscale['scale_fields_count'] = intval(count($f_scale_data));
		$iscale['ans_count'] = intval(count($f_answ_data));
		$iscale['isfield'] = array();
		$iscale['afield'] = array();
		$iscale['answers'] = array();
		$iscale['mfield'] = array();
		$iscale['sdata'] = array();

		if (count($f_scale_data))
			foreach ($f_scale_data as $is_row) {
				array_push($iscale['sdata'], array(
					'sfield_text' => $is_row->stext,
					'sfield_id' => $is_row->id,
				));
			}

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

	public function onGetAdminOptions($data, $lists, $is_front = false) {

		$my = JFactory::getUser();
		$database = JFactory::getDBO();
		$row = $data['item'];

		$q_om_type = $row->sf_qtype;
		$sf_num_options = $row->sf_num_options;
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

		// Surveys with linkscape
		$query = "SELECT id, sf_name FROM #__survey_force_survs ".
			( ($is_front && JFactory::getUser()->get('usertype') != 'Super Administrator') ? " WHERE sf_author = LIKE '%\"".JFactory::getUser()->id."\"%' " : " ");//".JFactory::getUser()->id : ''

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
		include_once(JPATH_SITE . "/plugins/survey/likertscale/admin/js/likertscale.js.php");
		include_once(JPATH_SITE . "/plugins/survey/likertscale/admin/options/likertscale.php");
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

		$result = array();

		$query = "SELECT stext FROM #__survey_force_scales WHERE quest_id = '".$question->id."' ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp_data = $database->loadColumn();
		$result['scale'] = implode(', ', $tmp_data);

		$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '".$question->id."' AND is_main = 1 ORDER BY ordering";
		$database->SetQuery( $query );
		$tmp_data = ($database->loadObjectList() == null? array(): $database->loadObjectList());

		$query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '".$question->id."' and survey_id = '".$question->sf_survey."' and start_id = '".$start_data->id."'";
		$database->SetQuery( $query );
		$ans_inf_data = ($database->loadObjectList() == null? array(): $database->loadObjectList());

		$result['answer'] = array();
		$j = 0;
		while ( $j < count($tmp_data) ) {
			$result['answer'][$j] = array();
			$result['answer'][$j]['num'] = $j;
			$result['answer'][$j]['f_id'] = $tmp_data[$j]->id;
			$result['answer'][$j]['f_text'] = $tmp_data[$j]->ftext;
			$result['answer'][$j]['alt_text'] = JText::_('COM_SURVEYFORCE_NO_ANSWER');
			foreach ($ans_inf_data as $ans_data) {
				if ($ans_data->answer == $tmp_data[$j]->id) {
					$query = "SELECT * FROM #__survey_force_scales WHERE id = '".$ans_data->ans_field."'"
						. "\n and quest_id = '".$question->id."'"
						. "\n ORDER BY ordering";
					$database->SetQuery( $query );
					$alt_data = ($database->loadObjectList() == null? array(): $database->loadObjectList());
					$result['answer'][$j]['alt_text'] = ($ans_data->ans_field==0? JText::_('COM_SURVEYFORCE_NO_ANSWER') :$alt_data[0]->stext);
					$result['answer'][$j]['alt_id'] = $ans_data->ans_field;
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