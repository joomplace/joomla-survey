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

class SurveyforceViewSection extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        
        $submenu = "ADD NEW SECTION";
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();

        $this->surv_id = JFactory::getApplication()->input->get('surv_id');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->questions = $this->get('Questions');
            
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
        $user       = JFactory::getUser();
        $isNew      = ($this->item->id == 0);

        JToolBarHelper::apply('section.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('section.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('section.cancel', 'JTOOLBAR_CANCEL');
        
    }

}