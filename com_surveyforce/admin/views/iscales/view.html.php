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

class SurveyforceViewIscales extends JViewLegacy {

     protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'importance_scales';
        SurveyforceHelper::addSurveysSubmenu('iscales');

        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();        
        $this->addToolBar();


        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
       
        $this->items = $items;
        $this->pagination = $pagination;
        $this->state = $state;
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        JToolBarHelper::addNew('iscale.add');
        JToolBarHelper::editList('iscale.edit');
       
        JToolBarHelper::deleteList('', 'iscales.delete');
    }

    protected function getSortFields() {
        return array(
            'scale_name' => JText::_('COM_SURVEYFORCE_NAME'),           
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

}