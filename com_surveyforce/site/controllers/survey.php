<?php

/**
 * Survey Force Deluxe Component for Joomla 3
 * @package Joomla.Component
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
define('_ISO', 'charset=utf-8');

jimport('joomla.application.component.controllerform');


/**
 * Results Controller
 */
class SurveyforceControllerSurvey extends JControllerForm {

	public function getModel($name = 'survey', $prefix = '', $config = array('ignore_request' => true))
    {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function saveQuestions()
	{
        \JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));
		
		$types = array('likert-scale' => '1', 'pick-one' => '2', 'pick-many' => '3', 'short-answer' => '4', 'ranking-dropdown' => '5', 'ranking-dragdrop' => '6', 'boilerplate' => '7', 'page-break' => '8', 'ranking' => '9', 'section-separator' => '10');

		$database = JFactory::getDbo();
		$my = JFactory::getUser();

		$surv_id = JFactory::getApplication()->input->get('surv_id', 0);
        $json = json_decode(JFactory::getApplication()->input->get('json', '', "STRING"), true);

		//sort questions
		$sort_questions = array(); $old_sections = array();
		if(count($json)){
			foreach ($json as $systemid => $question) {
				if(is_array($question)){
					$question['system_id'] = $systemid;
					$sort_questions[$question['questOrdering']] = $question;
				}
			}
		}

		$return_str_json = '{}';
		if(count($sort_questions)) ksort($sort_questions);

