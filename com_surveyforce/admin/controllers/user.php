<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerUser extends JControllerForm
{
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
		$tmpl   = $this->input->get('tmpl');
        $layout = $this->input->get('layout', 'edit', 'string');
        $append = '';

        // Setup redirect info.
        if ($tmpl) {
            $append .= '&tmpl=' . $tmpl;
        }

        if ($layout) {
            $append .= '&layout=' . $layout;
        }

        if ($recordId) {
            $append .= '&' . $urlVar . '=' . $recordId;
        }

        if (JFactory::getApplication()->input->get('id')) {
			$append .= '&list_id='.JFactory::getApplication()->input->get('id');
		}	
		
		if($recordId) {
			$append .= '&id='.$recordId;
		}
		
		return $append;
	}
	
    public function add()
    {
        parent::add();
    }

    public function cancel($key = null)
    {
        $jform = JFactory::getApplication()->input->get('jform', array(),'array');
        $listid = !empty($jform['list_id']) ? $jform['list_id'] : 0;

        $session = JFactory::getSession();
        if(!empty($session->get('list_id'))) {
            $session->clear('list_id');
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=users&id='.$listid, false));
    }

    public function save($key = null, $urlVar = null)
    {
    	parent::save($key, $urlVar);
		
		$input = JFactory::getApplication()->input;
		$jform = $input->get('jform', array(),'array');
        $listid = !empty($jform['list_id']) ? $jform['list_id'] : 0;
        $task = $input->getCmd('task');

        $session = JFactory::getSession();
        if(!empty($session->get('list_id'))) {
            $session->clear('list_id');
        }
		
		if($task != 'save2new') {
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=users&id='.$listid, false));
		}
    }
}
