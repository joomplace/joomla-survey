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

class SurveyforceViewTemplate extends JViewLegacy
{
	protected $state;
	protected $form;

	public function display($tpl = null)
    {
		$this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$submenu = "TEMPLATE_CSS_EDITOR_ADMIN";
		SurveyforceHelper::showTitle($submenu, '('.$this->item->sf_display_name.')');
		SurveyforceHelper::getCSSJS();

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
		JToolBarHelper::apply('template.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('template.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CANCEL');
	}

}