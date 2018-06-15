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

class SurveyforceViewReports extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

//TODO: Choose from question

    function display($tpl = null) {
        $submenu = 'reports';

        SurveyforceHelper::addReportsSubmenu($submenu);
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->addToolbar();

        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');

        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage($this->get('Errors'), 'error');
            return false;
        }

        $this->items = $items;
        $this->pagination = $pagination;
        $this->state = $state;
        $this->survey_names = $this->get('Survey_names');
        $this->usertype = $this->get('Usertype');
        $this->sf_status = $this->get('Sf_status');
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
    }

    protected function addToolBar() {

        $canDo = JHelperContent::getActions('com_surveyforce', 'component');
        if ($canDo->get('core.manage')) {
            JToolBarHelper::custom('reports.pdf_sum', 'print.png', 'print_f2.png', 'PDF (sum)', false);
            JToolBarHelper::custom('reports.pdf_sum_perc', 'print.png', 'print_f2.png', 'PDF (sum %)', false);
            JToolBarHelper::custom('reports.csv_sum', 'print.png', 'print_f2.png', 'CSV (sum)', false);
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('COM_SURVEYFORCE_DELETE_REPORT'), 'reports.delete');
            JToolbar::getInstance('toolbar')->appendButton('Link', 'trash', JText::_('COM_SURVEYFORCE_CLEANUP_BUTTON'), 'index.php?option=com_surveyforce&task=cleanup.display');
        }
    }

    protected function getSortFields() {
        return array(
            'sf_ust.is_complete' => JText::_('COM_SURVEYFORCE_STATUS'),
            'sf_ust.usertype' => JText::_('COM_SURVEYFORCE_USERTYPE'),
            'sf_u.survey_name' => JText::_('COM_SURVEYFORCE_SURVEY'),
        );
    }

}