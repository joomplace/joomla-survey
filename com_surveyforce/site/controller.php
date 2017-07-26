<?php

/**
 * SurveyForce Delux Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy
{
    /**
     * @param bool $cachable
     * @param array $urlparams
     * @internal in my opinion this method may contains only call parent display method. All the rest is superfluous
     * @return JControllerLegacy|void
     */
    public function display($cachable = false, $urlparams = array())
    {
        $view   = $this->input->get('view');
        $task   = $this->input->get('task');
        $itemid = $this->input->get('Itemid', 0, 'INT');

        if (empty($itemid)) {
            $itemid = !empty($this->input->post->get('Itemid', 0, 'INT')) ? $this->input->post->get('Itemid', 0, 'INT') : $this->input->get->get('Itemid', 0, 'INT');
        }

        $this->input->set('Itemid', $itemid);

        if ($view == 'authoring' && !isset($_SESSION['view'])) {
            $_SESSION['view'] = 'authoring';
        } elseif ($view != 'authoring' && (isset($_SESSION['view']) && $_SESSION['view'] != 'authoring')) {
            unset($_SESSION['view']);
        }

        if (isset($_SESSION['view']) && $_SESSION['view'] == 'authoring' && $view != 'survey' && $view != 'passed_survey' && $view != 'category') {
            $this->input->set('view', 'authoring');
        } else {
            if ($view != 'category' && $view != 'insert_survey' && $view != 'passed_survey') $this->input->set('view', 'survey');
        }

        if ($task == 'start_invited') {
            $this->input->set('view', 'survey');
        }

        if ($task == 'view_users') {
            $this->input->set('view', 'authoring');
        }

        parent::display($cachable);
    }
}