<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Survey Force Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class SurveyforceTableIscale extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__survey_force_iscales', 'id', $db);
	}

    public function store($updateNulls = false) {
        return parent::store($updateNulls);
    }
}