		if(count($sort_questions)){
			$sort_questions = array_values($sort_questions);

			if($surv_id){
				$new_ids = array();

				for($n = 0; $n < count($sort_questions); $n++){
					if(isset($sort_questions[$n]['id']) && $sort_questions[$n]['id'] != '') $new_ids[] = $sort_questions[$n]['id'];

					if(isset($sort_questions[$n]['id']) && $sort_questions[$n]['sf_qtype'] == 'section-separator'){
						$old_sections[] = $sort_questions[$n]['id'];
					}
				}

				$database->setQuery("SELECT `id` FROM `#__survey_force_quests` WHERE `sf_survey` = '".$surv_id."'");
				$old_ans_ids = $database->loadColumn();

				$del_ids = (count($new_ids)) ? array_diff($old_ans_ids, $new_ids) : $old_ans_ids;

				if(count($del_ids)){
					$database->setQuery("DELETE FROM `#__survey_force_quests` WHERE `id` IN (".implode(",", $del_ids).") AND `sf_survey` = '".$surv_id."'");
					$database->execute();

					$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `quest_id` IN (".implode(",", $del_ids).")");
					$database->execute();

					$database->setQuery("DELETE FROM `#__survey_force_scales` WHERE `quest_id` IN (".implode(",", $del_ids).")");
					$database->execute();

					$database->setQuery("DELETE FROM `#__survey_force_quest_show` WHERE `quest_id` IN (".implode(",", $del_ids).")");
					$database->execute();

					$database->setQuery("DELETE FROM `#__survey_force_rules` WHERE `quest_id` IN (".implode(",", $del_ids).")");
					$database->execute();
				}

				$database->setQuery("SELECT `id` FROM `#__survey_force_qsections`");
				$all_sections = $database->loadColumn();

				if(count($old_sections) && count($all_sections)){
					$del_sections = array_diff($all_sections, $old_sections);
				} else {
					$del_sections = $all_sections;
				}

				if(count($del_sections)){
					$database->setQuery("DELETE FROM `#__survey_force_qsections` WHERE `id` IN (".implode(",", $del_sections).")");
					$database->execute();
				}
			}

			$return_json = array();
			$return_json['questions'] = array();
			$return_json['answers'] = array();

			$section_ord = 0; $sections = array();
			$quest_id = array();
			foreach ($sort_questions as $question) {

				if($question['sf_qtype'] == 'section-separator'){
					
					$section = new stdClass;
					if(isset($question['id']) && $question['id'] != ''){
						$section->id = $question['id'];
						$section->sf_name = $question['sf_qtitle'];
						$section->addname = 1;
						$section->ordering = $section_ord;
						$section->sf_survey_id = $surv_id;

						$database->updateObject('#__survey_force_qsections', $section, 'id');
						
					} else {
						$section->id = '';
						$section->sf_name = $question['sf_qtitle'];
						$section->addname = 1;
						$section->ordering = $section_ord;
						$section->sf_survey_id = $surv_id;

						$database->insertObject('#__survey_force_qsections', $section, 'id');
						$section->id = $database->insertid();
					}

					$section_ord++;
					$sections[] = $section->id;
					$quest_id[] = '0';
					continue;
				}

				$is_new = true;
				if(isset($question['id']) && $question['id'] != '') $is_new = false;

				if(!$is_new){
					$row = new stdClass;
					$row->id = $question['id'];
					$row->sf_qtext = $question['sf_qtitle'];
					$row->sf_impscale = (isset($question['sf_iscale'])) ? $question['sf_iscale'] : 0;
					$row->sf_qdescr = (isset($question['sf_qdescription'])) ? $question['sf_qdescription'] : '';
					$row->published = $question['published'];
					$row->sf_compulsory = $question['sf_compulsory'];
					$row->sf_section_id = 0;
					$row->sf_default_hided = $question['sf_default_hided'];
					$row->is_final_question = $question['is_final_question'];
					$row->sf_qstyle = (isset($question['choiceStyle'])) ? $question['choiceStyle'] : 0;
					$row->ordering = $question['questOrdering'];

					$database->updateObject('#__survey_force_quests', $row, 'id');
				} else {
					$row = new stdClass;
					$row->id = '';
					$row->sf_survey = $surv_id;
					$row->sf_qtype = $types[$question['sf_qtype']];
					$row->sf_qtext = $question['sf_qtitle'];
					$row->sf_impscale = (isset($question['sf_iscale'])) ? $question['sf_iscale'] : 0;
					$row->sf_qdescr = (isset($question['sf_qdescription'])) ? $question['sf_qdescription'] : '';
					$row->published = $question['published'];
					$row->sf_compulsory = $question['sf_compulsory'];
					$row->sf_section_id = 0;
					$row->sf_default_hided = $question['sf_default_hided'];
					$row->is_final_question = $question['is_final_question'];
					$row->sf_qstyle = (isset($question['choiceStyle'])) ? $question['choiceStyle'] : 0;
					$row->ordering = $question['questOrdering'];

					$database->insertObject('#__survey_force_quests', $row, 'id');
					$question['id'] = $database->insertid();
				}

				$quest_id[] = $question['id'];
				$return_json['questions'][$question['system_id']] = $question['id'];
				
				//save answers
				if(isset($question['answers']) && count($question['answers'])){
					
					$answer = $question['answers'];

					switch($question['sf_qtype'])
					{
						case 'pick-one':
						case 'pick-many':

							$new_ids = array();
							for($n = 0; $n < count($answer); $n++){
								if($answer[$n]['id'] != '') $new_ids[] = $answer[$n]['id'];
							}

							$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '1'");
							$old_ans_ids = $database->loadColumn();

							$del_ids = (count($new_ids)) ? array_diff($old_ans_ids, $new_ids) : array();
							if(count($del_ids)){
								$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $del_ids).") AND `quest_id` = '".$question['id']."'");
								$database->execute();
							}

							for($n = 0; $n < count($answer); $n++){
								if($answer[$n]['id'] != ''){
									$ans = new stdClass;
									$ans->id = $answer[$n]['id'];
									$ans->ftext = $answer[$n]['title'];
									$ans->ordering = $n;

									$database->updateObject('#__survey_force_fields', $ans, 'id');

									$ans_id = $answer[$n]['id'];
								} else {
									$ans = new stdClass;
									$ans->id = '';
									$ans->quest_id = $question['id'];
									$ans->ftext = $answer[$n]['title'];
									$ans->alt_field_id = 0;
									$ans->is_main = 1;
									$ans->is_true = 1;
									$ans->ordering = $n;

									$database->insertObject('#__survey_force_fields', $ans, 'id');
									$ans_id = $database->insertid();
								}

								$return_json['answers'][$question['system_id']][] = $ans_id;
							}

							if(isset($answer[0]['other_option']) && $answer[0]['other_option']){

								$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '0'");
								$is_other = $database->loadResult();

								if($is_other){
									$other = new stdClass;
									$other->id = $is_other;
									$other->ftext = $answer[0]['other_option_text'];
									$other->ordering = $n + 1;

									$database->updateObject('#__survey_force_fields', $other, 'id');
								} else {
									$other = new stdClass;
									$other->id = '';
									$other->quest_id = $question['id'];
									$other->ftext = $answer[0]['other_option_text'];
									$other->alt_field_id = 0;
									$other->is_main = 0;
									$other->is_true = 1;
									$other->ordering = $n + 1;

									$database->insertObject('#__survey_force_fields', $other, 'id');
								}

							} else {
								$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '0'");
								$is_other = $database->loadResult();

								if($is_other){
									$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '0'");
									$database->execute();
								}
							}

						break;
						case 'ranking':
						case 'ranking-dragdrop':
							$new_ids = array();
							for($n = 0; $n < count($answer); $n++){
								if(isset($answer[$n]['leftid']) && $answer[$n]['leftid'] != '') $new_ids[] = $answer[$n]['leftid'];
							}

							$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '1'");
							$old_ans_ids = $database->loadColumn();

							$del_ids = (count($new_ids)) ? array_diff($old_ans_ids, $new_ids) : $old_ans_ids;
							if(count($del_ids)){

								$database->setQuery("SELECT `alt_field_id` FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $del_ids).") AND `is_main` = '1'");
								$right_del_ids = $database->loadColumn();

								$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $del_ids).") AND `quest_id` = '".$question['id']."'");
								$database->execute();
								if(count($right_del_ids)){
									$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $right_del_ids).") AND `quest_id` = '".$question['id']."'");
									$database->execute();
								}
							}
							
							for($n = 0; $n < count($answer); $n++){
								if(isset($answer[$n]['leftid']) && $answer[$n]['leftid'] != ''){
									$leftans = new stdClass;
									$leftans->id = $answer[$n]['leftid'];
									$leftans->ftext = $answer[$n]['left'];
									$leftans->ordering = $n;

									$database->updateObject('#__survey_force_fields', $leftans, 'id');
									$ans_left_id = $answer[$n]['leftid'];

									$rightans = new stdClass;
									$rightans->id = $answer[$n]['rightid'];
									$rightans->ftext = $answer[$n]['right'];
									$rightans->ordering = $n;

									$database->updateObject('#__survey_force_fields', $rightans, 'id');
									$ans_right_id = $answer[$n]['rightid'];
								} else {
									$rightans = new stdClass;
									$rightans->id = '';
									$rightans->quest_id = $question['id'];
									$rightans->ftext = $answer[$n]['right'];
									$rightans->alt_field_id = 0;
									$rightans->is_main = 0;
									$rightans->is_true = 1;
									$rightans->ordering = $n;

									$database->insertObject('#__survey_force_fields', $rightans, 'id');
									$ans_right_id = $database->insertid();

									$leftans = new stdClass;
									$leftans->id = '';
									$leftans->quest_id = $question['id'];
									$leftans->ftext = $answer[$n]['left'];
									$leftans->alt_field_id = $ans_right_id;
									$leftans->is_main = 1;
									$leftans->is_true = 1;
									$leftans->ordering = $n;

									$database->insertObject('#__survey_force_fields', $leftans, 'id');
									$ans_left_id = $database->insertid();
								}

								$return_json['answers'][$question['system_id']]['leftid'][] = $ans_left_id;
								$return_json['answers'][$question['system_id']]['rightid'][] = $ans_right_id;
							}

						break;
						case 'ranking-dropdown':
						case 'likert-scale':

							$new_oids = array();
							if(isset($answer['oid']) && count($answer['oid']))
								for($n = 0; $n < count($answer['oid']); $n++){
									if($answer['oid'][$n] != '') $new_oids[] = $answer['oid'][$n];
								}

							$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '1'");
							$old_ans_oids = $database->loadColumn();

							$del_oids = (count($new_oids)) ? array_diff($old_ans_oids, $new_oids) : $old_ans_oids;
							if(count($del_oids)){
								$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $del_oids).") AND `quest_id` = '".$question['id']."'");
								$database->execute();
							}

							if($question['sf_qtype'] == 'ranking-dropdown') {
								$new_ids = array();
								if(isset($answer['rid']) && count($answer['rid']))
									for($n = 0; $n < count($answer['rid']); $n++){
										if($answer['rid'][$n] != '') $new_ids[] = $answer['rid'][$n];
									}

								$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$question['id']."' AND `is_main` = '0'");
								$old_ans_ids = $database->loadColumn();

								$del_ids = (count($new_ids)) ? array_diff($old_ans_ids, $new_ids) : $old_ans_ids;
								if(count($del_ids)){
									$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `id` IN (".implode(",", $del_ids).") AND `quest_id` = '".$question['id']."'");
									$database->execute();
								}
							} else {
								$new_ids = array();
								if(isset($answer['sid']) && count($answer['sid']))
									for($n = 0; $n < count($answer['sid']); $n++){
										if($answer['sid'][$n] != '') $new_ids[] = $answer['sid'][$n];
									}

								$database->setQuery("SELECT `id` FROM `#__survey_force_scales` WHERE `quest_id` = '".$question['id']."'");
								$old_ans_ids = $database->loadColumn();

								$del_ids = (count($new_ids)) ? array_diff($old_ans_ids, $new_ids) : $old_ans_ids;
								if(count($del_ids)){
									$database->setQuery("DELETE FROM `#__survey_force_scales` WHERE `id` IN (".implode(",", $del_ids).") AND `quest_id` = '".$question['id']."'");
									$database->execute();
								}
							}

							if(isset($answer['oid']) && count($answer['oid']))
							for($n = 0; $n < count($answer['oid']); $n++){
								if(isset($answer['oid'][$n]) && $answer['oid'][$n] != ''){
									$opt_ans = new stdClass;
									$opt_ans->id = $answer['oid'][$n];
									$opt_ans->ftext = $answer['options'][$n];
									$opt_ans->ordering = $n;

									$database->updateObject('#__survey_force_fields', $opt_ans, 'id');
									$opt_id = $answer['oid'][$n];
								} else {
									$opt_ans = new stdClass;
									$opt_ans->id = '';
									$opt_ans->quest_id = $question['id'];
									$opt_ans->ftext = $answer['options'][$n];
									$opt_ans->alt_field_id = 0;
									$opt_ans->is_main = 1;
									$opt_ans->is_true = 1;
									$opt_ans->ordering = $n;

									$database->insertObject('#__survey_force_fields', $opt_ans, 'id');
									$opt_id = $database->insertid();

								}

								$return_json['answers'][$question['system_id']]['oid'][] = $opt_id;
							}

							if($question['sf_qtype'] == 'ranking-dropdown'){

								if(isset($answer['rid']) && count($answer['rid']))
								for($n = 0; $n < count($answer['rid']); $n++){
									if(isset($answer['rid'][$n]) && $answer['rid'][$n] != ''){
										$ans = new stdClass;
										$ans->id = $answer['rid'][$n];
										$ans->ftext = $answer['ranks'][$n];
										$ans->ordering = $n;

										$database->updateObject('#__survey_force_fields', $ans, 'id');
										$rid = $answer['rid'][$n];
									} else {
										$ans = new stdClass;
										$ans->id = '';
										$ans->quest_id = $question['id'];
										$ans->ftext = $answer['ranks'][$n];
										$ans->alt_field_id = 0;
										$ans->is_main = 0;
										$ans->is_true = 1;
										$ans->ordering = $n;

										$database->insertObject('#__survey_force_fields', $ans, 'id');
										$rid = $database->insertid();
									}

									$return_json['answers'][$question['system_id']]['rid'][] = $rid;
								}

							} else {

								if(isset($answer['sid']) && count($answer['sid']))
								for($n = 0; $n < count($answer['sid']); $n++){
									if(isset($answer['sid'][$n]) && $answer['sid'][$n] != ''){
										$ans = new stdClass;
										$ans->id = $answer['sid'][$n];
										$ans->stext = $answer['scales'][$n];
										$ans->ordering = $n;

										$database->updateObject('#__survey_force_scales', $ans, 'id');
										$sid = $answer['sid'][$n];
									} else {
										$ans = new stdClass;
										$ans->id = '';
										$ans->quest_id = $question['id'];
										$ans->stext = $answer['scales'][$n];
										$ans->ordering = $n;

										$database->insertObject('#__survey_force_scales', $ans, 'id');
										$sid = $database->insertid();
									}

									$return_json['answers'][$question['system_id']]['sid'][] = $sid;
								}

							}

						break;
					}
				}
			}

			$return_str_json = (count($return_json)) ? json_encode($return_json) : '{}';
		} else {
			$database->setQuery("SELECT `id` FROM `#__survey_force_quests` WHERE `sf_survey` = '".$surv_id."'");
			$del_ids = $database->loadColumn();

			if(count($del_ids)){
				$database->setQuery("DELETE FROM `#__survey_force_quests` WHERE `id` IN (".implode(",", $del_ids).") AND `sf_survey` = '".$surv_id."'");
				$database->execute();

				$database->setQuery("DELETE FROM `#__survey_force_fields` WHERE `quest_id` IN (".implode(",", $del_ids).")");
				$database->execute();
			}
		}

		//assign sections id to questions
		$sec_num = 0;
		if(count($sort_questions)){
			foreach ($sort_questions as $question) {
				if($question['sf_qtype'] == 'section-separator'){
					$qids = array();
					$questions_system_id = $question['sections'];
					if(count($questions_system_id)){
						foreach ($questions_system_id as $q_system_id) {
							
							foreach ($sort_questions as $quest) {
								if(isset($quest['id']) && $quest['system_id'] == $q_system_id){
									$qids[] = $quest['id'];
								}
							}

						}
					}

					if(count($qids)){

						$database->setQuery("UPDATE `#__survey_force_quests` SET `sf_section_id` = '".$sections[$sec_num]."' WHERE `id` IN (".implode(",", $qids).")");
						$database->execute();
					}

					$sec_num++;
				}
			}
		}

		//saving hides and rules
		if(count($sort_questions)){
			foreach ($sort_questions as $ii => $question) {

				if($question['sf_qtype'] == 'section-separator'){
					continue;
				}

				$database->setQuery("DELETE FROM `#__survey_force_quest_show` WHERE `quest_id` = '".$quest_id[$ii]."' AND `survey_id` = '".$surv_id."'");
				$database->execute();

				if(isset($question['hides']) && count($question['hides'])){
					for($n = 0; $n < count($question['hides']); $n++){

						switch($question['sf_qtype']){
							case 'pick-one':
							case 'pick-many':

								if(isset($return_json['questions'][$question['hides'][$n]['question']]) && isset($return_json['answers'][$question['hides'][$n]['question']][$question['hides'][$n]['answer'] - 1])){

									$hide = new stdClass;
									$hide->id = '';
									$hide->quest_id = $quest_id[$ii];
									$hide->survey_id = $surv_id;
									$hide->quest_id_a = $return_json['questions'][$question['hides'][$n]['question']];
									$hide->answer = $return_json['answers'][$question['hides'][$n]['question']][$question['hides'][$n]['answer'] - 1];
									$hide->ans_field = 0;

									$database->insertObject('#__survey_force_quest_show', $hide, 'id');
								}

							break;
							case 'ranking':
							case 'ranking-dragdrop':

								if(isset($return_json['questions'][$question['hides'][$n]['question']]) && isset($return_json['answers'][$question['hides'][$n]['question']]['leftid'][$question['hides'][$n]['answer'] - 1])){

									$hide = new stdClass;
									$hide->id = '';
									$hide->quest_id = $quest_id[$ii];
									$hide->survey_id = $surv_id;
									$hide->quest_id_a = $return_json['questions'][$question['hides'][$n]['question']];
									$hide->answer = $return_json['answers'][$question['hides'][$n]['question']]['leftid'][$question['hides'][$n]['answer'] - 1];
									$hide->ans_field = 0;

									$database->insertObject('#__survey_force_quest_show', $hide, 'id');
								}

							break;
							case 'ranking-dropdown':
							case 'likert-scale':
								if(isset($return_json['questions'][$question['hides'][$n]['question']]) && isset($return_json['answers'][$question['hides'][$n]['question']]['oid'][$question['hides'][$n]['answer'] - 1])){

									$hide = new stdClass;
									$hide->id = '';
									$hide->quest_id = $quest_id[$ii];
									$hide->survey_id = $surv_id;
									$hide->quest_id_a = $return_json['questions'][$question['hides'][$n]['question']];
									$hide->answer = $return_json['answers'][$question['hides'][$n]['question']]['oid'][$question['hides'][$n]['answer'] - 1];
									$hide->ans_field = 0;

									$database->insertObject('#__survey_force_quest_show', $hide, 'id');
								}
							break;
						}

					}
				}

				$database->setQuery("DELETE FROM `#__survey_force_rules` WHERE `quest_id` = '".$quest_id[$ii]."'");
				$database->execute();

				if(isset($question['rules']) && count($question['rules'])){
					for($n = 0; $n < count($question['rules']); $n++){
						switch($question['sf_qtype']){
							case 'pick-one':
							case 'pick-many':
								if(isset($return_json['questions'][$question['rules'][$n]['question']]) && isset($question['rules'][$n]['answer'])){

									$ordering = $question['rules'][$n]['answer'] - 1;
									$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$quest_id[$ii]."' AND `is_main` = '1' AND `ordering` = '".$ordering."'");
									$answer_id = $database->loadResult();

									$rule = new stdClass;
									$rule->id = '';
									$rule->quest_id = $quest_id[$ii];
									$rule->answer_id = $answer_id;
									$rule->next_quest_id = $return_json['questions'][$question['rules'][$n]['question']];
									$rule->alt_field_id = 0;
									$rule->priority = $question['rules'][$n]['priority'];

									$database->insertObject('#__survey_force_rules', $rule, 'id');
								}

							break;
							case 'ranking-dropdown':
							case 'ranking-dragdrop':
							case 'ranking':
							case 'likert-scale':
								if(isset($return_json['questions'][$question['rules'][$n]['question']]) && isset($question['rules'][$n]['answer'])){

									$orderAns = $question['rules'][$n]['answer'] - 1;
									$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$quest_id[$ii]."' AND `is_main` = '0' AND `ordering` = '".$orderAns."'");
									$answer_id = $database->loadResult();

									$orderOpt = $question['rules'][$n]['option'] - 1;
									$database->setQuery("SELECT `id` FROM `#__survey_force_fields` WHERE `quest_id` = '".$quest_id[$ii]."' AND `is_main` = '1' AND `ordering` = '".$orderOpt."'");
									$option_id = $database->loadResult();

									$rule = new stdClass;
									$rule->id = '';
									$rule->quest_id = $quest_id[$ii];
									$rule->answer_id = $answer_id;
									$rule->next_quest_id = $return_json['questions'][$question['rules'][$n]['question']];
									$rule->alt_field_id = $option_id;
									$rule->priority = $question['rules'][$n]['priority'];

									$database->insertObject('#__survey_force_rules', $rule, 'id');
								}
							break;
							
						}
					}
				}
			}
		}

		echo $return_str_json;
		die;
	}

	public function saveSurvey()
	{
        \JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));
		
		$database = JFactory::getDbo();
		$my = JFactory::getUser();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if(!$my->id){
			echo 'no login';
			die;
		}

        $post = \JFactory::getApplication()->input->post;
		$image_file = \JFactory::getApplication()->input->files->get('image_file');
		
		if($image_file && $image_file['name'] != ''){
			if(!preg_match('/image.*/', $image_file['type'])){
				return false;
			}

			if(!JFolder::exists(JPATH_SITE.'/images/com_surveyforce')){
				JFolder::create(JPATH_SITE.'/images/com_surveyforce', 0757);
			}

            $image_file['name'] = str_replace(array(" ", "(", ")"), array("_", "", ""), $image_file['name']);

			if(!JFile::move($image_file['tmp_name'], JPATH_SITE.'/images/com_surveyforce/'.$image_file['name'])){
				return false;
			}

			$filename = $image_file['name'];
		}

		if($post->get('sf_image') != '') $filename = $post->get('sf_image');

		if(count($post)){

            $row = new stdClass;
            $row->id = $post->get('survey_id');
            $row->sf_name = $post->get('sf_name', "New Survey", "STRING");
            $row->sf_descr = $post->get('sf_descr', "", "STRING");
            $row->sf_image = (isset($filename)) ? $filename : '';
            $row->sf_cat = $post->get('sf_cat');

            $sf_date_started = $post->get('sf_date_started',"0000-00-00 00:00:00","STRING");
            if($sf_date_started != "0000-00-00 00:00:00"){
                $sf_date_started = date("Y-m-d H:i:s", strtotime($sf_date_started));
            }

            $sf_date_expired = $post->get('sf_date_expired', "0000-00-00 00:00:00", "STRING");
            if($sf_date_expired != "0000-00-00 00:00:00"){
                $sf_date_expired = date("Y-m-d H:i:s", strtotime($sf_date_expired));
            }

            $row->sf_date_started = $sf_date_started;
            $row->sf_date_expired = $sf_date_expired;
            $row->sf_author = $my->id;
            $row->sf_public = $post->get('sf_public', '') ? 1 : 0;
            $row->sf_invite = $post->get('sf_invite', '') ? 1 : 0;
            $row->sf_reg = $post->get('sf_reg', '') ? 1 : 0;
            $row->published = $post->get('published', '') ? 1 : 0;
            $row->sf_fpage_type = $post->get('sf_fpage_type');
            $row->sf_fpage_text = $post->get('sf_fpage_text', '', "STRING");
            $row->sf_special = $post->get('sf_special', 0);
            $row->sf_auto_pb = $post->get('sf_auto_pb', '') ? 1 : 0;
            $row->sf_progressbar = $post->get('sf_progressbar', '') ? 1 : 0;
            $row->sf_progressbar_type = $post->get('sf_progressbar_type');
            $row->sf_enable_descr = $post->get('sf_enable_descr', '') ? 1 : 0;
            $row->sf_reg_voting = $post->get('sf_reg_voting');
            $row->sf_inv_voting = $post->get('sf_inv_voting');
            $row->sf_template = $post->get('sf_template');
            $row->sf_pub_voting = $post->get('sf_pub_voting');
            $row->sf_pub_control = $post->get('sf_pub_control');
            $row->surv_short_descr = $post->get('surv_short_descr', '', "STRING");
            $row->sf_after_start = $post->get('sf_after_start');
            $row->sf_redirect_enable = $post->get('sf_redirect_enable', '') ? 1 : 0;
            $row->sf_redirect_url = $post->get('sf_redirect_url');
            $row->sf_redirect_delay = $post->get('sf_redirect_delay', 0, "INT");
            $row->sf_prev_enable = $post->get('sf_prev_enable', '') ? 1 : 0;
            $row->sf_random = $post->get('sf_random');
            $row->sf_step = $post->get('sf_step');

			$database->updateObject("#__survey_force_survs", $row, "id");
		}

		echo 'notif({
				  type: "success",
				  msg: "Successfully saved.",
				  position: "right",
				  fade: true,
				  timeout: 2000
				});';
		die;
	}

	public function SF_StartSurvey() {

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$session = JFactory::getSession();
		$database = JFactory::getDbo();
		$my = JFactory::getUser();

		$ret_str = '';
		$preview = JFactory::getApplication()->input->getInt('preview', 0);
		$survey_id = JFactory::getApplication()->input->getInt('survey', 0);
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		$invite_num = $session->get('invite_num', '');

		$now = strtotime(JFactory::getDate());
		$special = false;
		$surv_usertype = 0;
		$surv_user_id = 0;
		$surv_invite_id = 0;


		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $survey_id . "'";
		$database->setQuery($query);
		$survey = $database->loadObject();

		$auto_pb = $survey->sf_auto_pb;

		$query = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query);
		$isInstolled = $database->loadObject();
		
		$friends = array();
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))  {
			$query = "SELECT j.connect_to FROM #__community_connection AS j WHERE j.status = 1 AND j.connect_from = '{$survey->sf_author}'";
			$database->setQuery($query);
			$friends = $database->loadColumn();
		}


		if (!$preview) {
			if (($survey->published) && (($survey->sf_date_expired == '0000-00-00 00:00:00' && $survey->sf_date_started == '0000-00-00 00:00:00') || (strtotime($survey->sf_date_expired) >= $now && strtotime($survey->sf_date_started) <= $now))) {
				if (($my->id) && ($survey->sf_reg)) {
					//null;
				} elseif (($my->id) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends)) {
					//null;
				} elseif ($my->id == $survey->sf_author) {
					//null;
				} elseif ($survey->sf_public) {
					//null;
				} elseif ($survey->sf_invite && ($invite_num != '')) {
					$query = "SELECT inv_status FROM #__survey_force_invitations WHERE invite_num = '" . $invite_num . "'";
					$database->setQuery($query);
					$inv_status = $database->loadObject();
					$inv_data = ($inv_status == null ? array() : $inv_status);
					if (count($inv_data) == 1) {
						if ($inv_data->inv_status != 1) {
							// Continue
						} elseif ($inv_data->inv_status == 1 && $survey->sf_inv_voting == 1) {
							// Invitation completed
							if ($survey->sf_after_start) {
								$query = "SELECT a.id FROM #__survey_force_user_starts AS a, #__survey_force_invitations AS b WHERE b.invite_num = '" . $invite_num . "' AND b.id = a.invite_id ORDER BY a.id DESC";
								$database->setQuery($query);
								$inv_start_id = $database->loadResult();
								$ret_str .= $this->get_graph_results($survey_id, $inv_start_id);
							}
							$ret_str .= "\t" . '<task>invite_complete</task>' . "\n";
							$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
							$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
							return $ret_str;
						}
					} else {
						return $ret_str;
					}
				} elseif (($my->id > 0) && ($survey->sf_special > 0)) {
					$query = "SELECT DISTINCT b.id FROM #__survey_force_users AS a, #__users AS b "
						. "\n WHERE a.list_id IN ({$survey->sf_special}) AND b.id = '{$my->id}' "
						. "\n AND a.name = b.username AND a.email = b.email AND a.lastname = b.name ";
					$database->setQuery($query);

					if (intval($database->LoadResult()) < 1) {
						if (SurveforceHelper::SF_GetUserType($survey->id) == 1 && SurveforceHelper::SF_GetUserType($survey->id) == 2)
							return $ret_str;
					}
					$special = true;
				} else {
					return $ret_str;
				}
			} else {
				return $ret_str;
			}

			if ($my->id) {
				$surv_usertype = 1;
				$surv_user_id = $my->id;
			}

			if ($survey->sf_anonymous) {
				$surv_usertype = 0;
				$surv_user_id = 0;
			}
			$invited_survey = false;
			if ($invite_num != '') {
				$query = "SELECT inv_status, user_id, id FROM #__survey_force_invitations WHERE invite_num = '" . $invite_num . "'";
				$database->setQuery($query);
				$inv_status = $database->loadObject();
				$inv_data = ($inv_status == null ? array() : $inv_status);
				if (($inv_data->inv_status == 1 || ($survey->sf_anonymous && $inv_data->inv_status == 3)) && $survey->sf_inv_voting == 1) {
					if ($survey->sf_after_start && !$survey->sf_anonymous) {
						$query = "SELECT a.id FROM #__survey_force_user_starts AS a, #__survey_force_invitations AS b WHERE b.invite_num = '" . $invite_num . "' AND b.id = a.invite_id ORDER BY a.id DESC";
						$database->setQuery($query);
						$inv_start_id = $database->loadResult();
						$ret_str .= $this->get_graph_results($survey_id, $inv_start_id);
					}
					$ret_str .= "\t" . '<task>invite_complete</task>' . "\n";
					$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
					$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					return $ret_str;
				}
				$surv_usertype = 2;
				$surv_invite_id = $inv_data->id;
				$surv_user_id = $inv_data->user_id;
				if ($survey->sf_anonymous)
					$query = "UPDATE #__survey_force_invitations SET inv_status = 3 WHERE invite_num = '" . $invite_num . "'";
				else
					$query = "UPDATE #__survey_force_invitations SET inv_status = 2 WHERE invite_num = '" . $invite_num . "'";
				$database->setQuery($query);
				$database->query();
				$invited_survey = true;
				if ($survey->sf_anonymous) {
					$surv_usertype = 0;
					$surv_user_id = 0;
					$surv_invite_id = 0;
				}
			}

			if (($my->id > 0) && ($survey->sf_reg_voting == 1)) {
				$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '" . $my->id . "' AND is_complete = 1 ORDER BY id DESC";
				$database->setQuery($query);
				$reg_start_id = $database->LoadResult();
				if ($reg_start_id > 0) {
					if ($survey->sf_after_start && !$survey->sf_anonymous) {
						$ret_str .= $this->get_graph_results($survey_id, $reg_start_id);
					}
					$ret_str .= "\t" . '<task>reg_complete</task>' . "\n";
					$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
					$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					return $ret_str;
				}
			}

			if (($my->id) && ($survey->sf_friend_voting == 1) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends)) {
				$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '" . $my->id . "' AND is_complete = 1 ORDER BY id DESC";
				$database->setQuery($query);
				$reg_start_id = $database->LoadResult();
				if ($reg_start_id > 0) {
					if ($survey->sf_after_start && !$survey->sf_anonymous) {
						$ret_str .= $this->get_graph_results($survey_id, $reg_start_id);
					}
					$ret_str .= "\t" . '<task>reg_complete</task>' . "\n";
					$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
					$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					return $ret_str;
				}
			}

			if (($my->id < 1 || ($my->id > 0 && $survey->sf_anonymous)) && ($survey->sf_pub_control > 0) && ($survey->sf_pub_voting == 1) && !$invited_survey) {
				$ip = $_SERVER["REMOTE_ADDR"];
                $cookie = \JFactory::getApplication()->input->cookie->get(md5('survey' . $survey_id), '');

				if ($survey->sf_pub_control == 1) {
					$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `sf_ip_address` = '{$ip}' AND is_complete = 1 ORDER BY id DESC";
				} elseif ($survey->sf_pub_control == 2) {
					$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND is_complete = 1 ORDER BY id DESC";
				} elseif ($survey->sf_pub_control == 3) {
					$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND `sf_ip_address` = '{$ip}' AND is_complete = 1 ORDER BY id DESC ";
				}

				$database->setQuery($query);
				$pub_start_id = $database->LoadResult();
				if ($pub_start_id > 0) {
					if ($survey->sf_after_start) {
						$ret_str .= $this->get_graph_results($survey_id, $pub_start_id);
					}
					$ret_str .= "\t" . '<task>pub_complete</task>' . "\n";
					$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
					$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					return $ret_str;
				}
			}
		}

		$is_edit_voting = ($survey->sf_reg_voting == 3 && $my->id > 0) ||
			($survey->sf_inv_voting == 3 && $invite_num != '' ) ||
			($survey->sf_friend_voting == 3 && $my->id > 0 && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends));

		if ($survey_id) {
			$usr_data = null;

			if (!$preview) {
				if ($invite_num != '') {

					if ($survey->sf_inv_voting == 2) {
						$query = "SELECT id FROM #__survey_force_user_starts WHERE survey_id = {$survey_id} AND usertype = '" . $surv_usertype . "' AND invite_id = " . $surv_invite_id . " AND user_id = '" . $surv_user_id . "'  AND is_complete = 1 ORDER BY id DESC";
						$database->setQuery($query);
						$usr_starts = $database->loadAssoc();

						if ($usr_starts) {
							$query = "DELETE FROM `#__survey_force_user_ans_txt` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_answers` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_answers_imp` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_chain` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
						}
						$query = "DELETE FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND usertype = '" . $surv_usertype . "' AND invite_id = " . $surv_invite_id . " AND user_id = '" . $surv_user_id . "' AND is_complete = 1";
						$database->setQuery($query);
						$database->query();
					}

					if ($survey->sf_inv_voting == 3) {
						$survey_time = JFactory::getDate();
						$query = "UPDATE `#__survey_force_user_starts` SET is_complete = 0, sf_time = '{$survey_time}' WHERE survey_id = $survey_id AND usertype = " . $surv_usertype . " AND invite_id = " . $surv_invite_id . " AND user_id = '" . $surv_user_id . "' AND is_complete = 1";
						$database->setQuery($query);
						$database->query();
					}

					$query = "SELECT * FROM #__survey_force_user_starts WHERE survey_id = $survey_id AND usertype = " . $surv_usertype . " AND invite_id = '" . $surv_invite_id . "' AND user_id = '" . $surv_user_id . "' AND is_complete = 0 ORDER BY id DESC";
					$database->setQuery($query);
					$usr_data = $database->loadObject();
				} elseif ($my->id > 0) {

					if (!$special && ($survey->sf_reg_voting == 2 || ($survey->sf_friend_voting == 2 && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends)) )) {

						$query = "SELECT id FROM #__survey_force_user_starts WHERE survey_id = {$survey_id} AND user_id = '" . $my->id . "' AND is_complete = 1 ORDER BY id DESC";
						$database->setQuery($query);
						$usr_starts = $database->loadAssoc();
						if ($usr_starts) {
							$query = "DELETE FROM `#__survey_force_user_ans_txt` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();

							$query = "DELETE FROM `#__survey_force_user_answers` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_answers_imp` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_chain` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '" . $my->id . "' AND is_complete = 1";
							$database->setQuery($query);
							$database->query();
						}
					}

					if (!$special && ($survey->sf_reg_voting == 3 || ($survey->sf_friend_voting == 3 && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends)))) {
						$survey_time = JFactory::getDate();
						$query = "UPDATE `#__survey_force_user_starts` SET is_complete = 0, sf_time = '{$survey_time}' WHERE survey_id = $survey_id AND user_id = '" . $my->id . "' AND is_complete = 1";
						$database->setQuery($query);
						$database->query();
					}

					$query = "SELECT * FROM #__survey_force_user_starts WHERE survey_id = $survey_id AND user_id = '" . $my->id . "' AND is_complete = 0 ORDER BY id DESC";
					$database->setQuery($query);
					$usr_data = $database->loadObject();
				} elseif (($my->id < 1 || ($my->id > 0 && $survey->sf_anonymous)) && $survey->sf_pub_control > 0) {
					$ip = $_SERVER["REMOTE_ADDR"];
                    $cookie = \JFactory::getApplication()->input->cookie->get(md5('survey' . $survey_id), '');

					if ($survey->sf_pub_voting == 2) {
						if ($survey->sf_pub_control == 1) {
							$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `sf_ip_address` = '{$ip}' AND is_complete = 1  ORDER BY id DESC";
						} elseif ($survey->sf_pub_control == 2) {
							$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND is_complete = 1  ORDER BY id DESC";
						} elseif ($survey->sf_pub_control == 3) {
							$query = "SELECT id FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND `sf_ip_address` = '{$ip}' AND is_complete = 1  ORDER BY id DESC";
						}


						$database->setQuery($query);
						$usr_starts = $database->loadAssoc();

						if ($usr_starts) {
							$query = "DELETE FROM `#__survey_force_user_ans_txt` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_answers` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_answers_imp` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_chain` WHERE  start_id  IN (" . implode(',', $usr_starts) . ")";
							$database->setQuery($query);
							$database->query();
							$query = "DELETE FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND id  IN (" . implode(',', $usr_starts) . ") AND is_complete = 1";
							$database->setQuery($query);
							$database->query();
						}
					}

					if ($survey->sf_pub_control == 1) {
						$query = "SELECT * FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `sf_ip_address` = '{$ip}' AND is_complete = 0  ORDER BY id DESC";
					} elseif ($survey->sf_pub_control == 2) {
						$query = "SELECT * FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND is_complete = 0  ORDER BY id DESC";
					} elseif ($survey->sf_pub_control == 3) {
						$query = "SELECT * FROM `#__survey_force_user_starts` WHERE survey_id = {$survey_id} AND user_id = '0' AND `unique_id` = '{$cookie}' AND `sf_ip_address` = '{$ip}' AND is_complete = 0  ORDER BY id DESC";
					}

					$database->setQuery($query);
					$usr_data = $database->loadObject();
				}
			}//if not preview

			$last_page_quest_id = 0;
			if ($usr_data == null) {
				$user_unique_id = md5(uniqid(rand(), true));

				$survey_time = JFactory::getDate();
				$query = "INSERT INTO #__survey_force_user_starts (unique_id, usertype, user_id, invite_id, sf_time, survey_id, is_complete, sf_ip_address) "
					. "\n VALUES ('" . $user_unique_id . "', '" . $surv_usertype . "', '" . $surv_user_id . "', '" . $surv_invite_id . "', '" . $survey_time . "', '" . $survey_id . "', 0, '" . $_SERVER["REMOTE_ADDR"] . "')";
				$database->setQuery($query);

				$database->query();
				$start_id = $database->insertid();

				if ($preview) {
					$query = "INSERT INTO `#__survey_force_previews` SET `start_id` = '{$start_id}', `time` = '" . strtotime(JFactory::getDate()) . "', `survey_id` = '{$survey_id}', `unique_id` = '{$user_unique_id}'";
					$database->setQuery($query);
					$database->query();

					$query = "DELETE FROM `#__survey_force_user_starts` WHERE `id` = '{$start_id}'";
					$database->setQuery($query);
					$database->query();
				} else {
					setcookie(md5('survey' . $survey_id), $user_unique_id, strtotime(JFactory::getDate()) + 31536000);
				}

				$sf_chain = SurveyforceHelper::create_chain($survey_id);

				$query = "INSERT INTO `#__survey_force_user_chain` SET `start_id` = '{$start_id}', `sf_time` = '" . strtotime(JFactory::getDate()) . "', `survey_id` = '{$survey_id}', `unique_id` = '{$user_unique_id}', `sf_chain` = '{$sf_chain}', `invite_id` = '{$surv_invite_id}'";
				$database->setQuery($query);
				$database->query();

				$query = " SELECT * FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' " . ($auto_pb ? " AND sf_qtype <> 8 " : '') . " ORDER BY sf_section_id, ordering, id ";
				$database->setQuery($query);
				$q_data = $database->loadObjectList('id');

				$ret_str .= "\t" . '<user_id>' . $user_unique_id . '</user_id>' . "\n";
				$ret_str .= "\t" . '<start_id>' . $start_id . '</start_id>' . "\n";
				$ret_str .= "\t" . '<is_resume>0</is_resume>' . "\n";

				if ($sf_chain) {
					$n = 0;
					$last_page_quest_id = 0;
					$pages = explode('*#*', $sf_chain);
					$questions = explode('*', $pages[0]);

					foreach ($questions as $question) {
						if (isset($q_data[$question])) {
							$ret_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
							$ret_str .= "\t" . '<question_data>' . "\n";
							$ret_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
							$ret_str .= "\t" . '</question_data>' . "\n";
							$last_page_quest_id = $question;
							$n++;
						}
					}

					if ($n > 0) {
						$page_task = 'start';
						if ($last_page_quest_id) {
							$questions = explode('*', $sf_chain);
							if (end($questions) == $last_page_quest_id) {
								$page_task = 'start_last_question';
							}
						}

						$ret_str .= "\t" . '<task>' . $page_task . '</task>' . "\n";
						$ret_str .= "\t" . '<quest_count>' . $n . '</quest_count>' . "\n";
						$ret_str .= "\t" . '<is_prev>' . $survey->sf_prev_enable . '</is_prev>' . "\n";
						$ret_str .= "\t" . '<progress_bar>0%</progress_bar>' . "\n";
						$ret_str .= "\t" . '<progress_bar_txt><![CDATA[' . JText::_('COM_SURVEYFORCE_PROGRESS') . ' 0%]]></progress_bar_txt>' . "\n";
					} else {
						$ret_str = '';
					}
				} else {
					$ret_str = '';
				}
			} else {
				$user_unique_id = $usr_data->unique_id;
				setcookie(md5('survey' . $survey_id), $user_unique_id, strtotime(JFactory::getDate()) + 31536000);
				$start_id = $usr_data->id;
				$query = "SELECT a.quest_id FROM #__survey_force_user_answers AS a, #__survey_force_quests AS b WHERE b.published = 1 AND b.id = a.quest_id AND survey_id = '$survey_id' AND start_id = '$start_id' ORDER BY b.ordering DESC, b.id DESC ";
				$database->setQuery($query);

				$quest_ids = $database->loadAssocList();

				$sf_chain = SurveyforceHelper::create_chain($survey_id);

				$query = "UPDATE `#__survey_force_user_chain` SET `sf_chain` = '{$sf_chain}' WHERE `start_id` = '{$start_id}' AND `survey_id` = '{$survey_id}' AND `unique_id` = '{$user_unique_id}'";
				$database->setQuery($query);
				$database->query();

				$chain_questions = array_values(array_filter(explode('*', trim(str_replace('*#*', '*', $sf_chain),'#'))));

				$query = "SELECT a.quest_id FROM `#__survey_force_quest_show` AS a, #__survey_force_user_answers AS b, #__survey_force_quests c WHERE a.survey_id = '" . $survey_id . "' AND c.id = a.quest_id_a AND ((c.sf_qtype NOT IN (2, 3) AND a.answer = b.answer AND a.ans_field = b.ans_field) OR (c.sf_qtype IN (2, 3) AND a.answer = b.answer)) AND b.start_id = '" . $start_id . "' ";
				$database->setQuery($query);

				$not_shown = $database->loadAssocList();
				if (!count($not_shown))
					$not_shown = array(0);

				if ($survey->sf_random) {
					$not_shown = array(0);
				}

				$chain_questions = array_diff($chain_questions, $not_shown);
				$quest_id = $chain_questions[0];
				foreach ($chain_questions as $c => $chain_question) {
					if (in_array($chain_question, $quest_ids)) {
						$quest_id = (isset($chain_questions[$c + 1]) ? $chain_questions[$c + 1] : $chain_question);
					}
				}

				if (($my->id && $survey->sf_reg_voting == 3) || ($survey->sf_inv_voting == 3 && $invite_num != '') || ($my->id && $survey->sf_friend_voting == 3 && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends))) {
					$quest_id = 0;
				}

				$query = " SELECT * FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' " . ($auto_pb ? " AND sf_qtype <> 8 " : '') . " AND id IN ('" . @implode("','", $chain_questions) . "') ORDER BY sf_section_id, ordering, id ";
				$database->setQuery($query);
				$q_data = $database->loadObjectList('id');
				$task = 0;


				$ret_str .= "\t" . '<user_id>' . $user_unique_id . '</user_id>' . "\n";
				$ret_str .= "\t" . '<start_id>' . $start_id . '</start_id>' . "\n";

				$tmp = 0;
				$tmp_str = '';
				$first_pb = 0;
				$first_quest_id = -1;

				$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
				foreach ($pages as $p => $page) {
					$questions = explode('*', $page);
					$questions = array_diff($questions, $not_shown);
					if (in_array($quest_id, $questions)) {
						foreach ($questions as $question) {
							if (isset($q_data[$question])) {
								if ($first_quest_id == -1)
									$first_quest_id = $question;
								$tmp_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
								$tmp_str .= "\t" . '<question_data>' . "\n";
								$tmp_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
								$tmp_str .= "\t" . '</question_data>' . "\n";
								$last_page_quest_id = $question;
								$tmp++;
							}
						}
						$first_pb = $p;
					}
				}

				if ($is_edit_voting && $tmp == 0) {
					if (count($pages) > 0) {
						$tmp = 0;

						$questions = explode('*', $pages[0]);
						$questions = array_diff($questions, $not_shown);
						foreach ($questions as $question) {
							if (isset($q_data[$question])) {
								if ($first_quest_id == -1)
									$first_quest_id = $question;
								$ret_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
								$ret_str .= "\t" . '<question_data>' . "\n";
								$ret_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
								$ret_str .= "\t" . '</question_data>' . "\n";
								$last_page_quest_id = $question;
								$tmp++;
							}
						}
					}
					$first_pb = 0;
				}

				if ($first_pb > 0)
					$task = 1;


				$ret_str .= "\t" . '<is_resume>' . $task . '</is_resume>' . "\n";

				if ($tmp > 0) {

					$nn = 0;

					if ($survey->sf_progressbar_type == '0') {
						foreach ($chain_questions as $chain_question) {
							if ($chain_question == $first_quest_id)
								break;
							$nn++;
						}

						$nn = floor(100 * $nn / count($q_data));
					} elseif ($survey->sf_progressbar_type == '1') {
						$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
						foreach ($pages as $p => $page) {
							$questions = explode('*', $page);
							if (in_array($first_quest_id, $questions))
								break;
							$nn++;
						}
						$nn = floor(100 * $nn / count($pages));
					}

					$page_task = 'start';
					if ($last_page_quest_id) {

						if (end($chain_questions) == $last_page_quest_id) {
							if (!$task) {
								$page_task = 'start_last_question';
							} else {
								$page_task = 'last_question';
							}
						}
					}

					$ret_str .= $tmp_str;
					$ret_str .= "\t" . '<task>' . $page_task . '</task>' . "\n";
					$ret_str .= "\t" . '<quest_count>' . $tmp . '</quest_count>' . "\n";
					$ret_str .= "\t" . '<is_prev>' . $survey->sf_prev_enable . '</is_prev>' . "\n";
					$ret_str .= "\t" . '<progress_bar>' . $nn . '%</progress_bar>' . "\n";
					$ret_str .= "\t" . '<progress_bar_txt><![CDATA[' . JText::_('COM_SURVEYFORCE_PROGRESS') . ' ' . (int) $nn . '%]]></progress_bar_txt>' . "\n";
				} else {
					$ret_str = '';
				}
			}
		}

		return $ret_str;
	}

	public function SF_ajaxAction() {
		$sf_task = $this->input->get('action', '');
		$limit = $this->input->get('limit', 0);
		$page = $this->input->get('count', 0);
		$survey_id = $this->input->get('survey', 0);
		$pagination = $this->input->get('pagination', 0);
		$this->SF_process_ajax($sf_task, $limit, $page, $survey_id, $pagination);
		exit();
	}

	public function SF_process_ajax($sf_task, $limit = 0, $count = 0, $survey_id = 0, $pagination = null) {
		error_reporting(error_reporting() ^ E_NOTICE);

		@ob_start();
		$ret_str = '';
		switch ($sf_task) {
			case 'start': $ret_str = $this->SF_StartSurvey();
				break;
			case 'next': $ret_str = $this->SF_NextQuestion();
				break;
			case 'prev': $ret_str = $this->SF_PrevQuestion();
				break;
			case 'result': $ret_str = $this->get_final_result($limit, $count, $survey_id, $pagination);
				break;
			default: break;
		}
		$iso = explode('=', _ISO);
		echo "\n" . date('Y-m-d H:i:s');
		$debug_str = ob_get_contents();

		@ob_end_clean();
		@ob_end_clean();
		if ($ret_str != "") {
			header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			header('Content-Type: text/xml');
			echo '<?xml version="1.0" encoding="' . $iso[1] . '" standalone="yes"?>';
			echo '<response>' . "\n";
			echo $ret_str;
			echo "\t" . '<debug><![CDATA[' . $debug_str . '&nbsp;]]></debug>' . "\n";
			echo '</response>' . "\n";
		} else {
			header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			header('Content-Type: text/xml');
			echo '<?xml version="1.0" encoding="' . $iso[1] . '" standalone="yes"?>';
			echo '<response>' . "\n";
			echo "\t" . '<task>failed</task>' . "\n";
			echo "\t" . '<quest_count>0</quest_count>' . "\n";
			echo "\t" . '<info>boom</info>' . "\n";
			echo "\t" . '<debug><![CDATA[' . $debug_str . '&nbsp;]]></debug>' . "\n";
			echo '</response>' . "\n";
		}
	}

	public function SF_NextQuestion($limit = 0, $page = 0) {

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$database = JFactory::getDbo();
		$my = JFactory::getUser();
		$ret_str = '';

		$preview = $this->input->getInt('preview', 0);

		$sf_config = JComponentHelper::getParams('com_surveyforce');

		$survey_id = $this->input->getInt('survey', 0); // id of the survey from 'survs' table
		$invite_num = $this->input->getString('invite', '');
		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $survey_id . "'";
		$database->setQuery($query);
		$survey = ($database->loadObject() == null ? array() : $database->loadObject());

		$query = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query);
		$isInstolled = $database->loadObject();
		
		$friends = array();
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))  {
			$query = "SELECT j.connect_to FROM #__community_connection AS j WHERE j.status = 1 AND j.connect_from = '{$survey->sf_author}'";
			$database->setQuery($query);
			$friends = $database->loadColumn();
		}

		$auto_pb = $survey->sf_auto_pb;

		$now =strtotime(JFactory::getDate());
		if (!$preview) {
			if (($survey->published) && (($survey->sf_date_expired == '0000-00-00 00:00:00' && $survey->sf_date_started == '0000-00-00 00:00:00') || (strtotime($survey->sf_date_expired) >= $now && strtotime($survey->sf_date_started) <= $now))) {
				if (($my->id) && ($survey->sf_reg)) {

				} elseif (($my->id) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array($my->id, $friends)) {

				} elseif ($my->id == $survey->sf_author) {

				} elseif ($survey->sf_public) {

				} elseif (($my->id > 0) && ($survey->sf_special > 0)) {

				} elseif ($survey->sf_invite && ($invite_num != '')) {
					$query = "SELECT inv_status FROM #__survey_force_invitations WHERE invite_num = '" . $invite_num . "'";
					$database->setQuery($query);
					$inv_data = $database->loadObjectList();
					if (count($inv_data) == 1) {
						if ($inv_data[0]->inv_status != 1) {
							// Continue
						} elseif ($inv_data[0]->inv_status == 1 && $survey->sf_inv_voting == 1) {
							// Invitation completed
							if ($survey->sf_after_start) {
								$query = "SELECT a.id FROM #__survey_force_user_starts AS a, #__survey_force_invitations AS b WHERE b.invite_num = '" . $invite_num . "' AND b.id = a.invite_id ORDER BY a.id DESC";
								$database->setQuery($query);
								$inv_start_id = $database->loadResult();
								$ret_str .= $this->get_graph_results($survey_id, $inv_start_id);
							}
							$ret_str .= "\t" . '<task>invite_complete</task>' . "\n";
							$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
							return $ret_str;
						}
					} else {
						//bad $invite_num
						return $ret_str;
					}
				} else {
					if ((!$my->id) && ($survey->sf_reg || $survey->sf_friend)) {
						$ret_str .= "\t" . '<task>timed_out</task>' . "\n";
						$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
						$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					}
					return $ret_str;
				}
			} else {
				return $ret_str;
			}
		}

		$user_id = $this->input->getString('user_id', ''); // unique id from 'starts' table
		$start_id = $this->input->getInt('start_id', 0); // id from 'starts' table

		$quest_ids = $this->input->getVar('quest_id'); // ids of the previous questions from the 'quests' table

		$answers = $this->input->getVar('answer', array());  // answers of the previous questions (for inserting into 'user_answers' table)
		$is_imp_scales = $this->input->getVar('is_imp_scale', array()); //1 - if imp.scale answer is SET
		$imp_scale_choices = $this->input->getVar('imp_scale', array());


		for ($hi = 0, $hn = count($answers); $hi < $hn; $hi++) {
			$answers[$hi] = urldecode($answers[$hi]);
		}

		if (($survey_id) && ($user_id) && is_array($quest_ids) && ($start_id)) {
			if ($preview) {
				$query = "SELECT survey_id, unique_id FROM #__survey_force_previews WHERE `start_id` = '" . $start_id . "'";
			} else {
				$query = "SELECT survey_id, unique_id FROM #__survey_force_user_starts WHERE id = '" . $start_id . "'";
			}
			$database->setQuery($query);
			$st_surv_data = ($database->loadObject() == null ? array() : $database->loadObject());

			$survey_time = JFactory::getDate();

			$start_survey = $st_surv_data->survey_id;
			$un_id = $st_surv_data->unique_id;
			if (($survey_id == $start_survey) && ($user_id == $un_id)) {

				$query = "SELECT sf_chain FROM #__survey_force_user_chain WHERE start_id = '" . $start_id . "'";
				$database->setQuery($query);
				$sf_chain = $database->LoadResult();

				if (preg_match_all('/(?<id>\d+)/', $sf_chain, $preg_match)) {
					$chain_questions = $preg_match['id'];
				}

				//$chain_questions = explode('*', str_replace('*#*', '*', $sf_chain));

				for ($ii = 0, $nn = count($is_imp_scales); $ii < $nn; $ii++) {
					$is_imp_scale = $is_imp_scales[$ii];
					$imp_scale_choice = $imp_scale_choices[$ii];
					$quest_id = $quest_ids[$ii];
					// write info to 'answer_imp.scale' table
					if ($is_imp_scale) {
						if (!$imp_scale_choice)
							$imp_scale_choice = 0;
						$query = "SELECT sf_impscale from #__survey_force_quests WHERE published = 1 AND id = '" . $quest_id . "'";
						$database->setQuery($query);
						$q_imp_scale = $database->LoadResult();
						if ($q_imp_scale) {
							$query = "SELECT count(*) from #__survey_force_iscales_fields WHERE id = '" . $imp_scale_choice . "' AND iscale_id = '" . $q_imp_scale . "'";
							$database->setQuery($query);
							$q_count_iscale = $database->LoadResult();
							if ($q_count_iscale == 1) {
								$query = "DELETE FROM #__survey_force_user_answers_imp WHERE start_id = '$start_id' AND survey_id = '$survey_id' AND quest_id = '$quest_id' AND iscale_id = '$q_imp_scale' ";
								$database->setQuery($query);
								$database->query();

								$query = "INSERT INTO #__survey_force_user_answers_imp (start_id, survey_id, quest_id, iscale_id, iscalefield_id, sf_imptime) "
									. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . $q_imp_scale . "', '" . $imp_scale_choice . "', '" . $survey_time . "')";
								$database->setQuery($query);
								$database->query();
							}
						}
					}
				}
				$next_id = null;
				for ($ii = 0, $nn = count($quest_ids); $ii < $nn; $ii++) {
					$quest_id = $quest_ids[$ii];
					$answer = $answers[$ii];
					// get question type
					$query = "SELECT sf_qtype from #__survey_force_quests WHERE published = 1 AND id = '" . $quest_id . "'";
					$database->setQuery($query);
					$qtype = $database->LoadResult();
					///////////////////////////////

					if ($next_id == null && !$survey->sf_random) {
						switch ($qtype) {
							case 1:
							case 5:
							case 6:
								$tmp_data = explode(',', $answer);
								$i = 0;
								$priority = 0;
								while ($i < count($tmp_data)) {
									$ttt = explode('-', $tmp_data[$i]);
									//print_r($ttt);die();
                                    $query = "SELECT * FROM `#__survey_force_rules` WHERE `quest_id` = '".$quest_id."' AND ((`answer_id`='".(!empty($ttt[0]) ? $ttt[0] : '0')."' AND `alt_field_id`='".(isset($ttt[1]) ? $ttt[1] : '0')."') OR (`answer_id`='9999997' AND `alt_field_id`='9999997')) ORDER BY `priority` DESC, `id` DESC LIMIT 0,1 ";
                                    $database->setQuery($query);
									$rule_data = null;
									$rule_data = $database->loadObject();
									if ($rule_data != null && $rule_data->priority > $priority) {
										$next_id = $rule_data->next_quest_id;
										$priority = $rule_data->priority;
										if (in_array($next_id, $quest_ids)) {
											$next_id = null;
										}
									}
									$i++;
								}
								break;
							case 9:
								$tmp_data = explode('!!,!!', $answer);
								if (strpos($answer, '!!-,-!!') > 0) {
									for ($i = 0, $n = count($tmp_data); $i < $n; $i++) {
										if (strpos($tmp_data[$i], '!!-,-!!') > 0) {
											$tmp = explode('!!-,-!!', $tmp_data[$i]);
											$tmp_data[$i] = $tmp[0];
											break;
										}
									}
								}

								$i = 0;
								$priority = 0;
								while ($i < count($tmp_data)) {
									$ttt = explode('!!--!!', $tmp_data[$i]);
									$query = "SELECT * FROM `#__survey_force_rules` WHERE `quest_id`='".$quest_id."' AND ((`answer_id`='".(!empty($ttt[0]) ? $ttt[0] : '0')."' AND `alt_field_id`='".(!empty($ttt[1]) ? $ttt[1] : '0')."') OR (`answer_id`='9999997' AND `alt_field_id`='9999997')) ORDER BY `priority` DESC, `id` DESC LIMIT 0,1 ";
									$database->setQuery($query);
									$rule_data = null;
									$rule_data = $database->loadObject();
									if ($rule_data != null && $rule_data->priority > $priority) {
										$next_id = $rule_data->next_quest_id;
										$priority = $rule_data->priority;
										if (in_array($next_id, $quest_ids)) {
											$next_id = null;
										}
									}
									$i++;
								}
								break;
							case 2:
								if (strpos($answer, '!!--!!') > 0) {
									$answer_id = explode('!!--!!', $answer);
									$answer_id = intval($answer_id[0]);
								}
								else
									$answer_id = $answer;
								$query = "SELECT * FROM `#__survey_force_rules` WHERE `quest_id`='".$quest_id."' AND (`answer_id`='".intval($answer_id)."' OR `answer_id`='9999997' )";
								$database->setQuery($query);
								$rule_data = null;
								$rule_data = $database->loadObject();
								if ($rule_data != null) {
									$next_id = $rule_data->next_quest_id;
									if (in_array($next_id, $quest_ids))
										$next_id = null;
								}
								break;
							case 3:
								$answer_str = '';
								if (strpos($answer, '!!--!!') > 0) {
									$tmp_data = explode('!!,!!', $answer);
									foreach ($tmp_data as $i => $data) {
										if (strpos($data, '!!--!!') > 0) {
											$answer_id = explode('!!--!!', $answer);
											$answer_str .= intval($answer_id[0]) . ',';
										}
										else
											$answer_str .= intval($data) . ',';
									}
									$answer_str = substr($answer_str, 0, -1);
								}
								else {
									$answer_str = str_replace('!!,!!', ',', $answer);
								}
								if ( empty($answer_str) ) $answer_str = 0;
								$query = "SELECT * FROM `#__survey_force_rules` WHERE `quest_id`='".$quest_id."' AND `answer_id` IN (".$answer_str.", '9999997') ORDER BY `priority` DESC, `id` DESC LIMIT 0,1 ";
								$database->setQuery($query);
								$rule_data = null;
								$rule_data = $database->loadObject();
								if ($rule_data != null) {
									$next_id = $rule_data->next_quest_id;
									if (in_array($next_id, $quest_ids))
										$next_id = null;
								}
								break;
						}

						if ($next_id != null) {
							$quest_data = $chain_questions;

							$nxt = 0;
							foreach ($quest_data as $q_id) {
								if ($q_id != $quest_id && $nxt == 0) {
									continue;
								} elseif ($q_id == $quest_id && $nxt == 0) {
									$nxt = 1;
									continue;
								}
								if ($q_id == $next_id) {
									break;
								}
								if (!in_array($q_id, $quest_ids)) { //insert data only if question not on current page
									$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = " . $q_id;
									$database->setQuery($query);
									$database->query();
									$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
										. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $q_id . "', '0', '0', '0', '" . $survey_time . "')";
									$database->setQuery($query);
									$database->query();
								}
							}
						}
					}
					// insert results to the Database
					switch ($qtype) {
						case 1:
						case 5:
						case 6:
							$tmp_data = explode(',', $answer);
							$i = 0;
							$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$database->query();
							while ($i < count($tmp_data)) {
								$ttt = explode('-', $tmp_data[$i]);
								$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
									. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . (isset($ttt[0]) ? $ttt[0] : '0') . "', '" . (isset($ttt[1]) ? $ttt[1] : '0') . "', '" . (int) $next_id . "', '" . $survey_time . "')";
								$database->setQuery($query);
								$database->query();
								$i++;
							}
							break;
						case 9:
							$tmp_data = explode('!!,!!', $answer);
							$other_id = -1;
							$other_txt = '';
							$atxt_id = 0;
							$atxt_id2 = 0;

							$query = "SELECT a.next_quest_id FROM #__survey_force_user_answers AS a WHERE a.start_id = $start_id AND a.survey_id = $survey_id AND a.quest_id = $quest_id AND a.answer > 0  AND a.next_quest_id > 0 ";
							$database->setQuery($query);
							$answer_id = $database->loadResult();
							if ($answer_id > 0) {
								$query = "DELETE FROM #__survey_force_user_ans_txt WHERE id = '" . $answer_id . "' AND start_id = $start_id ";
								$database->setQuery($query);
								$database->query();
							}

							$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$database->query();

							if (strpos($answer, '!!-,-!!') > 0) {
								for ($i = 0, $n = count($tmp_data); $i < $n; $i++) {
									if (strpos($tmp_data[$i], '!!-,-!!') > 0) {
										$tmp = explode('!!-,-!!', $tmp_data[$i]);
										$tmp_data[$i] = $tmp[0];
										$other_txt = $tmp[1];
										$tmp = explode('!!--!!', $tmp[0]);
										$other_id = intval($tmp[0]);
										break;
									}
								}

								$query = "INSERT INTO #__survey_force_user_ans_txt (ans_txt, start_id) "
									. "\n VALUES ('" . $other_txt . "', '" . $start_id . "')";
								$database->setQuery($query);
								$database->query();
								$atxt_id2 = $database->insertid();
							}
							$i = 0;
							while ($i < count($tmp_data)) {
								$ttt = explode('!!--!!', $tmp_data[$i]);
								if ($other_id == (isset($ttt[0]) ? $ttt[0] : '0'))
									$atxt_id = $atxt_id2;
								$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
									. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . (isset($ttt[1]) ? $ttt[1] : '0') . "', '" . (isset($ttt[0]) ? $ttt[0] : '0') . "', '" . $atxt_id . "', '" . $survey_time . "')";
								$database->setQuery($query);
								$database->query();
								$atxt_id = 0;
								$i++;
							}
							break;
						case 2:
							$query = "SELECT ans_field FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$answer_id = $database->loadResult();
							if ($answer_id > 0) {
								$query = "DELETE FROM #__survey_force_user_ans_txt WHERE id = $answer_id AND start_id = $start_id ";
								$database->setQuery($query);
								$database->query();
							}
							$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$database->query();

							$tmp_txt = '';
							$atxt_id = 0;
							if (strpos($answer, '!!--!!') > 0) {
								$tmp_data = explode('!!--!!', $answer);
								$tmp_txt = strval($tmp_data[1]);
								$tmp_data = intval($tmp_data[0]);
								if (strlen($tmp_txt) > 0) {
									$query = "INSERT INTO #__survey_force_user_ans_txt (ans_txt, start_id) "
										. "\n VALUES ('" . $tmp_txt . "', '" . $start_id . "')";
									$database->setQuery($query);
									$database->query();
									$atxt_id = $database->insertid();
								} else {
									$tmp_data = 0;
								}
							}
							else
								$tmp_data = intval($answer);


							$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
								. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . $tmp_data . "', '" . $atxt_id . "', '0', '" . $survey_time . "')";
							$database->setQuery($query);
							$database->query();
							break;
						case 3:
							$tmp_data = explode('!!,!!', $answer);
							$i = 0;
							$query = "SELECT ans_field FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$answer_id = $database->loadResult();
							if ($answer_id > 0) {
								$query = "DELETE FROM #__survey_force_user_ans_txt WHERE id = $answer_id AND start_id = $start_id ";
								$database->setQuery($query);
								$database->query();
							}
							$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$database->query();

							while ($i < count($tmp_data)) {
								$tmp_txt = '';
								$atxt_id = 0;
								if (strpos($tmp_data[$i], '!!--!!') > 0) {
									$tmp_datas = explode('!!--!!', $tmp_data[$i]);
									$tmp_txt = strval($tmp_datas[1]);
									$tmp_datas = intval($tmp_datas[0]);

									if (strlen($tmp_txt) > 0) {
										$query = "INSERT INTO #__survey_force_user_ans_txt (ans_txt, start_id) "
											. "\n VALUES ('" . $tmp_txt . "', '" . $start_id . "')";
										$database->setQuery($query);
										$database->query();
										$atxt_id = $database->insertid();
									} else {
										$tmp_datas = 0;
									}
								}
								else
									$tmp_datas = intval($tmp_data[$i]);


								$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
									. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . $tmp_datas . "', '" . $atxt_id . "', '0', '" . $survey_time . "')";
								$database->setQuery($query);
								$database->query();
								$i++;
							}
							break;
						case 4:
							$query = "SELECT answer FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
							$database->setQuery($query);
							$result = $database->loadRowList();
							$answer_id = array();
							foreach($result as $answer_res)
								array_push($answer_id, $answer_res[0]);

							if (count($answer_id) > 0) {
								$query = "DELETE FROM #__survey_force_user_ans_txt WHERE id IN (" . implode(',', $answer_id) . ") AND start_id = $start_id ";
								$database->setQuery($query);
								$database->query();
								$query = "DELETE FROM #__survey_force_user_answers WHERE start_id = $start_id AND survey_id = $survey_id AND quest_id = $quest_id ";
								$database->setQuery($query);
								$database->query();
							}
							$tmp_data = $database->escape(urldecode($answer));
							if (strpos($answer, '!!--!!') > 0) {
								$tmp_data = explode('!!,!!', $tmp_data);
								$i = 0;
								while ($i < count($tmp_data)) {
									$tmp_datas = explode('!!--!!', $tmp_data[$i]);
									$tmp_txt = strval($tmp_datas[1]);
									$tmp_datas = intval($tmp_datas[0]) + 1;

									if (strlen($tmp_txt) > 0) {
										$query = "INSERT INTO #__survey_force_user_ans_txt (ans_txt, start_id) "
											. "\n VALUES ('" . $tmp_txt . "', '" . $start_id . "')";
										$database->setQuery($query);
										$database->query();
										$atxt_id = $database->insertid();
									}
									else
										$atxt_id = 0;

									$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
										. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . $atxt_id . "', '" . $tmp_datas . "', '0', '" . $survey_time . "')";
									$database->setQuery($query);
									$database->query();
									$i++;
								}
							}
							else {
								if (strlen($tmp_data) > 0) {
									$query = "INSERT INTO #__survey_force_user_ans_txt (ans_txt, start_id) "
										. "\n VALUES ('" . $tmp_data . "', '" . $start_id . "')";
									$database->setQuery($query);
									$database->query();
									$atxt_id = $database->insertid();
								}
								else
									$atxt_id = 0;

								$query = "INSERT INTO #__survey_force_user_answers (start_id, survey_id, quest_id, answer, ans_field, next_quest_id, sf_time) "
									. "\n VALUES ('" . $start_id . "', '" . $survey_id . "', '" . $quest_id . "', '" . $atxt_id . "', 0, '0', '" . $survey_time . "')";
								$database->setQuery($query);
								$database->query();
							}
							break;
					}
				}


				$query = "SELECT a.quest_id FROM `#__survey_force_quest_show` AS a, #__survey_force_user_answers AS b, #__survey_force_quests c WHERE a.survey_id = '" . $survey_id . "' AND c.id = a.quest_id_a AND ((c.sf_qtype NOT IN (2, 3) AND a.answer = b.answer AND a.ans_field = b.ans_field) OR (c.sf_qtype IN (2, 3) AND a.answer = b.answer)) AND b.start_id = '" . $start_id . "' ";
				$database->setQuery($query);
				$not_shown = $database->loadColumn();

				if (!count($not_shown))
					$not_shown = array(0);

				if ($survey->sf_random) {
					$not_shown = array(0);
					$next_id = null;
				}
				$chain_questions = array_diff($chain_questions, $not_shown);

				$not_shown_str = implode(',', $not_shown);
				$query = "SELECT * FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' " . ($auto_pb ? " AND sf_qtype <> 8 " : '') . " AND id IN ('" . @implode("','", $chain_questions) . "') AND id NOT IN (" . $not_shown_str . ") ORDER BY sf_section_id, ordering, id ";
				$database->setQuery($query);
				$q_data = $database->loadObjectList('id');

				$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = $survey_id AND id IN ( '" . implode('\' , \'', $quest_ids) . "' ) AND id IN ('" . @implode("','", $chain_questions) . "') ORDER BY sf_section_id ASC, ordering DESC, id DESC LIMIT 0 , 1";
				$database->setQuery($query);
				$last_id = $database->loadResult();
				$tmp_str = '';
				$tmp = 0;
				$first_quest_id = -1;
				// id of last question that would displayed
				$last_page_quest_id = 0;

				if ($next_id == null) {
					$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
					foreach ($pages as $p => $page) {
						$questions = explode('*', $page);
						$questions = array_diff($questions, $not_shown);
						if (!count($questions))
							continue;

						if (in_array($last_id, $questions)) {
							$last_id = 0;
							continue;
						}

						if ($last_id == 0) {
							foreach ($questions as $question) {
								if (isset($q_data[$question])) {
									if ($first_quest_id == -1)
										$first_quest_id = $question;
									$tmp_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
									$tmp_str .= "\t" . '<question_data>' . "\n";
									$tmp_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
									$tmp_str .= "\t" . '</question_data>' . "\n";
									$last_page_quest_id = $question;
									$tmp++;
								}
							}
							break;
						}
					}
				}
				else {

					$pages = explode('*#*', $sf_chain);
					foreach ($pages as $p => $page) {
						$questions = explode('*', $page);
						$questions = array_diff($questions, $not_shown);
						if (in_array($next_id, $questions)) {
							foreach ($questions as $question) {
								if (isset($q_data[$question])) {
									if ($first_quest_id == -1)
										$first_quest_id = $question;
									$tmp_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
									$tmp_str .= "\t" . '<question_data>' . "\n";
									$tmp_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
									$tmp_str .= "\t" . '</question_data>' . "\n";
									$last_page_quest_id = $question;
									$tmp++;
								}
							}
							break;
						}
					}
				}

				$is_finish_question = JFactory::getApplication()->input->getInt('finish', 0);
				$tmp = ($is_finish_question) ? 0 : $tmp;

				if ($tmp > 0) {

					$nn = 0;

					if ($survey->sf_progressbar_type == '0') {
						foreach ($chain_questions as $chain_question) {
							if ($chain_question == $first_quest_id)
								break;
							$nn++;
						}

						$nn = floor(100 * $nn / count($q_data));
					} elseif ($survey->sf_progressbar_type == '1') {
						$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
						foreach ($pages as $p => $page) {
							$questions = explode('*', $page);
							if (in_array($first_quest_id, $questions))
								break;
							$nn++;
						}
						$nn = floor(100 * $nn / count($pages));
					}

					$page_task = 'next';
					if ($last_page_quest_id) {
						if (end($chain_questions) == $last_page_quest_id) {
							$page_task = 'last_question';
						}
					}

					$ret_str .= "\t" . '<task>' . $page_task . '</task>' . "\n";
					$ret_str .= "\t" . '<quest_count>' . $tmp . '</quest_count>' . "\n";
					$ret_str .= "\t" . '<is_prev>' . $survey->sf_prev_enable . '</is_prev>' . "\n";
					$ret_str .= "\t" . '<progress_bar>' . $nn . '%</progress_bar>' . "\n";
					$ret_str .= "\t" . '<progress_bar_txt><![CDATA[' . JText::_('COM_SURVEYFORCE_PROGRESS') . ' ' . (int) $nn . '%]]></progress_bar_txt>' . "\n";
					$ret_str .= $tmp_str;
				} else {


					$ret_str .= "\t" . '<task>finish</task>' . "\n";
					$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					$ret_str .= "\t" . '<is_prev>' . $survey->sf_prev_enable . '</is_prev>' . "\n";
					$ret_str .= "\t" . '<is_final_question>0</is_final_question>' . "\n";
					$query = "SELECT usertype, invite_id FROM #__survey_force_user_starts WHERE id = '" . $start_id . "' and unique_id = '" . $user_id . "'";
					$database->setQuery($query);
					$surv_start_data = $database->loadObjectList();
					if (isset($surv_start_data[0]) && $surv_start_data[0]->usertype == 2) {
						if ($surv_start_data[0]->invite_id) {
							$query = "UPDATE #__survey_force_invitations SET inv_status = 1 WHERE id = '" . $surv_start_data[0]->invite_id . "'";
							$database->setQuery($query);
							$database->query();
						}
					}
					$query = "UPDATE #__survey_force_user_starts SET is_complete = 1 WHERE id = '" . $start_id . "' and unique_id = '" . $user_id . "'";
					$database->setQuery($query);
					$database->query();

					$query = "UPDATE #__survey_force_user_starts SET is_complete = 1 WHERE id = '" . $start_id . "' and unique_id = '" . $user_id . "'";
					$database->setQuery($query);
					$database->query();

					$query = "SELECT `a`.`sf_redirect_enable`, `a`.`sf_redirect_url`, `a`.`sf_redirect_delay`, `a`.`sf_fpage_type`, `a`.`sf_fpage_text`, `b`.`email` FROM `#__survey_force_survs` AS `a` LEFT JOIN `#__users` AS `b` ON `a`.`sf_author` = `b`.`id` WHERE `a`.`id` = '$survey_id' ";
					$database->setQuery($query);
					$fpage = null;
					$fpage = $database->loadObject();
					$ret_str .= "\t" . '<fpage_type>' . $fpage->sf_fpage_type . '</fpage_type>' . "\n";


					if (!$preview) {
						if ($sf_config->get('sf_an_mail') || $sf_config->get('sf_an_mail_others')) {

							$message = $this->get_user_result($start_id);
							$message = $sf_config->get('sf_an_mail_text') . " \n\n " . $message;

							$subject = '[SURVEY] ' . $sf_config->get('sf_an_mail_subject');

                            $emails = array();
							if(!empty(trim($sf_config->get('sf_an_mail_other_emails')))) {
                                $emails = explode(',', $sf_config->get('sf_an_mail_other_emails'));
                            }
                            if (!empty($emails)){
                                for($i=0;$i<count($emails);$i++) {
                                    if(empty(trim($emails[$i]))) {
                                        unset($emails[$i]);
                                    }
                                }
                                $emails = array_values($emails);
                            }
							if ($sf_config->get('sf_an_mail') && !empty($fpage->email)) {
								$emails[] = $fpage->email;
							}


							if (!empty($emails)){

								$mailer = JFactory::getMailer();

								$config = new JConfig();
								$mailfrom = $config->mailfrom;
								$sitename = $config->fromname;

								$sender = array($mailfrom, $sitename);
								$mailer->setSender($sender);

								foreach ($emails as &$email){
									$email = trim($email);
								}

								$mailer->addRecipient($emails);
								$body   = $message;
								$mailer->isHTML(true);
								$mailer->setSubject($subject);
								$mailer->setBody($body);

								try {
									$send = $mailer->Send();
								} catch (Exception $e) {
									$error = $e->getMessage();
								}

								if ($send !== true) {
									echo 'Error sending email: ' . $error;
								}
							}
						}
					}


					$fpage_text = (($fpage->sf_redirect_enable && $fpage->sf_redirect_delay) || !$fpage->sf_redirect_enable) ? $this->get_final_result(1, 20, $survey_id, 0) : "";
					$ret_str .= "\t" . '<fpage_text><![CDATA[' . stripslashes($fpage_text) . '&nbsp;]]></fpage_text>' . "\n";
					$ret_str .= "\t" . '<fpage_redirect_enable><![CDATA[' . $fpage->sf_redirect_enable . ']]></fpage_redirect_enable>' . "\n";
					$ret_str .= "\t" . '<fpage_redirect_url><![CDATA[' . $fpage->sf_redirect_url . ']]></fpage_redirect_url>' . "\n";
					$ret_str .= "\t" . '<fpage_redirect_delay><![CDATA[' . $fpage->sf_redirect_delay . ']]></fpage_redirect_delay>' . "\n";


					if (file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php')) {
						include_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php');
						include_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php');						

						 error_reporting(E_ALL);
						 ini_set('display_errors', 1);
						 
						CuserPoints::assignPoint("completed.survey" . $survey_id);
					}

					$params = array();
                    // for B/C
                    $params['params'] = array();
					$params['survey_id'] = $survey_id;
					$params['start_id'] = $start_id;
					$params['passed'] = 1;
                    $params['user_points'] = 1;
                    // for B/C
                    $params['params'] = $params;

                    JPluginHelper::importPlugin('system');
                    $dispatcher = JEventDispatcher::getInstance();
                    $dispatcher->trigger('onAfterSurveyComplete', array($survey_id));
					$dispatcher->trigger('onSForceFinished', $params);
				}
			}
		}

		return $ret_str;
	}

	public function get_final_result($limit, $count, $survey_id, $pagination) {

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$database = JFactory::getDbo();
		$ret_str = '';
		$start_id = intval($this->input->get('start_id', 0)); // id from 'starts' table

		$query = "SELECT `a`.`sf_fpage_type`, `a`.`sf_fpage_text`, `b`.`email` FROM `#__survey_force_survs` AS `a` LEFT JOIN `#__users` AS `b` ON `a`.`sf_author` = `b`.`id` WHERE `a`.`id` = '$survey_id' ";
		$database->setQuery($query);
		$fpage = null;
		$fpage = $database->loadObject();
		$ret_str = $fpage->sf_fpage_type;

		if ($fpage->sf_fpage_type == 0) {
			$ret_str = $fpage->sf_fpage_text;
		} elseif ($fpage->sf_fpage_type == 1 || $fpage->sf_fpage_type == 2) {

			$ret_str = '';
			if ($fpage->sf_fpage_type == 2)
				$ret_str = $fpage->sf_fpage_text;

			$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' ORDER BY sf_section_id, ordering, id ";

			$database->setQuery($query);
			$questions = $database->loadAssocList();

			set_time_limit(0);
			require (JPATH_SITE . '/components/com_surveyforce/helpers/generate.php');

			$sf_config = JComponentHelper::getParams('com_surveyforce');
			$prefix = $sf_config->get('sf_result_type') == 'Bar' ? 'b' : 'p';
			$gg = new sf_ImageGenerator(array($sf_config->get('sf_result_type')));
			$gg->width = $sf_config->get($prefix . '_width', 600 );
			$gg->height = $sf_config->get($prefix . '_height', 250);

			$gg->width = ( !empty($gg->width) ? $gg->width : 600);
			$gg->height = ( !empty($gg->height) ? $gg->height : 250);

			$gg->clearOldImages(); //delete yesterday images
			$imgs = array();


			$fpage_text = '<p align="left"><strong>' . JText::_('COM_SURVEYFORCE_SF_SURVEY_RESULTS') . '</strong></p><br/>';



			foreach ($questions as $question) {
				$img_src = $gg->getImage($survey_id, $question['id'], $start_id);
				if (is_array($img_src)) {
					foreach ($img_src as $imgsrc) {
						$imgs[] = $imgsrc;
					}
				} elseif ($img_src) {
					$imgs[] = $img_src;
				}
			}


			$img_per_page = $count;
			$limitstart = $limit;
			$total = count($imgs);

			$ret_pagination = "<div class='pagination-surv' style='border-top: 1px solid #CCCCCC;margin: 10px 0 5px;
            padding: 10px 0 0;
            text-align: center;
            width: 100%;'>";
			$pages = ceil($total / $img_per_page);
			$page = 1;

			if ($pages > 1) {
				if ($limitstart == 1)
					$ret_pagination .= '&nbsp;&nbsp;' . JText::_('COM_SURVEYFORCE_FIRST') . '&nbsp;&nbsp;';
				else
					$ret_pagination .= '&nbsp;&nbsp;<a href="javascript: pagination_go(1,' . $survey_id . ')">' . JText::_('COM_SURVEYFORCE_FIRST') . '</a>&nbsp;&nbsp;';
				for ($i = 0; $i < $pages; $i++) {
					if ($limitstart >= ($i * $img_per_page) && $limitstart < ($i + 1) * $img_per_page) {
						$ret_pagination .= ($i + 1) . '&nbsp;&nbsp;';
						$page = $i + 1;
					} else {
						if ($i == 0) {
							$ret_pagination .= '<a href="javascript: pagination_go(1,' . $survey_id . ')">' . ($i + 1) . '</a>&nbsp;&nbsp;';
						}
						else
							$ret_pagination .= '<a href="javascript: pagination_go(' . $i * $img_per_page . ',' . $survey_id . ')">' . ($i + 1) . '</a>&nbsp;&nbsp;';
					}
				}

				if ($limitstart == $img_per_page * ($pages - 1))
					$ret_pagination .= JText::_('COM_SURVEYFORCE_LAST');
				else
					$ret_pagination .= '<a href="javascript: pagination_go(' . $img_per_page * ($pages - 1) . ',' . $survey_id . ')">' . JText::_('COM_SURVEYFORCE_LAST') . '</a>';
			}
			$ret_pagination .= "</div>";


			$i = 0;
			if ($limit == 1) {
				for ($i = 0; $i <= $count - 1; $i++) {
				    if(isset($imgs[$i])) {
                        $fpage_text .= $imgs[$i];
                    }
				}
			} else {
				for ($i = $limit; $i <= $limit - 1 + $count; $i++) {
					if (isset($imgs[$i])) {
						$fpage_text .= $imgs[$i];
					}
				}
			}
			$fpage_text .= $ret_pagination;

			if ($fpage_text == '<p align="left"><strong>' . JText::_('COM_SURVEYFORCE_SF_SURVEY_RESULTS') . '</strong></p><br/>')
				$fpage_text = 'No graphs available.';
			$ret_str .= $fpage_text;
		}
		else {
			$ret_str = '<strong>End of the survey - Thank you for your time.</strong>';
		}
		if ($pagination) {
			echo $ret_str;
			die;
		} else {
			return $ret_str;
		}
	}

	public function SF_PrevQuestion() {
	
		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$database = JFactory::getDbo();
		$ret_str = '';
		$preview = intval($this->input->get('preview', 0));
		$survey_id = intval($this->input->get('survey', 0)); // id of the survey from 'survs' table
		$invite_num = strval($this->input->get('invite', ''));
		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $survey_id . "'";
		$database->setQuery($query);
		$survey = $database->loadObject();

		$sf_config = JComponentHelper::getParams('com_surveyforce');

		$query = "SELECT * FROM #__extensions WHERE name = 'com_community' AND type = 'component'";
		$database->setQuery($query);
		$isInstolled = $database->loadObject();
		
		$friends = array();
		if ($sf_config->get('sf_enable_jomsocial_integration') && !empty($isInstolled))  {
			$query = "SELECT j.connect_to FROM #__community_connection AS j WHERE j.status = 1 AND j.connect_from = '{$survey->sf_author}'";
			$database->setQuery($query);
			$friends = $database->loadColumn();
		}


		$auto_pb = $survey->sf_auto_pb;
		$user_id = strval($this->input->get('user_id', '')); // unique id from 'starts' table
		$start_id = intval($this->input->get('start_id', 0)); // id from 'starts' table

		$quest_ids = $this->input->get('quest_id', array(), 'array'); // id of the previous question from the 'quests' table


		$now = strtotime(JFactory::getDate());
		if (!$preview) {
			if (($survey->published) && ($survey->sf_date_expired == '0000-00-00 00:00:00' && $survey->sf_date_started == '0000-00-00 00:00:00') || (strtotime($survey->sf_date_expired) >= $now && strtotime($survey->sf_date_started) <= $now)) {
				if ((JFactory::getUser()->id) && ($survey->sf_reg)) {

				} elseif ((JFactory::getUser()->id) && ($survey->sf_friend) && $sf_config->get('sf_enable_jomsocial_integration') && in_array(JFactory::getUser()->id, $friends)) {

				} elseif (JFactory::getUser()->id == $survey->sf_author) {

				} elseif ($survey->sf_public) {

				} elseif ((JFactory::getUser()->id > 0) && ($survey->sf_special > 0)) {

				} elseif ($survey->sf_invite && ($invite_num != '')) {
					$query = "SELECT inv_status FROM #__survey_force_invitations WHERE invite_num = '" . $invite_num . "'";
					$database->setQuery($query);
					$inv_data = $database->loadObjectList();
					if (count($inv_data) == 1) {
						if ($inv_data[0]->inv_status != 1) {
							// Continue
						} elseif ($inv_data[0]->inv_status == 1 && $survey->sf_inv_voting == 1) {
							// Invitation completed
							if ($survey->sf_after_start) {
								$query = "SELECT a.id FROM #__survey_force_user_starts AS a, #__survey_force_invitations AS b WHERE b.invite_num = '" . $invite_num . "' AND b.id = a.invite_id ORDER BY a.id DESC";
								$database->setQuery($query);
								$inv_start_id = $database->loadResult();
								$ret_str .= $this->get_graph_results($survey_id, $inv_start_id);
							}
							$ret_str .= "\t" . '<task>invite_complete</task>' . "\n";
							return $ret_str;
						}
					} else {
						//bad $invite_num
						return $ret_str;
					}
				} else {
					if ((!JFactory::getUser()->id) && ($survey->sf_reg || $survey->sf_friend)) {
						$ret_str .= "\t" . '<task>timed_out</task>' . "\n";
						$ret_str .= "\t" . '<quest_count>0</quest_count>' . "\n";
					}
					return $ret_str;
				}
			} else {
				return $ret_str;
			}
		}

		if (($survey_id) && ($user_id) && is_array($quest_ids) && ($start_id)) {

			if ($preview) {
				$query = "SELECT survey_id, unique_id FROM #__survey_force_previews WHERE `start_id` = '" . $start_id . "'";
			} else {
				$query = "SELECT survey_id, unique_id FROM #__survey_force_user_starts WHERE id = '" . $start_id . "'";
			}

			$database->setQuery($query);
			$st_surv_data = $database->loadObjectList();

			$survey_time = JFactory::getDate();

			$start_survey = $st_surv_data[0]->survey_id;
			$un_id = $st_surv_data[0]->unique_id;

			$query = "SELECT sf_chain FROM #__survey_force_user_chain WHERE start_id = '" . $start_id . "'";
			$database->setQuery($query);
			$sf_chain = $database->LoadResult();
			$chain_questions = explode('*', str_replace('*#*', '*', $sf_chain));

			if (($survey_id == $start_survey) && ($user_id == $un_id)) {
				$prev_id = null;

				$query = "SELECT a.quest_id FROM `#__survey_force_quest_show` AS a, #__survey_force_user_answers AS b, #__survey_force_quests c WHERE a.survey_id = '" . $survey_id . "' AND c.id = a.quest_id_a AND ((c.sf_qtype NOT IN (2, 3) AND a.answer = b.answer AND a.ans_field = b.ans_field) OR (c.sf_qtype IN (2, 3) AND a.answer = b.answer)) AND b.start_id = '" . $start_id . "' ";
				$database->setQuery($query);

				$not_shown = $database->loadColumn();
				if (!count($not_shown))
					$not_shown = array(0);

				if ($survey->sf_random) {
					$not_shown = array(0);
				}

				$chain_questions = array_diff($chain_questions, $not_shown);

				$not_shown_str = implode(',', $not_shown);

				$query = "SELECT * FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' " . ($auto_pb ? " AND sf_qtype <> 8 " : '') . "  AND id NOT IN ( $not_shown_str ) AND id IN ('" . @implode("','", $chain_questions) . "') ORDER BY sf_section_id, ordering, id ";
				$database->setQuery($query);
				$q_data = $database->loadObjectList('id');

				$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = $survey_id AND id IN ( " . implode(', ', $quest_ids) . " ) AND id IN ('" . @implode("','", $chain_questions) . "')  AND id NOT IN ( $not_shown_str ) ORDER BY sf_section_id, ordering ASC, id ASC LIMIT 0 , 1";
				$database->setQuery($query);
				$first_id2 = $database->loadResult();

				$query = "SELECT ordering FROM #__survey_force_quests WHERE published = 1 AND sf_survey = $survey_id AND id = '$first_id2'  AND id NOT IN ( $not_shown_str ) ORDER BY sf_section_id, ordering ASC, id ASC LIMIT 0 , 1";
				$database->setQuery($query);
				$first_id_order = $database->loadResult();

				$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = $survey_id AND id <> '$first_id2' AND id IN ('" . @implode("','", $chain_questions) . "') AND id NOT IN ( $not_shown_str ) AND ordering <= $first_id_order AND sf_qtype <> 8 ORDER BY sf_section_id ASC, ordering DESC, id ASC LIMIT 0 , 1";
				$database->setQuery($query);
				$first_id = $database->loadResult();

				$prev_id = null;
				$query = " SELECT c.id "
					. " FROM #__survey_force_rules AS a, #__survey_force_user_answers AS b, #__survey_force_quests AS c "
					. " WHERE  c.published = 1 AND a.next_quest_id IN (" . implode(',', $quest_ids) . ") "
					. " AND b.start_id = '$start_id' AND b.survey_id = '$survey_id' AND b.quest_id = a.quest_id  AND (b.answer = a.answer_id OR (a.answer_id = 9999997 AND b.next_quest_id IN (" . implode(',', $quest_ids) . "))) AND ( b.ans_field = a.alt_field_id OR (a.alt_field_id = 9999997 AND b.next_quest_id IN (" . implode(',', $quest_ids) . ") ) OR c.sf_qtype IN ( 2, 3 ) ) "
					. " AND c.id = a.quest_id  AND c.id NOT IN ( $not_shown_str ) AND c.id IN ('" . @implode("','", $chain_questions) . "')"
						. " ORDER BY a.priority DESC, a.id DESC, c.ordering, c.id ";

				$database->setQuery($query);
				$prev_id = $database->loadResult();

				$tmp = 0;
				$tmp_str = '';
				$first_pb = 0;
				$last_pb = 0;
				$first_real_quest = 0;
				$first_quest_id = 0;
				$first_quest_id = -1;

				$page_no = 0;

				if ($prev_id == null) {

					$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
					foreach ($pages as $p => $page) {
						$questions = explode('*', $page);
						if (in_array($quest_ids[0], $questions) && isset($pages[$p - 1])) {
							$questions = explode('*', $pages[$p - 1]);
							$questions = array_diff($questions, $not_shown);
							foreach ($questions as $question) {
								if (isset($q_data[$question])) {
									if ($first_quest_id == -1)
										$first_quest_id = $question;
									$tmp_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
									$tmp_str .= "\t" . '<question_data>' . "\n";
									$tmp_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
									$tmp_str .= "\t" . '</question_data>' . "\n";
									$pq_id = $question;
									$tmp++;
								}
							}
							$page_no = ($p - 1);
							break;
						}
					}
				}
				else {
					$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
					foreach ($pages as $p => $page) {
						$questions = explode('*', $page);
						$questions = array_diff($questions, $not_shown);
						if (in_array($prev_id, $questions)) {
							foreach ($questions as $question) {
								if (isset($q_data[$question])) {
									if ($first_quest_id == -1)
										$first_quest_id = $question;
									$tmp_str .= "\t" . '<is_final_question>' . $q_data[$question]->is_final_question . '</is_final_question>' . "\n";
									$tmp_str .= "\t" . '<question_data>' . "\n";
									$tmp_str .= SurveyforceHelper::SF_GetQuestData($q_data[$question], $start_id, $survey);
									$tmp_str .= "\t" . '</question_data>' . "\n";
									$pq_id = $question;
									$tmp++;
								}
							}
							$page_no = $p;
							break;
						}
					}
				}

				if ($tmp > 0) {

					if ($page_no == 0)
						$ret_str .= "\t" . '<task>prev0</task>' . "\n";
					else
						$ret_str .= "\t" . '<task>prev</task>' . "\n";

					$ret_str .= "\t" . '<is_prev>' . $survey->sf_prev_enable . '</is_prev>' . "\n";
					$ret_str .= "\t" . '<quest_count>' . $tmp . '</quest_count>' . "\n";

					$nn = 0;

					if ($survey->sf_progressbar_type == '0') {
						foreach ($chain_questions as $chain_question) {
							if ($chain_question == $first_quest_id)
								break;
							$nn++;
						}

						$nn = floor(100 * $nn / count($q_data));
					} elseif ($survey->sf_progressbar_type == '1') {
						$pages = explode('*#*', SurveyforceHelper::clear_chain($sf_chain, $not_shown));
						foreach ($pages as $p => $page) {
							$questions = explode('*', $page);
							if (in_array($first_quest_id, $questions))
								break;
							$nn++;
						}
						$nn = floor(100 * $nn / count($pages));
					}

					$ret_str .= "\t" . '<progress_bar>' . $nn . '%</progress_bar>' . "\n";
					$ret_str .= "\t" . '<progress_bar_txt><![CDATA[' . JText::_('COM_SURVEYFORCE_PROGRESS') . ' ' . (int) $nn . '%]]></progress_bar_txt>' . "\n";
					$ret_str .= $tmp_str;
				}
				else {
					$ret_str = '';
				}
			}
		}
		return $ret_str;
	}

	public function get_user_result($id) {

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$database = JFactory::getDbo();

		$query = "SELECT s.*, u.username reg_username, u.name reg_name, u.email reg_email,"
			. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
			. "\n FROM #__survey_force_user_starts as s"
			. "\n LEFT JOIN #__users as u ON u.id = s.user_id and s.usertype=1"
			. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = s.user_id and s.usertype=2"
			. "\n WHERE s.id = '" . $id . "'";
		$database->setQuery($query);
		$start_data = $database->loadObjectList();

		$query = "SELECT * FROM #__survey_force_survs WHERE id = '" . $start_data[0]->survey_id . "' ";
		$database->setQuery($query);
		$survey_data = $database->loadObjectList();

		$query = "SELECT q.*"
			. "\n FROM #__survey_force_quests as q"
			. "\n WHERE q.published = 1 AND q.sf_survey = '" . $start_data[0]->survey_id . "' AND sf_qtype NOT IN (7, 8) "
			. "\n ORDER BY q.sf_section_id, q.ordering, q.id ";
		$database->setQuery($query);
		$questions_data = $database->loadObjectList();
		$message = '';
		$i = 0;
		$questions_data[$i]->answer = '';
		if (is_array($questions_data) && count($questions_data) > 0)
			while ($i < count($questions_data)) {
				$questions_data[$i]->sf_qtext = trim(strip_tags(@$questions_data[$i]->sf_qtext));
				if (@$questions_data[$i]->sf_impscale) {
					$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '" . $questions_data[$i]->sf_impscale . "'";
					$database->setQuery($query);
					$questions_data[$i]->iscale_name = $database->loadResult();

					$query = "SELECT iscalefield_id FROM #__survey_force_user_answers_imp"
						. "\n WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "'"
						. "\n AND iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
						. "\n and start_id = '" . $id . "'";
					$database->setQuery($query);
					$ans_inf = $database->LoadResult();

					$questions_data[$i]->answer_imp = array();
					$query = "SELECT * FROM #__survey_force_iscales_fields WHERE iscale_id = '" . $questions_data[$i]->sf_impscale . "'"
						. "\n ORDER BY ordering";
					$database->setQuery($query);
					$tmp_data = $database->loadObjectList();
					$j = 0;
					while ($j < count($tmp_data)) {
						$questions_data[$i]->answer_imp[$j]->num = $j;
						$questions_data[$i]->answer_imp[$j]->f_id = $tmp_data[$j]->id;
						$questions_data[$i]->answer_imp[$j]->f_text = $tmp_data[$j]->isf_name;
						$questions_data[$i]->answer_imp[$j]->alt_text = '';
						if ($ans_inf == $tmp_data[$j]->id) {
							$questions_data[$i]->answer_imp[$j]->alt_text = '1';
							$questions_data[$i]->answer_imp[$j]->alt_id = $ans_inf;
						}
						$j++;
					}
				}

				switch (@$questions_data[$i]->sf_qtype) {
					case 1:
						$questions_data[$i]->answer = array();
						$questions_data[$i]->scale = '';
						$query = "SELECT stext FROM #__survey_force_scales WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadAssocList();
						foreach($tmp_data as &$tmpd){
							$tmpd = $tmpd['stext'];
						}
						$questions_data[$i]->scale = implode(', ', $tmp_data);

						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();

						$query = "SELECT * FROM #__survey_force_user_answers WHERE quest_id = '" . $questions_data[$i]->id . "' and survey_id = '" . $questions_data[$i]->sf_survey . "' and start_id = '" . $id . "'";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$j = 0;
						while ($j < count($tmp_data)) {
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							foreach ($ans_inf_data as $ans_data) {
								if ($ans_data->answer == $tmp_data[$j]->id) {
									$query = "SELECT * FROM #__survey_force_scales WHERE id = '" . $ans_data->ans_field . "'"
										. "\n and quest_id = '" . $questions_data[$i]->id . "'"
										. "\n ORDER BY ordering";
									$database->setQuery($query);
									$alt_data = $database->loadObjectList();
									$questions_data[$i]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $alt_data[0]->stext);
									$questions_data[$i]->answer[$j]->alt_id = $ans_data->ans_field;
								}
							}
							$j++;
						}
						break;
					case 2:
						$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 2 ) WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id ";
						$database->setQuery($query);
						$ans_inf = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data)) {
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = '';
							if (count($ans_inf) > 0 && $ans_inf[0]->answer == $tmp_data[$j]->id) {
								$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_inf[0]->ans_txt != '' ? ' (' . $ans_inf[0]->ans_txt . ')' : '');
								$questions_data[$i]->answer[$j]->alt_text = '1';
								$questions_data[$i]->answer[$j]->alt_id = $ans_inf;
							}
							$j++;
						}
						break;
					case 3:
						$query = "SELECT a.answer, b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c ) LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.ans_field = b.id AND c.sf_qtype = 3 )	WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id ";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data)) {
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = '';
							foreach ($ans_inf_data as $ans_data) {
								if ($ans_data->answer == $tmp_data[$j]->id) {
									$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');
									$questions_data[$i]->answer[$j]->alt_text = '1';
									$questions_data[$i]->answer[$j]->alt_id = $ans_data->answer;
								}
							}
							$j++;
						}
						break;
					case 4:
						$n = substr_count($questions_data[$i]->sf_qtext, "{x}") + substr_count($questions_data[$i]->sf_qtext, "{y}");
						if ($n > 0) {
							$query = "SELECT b.ans_txt, a.ans_field FROM #__survey_force_user_answers as a LEFT JOIN #__survey_force_user_ans_txt as b ON a.answer = b.id	WHERE a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' ORDER BY a.ans_field ";
							$database->setQuery($query);
							$ans_inf_data = $database->loadObjectList();
							$questions_data[$i]->answer = $ans_inf_data;
							$questions_data[$i]->answer_count = $n;
						} else {
							$query = "SELECT b.ans_txt FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b WHERE a.quest_id = '" . $questions_data[$i]->id . "' and a.survey_id = '" . $questions_data[$i]->sf_survey . "' and a.start_id = '" . $id . "' and a.answer = b.id";
							$database->setQuery($query);
							$ans_inf_data = $database->LoadResult();
							$questions_data[$i]->answer = ($ans_inf_data == '') ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $ans_inf_data;
						}
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT a.* , b.ans_txt FROM ( #__survey_force_user_answers AS a, #__survey_force_quests AS c )
LEFT JOIN #__survey_force_user_ans_txt AS b ON ( a.next_quest_id = b.id AND c.sf_qtype = 9 ) WHERE c.published = 1 AND a.quest_id = '" . $questions_data[$i]->id . "' AND a.survey_id = '" . $questions_data[$i]->sf_survey . "' AND a.start_id = '" . $id . "' AND c.id = a.quest_id";
						$database->setQuery($query);
						$ans_inf_data = $database->loadObjectList();

						$questions_data[$i]->answer = array();
						$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '" . $questions_data[$i]->id . "'"
							. "\n and is_main = 1 ORDER BY ordering";
						$database->setQuery($query);
						$tmp_data = $database->loadObjectList();
						$j = 0;
						while ($j < count($tmp_data)) {
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->f_id = $tmp_data[$j]->id;
							$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext;
							$questions_data[$i]->answer[$j]->alt_text = ($questions_data[$i]->sf_qtype == 9 ? '' : JText::_('COM_SURVEYFORCE_NO_ANSWER'));
							foreach ($ans_inf_data as $ans_data) {
								if ($ans_data->answer == $tmp_data[$j]->id) {
									$questions_data[$i]->answer[$j]->f_text = $tmp_data[$j]->ftext . ($ans_data->ans_txt != '' ? ' (' . $ans_data->ans_txt . ')' : '');
									$query = "SELECT * FROM #__survey_force_fields WHERE id = '" . $ans_data->ans_field . "'"
										. "\n and quest_id = '" . $questions_data[$i]->id . "'"
										. "\n and is_main = 0 ORDER BY ordering";
									$database->setQuery($query);
									$alt_data = $database->loadObjectList();
									if (count($alt_data) > 0) {
										$questions_data[$i]->answer[$j]->alt_text = ($ans_data->ans_field == 0 ? ($questions_data[$i]->sf_qtype == 9 ? '' : JText::_('COM_SURVEYFORCE_NO_ANSWER')) : $alt_data[0]->ftext);
										$questions_data[$i]->answer[$j]->alt_id = $ans_data->ans_field;
									}
								}
							}
							$j++;
						}
						break;
					case 7:
					case 8:
						break;
					default:
						if (!$questions_data[$i]->answer)
							$questions_data[$i]->answer = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						break;
				}
				$i++;
			}

		$message .= "<hr/>";
		$message .= "<h3>" . JText::_('COM_SURVEYFORCE_SF_SURVEY_INFORMATION') . "</h3>";
		$message .= '<strong>'.JText::_("COM_SURVEYFORCE_NAME") . '</strong>: ' . $survey_data[0]->sf_name . "<br/>";
		$message .= '<strong>'.JText::_("COM_SURVEYFORCE_DESCRIPTION") . '</strong>: ' . strip_tags($survey_data[0]->sf_descr) . "<br/>";
		if($survey_data[0]->sf_date_started != '0000-00-00 00:00:00') {
            $message .= '<strong>' . JText::_("COM_SURVEYFORCE_START_AT") . '</strong>: ' . JHtml::_('date', $survey_data[0]->sf_date_started, 'Y-m-d H:i:s') . "<br/>";
        }
		$message .= '<strong>'.JText::_('COM_SURVEYFORCE_USER') . '</strong>: ';
		switch ($start_data[0]->usertype) {
			case '0': $message .= JText::_('COM_SURVEYFORCE_ANON');
				break;
			case '1': $message .= JText::_('COM_SURVEYFORCE_REG_USER') . $start_data[0]->reg_username . ", " . $start_data[0]->reg_name . " (" . $start_data[0]->reg_email . ")";
				break;
			case '2': $message .= JText::_('COM_SURVEYFORCE_INV_USER') . $start_data[0]->inv_name . " " . $start_data[0]->inv_lastname . " (" . $start_data[0]->inv_email . ")";
				break;
		}
		$message .= "<hr/>";
		foreach ($questions_data as $qrow) {
			$message .= strip_tags($qrow->sf_qtext) . "<br/><br/>";
			switch ($qrow->sf_qtype) {
				case 2:
				case 3:
					foreach ($qrow->answer as $arow) {
						$img_ans = $arow->alt_text ? ' - ' . JText::_('COM_SURVEYFORCE_USER_CHOICE') : '';
						$message .= "<p>" . $arow->f_text . " " . $img_ans . "</p>";
					}
					break;
				case 1: $message .= JText::_('COM_SURVEYFORCE_SCALE') . ": " . $qrow->scale . "";
				case 5:
				case 6:
				case 9:
					foreach ($qrow->answer as $arow) {
						$message .= "<p>" . $arow->f_text . " - " . $arow->alt_text . "</p>";
					}
					break;
				case 4:
					if (isset($qrow->answer_count)) {
						$tmp = JText::_('COM_SURVEYFORCE_FIRST_ANSWER');
						for ($ii = 1; $ii <= $qrow->answer_count; $ii++) {
							if ($ii == 2)
								$tmp = JText::_('COM_SURVEYFORCE_SECOND_ANSWER');
							elseif ($ii == 3)
								$tmp = JText::_('COM_SURVEYFORCE_THIRD_ANSWER');
							elseif ($ii > 3)
								$tmp = $ii . JText::_('COM_SURVEYFORCE_X_ANSWER');
							foreach ($qrow->answer as $answer) {
								if ($answer->ans_field == $ii) {
									$message .= "<p>" . $tmp . strip_tags(($answer->ans_txt == '' ? JText::_('COM_SURVEYFORCE_NO_ANSWER') : $answer->ans_txt)) . "</p>";
									$tmp = -1;
								}
							}
							if ($tmp != -1) {
								$message .= "<p>" . $tmp . " " . JText::_('COM_SURVEYFORCE_NO_ANSWER') . "</p>";
							}
						}
					} else {
						$message .= "<p>" . ($qrow->answer) . "</p>";
					}
					break;
				default:
					$message .= "<p>" . ($qrow->answer) . "</p>";
					break;
			}

			if ($qrow->sf_impscale) {
				$message .= "<br/>";
				$message .= strip_tags($qrow->iscale_name) . "<br/>";
				foreach ($qrow->answer_imp as $arow) {
					$img_ans = $arow->alt_text ? ' - ' . JText::_('COM_SURVEYFORCE_USER_CHOICE') : '';
					$message .= "<p>" . $arow->f_text . " " . $img_ans . "</p>";
				}
			}
			$message .= "<hr/>";
		}

		return $message;
	}

	public function get_graph_results($survey_id, $start_id) {

		$tag = JFactory::getLanguage()->getTag();
		$lang = JFactory::getLanguage();
		$lang->load(COMPONENT_OPTION, JPATH_SITE, $tag, true);

		$database = JFactory::getDbo();
		$ret_str = '';
		$ret_str .= "\t" . '<fpage_type>1</fpage_type>' . "\n";
		$query = "SELECT id FROM #__survey_force_quests WHERE published = 1 AND sf_survey = '" . $survey_id . "' ORDER BY sf_section_id, ordering, id ";
		$database->setQuery($query);
		$questions = $database->loadAssocList();

		require ( JPATH_ROOT . '/components/com_surveyforce/helpers/generate.php' );
		JLoader::register('mos_Survey_Force_Config', JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'surveyforce.class.php');

		$sf_config = new mos_Survey_Force_Config( );
		$prefix = $sf_config->get('sf_result_type') == 'Bar' ? 'b' : 'p';
		$gg = new sf_ImageGenerator(array($sf_config->get('sf_result_type')));
		$gg->colors['axisColor1'] = $sf_config->get($prefix . '_axis_color1');
		$gg->colors['axisColor2'] = $sf_config->get($prefix . '_axis_color2');
		$gg->colors['aquaColor1'] = $sf_config->get($prefix . '_aqua_color1');
		$gg->colors['aquaColor2'] = $sf_config->get($prefix . '_aqua_color2');
		$gg->colors['aquaColor3'] = $sf_config->get($prefix . '_aqua_color3');
		$gg->colors['aquaColor4'] = $sf_config->get($prefix . '_aqua_color4');
		$gg->colors['barColor1'] = $sf_config->get($prefix . '_bar_color1');
		$gg->colors['barColor2'] = $sf_config->get($prefix . '_bar_color2');
		$gg->colors['barColor3'] = $sf_config->get($prefix . '_bar_color3');
		$gg->colors['barColor4'] = $sf_config->get($prefix . '_bar_color4');
		$gg->width = $sf_config->get($prefix . '_width', 600);
		$gg->height = $sf_config->get($prefix . '_height', 250);
		$gg->clearOldImages(); //delete yesterday images
		$fpage_text = '<p align="left"><strong>' . JText::_('COM_SURVEYFORCE_SF_SURVEY_RESULTS') . '</strong></p><br/>';
		foreach ($questions as $question) {
			$img_src = $gg->getImage($survey_id, $question, $start_id);
			if (is_array($img_src)) {
				foreach ($img_src as $imgsrc) {
					$fpage_text .= $imgsrc;
				}
			} elseif ($img_src) {
				$fpage_text .= $img_src;
			}
		}
		if ($fpage_text == '<p align="left"><strong>' . JText::_('COM_SURVEYFORCE_RESULTS') . '</strong></p><br/>')
			$fpage_text .= 'No graphs available.';
		$ret_str .= "\t" . '<fpage_text><![CDATA[' . stripslashes($fpage_text) . '&nbsp;]]></fpage_text>' . "\n";
		return $ret_str;
	}

	public function succesfully() {
		?>
		<script type="text/javascript">
			parent.location.href = parent.location.href;
		</script>
	<?php

	}

}
