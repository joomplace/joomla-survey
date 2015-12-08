<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu');?>

<div class="hero-unit" style="padding: 20px ! important;">
	<h1><?php echo JText::_('COM_SURVEYFORCE_BE_SUBMENU_SAMPLEDATA'); ?></h1>
	<br />
	<div class="well span6" style="width: 48%; min-height: 645px;">
		<img src="<?php echo JUri::root(); ?>administrator/components/com_surveyforce/assets/images/sample1.png" title="<?php echo JText::_('COM_SURVEYFORCE_CUSTOMER_SERVICE_SATISURVEYFORCEACTION'); ?>" alt="<?php echo JText::_('COM_SURVEYFORCE_CUSTOMER_SERVICE_SATISURVEYFORCEACTION'); ?>"  style="max-height: 430px;"/>
		<br />
		<br />
		<p><?php echo JText::_('COM_SURVEYFORCE_SAMPLE_CUSTOMER_SERVICE'); ?></p>
		<br/>
		<?php if ($this->is_sample1) {?>
			<?php echo JText::_('COM_SURVEYFORCE_DIRECT_LINK_TO_FRONT_END'); ?><br />
			<a href="<?php echo JURI::root().'index.php?option=com_surveyforce&id='.$this->is_sample1; ?>" target="_blank"><?php echo JText::_('COM_SURVEYFORCE_CUSTOMER_SERVICE_SATISFACTION'); ?></a>
		<?php } else { ?>
			<form name="adminForm1" action="" method="post">
				<button class="btn btn-primary" onclick="Joomla.submitbutton('samples.installsample1');">
					<i class="icon-download"></i>
					<?php echo JText::_('COM_SURVEYFORCE_INSTALL_THIS_SAMPLE'); ?>
				</button>
				<input type="hidden" name="option" value="com_surveyforce" />
				<input type="hidden" name="task" value="samples.installsample1" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
			<p>
				<?php echo JText::_('COM_SURVEYFORCE_AFTER_YOU_INSTALL'); ?>
			</p>
		<?php } ?>
	</div>
	<div class="well span6" style="width: 48%; min-height: 645px;">
		<img src="<?php echo JUri::root(); ?>administrator/components/com_surveyforce/assets/images/sample2.png" title="<?php echo JText::_('COM_SURVEYFORCE_S_SAMPLE_BRANCHING_SURVEY'); ?>" alt="<?php echo JText::_('COM_SURVEYFORCE_S_SAMPLE_BRANCHING_SURVEY'); ?>"  style="max-height: 430px;"/>
		<br />
		<br />
		<p><?php echo JText::_('COM_SURVEYFORCE_SAMPLE_BRANCHING_SURVEY'); ?></p>
		<br/>
		<?php if ($this->is_sample2) {?>
			<?php echo JText::_('COM_SURVEYFORCE_DIRECT_LINK_TO_FRONT_END'); ?><br />
			<a href="<?php echo JURI::root().'index.php?option=com_surveyforce&id='.$this->is_sample2; ?>" target="_blank"><?php echo JText::_('COM_SURVEYFORCE_S_SAMPLE_BRANCHING_SURVEY'); ?></a>
		<?php } else { ?>
			<form name="adminForm2" action="" method="post">
				<button class="btn btn-primary" onclick="Joomla.submitbutton('samples.installsample2');">
					<i class="icon-download"></i>
					<?php echo JText::_('COM_SURVEYFORCE_INSTALL_THIS_SAMPLE'); ?>
				</button>
				<input type="hidden" name="option" value="com_surveyforce" />
				<input type="hidden" name="task" value="samples.installsample2" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
			<p>
				<?php echo JText::_('COM_SURVEYFORCE_AFTER_YOU_INSTALL'); ?>
			</p>
		<?php } ?>
	</div>

	<p style="clear: both">	</p>
</div>