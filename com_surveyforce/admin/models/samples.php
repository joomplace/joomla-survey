<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelSamples extends JModelList {

    public function __construct($config = array()) {

        parent::__construct($config);
    }

    protected function getListQuery() {
        
    }

    public function getSample1() {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT id FROM #__survey_force_survs WHERE `sf_name`='.$db->quote('Customer Service Satisfaction Survey'));
        $sample = $db->loadObject();

        if ($sample)
            return $sample->id;
        else
            return 0;
    }

    public function getSample2() {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT id FROM #__survey_force_survs WHERE `sf_name`='.$db->quote('Sample Branching Survey'));
        $sample = $db->loadObject();

        if ($sample)
            return $sample->id;
        else
            return 0;
    }

}
