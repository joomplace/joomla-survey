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

class SurveyforceViewDashboard_Items extends JViewLegacy {

    protected $items = null;

    function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->items = $this->get('Items');
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addToolBar();
        $this->setDocument();

        parent::display($tpl);
    }

    protected function addToolBar() {

        JToolBarHelper::title(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_MANAGER_DASHBOARD_ITEMS'), 'dashboard items');

        JToolBarHelper::addNew('dashboard_items.add');


        JToolBarHelper::editList('dashboard_items.edit', 'JTOOLBAR_EDIT');
        JToolBarHelper::divider();


        JToolBarHelper::deleteList('', 'dashboard_items.delete', 'JTOOLBAR_DELETE');
    }

    protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_MANAGER_DASHBOARD_ITEMS'));
    }

}
