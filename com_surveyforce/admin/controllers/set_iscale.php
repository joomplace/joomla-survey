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
	public function cancel($key = null)
    {
        $qid = isset($_SESSION['qid']) ? $_SESSION['qid'] : 0;
		unset($_SESSION['qid']);
		$this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$qid);
	}

    public function save($key = null, $urlVar = null)
    {
        $qid = 0;
        if(isset($_SESSION['qid'])) {
            $qid = $_SESSION['qid'];
            unset($_SESSION['qid']);
        }

        $this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$qid);
	}
}