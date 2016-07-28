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

class SurveyforceViewInsert_survey extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {

    	$eName	= JRequest::getVar('e_name');
    	$eName	= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
    	$this->eName = $eName;

        $items 		= $this->get('Items');
    	$pagination = $this->get('Pagination');
    	$state		= $this->get('State');
    	        
        if (count($errors = $this->get('Errors'))) 
        {
                JError::raiseError(500, implode('<br />', $errors));
                return false;
        }
	      
      	$this->items = $items;
       	$this->pagination = $pagination;
    	$this->state = $state;

        parent::display($tpl);
    }

}