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

class SurveyforceViewUser extends JViewLegacy
{
    protected $items;
    protected $state;
    protected $form;

    function display($tpl = null)
    {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'user';
        SurveyforceHelper::showTitle($submenu);      
        
        $item = $this->get('Item');
        $form = $this->get('Form');
        $state = $this->get('State');
        $lists = $this->get('Users');

        $this->users = $lists['users'];
        $this->reg_users = $lists['reg_users'];

        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $this->item = $item;
        $this->form = $form;
        $this->state = $state;
        $this->listid = $_SESSION['listid'];

        $this->addToolBar();   
        
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);

        JToolBarHelper::apply('user.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('user.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('user.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);        
        JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CANCEL');
    }
}