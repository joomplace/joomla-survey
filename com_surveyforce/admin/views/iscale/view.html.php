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

class SurveyforceViewIscale extends JViewLegacy {

    protected $state;
    protected $fields;
    protected $item;
    protected $form;

    public function display($tpl = null) {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        if ($this->item->id){
            $this->fields = $this->get('Fields');
            $submenu = "edit_importance_scale";
        }
        else {
            $submenu = "new_importance_scale";
        }
        
        $this->edit_title = SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();

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
        JToolBarHelper::apply('iscale.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('iscale.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('iscale.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::custom('iscale.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        JToolBarHelper::cancel('iscale.cancel', 'JTOOLBAR_CANCEL');
        JToolBarHelper::divider();
        JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
    }

}