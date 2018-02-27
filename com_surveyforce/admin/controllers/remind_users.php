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

class SurveyforceControllerRemind_users extends JControllerForm
{

    public function remind_start()
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


        $query = "SELECT * FROM `#__survey_force_emails` WHERE id ='" . $email_id . "'";
        $database->SetQuery($query);
        $Send_email = $database->loadObjectList();

        $query = "SELECT is_invited, survey_id FROM `#__survey_force_listusers` WHERE `id` = '" . $list_id . "'";
        $database->SetQuery($query);
        $list_data = $database->loadObjectList();

        $is_invited = $list_data[0]->is_invited;
        $survey_id = $list_data[0]->survey_id;

        $query = "SELECT count(a.id) FROM `#__survey_force_users` as a, `#__survey_force_invitations` as b WHERE a.`list_id` ='" . $list_id . "' and a.is_invited = 1 and a.invite_id = b.id and b.inv_status = 0";
        $database->SetQuery($query);
        $Users_count = $database->LoadResult();
        $query = "SELECT a.* FROM `#__survey_force_users` as a, `#__survey_force_invitations` as b WHERE a.list_id ='" . $list_id . "' and a.is_invited = 1 and a.invite_id = b.id and b.inv_status = 0 and a.is_invited = 1";

        $database->SetQuery($query);
        $UsersList = $database->loadObjectList();
        $Users_to_remind = count($UsersList);

        $config = JFactory::getConfig();
        $mailfrom = $config->mailfrom;
        $fromname = $config->fromname;

        $message = $Send_email[0]->email_body;
        $subject = stripslashes($Send_email[0]->email_subject);
        $email_reply = $Send_email[0]->email_reply ? $Send_email[0]->email_reply : $mailfrom;
        $ii = 1;

        $query = "UPDATE `#__survey_force_listusers` SET `date_remind` = '" . date('Y-m-d H:i:s') . "' WHERE `id` ='" . $list_id . "'";
        $database->SetQuery($query);
        $database->execute();
        $send_rem = 0;
        $counter = 0;
        foreach ($UsersList as $user_row) {
            if ($mail_max && $send_rem == $mail_max) {
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
            $query = "SELECT `invite_num` FROM `#__survey_force_invitations` WHERE `id` = '" . $user_row->invite_id . "'";
            $database->SetQuery($query);
            $user_invite_num = $database->LoadResult();
            $link = '<a href="' . JURI::root() . "/index.php?option=com_surveyforce&task=start_invited&survey=" . $survey_id . "&invite=" . $user_invite_num . '">' . JText::_("COM_SURVEYFORCE_INVITE_LINK_TEXT") . '</a>';
            $user_name = ' ' . $user_row->name . ' ' . $user_row->lastname . ' ';
            $message_user = str_replace('#link#', $link, $message);
            $message_user = str_replace('#name#', $user_name, $message_user);
            $jmail = JFactory::getMailer();
            $jmail->sendMail($email_reply, $fromname, $user_row->email, $subject, nl2br($message_user), 1); //1 - in HTML mode

            $query = "UPDATE `#__survey_force_users` SET `is_reminded` = `is_reminded` + 1 WHERE `id` ='" . $user_row->id . "'";
            $database->SetQuery($query);
            $database->execute();
            if (($mail_pause && $mail_count) && $counter == ($mail_count - 1) && $Users_count != $ii) {
                $counter = -1;
                for ($jj = $mail_pause; $jj > 0; $jj--) {
                    echo "<script>var div_log = getObj_frame('div_invite_log');"
                    . "var div_log_txt = getObj_frame('div_invite_log_txt');"
                    . " if (div_log) {"
                    . "div_log.innerHTML = '" . intval(($ii) * 100 / $Users_count) . "%';"
                    . "div_log.style.width = '" . intval(($ii) * 600 / $Users_count) . "px';"
                    . "}"
                    . " if (div_log_txt) {"
                    . "div_log_txt.innerHTML =  '" . ($ii) . ' ' . JText::_('COM_SURVEYFORCE_USERS_REMINDED_PAUSE') . " $jj " . JText::_('COM_SURVEYFORCE_SECONDS') . "';"
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
                . "div_log.innerHTML = '" . intval(($ii) * 100 / $Users_count) . "%';"
                . "div_log.style.width = '" . intval(($ii) * 600 / $Users_count) . "px';"
                . "}"
                . " if (div_log_txt) {"
                . "div_log_txt.innerHTML = '" . ($ii) . ' ' . JText::_('COM_SURVEYFORCE_USERS_REMINDED') . "';"
                . "}"
                . "</script>";
                @flush();
                @ob_flush();
                sleep(1);
            }
            $ii ++;
            $send_rem ++;
            $counter++;
        }
        /* $query = "UPDATE `#__survey_force_users` SET `is_reminded` = '0' WHERE `list_id` ='".$list_id."'";
          $database->SetQuery($query);
          $database->execute(); */

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
