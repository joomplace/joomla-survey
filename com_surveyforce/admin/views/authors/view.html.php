<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceViewAuthors extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $sidebar;

	public function display($tpl = null)
	{
		$this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

		$jinput = JFactory::$application->input;
		$layout = $jinput->getCmd('layout', 'default');

		$submenu = 'list_of_authors_admin';

		if ($layout == 'users')
		{
			$submenu = 'users';
			SurveyforceHelper::addAuthorsSubmenu('authors_add');
		}
		else
		{
			SurveyforceHelper::addAuthorsSubmenu('authors');
		}

		SurveyforceHelper::showTitle($submenu);
		SurveyforceHelper::getCSSJS();
		$this->addToolBar();

		if ($layout == 'users')
		{
			$model = JModelList::getInstance('UsersList', 'SurveyforceModel');

			$items      = $model->getItems();
			$state      = $model->getState();
			$pagination = $model->getPagination();
		}
		else
		{
			$items      = $this->get('Items');
			$state      = $this->get('State');
			$pagination = $this->get('Pagination');
		}

        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

		$this->items      = $items;
		$this->state      = $state;
		$this->sidebar    = JHtmlSidebar::render();
		$this->pagination = $pagination;

		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		$jinput = JFactory::$application->input;
		$layout = $jinput->getCmd('layout', 'default');

		if ($layout == 'users')
		{
			JToolBarHelper::addNew('authors.add', 'COM_SURVEYFORCE_BUTTON_ADD', TRUE);
			JToolbarHelper::cancel();
		}
		else
		{
			JToolBarHelper::addNew('authors.usersList');
			JToolBarHelper::deleteList('', 'authors.delete');
		}
	}

	protected function getSortFields()
	{
		return array(
			'us.id'             => JText::_('JGRID_HEADING_ID'),
			'us.name'           => JText::_('COM_SURVEYFORCE_NAME'),
			'us.username'       => JText::_('COM_SURVEYFORCE_USERNAME'),
			'us.email'          => JText::_('COM_SURVEYFORCE_EMAIL'),
			'us.lastVisitDate'  => JText::_('COM_SURVEYFORCE_LAST_VISIT')
		);
	}
}