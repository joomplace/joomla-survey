<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerAuthor extends JControllerForm
{

	public function add()
	{
		$database = JFactory::getDbo();
		$query = "SELECT user_id FROM #__survey_force_authors ";
		$database->setQuery($query);
		$authors = @array_merge(array('0' => 0), $database->loadRowList());


		// get the subset (based on limits) of required records
		$query = "SELECT id "
			. " FROM #__users "
			. " WHERE id NOT IN (" . implode(',', $authors) . ") AND name <> 'Super User'";
		$database->setQuery($query);
		$new_authors = $database->loadObjectList();

		$new_id = '';
		if ( count($new_authors) ) {
			foreach ($new_authors as $author)
				$new_id .= '(' . $author->id . '),';

			$new_id = mb_substr($new_id, 0, strrpos($new_id, ','));
			$new_id .= ';';

			$query = 'INSERT INTO `#__survey_force_authors` (`user_id`) VALUES ' . $new_id;
			$database->setQuery($query);
			$database->execute();
		}
	}

}
