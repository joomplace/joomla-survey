<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelPassed_survey extends JModelItem
{

    protected $item;

    public function __construct()
    {
        $this->database = JFactory::getDbo();
        parent::__construct();
    }

    public function getItem($user_id = null)
    {
        $database = JFactory::getDbo();

        if (empty($user_id)) {
            $user_id = JFactory::getUser()->id;
        }

        $query = "SELECT * FROM `#__survey_force_survs` s LEFT JOIN `#__survey_force_user_starts` u ON s.id = u.survey_id WHERE u.user_id = $user_id GROUP BY s.id";
        $database->setQuery($query);
        $rows = $database->loadObjectList();

        if(empty($this->item)) {
            $this->item = new stdClass();
        }
        if(empty($this->item->surveys)) {
            $this->item->surveys = array();
        }

        if(is_array($rows) && count($rows)) {
            foreach ($rows as $i => $row) {
                $row->surv_short_descr = trim(strip_tags($rows[$i]->surv_short_descr));
                $this->item->surveys[$i] = $row;
            }
        }

        return $this->item;
    }

}