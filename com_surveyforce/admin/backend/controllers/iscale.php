<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class SurveyforceControllerIscale extends JControllerForm {

	public function __construct($config)
	{
		$quest_id = JFactory::getApplication()->input->get('quest_id');
		if ( !empty($quest_id) )
			JFactory::getSession()->set('iscale_quest_id', $quest_id);

		parent::__construct($config);
	}

    protected function allowEdit($data = array(), $key = 'id') {

        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_surveyforce');
    }

	public function save()
	{
		parent::save();
		if ( JFactory::getSession()->get('iscale_quest_id') )
		{
			JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.JFactory::getSession()->get('iscale_quest_id'));
		}
	}

	public function cancel()
	{
		if ( JFactory::getSession()->get('iscale_quest_id') )
		{
			JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.JFactory::getSession()->get('iscale_quest_id'));
		}
		parent::cancel();
	}

}
