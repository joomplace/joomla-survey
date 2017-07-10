<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
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
 * Form Field class for the IScale.
 *
 */
class JFormFieldIscale extends JFormFieldList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Iscale';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();

		$db		= JFactory::getDbo();
		
		$query	= "(SELECT '- Select scale -' AS `text`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `iscale_name` AS `text`, `id` AS `value` FROM `#__survey_force_iscales` WHERE `id` > 0)";
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		
		return $options; 
	} 
}