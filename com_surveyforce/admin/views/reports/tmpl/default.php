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
JHtml::_('formbehavior.chosen', 'select');

$rows = $this->items;
$sortFields = $this->getSortFields();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>
<?php echo $this->loadTemplate('menu'); ?>
<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }

	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'reports.pdf_sum' || pressbutton == 'reports.pdf_sum_perc') {
			if (form.filter_survey_name.selectedIndex<1) {
				alert("<?php echo JText::_('COM_SF_SELECT_SURVEY'); ?>");
				return;
			}
			submitform( pressbutton );
            document.getElementsByName("task")[0].value = "";
			return;
		}
		submitform( pressbutton );
        document.getElementsByName("task")[0].value = "";
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=reports'); ?>" method="post" name="adminForm" id="adminForm">
	
    <div id="j-sidebar-container" class="span2">

		<?php echo $this->sidebar; ?>
		<br/><br/>

        <div class="filter-select fltrt">
            <h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></h4>
            <select name="filter_is_complete" class="inputbox" onchange="document.adminForm.task.value = 'reports'; this.form.submit()" style="width: 268px">
                <option value=""><?php echo JText::_('COM_SURVEYFORCE_SELECT_STATUS'); ?></option>
                <?php echo JHtml::_('select.options', $this->sf_status, 'value', 'text', $this->state->get('filter.is_complete')); ?>
            </select>
			<br/><br/>
            <select name="filter_usertype" class="inputbox" onchange="document.adminForm.task.value = 'reports'; this.form.submit()" style="width: 268px">
                <option value=""><?php echo JText::_('COM_SURVEYFORCE_SELECT_USERTYPE'); ?></option>
                <?php echo JHtml::_('select.options', $this->usertype, 'value', 'text', $this->state->get('filter.usertype')); ?>
            </select>
			<br/><br/>
            <select name="filter_survey_name" class="inputbox" onchange="document.adminForm.task.value = 'reports'; this.form.submit()" style="width: 268px">
                <option value=""><?php echo JText::_('COM_SURVEYFORCE_S_SELECT_SURVEY'); ?></option>
                <?php echo JHtml::_('select.options',$this->survey_names, 'value', 'text', $this->state->get('filter.survey_name')); ?>
            </select>
        </div>
    </div>
	
	<div id="j-main-container" class="span10">

    <div id="filter-bar" class="btn-toolbar">

        <div class="btn-group pull-right hidden-phone">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div class="btn-group pull-right hidden-phone">
            <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
            <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
            </select>
        </div>
        <div class="btn-group pull-right">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
            </select>
        </div>
    </div>

    <table class="table table-striped">
        <tr>
            <th width="1%">#</th>
            <th width="1%" class="title"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
            <th ><?php echo JText::_('COM_SURVEYFORCE_DATE'); ?></th>
            <th ><?php echo JText::_('COM_SURVEYFORCE_STATUS'); ?></th>
            <th ><?php echo JText::_('COM_SURVEYFORCE_SURVEY2'); ?></th>
            <th ><?php echo JText::_('COM_SURVEYFORCE_USERTYPE'); ?></th>
            <th ><?php echo JText::_('COM_SURVEYFORCE_USER_INFO'); ?></th>
        </tr>
        <?php
        $k = 0;
        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            $row = $rows[$i];

            $link = 'index.php?option=com_surveyforce&task=report.report&id=' . $row->id;
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td align="center"><?php echo $row->id; ?></td>
                <td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
                <td align="center">
                    <a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_SURVEYFORCE_VIEW_RESULTS'); ?>">
                        <?php echo JHtml::_('date',$row->sf_time,'H:i d/m/Y'); ?>
                    </a>
                </td>
                <td align="center">
                    <?php echo ($row->is_complete) ? JText::_('COM_SURVEYFORCE_COMPLETED') : JText::_('COM_SURVEYFORCE_NOT_COMPLETED'); ?>
                </td>
                <td align="center">
                    <?php echo $row->survey_name; ?>
                </td>
                <td align="center">
                    <?php
                    switch ($row->usertype) {
                        case '0': echo JText::_('COM_SURVEYFORCE_GUEST');
                            break;
                        case '1': echo JText::_('COM_SURVEYFORCE_REGISTERED_USER');
                            break;
                        case '2': echo JText::_('COM_SURVEYFORCE_INVITED_USER');
                            break;
                    }
                    ?>
                </td>
                <td align="center">
                    <?php
                    switch ($row->usertype) {
                        case '0': echo JText::_('COM_SURVEYFORCE_ANONYMOUS');
                            break;
                        case '1': echo $row->reg_username . ", " . $row->reg_name . " (" . $row->reg_email . ")";
                            break;
                        case '2': echo $row->inv_name . " " . $row->inv_lastname . " (" . $row->inv_email . ")";
                            break;
                    }
                    ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
		<tfoot>
		<tr><td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
		</td></tr></tfoot>
    </table>

	</div>


    <input type="hidden" name="option" value="com_surveyforce" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <input type="hidden" name="task" value="reports" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0">
    <?php echo JHtml::_('form.token'); ?>
</form>