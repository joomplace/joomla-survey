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
    
    function Redeclare_element_inputs(object) {
        if (object.hasChildNodes()) {
            var children = object.childNodes;
            for (var i = 0; i < children.length; i++) {
                if (children[i].nodeName.toLowerCase() == 'input') {
                    var inp_name = children[i].name;
                    var inp_value = children[i].value;
                    object.removeChild(object.childNodes[i]);
                    var input_hidden = document.createElement("input");
                    input_hidden.type = "hidden";
                    input_hidden.name = inp_name;
                    input_hidden.value = inp_value;
                    object.appendChild(input_hidden);
                }
            };
        };
    }


    function Delete_tbl_row(element) {
        var del_index = element.parentNode.parentNode.sectionRowIndex;
        var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
        element.parentNode.parentNode.parentNode.deleteRow(del_index);
        ReAnalize_tbl_Rows(del_index - 1, tbl_id);
    }

    function Up_tbl_row(element) {
        if (element.parentNode.parentNode.sectionRowIndex > 1) {
            var sec_indx = element.parentNode.parentNode.sectionRowIndex;
            var table = element.parentNode.parentNode.parentNode;
            var tbl_id = table.parentNode.id;
            var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
            element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
            var row = table.insertRow(sec_indx - 1);
            var cell1 = document.createElement("td");
            var cell2 = document.createElement("td");
            var cell3 = document.createElement("td");
            var cell4 = document.createElement("td");
            cell1.align = 'center';
            cell1.innerHTML = 0;
            cell2.align = 'left';
            cell2.innerHTML = cell2_tmp;
            cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
            cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
            row.appendChild(cell1);
            row.appendChild(cell2);
            row.appendChild(cell3);
            row.appendChild(cell4);
            row.appendChild(document.createElement("td"));
            row.appendChild(document.createElement("td"));
            ReAnalize_tbl_Rows(sec_indx - 2, tbl_id);
        }
    }

    function Down_tbl_row(element) {
        if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
            var sec_indx = element.parentNode.parentNode.sectionRowIndex;
            var table = element.parentNode.parentNode.parentNode;
            var tbl_id = table.parentNode.id;
            var cell2_tmp = element.parentNode.parentNode.cells[1].innerHTML;
            element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
            var row = table.insertRow(sec_indx + 1);
            var cell1 = document.createElement("td");
            var cell2 = document.createElement("td");
            var cell3 = document.createElement("td");
            var cell4 = document.createElement("td");
            cell1.align = 'center';
            cell1.innerHTML = 0;
            cell2.align = 'left';
            cell2.innerHTML = cell2_tmp;
            cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
            cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
            row.appendChild(cell1);
            row.appendChild(cell2);
            row.appendChild(cell3);
            row.appendChild(cell4);
            row.appendChild(document.createElement("td"));
            row.appendChild(document.createElement("td"));
            ReAnalize_tbl_Rows(sec_indx, tbl_id);
        }
    }

    function Add_new_tbl_field(elem_field, tbl_id, field_name) {
        var new_element_txt = getObj(elem_field).value;
        if (TRIM_str(new_element_txt) == '') {
            alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_TEXT_TO_FIELD'); ?>");return;
        }
        getObj(elem_field).value = '';
        var tbl_elem = getObj(tbl_id);
        var row = tbl_elem.insertRow(tbl_elem.rows.length);
        var cell1 = document.createElement("td");
        var cell2 = document.createElement("td");
        var cell3 = document.createElement("td");
        var cell4 = document.createElement("td");
        var cell5 = document.createElement("td");
        var cell6 = document.createElement("td");
        var input_hidden = document.createElement("input");
        input_hidden.type = "hidden";
        input_hidden.name = field_name;
        input_hidden.value = new_element_txt;
        cell1.align = 'center';
        cell1.innerHTML = 0;
        cell2.innerHTML = new_element_txt;
        cell2.appendChild(input_hidden);
        cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a>';
        cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/mages/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a>';
        cell5.innerHTML = '';
        row.appendChild(cell1);
        row.appendChild(cell2);
        row.appendChild(cell3);
        row.appendChild(cell4);
        row.appendChild(cell5);
        row.appendChild(cell6);
        ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
    }

    Joomla.submitbutton = function(task)
    {
        if (task == 'set_iscale.cancel' || document.formvalidator.isValid(document.id('setiscale-form'))) {
            Joomla.submitform(task, document.getElementById('setiscale-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
    
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=set_default'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="setiscale-form">
    <div class="row-fluid">	   
        <div id="j-main-container" class="span7 form-horizontal">
            <ul class="nav nav-tabs" id="configTabs">
                <li class="active"><a href="#setiscale-details" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SET_DEFAULT'); ?></a></li>	    
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="setiscale-details">
                    <fieldset class="adminform">
						<?php echo $this->options; ?>
                </fieldset>
                </div>
            </div>
        </div>
       
        <input type="hidden" name="task" value="" />
        <input type="hidden" value="<?php echo $this->id; ?>" name="id">
		<input type="hidden" name="sf_qtype" value="<?php echo $this->sf_qtype; ?>" />
        <?php echo JHtml::_('form.token'); ?>	
    </div>
</form>
