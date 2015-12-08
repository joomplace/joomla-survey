<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerSet_iscale extends JControllerForm
{

	public function cancel(){
		
		$qid = $_SESSION['qid'];
		unset($_SESSION['qid']);

		$this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$qid);

	}

    public function save(){
        
        unset($_SESSION['qid']);
    	$data = $_POST;
 		
    	

        $this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$quest_id);
	    
	}
}
