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

class SurveyforceViewSurveys extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'surveys';

        SurveyforceHelper::addSurveysSubmenu($submenu);
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
        JToolBarHelper::addNew('survey.add');
        JToolBarHelper::editList('survey.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('surveys.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('surveys.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::deleteList('', 'surveys.delete');
		JToolBarHelper::spacer(20);
		JToolBarHelper::custom('surveys.preview', 'eye-open.png', 'eye-open_f2.png', 'COM_SURVEYFORCE_PREVIEW', true);
    }

    protected function getSortFields() {
        return array(
            'sf_name' => JText::_('COM_SURVEYFORCE_NAME'),
            'sf_cat' => JText::_('COM_SF_CATEGORY'),
			'sf_date_started' => JText::_('COM_SURVEYFORCE_STARTED_ON'),
            'sf_date_expired' => JText::_('COM_SURVEYFORCE_EXPIRED_ON'),
            'sf_author' => JText::_('COM_SURVEYFORCE_AUTHOR'),
            'sf_public' => JText::_('COM_SURVEYFORCE_PUBLIC'),
            'sf_auto_pb' => JText::_('COM_SURVEYFORCE_AUTO_PAGE_BREAK'),
            'sf_invite' => JText::_('COM_SURVEYFORCE_FOR_INVITED'),
            'sf_reg' => JText::_('COM_SF_FOR_REG'),
            'published' => JText::_('JPUBLISHED'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

}