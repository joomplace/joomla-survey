<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$saveOrder = $ordering = $listOrder == 'ordering';
$user = JFactory::getUser();
$userId = $user->get('id');
$extension = 'com_surveyforce';


$saveOrder = $listOrder == 'ordering';
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_surveyforce&task=surveys.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
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

	Joomla.submitbutton = function(task) {
		if (task == 'surveys.preview') {
            if(document.adminForm.boxchecked.value > 1) {
                alert('<?php echo JText::_('COM_SURVEYFORCE_SURVEYS_PREVIEW_SELECT_ONE'); ?>');
                return false;
            }
			document.adminForm.target = '_blank';
			Joomla.submitform(task);
			document.adminForm.target = '';
		} else {
            Joomla.submitform(task);
        }
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=surveys'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php endif;?>
    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value = '';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
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
        <div class="clearfix"> </div>
        <table class="table table-striped" id="testimonialsList">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 'id', $listDirn, $listOrder); ?>
                    </th>

                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_NAME', 'sf_name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_SF_CATEGORY', 'sf_cat', $listDirn, $listOrder); ?>
                    </th>
                    <th  width="10%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_AUTHOR', 'sf_author', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_PUBLIC', 'sf_public', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_AUTO_PAGE_BREAK', 'sf_auto_pb', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_FOR_INVITED', 'sf_invite', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SF_FOR_REG', 'sf_reg', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_FOR_USERS_IN_LISTS', 'sf_for_users_in_list', $listDirn, $listOrder); ?>
                    </th>
					<th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_STARTED_ON', 'sf_date_started', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_EXPIRED_ON', 'sf_date_expired', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="13">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php
                foreach ($this->items as $i => $item) :
                    $ordering = ($listOrder == 'ordering');
                    $canEdit = $user->authorise('core.edit', $extension . '.surveys.' . $item->id);
                    $canCheckin = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                    $canChange = $user->authorise('core.edit.state', $extension . '.surveys.' . $item->id) && $canCheckin;
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
                        <td class="center">
                            <?php echo $item->id; ?>
                        </td>
                        <td class="nowrap center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=survey&layout=edit&id='.$item->id); ?>"><?php echo $this->escape(str_replace("&nbsp;", '', mb_substr(strip_tags($item->sf_name), 0, 70))); ?></a>&nbsp;<a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=questions&surv_id=' . $item->id); ?>">[<?php echo JText::_('COM_SURVEYFORCE_VIEW_QUESTIONS');?>]</a>
                                <?php else : ?>
                                    <?php echo $this->escape(str_replace("&nbsp;", '', mb_substr(strip_tags($item->sf_name), 0, 70))); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="center">
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'surveys.', $canChange); ?>
                        </td>
                        <td class="has-context">
                            <div class="pull-left">
                                <?php echo $item->sf_catname; ?>
                            </div>
                        </td>
                        <td class="has-context center">
                                <?php echo $item->username; ?>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if($item->sf_public){ ?>
                                    <i class="icon-publish"></i>
                                <?php } else {?>
                                    <i class="icon-unpublish"></i>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if($item->sf_auto_pb){ ?>
                                    <i class="icon-publish"></i>
                                <?php } else {?>
                                    <i class="icon-unpublish"></i>
                                <?php }?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if($item->sf_invite){ ?>
                                    <i class="icon-publish"></i>
                                <?php } else {?>
                                    <i class="icon-unpublish"></i>
                                <?php }?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if($item->sf_reg){ ?>
                                    <i class="icon-publish"></i>
                                <?php } else {?>
                                    <i class="icon-unpublish"></i>
                                <?php }?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if($item->sf_special){ ?>
                                    <i class="icon-publish"></i>
                                <?php } else {?>
                                    <i class="icon-unpublish"></i>
                                <?php }?>
                            </div>
                        </td>
						 <td class="has-context">
                            <div class="center">
                                <?php if ($item->sf_date_started == '0000-00-00 00:00:00')
										echo JText::_('COM_SURVEYFORCE_NO');
									else
										if ( strtotime($item->sf_date_started) > strtotime(JFactory::getDate()) )
											echo '<font color="lightgray">'.JHtml::_('date',$item->sf_date_started,'H:i:s d/m/Y').'</font>';
										else
											echo JHtml::_('date',$item->sf_date_started,'H:i:s d/m/Y');
								?>
                            </div>
                        </td>
                        <td class="has-context">
                            <div class="center">
                                <?php if ($item->sf_date_expired == '0000-00-00 00:00:00')
										echo JText::_('COM_SURVEYFORCE_NO');
									else
										if ( strtotime($item->sf_date_expired) < strtotime(JFactory::getDate()) )
											echo '<font color="red">'.JHtml::_('date',$item->sf_date_expired,'H:i:s d/m/Y').'</font>';
										else
											echo JHtml::_('date',$item->sf_date_expired,'H:i:s d/m/Y');
								?>
                            </div>
                        </td>


                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>