<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class SurveyforceTableQuestion extends JTable {

	function __construct(&$db) {
		parent::__construct('#__survey_force_quests', 'id', $db);
	}

	public function store($updateNulls = false) {

		$database = JFactory::getDbo();
		$mainframe = JFactory::getApplication();

		$jform = $mainframe->input->get('jform', array(), 'ARRAY');

		//$this->id = (int)$jform['id'];
		//$this->sf_qtype = $jform['sf_qtype'];
		$this->sf_qstyle = isset($jform['sf_qstyle']) ? $jform['sf_qstyle'] : 0;
		//$this->sf_survey = @$jform['sf_survey'];
		$this->sf_num_options = @$jform['sf_num_options'];

		$app = JFactory::getApplication();
        $issave2copy = $app->input->get('task') == 'save2copy';

		// Alter the title for save as copy
		if ($issave2copy)
		{
			$this->id = '';
		}

		if ($this->id < 1) {
			$query = "SELECT MAX(ordering) FROM `#__survey_force_quests` WHERE sf_survey = ".$this->sf_survey;
			$database->SetQuery($query);
			$max_ord = $database->LoadResult();

			$this->ordering = $max_ord + 1;
			$jform['ordering'] = $max_ord + 1;
		}

		$res = parent::store($updateNulls);

		$database->setQuery("SELECT * FROM `#__survey_force_quests` WHERE `id` = '".$this->id."'");
		$row = $database->loadObject();

		$query = "DELETE FROM `#__survey_force_rules` WHERE `quest_id` = '" . $row->id . "'";
		$database->setQuery($query);
		$database->execute();

		$rules_ar = array();
		$rules_count = 0;

		$sf_hid_rule = JFactory::getApplication()->input->get('sf_hid_rule', '', 'array', array());
		$sf_hid_rule_alt = JFactory::getApplication()->input->get('sf_hid_rule_alt', '', 'array', array());
		$sf_hid_rule_quest = JFactory::getApplication()->input->get('sf_hid_rule_quest', '', 'array', array());

		$query = "DELETE FROM `#__survey_force_quest_show` WHERE `quest_id` = '" . $row->id . "'";
		$database->setQuery($query);
		$database->execute();

		$sf_hid_rule2_id = JFactory::getApplication()->input->get('sf_hid_rule2_id', '', 'array',array());
		$sf_hid_rule2_alt_id = JFactory::getApplication()->input->get('sf_hid_rule2_alt_id', '', 'array', array());
		$sf_hid_rule2_quest_ids = JFactory::getApplication()->input->get('sf_hid_rule2_quest_id', '', 'array', array());

		if (is_array($sf_hid_rule2_quest_ids) && count($sf_hid_rule2_quest_ids)) {
			foreach ($sf_hid_rule2_quest_ids as $ij => $sf_hid_rule2_quest_id) {
				$query = "INSERT INTO `#__survey_force_quest_show` (quest_id, survey_id, quest_id_a, answer, ans_field)
                VALUES('" . $row->id . "','" . $row->sf_survey . "', '" . $sf_hid_rule2_quest_id . "', '" . (isset($sf_hid_rule2_id[$ij]) ? $sf_hid_rule2_id[$ij] : 0) . "', '" . (isset($sf_hid_rule2_alt_id[$ij]) ? $sf_hid_rule2_alt_id[$ij] : 0) . "')";
				$database->setQuery($query);
				$database->execute();
			}
		}

		$priority = JFactory::getApplication()->input->get('priority', '', 'array', array());
		if (is_array($sf_hid_rule) && count($sf_hid_rule)) {

			foreach ($sf_hid_rule as $f_rule) {

				$rules_ar[$rules_count]->rul_txt = $f_rule;
				$rules_ar[$rules_count]->answer_id = 0;
				$rules_ar[$rules_count]->rul_txt_alt = (isset($sf_hid_rule_alt[$rules_count]) ? $sf_hid_rule_alt[$rules_count] : 0);
				$rules_ar[$rules_count]->answer_id_alt = 0;
				$rules_ar[$rules_count]->quest_id = isset($sf_hid_rule_quest[$rules_count]) ? $sf_hid_rule_quest[$rules_count] : 0;
				$rules_ar[$rules_count]->priority = isset($priority[$rules_count]) ? $priority[$rules_count] : 0;
				$rules_count++;

			}
		}

		$data = array();
		$data['rules_ar'] = $rules_ar;
		$data['qid'] = $row->id;
		$data['issave2copy'] = $issave2copy;

		if ($jform['sf_qtype']) {

			$type = SurveyforceHelper::getQuestionType($jform['sf_qtype']);

			JPluginHelper::importPlugin('survey', $type);
			$className = 'plgSurvey' . ucfirst($type);

			$data['quest_type'] = $type;
			$data['rules_count'] = $rules_count;

			if (method_exists($className, 'onSaveQuestion'))
				$return = $className::onSaveQuestion($data);

			$rules_ar = $return['rules_ar'];
			if (is_array($rules_ar) && count($rules_ar) > 0) {
				foreach ($rules_ar as $rule_one) {
					if ($rule_one->answer_id) {

						$new_rule = JTable::getInstance('Rules', 'SurveyforceTable', array());
						$new_rule->quest_id = $row->id;
						$new_rule->next_quest_id = $rule_one->quest_id;
						$new_rule->answer_id = $rule_one->answer_id;

						$new_rule->alt_field_id = $rule_one->answer_id_alt;
						$new_rule->priority = $rule_one->priority;
						if (!$new_rule->check()) { echo "<script> alert('".$new_rule->getError()."'); window.history.go(-1); </script>\n"; exit(); }
						if (!$new_rule->store()) { echo "<script> alert('".$new_rule->getError()."'); window.history.go(-1); </script>\n"; exit(); }
					}
				}
			}

			$super_rule = intval(JFactory::getApplication()->input->get('super_rule', 0));
			$sf_quest_list2 = intval(JFactory::getApplication()->input->get('sf_quest_list2', 0));

			if ($super_rule && $sf_quest_list2) {

				$new_rule = JTable::getInstance('Rules', 'SurveyforceTable', array());
				$new_rule->quest_id = $row->id;
				$new_rule->next_quest_id = $sf_quest_list2;
				$new_rule->answer_id = 9999997;

				$new_rule->alt_field_id = 9999997;
				$new_rule->priority = 1000;
				if (!$new_rule->check()) { echo "<script> alert('".$new_rule->getError()."'); window.history.go(-1); </script>\n"; exit(); }
				if (!$new_rule->store()) { echo "<script> alert('".$new_rule->getError()."'); window.history.go(-1); </script>\n"; exit(); }

			}

			$insert_pb = intval($jform['insert_pb']);
			$q_id = intval(JFactory::getApplication()->input->get('id', 0));

			if ($q_id == 0 && $insert_pb) {

				$sf_survey = $row->sf_survey;
				$query = "INSERT INTO `#__survey_force_quests` (`sf_survey`, `sf_qtype`, `sf_compulsory`, `sf_qtext`, `ordering`, `published`, `is_final_question`) VALUES ({$sf_survey}, 8, 0, 'Page Break', " . (int)(@$max_ord + 2) . ", " . $row->published . ", " . $jform['is_final_question'] . ")";
				$database->setQuery($query);
				$database->execute();

			}

			SurveyforceHelper::SF_refreshSection($row->sf_section_id);
			SurveyforceHelper::SF_refreshOrder($row->sf_survey);

		}

		return true;

	}

}