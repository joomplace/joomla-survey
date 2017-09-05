<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">

	function insertRow() {

		$('[name="add_another"]').attr('disabled', 'disabled');

	}
	Joomla.submitbutton = function(task)
	{
		if (task == 'email.cancel' || document.formvalidator.isValid(document.id('email-form'))) {
			<?php echo $this->form->getField('email_body')->save(); ?>
			Joomla.submitform(task, document.getElementById('email-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
	jQuery(document).ready(function() {
		jQuery('#configTabs a:first').tab('show');
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="email-form" class="form-validate">
	<input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
	<div class="row-fluid">
		<div id="j-main-container" class="span7 form-horizontal">
			<ul class="nav nav-tabs" id="configTabs">
				<li class="active"><a href="#email-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_NEW_EMAIL'); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="email-details">
					<fieldset class="adminform">
						<legend><?php echo JText::_('COM_SURVEYFORCE_EMAIL_DETAILS') ?></legend>
						<div class="control-group form-inline">
							<?php echo JText::_('COM_SURVEYFORCE_SUBJECT') . ':' ?>
							<div class="controls">
								<?php echo $this->form->getInput('email_subject'); ?>
							</div>
						</div>
						<div class="control-group form-inline">
							<?php echo JText::_('COM_SURVEYFORCE_BODY') . ':' ?>
							<div style="position: absolute">
								<br /><br />
								<b>#link#</b> - Survey link<br />
								<b>#name#</b> - User name<br />
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('email_body'); ?>
							</div>
						</div>
						<div class="control-group form-inline">
							<?php echo JText::_('COM_SURVEYFORCE_REPLY_TO') . ':' ?>
							<div class="controls">
                            <?php
                                $config = JFactory::getConfig();
                                $hint = $this->form->getFieldAttribute('email_reply', 'hint') ? $this->form->getFieldAttribute('email_reply', 'hint') : $config->get('mailfrom');
                                $this->form->setFieldAttribute('email_reply', 'hint', $hint)
                            ?>
                            <?php echo $this->form->getInput('email_reply'); ?>
							</div>
						</div>
						<br style="clear:both;"/>


				</div>


			</div>
		</div>

		<input type="hidden" value="<?php JFactory::getUser()->id; ?>" name="user_id">
		<input type="hidden" name="task" value="" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
