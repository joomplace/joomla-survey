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

class SurveyforceViewSamples extends JViewLegacy {

    
    function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'samples';
        SurveyforceHelper::showTitle($submenu);       
        SurveyforceHelper::getCSSJS();      
        
        
        $this->is_sample2 = $this->get('Sample2');
        $this->is_sample1 = $this->get('Sample1');


        

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
       
       

        parent::display($tpl);
    }
  
   

}