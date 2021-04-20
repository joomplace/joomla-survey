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

class SurveyforceViewEmails extends JViewLegacy
{

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
    {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'emails';

        SurveyforceHelper::addConfigurationSubmenu($submenu);
        SurveyforceHelper::showTitle($submenu);
        $this->addToolBar();

        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');

        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
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
    protected function addToolBar()
    {
        JToolBarHelper::addNew('email.add');
        JToolBarHelper::editList('email.edit');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'emails.delete');
    }

    protected function getSortFields()
    {
        return array(
            'email_subject' => JText::_('COM_SURVEYFORCE_SUBJECT'),
            'email_body' => JText::_('COM_SURVEYFORCE_BODY'),
            'email_reply' => JText::_('COM_SURVEYFORCE_REPLY_TO'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

}