<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Survey.
 *
 */
class JFormFieldSurvey extends JFormFieldList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Survey';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();

		$query	= "(SELECT '- Select survey -' AS `text`, '0' AS `surv_id`, '0' AS `value`, '0' AS `sf_survey` FROM `#__users` LIMIT 0,1) UNION (SELECT `sf_name` AS `text`, `id` AS `surv_id`, `id` AS `value`, `id` AS `sf_survey` FROM `#__survey_force_survs` WHERE `id` > 0)";
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
            JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}
		
		return $options; 
	} 
}