<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
    public function __construct(&$subject, $config)
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
