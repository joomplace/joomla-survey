<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Survey Force Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
 defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the SurveyForce Deluxe Component
 */
class SurveyforceViewNewquestion extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {
		$this->questions	= $this->getAllquestions();
                
		// Check for errors.
		if (!empty($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
				
		parent::display($tpl);
    }
	
	public function getAllquestions()
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__survey_force_qtypes`");
		$questions = $db->loadObjectList();
		return $questions;
	 }
}
?>