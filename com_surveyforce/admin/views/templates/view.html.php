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

class SurveyforceViewTemplates extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
    {
        $submenu = 'templates';
        SurveyforceHelper::addConfigurationSubmenu($submenu);
        
        SurveyforceHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->addToolbar();

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

    protected function addToolBar()
    {
        JToolBarHelper::deleteList('', 'templates.delete');
		JToolBarHelper::custom('templates.install_show', 'upload', 'upload', JText::_('Install'), false);
    }

}