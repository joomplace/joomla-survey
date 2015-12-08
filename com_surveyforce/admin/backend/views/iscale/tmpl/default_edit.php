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
    <legend><?php echo JText::_('COM_SF_EDIT_IMPORTANCE_SCALE'); ?></legend>
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
                            <tr>
                                <th width="1%" class="nowrap center">
                                    <?php echo JText::_('COM_SURVEYFORCE_ID'); ?>
                                </th>
                                <th width="20%" class="nowrap center">
                                    <?php echo JText::_('COM_SURVEYFORCE_SCALE_OPTIONS'); ?>
                                </th>
                                <th width="1%" class="hidden-phone">
                                    <?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>
                                </th>
                                <th width="1%" class="nowrap center">
                                    <?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>
                                </th>
                                <th width="1%">
                                    <?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>
                                </th>										

                            </tr>
                        </thead>
                        <tfoot>

                        </tfoot>
                        <tbody>
                            <?php
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
                                        <a title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>" onclick="javascript:Delete_tbl_row(this); return false;" href="javascript: void(0);">
                                            <i class="icon-delete"></i>
                                        </a>
                                    </td>

                                    <td class="nowrap center" width="1%">
                                        <?php if($i !== 0){?>
                                        <a title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>" onclick="javascript:Up_tbl_row(this); return false;" href="javascript: void(0);">
                                            <i class="icon-arrow-up"></i>
                                        </a>
                                        <?php }?>
                                    </td>

                                    <td class="nowrap center" width="1%">
                                         <?php if($i < (count($this->fields)-1)){?>
                                        <a title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>" onclick="javascript:Down_tbl_row(this); return false;" href="javascript: void(0);">
                                            <i class="icon-arrow-down"></i>
                                        </a>
                                        <?php }?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                        function ReAnalize_tbl_Rows(start_index, tbl_id) {
                            start_index = 1;
                            var tbl_elem = getObj(tbl_id);
                            if (tbl_elem.rows[start_index]) {
                                var count = start_index;
                                var row_k = 1 - start_index % 2;
                                for (var i = start_index; i < tbl_elem.rows.length; i++) {
                                    tbl_elem.rows[i].cells[0].innerHTML = count;
                                    Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
                                    if (i > 1) {
                                        tbl_elem.rows[i].cells[3].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><i class="icon-arrow-up"></i></a>';
                                    } else {
                                        tbl_elem.rows[i].cells[3].innerHTML = '';
                                    }
                                    if (i < (tbl_elem.rows.length - 1)) {
                                        tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"><i class="icon-arrow-down"></i></a>';
                                        ;
                                    } else {
                                        tbl_elem.rows[i].cells[4].innerHTML = '';
                                    }
                                    tbl_elem.rows[i].className = 'row' + row_k;
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
                                }
                                ;
                            }
                            ;
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
                                var cell5 = document.createElement("td");
                                cell1.className = 'nowrap center';
                                cell1.width = '1%';
                                cell1.innerHTML = 0;
                                cell2.className = 'nowrap center';
                                cell2.width = '20%';
                                cell2.innerHTML = cell2_tmp;
                                cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><i class="icon-delete"></i></a>';
                                cell3.width = '1%';
                                cell3.className = 'nowrap center';
                                cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><i class="icon-arrow-up"></i></a>';
                                cell4.width = '1%';
                                cell4.className = 'nowrap center';
                                cell5.width = '1%';
                                cell5.className = 'nowrap center';
                                row.appendChild(cell1);
                                row.appendChild(cell2);
                                row.appendChild(cell3);
                                row.appendChild(cell4);
                                row.appendChild(cell5);
                                
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
                                var cell5 = document.createElement("td");
                                cell1.className = 'nowrap center';
                                cell1.width = '1%';
                                cell1.innerHTML = 0;
                                cell2.className = 'nowrap center';
                                cell2.width = '20%';
                                cell2.innerHTML = cell2_tmp;
                                cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><i class="icon-delete"></i></a>';
                                cell3.width = '1%';
                                cell3.className = 'nowrap center';
                                cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><i class="icon-arrow-up"></i></a>';
                                cell4.width = '1%';
                                cell4.className = 'nowrap center';
                                cell5.width = '1%';
                                cell5.className = 'nowrap center';
                                row.appendChild(cell1);
                                row.appendChild(cell2);
                                row.appendChild(cell3);
                                row.appendChild(cell4);
                                row.appendChild(cell5);
                               
                                ReAnalize_tbl_Rows(sec_indx, tbl_id);
                            }
                        }

                        function Add_new_tbl_field(elem_field, tbl_id, field_name) {
                            var new_element_txt = getObj(elem_field).value;
                            if (TRIM_str(new_element_txt) == '') {
                                alert("<?php echo JText::_('COM_SURVEYFORCE_PLEASE_ENTER_TEXT_TO_FIELD'); ?>");
                                return;
                            }
                            getObj(elem_field).value = '';
                            var tbl_elem = getObj(tbl_id);
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
                            cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><i class="icon-delete"></i></a>';
                            cell3.width = '1%';
                            cell3.className = 'nowrap center';
                            cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><i class="icon-arrow-up"></i></a>';
                            cell5.innerHTML = '';
                            cell4.width = '1%';
                            cell4.className = 'nowrap center';
                            cell5.width = '1%';
                            cell5.className = 'nowrap center';
                            row.appendChild(cell1);
                            row.appendChild(cell2);
                            row.appendChild(cell3);
                            row.appendChild(cell4);
                            row.appendChild(cell5);
                            
                            ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
                        }
                       
                        //-->
                    </script>
                    <div class="control-group form-inline">

                        <input id="new_field" class="text_area" style="width:205px " type="text" name="jform[new_field]">
                        <input class="btn" type="button" name="jform[add_new_field]" value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'sf_hid_fields[]');">
                    </div>

                </div>

            </div> 

        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>	
    </div>
</form>
