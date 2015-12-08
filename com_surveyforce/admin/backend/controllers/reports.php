<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerReports extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getModel($name = 'Reports', $prefix = 'SurveyforceModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function add() {
        $this->setRedirect('index.php?option=com_surveyforce&task=report.add');
    }

    public function delete() {
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        if ($tmpl == 'component')
            $tmpl = '&tmpl=component';
        else
            $tmpl = '';

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $item_id = $cid['0'];
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=report.edit&id=' . $item_id, false));
    }

	public function pdf_sum()
	{
		self::SF_ViewRepUsers(0);
	}

	public function pdf_sum_perc()
	{
		self::SF_ViewRepUsers(1);
	}

	function SF_ViewRepUsers( $is_pc = 0 ) {

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$database = JFactory::getDbo();

		$surv_id = JFactory::getApplication()->input->get('filter_survey_name', 0);

		$query = "SELECT * FROM #__survey_force_survs WHERE id = '".$surv_id."'";
		$database->SetQuery( $query );
		$survey_data = $database->loadObjectList();
		if (!count($survey_data)) {
			echo "<script> alert('".JText::_('COM_SF_NO_RESULTS_FOUND')."'); window.history.go(-1);</script>\n";
			exit;
		}

		$survey_data = $survey_data[0];

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."'";
		$database->SetQuery( $query );
		$survey_data->total_starts = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and usertype = 0";
		$database->SetQuery( $query );
		$survey_data->total_gstarts = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and usertype = 1";
		$database->SetQuery( $query );
		$survey_data->total_rstarts = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and usertype = 2";
		$database->SetQuery( $query );
		$survey_data->total_istarts = $database->LoadResult();

		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and is_complete = 1";
		$database->SetQuery( $query );
		$survey_data->total_completes = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and is_complete = 1 and usertype = 0";
		$database->SetQuery( $query );
		$survey_data->total_gcompletes = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and is_complete = 1 and usertype = 1";
		$database->SetQuery( $query );
		$survey_data->total_rcompletes = $database->LoadResult();
		$query = "SELECT count(*) FROM #__survey_force_user_starts WHERE survey_id = '".$surv_id."' and is_complete = 1 and usertype = 2";
		$database->SetQuery( $query );
		$survey_data->total_icompletes = $database->LoadResult();

		$query = "SELECT q.*"
			. " FROM #__survey_force_quests as q"
			. " WHERE q.published = 1 AND q.sf_survey = '".$survey_data->id."'"
			. " ORDER BY q.ordering, q.id ";

		$database->SetQuery( $query );
		$questions_data = $database->loadObjectList();
		$i = 0;
		$query = "SELECT b.id FROM #__survey_force_user_starts as b "
				.( !empty($cid) ? "WHERE b.id in (".implode(',', $cid).")" : " WHERE b.survey_id = ".$surv_id );

		$database->SetQuery( $query );

		$start_id_array = $database->loadColumn();
		$start_id_array[] = 0;
		$start_ids = @implode(',',$start_id_array);

		while ( $i < count($questions_data) ) {
			if ($questions_data[$i]->sf_impscale) {
				$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '".$questions_data[$i]->sf_impscale."'";
				$database->SetQuery( $query );
				$questions_data[$i]->iscale_name = $database->loadResult();

				$query = "SELECT count(a.id) FROM #__survey_force_user_answers_imp as a"
					. "\n WHERE a.quest_id = '".$questions_data[$i]->id."' and a.survey_id = '".$questions_data[$i]->sf_survey."' and a.iscale_id = '".$questions_data[$i]->sf_impscale."'"
					. "\n and a.start_id IN (".$start_ids.")";
				$database->SetQuery( $query );
				$questions_data[$i]->total_iscale_answers = $database->LoadResult();

				$query = "SELECT b.isf_name, count(a.iscalefield_id) as ans_count FROM #__survey_force_iscales_fields as b LEFT JOIN #__survey_force_user_answers_imp as a ON a.quest_id = '".$questions_data[$i]->id."' and a.survey_id = '".$questions_data[$i]->sf_survey."' and a.iscale_id = '".$questions_data[$i]->sf_impscale."' and a.start_id IN (".$start_ids.") and a.iscalefield_id = b.id"
					. "\n WHERE b.iscale_id = '".$questions_data[$i]->sf_impscale."'"
					. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
				$database->SetQuery( $query );
				$ans_data = $database->loadObjectList();
				$questions_data[$i]->answer_imp = array();
				$j = 0;
				while ( $j < count($ans_data) ) {
					$questions_data[$i]->answer_imp[$j]->num = $j;
					$questions_data[$i]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
					$questions_data[$i]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
					$j ++;
				}
			}
			$questions_data[$i]->sf_qtext = trim(strip_tags($questions_data[$i]->sf_qtext,'<a><b><i><u>'));
			switch ($questions_data[$i]->sf_qtype) {
				case 2:
					$query = "SELECT count(a.id) FROM #__survey_force_user_answers as a"
						. "\n WHERE a.quest_id = '".$questions_data[$i]->id."' and a.survey_id = '".$questions_data[$i]->sf_survey."' "
						. "\n and start_id IN (".$start_ids.")";
					$database->SetQuery( $query );
					$questions_data[$i]->total_answers = ( $database->LoadResult() ? $database->LoadResult() : 1);

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.start_id IN (".$start_ids.") AND a.answer = b.id AND a.quest_id = '".$questions_data[$i]->id."') "
						. "\n WHERE b.quest_id = '".$questions_data[$i]->id."'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering"; //ans_count DESC
					$database->SetQuery( $query );
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ( $j < count($ans_data) ) {
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = ($is_pc? round($ans_data[$j]->ans_count/$questions_data[$i]->total_answers*100,2 ): $ans_data[$j]->ans_count);
						$j ++;
					}
					break;
				case 3:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '".$questions_data[$i]->id."' and survey_id = '".$questions_data[$i]->sf_survey."' and start_id IN (".$start_ids.")";
					$database->SetQuery( $query );
					$questions_data[$i]->total_answers = ( $database->LoadResult() ? $database->LoadResult() : 1);

					$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id AND a.start_id IN (".$start_ids.") AND a.quest_id = '".$questions_data[$i]->id."' )"
						. "\n WHERE b.quest_id = '".$questions_data[$i]->id."'"
						. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
					$database->SetQuery( $query );
					$ans_data = $database->loadObjectList();
					$questions_data[$i]->answer = array();
					$j = 0;
					while ( $j < count($ans_data) ) {
						$questions_data[$i]->answer[$j]->num = $j;
						$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ftext;
						$questions_data[$i]->answer[$j]->ans_count = ($is_pc? round($ans_data[$j]->ans_count/$questions_data[$i]->total_answers*100, 2) :$ans_data[$j]->ans_count);
						$j ++;
					}
					break;
				case 4:
					$n = substr_count($questions_data[$i]->sf_qtext, '{x}')+substr_count($questions_data[$i]->sf_qtext, '{y}');
					if ($n > 0) {
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$questions_data[$i]->id."' AND survey_id = '".$questions_data[$i]->sf_survey."' AND start_id IN (".$start_ids.")  GROUP BY start_id, quest_id ";
						$database->SetQuery( $query );
						$questions_data[$i]->total_answers = ( count($database->loadColumn()) ? count($database->loadColumn()) : 1);

						$questions_data[$i]->answer = array();
						$questions_data[$i]->answers_top100 = array();
						$questions_data[$i]->answer_count = $n;
						for($j = 0; $j < $n; $j++) {
							$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = ".($j+1)
								." AND quest_id = '".$questions_data[$i]->id."' AND start_id IN (".$start_ids.") "
								." AND survey_id = '".$questions_data[$i]->sf_survey."' ";
							$database->SetQuery( $query );
							$ans_txt_data = @array_merge(array(0=>0),$database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '".$questions_data[$i]->id."'"
								. "\n AND a.answer = b.id AND a.start_id IN (".$start_ids.") "
								. "\n AND a.answer IN (".implode(',', $ans_txt_data).") "
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->SetQuery( $query );
							$ans_data = $database->loadObjectList();
							$jj = 0;
							$tmp = array();
							while ( $jj < count($ans_data) ) {
								$tmp[$jj]->num = $jj;
								$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
								$tmp[$jj]->ans_count = ($is_pc? round($ans_data[$jj]->ans_count/$questions_data[$i]->total_answers*100, 2): $ans_data[$jj]->ans_count);
								$jj ++;
							}
							$questions_data[$i]->answer[$j] = $tmp;

							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '".$questions_data[$i]->id."' AND a.answer = b.id"
								. "\n AND a.answer IN (".implode(',', $ans_txt_data).") AND a.start_id IN (".$start_ids.") "
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->SetQuery( $query );
							$ans_data = $database->loadColumn();
							$ans_data = (is_array($ans_data)?$ans_data:array());
							$questions_data[$i]->answers_top100[$j] = implode(', ',$ans_data);
						}
					}
					else {
						$query = "SELECT id FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$questions_data[$i]->id."' AND survey_id = '".$questions_data[$i]->sf_survey."' AND start_id IN (".$start_ids.")  GROUP BY start_id, quest_id ";
						$database->SetQuery( $query );
						$questions_data[$i]->total_answers = ( count($database->loadColumn()) ? count($database->loadColumn()) : 1);

						$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '".$questions_data[$i]->id."' and a.survey_id = '".$questions_data[$i]->sf_survey."' and a.answer = b.id and a.start_id IN (".$start_ids.")"
							. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
						$database->SetQuery( $query );
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer = array();
						$j = 0;
						while ( $j < count($ans_data) ) {
							$questions_data[$i]->answer[$j]->num = $j;
							$questions_data[$i]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
							$questions_data[$i]->answer[$j]->ans_count = ($is_pc? round($ans_data[$j]->ans_count/$questions_data[$i]->total_answers*100, 2):$ans_data[$j]->ans_count);
							$j ++;
						}
						$ans_data = array();
						$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
							. "\n WHERE a.quest_id = '".$questions_data[$i]->id."' and a.survey_id = '".$questions_data[$i]->sf_survey."' and a.start_id IN (".$start_ids.") and a.answer = b.id "
							. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
						$database->SetQuery( $query );
						$ans_data = $database->loadColumn();
						if (count($ans_data) > 0) {
							$questions_data[$i]->answers_top100 = implode(', ',$ans_data);
						} else { $questions_data[$i]->answers_top100 = ''; }
					}
					break;
				case 1:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '".$questions_data[$i]->id."' and survey_id = '".$questions_data[$i]->sf_survey."' and start_id IN (".$start_ids.")";
					$database->SetQuery( $query );
					$questions_data[$i]->total_answers = ( $database->LoadResult() ? $database->LoadResult() : 1);

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '".$questions_data[$i]->id."' ORDER by ordering";
					$database->SetQuery( $query );
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ( $j < count($f_data) ) {
						$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '".$f_data[$j]->id."' AND a.start_id IN (".$start_ids.") AND a.quest_id = '".$questions_data[$i]->id."' )"
							. "\n WHERE b.quest_id = '".$questions_data[$i]->id."'"
							. "\n GROUP BY b.stext ORDER BY b.ordering";
						$database->SetQuery( $query );
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ( $jj < count($ans_data) ) {
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = ($is_pc? round($ans_data[$jj]->ans_count/$questions_data[$i]->total_answers*100, 2):$ans_data[$jj]->ans_count);
							$jj ++;
						}
						$j++;
					}
					break;
				case 5:
				case 6:
				case 9:
					$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
						. "\n WHERE quest_id = '".$questions_data[$i]->id."' and survey_id = '".$questions_data[$i]->sf_survey."' and start_id IN (".$start_ids.")";
					$database->SetQuery( $query );
					$questions_data[$i]->total_answers = ( $database->LoadResult() ? $database->LoadResult() : 1);

					$query = "SELECT * FROM #__survey_force_fields WHERE quest_id = '".$questions_data[$i]->id."' and is_main = '1' ORDER by ordering";
					$database->SetQuery( $query );
					$f_data = $database->loadObjectList();
					$j = 0;
					$questions_data[$i]->answer = array();
					while ( $j < count($f_data) ) {
						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b LEFT JOIN #__survey_force_user_answers as a ON ( a.ans_field = b.id AND a.answer = '".$f_data[$j]->id."' AND a.start_id IN (".$start_ids.") AND a.quest_id = '".$questions_data[$i]->id."' )"
							. "\n WHERE b.quest_id = '".$questions_data[$i]->id."' and b.is_main = '0'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->SetQuery( $query );
						$ans_data = $database->loadObjectList();
						$questions_data[$i]->answer[$j]->full_ans = array();
						$jj = 0;
						$questions_data[$i]->answer[$j]->ftext = $f_data[$j]->ftext;
						while ( $jj < count($ans_data) ) {
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
							$questions_data[$i]->answer[$j]->full_ans[$jj]->ans_count = ($is_pc? round($ans_data[$jj]->ans_count/$questions_data[$i]->total_answers*100, 2):$ans_data[$jj]->ans_count);
							$jj ++;
						}
						$j++;
					}
					break;
			}
			$i++;
		}

		SurveyforceHelper::SF_PrintRepSurv_List( $survey_data, $questions_data, $is_pc );
	}

	public function csv_sum()
	{
		$database = JFactory::getDbo();
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		$filt_status = JFactory::getApplication()->input->get('filter_is_complete', '');
		$filt_utype = JFactory::getApplication()->input->get('filter_usertype', 0);
		$surv_id = JFactory::getApplication()->input->get('filter_survey_name', 0);

		// get the subset (based on limits) of required records
		$query = "SELECT distinct sf_s.sf_name as survey_name, sf_s.id as survey_id "
			. "\n FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";

		$query .= ""
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ( $surv_id ? "\n and sf_s.id = $surv_id" : '' )
			. ( $filt_status!='' ? "\n and sf_ust.is_complete = '".$filt_status."'" : '' )
			. ( $filt_utype ? "\n and sf_ust.usertype = '".($filt_utype -1)."'" : '' );

		if ((count($cid) > 0) && ($cid[0] != 0)) {
			$cids =implode(',', $cid);
			$query .= "\n and sf_ust.id in (".$cids.")";
		}
		$query .= "\n ORDER BY sf_s.sf_name";

		$database->SetQuery($query);
		$rows = $database->loadObjectList();

		$query = "SELECT distinct sf_ust.id "
			. "\n FROM #__survey_force_user_starts as sf_ust, #__survey_force_survs as sf_s";

		$query .= ""
			. "\n WHERE sf_ust.survey_id = sf_s.id"
			. ( $surv_id ? "\n and sf_s.id = $surv_id" : '' )
			. ( $filt_status!='' ? "\n and sf_ust.is_complete = '".$filt_status."'" : '' )
			. ( $filt_utype ? "\n and sf_ust.usertype = '".($filt_utype -1)."'" : '' );

		if ((count($cid) > 0) && ($cid[0] != 0)) {
			$cids =implode(',', $cid);
			$query .= "\n and sf_ust.id in (".$cids.")";
		}

		$query .= "\n ORDER BY sf_ust.id";
		$database->SetQuery( $query );
		$start_ids = $database->loadColumn();
		$start_ids[] = 0;
		$starts_str = implode(',',$start_ids);

		$ri = 0;
		while ($ri < count($rows)) {

			$query = "SELECT * FROM #__survey_force_survs WHERE id = '".$rows[$ri]->survey_id."'";
			$database->SetQuery( $query );
			$rows[$ri]->survey_data = $database->loadObjectList();

			$query = "SELECT q.*"
				. "\n FROM #__survey_force_quests as q"
				. "\n WHERE q.published = 1 AND q.sf_survey = '".$rows[$ri]->survey_id."' AND sf_qtype NOT IN (7, 8)"
				. "\n ORDER BY q.ordering, q.id ";
			$database->SetQuery( $query );
			$rows[$ri]->questions_data = $database->loadObjectList();
			$qi = 0;
			$rows[$ri]->questions_data[$qi]->answer = '';
			while ( $qi < count($rows[$ri]->questions_data) ) {
				if ($rows[$ri]->questions_data[$qi]->sf_impscale) {
					$query = "SELECT iscale_name FROM #__survey_force_iscales WHERE id = '".$rows[$ri]->questions_data[$qi]->sf_impscale."'";
					$database->SetQuery( $query );
					$rows[$ri]->questions_data[$qi]->iscale_name = $database->loadResult();

					$query = "SELECT count(id) FROM #__survey_force_user_answers_imp"
						. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."' and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
						. "\n AND iscale_id = '".$rows[$ri]->questions_data[$qi]->sf_impscale."' and start_id IN (".$starts_str.")";
					$database->SetQuery( $query );
					$rows[$ri]->questions_data[$qi]->total_iscale_answers = $database->LoadResult();

					$query = "SELECT b.isf_name, count(a.id) as ans_count FROM #__survey_force_iscales_fields as b"
						. "\n LEFT JOIN #__survey_force_user_answers_imp as a ON"
						. "\n a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
						. "\n and a.survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
						. "\n and a.iscale_id = '".$rows[$ri]->questions_data[$qi]->sf_impscale."'"
						. "\n and a.start_id IN (".$starts_str.") and a.iscalefield_id = b.id "
						. "\n WHERE b.iscale_id = '".$rows[$ri]->questions_data[$qi]->sf_impscale."'"
						. "\n GROUP BY b.isf_name ORDER BY  b.ordering";//ans_count DESC,
					$database->SetQuery( $query );
					$ans_data = $database->loadObjectList();

					$rows[$ri]->questions_data[$qi]->answer_imp = array();
					$j = 0;
					while ( $j < count($ans_data) ) {
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->num = $j;
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->ftext = $ans_data[$j]->isf_name;
						$rows[$ri]->questions_data[$qi]->answer_imp[$j]->ans_count = $ans_data[$j]->ans_count;
						$j ++;
					}
				}
				$rows[$ri]->questions_data[$qi]->sf_qtext = trim(strip_tags($rows[$ri]->questions_data[$qi]->sf_qtext,'<a><b><i><u>'));
				switch ($rows[$ri]->questions_data[$qi]->sf_qtype) {
					case 2:
						$query = "SELECT count(id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
							. "\n and start_id IN (".$starts_str.") ";
						$database->SetQuery( $query );
						$rows[$ri]->questions_data[$qi]->total_answers = $database->LoadResult();

						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
							. "\n LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id and a.start_id IN (".$starts_str.") AND a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' )"
							. "\n WHERE b.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->SetQuery( $query );
						$ans_data = $database->loadObjectList();
						$rows[$ri]->questions_data[$qi]->answer = array();
						$j = 0;
						while ( $j < count($ans_data) ) {
							$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ftext;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j ++;
						}
						break;
					case 3:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."' "
							. "\n and start_id IN (".$starts_str.") ";
						$database->SetQuery( $query );
						$rows[$ri]->questions_data[$qi]->total_answers = $database->LoadResult();

						$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
							. "\n LEFT JOIN #__survey_force_user_answers as a ON ( a.answer = b.id and a.start_id IN (".$starts_str.") AND a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' )"
							. "\n WHERE b.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n GROUP BY b.ftext ORDER BY b.ordering";//ans_count DESC
						$database->SetQuery( $query );
						$ans_data = $database->loadObjectList();
						$rows[$ri]->questions_data[$qi]->answer = array();
						$j = 0;
						while ( $j < count($ans_data) ) {
							$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ftext;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
							$j ++;
						}
						break;
					case 4:
						$n = substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, '{x}')+substr_count($rows[$ri]->questions_data[$qi]->sf_qtext, '{y}');
						if ($n > 0) {
							$query = "SELECT id FROM #__survey_force_user_answers"
								. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
								. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
								. "\n and start_id IN (".$starts_str.") GROUP BY start_id, quest_id";
							$database->SetQuery( $query );
							$rows[$ri]->questions_data[$qi]->total_answers = count($database->loadColumn());
							$rows[$ri]->questions_data[$qi]->answer = array();
							$rows[$ri]->questions_data[$qi]->answers_top100 = array();
							$rows[$ri]->questions_data[$qi]->answer_count = $n;
							for($j = 0; $j < $n; $j++) {
								$query = "SELECT answer FROM #__survey_force_user_answers WHERE ans_field = ".($j+1)
									." AND quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
									." AND survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
									." AND start_id IN (".$starts_str.") ";
								$database->SetQuery( $query );
								$ans_txt_data = @array_merge(array(0=>0),$database->loadColumn());

								$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
									. "\n #__survey_force_user_answers as a"
									. "\n WHERE a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
									. "\n and a.answer = b.id and a.start_id IN (".$starts_str.")"
									. "\n AND a.answer IN (".implode(',', $ans_txt_data).") "
									. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
								$database->SetQuery( $query );
								$ans_data = $database->loadObjectList();
								$jj = 0;
								$tmp = array();
								while ( $jj < count($ans_data) ) {
									$tmp[$jj]->num = $jj;
									$tmp[$jj]->ftext = $ans_data[$jj]->ans_txt;
									$tmp[$jj]->ans_count = $ans_data[$jj]->ans_count;
									$jj ++;
								}
								$rows[$ri]->questions_data[$qi]->answer[$j] = $tmp;

								$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
									. "\n WHERE a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' and a.answer = b.id"
									. "\n and a.start_id IN (".$starts_str.")"
									. "\n AND a.answer IN (".implode(',', $ans_txt_data).") "
									. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
								$database->SetQuery( $query );
								$ans_data = $database->loadColumn();
								$rows[$ri]->questions_data[$qi]->answers_top100[$j] = implode(', ',$ans_data);
							}
						}
						else {
							$query = "SELECT id FROM #__survey_force_user_answers"
								. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
								. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
								. "\n and start_id IN (".$starts_str.") GROUP BY start_id, quest_id";
							$database->SetQuery( $query );
							$rows[$ri]->questions_data[$qi]->total_answers = count($database->loadColumn());

							$query = "SELECT b.ans_txt, count(a.answer) as ans_count FROM #__survey_force_user_ans_txt as b,"
								. "\n #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
								. "\n and a.answer = b.id and a.start_id IN (".$starts_str.")"
								. "\n GROUP BY b.ans_txt ORDER BY ans_count DESC LIMIT 0,5";
							$database->SetQuery( $query );
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer = array();
							$j = 0;
							while ( $j < count($ans_data) ) {
								$rows[$ri]->questions_data[$qi]->answer[$j]->num = $j;
								$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $ans_data[$j]->ans_txt;
								$rows[$ri]->questions_data[$qi]->answer[$j]->ans_count = $ans_data[$j]->ans_count;
								$j ++;
							}
							$query = "SELECT b.ans_txt FROM #__survey_force_user_ans_txt as b, #__survey_force_user_answers as a"
								. "\n WHERE a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' and a.answer = b.id"
								. "\n and a.start_id IN (".$starts_str.")"
								. "\n ORDER BY a.sf_time DESC LIMIT 0,100";
							$database->SetQuery( $query );
							$ans_data = $database->loadColumn();
							$rows[$ri]->questions_data[$qi]->answers_top100 = implode(', ',$ans_data);
						}
						break;
					case 1:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
							. "\n and start_id IN (".$starts_str.")";
						$database->SetQuery( $query );
						$rows[$ri]->questions_data[$qi]->total_answers = $database->LoadResult();

						$query = "SELECT * FROM #__survey_force_fields"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."' ORDER by ordering";
						$database->SetQuery( $query );
						$f_data = $database->loadObjectList();
						$j = 0;
						$rows[$ri]->questions_data[$qi]->answer = array();
						while ( $j < count($f_data) ) {
							$query = "SELECT b.stext, count(a.answer) as ans_count FROM #__survey_force_scales as b"
								. "\n LEFT JOIN #__survey_force_user_answers as a"
								. "\n ON ( a.ans_field = b.id and a.answer = '".$f_data[$j]->id."' "
								. "\n and a.start_id IN (".$starts_str.") AND a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' )"
								. "\n WHERE b.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
								. "\n GROUP BY b.stext ORDER BY b.ordering";
							$database->SetQuery( $query );
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans = array();
							$jj = 0;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $f_data[$j]->ftext;
							while ( $jj < count($ans_data) ) {
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->stext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj ++;
							}
							$j++;
						}
						break;
					case 5:
					case 6:
					case 9:
						$query = "SELECT count(distinct start_id) FROM #__survey_force_user_answers"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
							. "\n and survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
							. "\n and start_id IN (".$starts_str.")";
						$database->SetQuery( $query );
						$rows[$ri]->questions_data[$qi]->total_answers = $database->LoadResult();

						$query = "SELECT * FROM #__survey_force_fields"
							. "\n WHERE quest_id = '".$rows[$ri]->questions_data[$qi]->id."' and is_main = '1' ORDER by ordering";
						$database->SetQuery( $query );
						$f_data = $database->loadObjectList();
						$j = 0;
						$rows[$ri]->questions_data[$qi]->answer = array();
						while ( $j < count($f_data) ) {
							$query = "SELECT b.ftext, count(a.answer) as ans_count FROM #__survey_force_fields as b"
								. "\n LEFT JOIN #__survey_force_user_answers as a ON a.ans_field = b.id"
								. "\n and a.answer = '".$f_data[$j]->id."'"
								. "\n and a.quest_id = '".$rows[$ri]->questions_data[$qi]->id."'"
								. "\n and a.survey_id = '".$rows[$ri]->questions_data[$qi]->sf_survey."'"
								. "\n and a.start_id IN (".$starts_str.")"
								. "\n WHERE b.quest_id = '".$rows[$ri]->questions_data[$qi]->id."' and b.is_main = '0'"
								. "\n GROUP BY b.ftext ORDER BY b.ordering ";//ans_count DESC

							$database->SetQuery( $query );
							$ans_data = $database->loadObjectList();
							$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans = array();
							$jj = 0;
							$rows[$ri]->questions_data[$qi]->answer[$j]->ftext = $f_data[$j]->ftext;
							while ( $jj < count($ans_data) ) {
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ftext = $ans_data[$jj]->ftext;
								$rows[$ri]->questions_data[$qi]->answer[$j]->full_ans[$jj]->ans_count = $ans_data[$jj]->ans_count;
								$jj ++;
							}
							$j++;
						}
						break;
				}
				$qi++;
			}

			$ri ++;
		}


		$text_to_csv = "";
		$cur_survey = -1;
		for ($ij=0, $n=count($rows); $ij < $n; $ij++) {
			$row = $rows[$ij];
			if ($cur_survey != $row->survey_id) {
				$text_to_csv .= JText::_('COM_SF_SURVEY_INFORMATION').':,'."\n";
				$text_to_csv .= JText::_('COM_SF_NAME').':,';
				$text_to_csv .= SurveyforceHelper::SF_processCSVField($row->survey_data[0]->sf_name).","."\n";
				$text_to_csv .= JText::_('COM_SF_DESCRIPTION').',';
				$text_to_csv .= SurveyforceHelper::SF_processCSVField($row->survey_data[0]->sf_descr).","."\n";
			}
			$cur_survey = $row->survey_id;
			$text_to_csv .= "\n".JText::_('COM_SF_ANSWERS').':,' . "\n";
			foreach ($row->questions_data as $qrow) {
				$text_to_csv .= "\n" . SurveyforceHelper::SF_processCSVField($qrow->sf_qtext) ."," ."\n";
				switch ($qrow->sf_qtype) {
					case 2:
					case 3:
					case 4:
						if (isset($qrow->answer_count)) {
							$tmp = JText::_('COM_SF_1ST_ANSWER');
							for($ii = 1; $ii <= $qrow->answer_count; $ii++) {
								if ($ii == 2) $tmp = JText::_('COM_SF_SECOND_ANSWER');
								elseif($ii == 3)	$tmp = JText::_('COM_SF_THIRD_ANSWER');
								elseif ($ii > 3) $tmp = $ii.JText::_('COM_SF_TH_ANSWER');
								$text_to_csv .= $tmp  . "\n";
								$total = $qrow->total_answers;
								$i = 0;
								$tmp_data = array();
								if (count($qrow->answer[$ii-1]) > 0 ) {
									foreach ($qrow->answer[$ii-1] as $arow) {
										$tmp_data[$i] = $arow->ans_count;
										$i++;
									}
									foreach ($qrow->answer[$ii-1] as $arow) {
										$text_to_csv .=  SurveyforceHelper::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
									}
									if ($qrow->sf_qtype == 4) {
										$text_to_csv .= JText::_('COM_SF_OTHER_ANSWERS').':,,' . SurveyforceHelper::SF_processCSVField($qrow->answers_top100[$ii-1]) . "\n";
									}

								}
							}
						}
						else {
							$i = 0;
							$tmp_data = array();
							foreach ($qrow->answer as $arow) {
								$tmp_data[$i] = $arow->ans_count;
								$i++;
							}
							foreach ($qrow->answer as $arow) {
								$text_to_csv .=  SurveyforceHelper::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
							}
							if ($qrow->sf_qtype == 4) {
								$text_to_csv .= JText::_('COM_SF_OTHER_ANSWERS').':,,' . SurveyforceHelper::SF_processCSVField($qrow->answers_top100) . "\n";
							}
						}
						break;

					case 1:
					case 5:
					case 6:
					case 9:
						foreach ($qrow->answer as $arows) {
							$i = 0;
							$tmp_data = array();
							foreach ($arows->full_ans as $arow) {
								$tmp_data[$i] = $arow->ans_count;
								$i++;
							}
							if (isset($arows->ftext)) {
								$text_to_csv .= JText::_('COM_SF_OPTION').':,' . SurveyforceHelper::SF_processCSVField($arows->ftext) . "\n";
							}

							foreach ($arows->full_ans as $arow) {
								$text_to_csv .= SurveyforceHelper::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
							}
						}
						break;
				}
				if ($qrow->sf_impscale) {
					$i = 0;
					$tmp_data = array();
					foreach ($qrow->answer_imp as $arow) {
						$tmp_data[$i] = $arow->ans_count;
						$i++;
					}

					$text_to_csv .= SurveyforceHelper::SF_processCSVField($qrow->iscale_name) . "\n";
					foreach ($qrow->answer_imp as $arow) {
						$text_to_csv .= SurveyforceHelper::SF_processCSVField($arow->ftext) . ",," . $arow->ans_count . "\n";
					}
				}
			}
			$text_to_csv .= "\n";
		}
		@ob_end_clean();
		header("Content-type: application/csv");
		$text_to_csv = html_entity_decode($text_to_csv, ENT_QUOTES, "utf-8");
		header("Content-Length: ".strlen(ltrim($text_to_csv)));
		header("Content-Disposition: inline; filename=report.csv");
		echo $text_to_csv;
		die;
	}

}
