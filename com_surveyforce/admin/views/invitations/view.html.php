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

class SurveyforceViewInvitations extends JViewLegacy
{
    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');        
        
        $submenu = "generate_invitations_admin";
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Check for errors.
        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        JToolBarHelper::custom('invitations.generate', 'save-new.png', 'save-new_f2.png', 'COM_SURVEYFORCE_GENERATE', false);        
        JToolBarHelper::cancel('invitations.cancel', 'JTOOLBAR_CANCEL');
    }

}