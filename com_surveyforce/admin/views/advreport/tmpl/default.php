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
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript" language="javascript">
    Joomla.submitbutton = function(pressbutton) {
        if ( jQuery('#report-cross').hasClass('active') ) {
			Joomla.submitform('get_cross_rep', document.getElementById('adminForm'));
            return;
        }
        if ( jQuery('#report-csv').hasClass('active') ) {
			Joomla.submitform('view_irep_surv', document.getElementById('adminForm'));
            return;
        }

    }

	function loadDataToSelect( value, select_id, task )
	{
		jQuery('#'+select_id+' option').remove();
		jQuery.getJSON( location.href+'&task='+task+'&id='+value, function( data ) {
			jQuery.each( data, function( key, val ) {
				jQuery('#'+select_id).append(new Option(val, key));
			});
		});
	}


</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=advreport'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-main-container" class="span12 form-horizontal">
    <ul class="nav nav-tabs" id="reportTabs">
            <li class="active"><a href="#report-cross" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_CROSS_REPORT'); ?></a></li>

                <li><a href="#report-csv" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_CSV_REPORT'); ?></a></li>

        </ul>
    <div class="tab-content">
            <div class="tab-pane active" id="report-cross">
				<legend><?php echo JText::_('COM_SURVEYFORCE_REPORT_DETAILS'); ?></legend>

				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_SELECT_SURVEY'); ?>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', $this->surveys, 'survid', 'onchange="loadDataToSelect(this.value, \'mquest_id\', \'survid\');loadDataToSelect(this.value, \'cquest_id\', \'survid_c\');"', 'value', 'text'); ?>
					</div>
				</div>

			<?php if ($this->mquest_id != '') { ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_SELECT_QUESTION_YOU_WOULD_LIKE'); ?>
					</div>
					<div class="controls">
						<?php echo  JHtml::_( 'select.genericlist', $this->mquest_id , 'mquest_id', 'multiple style="width:600px;"', 'value', 'text', $this->mquest_id[0]->value); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_SELECT_QUESTION_YOU_WOULD_LIKE'); ?>
					</div>
					<div class="controls">
						<?php echo  JHtml::_( 'select.genericlist', $this->cquest_id , 'cquest_id[]', 'multiple style="width:600px;"', 'value', 'text', $this->cquest_id[0]->value); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_FROM_DATE'); ?>
					</div>
					<div class="controls">
						<?php echo JHTML::calendar('', 'start_date', 'start_date', '%Y-%m-%d', array('class'=>"text_area",'size'=>"15", 'maxlength'=>"19")); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_TO_DATE'); ?>
					</div>
					<div class="controls">
						<?php echo JHTML::calendar('', 'end_date', 'end_date', '%Y-%m-%d', array('class'=>"text_area",'size'=>"15", 'maxlength'=>"19")); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_INCLUDE_COMPLETE'); ?>
					</div>
					<div class="controls">
						<input type="checkbox" name="is_complete" checked="checked" value="1" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_INCLUDE_NOT_COMPLETE'); ?>
					</div>
					<div class="controls">
						<input type="checkbox" name="is_notcomplete" checked="checked" value="1" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_SURVEYFORCE_GET_REPORT_IN'); ?>
					</div>
					<div class="controls">
						<select name="rep_type" class="inputbox" >
							<option value="pdf" selected="selected">Adobe PDF</option>
							<option value="csv">Excel (CSV)</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<div class="controls left">
						<button class="btn btn-primary" onclick="Joomla.submitbutton('advreport.report')">
							<span class="icon-print"></span>&nbsp;&nbsp;<?php echo JText::_('COM_SURVEYFORCE_REPORT'); ?>
						</button>
					</div>
				</div>
			<?php } else {	?>
				<div class="control-group">
					<?php echo JText::_('COM_SURVEYFORCE_CROSS_REPORT_CAN_NOT_BE_CREATED'); ?>
				</div>
			<?php
			}
?>
		</div>
        <div class="tab-pane" id="report-csv">
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('COM_SURVEYFORCE_CATEGORY2'); ?>
				</div>
				<div class="controls">
					<?php echo  JHtml::_( 'select.genericlist', $this->categories , 'catid', 'onchange="loadDataToSelect(this.value, \'survid_csv\', \'catid\');"', 'value', 'text', 0); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('COM_SURVEYFORCE_SELECT_SURVEY'); ?>
				</div>
				<div class="controls">
					<?php echo JHtml::_('select.genericlist', $this->surveys_csv, 'survid_csv', '', 'value', 'text'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('COM_SURVEYFORCE_INCLUDE_IMP_SCALE'); ?>
				</div>
				<div class="controls">
					<input type="checkbox" name="inc_imp" value="1" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_('COM_SURVEYFORCE_INCLUDE_DATE_STATUS_OR_COMPLETION'); ?>
				</div>
				<div class="controls">
					<input type="checkbox" name="add_info" value="1" />
				</div>
			</div>
			<div class="control-group">
				<div class="controls left">
					<button class="btn btn-primary" onclick="Joomla.submitbutton('advreport.report')">
						<span class="icon-print"></span>&nbsp;&nbsp;<?php echo JText::_('COM_SURVEYFORCE_REPORT'); ?>
					</button>
				</div>
			</div>

        </div>
    </div>
</div>
	<!-- get_cross_rep -->
	<!-- view_irep_surv -->
    <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="task" id="task" value="" />
</form>