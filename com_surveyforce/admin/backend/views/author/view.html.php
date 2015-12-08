<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceViewAuthor extends JViewLegacy
{

	protected $state;
	protected $item;
	protected $form;

	public function display($tpl = null)
	{
		$this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

		$submenu = "category";
		SurveyforceHelper::showTitle($submenu);
		SurveyforceHelper::getCSSJS();

		//$this->settings = JComponentHelper::getParams('com_surveyforce');

		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->item->date_added = ($this->item->date_added) ? $this->item->date_added : JFactory::getDate();


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
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
		JToolBarHelper::apply('category.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('category.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('category.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('category.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}

}