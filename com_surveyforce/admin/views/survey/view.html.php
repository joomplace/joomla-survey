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

class SurveyforceViewSurvey extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        
        $submenu = "survey";
        SurveyforceHelper::showTitle('SURVEY_ADMIN');
        
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
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
        JToolBarHelper::apply('survey.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('survey.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('survey.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::custom('survey.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CANCEL');
        
    }

}