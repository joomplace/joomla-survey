<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewReport extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $surveys;
    protected $ordering_list;

    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $submenu = 'report_admin';
        SurveyforceHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

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
		JToolBarHelper::custom('report.pdf', 'print.png', 'print_f2.png', 'PDF', false);
        JToolBarHelper::back();
		JToolBarHelper::spacer(20);
		JToolBarHelper::deleteList(JText::_('COM_SURVEYFORCE_DELETE_REPORT'), 'reports.delete');
    }

}
