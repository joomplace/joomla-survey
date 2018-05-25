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

class SurveyforceViewListuser extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        
        $submenu = "LIST_OF_USERS_ADMIN";
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        if(JFactory::getApplication()->input->getInt('id')) {
            $tpl = 'edit';
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage($this->get('Errors'), 'error');
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {

        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        JToolBarHelper::apply('listuser.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('listuser.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('listuser.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::custom('listuser.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        JToolBarHelper::cancel('listuser.cancel', 'JTOOLBAR_CANCEL');
        
    }

}