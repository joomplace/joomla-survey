<?php
/**
 * SurveyForce Component for Joomla 3
 * @package Joomlaquiz
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

class SurveyforceControllerAuthoring extends JControllerLegacy
{
	public function getModel($name = 'Authoring', $prefix = 'SurveyforceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function saveOrderAjax()
	{
		@ob_clean();
		header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: text/plain; charset=utf-8');

		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		$return = FALSE;
		$db = JFactory::getDbo();

		for ($i = 0, $n = count($pks); $i < $n; $i++)
		{
			$query = $db->getQuery(true)
				->update('#__survey_force_quests')
				->set('`ordering` = ' . $order[$i])
				->where('`id` = ' . $pks[$i]);
			$db->setQuery($query);
			try
			{
				$db->execute();
				$return = TRUE;
			}
			catch(RuntimeException $e)
			{
				$return = FALSE;
				break;
			}
		}

		echo ($return ? '1' : '0');
		jexit();
	}
}