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

class SurveyforceControllerInvite_users extends JControllerForm
{

    public function invitation_start()
    {

        $database = JFactory::getDBO();
        $app = JFactory::getApplication();

        $email_id = $app->input->get('email');
        $list_id = $app->input->get('list');

        $component_params = JComponentHelper::getParams('com_surveyforce');

        $mail_pause = intval($component_params->get('sf_mail_pause'));
        $mail_count = intval($component_params->get('sf_mail_count'));
        $mail_max = intval($component_params->get('sf_mail_maximum'));

        ignore_user_abort(false); // STOP script if User press 'STOP' button
        @set_time_limit(0);
        @ob_end_clean();
        @ob_start();
        echo "<script>function getObj_frame(name) {"
        . " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
        . "	else if (parent.document.all) { return parent.document.all[name]; }"
        . "	else if (parent.document.layers) { return parent.document.layers[name]; }}</script>";

        $query = "SELECT * FROM `#__survey_force_emails` WHERE `id` ='" . $email_id . "'";
        $database->SetQuery($query);
        $Send_email = $database->loadObjectList();

        $query = "SELECT count(*) FROM `#__survey_force_users` WHERE `list_id`= '" . $list_id . "' AND `is_invited` = 0 ";
        $database->SetQuery($query);
        $is_invited = intval($database->LoadResult());

        $query = "SELECT survey_id FROM `#__survey_force_listusers` WHERE `id` = '" . $list_id . "'";
        $database->SetQuery($query);
        $survey_id = intval($database->LoadResult());

        if ($is_invited < 1) {
            echo "<script>var div_log = getObj_frame('div_invite_log_txt'); if (div_log) {"
            . "div_log.innerHTML = '" . JText::_('COM_SURVEYFORCE_ALL_USERS_FROM_THE_FOLLOWING_LIST') . "';"
            . "}</script>";
            @flush();
            @ob_end_flush();
            die();
        }

        $query = "SELECT count(*) FROM `#__survey_force_users` WHERE `list_id` ='" . $list_id . "' and `is_invited` = '0'";
        $database->SetQuery($query);
        $Users_count = $database->LoadResult();
        $query = "SELECT * FROM `#__survey_force_users` WHERE `list_id` ='" . $list_id . "' and `is_invited` = '0'";
        $database->SetQuery($query);
        $UsersList = $database->loadObjectList();
        $Users_to_invite = count($UsersList);

        $config = new JConfig();
        $mailfrom = !empty($config->mailfrom) ? $config->mailfrom: 'noreply@'.$_SERVER['SERVER_NAME'];
        $fromname = !empty($config->fromname) ? $config->fromname: 'SurveyForce';

        $message = $Send_email[0]->email_body;
        $subject = stripslashes($Send_email[0]->email_subject);
        $email_reply = $Send_email[0]->email_reply ? $Send_email[0]->email_reply : $mailfrom;
        $ii = 1;

        $query = "UPDATE `#__survey_force_listusers` SET `is_invited` = '2', `date_invited` = '" . date('Y-m-d H:i:s') . "' WHERE `id` ='" . $list_id . "'";
        $database->SetQuery($query);
        $database->execute();
        $send_count = 0;
        $counter = 0;
        $sendError = 0;
        foreach ($UsersList as $user_row) {
            if ($mail_max && $send_count == $mail_max) {
                echo "<script>var st_but = getObj_frame('Start_button');"
                . "var div_log_txt = getObj_frame('div_invite_log_txt');"
                . "st_but.value = 'Resume';"
                . " if (div_log_txt) {"
                . "div_log_txt.innerHTML = '" . JText::_('COM_SURVEYFORCE_MAXIMUM_NUMBER_MAILS_EXCEED') . "';"
                . "}"
                . "</script>";
                @flush();
                @ob_flush();
                die;
            }
            $user_invite_num = md5(uniqid(rand(), true));
            $link = ' <a href="' . JURI::root() . str_replace('administrator/', '', trim(JRoute::_("index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num), "/")) . '">' . JText::_("COM_SURVEYFORCE_INVITE_LINK_TEXT") . '</a>';
            $user_name = ' ' . $user_row->name . ' ' . $user_row->lastname . ' ';
            $message_user = str_replace('#link#', $link, $message);
            $message_user = str_replace('#name#', $user_name, $message_user);

            $query = "INSERT INTO `#__survey_force_invitations` (`invite_num`, `user_id`, `inv_status`) VALUES ('" . $user_invite_num . "', '" . $user_row->id . "', 0)";
            $database->SetQuery($query);
            $database->execute();
            $user_invite_id = $database->insertid();

            $jmail = JFactory::getMailer();
            $sendResult = $jmail->sendMail($email_reply, $fromname, $user_row->email, $subject, $message_user, 1); //1 - in HTML mode
            if(!$sendResult) {
                $sendError++;
            }

            $query = "UPDATE `#__survey_force_users` SET `is_invited` = '1', `invite_id` = '" . $user_invite_id . "' WHERE `id` ='" . $user_row->id . "'";
            $database->SetQuery($query);
            $database->execute();
            $error_message = $sendError > 0 ? ' <span style="color:red;">'.$sendError.' '.JText::_('COM_SURVEYFORCE_USERS_INVITED_ERRORS').'</span>' : '';
            if (($mail_pause && $mail_count) && $counter == ($mail_count - 1) && $Users_count != $ii) {

                $counter = -1;
                for ($jj = $mail_pause; $jj > 0; $jj--) {

                    echo "<script>var div_log = getObj_frame('div_invite_log');"
                    . "var div_log_txt = getObj_frame('div_invite_log_txt');"
                    . " if (div_log) {"
                    . "div_log.innerHTML = '" . intval(($ii - $Users_to_invite + $Users_count) * 100 / $Users_count) . "%';"
                    . "div_log.style.width = '" . intval(($ii - $Users_to_invite + $Users_count) * 600 / $Users_count) . "px';"
                    . "}"
                    . " if (div_log_txt) {"
                    . "div_log_txt.innerHTML =  '" . ($ii - $Users_to_invite + $Users_count) . ' ' . JText::_('COM_SURVEYFORCE_USERS_INVITED_PAUSE') . " $jj " . JText::_('COM_SURVEYFORCE_SECONDS') .$error_message. "';"
                    . "}"
                    . "</script>";
                    @flush();
                    @ob_flush();
                    sleep(1);
                }
            } else {
                echo "<script>var div_log = getObj_frame('div_invite_log');"
                . "var div_log_txt = getObj_frame('div_invite_log_txt');"
                . " if (div_log) {"
                . "div_log.innerHTML = '" . intval(($ii - $Users_to_invite + $Users_count) * 100 / $Users_count) . "%';"
                . "div_log.style.width = '" . intval(($ii - $Users_to_invite + $Users_count) * 600 / $Users_count) . "px';"
                . "}"
                . " if (div_log_txt) {"
                . "div_log_txt.innerHTML = '" . ($ii - $Users_to_invite + $Users_count) . ' ' . JText::_('COM_SURVEYFORCE_USERS_INVITED') .$error_message. "';"
                . "}"
                . "</script>";
                @flush();
                @ob_flush();
            }
            $ii++;
            $send_count++;
            $counter++;
            sleep(1);
        }
        $query = "UPDATE `#__survey_force_listusers` SET `is_invited` = '1' WHERE `id` ='" . $list_id . "'";
        $database->SetQuery($query);
        $database->execute();
        echo "<script>var div_log = getObj_frame('div_invite_log'); if (div_log) {"
        . "div_log.innerHTML = '100%';"
        . "div_log.style.width = '600px';"
        . "}</script>";
        @flush();
        @ob_end_flush();

        die();
    }

    public function cancel()
    {

        $this->setRedirect('index.php?option=com_surveyforce&view=listusers');
    }
}
