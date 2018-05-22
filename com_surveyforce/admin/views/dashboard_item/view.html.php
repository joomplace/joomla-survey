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

class SurveyforceViewDashboard_item extends JViewLegacy {

    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null) {

        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage($this->get('Errors'), 'error');
            return false;
        }

        $isNew = $this->item->id == 0;
        JToolBarHelper::title(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_DASHBOARD_ITEM_EDITING'));
        $this->addToolBar();

        parent::display($tpl);
    }

    protected function addToolBar() {
        $user = JFactory::getUser();
        JToolBarHelper::apply('dashboard_item.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('dashboard_item.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('dashboard_item.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);

        JToolBarHelper::cancel('dashboard_item.cancel', 'JTOOLBAR_CANCEL');
    }

}
