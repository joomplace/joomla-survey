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

class SurveyforceViewCategories extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'categories';
        
        SurveyforceHelper::addCategoriesSubmenu($submenu);
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();        
        $this->addToolBar();


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
        $this->sidebar = JHtmlSidebar::render();
        
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        JToolBarHelper::addNew('category.add');
        JToolBarHelper::editList('category.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('categories.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('categories.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'categories.delete');
    }

    protected function getSortFields() {
        return array(
            'sf_catname' => JText::_('COM_SURVEYFORCE_NAME'),           
            'published' => JText::_('JPUBLISHED'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

}