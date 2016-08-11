<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewAdvreport extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_REPORT_DETAILS'), 'dashboard');
		JToolBarHelper::custom('advreport.report', 'print.png', 'print_f2.png', 'Report', false);
	}

	protected function setDocument() {
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_REPORT_DETAILS'));
	}

    function display($tpl = null) {

		switch ( JFactory::getApplication()->input->get('task') )
		{
			case 'catid': $this->ajaxSurveysByCat( JFactory::getApplication()->input->get('id') ); break;
			case 'survid': $this->ajaxMQuestionsBySurvey( JFactory::getApplication()->input->get('id') ); break;
			case 'survid_c': $this->ajaxCQuestionsBySurvey( JFactory::getApplication()->input->get('id') ); break;
			case 'get_cross_rep': $this->get_cross_rep(); break;
			case 'view_irep_surv': $this->view_irep_surv(); break;

		}

        $submenu = 'advreport';
        SurveyforceHelper::showTitle($submenu);
	    SurveyforceHelper::addReportsSubmenu($submenu);
	    $this->sidebar = JHtmlSidebar::render();

        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $this->addToolbar();

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

			$this->setModel( JModelLegacy::getInstance('question', 'SurveyforceModel') );
			$model = $this->getModel('question');
			$this->surveys = $model->getSurveysList();
			$this->surveys_csv = $this->surveys;

	    if (count($this->surveys))
	    {
			$this->mquest_id = $this->getMQuestionsBySurvey($this->surveys[0]->value);
			$this->cquest_id = $this->getCQuestionsBySurvey($this->surveys[0]->value);
	    }

        $this->categories = $this->getCategoriesList();
        $this->state = $this->get('State');

        parent::display($tpl);
    }

	protected function getCategoriesList()
	{
		$database = JFactory::getDbo();
		$query = "SELECT id AS value, sf_catname AS text"
			. "\n FROM #__survey_force_cats"
			. "\n ORDER BY sf_catname"
		;
		$database->setQuery( $query );
		$categories = $database->LoadObjectList();
		array_unshift($categories, (object)(array('value'=>0, 'text'=> JText::_('COM_SURVEYFORCE_SELECT_CATEGORY'))) );

		return $categories;
	}

	protected function getMQuestionsBySurvey( $survid )
	{
		$database = JFactory::getDbo();
		$query = "SELECT id AS value, SUBSTRING(sf_qtext,1,100) AS text, sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_qtype NOT IN (4, 7, 8) AND sf_survey = $survid ORDER BY ordering, id";
		$database->SetQuery( $query );
		$questions_tmp = $database->loadObjectList();
		$questions = array();
		if (count($questions_tmp)>0) {
			foreach($questions_tmp as $question) {
				if ($question->sf_qtype != 2 && $question->sf_qtype != 3) {
					$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$question->value} AND is_main = 1 ORDER BY ordering";
					$database->SetQuery( $query );
					$fields_tmp = $database->loadObjectList();
					foreach($fields_tmp as $field) {
						$tmp = new stdClass;
						$tmp->value = $question->value.'_'.$field->id;
						$tmp->text = trim( strip_tags($question->text.'  - '.$field->ftext) );
						$questions[] = $tmp;
					}
				}
				else {
					$tmp = new stdClass;
					$tmp->value = $question->value;
					$tmp->text = trim( strip_tags($question->text) );
					$questions[] = $tmp;
				}
			}

			return $questions;
		}
		else
			return '';
	}

	protected function getCQuestionsBySurvey( $survid )
	{
		$database = JFactory::getDbo();
		$query = "SELECT id AS value, SUBSTRING(sf_qtext,1,100) AS text, sf_qtype FROM #__survey_force_quests WHERE published = 1 AND sf_qtype NOT IN (7, 8) AND sf_survey = $survid ORDER BY ordering";
		$database->SetQuery( $query );
		$questions_tmp = $database->loadObjectList();
		$questions = array();
		if (count($questions_tmp)>0) {
			foreach($questions_tmp as $question) {
				if ($question->sf_qtype != 2 && $question->sf_qtype != 3 && $question->sf_qtype != 4) {
					$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$question->value} AND is_main = 1 ORDER BY ordering";
					$database->SetQuery( $query );
					$fields_tmp = $database->loadObjectList();
					foreach($fields_tmp as $field) {
						$tmp = new stdClass;
						$tmp->value = $question->value.'_'.$field->id;
						$tmp->text = $question->text.'  - '.$field->ftext;
						$questions[] = $tmp;
					}
				}
				else {
					$tmp = new stdClass;
					$tmp->value = $question->value;
					$tmp->text = $question->text;
					$questions[] = $tmp;
				}
			}
		}

		array_unshift($questions, (object)(array('value'=>0, 'text'=> JText::_('COM_SURVEYFORCE_ALL_QUESTIONS'))) );

		return $questions;
	}

	protected function ajaxSurveysByCat( $cat_id )
	{
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$this->setModel( JModelLegacy::getInstance('question', 'SurveyforceModel') );
		$model = $this->getModel('question');
		$this->surveys_csv = $model->getSurveysList($cat_id);

		$data = array();
		foreach ($this->surveys_csv as $surv)
			$data[$surv->value] = $surv->text;

		die(json_encode($data));
	}

	protected function ajaxMQuestionsBySurvey( $survid )
	{
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$this->mquest_id = $this->getMQuestionsBySurvey($survid);

		$data = array();
		foreach ($this->mquest_id as $quest)
			$data[$quest->value] = $quest->text;

		die(json_encode($data));
	}

	protected function ajaxCQuestionsBySurvey( $survid )
	{
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$this->cquest_id = $this->getCQuestionsBySurvey($survid);

		$data = array();
		foreach ($this->cquest_id as $quest)
			$data[$quest->value] = $quest->text;

		die(json_encode($data));
	}

	protected function get_cross_rep()
	{
		$database = JFactory::getDbo();

		$survid = intval( JFactory::getApplication()->input->get('survid', 0) );
		$mquest_id = JFactory::getApplication()->input->get('mquest_id', 0);
		$cquest_id = JFactory::getApplication()->input->get('cquest_id', array(), 'ARRAY');
		$start_date = JFactory::getApplication()->input->get('start_date', 0);
		$end_date = JFactory::getApplication()->input->get('end_date', 0);

		$is_complete = intval( JFactory::getApplication()->input->get('is_complete', 0) );
		$is_notcomplete = intval( JFactory::getApplication()->input->get('is_notcomplete', 0) );
		$type = strval( JFactory::getApplication()->input->get('rep_type', 'csv') );

			$date_where = '';
			if ($start_date != '' && $end_date != '') {
				$date_where = " AND sf_time BETWEEN '$start_date' AND '$end_date' ";
			}
			elseif ($start_date != '' && $end_date == '') {
				$date_where = " AND sf_time > '$start_date' ";
			}
			elseif ($start_date == '' && $end_date != '') {
				$date_where = " AND sf_time < '$end_date' ";
			}
			$query = "SELECT id FROM `#__survey_force_user_starts` "
				."WHERE survey_id = $survid "
				.($is_complete? ($is_notcomplete? '': " AND is_complete = 1 ") : ($is_notcomplete? " AND is_complete = 0 ": ''))
				.$date_where;
			$database->SetQuery( $query );
			$start_ids = $database->loadColumn();

			$m_id = intval($mquest_id);
			$f_id = 0;
			if (strpos($mquest_id, '_') > 0) {
				$f_id = intval( mb_substr($mquest_id, strpos($mquest_id, '_') + 1) );
			}
			$query = "SELECT sf_qtype FROM #__survey_force_quests  WHERE published = 1 AND id = $m_id";
			$database->SetQuery( $query );
			$qtype = $database->loadResult();

			if ($qtype == 1) {

				if ($f_id > 0) {
					$query = "SELECT stext FROM #__survey_force_scales  WHERE id = $f_id ORDER BY ordering";
					$database->SetQuery( $query );
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = $m_id ORDER BY ordering";

			}
			elseif ($qtype == 2 || $qtype == 3){

				if ($f_id > 0) {
					$query = "SELECT ftext FROM #__survey_force_fields  WHERE id = $f_id";
					$database->SetQuery( $query );
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = $m_id  ORDER BY ordering";
			}
			elseif ($qtype == 5 || $qtype == 6 || $qtype == 9){

				if ($f_id > 0) {
					$query = "SELECT ftext FROM #__survey_force_fields  WHERE id = $f_id";
					$database->SetQuery( $query );
					$f_text = $database->loadResult();
				}
				$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = $m_id AND is_main = 0 ORDER BY ordering";
			}
			$database->SetQuery( $query );
			$fields_ids = @array_merge($database->loadColumn(), array(0=>0));
			$starts_by_fields = array();
			foreach($fields_ids as $fields_id) {
				if(!empty($start_ids)){
					$query = "SELECT start_id FROM #__survey_force_user_answers WHERE start_id IN (".implode(',', $start_ids).") "
					." AND quest_id = $m_id "
					.($qtype == 2 || $qtype == 3? " AND answer = $fields_id ": " AND answer = $f_id AND ans_field = $fields_id ");


					$database->SetQuery( $query );
					$starts_by_fields[$fields_id] = $database->loadColumn();
				} else {
					$starts_by_fields[$fields_id] = array();
				}

				if (count($starts_by_fields[$fields_id]) < 1)
					$starts_by_fields[$fields_id] = array(0);
			}

			$all_quests = false;
			if (in_array('0', $cquest_id)){
				$all_quests = true;
				$query = "SELECT id, sf_qtype, sf_qtext FROM #__survey_force_quests  WHERE published = 1 AND sf_survey = $survid AND sf_qtype NOT IN (7,8) ORDER BY ordering, id";
				$database->SetQuery( $query );
				$questions2 = $database->loadObjectList();
				$questions = array();
				foreach($questions2 as $key => $quest){
					$questions2[$key]->answer_count = 0;
					if ($quest->sf_qtype != 2 && $quest->sf_qtype != 3)
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} AND is_main = 1 ORDER BY ordering";
					else
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} ORDER BY ordering";
					$database->SetQuery( $query );
					$questions2[$key]->fields = @array_merge($database->loadColumn(), array(0 => 0));

					if ($quest->sf_qtype != 1 && $quest->sf_qtype != 4) {
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$quest->id} AND is_main = 0 ORDER BY ordering";
					}
					elseif ($quest->sf_qtype == 4) {
						$questions2[$key]->answer_count = mb_substr_count($quest->sf_qtext, '{x}')+mb_substr_count($quest->sf_qtext, '{y}');
						$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
						if ($questions2[$key]->answer_count > 0) {
							$n = $questions2[$key]->answer_count;
							$questions2[$key]->fields = array();
							$a_fields = array();
							for($i = 1; $i <= $n; $i++){
								if(!empty($start_ids)){
									$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE survey_id = {$survid} AND quest_id = {$quest->id} AND ans_field = {$i} AND start_id IN (".implode(',', $start_ids).")";
									$database->SetQuery( $query );
									$ans_ids = $database->loadColumn();
								} else {
									$ans_ids = array();
								}

								if (!is_array($ans_ids))
									$ans_ids = array();

								if ( !empty($ans_ids) )
								{
									$query = "SELECT `ans_txt`, count( * ) num "
										." FROM `#__survey_force_user_ans_txt` "
										." WHERE id IN (".implode(',',$ans_ids).") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
									$database->SetQuery( $query );
									$ans_txts = $database->loadObjectList();
									foreach($ans_txts as $ans_txt) {
										$questions2[$key]->fields = @array_merge($questions2[$key]->fields, array(0 => $ans_txt->ans_txt));
									}
								}

								$a_fields[] = $i;
							}

							$questions2[$key]->a_fields = $a_fields;
						}
						else {
							if(!empty($start_ids)){
								$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE start_id IN (".implode(',', $start_ids).") AND survey_id = {$survid} AND quest_id = {$quest->id} ";
								$database->SetQuery( $query );
								$ans_ids = $database->loadColumn();
							} else {
								$ans_ids = array();
							}

							if (!is_array($ans_ids))
								$ans_ids = array();

							if ( !empty($ans_ids) )
							$query = "SELECT `ans_txt`, count( * ) num FROM `#__survey_force_user_ans_txt` WHERE id IN (".implode(',',$ans_ids).") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
						}
					}
					else{
						$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = {$quest->id} ORDER BY ordering";
					}
					$database->SetQuery( $query );
					if ($quest->sf_qtype == 4 && $questions2[$key]->answer_count < 1) {
						$ans_txts = $database->loadObjectList();
						$questions2[$key]->fields = array();
						foreach($ans_txts as $ans_txt) {
							$questions2[$key]->fields = @array_merge($questions2[$key]->fields, array(0 => $ans_txt->ans_txt));
						}

						$questions2[$key]->a_fields = null;
						$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
					}
					elseif ($quest->sf_qtype != 4)
						$questions2[$key]->a_fields = @array_merge($database->loadColumn(), array(0 => 0));

					$questions[$quest->id] = $quest;
					$questions[$quest->id]->a_fields = $questions2[$key]->a_fields;
					$questions[$quest->id]->fields = $questions2[$key]->fields;
					$questions[$quest->id]->answer_count = $questions2[$key]->answer_count;
				}
			}
			else {
				$questions = array();
				foreach($cquest_id as $quest) {
					$tmp = new stdClass;
					$tmp->answer_count = 0;
					$tmp->id = intval($quest);
					$query = "SELECT sf_qtype, sf_qtext FROM #__survey_force_quests  WHERE published = 1 AND id = {$tmp->id}";
					$database->SetQuery( $query );
					$n = null;
					$n = $database->loadObject();
					$tmp->sf_qtype = $n->sf_qtype;
					$tmp->sf_qtext = $n->sf_qtext;
					if ($tmp->sf_qtype != 1 && $tmp->sf_qtype != 4) {
						$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$tmp->id} AND is_main = 0 ORDER BY ordering";
					}
					elseif ($tmp->sf_qtype == 4) {
						$tmp->answer_count = mb_substr_count($tmp->sf_qtext, '{x}')+mb_substr_count($tmp->sf_qtext, '{y}');
						if ($tmp->answer_count > 0) {
							$n = $tmp->answer_count;
							$tmp->fields = array();
							$a_fields = array();
							for($i = 1; $i <= $n; $i++){
								if(!empty($start_ids)){
									$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE survey_id = {$survid} AND quest_id = {$tmp->id} AND ans_field = {$i} AND start_id IN (".implode(',', $start_ids).")";
									$database->SetQuery( $query );
									$ans_ids = $database->loadColumn();
								} else {
									$ans_ids = array();
								}

								if (!is_array($ans_ids))
									$ans_ids = array();

								if ( !empty($ans_ids) )
								$query = "SELECT `ans_txt`, count( * ) num "
									." FROM `#__survey_force_user_ans_txt` "
									." WHERE id IN (".implode(',',$ans_ids).") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
								$database->SetQuery( $query );
								$ans_txts = $database->loadObjectList();
								foreach($ans_txts as $ans_txt) {
									$tmp->fields = @array_merge($tmp->fields, array(0 => $ans_txt->ans_txt));
								}

								$a_fields[] = $i;
							}
							$tmp->a_fields = $a_fields;
						}
						else {
							if(!empty($start_ids)){
								$query = "SELECT `answer` FROM `#__survey_force_user_answers` WHERE quest_id = {$tmp->id} AND start_id IN (".implode(',', $start_ids).")";
								$database->SetQuery( $query );
								$ans_ids = $database->loadColumn();
							} else {
								$ans_ids = array();
							}

							if (!is_array($ans_ids))
								$ans_ids = array();

							if ( !empty($ans_ids) )
							$query = "SELECT `ans_txt`, count( * ) num FROM `#__survey_force_user_ans_txt` WHERE id IN (".implode(',',$ans_ids).") GROUP BY `ans_txt` ORDER BY num DESC LIMIT 0 , 10";
						}
					}
					else {
						$query = "SELECT id FROM #__survey_force_scales WHERE quest_id = {$tmp->id} ORDER BY ordering";
					}
					$database->SetQuery( $query );
					if ($tmp->sf_qtype == 4 && $tmp->answer_count < 1) {
						$ans_txts = $database->loadObjectList();
						$tmp->fields = array();
						foreach($ans_txts as $ans_txt) {
							$tmp->fields = @array_merge($tmp->fields, array(0 => $ans_txt->ans_txt));
						}

						$tmp->a_fields = null;
					}
					elseif ($tmp->sf_qtype != 4) {
						$tmp->a_fields = @array_merge($database->loadColumn(), array(0 => 0));
						if (strpos($quest, '_') > 0) {
							$tmp->fields = array(0 => 0, 1 => intval(mb_substr($quest, strpos($quest, '_') + 1)) );
						}
						else {
							$query = "SELECT id FROM #__survey_force_fields WHERE quest_id = {$tmp->id} AND is_main = 1";
							$database->SetQuery( $query );
							$tmp->fields = @array_merge($database->loadColumn(), array(0 => 0));
						}
						foreach($questions as $key => $question){
							if ($question->id == $tmp->id) {
								$questions[$key]->fields = @array_merge($tmp->fields, $questions[$key]->fields);
								$tmp = null;
								break;
							}
						}
					}

					if ($tmp != null)
						$questions[$tmp->id] = $tmp;
				}
			}
			$result_data = array();
			foreach($questions as $question) {
				$tmp = array();
				foreach($fields_ids as $fields_id) {
					if ( $question->sf_qtype == 2 || $question->sf_qtype == 3 ) {
						$query = "SELECT answer FROM #__survey_force_user_answers "
							." WHERE start_id IN (".implode(',', $starts_by_fields[$fields_id]).") "
							." AND quest_id = {$question->id} "
							." AND answer IN (".implode(',', $question->fields).") "
						;
					}
					elseif ($question->sf_qtype == 4) {
						if ($question->answer_count > 0) {
							$query = "SELECT a.ans_txt, b.ans_field FROM #__survey_force_user_ans_txt AS a LEFT JOIN #__survey_force_user_answers AS b ON b.answer = a.id AND b.quest_id = {$question->id}"
								." WHERE a.start_id IN (".implode(',', $starts_by_fields[$fields_id]).") "
								." AND a.ans_txt IN ('".implode("', '", $question->fields)."') ORDER BY b.ans_field";
						}
						else {
							$query = "SELECT ans_txt FROM #__survey_force_user_ans_txt "
								." WHERE start_id IN (".implode(',', $starts_by_fields[$fields_id]).") "
								." AND ans_txt IN ('".implode("', '", array_map('mysql_escape_string', $question->fields))."') ";
						}
					}
					else {
						$query = "SELECT answer, ans_field FROM #__survey_force_user_answers "
							." WHERE start_id IN (".implode(',', $starts_by_fields[$fields_id]).") "
							." AND quest_id = {$question->id} "
							.( $question->sf_qtype == 1?" AND ans_field IN (".implode(',', $question->a_fields).") ":" AND answer IN (".implode(',', $question->fields).") ")
						;
					}
					$database->SetQuery( $query );
					if ( $question->sf_qtype == 2 || $question->sf_qtype == 3 ) {
						$t = array_count_values($database->loadColumn());
						$tmp[$fields_id] = array();
						foreach($question->fields as $f_id ){
							$tmp[$fields_id][$f_id] = isset($t[$f_id])? $t[$f_id]: 0;
						}
					}
					elseif ($question->sf_qtype == 4) {
						if ($question->answer_count > 0) {
							$tmp_data = $database->loadObjectList();
							$t_fields = array();
							foreach($tmp_data as $data){
								if (!isset($t_fields[$data->ans_field]))
									$t_fields[$data->ans_field] = array();
								$t_fields[$data->ans_field][] = $data->ans_txt;
							}

							foreach($t_fields as $key => $data){
								$t_fields[$key] = array_count_values($data);
							}

							$tmp[$fields_id] = $t_fields;
						}
						else {
							$t = array_count_values($database->loadColumn());
							$tmp[$fields_id] = array();
							foreach($question->fields as $f_id ){
								$tmp[$fields_id][$f_id] = isset($t[$f_id])? $t[$f_id]: 0;
							}
						}
					}
					else {
						$tmp_data = $database->loadObjectList();
						$t_fields = array();
						foreach($tmp_data as $data){
							if (!isset($t_fields[$data->answer]))
								$t_fields[$data->answer] = array();
							$t_fields[$data->answer][] = $data->ans_field;
						}

						foreach($t_fields as $key => $data){
							$t_fields[$key] = array_count_values($data);
						}

						foreach($t_fields as $key => $data){
							foreach($question->a_fields as $af_id){
								$t_fields[$key][$af_id] = isset($t_fields[$key][$af_id])? $t_fields[$key][$af_id]: 0;
							}
						}

						$tmp[$fields_id] = $t_fields;
					}
				}
				if ( $question->sf_qtype == 2 || $question->sf_qtype == 3 || $question->sf_qtype == 4 ) {
					if ($question->sf_qtype == 4 && $question->answer_count > 0) {
						$t = array();
						foreach($fields_ids as $fields_id2) {
							foreach($tmp[$fields_id2] as $f_id=>$fields){
								foreach($fields as $af_id=>$count){
									foreach($fields_ids as $fields_id) {
										$t[$f_id][$af_id][$fields_id] = isset($tmp[$fields_id][$f_id][$af_id])?$tmp[$fields_id][$f_id][$af_id]:'0';
									}
								}
							}
						}
					}
					else {
						$t = array();
						foreach($question->fields as $f_id ){
							$t[$f_id] = array();
							foreach($fields_ids as $fields_id) {
								$t[$f_id][$fields_id] = $tmp[$fields_id][$f_id];
							}
						}
					}
				}
				else {
					$t = array();
					foreach($question->fields as $f_id){
						foreach($question->a_fields as $af_id){
							foreach($fields_ids as $fields_id) {
								$t[$f_id][$af_id][$fields_id] = isset($tmp[$fields_id][$f_id][$af_id])?$tmp[$fields_id][$f_id][$af_id]:'0';
							}
						}
					}
				}

				$result_data[$question->id] = $t;
			}

			if ($type == 'pdf') {
				chdir(JPATH_BASE );
				/*
				 * Create the pdf document
				 */

				@ini_set('memory_limit', '512M');
				require_once(JPATH_COMPONENT_ADMINISTRATOR . '/assets/tcpdf/sf_pdf.php');

				$pdf_doc = new sf_pdf();

				$pdf = &$pdf_doc->_engine;

				$pdf->getAliasNbPages();
				$pdf->AddPage();

				$pdf->SetFont('freesans');
				$fontFamily = $pdf->getFontFamily();

				$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$m_id}";
				$database->SetQuery( $query );
				$main_quest = $pdf_doc->cleanText($database->loadResult().(isset($f_text)?" - $f_text\n":"\n"));
				$start_key = 'dummy';
				reset ($result_data);

				for($ij = 0, $nm = count($result_data); $ij < $nm; $ij++ ) {
					if ($start_key == 'dummy')
						list($key, $data) = each($result_data);
					$cur_y = $pdf->GetY();


					if ($cur_y > 240)
						$pdf->AddPage();

					$pdf->SetX(60);
					$pdf->SetFontSize(8);
					$pdf->setFont($fontFamily, 'B');
					$pdf->setFont($fontFamily, 'I');
					$pdf->MultiCell(0, 0, $main_quest, 0, 'J', 0, 1, 0 ,0, true, 0);
					$pdf->Ln(0.5);

					$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$key}";
					$database->SetQuery( $query );

					$quest = $pdf_doc->cleanText($database->loadResult())."\n";
					$pdf->setFont($fontFamily, 'I');
					$pdf->MultiCell(60, 0, $quest , 0, 'J', 0, 1, 0 ,0, true, 0);
					$pdf->Ln(0.5);

					$cur_y = $pdf->GetY();
					$col_width = 130/(count($fields_ids )+1);
					$pdf->SetFontSize(6);

					$pdf->SetX(60);
					$pdf->MultiCell($col_width, 0, "Total" , 0, 'C', 0, 1, 0 ,0, true, 0);

					$i = 1;
					$line_y = 10000;
					foreach($fields_ids as $fields_id) {
						$pdf->setFontSize(4);
						$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$fields_id}";
						if ($qtype == 1) {
							$query = "SELECT stext FROM #__survey_force_scales WHERE id = {$fields_id} ORDER BY ordering";
						}
						$database->SetQuery( $query );
						$tt = $pdf_doc->cleanText($database->loadResult());
						if ($fields_id == 0)
							$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						$pdf->SetY($cur_y);
						$pdf->SetX(60+($col_width)*$i);
						$pdf->MultiCell($col_width, 50, $tt , 0, 'C', 0, 1, 0 ,0, true, 0);										
						if($maxlen<strlen($tt))
							$maxlen = strlen($tt);
						$i++;
					}	
					
					$cur_y = $pdf->GetY()+10	;
                    $pdf->SetY($cur_y);

					$pdf->setFontSize(6);
					$pdf->line( 60, $pdf->GetY()+2, 200, $pdf->GetY()+2);
					$pdf->Ln();
					$pdf->setFont($fontFamily, 'B');
					if ( $questions[$key]->sf_qtype == 2 || $questions[$key]->sf_qtype == 3 ) {
						$total_row = array('total'=>0);
						$cur_y2 = $pdf->GetY();
						foreach($data as $k => $item) {
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->SetQuery( $query );
							$tt = $pdf_doc->cleanText($database->loadResult());
							if ($k == 0)
								$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							$total_col = 0;

							$pdf->SetY($cur_y2);
							$cur_y = $pdf->GetY();
							$pdf->SetY($cur_y);
							$pdf->SetX(17);
							$pdf->MultiCell(40, 0, $tt."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);
							$pdf->Ln(0.5);
							$cur_y2 = $pdf->GetY();

							$pdf->SetY($cur_y);
							$i = 1;
							foreach($fields_ids as $fields_id) {

								if($cur_y > $pdf->getPageHeight()-20){
                                    $cur_y = 15;
                                }

								$pdf->SetY($cur_y);
								$pdf->SetX(60+$col_width*$i);
								$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);

								$total_col = $total_col + $item[$fields_id];
								if (!isset($total_row[$fields_id]))
									$total_row[$fields_id] = 0;
								$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
								$i++;
							}
							$total_row['total'] = $total_row['total'] + $total_col;
							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0 ,0, true, 0);
						}
						$pdf->line( 60, $pdf->GetY()+2, 200, $pdf->GetY()+2);
						$pdf->Ln();
						$cur_y = $pdf->GetY();
						$pdf->SetX(30);
						$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0 ,0, true, 0);

						$pdf->SetY($cur_y);
						$pdf->SetX(60);
						$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0 ,0, true, 0);
						$i = 1;
						foreach($fields_ids as $fields_id) {
							$pdf->SetY($cur_y);
							$pdf->SetX(60+$col_width*$i);
							$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);
							$i++;
						}
					}
					elseif ($questions[$key]->sf_qtype == 4) {
						if ($questions[$key]->answer_count > 0 ) {
							foreach($data as $nn => $itemz) {
								$tmp = '';
								if ($nn == 1) $tmp = JText::_('COM_SF_1ST_ANSWER');
								if ($nn == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								if ($nn == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
								if ($nn > 3) $tmp = $nn.JText::_('COM_SF_TH_ANSWER');

								$pdf_doc->cleanText($tmp);
								$pdf->SetX(18);
								$pdf->MultiCell(42, 0, $tmp."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);

								$total_row = array('total'=>0);
								$cur_y2 = $pdf->GetY();
								foreach($itemz as $k=>$item) {
									$tt = $pdf_doc->cleanText($k);

									if ($k === 0)
										$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
									$total_col = 0;

									if ($cur_y2 > 240) {
										$pdf->AddPage();
										$cur_y2 = $pdf->GetY();
									}

									$pdf->SetY($cur_y2);
									$cur_y = $pdf->GetY();
									$pdf->SetY($cur_y);
									$pdf->SetX(17);
									$pdf->MultiCell(40, 0, $tt."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);
									$pdf->Ln(0.5);
									$cur_y2 = $pdf->GetY();

									$i = 1;
									foreach($fields_ids as $fields_id) {
										$pdf->SetY($cur_y);
										$pdf->SetX(60+$col_width*$i);
										$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);
										$total_col = $total_col + $item[$fields_id];
										if (!isset($total_row[$fields_id]))
											$total_row[$fields_id] = 0;
										$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
										$i++;
									}
									$total_row['total'] = $total_row['total'] + $total_col;
									$pdf->SetY($cur_y);
									$pdf->SetX(60);
									$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0 ,0, true, 0);
								}
								$pdf->line( 60, $pdf->GetY()+2, 200, $pdf->GetY()+2);
								$pdf->Ln();
								$cur_y = $pdf->GetY();
								$pdf->SetX(30);
								$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0 ,0, true, 0);

								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0 ,0, true, 0);

								$i = 1;
								foreach($fields_ids as $fields_id) {
									$pdf->SetY($cur_y);
									$pdf->SetX(60+$col_width*$i);
									$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);
									$i++;
								}
							}
						}
						else {
							$total_row = array('total'=>0);
							$cur_y2 = $pdf->GetY();
							foreach($data as $k => $item) {
								$tt = $pdf_doc->cleanText($k);

								if ($k === 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$total_col = 0;

								if ($cur_y2 > 240) {
									$pdf->AddPage();
									$cur_y2 = $pdf->GetY();
								}

								$pdf->SetY($cur_y2);
								$cur_y = $pdf->GetY();
								$pdf->SetX(17);
								$pdf->MultiCell(40, 0, $tt."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);
								$pdf->Ln(0.5);
								$cur_y2 = $pdf->GetY();

								$i = 1;
								foreach($fields_ids as $fields_id) {
									$pdf->SetY($cur_y);
									$pdf->SetX(60+$col_width*$i);
									$pdf->MultiCell($col_width, 0, "{$item[$fields_id]}", 0, 'C', 0, 1, 0 ,0, true, 0);

									$total_col = $total_col + $item[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
									$i++;
								}
								$total_row['total'] = $total_row['total'] + $total_col;
								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0 ,0, true, 0);

							}
							$pdf->SetY($cur_y2);
							$pdf->line( 60, $pdf->GetY()+2, 200, $pdf->GetY()+2);
							$pdf->Ln();
							$cur_y = $pdf->GetY();

							$pdf->SetX(30);
							$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0 ,0, true, 0);

							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0 ,0, true, 0);

							$i = 1;
							foreach($fields_ids as $fields_id) {
								$pdf->SetY($cur_y);
								$pdf->SetX(60+$col_width*$i);
								$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);
								$i++;
							}
						}
					}
					else {
						foreach($data as $k => $item) {
							$total_row = array('total'=>0);
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->SetQuery( $query );
							$tt = $database->loadResult();
							if ($k == 0)
								continue;

							if ($pdf->GetY() > 240) {
								$pdf->AddPage();
							}

							$tt = $pdf_doc->cleanText($tt);
							$pdf->SetX(17);
							$pdf->MultiCell(42, 0, $tt."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);
							$cur_y2 = $pdf->GetY();

							foreach($item as $kk => $it) {
								$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$kk}";
								if ($questions[$key]->sf_qtype == 1) {
									$query = "SELECT stext  FROM #__survey_force_scales WHERE id = {$kk} ORDER BY ordering";
								}
								$database->SetQuery( $query );
								$tt = $pdf_doc->cleanText($database->loadResult());
								if ($kk == 0)
									$tt = ($questions[$key]->sf_qtype == 9? JText::_('COM_SF_NO_RANK') :JText::_('COM_SURVEYFORCE_NO_ANSWER'));

								if ($cur_y2 > 240) {
									$pdf->AddPage();
									$cur_y2 = $pdf->GetY();
								}

								$pdf->SetY($cur_y2);
								$cur_y = $pdf->GetY();
								$pdf->SetY($cur_y);
								$pdf->SetX(20);
								$pdf->MultiCell(40, 0, $tt."\n" , 0, 'J', 0, 1, 0 ,0, true, 0);
								$pdf->Ln(0.5);
								$cur_y2 = $pdf->GetY();

								$total_col = 0;
								$i=1;
								foreach($fields_ids as $fields_id) {
									$pdf->SetY($cur_y);
									$pdf->SetX(60+$col_width*$i);
									$pdf->MultiCell($col_width, 0, "{$it[$fields_id]}" , 0, 'C', 0, 1, 0 ,0, true, 0);

									$total_col = $total_col + $it[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $it[$fields_id];
									$i++;
								}
								$total_row['total'] = $total_row['total'] + $total_col;
								$pdf->SetY($cur_y);
								$pdf->SetX(60);
								$pdf->MultiCell($col_width, 0, "{$total_col}", 0, 'C', 0, 1, 0 ,0, true, 0);
							}
							$pdf->line( 60, $pdf->GetY()+2, 200, $pdf->GetY()+2);
							$pdf->Ln();
							$cur_y = $pdf->GetY();

							$pdf->SetX(30);
							$pdf->MultiCell(20, 0, "Totals", 0, 'R', 0, 1, 0 ,0, true, 0);

							$pdf->SetY($cur_y);
							$pdf->SetX(60);
							$pdf->MultiCell($col_width, 0, "{$total_row['total']}", 0, 'C', 0, 1, 0 ,0, true, 0);

							$i = 1;
							foreach($fields_ids as $fields_id) {
								$pdf->SetY($cur_y);
								$pdf->SetX(60+$col_width*$i);
								$pdf->MultiCell($col_width, 0, "{$total_row[$fields_id]}", 0, 'C', 0, 1, 0 ,0, true, 0);
								$i++;
							}
						}
					}
					$pdf->line( 15, $pdf->GetY()+2, 200, $pdf->GetY()+2);
					$pdf->Ln();$pdf->Ln();
				}

				$data = $pdf->Output('', 'S');
				@ob_end_clean();
				header("Content-type: application/pdf");
				header("Content-Length: ".strlen(ltrim($data)));
				header("Content-Disposition: attachment; filename=report.pdf");
				echo $data;
				exit;
			} else {
				$csv_data = "";
				$z = ',';
				$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$m_id}";
				$database->SetQuery( $query );
				$main_quest = $this->SF_processPDFField($database->loadResult()).(isset($f_text)?" - $f_text":'');
				foreach($result_data  as $key => $data) {
					$csv_data .= $z.$main_quest."\n";
					$query = "SELECT  sf_qtext   FROM #__survey_force_quests  WHERE published = 1 AND id = {$key}";
					$database->SetQuery( $query );
					$csv_data .= $this->SF_processPDFField($database->loadResult())."\n";
					$csv_data .="{$z}Total";
					foreach($fields_ids as $fields_id) {
						$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$fields_id}";
						if ($qtype == 1) {
							$query = "SELECT stext FROM #__survey_force_scales WHERE id = {$fields_id} ORDER BY ordering";
						}
						$database->SetQuery( $query );
						$tt = $this->SF_processPDFField($database->loadResult());
						if ($fields_id == 0)
							$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
						$csv_data .="{$z}{$tt}";
					}
					$csv_data .= "\n";
					if ( $questions[$key]->sf_qtype == 2 || $questions[$key]->sf_qtype == 3 ) {
						$total_row = array('s'=>0);
						foreach($data as $k => $item) {
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->SetQuery( $query );
							$tt = $this->SF_processPDFField($database->loadResult());
							if ($k == 0)
								$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
							$ech = '';
							$total_col = 0;

							foreach($fields_ids as $fields_id) {
								$ech .= "{$z}".$item[$fields_id];
								$total_col = $total_col + $item[$fields_id];
								if (!isset($total_row[$fields_id]))
									$total_row[$fields_id] = 0;
								$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
							}
							$total_row['s'] = $total_row['s'] + $total_col;
							$csv_data .= "$tt{$z}$total_col".$ech."\n";
						}
						$ech = '';
						foreach($fields_ids as $fields_id) {
							$ech .= "{$z}".$total_row[$fields_id];
						}
						$csv_data .= "Total{$z}{$total_row['s']}".$ech."\n";
					}
					elseif( $questions[$key]->sf_qtype == 4 ) {
						if ($questions[$key]->answer_count > 0 ) {
							foreach($data as $nn => $itemz) {
								$tmp = '';
								if ($nn == 1) $tmp = JText::_('COM_SF_1ST_ANSWER');
								if ($nn == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								if ($nn == 3) $tmp = JText::_('COM_SF_THIRD_ANSWER');
								if ($nn > 3) $tmp = $nn.JText::_('COM_SF_TH_ANSWER');
								$csv_data .= "$tmp\n";
								$total_row = array('s'=>0);
								foreach($itemz as $k=>$item) {
									$tt = $this->SF_processPDFField($k);
									if ($k === 0)
										$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
									$ech = '';
									$total_col = 0;

									foreach($fields_ids as $fields_id) {
										$ech .= "{$z}".$item[$fields_id];
										$total_col = $total_col + $item[$fields_id];
										if (!isset($total_row[$fields_id]))
											$total_row[$fields_id] = 0;
										$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
									}
									$total_row['s'] = $total_row['s'] + $total_col;
									$csv_data .= "$tt{$z}$total_col".$ech."\n";
								}
								$ech = '';
								foreach($fields_ids as $fields_id) {
									$ech .= "{$z}".$total_row[$fields_id];
								}
								$csv_data .= "Total{$z}{$total_row['s']}".$ech."\n";
							}
						}
						else {
							$total_row = array('s'=>0);
							foreach($data as $k => $item) {
								$tt = $this->SF_processPDFField($k);
								if ($k === 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$ech = '';
								$total_col = 0;

								foreach($fields_ids as $fields_id) {
									$ech .= "{$z}".$item[$fields_id];
									$total_col = $total_col + $item[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $item[$fields_id];
								}
								$total_row['s'] = $total_row['s'] + $total_col;
								$csv_data .= "$tt{$z}$total_col".$ech."\n";
							}
							$ech = '';
							foreach($fields_ids as $fields_id) {
								$ech .= "{$z}".$total_row[$fields_id];
							}
							$csv_data .= "Total{$z}{$total_row['s']}".$ech."\n";
						}
					}
					else {
						foreach($data as $k => $item) {
							$total_row = array('s'=>0);
							$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$k}";
							$database->SetQuery( $query );
							$tt = $this->SF_processPDFField($database->loadResult());
							if ($k == 0)
								continue;

							$csv_data .= "$tt\n";
							foreach($item as $kk => $it) {
								$query = "SELECT ftext FROM #__survey_force_fields WHERE id = {$kk}";
								if ($questions[$key]->sf_qtype == 1) {
									$query = "SELECT stext  FROM #__survey_force_scales WHERE id = {$kk} ORDER BY ordering";
								}
								$database->SetQuery( $query );
								$tt = $this->SF_processPDFField($database->loadResult());
								if ($kk == 0)
									$tt = JText::_('COM_SURVEYFORCE_NO_ANSWER');
								$ech = '';
								$total_col = 0;

								foreach($fields_ids as $fields_id) {
									$ech .= "{$z}".$it[$fields_id];
									$total_col = $total_col + $it[$fields_id];
									if (!isset($total_row[$fields_id]))
										$total_row[$fields_id] = 0;
									$total_row[$fields_id] = $total_row[$fields_id] + $it[$fields_id];
								}
								$total_row['s'] = $total_row['s'] + $total_col;
								$csv_data .= "$tt{$z}$total_col".$ech."\n";
							}
							$ech = '';
							foreach($fields_ids as $fields_id) {
								$ech .= "{$z}".$total_row[$fields_id];
							}
							$csv_data .= "Total{$z}{$total_row['s']}".$ech."\n";
						}
					}
					$csv_data .= "\n\n";
				}
				$filedata = $this->SF_processField($csv_data);
				@ob_end_clean();
				header("Content-type: application/csv");
				header("Content-Length: ".strlen(ltrim($filedata)));
				header("Content-Disposition: attachment; filename=report.csv");
				echo $filedata;
			}
			exit;
	}

	protected function SF_processPDFField($field_text, $allowed_tags = '') {
		$field_text = strip_tags($field_text, $allowed_tags );
		$field_text = $this->rel_pdfCleaner($field_text);
		$field_text = (get_magic_quotes_gpc()) ? mosStripslashes( $field_text ) : $field_text;
		$field_text = str_replace( '&quot;', '"', $field_text );
		$field_text = str_replace( '&#039;', "'", $field_text );
		$field_text = str_replace( '&#39;', "'", $field_text );
	return trim($field_text);
	}

	protected function SF_processField($field_text) {
		$field_text = (get_magic_quotes_gpc()) ? mosStripslashes( $field_text ) : $field_text;
		$field_text = JFilterOutput::ampReplace($field_text);
		$field_text = str_replace( '&quot;', '"', $field_text );
		$field_text = str_replace( '&#039;', "'", $field_text );
		$field_text = str_replace( '&#39;', "'", $field_text );
		return trim($field_text);
	}
	function get_html_translation_table_my() {
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
		$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
		$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
		$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
		$trans[chr(134)] = '&dagger;';    // Dagger
		$trans[chr(135)] = '&Dagger;';    // Double Dagger
		$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
		$trans[chr(137)] = '&permil;';    // Per Mille Sign
		$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
		$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
		$trans[chr(140)] = '&OElig;    ';    // Latin Capital Ligature OE
		$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
		$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
		$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
		$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
		$trans[chr(149)] = '&bull;';    // Bullet
		$trans[chr(150)] = '&ndash;';    // En Dash
		$trans[chr(151)] = '&mdash;';    // Em Dash
		$trans[chr(152)] = '&tilde;';    // Small Tilde
		$trans[chr(153)] = '&trade;';    // Trade Mark Sign
		$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
		$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
		$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
		$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
		ksort($trans);
		return $trans;
	}

	protected function rel_pdfCleaner( $text ) {

		// Ugly but needed to get rid of all the stuff the PDF class cant handle
		$text = str_replace( '<p>', 			"\n\n", 	$text );
		$text = str_replace( '<P>', 			"\n\n", 	$text );
		$text = str_replace( '<br />', 			"\n", 		$text );
		$text = str_replace( '<br>', 			"\n", 		$text );
		$text = str_replace( '<BR />', 			"\n", 		$text );
		$text = str_replace( '<BR>', 			"\n", 		$text );
		$text = str_replace( '<li>', 			"\n - ", 	$text );
		$text = str_replace( '<LI>', 			"\n - ", 	$text );
		$text = str_replace( '{mosimage}', 		'', 		$text );
		$text = str_replace( '{mospagebreak}', 	'',			$text );

		$text = strip_tags( $text );
		$text = strtr( $text, array_flip($this->get_html_translation_table_my( ) ) );
		$text = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $text );

		return $text;
	}


	protected function view_irep_surv()
	{
		@set_time_limit( 3600 );
		@ini_set('memory_limit', '2024M');
		global $database, $front_end;

		$max_quest_length = 150;
		$database = JFactory::getDbo();

		$show_iscale = intval(JFactory::getApplication()->input->get('inc_imp', 0));
		$add_info = intval(JFactory::getApplication()->input->get('add_info', 0));
		$id = intval(JFactory::getApplication()->input->get('survid_csv', 0));
		$query = "SELECT * FROM #__survey_force_survs WHERE id = '".$id."'";
		$database->SetQuery( $query );
		$survey_data = $database->LoadObject();
		
		if (isset($survey_data->id) && $survey_data->id) {
			$query = "SELECT sf_ust.*, sf_s.sf_name as survey_name, u.username reg_username, u.name reg_name, u.email reg_email,"
				. "\n sf_u.name as inv_name, sf_u.lastname as inv_lastname, sf_u.email as inv_email"
				. "\n FROM (#__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s)"
				. "\n LEFT JOIN #__users as u ON u.id = sf_ust.user_id and sf_ust.usertype=1"
				. "\n LEFT JOIN #__survey_force_users as sf_u ON sf_u.id = sf_ust.user_id and sf_ust.usertype=2"
				. "\n WHERE sf_ust.survey_id = sf_s.id"
				. "\n and sf_s.id = $id"
				. "\n ORDER BY sf_ust.sf_time DESC, sf_ust.id DESC";
			$database->SetQuery($query);
			$rows = $database->loadObjectList();



			$query = "SELECT a.*, b.iscale_name FROM #__survey_force_quests as a LEFT JOIN #__survey_force_iscales as b ON b.id=a.sf_impscale WHERE a.published = 1 AND a.sf_survey = $id AND a.sf_qtype IN (1,2,3,4,5,6,9) ORDER BY a.ordering, a.sf_qtext";
			$database->SetQuery($query);
			$sf_quests = $database->loadObjectList();

			$iii = 0;

			$t_fields = array();
			foreach($sf_quests as $key => $sfq) {
				switch ($sfq->sf_qtype) {
					case 1:
						$query = "SELECT id, ftext FROM #__survey_force_fields WHERE quest_id = {$sfq->id} AND is_main = 1 ORDER BY ordering";
						$database->SetQuery( $query );
						$t_fields[$sfq->id.'1'] = $database->loadObjectList();

						$tmp_str = '';
						if(count($t_fields[$sfq->id.'1']))
							foreach($t_fields[$sfq->id.'1'] as $field) {
								$tmp_str .= ','.str_replace(',','',$this->SF_processCSVField(str_replace("\r\n","",$sfq->sf_qtext.' - '.$field->ftext)));
							}
						$sf_quests[$key]->sf_qtext2 = $tmp_str;
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT id, ftext FROM #__survey_force_fields AS a WHERE quest_id = {$sfq->id} AND is_main = 1 ORDER BY ordering";
						$database->SetQuery( $query );
						$t_fields[$sfq->id.'569'] = $database->loadObjectList();
						break;
				}
			}

			@ob_end_clean();
			header("Content-type: application/csv");
			header("Content-Disposition: inline; filename=report.csv");

			if ($add_info) {
				echo '"","","","",';
			}

			echo '"",""';


			$nnn = count($rows);

			while ($iii < $nnn) {

				$rows[$iii]->questions = array();

				foreach($sf_quests as $key => $sfq) {
					$sf_quests[$key]->sf_qtext = trim(strip_tags($sf_quests[$key]->sf_qtext,'<a><b><i><u>'));
					$one_answer = new stdClass();
					$one_answer->quest_id = $sfq->id;
					$user_answer = '';
					if ($sfq->sf_impscale) {
						$query = "SELECT b.isf_name FROM #__survey_force_iscales_fields as b, #__survey_force_user_answers_imp as a"
							. "\n WHERE a.quest_id = '".$sfq->id."' AND a.survey_id = '".$sfq->sf_survey."' AND a.start_id = '".$rows[$iii]->id."' AND a.iscalefield_id = b.id "
							. "\n AND b.iscale_id = '".$sfq->sf_impscale."'";
						$database->SetQuery( $query );
						$user_answer = $database->LoadResult();
					}
					$one_answer->sf_impscale = $sfq->sf_impscale;
					$one_answer->iscale_answer = $user_answer;
					$user_answer = '';
					switch ($sfq->sf_qtype) {
						case 1:
							$fields = $t_fields[$sfq->id.'1'];


							$tmp_str = '';
							foreach($fields as $field){
								$query = "SELECT b.stext as user_answer FROM #__survey_force_user_answers as a, #__survey_force_scales as b"
									. "\n WHERE a.quest_id = '".$sfq->id."' and b.quest_id = a.quest_id and a.answer = {$field->id} and b.id = a.ans_field  and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."' ORDER BY b.ordering";
								$database->SetQuery( $query );
								$user_answer .= $this->SF_processCSVField($database->LoadResult()).',';

								$tmp_str .= ','.str_replace(',','',$this->SF_processCSVField(str_replace("\r\n","",$sfq->sf_qtext.' - '.$field->ftext)));
							}
							$sf_quests[$key]->sf_qtext2 = $tmp_str;
							break;
						case 5:
						case 6:
						case 9:
							$fields = $t_fields[$sfq->id.'569'];
							$user_answer = '';
							$tmp_str = '';							
							foreach($fields as $field){
								$query = "SELECT b.ftext as user_answer, c.ans_txt AS user_text  FROM (#__survey_force_user_answers as a, #__survey_force_fields as b) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.next_quest_id = c.id "
									. "\n WHERE a.quest_id = '".$sfq->id."' and b.quest_id = a.quest_id and a.answer = {$field->id} and b.id = a.ans_field  and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."'";
								$database->SetQuery( $query );

								$user_answer_ = $database->LoadObjectList();
								if (isset($user_answer_[0])) {
									
									$user_answer .= '"'.$user_answer_[0]->user_answer.($user_answer_[0]->user_text? ' ('.str_replace(',','',$this->SF_processCSVField_noquot(str_replace("\r\n","",$user_answer_[0]->user_text))).')':'').'",';
								} else {
									$user_answer .= JText::_('COM_SURVEYFORCE_NO_ANSWER').',';
								}


								$tmp_str .= ','.str_replace(',','',$this->SF_processCSVField(str_replace("\r\n","",$sfq->sf_qtext.' - '.$field->ftext)));
							}							
							$sf_quests[$key]->sf_qtext2 = $tmp_str;
							break;
						case 2:
							$query = "SELECT b.ftext as user_answer, c.ans_txt AS user_text  FROM (#__survey_force_user_answers as a, #__survey_force_fields as b ) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.ans_field = c.id "
								. "\n WHERE a.quest_id = '".$sfq->id."' and b.quest_id = a.quest_id and b.id = a.answer and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."'";
							$database->SetQuery( $query );
							$user_answer_ = $database->LoadObjectList();
							$user_answer = '';
							if (isset($user_answer_[0])) {
								$user_answer = $user_answer_[0]->user_answer.($user_answer_[0]->user_text? ' ('.str_replace(',','',$this->SF_processCSVField_noquot(str_replace("\r\n","",$user_answer_[0]->user_text))).')':'');
							}
							break;

						case 3:
							$query = "SELECT b.ftext AS user_answer, c.ans_txt AS user_text FROM (#__survey_force_user_answers as a, #__survey_force_fields as b) LEFT JOIN `#__survey_force_user_ans_txt` AS c ON a.ans_field = c.id "
								. "\n WHERE a.quest_id = '".$sfq->id."' and b.quest_id = a.quest_id and b.id = a.answer and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."'"
								. "\n ORDER BY b.ordering";
							$database->SetQuery( $query );
							$ans_inf_data = $database->LoadObjectList();
							$user_answer = '';
							if (count($ans_inf_data)) {
								foreach($ans_inf_data as $ans_inf_data_) {
									$user_answer .= $ans_inf_data_->user_answer.($ans_inf_data_->user_text? ' ('.str_replace(',','',$this->SF_processCSVField_noquot(str_replace("\r\n","",$ans_inf_data_->user_text))).')':'').';';
								}
							}
							break;
						case 4:
							$n = mb_substr_count($sfq->sf_qtext, '{x}')+mb_substr_count($sfq->sf_qtext, '{y}');
							if ($n > 0) {
								$tmp = JText::_('COM_SF_1ST_ANSWER');
								$tmp_str = '';
								for($i = 0; $i < $n; $i++){
									if ($i == 1) $tmp = JText::_('COM_SF_SECOND_ANSWER');
									elseif($i == 2)	$tmp = JText::_('COM_SF_THIRD_ANSWER');
									elseif ($i > 2) $tmp = ($i+1).JText::_('COM_SF_TH_ANSWER');
									$query = "SELECT b.ans_txt as user_answer FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b "
										." WHERE a.ans_field = '".($i+1)."' AND a.quest_id = '".$sfq->id."' and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."' and a.answer = b.id";
									$database->SetQuery( $query );
									$user_answer .= '"'.$this->SF_processCSVField_noquot($database->LoadResult()).'",';

									$tmp_str .= ',"'.mb_substr(str_replace(',', '', $this->SF_processCSVField_noquot(str_replace("\r\n", "", $sfq->sf_qtext))), 0, $max_quest_length).' - '.$tmp.'"';

								}
								$sf_quests[$key]->sf_qtext2 = $tmp_str;
							}
							else {
								$query = "SELECT b.ans_txt as user_answer FROM #__survey_force_user_answers as a, #__survey_force_user_ans_txt as b WHERE a.quest_id = '".$sfq->id."' and a.survey_id = '".$sfq->sf_survey."' and a.start_id = '".$rows[$iii]->id."' and a.answer = b.id";
								$database->SetQuery( $query );
								$user_answer = $this->SF_processCSVField_noquot($database->LoadResult());
								if (!$user_answer) $user_answer = '';
							}
							break;
					}
					$one_answer->answer = $user_answer;
					$one_answer->sf_qtype = (isset($sf_quests[$key]->sf_qtext2) && $sfq->sf_qtype == 4? 41:$sfq->sf_qtype);

					$rows[$iii]->questions[] = $one_answer;
					unset($one_answer);

				}
				$row = $rows[$iii];

				if ($iii == 0) {
					foreach ($sf_quests as $i=>$sfq) {
						if (!isset($sfq->sf_qtext2))
							echo ','.$this->SF_processCSVField(mb_substr(str_replace("\r\n","",str_replace(',','',$sfq->sf_qtext)),0, $max_quest_length));
						else
							echo $sfq->sf_qtext2;
						if ($show_iscale && $sfq->sf_impscale) {
							echo ','.str_replace(',','',$this->SF_processCSVField(mb_substr(str_replace("\r\n","",$sfq->iscale_name),0, $max_quest_length)));
						}
					}
					echo "\n";
				}

				if ($add_info) {
					echo '"'.$row->id.'","'.$row->sf_time.'","'.($row->is_complete == 0?'Incomplete': 'Complete').'",'.$this->SF_processCSVField(str_replace("\r\n","",$row->survey_name)).',"';
					switch($row->usertype) {
						case '0': echo JText::_('COM_SF_GUEST').'",'; break;
						case '1': echo JText::_('COM_SF_REGISTERED_USER').'",'; break;
						case '2': echo JText::_('COM_SF_INVITED_USER').'",'; break;
						default: echo '",'; break;
					}
				}
				else {
					switch($row->usertype) {
						case '0': echo '"'.JText::_('COM_SF_GUEST').'",'; break;
						case '1': echo '"'.JText::_('COM_SF_REGISTERED_USER').'",'; break;
						case '2': echo '"'.JText::_('COM_SF_INVITED_USER').'",'; break;
						default: echo '"",'; break;
					}
				}
				switch($row->usertype) {
					case '0': echo '"'.JText::_('COM_SF_ANONYMOUS').'",'; break;
					case '1': echo '"'.$row->reg_username."; ".$row->reg_name." (".$row->reg_email.')",'; break;
					case '2': echo '"'.$row->inv_name." ".$row->inv_lastname." (".$row->inv_email.')",'; break;
					default: echo '"",'; break;
				}

				foreach ($row->questions as $rq) {
					if ($rq->sf_qtype != 1 && $rq->sf_qtype != 5 && $rq->sf_qtype != 6 && $rq->sf_qtype !=9 && $rq->sf_qtype != 41)
						echo $this->SF_processCSVField($rq->answer).",";
					else
						echo $rq->answer;
					if ($show_iscale && $rq->sf_impscale) {
						echo $this->SF_processCSVField($rq->iscale_answer).",";
					}
				}
				echo "\n";

				unset($row);
				unset($rows[$iii]);
				$iii++;
			}

			unset($t_fields);

			die;
		}
	}

	function SF_processCSVField($field_text) {
		$field_text = strip_tags($field_text);
		$field_text = str_replace( '&#039;', "'", $field_text );
		$field_text = str_replace( '&#39;', "'", $field_text );
		$field_text = str_replace('&quot;',  '"', $field_text );
		$field_text = str_replace( '"', '""', $field_text );
		$field_text = str_replace( "\n", ' ', $field_text );
		$field_text = str_replace( "\r", ' ', $field_text );
		$field_text = strtr( $field_text, array_flip($this->get_html_translation_table_my( ) ) );
		$field_text = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $field_text );
		$field_text = '"'.$field_text.'"';
		return $field_text;
	}

	function SF_processCSVField_noquot($field_text) {
		$field_text = strip_tags($field_text);
		$field_text = str_replace( '&#039;', "'", $field_text );
		$field_text = str_replace( '&#39;', "'", $field_text );
		$field_text = str_replace('&quot;',  '"', $field_text );
		$field_text = str_replace( '"', '""', $field_text );
		$field_text = str_replace( "\n", ' ', $field_text );
		$field_text = str_replace( "\r", ' ', $field_text );
		$field_text = strtr( $field_text, array_flip($this->get_html_translation_table_my( ) ) );
		$field_text = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $field_text );
		return $field_text;
	}
}