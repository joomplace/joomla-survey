<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script language="javascript" type="text/javascript">
    <!--
    function getObj(name)
    {
      if (document.getElementById)  {  return document.getElementById(name);  }
      else if (document.all)  {  return document.all[name];  }
      else if (document.layers)  {  return document.layers[name];  }
    }

    function StartInvitation() {

        var form = document.adminForm;
        var inv_frame = getObj('invite_frame');
        inv_frame.src = 'index.php?option=com_surveyforce&tmpl=component&task=invite_users.invitation_start&email=' + document.getElementById('jform_email_id').value +'&list=<?php echo $this->item->id?>';

    }
    
    function StopInvitation() {
        var form = document.adminForm;
        form.Start.value = 'Resume';
        if (!document.all)
            for (var i=0;i<top.frames.length;i++)
              top.frames[i].stop()
        else
            for (var i=0;i<top.frames.length;i++)
              top.frames[i].document.execCommand('Stop')
    }

    Joomla.submitbutton = function(task)
    {
        if (task == 'invite_users.cancel' || document.formvalidator.isValid(document.id('inviteusers-form'))) {
            Joomla.submitform(task, document.getElementById('inviteusers-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }

	jQuery(document).ready(function () {
		jQuery('#configTabs a:first').tab('show');
	});
-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=invite_users');?>" enctype="multipart/form-data" method="post" name="adminForm" id="inviteusers-form" class="form-validate">
    
    <div class="row-fluid">	   
        <div id="j-main-container" class="span9 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li><a href="#invite-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_INVITATION_DETAILS'); ?></a></li>
            </ul>
            <div class="tab-content">
				<div class="tab-pane" id="invite-details">
					<fieldset class="adminform">
						<div class="control-group form-inline">
							<legend><?php echo JText::_('COM_SURVEYFORCE_LIST_OF_USERS'); ?>: <?php echo $this->item->listname;?></legend>
						</div>
						<div class="control-group form-inline">
							<?php echo $this->form->getLabel('email_id'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('email_id'); ?>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<input type="hidden" name="option" value="com_surveyforce" />
						<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

						<table width="100%" class="adminform">
							<tr>
								<td width="20%">
									<input type="button" class="btn" name="Start" value="Start" id="Start_button" onClick="javascript:StartInvitation();">
									<input type="button" class="btn" name="Stop" value="Stop" onClick="javascript:StopInvitation();">
								</td>
								<td width="80%" align="left">
									<div id="div_invite_log" style="width:0px; background-color:#000000; color:#FFFFFF; text-align:center">
									</div>
									<div id="div_invite_log_txt" style="width:600px; text-align:left">
										<?php if ($this->item->is_invited == 0) { ?>
										<?php echo JText::_('COM_SURVEYFORCE_PRESS_START_TO_BEGIN_INVITATIONS'); ?>
										<?php } elseif ($this->item->is_invited == 1) { ?>
										<?php echo JText::_('COM_SURVEYFORCE_USERS_FROM_LIST_HAD_BEEN_SENT_INVITATIONS'); ?>
										<?php } elseif ($this->item->is_invited == 2) { ?>
										<?php echo JText::_('COM_SURVEYFORCE_PRESS_START_TO_CONTINUE_INVITATIONS'); ?>
										<?php } ?>
									</div>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>	
</form>
<iframe src="" style="display:none " id="invite_frame"></iframe>