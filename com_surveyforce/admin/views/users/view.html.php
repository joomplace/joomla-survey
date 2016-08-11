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

class SurveyforceViewUsers extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'users';
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
        $this->listid = JFactory::getApplication()->input->get('id');
        $_SESSION['listid'] = $this->listid;

        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {

        JToolBarHelper::addNew('user.add');
        JToolBarHelper::editList('user.edit');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'users.delete');
        JToolBarHelper::cancel('users.cancel', 'Cancel');

    }

    protected function getSortFields() {

        return array(
            'name' => JText::_('COM_SURVEYFORCE_NAME'),           
            'lastname' => JText::_('COM_SURVEYFORCE_LASTNAME'),
            'email' => JText::_('COM_SURVEYFORCE_EMAIL'),
            'id' => JText::_('JGRID_HEADING_ID')
        );

    }

}