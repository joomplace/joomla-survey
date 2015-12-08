<?php
/**
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Editor Survey buton
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonSurveyforce extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgButtonSurveyforce(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */ 
	function onDisplay($name)
	{
		$mainframe = JFactory::getApplication();		
		
		$doc 		= JFactory::getDocument();
		$template 	= $mainframe->getTemplate();

		$link = 'index.php?option=com_surveyforce&amp;view=insert_survey&amp;tmpl=component&amp;e_name='.$name;

		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->modal = true;
		$button->link = $link;
		$button->class = 'btn';
		$button->text = 'Survey';
		$button->name = 'save-new';
		$button->options = "{handler: 'iframe', size: {x: 570, y: 500}}";

		return $button;
	}
}
