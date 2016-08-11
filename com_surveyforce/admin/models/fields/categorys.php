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

class JFormFieldCategorys extends JFormFieldList
{
	/**
	 * @var		string	The form field type.
	 */
	public $type = 'Categorys';

	protected function getOptions()
	{
		// Initialise variables.
		$options = array();

		$db = JFactory::getDbo();
		
		$query = "(SELECT '- Select category -' AS `text`, '- Select category -' AS `cat_id`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `sf_catname` AS `text`, `sf_catname` AS `cat_id`, `id` AS `value` FROM `#__survey_force_cats`)";
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		
		return $options; 
	} 
}