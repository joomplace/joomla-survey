<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerTemplate extends JControllerAdmin
{
	public function getModel($name = 'Template', $prefix = 'SurveyforceModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=templates', false));
	}

	public function save()
	{
		$this->apply();
	}

	public function apply()
	{
		$jForm = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
		$id = JFactory::getApplication()->input->get('id');

		$tmpl = JFactory::getApplication()->input->get('tmpl');
		if ($tmpl == 'component')
			$tmpl = '&tmpl=component';
		else
			$tmpl = '';

		$model = $this->getModel();

		jimport('joomla.filesystem.file');

		if ( $item = $model->getItem( $id ) ) {
			JFile::copy($item->filepath, $item->filepath.'.bkp');
			JFile::write($item->filepath, $jForm['sf_csscode']);
		}

		if ( JFactory::getApplication()->input->get('task') != 'save' )
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&id='.$id . $tmpl, false));
		else
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=templates', false));
	}
}
