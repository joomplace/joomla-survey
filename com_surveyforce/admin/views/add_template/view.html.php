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

class SurveyforceViewAdd_template extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    public function display($tpl = null) {
		$submenu = 'UPLOAD_TEMPLATE_PACKAGE';

		SurveyforceHelper::showTitle($submenu);
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->state = $this->get('State');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
		JToolBarHelper::custom('add_template.add', 'upload', 'upload', JText::_('Install'), false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel('add_template.cancel', 'JTOOLBAR_CANCEL');
    }

}