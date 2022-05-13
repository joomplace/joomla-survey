<?php

/**
 * Joomlaquiz Component for Joomla 3
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

/**
 * Results Model.
 *
 */
class JoomlaquizModelResults extends JModelList {

	public function __construct()
	{
		$this->database = JFactory::getDbo();
		parent::__construct();
	}

    public function getResults() {

		$database = JFactory::getDBO();
        $my = JFactory::getUser();
        $mainframe = JFactory::getApplication();

        $default_limit = 20;
        $limitstart = JFactory::getApplication()->input->get('limitstart', 0);
        $limit = $mainframe->getUserStateFromRequest('com_joomlaquiz.limit', 'limit', $default_limit, 'int');

        if (!$my->id) {
            return;
        }

        $query = "SELECT sq.c_id as id, SUM(squ.c_score) as user_score, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passing_score AS sq_c_passing_score, sq.c_quiz_id, "
                . "\n q.c_id, q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_pool, ch.q_chain, q.c_grading "
                . "\n FROM #__quiz_r_student_quiz as sq"
                . "\n LEFT JOIN #__quiz_r_student_question as squ ON sq.c_id = squ.c_stu_quiz_id"
                . "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
                . "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id"
                . "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE c_student_id = '{$my->id}' "
                . "\n GROUP BY squ.c_stu_quiz_id"
                . "\n ORDER BY sq.c_date_time DESC ";
        $database->SetQuery($query);
        $tmp_rows = $database->LoadObjectList();

        if (count($tmp_rows)) {
            foreach ($tmp_rows as $ii => $row) {
                if (!$row->user_score)
                    $tmp_rows[$ii]->user_score = 0;
                $nugno_score = ($row->c_passing_score * $row->c_full_score) / 100;
                $user_passed = 0;
                if ($tmp_rows[$ii]->user_score >= $nugno_score) {
                    $user_passed = 1;
                }
                $tmp_rows[$ii]->c_passed = $user_passed;
            }
        }

        $rows = array();
        $gquizzes = array();
        for ($i = 0, $n = count($tmp_rows); $i < $n; $i++) {
            if ($tmp_rows[$i]->c_grading && !in_array($tmp_rows[$i]->c_quiz_id, $gquizzes)) {
                if ($tmp_rows[$i]->c_grading == 1) {//First attempt
                    $query = "SELECT sq.c_id  as id, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed, sq.c_passing_score AS sq_c_passing_score, sq.c_quiz_id, "
                            . "\n q.c_id, q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_pool, ch.q_chain, q.c_grading "
                            . "\n FROM #__quiz_r_student_quiz as sq"
                            . "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
                            . "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id "
                            . "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE c_student_id = '{$my->id}' AND sq.c_quiz_id = " . $tmp_rows[$i]->c_quiz_id
                            . "\n ORDER BY sq.c_id ASC LIMIT 0, 1";
                    ;
                    $database->SetQuery($query);
                    $tmp = $database->LoadObjectList();
                    if (isset($tmp[0])) {
                        $query = "SELECT 1 FROM #__quiz_t_question AS q, #__quiz_r_student_question AS sq WHERE q.published = 1 AND q.c_manual = 1 AND q.c_id = sq.c_question_id AND sq.c_stu_quiz_id = '" . $tmp_rows[$i]->c_id . "' AND reviewed = 0";
                        $database->SetQuery($query);
                        $c_manual = (int) $database->LoadResult();
                        if ($c_manual) {
                            $tmp[0]->c_passed = -1;
                            $tmp[0]->c_total_score = JText::_('COM_JQ_SCORE_PENDING');
                        }
                    }
                    $rows[] = $tmp[0];
                    $gquizzes[] = $tmp_rows[$i]->c_quiz_id;
                } elseif ($tmp_rows[$i]->c_grading == 2) {//Last attempt
                    $query = "SELECT sq.c_id  as id, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passing_score AS sq_c_passing_score, sq.c_quiz_id, "
                            . "\n q.c_id, q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_pool, ch.q_chain, q.c_grading "
                            . "\n FROM #__quiz_r_student_quiz as sq"
                            . "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
                            . "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id"
                            . "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE c_student_id = '{$my->id}' AND sq.c_quiz_id = " . $tmp_rows[$i]->c_quiz_id
                            . "\n ORDER BY sq.c_id DESC LIMIT 0, 1";
                    ;
                    $database->SetQuery($query);
                    $tmp = $database->LoadObjectList();
                    if (isset($tmp[0])) {
                        $query = "SELECT 1 FROM #__quiz_t_question AS q, #__quiz_r_student_question AS sq WHERE q.published = 1 AND q.c_manual = 1 AND q.c_id = sq.c_question_id AND sq.c_stu_quiz_id = '" . $tmp_rows[$i]->c_id . "' AND reviewed = 0";
                        $database->SetQuery($query);
                        $c_manual = (int) $database->LoadResult();
                        if ($c_manual) {
                            $tmp[0]->c_passed = -1;
                            $tmp[0]->c_total_score = JText::_('COM_JQ_SCORE_PENDING');
                        }
                    }
                    $rows[] = $tmp[0];
                    $gquizzes[] = $tmp_rows[$i]->c_quiz_id;
                } elseif ($tmp_rows[$i]->c_grading == 3) {//Highest score
                    $query = "SELECT sq.c_id  as id, sq.c_passed, sq.c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed, sq.c_passing_score AS sq_c_passing_score, sq.c_quiz_id, "
                            . "\n q.c_id, q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_pool, ch.q_chain, q.c_grading "
                            . "\n FROM #__quiz_r_student_quiz as sq"
                            . "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
                            . "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id"
                            . "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE c_student_id = '{$my->id}'  AND sq.c_quiz_id = " . $tmp_rows[$i]->c_quiz_id
                            . "\n ORDER BY sq.c_total_score DESC LIMIT 0, 1";
                    ;
                    $database->SetQuery($query);
                    $tmp = $database->LoadObjectList();
                    if (isset($tmp[0])) {
                        $query = "SELECT 1 FROM #__quiz_t_question AS q, #__quiz_r_student_question AS sq WHERE q.published = 1 AND q.c_manual = 1 AND q.c_id = sq.c_question_id AND sq.c_stu_quiz_id = '" . $tmp_rows[$i]->c_id . "' AND reviewed = 0";
                        $database->SetQuery($query);
                        $c_manual = (int) $database->LoadResult();
                        if ($c_manual) {
                            $tmp[0]->c_passed = -1;
                            $tmp[0]->c_total_score = JText::_('COM_JQ_SCORE_PENDING');
                        }
                    }
                    $rows[] = $tmp[0];
                    $gquizzes[] = $tmp_rows[$i]->c_quiz_id;
                } elseif ($tmp_rows[$i]->c_grading == 4) {//Average score
                    $query = "SELECT MAX(sq.c_id) AS c_id, sq.c_passed, AVG(sq.c_total_score) AS c_total_score, sq.c_total_time, sq.c_date_time, sq.c_passed, sq.c_passing_score AS sq_c_passing_score, sq.c_quiz_id, "
                            . "\n q.c_id, q.c_title, q.c_author, q.c_passing_score, sq.c_student_id, u.username, u.name, u.email, q.c_full_score, q.c_pool, ch.q_chain, q.c_grading "
                            . "\n FROM #__quiz_r_student_quiz as sq"
                            . "\n LEFT JOIN #__users as u ON sq.c_student_id = u.id"
                            . "\n LEFT JOIN `#__quiz_q_chain` AS ch ON ch.s_unique_id = sq.unique_id"
                            . "\n LEFT JOIN #__quiz_t_quiz as q ON sq.c_quiz_id = q.c_id WHERE c_student_id = '{$my->id}' AND sq.c_quiz_id = " . $tmp_rows[$i]->c_quiz_id
                            . "\n GROUP BY sq.c_quiz_id ORDER BY sq.c_date_time DESC ";
                    ;
                    $database->SetQuery($query);
                    $tmp = $database->LoadObjectList();

                    $rows[] = $tmp[0];
                    $gquizzes[] = $tmp_rows[$i]->c_quiz_id;
                }
            } elseif (!$tmp_rows[$i]->c_grading) {

                $query = "SELECT 1 FROM #__quiz_t_question AS q, #__quiz_r_student_question AS sq WHERE q.published = 1 AND q.c_manual = 1 AND q.c_id = sq.c_question_id AND sq.c_stu_quiz_id = '" . $tmp_rows[$i]->c_id . "' AND reviewed = 0";
                $database->SetQuery($query);

                $c_manual = (int) $database->LoadResult();
                if ($c_manual) {
                    $tmp_rows[$i]->c_passed = -1;
                    $tmp_rows[$i]->c_total_score = JText::_('COM_JQ_SCORE_PENDING');
                }

                $rows[] = $tmp_rows[$i];
            }
        }

        $total = count($rows);
        $rows = array_slice($rows, $limitstart, $limit);
        $rows = array_merge($rows, array());

        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            if ($rows[$i]->c_pool) {
                $qids = str_replace('*', ",", $rows[$i]->q_chain);
                $total_score = 0;

                $total_score = JoomlaquizHelper::getTotalScore($qids, $rows[$i]->c_id);
                $rows[$i]->c_full_score = $total_score;
            }
        }

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        return array($rows, $pagination);
    }

    public function getQuizParams() {

        $my = JFactory::getUser();
        $database = JFactory::getDBO();
        $mainframe = JFactory::getApplication();

        $stu_id = intval(JFactory::getApplication()->input->get('id', 0));

        if (!$my->id || !$stu_id) {
            $mainframe->redirect('index.php?option=com_joomlaquiz&view=results');
            die;
        }

        $query = "SELECT * FROM #__quiz_r_student_quiz WHERE c_id = '{$stu_id}' AND c_student_id = '{$my->id}'";
        $database->SetQuery($query);
        $result_data = $database->LoadObjectList();

        $result_data = $result_data[0];
        $quiz_id = $result_data->c_quiz_id;

        $query = "SELECT a.*, b.template_name FROM #__quiz_t_quiz as a, #__quiz_templates as b WHERE a.c_id = '" . $quiz_id . "' and a.c_skin = b.id";
        $database->SetQuery($query);
        $quiz_params = $database->LoadObjectList();

        if (count($quiz_params)) {
            $quiz_params[0]->error = 0;
            $quiz_params[0]->message = '';
        }

        if (!isset($quiz_params[0]) || $quiz_params[0]->published != 1) {
            $quiz_params[0] = new stdClass;
            $quiz_params[0]->error = 1;
            $quiz_params[0]->message = '<p align="left">' . JText::_('COM_RESULTS_FOR_REGISTERED') . '</p>';
            return $quiz_params[0];
        }

        $doing_quiz = 1;
        $doing_pool = 1;

        $query = "SELECT c_pool FROM #__quiz_t_quiz WHERE c_id = '" . $quiz_id . "'";
        $database->SetQuery($query);
        if (!$database->loadResult()) {
            $doing_pool = 0;
        } else {
            $query = "SELECT q_count FROM #__quiz_pool WHERE q_id = '" . $quiz_id . "'";
            $database->SetQuery($query);
            if (!$database->loadResult()) {
                $doing_pool = 0;
            } else {
                $query = "SELECT COUNT(*) FROM #__quiz_t_question WHERE c_quiz_id = '0' AND published = 1";
                $database->SetQuery($query);
                if (!$database->loadResult()) {
                    $doing_pool = 0;
                }
            }
        }

        $query = "SELECT COUNT(*) FROM #__quiz_t_question WHERE c_quiz_id = '" . $quiz_id . "' AND published = 1";
        $database->SetQuery($query);
        if (!$database->LoadResult() && !$doing_pool) {
            $doing_quiz = -1;
        }

        if ($doing_quiz == 1) {
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_title, 'quiz_t_quiz', 'c_title', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_description, 'quiz_t_quiz', 'c_description', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_short_description, 'quiz_t_quiz', 'c_short_description', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_right_message, 'quiz_t_quiz', 'c_right_message', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_wrong_message, 'quiz_t_quiz', 'c_wrong_message', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_pass_message, 'quiz_t_quiz', 'c_pass_message', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_unpass_message, 'quiz_t_quiz', 'c_unpass_message', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_metadescr, 'quiz_t_quiz', 'c_metadescr', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_keywords, 'quiz_t_quiz', 'c_keywords', $quiz_params[0]->c_id);
            JoomlaquizHelper::JQ_GetJoomFish($quiz_params[0]->c_metatitle, 'quiz_t_quiz', 'c_metatitle', $quiz_params[0]->c_id);

            $session = JFactory::getSession();
            $session->set('quiz_lid', 0);
            $session->set('quiz_rel_id', 0);
            $session->set('quiz_package_id', 0);

            $query = "SELECT count(*) FROM #__quiz_t_question WHERE c_quiz_id = '" . $quiz_id . "' AND c_type = 4 AND published = 1";
            $database->SetQuery($query);
            $quiz_params[0]->if_dragdrop_exist = $database->LoadResult();
            $quiz_params[0]->c_description = JoomlaquizHelper::JQ_ShowText_WithFeatures($quiz_params[0]->c_description);
            $quiz_params[0]->is_attempts = 0;
            $quiz_params[0]->rel_id = 0;
            $quiz_params[0]->package_id = 0;
            $quiz_params[0]->lid = 0;
            $quiz_params[0]->force = 0;
            $quiz_params[0]->result_data = $result_data;

            return $quiz_params[0];
        } elseif ($doing_quiz == -1) {
            $quiz_params[0]->error = 1;
            $quiz_params[0]->message = '<p align="left">' . JText::_('COM_QUIZ_NOT_AVAILABLE') . '</p><br />';
            return $quiz_params[0];
        }
    }

}
