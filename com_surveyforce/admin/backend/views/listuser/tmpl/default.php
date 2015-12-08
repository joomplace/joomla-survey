<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">

	function insertRow() {

		$('[name="add_another"]').attr('disabled', 'disabled');

	}
	Joomla.submitbutton = function(task)
	{
		if (task == 'listuser.cancel' || document.formvalidator.isValid(document.id('listuser-form'))) {
			Joomla.submitform(task, document.getElementById('listuser-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
	jQuery(document).ready(function() {

		jQuery('#configTabs a:first').tab('show');
	});
</script>
<script language="javascript" type="text/javascript">
	<!--
	function getObj(name)
	{
		if (document.getElementById) {
			return document.getElementById(name);
		}
		else if (document.all) {
			return document.all[name];
		}
		else if (document.layers) {
			return document.layers[name];
		}
	}

	function TRIM_str(sStr) {
		return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
	}

	function ReAnalize_tbl_Rows( start_index ) {
		var tbl_elem = getObj('qfld_tbl');
		if (!start_index) { start_index = 1; }
		if (start_index < 0) { start_index = 1; }
		if (tbl_elem.rows[start_index]) {
			var count = start_index; var row_k = 1 - start_index%2;
			for (var i=start_index; i<tbl_elem.rows.length; i++) {
				tbl_elem.rows[i].cells[0].innerHTML = count;
				tbl_elem.rows[i].className = 'row'+row_k;
				count++;
				row_k = 1 - row_k;
			}
		}
	}

	function Delete_tbl_row(element) {
		var del_index = element.parentNode.parentNode.sectionRowIndex;
		element.parentNode.parentNode.parentNode.deleteRow(del_index);
		ReAnalize_tbl_Rows(del_index - 1);
	}

	function Add_new_tbl_field() {

		var new_user_name = getObj('new_name').value;
		var new_user_lastname = getObj('new_lastname').value;
		var new_user_email = getObj('new_email').value;
		if (new_user_name == '') {
			alert('<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_USER_NAME');?>');
			return false;
		}
		if (new_user_lastname == '') {
			alert('<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_USER_LASTNAME');?>');
			return false;
		}
		var reg_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
		if (!reg_email.test(new_user_email)) {
			alert('<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_VALID_EMAIL');?>');
			return false;
		}
		var tbl_elem = getObj('qfld_tbl');
		var row = tbl_elem.insertRow(tbl_elem.rows.length);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var cell5 = document.createElement("td");
		var cell6 = document.createElement("td");
		cell1.align = 'center';
		cell1.innerHTML = 0;
		cell2.innerHTML = new_user_name + '<input type="hidden" name="sf_hid_names[]" value="' + new_user_name + '">';
		cell3.innerHTML = new_user_lastname + '<input type="hidden" name="sf_hid_lastnames[]" value="' + new_user_lastname + '">';
		cell4.innerHTML = new_user_email + '<input type="hidden" name="sf_hid_emails[]" value="' + new_user_email + '">';
		cell5.innerHTML = '<a href="" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(cell5);
		row.appendChild(cell6);
		ReAnalize_tbl_Rows(tbl_elem.rows.length - 2);
		document.getElementById('jform_is_add_manually').checked = true;
	}


	//-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="listuser-form" class="form-validate">
	<input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />
	<?php echo $this->form->getInput('id'); ?>
	<div class="row-fluid">
		<div id="j-main-container" class="span7 form-horizontal">
			<ul class="nav nav-tabs" id="configTabs">
				<li><a href="#listuser-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_LIST_DETAILS'); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane" id="listuser-details">
					<fieldset class="adminform">

						<div class="control-group form-inline">
							<?php echo JText::_('COM_SURVEYFORCE_LIST_NAME') ?>
							<div class="controls">
								<?php echo $this->form->getInput('listname'); ?>
							</div>
						</div>
						<div class="control-group form-inline">
							<?php echo JText::_('COM_SURVEYFORCE_SURVEY') . ':' ?>
							<div class="controls">
								<?php echo $this->form->getInput('survey_id'); ?>
							</div>
						</div>
					</fieldset>
					<fieldset class="adminform">
						<?php if(!JFactory::getApplication()->input->get('id','')){ ?>
							<legend>
								<?php echo JText::_('COM_SURVEYFORCE_QUIK_ADD_TO_LIST') ?>
							</legend>
							<div class="control-group form-inline">
								<?php echo $this->form->getLabel('is_add_reg'); ?>
								<div class="controls">
									<?php echo $this->form->getInput('is_add_reg'); ?>
								</div>
							</div>
							<div class="control-group form-inline">
								<?php echo $this->form->getLabel('is_import_csv'); ?>
								<div class="controls">
									<?php echo $this->form->getInput('is_import_csv'); ?>
								</div>
							</div>
							<div class="control-group form-inline">
								<label class=" control-label" for="jform_csv_file" id="jform_csv_file-lbl">
									<?php echo JText::_('COM_SURVEYFORCE_SELECT_CSV_FILE') ?>
									<?php $csv_descr = JText::_('COM_SURVEYFORCE_THE_CSV_FILE_IS_USED')
										. "\n <b>".JText::_('COM_SURVEYFORCE_THE_STRUCTURE_OF_CSV_FILE')."</b>"
										. "\n ".JText::_('COM_SURVEYFORCE_LASTNAME_NAME_EMAIL')
										. '\n '.JText::_('COM_SURVEYFORCE_YOU_CAN_FIND_EXAMPLE_OF_CSV_STRINGS')
										. "\n <b>".JText::_('COM_SURVEYFORCE_YOU_CAN_FIND_EXAMPLE_OF_CSV')."</b>"
										. "\n `".JUri::base()."components/com_surveyforce/\nhelpers/example_users.csv`";
									?>
									<span class="icon icon-question-sign hasTooltip" data-original-title="<?php echo nl2br($csv_descr); ?>"></span>
								</label>
								<div class="controls">
									<?php echo $this->form->getInput('csv_file'); ?>
								</div>
							</div>
							<div class="control-group form-inline">
								<?php echo $this->form->getLabel('is_add_manually'); ?>
								<div class="controls">
									<?php echo $this->form->getInput('is_add_manually'); ?>
								</div>
							</div>
							<div class="clear"></div>
							<br />
							<table class="adminlist" id="qfld_tbl">
								<tr>
									<th width="20px" align="center">#</th>
									<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_NAME'); ?></th>
									<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_LASTNAME'); ?></th>
									<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_EMAIL'); ?></th>
									<th width="20px" align="center" class="title"></th>
									<th width="auto"></th>
								</tr>
							</table><br>
							<div style="text-align:left; padding-left:30px ">
								<input id="new_name" class="text_area" style="width:203px " type="text" name="new_name">
								<input id="new_lastname" class="text_area" style="width:203px " type="text" name="new_lastname">
								<input id="new_email" class="text_area" style="width:203px " type="text" name="new_email">
								<input class="btn" type="button" name="add_new_field" style="width:70px " value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field();">
							</div>
							<br />
						<?php } ?>
					</fieldset>
				</div>
			</div>
		</div>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
