<?php

/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelSurvey extends JModelAdmin {

    protected $context = 'com_surveyforce';

    public function getTable($type = 'Survey', $prefix = 'SurveyforceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_surveyforce.survey', 'survey', array('control' => 'jform', 'load_data' => false));
        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();
	    $item->sf_author = json_decode($item->sf_author);
        $form->bind($item);
		
	
        
		
		
		return $form;
    }

}
