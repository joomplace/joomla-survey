<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelCleanup extends JModelList {

    public function results_cleanup($data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach($data['surveys'] as $survey){
            $survey = $db->q($survey);
        }
        $surveys = implode(',', $data['surveys']);

        $query->select($db->qn('start_id'))
            ->from($db->qn('#__survey_force_user_answers'))
            ->where($db->qn('survey_id') . ' IN (' . $surveys . ')');
        if(isset($data['date_start']) && $data['date_start']){
            $query->where($db->qn('sf_time') .'>='. $db->q($data['date_start'] . ' 00:00:00'));
        }
        if(isset($data['date_end']) && $data['date_end']){
            $query->where($db->qn('sf_time') .'<='. $db->q($data['date_end'] . ' 23:59:59'));
        }
        $query->group('start_id');
        $db->setQuery($query);
        $start_ids = $db->loadColumn();

        if(!$start_ids){
            return false;
        }

        foreach ($start_ids as $start_id) {
            $start_id = $db->q($start_id);
        }
        $start_ids = implode(',', $start_ids);

        // #__survey_force_user_answers
        $query->clear();
        $conditions = array(
            $db->qn('start_id') . ' IN (' . $start_ids . ')'
        );
        $query->delete($db->qn('#__survey_force_user_answers'))->where($conditions);
        $db->setQuery($query)->execute();

        //#__survey_force_user_answers_imp
        $query->clear();
        $conditions = array(
            $db->qn('start_id') . ' IN (' . $start_ids . ')'
        );
        $query->delete($db->qn('#__survey_force_user_answers_imp'))->where($conditions);
        $db->setQuery($query)->execute();

        //#__survey_force_user_ans_txt
        $query->clear();
        $conditions = array(
            $db->qn('start_id') . ' IN (' . $start_ids . ')'
        );
        $query->delete($db->qn('#__survey_force_user_ans_txt'))->where($conditions);
        $db->setQuery($query)->execute();

        //#__survey_force_user_chain
        $query->clear();
        $conditions = array(
            $db->qn('start_id') . ' IN (' . $start_ids . ')'
        );
        $query->delete($db->qn('#__survey_force_user_chain'))->where($conditions);
        $db->setQuery($query)->execute();

        //#__survey_force_user_starts
        $query->clear();
        $conditions = array(
            $db->qn('id') . ' IN (' . $start_ids . ')'
        );
        $query->delete($db->qn('#__survey_force_user_starts'))->where($conditions);
        $db->setQuery($query)->execute();

        return true;
    }

}