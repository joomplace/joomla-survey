<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">

	Joomla.submitbutton = function(task)
	{
		if (task == 'iscale.cancel' || document.formvalidator.isValid(document.id('iscale-form'))) {

			Joomla.submitform(task, document.getElementById('iscale-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}

</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&layout=edit&id=' . (int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="iscale-form" class="form-validate">
<input type="hidden" name="jform[date_added]" value="<?php echo JFactory::getDate(); ?>" />

<?php echo '<legend>'.$this->edit_title.'</legend>'; ?>
<div class="row-fluid">
<div id="j-main-container" class="span9 form-horizontal" >
<div class="tab-pane" id="iscale">
<div class="control-group form-inline">
	<?php echo $this->form->getLabel('iscale_name'); ?>
	<div class="controls">
		<?php echo $this->form->getInput('iscale_name'); ?>
	</div>
</div>
<div class="control-group form-inline">

<div class="clearfix"> </div>
<table class="table table-striped" id="qfld_tbl">
	<thead>
	<tr class="notAalize">
		<th width="1%" class="nowrap center">
			#
		</th>
		<th width="20%" class="nowrap center">
			<?php echo JText::_('COM_SURVEYFORCE_SCALE_OPTIONS'); ?>
		</th>
		<th width="1%" class="nowrap center">
			<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>
		</th>
		<th width="1%" class="nowrap center">
			<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>
		</th>
		<th width="1%" class="nowrap center">
			<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>
		</th>

	</tr>
	</thead>
	<tbody>
	<?php if ( !empty($this->fields) ) {
	foreach ($this->fields as $i => $field) :
		?>
		<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
			<td class="nowrap center" width="1%">
				<?php echo $i; ?>
			</td>

			<td class="nowrap center" width="20%">
				<?php echo $field->isf_name; ?>
				<input type="hidden" value=" <?php echo $field->isf_name; ?>" name="sf_hid_fields[]">
			</td>

			<td class="nowrap center" width="1%">
					<i title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>" class="icon-delete" style="color: #0088CC; cursor: pointer;"></i>
			</td>

			<td class="nowrap center" width="1%">
					<i title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>" class="icon-arrow-up" style="color: #0088CC; cursor: pointer; display: <?php echo ( ($i !== 0) ? 'inline' : 'none' ); ?>;"></i>
			</td>

			<td class="nowrap center" width="1%">
					<i title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>" class="icon-arrow-down" style="color: #0088CC; cursor: pointer; display: <?php echo ( ($i < (count($this->fields)-1)) ? 'inline' : 'none' ); ?>;"></i>
			</td>
		</tr>
	<?php endforeach;
	 } ?>
	</tbody>
</table>

<script type="text/javascript">

	function ReAnalize_tbl_Rows( start_index, tbl_id ) {
		start_index = 1;
		var tbl_elem = getObj(tbl_id);
		if (tbl_elem.rows[start_index]) {
			var count = start_index; var row_k = 1 - start_index%2;
			for (var i=start_index; i<tbl_elem.rows.length; i++) {
				tbl_elem.rows[i].cells[0].innerHTML = count;
				Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
				if (i > 1) {
					tbl_elem.rows[i].cells[3].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
				} else { tbl_elem.rows[i].cells[3].innerHTML = ''; }
				if (i < (tbl_elem.rows.length - 1)) {
					tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"></a>';;
				} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
				tbl_elem.rows[i].className = 'row'+row_k;
				count++;
				row_k = 1 - row_k;
			}
		}
	}

	function reAnalizeRows() {
		k = 0;
		jQuery('#qfld_tbl > tbody').find('tr').each( function(){
				row = jQuery(this);

				if ( row.is(':first-child') )
					row.find('.icon-arrow-up').hide();
				else
					row.find('.icon-arrow-up').show();

				if ( row.is(':last-child') )
					row.find('.icon-arrow-down').hide();
				else
					row.find('.icon-arrow-down').show();

				row.find('td:first').html(k);
				k++;
		});
	}

	function iconsBind() {
		jQuery('.icon-delete').on("click", function() {
			jQuery(this).parents('tr:first').remove();
			reAnalizeRows();
		});

		jQuery(".icon-arrow-up,.icon-arrow-down").on("click", function(){
            var row = jQuery(this).closest("tr");
			if (jQuery(this).is(".icon-arrow-up")) {
				row.insertBefore(row.prev());
				reAnalizeRows();
			} else {
				row.insertAfter(row.next());
				reAnalizeRows();
			}
		});
	}

	function getObj(name) {
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

	function Add_new_tbl_field(elem_field, tbl_id, field_name) {
		var new_element_txt = jQuery('#'+elem_field).val();

		if (TRIM_str(new_element_txt) == '') {
			alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_TEXT_TO_FIELD'); ?>");
			return;
		}

		getObj(elem_field).value = '';
		var tbl_elem = getObj(tbl_id).tBodies[0];
		var row = tbl_elem.insertRow(tbl_elem.rows.length);
		var cell1 = document.createElement("td");
		var cell2 = document.createElement("td");
		var cell3 = document.createElement("td");
		var cell4 = document.createElement("td");
		var cell5 = document.createElement("td");

		var input_hidden = document.createElement("input");
		input_hidden.type = "hidden";
		input_hidden.name = field_name;
		input_hidden.value = new_element_txt;
		cell1.className = 'nowrap center';
		cell1.width = '1%';
		cell1.innerHTML = 0;
		cell2.className = 'nowrap center';
		cell2.width = '20%';
		cell2.innerHTML = new_element_txt;
		cell2.appendChild(input_hidden);
		cell3.innerHTML = '<i title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>" class="icon-delete" style="color: #0088CC; cursor: pointer;"></i>';
		cell3.width = '1%';
		cell3.className = 'nowrap center';
		cell4.innerHTML = '<i title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>" class="icon-arrow-up" style="color: #0088CC; cursor: pointer;"></i>';
		cell5.width = '1%';
		cell5.className = 'nowrap center';
		cell5.innerHTML = '<i title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>" class="icon-arrow-down" style="color: #0088CC; cursor: pointer;"></i>';;
		cell4.width = '1%';
		cell4.className = 'nowrap center';
		cell5.width = '1%';
		cell5.className = 'nowrap center';
		row.appendChild(cell1);
		row.appendChild(cell2);
		row.appendChild(cell3);
		row.appendChild(cell4);
		row.appendChild(cell5);

		iconsBind();
		reAnalizeRows();
	}

	iconsBind();

</script>
                    <div class="control-group form-inline">
                        <input id="new_field" class="text_area" style="width:205px " type="text">
                        <input class="btn" type="button" name="jform[add_new_field]" value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'sf_hid_fields[]');">
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
