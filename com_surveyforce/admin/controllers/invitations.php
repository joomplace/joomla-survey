<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerInvitations extends JControllerForm
{
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_surveyforce');
    }

	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_surveyforce&view=listusers');
	}

    public function generate()
    {
        $database = JFactory::getDbo();
        $database->setQuery("SELECT `params` FROM `#__extensions` WHERE `element` = 'com_surveyforce'");
        $mailPattern = json_decode($database->loadResult());
        $mailPattern = $mailPattern->sf_an_mail_pattern;
        $userJ = JFactory::getUser();
        $form = JFactory::getApplication()->input->get('jform',array(), 'ARRAY');
        $number = $form['count'] ;
		$surv_id = $form['survey'] ;

        $listuser = $this->getModel('Listuser','SurveyforceModel');
        $user = $this->getModel('User','SurveyforceModel');

        if ($number > 0 && $surv_id  > 0) {

            /*$query = "SELECT id "
                    ."\n FROM #__survey_force_listusers"
                    ."\n WHERE  listname = '_generated_users_' "
                    ;
            $database->setQuery( $query );
            $list_id = (int)$database->loadResult();
            if ( $list_id < 1) {*/
                $row = $listuser->getTable('Listuser');
                $row->listname = '_generated_users_'.  md5(time());
                $row->date_created = date( 'Y-m-d H:i:s' );
                $row->date_invited = date( 'Y-m-d H:i:s' );
                $row->survey_id = $surv_id?$surv_id:0;
                            $row->sf_author_id = $userJ->id;
                $row->store();
                $list_id = $row->id;
            //}

            $query = "SELECT MAX(id) "
                    ."\n FROM #__survey_force_users"
                    ."\n WHERE  list_id = '{$list_id}' "
                    ;
            $database->setQuery( $query );
            $max_id = (int)$database->loadResult();
            $max_id++;
            $csvdata = '';
            $dlm = ',';
            for ($i = 0; $i < $number; $i++) {
                $row_user = $user->getTable('User');
                $row_user->name = 'Name '.$max_id;
                $row_user->lastname = 'Lastname '.$max_id;
                $row_user->email = str_replace('*','email',$mailPattern);
                $row_user->list_id = $list_id;
                $row_user->store();

                $user_invite_num = md5(uniqid(rand().time(), true));

                $link = JUri::root() . "index.php?option=com_surveyforce&task=start_invited&survey=".$surv_id."&invite=".$user_invite_num;

                $query = "INSERT INTO `#__survey_force_invitations` (invite_num, user_id, inv_status) VALUES ('". $user_invite_num ."', '".$row_user->id."', 0)";
                $database->SetQuery($query);
                $database->execute();
                $user_invite_id = $database->insertid();

                $query = "UPDATE `#__survey_force_users` SET is_invited = '1', invite_id = '". $user_invite_id ."' WHERE id ='".$row_user->id."'";
                $database->SetQuery($query);
                $database->execute();
                $csvdata .= $row_user->name.$dlm.$row_user->lastname.$dlm.$row_user->email.$dlm.$link."\n";
                $max_id++;
            }
            @ob_end_clean();

            header("Content-type: application/csv");
            header("Content-Length: ".strlen(ltrim($csvdata)));
            header("Content-Disposition: inline; filename=invitations.csv");
            echo $csvdata;
            exit;
        }

        $this->setRedirect( "index.php?option=com_surveyforce&view=listusers" );
    }
}
