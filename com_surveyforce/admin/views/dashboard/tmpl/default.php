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
JHtml::_('behavior.modal');

JText::script('COM_SURVEYFORCE_BE_CONTROL_PANEL_CONNECTION_FAILED');
JText::script('COM_SURVEYFORCE_BE_CONTROL_PANEL_TIMEOUT');
JText::script('COM_SURVEYFORCE_BE_CONTROL_PANEL_CHANGELOG');
JText::script('COM_SURVEYFORCE_BE_CONTROL_PANEL_BUT_CHECK_VERSION');
?>
<script type="text/javascript">
	var currentVersion = '<?php echo $this->version;?>';
</script>
<script type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_surveyforce/assets/js/dashboard.js"></script>
<?php echo $this->loadTemplate('menu');?>

<div id="j-sidebar-container" class="span6" style="margin-left: 0px;">
	<div class="survf_dashboard">
    <?php foreach($this->dashboardItems as $ditem): ?>
        <div onclick="window.location = '<?php echo $ditem->url; ?>';" class="btn">
            <?php if ($ditem->icon) { ?>
                <img src="<?php echo $ditem->icon ?>" class="pmg-dashboard_item_icon"/>
            <?php } ?>
            <div><?php echo $ditem->title; ?></div>
        </div>
   <?php endforeach; ?>
		<div onclick="window.location = 'index.php?option=com_surveyforce&view=dashboard_items';" class="btn">
			<div><?php echo JText::_('COM_SURVEYFORCE_MANAGE_DASHBOARD_ITEMS');?></div>
		</div>
	</div>
</div>

<div id="j-main-container" class="span6 form-horizontal survf_control_panel_container well" style="margin-right: 0px;">
	<table class="table">
		<tr>
			<th colspan="100%" class="survf_control_panel_title">
				<strong><?php echo JText::_('COM_SURVEYFORCE'); ?></strong> component for Joomla! 3.x Developed by <a href="http://www.joomplace.com/" target="_blank">JoomPlace</a>.
			</th>
		</tr>
		<tr>
			<td width="120" style="border-top: 0 !important;"><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_INSTALLED_VERS') . ':'; ?></td>
			<td class="survf_control_panel_current_version"><?php echo $this->version;?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_ABOUT') . ':'; ?></td>
			<td>
				<?php echo JText::_('COM_SURVEYFORCE_ABOUT_TEXT'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table cellpadding="5" class="survf_control_panel_news_table">
					<tr>
						<td>
							<img src="<?php echo JURI::root();?>administrator/components/com_surveyforce/assets/images/tick.png"><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_NEWS'); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="survf_control_panel_news_cell" style="background-image: linear-gradient(to bottom, #FFFFFF, #EEEEEE);">
							<div id="survfLatestNews" class="survf_control_panel_news">
								<img src="<?php echo JURI::root();?>administrator/components/com_surveyforce/assets/images/ajax_loader_16x11.gif"/>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div class="modal hide fade" id="changelogModal">
		<div class="modal-header">
			<button type="button" role="presentation" class="close" style="z-index: 2000" data-dismiss="modal">x</button>
			<h3><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_LATEST_CHANGES'); ?></h3>
		</div>
		<div class="modal-body form-horizontal" id="body"></div>
		<div class="modal-footer">
			<button class="btn" id="closeBtn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_BUTTON_CLOSE'); ?></button>
			<button class="btn btn-primary" onclick="window.open('http://www.joomplace.com/members-area.html', '_blank'); jQuery('#changelogModal').modal('hide'); return false;"><?php echo JText::_('COM_SURVEYFORCE_BE_CONTROL_PANEL_BUTTON_DOWNLOAD'); ?></button>
		</div>
	</div>
</div>


<?php if ($this->messageTrigger) { ?>
<div id="notification" class="jqd-survey-wrap clearfix" style="clear: both">
    <div class="jqd-survey">
        <span><?php echo JText::_("COM_SURVEYFORCE_NOTIFICMES1"); ?><a onclick="dateAjaxRef()" style="cursor: pointer" rel="nofollow" target="_blank"><?php echo JText::_("COM_SURVEYFORCE_NOTIFICMES2"); ?></a><?php echo JText::_("COM_SURVEYFORCE_NOTIFICMES3"); ?><i id="close-icon" class="icon-remove" onclick="dateAjaxIcon()"></i></span>
    </div>
</div>
<?php } ?>