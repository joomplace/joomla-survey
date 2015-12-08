<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package   Survey Force Deluxe
 * @author    JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerAuthors extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Authors', $prefix = 'SurveyforceModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function usersList()
	{
		$this->setRedirect('index.php?option=com_surveyforce&view=authors&layout=users');
	}

	public function add()
	{
		$app = JFactory::$application;

		$cid = $this->input->get('cid', array(), 'ARRAY');

		if (!count($cid))
		{
			$this->setMessage('Please, select a user, which you want to add as Author', 'NOTICE');
			$this->setRedirect('index.php?option=com_surveyforce&view=authors&layout=users');
			return false;
		}

		$model = $this->getModel();

		if ($model->add($cid))
		{
			$this->setMessage(count($cid) . ' ' . JText::_('COM_SURVEYFORCE_SELECTED_USERS_ADDED'));
			$this->setRedirect('index.php?option=com_surveyforce&view=authors');
		}
		else
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$this->setRedirect('index.php?option=com_surveyforce&view=authors&layout=users');
		}

		return true;
	}
}