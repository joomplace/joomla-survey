<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerSection extends JControllerAdmin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function cancel(){

		$surv_id = JFactory::getApplication()->input->get('surv_id');
		$this->setRedirect('index.php?option=com_surveyforce&view=questions&surv_id='.$surv_id);
	}

	public function save()
    {
       $surv_id = JFactory::getApplication()->input->get('surv_id');

       $model = $this->getModel('Section');
       $model->save();

       $this->setRedirect('index.php?option=com_surveyforce&view=questions&surv_id='.$surv_id, 'Item successfully saved');
    }

	public function apply()
    {
       $surv_id = JFactory::getApplication()->input->get('surv_id');

       $model = $this->getModel('Section');
       $section_id = $model->save();

       $this->setRedirect('index.php?option=com_surveyforce&view=section&id='.$section_id.'&surv_id='.$surv_id, 'Item successfully saved');
    }

}
