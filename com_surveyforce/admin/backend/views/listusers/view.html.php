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

class SurveyforceViewListusers extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'users';

        SurveyforceHelper::addUserlistSubmenu('listusers');
        SurveyforceHelper::showTitle($submenu);  
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

        JToolBarHelper::addNew('listuser.add');
        JToolBarHelper::editList('listuser.edit');        
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'listusers.delete');
        JToolBarHelper::divider();
        JToolBarHelper::custom('listusers.invite_users', 'featured.png', 'featured_f2.png', 'COM_SURVEYFORCE_INVITE_USERS', false);
        JToolBarHelper::custom('listusers.remind_users', 'featured.png', 'featured_f2.png', 'COM_SURVEYFORCE_REMIND_USERS', false);
    }

    protected function getSortFields() {
        return array(
            'survey_id' => JText::_('COM_SURVEYFORCE_NAME'),           
            'listname' => JText::_('JPUBLISHED'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }

}