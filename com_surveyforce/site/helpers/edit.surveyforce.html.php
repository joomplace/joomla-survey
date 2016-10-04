<?php
/**
* Survey Force component for Joomla
* @version $Id: edit.surveyforce.html.php 2009-11-16 17:30:15
* @package Survey Force
* @subpackage edit.surveyforce.html.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

if (!function_exists('idBox'))
{
	function idBox( $rowNum, $recId, $checkedOut=false, $name='cid' ) {
		if ( $checkedOut ) {
			return '';
		} else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
		}
	}
}

function SF_showHeadPicture( $pic_type ) {
	$comp_folder = 'com_surveyforce';
	$html_output = '';	
	
	switch ($pic_type) {
		case 'categories':
			$html_output = '<img class="SF_png" src="components/'.$comp_folder.'/assets/images/headers/head_courses.png" border="0" title="'.JText::_('COM_SURVEYFORCE_SF_CATEGORIES').'" alt="'.JText::_('COM_SURVEYFORCE_SF_CATEGORIES').'" />';
		break;
		case 'surveys':
			$html_output = '<img class="SF_png" src="components/'.$comp_folder.'/assets/images/headers/head_quiz.png" border="0" title="'.JText::_('COM_SURVEYFORCE_SF_SURVEYS').'" alt="'.JText::_('COM_SURVEYFORCE_SF_SURVEYS').'" />';
		break;
		case 'usergroup':
			$html_output = '<img class="SF_png" src="components/'.$comp_folder.'/assets/images/headers/head_usergroup.png" border="0" title="'.JText::_('COM_SURVEYFORCE_SF_USERGROUPS').'" alt="'.JText::_('COM_SURVEYFORCE_SF_USERGROUPS').'" />';
		break;
		case 'report':			
				$html_output = '<img class="SF_png" src="components/'.$comp_folder.'/assets/images/headers/head_certificate.png" border="0" title="'.JText::_("COM_SURVEYFORCE_SF_REPORTS").'" alt="'.JText::_("COM_SURVEYFORCE_SF_REPORTS").'" />';
		break;
	}
	return $html_output;
}

function SF_showTop( $HeadPicture, $HeadTitle, $toolbar, $additionBottomRight, $additionBottomLeft = '', $additionUpLeft = '' )
{
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.modal', 'a.modal');
	$sf_config = JComponentHelper::getParams('com_surveyforce');

	$Itemid = JRequest::getVar('Itemid');

	?>
	<link rel="stylesheet" href="<?php echo JURI::root()?>components/com_surveyforce/assets/css/style.css" />
	<script language="javascript" type="text/javascript">
		function TRIM_str(sStr) {
			return (sStr.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""));
		}
	</script>

	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href="#">Dashboard</a>
				<?php if($additionUpLeft == 'new_survey'){ ?>

					<a onclick="javascript:window.open('<?php echo JURI::root(); ?>index.php?option=com_surveyforce&task=edit_surv&tmpl=component&Itemid=<?php echo $Itemid; ?>', '_blank'); return false;" href="javascript: void(0);" class="btn btn-primary"><i class="sf-icon-plus"></i>New
					</a>

				<?php } elseif($additionUpLeft == 'new_category'){ ?>

					<a href="<?php echo JURI::root(); ?>index.php?option=com_surveyforce&task=edit_cat&Itemid=<?php echo $Itemid; ?>" class="btn btn-primary"><i class="sf-icon-plus"></i>New
					</a>

				<?php } elseif($additionUpLeft == 'new_userlist'){ ?>

					<a href="<?php echo JURI::root(); ?>index.php?option=com_surveyforce&task=edit_list&Itemid=<?php echo $Itemid; ?>" class="btn btn-primary"><i class="sf-icon-plus"></i>New
					</a>

				<?php } elseif($additionUpLeft == 'new_email'){ ?>

					<a href="<?php echo JURI::root(); ?>index.php?option=com_surveyforce&task=add_email&Itemid=<?php echo $Itemid; ?>" class="btn btn-primary"><i class="sf-icon-plus"></i>New
					</a>

				<?php } ?>

				<div class="nav-collapse navbar-responsive-collapse collapse" style="height: 0px;">
					<ul class="nav pull-right">
						<?php if(!empty($toolbar)){ ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="sf-icon-wrench"></i> Actions <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php for ($i = 0; $i < count($toolbar); $i++):?>
									<?php if ($toolbar[$i]['btn_type'] == "divider"): ?>
										<li class="divider"></li>
									<?php else: ?>
										<li>
											<a href="<?php echo (isset($toolbar[$i]['btn_href']) ? $toolbar[$i]['btn_href'] : 'javascript: void(0);');?>" onclick="<?php echo $toolbar[$i]['btn_js'];?> return false;" <?php echo (isset($toolbar[$i]['modal']) ? 'class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 450}}"' : '');?>>
												<?php if(isset($toolbar[$i]['btn_ico']) && isset($toolbar[$i]['btn_str'])):?>
													<i class="<?php echo $toolbar[$i]['btn_ico'];?>"></i> <?php echo $toolbar[$i]['btn_str'];?>
												<?php endif;?>
											</a>
										</li>
									<?php endif;?>
								<?php endfor;?>
							</ul>
						</li>
						<?php } ?>

						<li <?php echo ($HeadPicture == 'surveys' ? 'class="active"' : '');?>><a href="<?php echo JRoute::_("index.php?option=com_surveyforce&task=surveys")?>"><i class="sf-icon-help"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_SURVEYS')?></a></li>

						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')) : ?>
							<li <?php echo ($HeadPicture == 'categories' ? 'class="active"' : '');?>><a href="<?php echo JRoute::_("index.php?option=com_surveyforce&task=categories")?>"><i class="sf-icon-box"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_CATEGORIES')?></a></li>
							<li <?php echo ($HeadPicture == 'usergroups' ? 'class="active"' : '');?>><a href="<?php echo JRoute::_("index.php?option=com_surveyforce&task=usergroups")?>"><i class="sf-icon-users"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_USERGROUPS')?></a></li>
						<?php endif;?>

						<li <?php echo ($HeadPicture == 'reports' ? 'class="active"' : '');?>><a href="<?php echo JRoute::_("index.php?option=com_surveyforce&task=reports")?>"><i class="sf-icon-paste"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_REPORTS')?></a></li>

						<?php if ($sf_config->get('sf_enable_jomsocial_integration')) : ?>
							<li><a href="<?php echo JRoute::_("index.php?option=com_surveyforce&task=help&tmpl=component");?>" class="modal hasTooltip" rel="{handler: 'iframe', size: {x:800, y:600}}" title="Help"><i class="sf-icon-help-circled"></i></a></li>
						<?php endif;?>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div>

	<div class="clearfix"></div>

	<?php if (!empty($additionBottomRight) || !empty($additionBottomLeft)): ?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="pull-left">
			<?php echo $additionBottomLeft; ?>
			<?php if($additionUpLeft == 'new_survey'){ ?>
			<input type="text" size="35" name="sf_search" id="sf_search" placeholder="Search survey"/>
			<?php } ?>
		</div>
		<div class="pull-right">
			<?php echo $additionBottomRight; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php endif;?>
	<?php
}

function ShowToolbar($toolbar) {
	$toolbar_thml = '<div class="btn-toolbar">';
	foreach ($toolbar as $toolbar_btn) {
		switch ($toolbar_btn['btn_type']) {
			case 'move':
				$btn_img = 'btn_move.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_MOVE');
			break;
			case 'copy':
				$btn_img = 'btn_copy.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_COPY');
			break;
			case 'save':
				$btn_img = 'btn_save.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_SAVE');
			break;
			case 'apply':
				$btn_img = 'btn_apply.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_APPLY');
			break;
			case 'back':
				$btn_img = 'btn_back.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_BACK');
			break;
			case 'cancel':
				$btn_img = 'btn_cancel.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_CANCEL');
			break;
			case 'del':
				$btn_img = 'btn_delete.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_DELETE');
			break;
			case 'edit':
				$btn_img = 'btn_edit.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_EDIT');
			break;
			case 'publish':
				$btn_img = 'btn_accept2.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_PUBLISH');
			break;
			case 'unpublish':
				$btn_img = 'btn_cancel2.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_UNPUBLISH');
			break;
			case 'new_s':
				$btn_img = 'btn_new_s.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_NEW');
			break;
			case 'report':
				$btn_img = "btn_reports.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_REPORT');
			break;
			case 'preview':
				$btn_img = "btn_preview.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_PREVIEW');
			break;

			case 'invite':
				$btn_img = "btn_send.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_INVITE');
			break;
			case 'remaind':
				$btn_img = "btn_remaind.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_REMAIND');
			break;
			case 'email':
				$btn_img = "btn_letter.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_CR_EMAIL');
			break;
			case 'demo':
				$btn_img = "btn_demo.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : '';
			break;
			case 'results':
				$btn_img = "btn_results.png";
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : '';
			break;

			case 'new':
			default:
				$btn_img = 'btn_new.png';
				$btn_str = isset($toolbar_btn['btn_str']) ? $toolbar_btn['btn_str'] : JText::_('COM_SURVEYFORCE_SF_NEW');
			break;
		}
		$btn_wh = 14;

		if ($toolbar_btn['btn_type'] != 'spacer') {
			$toolbar_thml .= '<a class="btn btn-small '.@$toolbar_btn['btn_class'].'" '.(isset($toolbar_btn['btn_class']) && $toolbar_btn['btn_class']=='modal'?' rel="{handler: \'iframe\', size: {x:800, y:600}}"': '')." href=\"".$toolbar_btn['btn_js']."\" title=\"".$btn_str."\" style=\"margin-bottom: 5px;\">";
			$toolbar_thml .= "<span><img class='SF_png' src='".JUri::base()."/components/com_surveyforce/assets/images/buttons/".$btn_img."' width='".$btn_wh."' height='".$btn_wh."' border='0' alt=\"".$btn_str."\" title=\"".$btn_str."\" style=\"padding-right: 5px;\"/></span>";
			$toolbar_thml .= $btn_str;
			$toolbar_thml .= "</a>";
		}
		else {
			$toolbar_thml .= "<img class='SF_png' src='".JUri::base()."/components/com_surveyforce/assets/images/buttons/spacer.png' border='0' width='2px' height='10px' />";
		}
	}
	$toolbar_thml .= "</div>";
	return $toolbar_thml;
}

class survey_force_front_html {
	
	function SF_uploadImage( $option ) {
		
		$css = mosGetParam($_REQUEST,'t','');
		?>
		<link rel="stylesheet" href="../../templates/<?php echo $css; ?>/css/template_css.css" type="text/css" />
		<form method="post" action="index.php" enctype="multipart/form-data" name="filename" class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="userfile"><?php echo JText::_('COM_SURVEYFORCE_FILE_UPLOAD')?>:</label>
				<div class="controls">
					<input class="inputbox" name="userfile" id="userfile" type="file" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="userfile"></label>
				<div class="controls">
					<input class="button" type="submit" value="<?php echo JText::_('COM_SURVEYFORCE_UPLOAD'); ?>" name="fileupload" />
					<?php echo JText::_('COM_SURVEYFORCE_MAX_SIZE'); ?><?php echo ini_get( 'post_max_size' );?>
				</div>
			</div>

			<input type="hidden" name="t" value="<?php echo $css?>">
			<input type="hidden" name="task" value="uploadimage">
			<input type="hidden" name="option" value="com_surveyforce">
			<input type="hidden" name="no_html" value="1">
			<input type="hidden" name="tmpl" value="component">
			<input type="hidden" name="view" value="authoring">
		</form>
<?php
	} 

	function SF_showCatsList( &$rows, &$pageNav, $option ) {
		
		?>
		<script type="text/javascript" language="javascript">
			
			var checkItem = function(element, task)
			{
				var form = document.adminForm;
				form.boxchecked.value = form.boxchecked.value + 1;

				var inputbox = jQuery(element).parent().parent().prev().prev().prev().find('input[type="checkbox"]');
				
				inputbox.prop('checked', true);
				inputbox.attr('checked', 'checked');

				Joomla.submitbutton(task);
				return false;
			}

			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if ( ((pressbutton == 'edit_cat') || (pressbutton == 'del_cat') ) && (form.boxchecked.value == "0")) {
					alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
				} else {
					form.task.value = pressbutton;
					form.submit();
				}
			} 
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
				$toolbar = array();
				
				$additionBottom = _PN_DISPLAY_NR . $pageNav->getLimitBox( "index.php?option=com_surveyforce&task=categories" ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';
				SF_showTop('categories', JText::_('COM_SURVEYFORCE_SF_CAT_LIST'), $toolbar, $additionBottom, '', 'new_category');
			?>

		<table width="100%" class="table table-striped table-hover">
			<thead>
				<tr>
					<th width="20px">#</th>
					<th width="20px"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th align="left" width="20%" ><?php echo JText::_('COM_SURVEYFORCE_SF_NAME')?></th>
					<th align="right" width="60%" ><?php echo JText::_('COM_SURVEYFORCE_SF_USERNAME')?></th>
					<th>Action</th>
				</tr>
			<thead>
			<tbody>
				<?php
				$k = 2;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link 	= JRoute::_("index.php?option=com_surveyforce&task=surveys&catid=". $row->id);
					$checked = JHtml::_('grid.id', $i, $row->id);?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
							<span>
								<?php echo mosToolTip( $row->sf_catdescr, JText::_('COM_SURVEYFORCE_SF_CAT_DESCRIPTION'), 280, 'tooltip.png', $row->sf_catname, $link );?>
							</span>
						</td>
						<td><?php echo $row->name; ?></td>
						<td>
							<div class="btn-group">
								<button class="btn btn-default" type="button" onclick="checkItem(this, 'edit_cat'); return false;"><i class="sf-icon-pencil"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_EDIT')?></button>
							</div>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}?>
			</tbody>
		</table>
		<div class="pagination" style="margin-left:30%;">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=categories";
			echo $pageNav->writePagesLinks($link).'<br/>';
			?>
		</div>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="categories" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		
		</form><br />
<br />

		</div>
		<?php
	}
	
	function SF_editCategory( &$row, &$lists, $option ) {

		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_cat') {
				form.task.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			if (TRIM_str(form.sf_catname.value) == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_ENTER_CAT_NAME') ?>" );
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('save_cat');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");
			$toolbar[] = array('btn_type' => 'apply',  'btn_js' => "Joomla.submitbutton('apply_cat');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),  "btn_ico" => "sf-icon-ok");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_cat');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

			$additionBottom = '';
			$headTitle = ($row->id ? JText::_('COM_SURVEYFORCE_SF_EDIT_CAT') : JText::_('COM_SURVEYFORCE_SF_NEW_CAT'));
			SF_showTop('categories', $headTitle, $toolbar, $additionBottom);
			?>

			<fieldset>
				<legend><?php echo JText::_('COM_SURVEYFORCE_SF_CAT_DETAILS')?></legend>

				<div class="control-group">
					<label class="control-label" for="sf_catname"><?php echo JText::_('COM_SURVEYFORCE_SF_NAME')?>:</label>
					<div class="controls">
						<input class="inputbox" type="text" name="sf_catname" id="sf_catname" size="30" maxlength="100" value="<?php echo $row->sf_catname; ?>" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="sf_catname"><?php echo JText::_('COM_SURVEYFORCE_SF_DESCRIPTION')?>:</label>
					<div class="controls">
						<textarea class="text_area" name="sf_catdescr" cols="60" rows="5"><?php echo $row->sf_catdescr; ?></textarea>
					</div>
				</div>
			</fieldset>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="user_id" value="<?php echo $row->user_id; ?>" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form><br />
<br />

		</div>
		<?php
	}
	
	function SF_showSurvsList( &$rows, &$lists, &$pageNav, $option, $is_i = false ) {

		$Itemid = JFactory::getApplication()->input->get("Itemid");
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		?>
		<script type="text/javascript" language="javascript">

			var checkItem = function(element, task)
			{
				var form = document.adminForm;
				form.boxchecked.value = form.boxchecked.value + 1;

				var inputbox = jQuery(element).parent().parent().parent().parent().parent().prev().find('input[type="checkbox"]');
				
				inputbox.prop('checked', true);
				inputbox.attr('checked', 'checked');

				Joomla.submitbutton(task);
				return false;
			}

			Joomla.submitbutton = function (task) {
				var form = document.adminForm;
				if ( ((task == 'preview_survey') || (task == 'show_results') || (task == 'view_rep_surv') || (task == 'edit_surv') || (task == 'del_surv') || (task == 'copy_surv_sel') || (task == 'move_surv_sel') || (task == 'unpublish_surv') || (task == 'publish_surv') ) && (form.boxchecked.value == "0")) {
					alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
				} else {
					if (task == 'del_surv') {
						if(confirm("<?php echo JText::_("COM_SURVEYFORCE_SF_ARE_SURE_TO_DELETE");?>")) {
							form.task.value = task;
							form.submit();
							form.target = "";
							form.task.value = 'surveys';
							return;
						}
						return;
					}
					
					if (task == 'preview_survey') {
						form.target = "_blank";	
					}
					form.task.value = task;
					form.submit();
					form.target = "";
					form.task.value = 'surveys';
				}
			} 
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
		<?php
		$toolbar = array();	

		$link = "index.php?option=com_surveyforce&task=surveys";
		$additionBottomRight = _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';

		if (!$sf_config->get('sf_enable_jomsocial_integration')) {
			$additionBottomLeft = JText::_('COM_SURVEYFORCE_SF_SEARCH').':&nbsp;'.$lists['category'];
		}

		SF_showTop('surveys', "", $toolbar, $additionBottomRight, @$additionBottomLeft, 'new_survey');
		?>
		<table width="100%" class="table table-striped table-hover">
			<thead>
				<tr>
					<th width="20px" class="center">ID</th>
					<th width="20" class="center hidden-phone"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th width="30%"><?php echo JText::_('COM_SURVEYFORCE_SF_NAME')?></th>
					<th class="center"><?php echo JText::_('COM_SURVEYFORCE_SF_ACTIVE')?></th>
					<?php if (!$sf_config->get('sf_enable_jomsocial_integration')) { ?>
						<th class="center hidden-phone"><?php echo JText::_('COM_SURVEYFORCE_SF_CATEGORY')?></th>
					<?php }?>
					<th class="center hidden-phone"><?php echo JText::_('COM_SURVEYFORCE_SF_ACCESS')?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];

					if ( !JFactory::getApplication()->input->get('task', 'surveys') || JFactory::getApplication()->input->get('task', 'surveys') == 'surveys') {
						$link2 	= JRoute::_("index.php?option=com_surveyforce&task=edit_surv&cid[]=". $row->id."&tmpl=component");
						$link 	= JRoute::_("index.php?option=com_surveyforce&task=questions&surv_id=". $row->id);
					} elseif (JFactory::getApplication()->input->get('task', 'surveys') == 'rep_surv') {
						$link2 	= JRoute::_("index.php?option=com_surveyforce&task=view_rep_survA&id=". $row->id);
						$link 	= '#';
					}

					$ico_published	= $row->published ? 'sf-icon-ok-circled' : 'sf-icon-cancel-circled';
					$task_published	= $row->published ? 'unpublish_surv' : 'publish_surv';
					$alt_published 	= $row->published ? JText::_('COM_SURVEYFORCE_SF_PUBLISHED')  : JText::_('COM_SURVEYFORCE_SF_UNPUBLISHED') ;
					$ico_public		= $row->sf_public ? 'sf-icon-ok' : 'sf-icon-block';
					$ico_invite		= $row->sf_invite ? 'sf-icon-ok' : 'sf-icon-block';
					$ico_reg		= $row->sf_reg ? 'sf-icon-ok' : 'sf-icon-block';
					$ico_friend		= $row->sf_friend ? 'sf-icon-ok' : 'sf-icon-block';
					$ico_spec		= $row->sf_special ? 'sf-icon-ok' : 'sf-icon-block';
					$ico_auto_pb 	= $row->sf_auto_pb  ? 'sf-icon-ok' : 'sf-icon-block';
					$checked = JHtml::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?> survey-hover">
						<td class="center"><?php echo $row->id;?></td>
						<td class="center hidden-phone"><?php echo $checked; ?></td>
						<td class="left">
							<span class="hasTooltip" title='<?php echo $row->sf_descr;?>'>
								<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo $link2?>', '_blank');"><?php echo $row->sf_name?></a>
							</span>
							<div class="small visible-phone">Category: <?php echo $row->sf_catname; ?></div>
							<div class="survey-hover-tools">
								<div class="survey-tools-left">
									<small>
										<strong><?php echo JText::_('COM_SURVEYFORCE_AUTHOR')?>:</strong><?php echo $row->username?><br/>
										<strong><?php echo JText::_('COM_SURVEYFORCE_SF_EXPIRED_ON')?>:</strong>
										<?php
										if ($row->sf_date_expired != '0000-00-00 00:00:00' && date('Y-m-d 00:00:00', strtotime('now')) > $row->sf_date_expired)
											echo '<b style="color:#FF0000">'.mosFormatDate($row->sf_date_expired, "Y-m-d").'</b>';
										elseif ($row->sf_date_expired == '0000-00-00 00:00:00')
											echo JText::_('COM_SURVEYFORCE_NEVER');
										else
											echo mosFormatDate($row->sf_date_expired, "Y-m-d");
										?>
									</small>
								</div>
								<div class="btn-group" style="float:right;">
                                    <button style="height: 34px;" data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
											<a onclick="checkItem(this, 'preview_survey');" href="javascript: void(0);">
											<i class="sf-icon-desktop"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_PREVIEW')?></a>
										</li>
										<li>
											<a onclick="checkItem(this,'show_results');" href="javascript: void(0);">
											<i class="sf-icon-chart-bar"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_VIEW_SURVEY_RESULTS')?></a>
										</li>
                                    </ul>
                                </div>
                                <div style="clear:both;"></div>
							</div>
						</td>
						<td class="center">
							<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
								<i class="<?php echo $ico_published;?>"></i>
							</a>
						</td>
						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')) { ?>
						<td class="center hidden-phone">
							<?php echo $row->sf_catname; ?>
						</td>
						<?php } ?>
						<td class="center hidden-phone">
							<?php echo JText::_('COM_SURVEYFORCE_SF_PUBLIC')?><br/>
							<?php if (!$sf_config->get('sf_enable_jomsocial_integration')) { ?>
							<?php echo JText::_('COM_SURVEYFORCE_SF_FOR_INVITED')?><br/>
							<?php } ?>
							<?php echo JText::_('COM_SURVEYFORCE_SF_FOR_REG')?><br/>
							<?php if ($sf_config->get('sf_enable_jomsocial_integration')) { ?>
							<?php echo JText::_('COM_SURVEYFORCE_SF_FOR_FRIENDS')?><br/>
							<?php } ?>
							<?php if ($lists['userlists']) { ?>
							<?php echo JText::_("COM_SURVEYFORCE_SF_FOR_USER_IN_LISTS")?>
							<?php } ?>
						</td>
						<td class="center hidden-phone">
							<i class="<?php echo $ico_public;?>"></i><br/>
							<?php if (!$sf_config->get('sf_enable_jomsocial_integration')) { ?>
							<i class="<?php echo $ico_invite;?>"></i><br/>
							<?php } ?>
							<i class="<?php echo $ico_reg;?>"></i><br/>
							<?php if ($sf_config->get('sf_enable_jomsocial_integration')) { ?>
							<i class="<?php echo $ico_friend;?>"></i><br/>
							<?php } ?>
							<?php if ($lists['userlists']) { ?>
							<i class="<?php echo $ico_spec;?>"></i>
							<?php } ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
			</tbody>
		</table>
		<div class="btn-group">
			<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li class="">
					<a onclick="Joomla.submitbutton('del_surv'); return false;" href="javascript: void(0);">
					<i class="sf-icon-trash"></i> <?php echo JText::_('COM_SURVEYFORCE_DELETE')?></a>
				</li>
				<li class="">
					<a onclick="Joomla.submitbutton('publish_surv'); return false;" href="javascript: void(0);"><i class="sf-icon-ok-circled"></i> <?php echo JText::_('COM_SURVEYFORCE_SURVEYFORCE_PUBLISH')?></a>
				</li>
				<li class="">
					<a onclick="Joomla.submitbutton('unpublish_surv'); return false;" href="javascript: void(0);">
					<i class="sf-icon-cancel-circled"></i> <?php echo JText::_('COM_SURVEYFORCE_UNPUBLISH')?></a>
				</li>
				<li class="">
					<a onclick="Joomla.submitbutton('move_surv_sel'); return false;" href="javascript: void(0);">
					<i class="sf-icon-retweet"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_MOVE')?>
					</a>
				</li>
				<li class="">
					<a onclick="Joomla.submitbutton('copy_surv_sel'); return false;" href="javascript: void(0);">
					<i class="sf-icon-docs"></i> <?php echo JText::_('COM_SURVEYFORCE_SF_COPY')?>
					</a>
				</li>
			</ul>
		</div>
		<div style="clear:both;"></div>
		<div class="pagination" style="margin-left:30%">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=surveys";
			echo $pageNav->writePagesLinks($link).'<br/>';
			?>
		</div>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get('task', 'surveys')?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		
		</form><br /><br />
		</div>
		<?php
	}
	
	function SF_editSurvey( &$row, &$lists, $option ) {
		if ($row->id && SurveyforceHelper::SF_GetUserType($row->id) != 1)
			mosRedirect( JRoute::_("index.php?option=com_surveyforce&task=surveys"));


		$query = "SELECT * FROM `#__extensions` WHERE `name` LIKE '%sf_score%' ";

		$this->database->setQuery( $query );
		$is_surveyforce_score = false;
		if ($this->database->LoadResult()) 
			$is_surveyforce_score = true;

		$sf_config = JComponentHelper::getParams('com_surveyforce');
	
		mosCommonHTML::loadCalendar();
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		jQuery(document).ready(function () {
			jQuery('#viewTabs a:first').tab('show');
		});

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_surv') {
				form.task.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			if (form.sf_name.value == ""){
				alert( "Survey must have a name." );
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
			<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<?php
				$toolbar = array();
				$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('save_surv');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");
				$toolbar[] = array('btn_type' => 'apply',  'btn_js' => "Joomla.submitbutton('apply_surv');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),  "btn_ico" => "sf-icon-ok");
				$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_surv');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

				$additionBottomRight = '';
				$additionBottomLeft = '';

				$headTitle = ($row->id ? JText::_('COM_SURVEYFORCE_SF_EDIT_SURVEY') : JText::_('COM_SURVEYFORCE_SF_NEW_SURVEY'));

				SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
				?>
				<ul class="nav nav-tabs" id="viewTabs">
					<li><a href="#tab_details-page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY_DETAILS'); ?></a></li>
					<li><a href="#tab_options-page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SF_OPTIONS'); ?></a></li>
					<li><a href="#tab_access-page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SF_ACCESS'); ?></a></li>
					<li><a href="#tab_final-page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_SF_FINAL_PAGE'); ?></a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane" id="tab_details-page">
						<div class="control-group">
							<label class="control-label" for="sf_name"><?php echo JText::_('COM_SURVEYFORCE_SF_NAME')?>:</label>
							<div class="controls">
								<input class="inputbox" type="text" id="sf_name" name="sf_name" size="50" maxlength="100" value="<?php echo $row->sf_name; ?>" />
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_descr"><?php echo JText::_('COM_SURVEYFORCE_SF_DESCRIPTION')?>:</label>
							<div class="controls">
								<?php SF_editorArea( 'editor2', $row->sf_descr, 'sf_descr', '100%;', '250', '40', '20' ) ; ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="surv_short_descr"><?php echo JText::_('COM_SURVEYFORCE_SF_SHORT_DESCRIPTION')?>:</label>
							<div class="controls">
								<?php SF_editorArea( 'editor2', $row->surv_short_descr, 'surv_short_descr', '100%;', '250', '40', '20' ) ; ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="published"><?php echo JText::_('COM_SURVEYFORCE_SF_PUBLISHED')?>:</label>
							<div class="controls">
								<?php echo $lists['published'] ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="start_date"><?php echo JText::_('COM_SURVEYFORCE_SF_EXPIRED_ON')?>:</label>
							<div class="controls">
								<?php
									if ($row->sf_date_expired && $row->sf_date_expired != '0000-00-00 00:00:00')
										$sf_date = mosFormatDate($row->sf_date_expired, "Y-m-d");
									else
										$sf_date = '';

									echo JHTML::_('calendar',(($sf_date != '-')?$sf_date:''), 'sf_date_expired','start_date','%Y-%m-%d' , array('size'=>10,'maxlength'=>"10"));
									?>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="tab_options-page">
						<div class="control-group">
							<label class="control-label" for="sf_enable_descr"><?php echo JText::_('COM_SURVEYFORCE_SF_ENABLE_DESCR')?>:</label>
							<div class="controls">
								<?php echo $lists['sf_enable_descr'] ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_enable_descr"><?php echo JText::_('COM_SURVEYFORCE_SF_IMAGE')?>:</label>
							<div class="controls">
								<?php
									$directory = 'surveyforce';
									$cur_template = JFactory::getApplication()->getTemplate();
									?>
								<?php echo $lists['images']?>
								<a style="cursor:pointer;" class="hasTooltip" title="<?php echo JText::_('COM_SF_UPLOAD')?>" onclick="popupWindow('<?php echo JRoute::_('index.php?tmpl=component&option=com_surveyforce&amp;task=uploadimage&amp;directory='.$directory.'&amp;t='.$cur_template); ?>','win1',290,140,'no');">
									<i class="sf-icon-upload"></i>
								</a>
								<br/>
								<br/>
								<div>
									<img style="max-width:350px;" src="<?php echo ($row->sf_image)?(JURI::root().'media/com_surveyforce/'.$row->sf_image): JURI::root().'components/com_surveyforce/assets/images/blank.png'?>" name="imagelib" class="img-polaroid">
								</div>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_progressbar"><?php echo JText::_('COM_SURVEYFORCE_SF_SHOW_PROGRESS')?>:</label>
							<div class="controls">
								<?php echo $lists['sf_progressbar'] ?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_TEMPLATE')?>:</label>
							<div class="controls">
								<?php echo $lists['sf_templates'] ?>
							</div>
						</div>

						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')):?>
							<div class="control-group">
								<label class="control-label" for="sf_categories"><?php echo JText::_('COM_SURVEYFORCE_SF_CATEGORY')?>:</label>
								<div class="controls">
									<?php echo $lists['sf_categories'] ?>
								</div>
							</div>
						<?php endif;?>

						<div class="control-group">
							<label class="control-label <?php echo (!$sf_config->get('sf_enable_jomsocial_integration') ? 'hasTooltip' : '');?>" <?php echo (!$sf_config->get('sf_enable_jomsocial_integration') ? 'title="'.JText::_('COM_SURVEYFORCE_SF_RANDOM_WARNING').'"' : '');?> for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_RANDOM_ORDER')?>:</label>
							<div class="controls">
								<?php echo $lists['sf_random'] ?>
							</div>
						</div>

						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')):?>
							<div class="control-group">
								<label class="control-label" for="sf_auto_pb"><?php echo JText::_('COM_SURVEYFORCE_SF_AUTO_INSERT_PB')?>:</label>
								<div class="controls">
									<?php echo $lists['sf_auto_pb'] ?>
								</div>
							</div>
						<?php else: ?>
							<input type="hidden" name="sf_auto_pb" value="0" />
						<?php endif;?>

						<div class="control-group">
							<label class="control-label" for="sf_after_start0"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_AFTER_START')?>:</label>
							<div class="controls">
								<label for="sf_after_start0">
									<input type="radio" <?php echo ($row->sf_after_start == 0 ? 'checked="checked"': '')?> name="sf_after_start" id="sf_after_start0" value="0"/>
										<?php echo JText::_('COM_SURVEYFORCE_SURVEY_AS_SHOW_MES')?>
								</label>
								<label for="sf_after_start1">
									<input type="radio" <?php echo ($row->sf_after_start == 1 ? 'checked="checked"': '')?> name="sf_after_start" id="sf_after_start1" value="1"/>
										<?php echo JText::_('COM_SURVEYFORCE_SURVEY_AS_SHOW_RES')?>
								</label>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="tab_access-page">
						<div class="control-group">
							<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_PUBLIC')?>:</label>
							<div class="controls">
								<input type="hidden" name="sf_public" value="<?php echo $row->sf_public; ?>">
								<input type="checkbox" name="sf_public_chk" onClick="javascript: this.form.sf_public.value = (this.checked)?1:0;" <?php echo ($row->sf_public == 1)?"checked":""; ?>>

								<label for="sf_pub_voting" class="extra-set">
									<?php echo JText::_('COM_SURVEYFORCE_SF_VOTING'); ?>:
									<?php echo $lists['sf_pub_voting']; ?>
								</label>

								<label for="sf_pub_control" class="extra-set">
									<?php echo JText::_('COM_SURVEYFORCE_SF_CONTROL'); ?>:
									<?php echo $lists['sf_pub_control']; ?>
								</label>

								<i class="sf-icon-info-circled hasTooltip" style="cursor: pointer;" title="Voting option will be enabled only if some control type is selected.<br><strong>Note that none of this control types ensures single voting by an advaced user. But in most cases it works.</strong>"></i>
							</div>
						</div>

						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')):?>
							<div class="control-group">
								<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_INVITED')?>:</label>
								<div class="controls">
									<input type="hidden" name="sf_invite" value="<?php echo $row->sf_invite; ?>">
									<input type="checkbox" name="sf_invite_chk" onClick="javascript: this.form['sf_invite'].value = (this.checked)?1:0;" <?php echo ($row->sf_invite == 1)?"checked":""; ?>>

									<label for="sf_inv_voting" class="extra-set">
										<?php echo JText::_('COM_SURVEYFORCE_SF_VOTING'); ?>:
										<?php echo $lists['sf_inv_voting']; ?>
									</label>
								</div>
							</div>
						<?php endif;?>

						<div class="control-group">
							<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_REG_FULL')?>:</label>
							<div class="controls">
								<input type="hidden" name="sf_reg" value="<?php echo $row->sf_reg; ?>">
								<input type="checkbox" name="sf_reg_chk" onClick="javascript: this.form['sf_reg'].value = (this.checked)?1:0;" <?php echo ($row->sf_reg == 1)?"checked":""; ?>>

								<label for="sf_reg_voting" class="extra-set">
									<?php echo JText::_('COM_SURVEYFORCE_SF_VOTING'); ?>:
									<?php echo $lists['sf_reg_voting']; ?>
								</label>
							</div>
						</div>

						<?php if ($sf_config->get('sf_enable_jomsocial_integration')):?>
							<div class="control-group">
								<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_FRIENDS')?>:</label>
								<div class="controls">
									<input type="hidden" name="sf_friend" value="<?php echo $row->sf_friend; ?>">
									<input type="checkbox" name="sf_friend_chk" onClick="javascript: this.form['sf_friend'].value = (this.checked)?1:0;" <?php echo ($row->sf_friend == 1)?"checked":""; ?>>

									<label class="extra-set" for="sf_friend_voting">
										<?php echo JText::_('COM_SURVEYFORCE_SF_VOTING'); ?>:
										<?php echo $lists['sf_friend_voting']; ?>
									</label>
								</div>
							</div>
						<?php endif;?>

						<?php if ($lists['userlists'] != null): ?>
							<div class="control-group">
								<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_USER_IN_LISTS')?>:</label>
								<div class="controls">
									<input type="hidden" name="sf_special" value="<?php echo $row->sf_special; ?>">
									<input type="checkbox" name="sf_special_chk" onClick="javascript: this.form['sf_special'].value = (this.checked)?1:0;" <?php echo ($row->sf_special)?"checked":""; ?>>

									<label class="extra-set" for="userlists">
										<?php echo $lists['userlists'] ?>
									</label>
								</div>
							</div>
						<?php endif;?>
					</div>

					<div class="tab-pane" id="tab_final-page">
						<div class="control-group">
							<label class="control-label" for="sf_templates"><?php echo JText::_('COM_SURVEYFORCE_SF_FINAL_PAGE')?>:</label>
							<div class="controls">
								<label for="sf_fpage_type_1">
									<input type="radio" <?php echo ($row->sf_fpage_type == 1 ? 'checked="checked"': '')?> name="sf_fpage_type" id="sf_fpage_type_1" value="1"/>
										<?php echo JText::_('COM_SURVEYFORCE_SF_SHOW_RESULTS')?>
								</label>

								<?php if ($is_surveyforce_score): ?>
									<label for="sf_fpage_type_2">
										<input type="radio" <?php echo ($row->sf_fpage_type == 2 ? 'checked="checked"': '')?> name="sf_fpage_type" id="sf_fpage_type_2" value="2"/>
											<?php echo JText::_('COM_SURVEYFORCE_SF_SHOW_SCORE_RESULTS')?>
									</label>
								<?php endif;?>

								<label for="sf_fpage_type_0">
									<input type="radio" <?php echo ($row->sf_fpage_type == 0 ? 'checked="checked"': '')?> name="sf_fpage_type" id="sf_fpage_type_0" value="0"/>
										<?php echo JText::_('COM_SURVEYFORCE_SF_SHOW_THIS')?>
								</label>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_progressbar"><?php echo JText::_('COM_SURVEYFORCE_SF_FINAL_PAGE').' '.JText::_("COM_SURVEYFORCE_SF_TEXT")?>:</label>
							<div class="controls">
								<?php SF_editorArea( 'editor3', ($row->sf_fpage_text == null ? '<strong>End of the survey � Thank you for your time.</strong>' : $row->sf_fpage_text), 'sf_fpage_text', '100%;', '250', '40', '20' ) ; ?>
							</div>
						</div>
					</div>
				</div>

				<div>
					<input type="hidden" name="sf_author" value="<?php echo $row->sf_author; ?>" />
					<input type="hidden" name="option" value="com_surveyforce" />
					<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="task" value="" />
				</div>
			</form>
		</div>
		<?php
	}
	
	function SF_moveSurvey_Select( $option, $cid, $CategoryList, $items ) {
		global $Itemid, $Itemid_s;
		
		?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'move_surv_sel'?'move_surv_save':'copy_surv_save')."');", "btn_str" => 'Save', 'btn_ico' => '');
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_surv');", "btn_str" => 'Cancel', 'btn_ico' => '');

			$additionBottomRight = '';
			$additionBottomLeft = '';

			if (JFactory::getApplication()->input->get('task') == 'move_surv_sel') {
				$headTitle= JText::_('COM_SURVEYFORCE_SF_MOVE_SURVEY');
			} elseif (JFactory::getApplication()->input->get('task') == 'copy_surv_sel') {
				$headTitle= JText::_('COM_SURVEYFORCE_SF_COPY_SURVEY');
			}

			SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		<table width="100%" class="table table-striped">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong><?php echo JText::_('COM_SURVEYFORCE_SF_COPYMOVE_TO')?>:</strong>
			<br />
			<?php echo $CategoryList ?>
			<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
			<strong><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEYS_BEING')?>:</strong>
			<br />
			<?php
			echo "<ol>";
			foreach ( $items as $item ) {
				echo "<li>". $item->sf_name ." (".$item->sf_catname.")</li>";
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top">
			<?php echo JText::_('COM_SURVEYFORCE_SF_THIS_WILL_COPYMOVE')?>
			</td>
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="task" value="" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_showQuestsList( &$rows, &$lists, &$pageNav, $option ) {
		$owner = SurveyforceHelper::SF_GetUserType($lists['survid']) == 1;
		$sf_config = JComponentHelper::getParams('com_surveyforce');

		$saveOrderingUrl = 'index.php?option=com_surveyforce&task=authoring.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'questionList', 'adminForm', 'asc', $saveOrderingUrl);
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if ( ((pressbutton == 'publish_quest') || (pressbutton == 'unpublish_quest') || (pressbutton == 'del_quest') || (pressbutton == 'edit_quest') || (pressbutton == 'move_quest_sel') || (pressbutton == 'copy_quest_sel') ) && (form.boxchecked.value == "0")) {
					alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
					return;
			}
				
			if (pressbutton == 'add_new') {

				form = document.adminForm2;
			}
			if ( ((pressbutton == 'move_quest_sel') || (pressbutton == 'copy_quest_sel')) && (form.boxchecked.value == "0")) {
				alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
		<?php
		$toolbar = array();
		if ($owner) {
			if (!$sf_config->get('sf_enable_jomsocial_integration')) {
				$toolbar[] = array('btn_type' => 'new',  'btn_js' => "Joomla.submitbutton('add_new_section');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_NEW_SECTION"),    "btn_ico" => "sf-icon-plus");

				$toolbar[] = array('btn_type' => 'divider', 'btn_js' => "", "btn_str" => "", "btn_ico" => "");
			}
			$toolbar[] = array('btn_type' => 'new',  'modal' => '1', 'btn_href' => 'index.php?option=com_surveyforce&task=new_question_type&tmpl=component', 'btn_js' => "",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_NEW_QUESTION"),    "btn_ico" => "sf-icon-plus");
			$toolbar[] = array('btn_type' => 'edit', 'btn_js' => "Joomla.submitbutton('edit_quest');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_EDIT"),   "btn_ico" => "sf-icon-pencil");
			$toolbar[] = array('btn_type' => 'del',  'btn_js' => "Joomla.submitbutton('del_quest');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_DELETE"), "btn_ico" => "sf-icon-trash");

			$toolbar[] = array('btn_type' => 'divider', 'btn_js' => "", "btn_str" => "", "btn_ico" => "");

			$toolbar[] = array('btn_type' => 'publish',   'btn_js' => "Joomla.submitbutton('publish_quest');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_PUBLISH"),   "btn_ico" => "sf-icon-ok-circled");
			$toolbar[] = array('btn_type' => 'unpublish', 'btn_js' => "Joomla.submitbutton('unpublish_quest');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_UNPUBLISH"), "btn_ico" => "sf-icon-cancel-circled");
		}

		if (!$sf_config->get('sf_enable_jomsocial_integration')) {
			$toolbar[] = array('btn_type' => 'divider', 'btn_js' => "", "btn_str" => "", "btn_ico" => "");

			if ($owner) {
				$toolbar[] = array('btn_type' => 'move', 'btn_js' => "Joomla.submitbutton('move_quest_sel');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_MOVE"), "btn_ico" => "sf-icon-retweet");
			}
			$toolbar[] = array('btn_type' => 'copy', 'btn_js' => "Joomla.submitbutton('copy_quest_sel');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_COPY"), "btn_ico" => "sf-icon-docs");
		}

		$link = "index.php?option=com_surveyforce&task=questions&surv_id=".$lists['survid'];
		$additionBottomRight = _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';
		$additionBottomLeft = JText::_('COM_SURVEYFORCE_SF_FILTER').': '.$lists['survey'];

		$headTitle= JText::_('COM_SURVEYFORCE_SF_LIST_QUESTS');

		SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);	
		?>

		<table width="100%" class="table table-striped">
		<tr>	
			<td align="left"><?php if (!$sf_config->get('sf_enable_jomsocial_integration')) { echo $lists['sf_auto_pb_on']; }?></td>
			<td align="right"><?php if ($lists['survid'] > 0) {?><?php echo JText::_('COM_SURVEYFORCE_COM_SF_LINK_FOR_THIS_SURVEY')?> <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&survey='.$lists['survid']);?>"><?php echo JRoute::_('index.php?option=com_surveyforce&survey='.$lists['survid']);?></a><?php }?></td>
		</tr>
		</table>

		<table width="100%" class="table table-striped" id="questionList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'ordering', 'ordering', 'asc', null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="20px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>

					<th width="2%"><?php echo JText::_('COM_SURVEYFORCE_SF_TEXT')?></th>
					<th width="32%">&nbsp;</th>
					<th class="center"><?php echo ucfirst(JText::_('COM_SURVEYFORCE_SF_PUBLISHED'));?></th>
					<!--<th colspan="2" width="5%"><?php /*echo JText::_('COM_SURVEYFORCE_SF_REORDER')*/?></th>
					<th width="2%"><?php /*echo JText::_('COM_SURVEYFORCE_SF_ORDER')*/?></th>
					<th width="1%">
						<a href="javascript: <?php /*echo ($lists['survid'] > 0 && $owner? 'saveorder('.count( $rows ).')' : ' void(0); ' )*/?>"><img src="components/com_surveyforce/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a>
					</th>-->
					<th class="center"><?php echo JText::_('COM_SURVEYFORCE_SF_TYPE')?></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY')?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				$s = 1;
				$ii = 0;
				$jj = 0;
				$first = true;
				$last = true;

				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$last = (isset($rows->last)?$rows->last:true);

					if ( isset($row->published) )
					{
						$ico_published	= $row->published ? 'sf-icon-ok-circled' : 'sf-icon-cancel-circled';
						$task_published	= $row->published ? 'unpublish_quest' : 'publish_quest';
						$alt_published 	= $row->published ? JText::_('COM_SURVEYFORCE_SF_PUBLISHED')  : JText::_('COM_SURVEYFORCE_SF_UNPUBLISHED') ;
					}

					if ( !isset($row->sf_section_id)) {
						$checked = '<input type="checkbox" id="cbs'.$ii.'" name="sec[]" value="'.$row['id'].'" onclick="isChecked(this.checked);" />';
						$link = JRoute::_('index.php?option=com_surveyforce&task=questions#');
						if ($owner)
							$link = JRoute::_('index.php?option=com_surveyforce&view=authoring&task=editA_sec&id='.$row['id']);
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td class="order nowrap center hidden-phone">
								<?php
								$disabledLabel = '';
								$disabledClassName = '';
								
								/*if (!$sortedByOrder)
								{
									$disabledLabel = JText::_('JORDERINGDISABLED');
									$disabledClassName = 'inactive tip-top';
								}*/
								?>
								<span class="sortable-handler hasTooltip <?php echo $disabledClassName; ?>" title="<?php echo $disabledLabel; ?>">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
							</td>
							<td><?php echo $checked; ?></td>

							<td align="left" colspan="2"><a title="Edit Section" href='<?php echo $link?>'><?php echo $row['sf_name']?></a></td>
							<!--<td align="center"></td>
							<td align="center"><?php /*if (!isset($row['first']) && $owner  && $row['quest_id'] != '') echo '<a href="#reorder" onClick="return listItemTask(\'cbs'.$ii.'\',\'orderupS\')" title="Move Up Section"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow_s.png" width="16" height="16" border="0" alt="Move Up Section"></a>'; */?></td>
							<td align="center"><?php /*if (!isset($row['end']) && $owner  && $row['quest_id'] != '') echo '<a href="#reorder" onClick="return listItemTask(\'cbs'.$ii.'\',\'orderdownS\')" title="Move Down Section"><img src="components/com_surveyforce/assets/images/toolbar/btn_downarrow_s.png" width="16" height="16" border="0" alt="Move Down Section"></a>'; */?></td>
							<td align="center" colspan="2">
							<input type="text" name="orderS[]" size="4" value="<?php /*echo $row['ordering'] */?>" <?php /*echo ($owner  && $row['quest_id'] != ''?'':'disabled="disabled"')*/?> class="inputbox" style="text-align: center; background-color: #FFFAEC;width:30px" />
							</td>-->
							<td align="left">&nbsp;Section</td>
							<td align="left"><?php echo $row['survey_name']; ?></td>
						</tr>
						<?php
						$ii++;
						$k = 3 - $k;
					}
					else {
						$link 	= JRoute::_('index.php?option=com_surveyforce&view=authoring&task=edit_quest&cid[0]='. $row->id);
						$checked = JHtml::_('grid.id', $jj, $row->id);

						?>
						<tr class="<?php echo "row$k"; ?>">
							<td class="order nowrap center hidden-phone">
								<?php
								$disabledLabel = '';
								$disabledClassName = '';
								
								/*if (!$sortedByOrder)
								{
									$disabledLabel = JText::_('JORDERINGDISABLED');
									$disabledClassName = 'inactive tip-top';
								}*/
								?>
								<span class="sortable-handler hasTooltip <?php echo $disabledClassName; ?>" title="<?php echo $disabledLabel; ?>">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
							</td>
							<td align="center"><?php
								if ($lists['survid'] > 0 && $i > 0 && $row->sf_section_id == @$rows[$i-1]->sf_section_id && $row->sf_section_id > 0) {
									echo $s;
									$s++;
								}
								elseif ($lists['survid'] > 0 && $row->sf_section_id > 0) {
									$s = 1;
									echo $s;
									$s++;
								}
								elseif ($row->sf_section_id == 0 || $lists['survid'] < 1){
									echo $checked;
								}
							?>
							</td>
							<?php echo ($row->sf_section_id > 0 && $lists['survid'] > 0 ? "<td>$checked</td>":''); ?>
							<td align="left" <?php echo ($row->sf_section_id > 0 && $lists['survid'] > 0? '':'colspan="2"')?>>
							<?php
							$txt_for_tip = '';
							if (!$sf_config->get('sf_enable_jomsocial_integration')) {
								if ( $row->sf_qtype == 7 )
									$txt_for_tip = '<b>'.JText::_('COM_SURVEYFORCE_SF_QUEST_TEXT').':</b><br/>'.$row->sf_qtext;
								elseif ( $row->sf_qtype == 8 )
									$txt_for_tip = '<b>'.trim(strip_tags($row->sf_qtext)).'</b><br/>';
								else
									$txt_for_tip = '<b>'.JText::_('COM_SURVEYFORCE_SF_IMP_SCALE_NOT_DEF').'</b>';
								if ($row->sf_impscale) {
									$txt_for_tip = "<b>".mysql_escape_string(nl2br($row->iscale_name))."</b><br>";
									$tot = $row->total_iscale_answers;
									$txt_for_tip .= "<table width=\'100%\' cellpadding=0 cellspacing=0 border=0>";
									foreach ($row->answer_imp as $arow) {
										$txt_for_tip .= "<tr><td width=\'85%\'>".$arow->ftext.":</td><td><b>".$arow->ans_count . "</b></td></tr>";
									}
									$txt_for_tip .= "</table>";
								}
								?>

								<?php echo mosToolTip( $txt_for_tip, JText::_('COM_SURVEYFORCE_SF_QUEST_RANK'), 280, 'tooltip.png', (strlen(trim(strip_tags($row->sf_qtext))) > 100 ? mb_substr(trim(strip_tags($row->sf_qtext)), 0, 100).'...': trim(strip_tags($row->sf_qtext))), $link );?>
							<?php } else { ?>
								<a href="<?php echo $link; ?>" ><?php echo (strlen(trim(strip_tags($row->sf_qtext))) > 100 ? mb_substr(trim(strip_tags($row->sf_qtext)), 0, 100).'...': trim(strip_tags($row->sf_qtext))); ?></a>
							<?php }?>
							</td>
							<td class="center">
								<?php if ( $owner) {?>
								<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $jj;?>','<?php echo $task_published;?>')">
									<i class="<?php echo $ico_published;?>"></i>
								</a>
								<?php }?>
							</td>

							<!--<td align="center">
							<?php /*if ((($jj+$pageNav->limitstart > 0)) && $first && $lists['survid'] > 0 && $owner)
									echo '<a href="#reorder" onClick="return listItemTask(\'cb'.$jj.'\',\'orderup\')" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a>';
							*/?>
							</td>
							<td align="center">
							<?php /*if (($jj+$pageNav->limitstart < $pageNav->total-1) && $lists['survid'] > 0 && $owner)
										echo '<a href="#reorder" onClick="return listItemTask(\'cb'.$jj.'\',\'orderdown\')" title="Move Down"><img src="components/com_surveyforce/assets/images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="Move Down"></a>';
							*/?>
							</td>
							<td align="center" colspan="2">
							<input type="text" name="order[]" style="width:30px;" size="4" value="<?php /*echo ($row->sf_section_id > 0 && $lists['survid'] > 0? $s-1: $row->ordering);*/?>" class="inputbox" style="text-align: center; " <?php /*echo ($lists['survid'] > 0 && $owner? '' : ' disabled="disabled" ' )*/?>  />
							</td>-->
							<td class="center">&nbsp;
								<?php echo $row->qtype_full; ?>
							</td>
							<td align="left">
								<?php echo $row->survey_name; ?>
							</td>
						</tr>
						<?php
						$last = true;
						$k = 3 - $k;
						$jj++;
					}
				}?>
			</tbody>
		</table>
		<div class="pagination">
		<?php
			$link = "index.php?option=com_surveyforce&amp;task=questions&surv_id=".$lists['survid'];
			echo $pageNav->writePagesLinks($link).'<br/>';
			?>
		</div>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="questions" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_editSection( &$row, &$lists, $option ) {
		?>
		<script type="text/javascript" src="/media/system/js/core.js"></script>
		<script type="text/javascript" src="components/com_surveyforce/oassets/js/verlib_mini.js"></script>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_section') {
				form.task.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation
			if (form.sf_name.value == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SEC_MUST_HAVE_NAME')?>" );
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<?php
			$toolbar = array();

			$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('save_section');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");
			$toolbar[] = array('btn_type' => 'apply',  'btn_js' => "Joomla.submitbutton('apply_section');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),  "btn_ico" => "sf-icon-ok");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_section');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			$headTitle= ($row->id ? JText::_('COM_SURVEYFORCE_SF_EDIT_SEC') : JText::_('COM_SURVEYFORCE_SF_NEW_SEC'));

			SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>
			<fieldset>
				<legend><?php echo JText::_('COM_SURVEYFORCE_SF_SEC_DETAILS')?></legend>

				<div class="control-group">
					<label class="control-label" for="sf_name"><?php echo JText::_('COM_SURVEYFORCE_SF_NAME')?>:</label>
					<div class="controls">
						<input class="inputbox" type="text" name="sf_name" id="sf_name" size="30" maxlength="100" value="<?php echo $row->sf_name; ?>" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="sf_surveys"><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY')?>:</label>
					<div class="controls">
						<?php echo $lists['sf_surveys']; ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="sf_questions"><?php echo JText::_('COM_SURVEYFORCE_SF_QUESTIONS')?>:</label>
					<div class="controls">
						<?php echo $lists['sf_questions']; ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="surv_short_descr"><?php echo JText::_('COM_SURVEYFORCE_SF_ORDERING')?>:</label>
					<div class="controls">
						<?php echo $lists['ordering']; ?>
					</div>
				</div>
			</fieldset>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		</div>
		<?php
	
	}
	
	function SF_editQ_PluginShow( &$row, $lists, $questionHTML, $type ) {
		$owner = SurveyforceHelper::SF_GetUserType($lists['survid']) == 1;
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		jQuery(document).ready(function () {
			jQuery('#viewTabs a:first').tab('show');
		});

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_quest') {
				form.task.value = pressbutton;
				form.submit();
				return;
			}
			// do field validation

			if (false && form.sf_qtext.value == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_QUEST_MUST_HAVE_TEXT')?>" );
			} else {
				form.task.value = pressbutton;
				form.submit();
			}
		}

		//-->
		</script>
		<div class="contentpane surveyforce">
			<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
				<?php
				$toolbar = array();
				if ($owner) {
					$toolbar[] = array('btn_type' => 'save',  'btn_js' => "Joomla.submitbutton('save_quest');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),    "btn_ico" => "sf-icon-ok-circled");
					$toolbar[] = array('btn_type' => 'apply', 'btn_js' => "Joomla.submitbutton('apply_quest');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),   "btn_ico" => "sf-icon-ok");
				}
				$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_quest');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"),   "btn_ico" => "sf-icon-cancel-circled");

				$additionBottomRight = '';
				$additionBottomLeft = '';

				$headTitle= ($row->id ? JText::_('COM_SURVEYFORCE_SF_EDIT_QUEST') : JText::_('COM_SURVEYFORCE_SF_NEW_QUEST')).' ('.$type->sf_qtype.')';

				SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
				?>
				<ul class="nav nav-tabs" id="viewTabs">
					<li><a href="#tab_survey-page" data-toggle="tab"><?php echo  JText::_('COM_SURVEYFORCE_QUEST_DETAILS'); ?></a></li>
					<?php if (!empty($questionHTML)):?>
						<li><a href="#tab_question-page" data-toggle="tab"><?php echo  JText::_('COM_SURVEYFORCE_SF_ANSWER'); ?></a></li>
					<?php endif;?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane" id="tab_survey-page">
						<div class="control-group">
							<label class="control-label" for="sf_qtext"><?php echo JText::_('COM_SURVEYFORCE_SF_QUEST_TEXT')?>:</label>
							<div class="controls">
							<?php if ($row->sf_qtype == 4) : ?>
								<span class="label label-info"><?php echo JText::_('COM_SURVEYFORCE_SF_SHORT_ANS_TOOLTIP')?></span><br/><br/>
							<?php endif;?>
								<?php
								if ($owner)
									SF_editorArea( 'editor2', $row->sf_qtext, 'sf_qtext', '100%;', '250', '40', '20' );
								else
									echo $row->sf_qtext;
								?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_survey"><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY')?>:</label>
							<div class="controls">
								<?php echo $lists['survey'];?>
							</div>
						</div>

						<?php if (!$sf_config->get('sf_enable_jomsocial_integration')):?>
							<div class="control-group">
								<label class="control-label" for="sf_impscale"><?php echo JText::_('COM_SURVEYFORCE_SF_IMP_SCALE')?>:</label>
								<div class="controls">
									<?php echo $lists['impscale'];?>
									<?php if ($owner): ?>
										<input type="button" class="btn" name="<?php echo JText::_('COM_SURVEYFORCE_SF_DEFINE_NEW')?>" onClick="javascript: document.adminForm.task.value='add_iscale_from_quest';document.adminForm.submit();" value="<?php echo JText::_('COM_SF_DEFINE_NEW')?>">
									<?php endif;?>
								</div>
							</div>
						<?php endif;?>

						<div class="control-group">
							<label class="control-label" for="sf_published"><?php echo JText::_('COM_SURVEYFORCE_SF_PUBLISHED')?>:</label>
							<div class="controls">
								<?php echo $lists['published'];?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="sf_ordering"><?php echo JText::_('COM_SURVEYFORCE_SF_ORDERING')?>:</label>
							<div class="controls">
								<?php echo $lists['ordering'];?>
							</div>
						</div>

						<?php if ($lists['sf_section_id'] != null):?>
							<div class="control-group">
								<label class="control-label" for="sf_sf_section_id"><?php echo JText::_('COM_SURVEYFORCE_SF_SECTION')?>:</label>
								<div class="controls">
									<?php echo $lists['sf_section_id'];?>
								</div>
							</div>
						<?php endif;?>

						<div class="control-group">
							<label class="control-label" for="sf_compulsory"><?php echo JText::_('COM_SURVEYFORCE_SF_COMPULSORY')?>:</label>
							<div class="controls">
								<?php echo $lists['compulsory'];?>
							</div>
						</div>

						<?php if (!($row->id > 0)): ?>
							<div class="control-group">
								<label class="control-label" for="sf_insert_pb"><?php echo JText::_('COM_SURVEYFORCE_SF_INSERT_PAGE_BREAK')?>:</label>
								<div class="controls">
									<?php echo $lists['insert_pb'];?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<?php
						if (!empty($questionHTML) )
						{
							echo '<div class="tab-pane" id="tab_question-page">';
							echo $questionHTML;
							echo '</div>';
						}
						?>

				</div>

				<div>
					<input type="hidden" name="sf_qtype" value="<?php echo $row->sf_qtype;?>" />
					<input type="hidden" name="id" value="<?php echo $row->id;?>" />
					<input type="hidden" name="task" value="" />

					<input type="hidden" name="surv_id" value="<?php echo $row->sf_survey;?>" />
					<input type="hidden" name="quest_id" value="<?php echo $row->id;?>" />
					<input type="hidden" name="red_task" value="<?php echo JFactory::getApplication()->input->get('task');?>" />
				</div>
			</form>
		</div>
		<?php
	}

	function SF_moveQ_Select( $option, $cid, $sec, $SurveyList, $items ) {
		global  $Itemid,$Itemid_s;
		
		?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-vertical move-q">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'move_quest_sel'?'move_quest_save':'copy_quest_save')."');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_section');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			if (JFactory::getApplication()->input->get('task') == 'move_quest_sel') {
				$headTitle= JText::_("COM_SURVEYFORCE_SF_MOVE_QUEST");
			} elseif (JFactory::getApplication()->input->get('task') == 'copy_quest_sel') {
				$headTitle= JText::_("COM_SURVEYFORCE_SF_COPY_QUEST");
			}

			SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

			<div class="block">
				<div class="control-group">
					<label class="control-label" for="sf_name"><?php echo JText::_("COM_SURVEYFORCE_SF_COPYMOVE_TO_SURVEY")?>:</label>
					<div class="controls">
						<?php echo $SurveyList ?>
					</div>
				</div>
			</div>
			<div class="block">
				<div class="control-group">
					<label class="control-label" for="sf_name"><?php echo JText::_('COM_SURVEYFORCE_SF_QUEST_BEING_COPYMOVE')?>:</label>
					<div class="controls">
						<?php
						echo "<ol>";
						foreach ( $items as $item ) {
							echo "<li>". $item->sf_qtext ." (".$item->survey_name.")</li>";
						}
						echo "</ol>";
						?>
					</div>
				</div>
			</div>
			<div class="block last">
				<div class="control-group">
					<label class="control-label" for="sf_name"><?php echo JText::_('COM_SURVEYFORCE_SF_THIS_WILL_COPYMOVE_QUESTS')?></label>
					<div class="controls"><!--////--></div>
				</div>
			</div>


		<input type="hidden" name="task" value="" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		foreach ( $sec as $id ) {
			echo "\n <input type=\"hidden\" name=\"sec[]\" value=\"$id\" />";
		}
		?>
		
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_showSetDefault( &$row, &$lists, $option ) {
		global  $Itemid,$Itemid_s;

		mosCommonHTML::loadCalendar();
		
		?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
		<?php
		$toolbar = array();
		$toolbar[] = array('btn_type' => 'save', 'btn_js' => "Joomla.submitbutton('save_default');");
		$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_default');");

		$additionBottomRight = '';
		$additionBottomLeft = '';

		$headTitle= JText::_("COM_SURVEYFORCE_SF_SET_DEF_ANSWERS");

		SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
		?>
			<fieldset class="adminform">
	<?php
			$className = 'plgSurvey' . ucfirst($lists['sf_qtype_plugin']);

			if (method_exists($className, 'onGetDefaultForm'))
				$return = $className::onGetDefaultForm($lists);

			if ( $return )
				echo $return;
			else
				echo '<h2>Error plugin '.$lists['sf_qtype_plugin'].'</h2>';

	?>
			</fieldset>
		<input type="hidden" name="sf_qtype" value="<?php echo $row->sf_qtype; ?>" />
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form>
		</div>
	<?php
	}

	function SF_editIScale( &$row, &$lists, $option ) {
		global  $Itemid, $Itemid_s;

		
		?>
		<script language="javascript" type="text/javascript">
		<!--

		function ReAnalize_tbl_Rows( start_index, tbl_id ) {
			start_index = 1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				var count = start_index; var row_k = 2 - start_index%2;
				for (var i=start_index; i<tbl_elem.rows.length; i++) {
					tbl_elem.rows[i].cells[0].innerHTML = count;
					Redeclare_element_inputs(tbl_elem.rows[i].cells[1]);
					if (i > 1) { 
						tbl_elem.rows[i].cells[3].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a>';
					} else { tbl_elem.rows[i].cells[3].innerHTML = ''; }
					if (i < (tbl_elem.rows.length - 1)) {
						tbl_elem.rows[i].cells[4].innerHTML = '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="components/com_surveyforce/assets/images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="Move Down"></a>';
					} else { tbl_elem.rows[i].cells[4].innerHTML = ''; }
					tbl_elem.rows[i].className = 'sectiontableentry'+row_k;
					count++;
					row_k = 3 - row_k;
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
				cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="components/com_surveyforce/assets/images/publish_x.png" width="12" height="12" border="0" alt="Delete"></a>';
				cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a>';
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
				cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="components/com_surveyforce/assets/images/publish_x.png" width="12" height="12" border="0" alt="Delete"></a>';
				cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a>';
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
				alert("Please enter text to the field.");return;
			}
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
			cell3.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="components/com_surveyforce/assets/images/publish_x.png" width="12" height="12" border="0" alt="Delete"></a>';
			cell4.innerHTML = '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a>';
			cell5.innerHTML = '';
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			ReAnalize_tbl_Rows(tbl_elem.rows.length - 2, tbl_id);
		}

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_iscale') {
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'cancel_iscale_A') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.iscale_name.value == ""){
				alert( "<?php echo JText::_("COM_SURVEYFORCE_SF_ALERT_IMP_SCALE_MUST_HAVE")?>" );
			} 
			else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save', 'btn_js' => "Joomla.submitbutton('save_iscale_A');");
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('cancel_iscale_A');");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			$headTitle= JText::_("COM_SURVEYFORCE_SF_NEW_IMP_SCALE");

			SF_showTop('surveys', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		<table width="100%" class="table table-striped">
			<tr>
				<td colspan="2"><?php echo JText::_("COM_SURVEYFORCE_SF_IMP_SCALE_DETAILS")?></td>
			</tr>
			<tr>
				<td align="left" width="20%" valign="top"><?php echo JText::_("COM_SURVEYFORCE_SF_QUEST_TEXT")?>:</td>
				<td><textarea class="text_area" rows="6" cols="60" name="iscale_name"><?php echo $row->iscale_name;?></textarea></td>
			</tr>
		</table>
		<br />
		<table width="100%" cellpadding="2" cellspacing="0" border="0"   id="qfld_tbl">
			<thead>
		<tr>
			<th width="20px" align="center">#</th>
			<th width="200px"><?php echo JText::_("COM_SURVEYFORCE_SF_SCALE_OPTION")?></th>
			<th width="20px" align="center"></th>
			<th width="20px" align="center"></th>
			<th width="20px" align="center"></th>
			<th width="auto"></th>
		</tr>
			</thead>
			<tbody>
		<?php
		$k = 1; $ii = 1; $ind_last = count($lists['sf_fields']);
		foreach ($lists['sf_fields'] as $frow) { ?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $ii?></td>
				<td align="left">
					<?php echo $frow->isf_name?>
					<input type="hidden" name="sf_hid_fields[]" value="<?php echo $frow->isf_name?>">
				</td>
				<td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="Delete"><img src="components/com_surveyforce/assets/images/publish_x.png" width="12" height="12" border="0" alt="Delete"></a></td>
				<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="Move Up"><img src="components/com_surveyforce/assets/images/toolbar/btn_uparrow.png" width="16" height="16" border="0" alt="Move Up"></a><?php } ?></td>
				<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="Move Down"><img src="components/com_surveyforce/assets/images/toolbar/btn_downarrow.png" width="16" height="16" border="0" alt="Move Down"></a><?php } ?></td>
				<td></td>
			</tr>
		<?php
		$k = 3 - $k; $ii ++;
		 } ?>
			</tbody>
		</table><br>
		<div style="text-align:left; padding-left:30px ">
			<input id="new_field" class="inputbox" style="width:205px " type="text" name="new_field">
			<input class="button" type="button" name="add_new_field" value="<?php echo JText::_("COM_SURVEYFORCE_SF_ADD")?>" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'sf_hid_fields[]');">
		</div>
		<br />
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_showListUsers( &$rows, &$lists, &$pageNav, $option ) {
		global  $Itemid,$Itemid_s;
		
		?>
		<script language="javascript" type="text/javascript">
			
			var checkItem = function(element, task)
			{
				var form = document.adminForm;
				form.boxchecked.value = form.boxchecked.value + 1;

				var inputbox = jQuery(element).parent().parent().parent().parent().prev().prev().prev().prev().prev().find('input[type="checkbox"]');
				
				inputbox.prop('checked', true);
				inputbox.attr('checked', 'checked');

				Joomla.submitbutton(task);
				return false;
			}

			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if ( ( (pressbutton == 'view_rep_list')|| (pressbutton == 'invite_users')|| (pressbutton == 'remind_users') || (pressbutton == 'edit_list') || (pressbutton == 'del_list') ) && (form.boxchecked.value == "0")) {
					alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
				} else {
					form.task.value = pressbutton;
					form.submit();
				}
			}
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			if (JFactory::getApplication()->input->get('task') == 'rep_list') {
				$toolbar[] = array('btn_type' => 'report', 'btn_js' => "Joomla.submitbutton('view_rep_list');");
				$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('reports');");
			}

			$link = "index.php?option=com_surveyforce&amp;task=usergroups";

			$additionBottomRight = _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';
			$additionBottomLeft = '';

			$headTitle= JText::_("COM_SURVEYFORCE_SF_USER_LISTS");

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft, 'new_userlist');
			?>

		<table width="100%" class="table table-striped table-hover">
			<thead>
				<tr>
					<th width="20px">#</th>
					<th width="20px"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_USER_LISTS")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_USERS")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_STARTS")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_SURVEY_INFORMATION")?></th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$link 	= "#";
					if (JFactory::getApplication()->input->get('task') == 'usergroups') {
						$link 	= JRoute::_('index.php?option='.$option."{$Itemid_s}&task=view_users&list_id=". $row->id);
					} elseif (JFactory::getApplication()->input->get('task') == 'rep_list') {
						$link 	= JRoute::_('index.php?option='.$option."{$Itemid_s}&task=view_rep_listA&id=". $row->id);
					}

					$checked = JHtml::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>" title="<?php echo JText::_("COM_SURVEYFORCE_SF_VIEWUSERS")?>">
							<?php echo $row->listname; ?>
							</a>
						</td>
						<td><?php echo $row->users_count; ?></td>
						<td><?php echo $row->total_starts; ?></td>
						<td align="left">
							<strong><?php echo JText::_('COM_SURVEYFORCE_NAME')?>:</strong><?php echo $row->survey_name; ?><br/>
							<small>
								<strong><?php echo JText::_('COM_SURVEYFORCE_AUTHOR')?>:</strong>&nbsp;<?php echo $row->author; ?><br/>
								<strong><?php echo JText::_('COM_SURVEYFORCE_CREATED')?>:</strong>&nbsp;<?php echo mosFormatDate( $row->date_created, _CURRENT_SERVER_TIME_FORMAT ); ?>
							</small>
						</td>
						<td>
							<div class="btn-group">
								<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								    <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li class="">
										<a onclick="checkItem(this, 'edit_list'); return false;" href="javascript: void(0);"><i class="sf-icon-pencil"></i><?php echo JText::_("COM_SURVEYFORCE_SF_EDIT");?></a>
									</li>
									<li>
										<a onclick="checkItem(this, 'invite_users'); return false;" href="javascript: void(0);">
										<i class="sf-icon-mail"></i><?php echo JText::_("COM_SURVEYFORCE_SF_INVITE");?></a>
									</li>
									<li>
										<a onclick="checkItem(this, 'remind_users'); return false;" href="javascript: void(0);"><i class="sf-icon-mail-alt"></i><?php echo JText::_("COM_SURVEYFORCE_SF_REMAIND");?></a>
									</li>
								</ul>
							</div>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
			</tbody>
		</table>
		<div class="btn-group">
			<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a onclick="Joomla.submitbutton('del_list'); return false;" href="javascript: void(0);">
					<i class="sf-icon-trash"></i><?php echo JText::_("COM_SURVEYFORCE_SF_DELETE");?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('emails'); return false;" href="javascript: void(0);">
					<i class="sf-icon-th-list"></i><?php echo JText::_("COM_SURVEYFORCE_SF_CR_EMAIL");?></a>
				</li>
			</ul>
		</div>
		<div style="clear:both;"></div>
		<div class="pagination" style="margin-left:30%">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=usergroups"; 
			echo $pageNav->writePagesLinks($link).'<br/>';
		?>
		</div>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get('task')?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form><br/><br/>
		</div>
		<?php
	}

	function SF_editUser( $list, $option ) {
		?>
		<script language="javascript" type="text/javascript">
			<!--

			Joomla.submitbutton = function(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel_user') {
					submitform( pressbutton );
					return;
				}

				// do field validation
				var reg_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;
				if (form.name.value == ""){
					alert( "<?php echo JText::_('COM_SF_USER_MUST_HAVE_NAME'); ?>" );
				} else if (form.lastname.value == ""){
					alert( "<?php echo JText::_('COM_SF_USER_MUST_HAVE_LASTNAME'); ?>" );
				} else if (form.email.value == ""){
					alert( "<?php echo JText::_('COM_SF_USER_MUST_HAVE_EMAIL'); ?>" );
				} else if (!reg_email.test(form.email.value)) {
					alert("<?php echo JText::_('COM_SF_PLEASE_ENTER_VALID_EMAIL'); ?>");
				} else {
					submitform( pressbutton );
				}
			}
			//-->
		</script>

		<div class="contentpane surveyforce">
			<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
				<?php
				$toolbar = array();
				$toolbar[] = array('btn_type' => 'save', 'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'add_user'?'save_user':'save_list')."');");
				if (JFactory::getApplication()->input->get('task') != 'add_user')
					$toolbar[] = array('btn_type' => 'apply', 'btn_js' => "Joomla.submitbutton('apply_list');");
				$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'add_user'?'cancel_user':'cancel_list')."');");

				$additionBottomRight = '';
				$additionBottomLeft = '';

				$headTitle= JText::_("COM_SURVEYFORCE_SF_ADD_USERS");

				SF_showTop('usergroup', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
				?>
				<table width="100%" class="table table-striped">
					<tr>
						<td colspan="2"><?php echo JText::_("COM_SURVEYFORCE_SF_USER_INFO")?></td>
					</tr>
					<tr>
						<td align="left" width="35%" valign="top"><?php echo JText::_("COM_SURVEYFORCE_SF_LIST_NAME")?>:</td>
						<td><?php echo $list->listname; ?></td>
					</tr>
				</table>
				<table width="100%" class="adminform">
					<tr>
						<th colspan="4"><?php echo JText::_('COM_SF_USER_DETAILS'); ?></th>
					</tr>
					<tr>
						<td align="right" width="20%" valign="top"><?php echo JText::_('COM_SF_USER_NAME'); ?>:</td>
						<td><input type="text" class="text_area" size="35" name="jform[name]" value=""></td>
					</tr>
					<tr>
						<td align="right" width="20%" valign="top"><?php echo JText::_('COM_SF_USER_LASTNAME'); ?>:</td>
						<td><input type="text" class="text_area" size="35" name="jform[lastname]" value=""></td>
					</tr>
					<tr>
						<td align="right" width="20%" valign="top"><?php echo JText::_('COM_SF_USER_EMAIL'); ?>:</td>
						<td><input type="text" class="text_area" size="35" name="jform[email]" value=""></td>
					</tr>
				</table>
				<input type="hidden" name="Itemid" value="" />
				<input type="hidden" name="option" value="com_surveyforce" />
				<input type="hidden" name="task" value="save_user" />
				<input type="hidden" name="list_list_id" value="<?php echo $list->id; ?>" />
				<input type="hidden" name="sf_author_id" value="<?php echo JFactory::getUser()->id?>" />
			</form><br/><br/>
		</div>
		<?php
	}

	function SF_editListUsers( &$rows, &$lists, &$sf_config, $pageNav, $option ) {
		global  $Itemid,$Itemid_s;

		?>
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_list') {
				submitform( pressbutton );
				return;
			}

			if (pressbutton == 'save_user' && (form.boxchecked.value == "0")) {
				alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
				return;
			}

			// do field validation
			if (form.listname.value == ""){
				alert( "<?php echo JText::_("COM_SURVEYFORCE_SF_ALERT_LIST_MUST_HAVE_NAME")?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal userlist">
			<?php
			$toolbar = array();

			$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'add_user'?'save_user':'save_list')."');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");

			if (JFactory::getApplication()->input->get('task') != 'add_user')
				$toolbar[] = array('btn_type' => 'apply',  'btn_js' => "Joomla.submitbutton('apply_list');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),  "btn_ico" => "sf-icon-ok");

			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('".(JFactory::getApplication()->input->get('task') == 'add_user'?'cancel_user':'cancel_list')."');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			if (JFactory::getApplication()->input->get('task') == 'add_user')
				$headTitle= JText::_("COM_SURVEYFORCE_SF_LIST_OF_USER").' - '.JText::_("COM_SURVEYFORCE_SF_ADD_USERS");
			else
				$headTitle= JText::_("COM_SURVEYFORCE_SF_LIST_OF_USER");

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

			<fieldset>
				<legend><?php echo JText::_("COM_SURVEYFORCE_SF_LIST_DETAILS")?></legend>

				<div class="control-group">
					<label class="control-label" for="listname"><?php echo JText::_('COM_SURVEYFORCE_SF_LIST_NAME')?>:</label>
					<div class="controls">
						<input type="text" class="inputbox" size="35" name="listname" id="listname" value="<?php echo $lists['listname'] ?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="survey"><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY')?>:</label>
					<div class="controls">
						<?php echo $lists['survey']; ?>
					</div>
				</div>

				<?php if(!empty($rows)): ?>
					<?php if($sf_config->get('sf_enable_lms_integration')): ?>
						<div class="control-group">
							<label class="control-label" for="is_add_lms"><input type="checkbox" name="is_add_lms" id="is_add_lms" value="1" checked> <?php echo JText::_("COM_SURVEYFORCE_SF_ADD_LMS_GROUP")?></label>
							<div class="controls">
								<?php echo $lists['lms_groups']; ?>
							</div>
						</div>
					<?php endif;?>

					<div class="control-group add-manually">
						<label class="control-label" for="is_add_manually"><input type="checkbox" name="is_add_manually" id="is_add_manually" value="1" checked> <?php echo JText::_("COM_SURVEYFORCE_SF_ADD_MANUALLY")?>:</label>
						<div class="controls"><!--////--></div>
					</div>

					<div class="control-group">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="20px">#</th>
									<th width="20px"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
									<th><?php echo JText::_("COM_SURVEYFORCE_SF_NAME")?></th>
									<th><?php echo JText::_("COM_SURVEYFORCE_SF_USERNAME")?></th>
									<th><?php echo JText::_("COM_SURVEYFORCE_SF_EMAIL")?></th>
									<th><?php echo JText::_("COM_SURVEYFORCE_SF_LAST_VISIT")?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="6">
										<div class="pagination" style="width: 100%;">
											<?php
											if(!empty($rows))
											{
												$link = "index.php?option=$option&amp;task=add_list{$Itemid_s}";
												echo $pageNav->writePagesLinks($link);

												echo $pageNav->writeLimitBox( $link );
											}
											?>
										</div>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php
							$k = 1;
							for ($i=0, $n=count($rows); $i < $n; $i++) {
								$row = $rows[$i];

								$checked = JHtml::_('grid.id', $i, $row->id, (isset($row->luid) && !is_null($row->luid) ? TRUE : FALSE));
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td class="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
									<td><?php echo $checked; ?></td>
									<td>
										<?php echo $row->name; ?>
									</td>
									<td>
										<?php echo $row->username; ?>
									</td>
									<td>
										<?php echo $row->email; ?>
									</td>
									<td>
										<?php echo $row->lastvisitDate; ?>
									</td>
								</tr>
								<?php
								$k = 3 - $k;
							}
							?>
							</tbody>
						</table>
					</div>
				<?php endif;?>
			</fieldset>

		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="add_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="id" value="<?php echo $lists['listid']; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="sf_author_id" value="<?php echo JFactory::getUser()->id?>" />				
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_show_Users( &$rows, &$lists, &$pageNav, $option ) {
		global  $Itemid, $Itemid_s;
		
		?>
		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function(pressbutton) {
				var form = document.adminForm;
				if ( ((pressbutton == 'del_user') ) && (form.boxchecked.value == "0")) {
					alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
				} else {
					form.task.value = pressbutton;
					form.submit();
				}
			}
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			
			$link = "index.php?option=com_surveyforce&task=view_users";

			$additionBottomRight = '<label for="limit">' . _PN_DISPLAY_NR . ' ' . $pageNav->getLimitBox( $link ) . '</label>';
			$additionBottomLeft = '';

			$headTitle= JText::_("COM_SURVEYFORCE_SF_USERS").' ( '.$lists['listname'].' )';

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		<table width="100%" class="table table-striped">
			<thead>
				<tr>
					<th width="20px" class="center">#</th>
					<th width="20px" class="center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_NAME")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_USERNAME")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_EMAIL")?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];

					$checked = JHtml::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td class="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td class="center"><?php echo $checked; ?></td>
						<td>
							<?php echo $row->name; ?>
						</td>
						<td>
							<?php echo $row->lastname; ?>
						</td>
						<td>
							<?php echo $row->email; ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
			</tbody>
		</table>
		<div class="btn-group">
			<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li class=""><a onclick="Joomla.submitbutton('add_user'); return false;" href="javascript: void(0);"><i class="sf-icon-plus"></i><?php echo JText::_("COM_SURVEYFORCE_ADD_USERS");?></a>
			</li>
			<li>
				<a onclick="Joomla.submitbutton('del_user'); return false;" href="javascript: void(0);">
				<i class="sf-icon-trash"></i><?php echo JText::_("COM_SURVEYFORCE_SF_DELETE");?></a>
			</li>
			<li>
				<a onclick="Joomla.submitbutton('cancel_list'); return false;" href="javascript: void(0);"><i class="sf-icon-cancel-circled"></i><?php echo JText::_("COM_SURVEYFORCE_SF_CANCEL");?></a>
			</li>
		</ul>
		</div>
		<div style="clear:both;"></div>
		<div class="pagination" style="margin-left:30%;">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=view_users"; 
			echo $pageNav->writePagesLinks($link).'<br/>';
		?>
		</div>

		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="view_users" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="list_id" value="<?php echo $lists['listid']?>" />		
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_ViewReports( &$rows, &$lists, &$pageNav, $option ) {
		global  $Itemid, $Itemid_s;
		$sf_config = JComponentHelper::getParams('com_surveyforce');
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			
			if (pressbutton == 'rep_pdf') { 
				form.target = '_blank';
				submitform( pressbutton );
				return;
			}
			
			if ( ((pressbutton == 'view_result_c')||(pressbutton == 'del_rep')) && (form.boxchecked.value == "0")) {
				alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
			}
			else {
				form.target = '';
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$link = "index.php?option=com_surveyforce&amp;task=reports";

			$additionBottomRight = _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';
			$additionBottomLeft = '<table border="0"><tr>
						<td>'.$lists['filt_status'].'</td>
						<td>'.$lists['survey'].'</td>
						<td>'.$lists['filt_utype'].'</td>
						</tr>
						<tr>
						<td colspan="2" align="right"></td>
						<td>'.$lists['filt_ulist'].'</td>
						</tr>
						<tr><td colspan="3">
							<table width="100%">
							';
								$jj = 0;
								foreach ($lists['filter_quest'] as $list1) {
								$additionBottomLeft.='<tr>
									<td width="20%">
										'.JText::_('COM_SURVEYFORCE_SF_CHOOSE_FROM_QUEST').'
									</td>
									<td>'.$list1.'</td>
									<td width="20%">';

									if (isset($lists['filter_quest_ans'][$jj])) {
										$additionBottomLeft.= JText::_('COM_SURVEYFORCE_SF_WHERE_THE_ANSWER').'</td><td>'.$lists['filter_quest_ans'][$jj];
									 } else { $additionBottomLeft.=  "</td>&nbsp;<td>&nbsp;"; }
									$jj ++;
								$additionBottomLeft.='</td></tr>';
								}
					$additionBottomLeft.='</table>
						</td></tr>
					</table>';

			$headTitle= JText::_("COM_SURVEYFORCE_SF_REPORTS");

			SF_showTop('reports', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>


		<table width="100%" class="table table-striped">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SF_DATE')?></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SF_STATUS')?></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY')?></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SF_USERTYPE')?></th>
					<th width="100"><?php echo JText::_('COM_SURVEYFORCE_SF_USER_INFO')?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 1;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];

					$link 	= JRoute::_(JURI::root().'index.php?option=com_surveyforce&view=authoring&task=view_result&id='. $row->id);
					$checked = JHtml::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
						<td><?php echo $checked; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>" title="View Results">
								<?php echo JDate::getInstance($row->sf_time)->format(JText::_('DATE_FORMAT_LC1')); ?>
							</a>
						</td>
						<td align="left">
							<?php echo ($row->is_complete)?JText::_('COM_SURVEYFORCE_SF_COMPLETED'):JText::_('COM_SURVEYFORCE_SF_NOT_COMPLETED'); ?>
						</td>
						<td align="left">
							<?php echo $row->survey_name; ?>
						</td>
						<td align="left">
							<?php switch($row->usertype) {
									case '0': echo JText::_('COM_SURVEYFORCE_SF_GUEST'); break;
									case '1': echo JText::_('COM_SURVEYFORCE_SF_REGISTERED_USER'); break;
									case '2': echo JText::_('COM_SURVEYFORCE_SF_INVITED_USER'); break;
								} ?>
						</td>
						<td align="left">
							<?php switch($row->usertype) {
									case '0': echo JText::_('COM_SURVEYFORCE_SF_ANONYMOUS'); break;
									case '1': echo $row->reg_username.", ".$row->reg_name." (".$row->reg_email.")"; break;
									case '2': echo $row->inv_name." ".$row->inv_lastname." (".$row->inv_email.")"; break;
								} ?>
						</td>
					</tr>
					<?php
					$k = 3 - $k;
				}
				?>
			</tbody>
		</table>
		<div class="btn-group">
			<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a onclick="Joomla.submitbutton('cross_rep'); return false;" href="javascript: void(0);"><i class="sf-icon-shuffle"></i><?php echo JText::_('COM_SURVEYFORCE_SF_CROSS_REPORT');?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('i_report'); return false;" href="javascript: void(0);"><i class="sf-icon-doc-text"></i><?php echo JText::_('COM_SURVEYFORCE_SF_CSV_REPORT');?></a>
				</li>

				<li hidden="true">
					<a onclick="Joomla.submitbutton('rep_surv'); return false;"  href="javascript: void(0);"><i class="sf-icon-book-open"></i><?php echo JText::_('COM_SURVEYFORCE_SF_REP_SURVEYS');?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('rep_pdf'); return false;" href="javascript: void(0);">
					<i class="sf-icon-file-pdf"></i><?php echo JText::_('COM_SURVEYFORCE_SF_PDF_REPORT');?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('rep_csv'); return false;" href="javascript: void(0);"><i class="sf-icon-doc-text"></i><?php echo JText::_('COM_SURVEYFORCE_SF_CSV_REPORT_SUM');?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('view_result_c'); return false;" href="javascript: void(0);">
					<i class="sf-icon-newspaper"></i><?php echo JText::_('COM_SURVEYFORCE_SF_REPORT');?></a>
				</li>
				<li>
					<a onclick="Joomla.submitbutton('del_rep'); return false;" href="javascript: void(0);">
					<i class="sf-icon-trash"></i><?php echo JText::_('COM_SURVEYFORCE_SF_DELETE');?></a>
				</li>
			</ul>
		</div>
		<div style="clear:both;"></div>
		<div class="pagination" style="margin-left:30%;">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=reports"; 
			echo $pageNav->writePagesLinks($link).'<br/>';
			?>
		</div>

		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="reports" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_ViewRepResult( $option, $start_data, $survey_data, $questions_data ) {
		global  $Itemid,$Itemid_s;
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'rep_print') { 
				form.target = '_blank';
				submitform( pressbutton );
				return;
			}
			
			form.target = '';
			submitform( pressbutton );
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'report', 'btn_js' => "Joomla.submitbutton('rep_print');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_PDF_REPORT'), "btn_ico" => "sf-icon-file-pdf");
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('reports');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			$headTitle= JText::_("COM_SURVEYFORCE_SF_RESULTS");

			SF_showTop('reports', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $start_data[0]->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form>

		<fieldset>
			<legend><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY_INFORMATION')?></legend>

			<div class="well sf-description">
				<h3><?php echo $survey_data[0]->sf_name?></h3>
				<?php echo nl2br($survey_data[0]->sf_descr)?>
				<blockquote>
					<small><strong><?php echo JText::_('COM_SURVEYFORCE_SF_START_AT')?></strong>: <?php echo JDate::getInstance($start_data[0]->sf_time)->format(JText::_('DATE_FORMAT_LC1'));?></small>
					<small><strong><?php echo JText::_('COM_SURVEYFORCE_SF_USER')?>:</strong>
						<?php switch($start_data[0]->usertype) {
							case '0': echo JText::_('COM_SURVEYFORCE_SF_ANONYMOUS'); break;
							case '1': echo JText::_('COM_SURVEYFORCE_SF_REGISTERED_USER').": ".$start_data[0]->reg_username.", ".$start_data[0]->reg_name." (".$start_data[0]->reg_email.")"; break;
							case '2': echo JText::_('COM_SURVEYFORCE_SF_INVITED_USER').": ".$start_data[0]->inv_name." ".$start_data[0]->inv_lastname." (".$start_data[0]->inv_email.")"; break;
						} ?>
					</small>
				</blockquote>
			</div>
		</fieldset>
		<div class="sf-answers">
		<?php
		foreach ($questions_data as $qrow) { 
			$k = 1;?>
			<blockquote>
				<?php if ($qrow->sf_qtext):?>
				<p><?php echo $qrow->sf_qtext?></p>
				<?php endif;?>
		<?php
			switch ($qrow->sf_qtype) {
				case 2:
				case 3:
					$ans = count($qrow->answer);
					foreach ($qrow->answer as $n => $arow) {
						$img_ans = $arow->alt_text ? "<img src='components/com_surveyforce/assets/images/buttons/btn_apply.png' width='12' height='12' border='0' />" : '';
						echo "<small " . ($img_ans ? 'class="label"' : '') . ">" . $arow->f_text . "</small>";
						if ($n != $ans-1) echo "<br/>";
						$k = 3 - $k;
					}
				break;
				case 1:	echo "<p><strong>Scale: " . $qrow->scale . "</strong></p>";$k = 1 - $k;
				case 5:
				case 6:
				case 9:
					$ans = count($qrow->answer);
					foreach ($qrow->answer as $n => $arow) {
						echo "<small>" . $arow->f_text . " <strong style=\"color: black;\">" . $arow->alt_text . "</strong></small>";
						if ($n != $ans-1) echo "<br/>";
						$k = 3 - $k;
					}
					break;
				case 4:
					if (isset($qrow->answer_count)){
						$tmp = JText::_('COM_SURVEYFORCE_COM_SF_FIRST_ANSWER');
						for($ii = 1; $ii <= $qrow->answer_count; $ii++) {
							if ($ii == 2) $tmp = JText::_('COM_SURVEYFORCE_COM_SF_SECOND_ANSWER');
							elseif($ii == 3)	$tmp = JText::_('COM_SURVEYFORCE_COM_SF_THIRD_ANSWER');
							elseif ($ii > 3) $tmp = $ii . JText::_('COM_SURVEYFORCE_COM_SF_X_ANSWER');
							$ans = count($qrow->answer);
							foreach($qrow->answer as $n => $answer) {
								if ($answer->ans_field == $ii) {
									echo "<small>".$tmp.nl2br(($answer->ans_txt == ''?' '.JText::_('COM_SURVEYFORCE_SURVEY_NO_ANSWER'):$answer->ans_txt))."</small>";
									if ($n != $ans-1) echo "<br/>";
									$k = 3 - $k;
									$tmp = -1;
								}
							}
							if ($tmp != -1)	{
								echo "<small>".$tmp." ".JText::_('COM_SURVEYFORCE_SURVEY_NO_ANSWER')."</small>";
								if ($ii != $qrow->answer_count) echo "<br/>";
								$k = 3 - $k;
							}
						}
					}
					else {
						echo "<small>".nl2br($qrow->answer)."</small>";
					}
					break;
				default:
					echo "<small>".nl2br($qrow->answer)."</small>";
				break;
			}
			?>
		</blockquote>
		<?php if ($qrow->sf_impscale) {?>
			<blockquote>
			<p><?php echo $qrow->iscale_name?></p>
			<?php
				$ans = count($qrow->answer_imp);
				foreach ($qrow->answer_imp as $i=>$arow) {
					$img_ans = $arow->alt_text ? "<img src='components/com_surveyforce/assets/images/buttons/btn_apply.png' width='12' height='12' border='0' />" : '';
					echo "<small " . ($img_ans ? 'class="label"' : '') . ">" . $arow->f_text . "</small>";
					if ($i != $ans-1) echo "<br/>";
					$k = 3 - $k;
				}
			?>
			</blockquote>
		<?php } ?>
		<br>
		<?php }
		?>
		</div>
		</div><?php
	}
	
	function SF_ViewRepSurv_List( $option, $survey_data, $questions_data, $is_list = 0, $list_id = 0){
		global  $Itemid,$Itemid_s;
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'rep_surv_print') { 
				form.target = '_blank';				
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'rep_list_print') { 
				form.target = '_blank';				
				submitform( pressbutton );
				return;
			}

			
			form.target = '';
			submitform( pressbutton );
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
				$toolbar = array();
				$toolbar[] = array('btn_type' => 'report', 'btn_js' => "Joomla.submitbutton('rep_surv_print');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_PDF_REPORT'), "btn_ico" => "sf-icon-file-pdf");
				$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('rep_surv');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

				$additionBottomRight = '';
				$additionBottomLeft = '';

				if ($is_list == 1) { $headTitle= JText::_('COM_SURVEYFORCE_SF_USERS');} else { $headTitle= JText::_('COM_SURVEYFORCE_SF_SURVEY');}
				$headTitle.=JText::_('COM_SURVEYFORCE_SF_RESULTS');

				SF_showTop('reports', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
				?>

			<input type="hidden" name="option" value="com_surveyforce" />
			<input type="hidden" name="id" value="<?php echo (JFactory::getApplication()->input->get('task') == 'view_rep_list')?$list_id:$survey_data->id; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form>
		<fieldset>
			<legend><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY_INFORMATION')?></legend>

			<div class="well sf-description" style="overflow: hidden;">
				<h3><?php echo $survey_data->sf_name?></h3>
				<?php echo nl2br($survey_data->sf_descr)?>
			</div>
		</fieldset>

		<br>
		<fieldset>
			<legend><?php echo JText::_('Survey statistics')?></legend>

			<?php if ($is_list == 1) { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="250px" valign="top">
							<img src="<?php echo JUri::base()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo ($survey_data->total_starts > $survey_data->total_inv_users)?$survey_data->total_starts:$survey_data->total_inv_users?>&grids=<?php echo $survey_data->total_inv_users.','.$survey_data->total_starts.','.$survey_data->total_completes?>">
						</td>
						<td valign="top">
							<div style="padding-top:1px ">
								<table cellpadding="0" cellspacing="0">
									<tr class="row1" height="25px"><td><b><?php echo $survey_data->total_inv_users?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_INVITED')?></td></tr>
									<tr class="row1" height="25px"><td><b><?php echo $survey_data->total_starts?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_STARTS')?></td></tr>
									<tr class="row1" height="25px"><td><b><?php echo $survey_data->total_completes?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_COMPLETES')?></td></tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			<?php } else { ?>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="250px" valign="top">
							<img src="<?php echo JUri::base()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo $survey_data->total_starts?>&grids=<?php echo $survey_data->total_starts.','.$survey_data->total_gstarts.','.$survey_data->total_rstarts.','.$survey_data->total_istarts.','.$survey_data->total_completes.','.$survey_data->total_gcompletes.','.$survey_data->total_rcompletes.','.$survey_data->total_icompletes?>">
						</td>
						<td valign="top"><div style="padding-top:1px ">
							<table cellpadding="0" cellspacing="0">
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_starts?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_STARTS')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_gstarts?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_STRST_GUEST')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_rstarts?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_STRAT_REG')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_istarts?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_START_INVITED')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_completes?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_COMPLETES')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_gcompletes?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_COMPL_GUEST')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_rcompletes?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_COMPL_REG')?></td></tr>
								<tr class="sectiontableentry2" height="25px"><td><b><?php echo $survey_data->total_icompletes?></b> - <?php echo JText::_('COM_SURVEYFORCE_SF_TOTAL_COMPL_INVITED')?></td></tr>
							</table>
						</td>
					</tr>
				</table>
			<?php } ?>
		</fieldset>

		<br/>

		<?php
		$tmp_data = array();
		$total = 0;
		$i = 0;
		foreach ($questions_data as $qrow) {
			switch ($qrow->sf_qtype) {
				case 2:
				case 3:
				case 4:
					if (isset($qrow->answer_count)) {
						$tmp = JText::_('COM_SURVEYFORCE_COM_SF_FIRST_ANSWER');
						?>
						<table>
							<tr>
								<td align="left"><?php echo $qrow->sf_qtext?></td>
							</tr>
						</table>
					<?php
						for($ii = 1; $ii <= $qrow->answer_count; $ii++) {
							if ($ii == 2)    $tmp = JText::_('COM_SURVEYFORCE_COM_SF_SECOND_ANSWER');
							elseif($ii == 3) $tmp = JText::_('COM_SURVEYFORCE_COM_SF_THIRD_ANSWER');
							elseif ($ii > 3) $tmp = $ii.JText::_('COM_SURVEYFORCE_COM_SF_X_ANSWER');
							$total = $qrow->total_answers;
							$i = 0;
							$tmp_data = array();
							if (count($qrow->answer[$ii-1]) > 0 ) {
								foreach ($qrow->answer[$ii-1] as $arow) {
									$tmp_data[$i] = $arow->ans_count;
									$i++;
								}
								?>
								<br>
								<table>
									<tr>
										<td align="left"><strong><?php echo $tmp?></strong></td>
									</tr>
								</table>

								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="250px" valign="top">
											<img src="<?php echo JUri::root()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo $total?>&grids=<?php echo implode(',',$tmp_data)?>">
										</td>
										<td valign="top">
											<div style="padding-top:1px ">
												<table cellpadding="0" cellspacing="0">
												<?php foreach ($qrow->answer[$ii-1] as $arow):?>
													<tr class="sectiontableentry2" height="25px">
														<td><strong><?php echo $arow->ans_count;?></strong> <?php echo $arow->ftext;?></td>
													</tr>
												<?php endforeach;?>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan='2'><strong>Other answers: </strong><?php echo $qrow->answers_top100[$ii-1];?></td>
									</tr>
								</table>
				<?php
							}
						}
					}
					else {
						$total = $qrow->total_answers;
						$i = 0;
						$tmp_data = array();
						foreach ($qrow->answer as $arow) {
							$tmp_data[$i] = $arow->ans_count;
							$i++;
						}
						?>
						<br>
						<table>
							<tr>
								<td align="left"><strong><?php echo $qrow->sf_qtext?></strong></td>
							</tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="250px" valign="top">
									<img src="<?php echo JUri::root()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo $total?>&grids=<?php echo implode(',',$tmp_data)?>">
								</td>
								<td valign="top">
									<div style="padding-top:1px ">
										<table cellpadding="0" cellspacing="0">
										<?php foreach ($qrow->answer as $arow):?>
											<tr class="sectiontableentry2" height="25px">
												<td><strong><?php echo $arow->ans_count;?></strong> <?php echo $arow->ftext;?></td>
											</tr>
										<?php endforeach;?>
										</table>
									</div>
								</td>
							</tr>
						<?php if ($qrow->sf_qtype == 4): ?>
							<tr>
								<td colspan="2"><strong>Other answers: </strong><?php echo $qrow->answers_top100;?></td>
							</tr>
						<?php endif; ?>
						</table>
				<?php
					}
				break;
				case 1:
				case 5:
				case 6:
				case 9:
					$total = $qrow->total_answers;
					?>
					<br>
					<table>
						<tr>
							<td align="left"><strong><?php echo $qrow->sf_qtext?></strong></td>
						</tr>
					</table>

					<?php
						foreach ($qrow->answer as $arows) {
							$i = 0;
							$tmp_data = array();
							foreach ($arows->full_ans as $arow) {
								$tmp_data[$i] = $arow->ans_count;
								$i++;
							}
							?>
							<table>
								<tr>
									<td align="left"><strong><?php echo $arows->ftext; ?></strong></td>
								</tr>
							</table>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="250px" valign="top">
										<img src="<?php echo JUri::root()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo $total?>&grids=<?php echo implode(',',$tmp_data)?>">
									</td>
									<td valign="top">
										<div style="padding-top:1px ">
											<table cellpadding="0" cellspacing="0">
											<?php foreach ($arows->full_ans as $arow):?>
												<tr class="sectiontableentry2" height="25px">
													<td><strong><?php echo $arow->ans_count;?></strong> <?php echo $arow->ftext;?></td>
												</tr>
											<?php endforeach;?>
											</table>
										</div>
									</td>
								</tr>
							</table>
					<?php }
				break;
			}
			if ($qrow->sf_impscale) {
				$total = $qrow->total_iscale_answers;
				$i = 0;
				$tmp_data = array();
				foreach ($qrow->answer_imp as $arow) {
					$tmp_data[$i] = $arow->ans_count;
					$i++;
				}
				?>
				<table>
					<tr>
						<td align="left"><strong><?php echo $qrow->iscale_name?></strong></td>
					</tr>
				</table>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="250px" valign="top">
							<img src="<?php echo JUri::root()?>administrator/components/com_surveyforce/includes/draw_grid.php?total=<?php echo $total?>&grids=<?php echo implode(',',$tmp_data)?>">
						</td>
						<td valign="top">
							<div style="padding-top:1px ">
								<table cellpadding="0" cellspacing="0">
								<?php foreach ($qrow->answer_imp as $arow):?>
									<tr class="sectiontableentry2" height="25px">
										<td><strong><?php echo $arow->ans_count;?></strong> <?php echo $arow->ftext;?></td>
									</tr>
								<?php endforeach;?>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<?php
			}
		}
		?>
		</div>
<?php
	}
	
	function SF_showCrossReport( $lists, $option ) {
		global  $Itemid,$Itemid_s;
		mosCommonHTML::loadCalendar();
		
		?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'report', 'btn_js' => "Joomla.submitbutton('get_cross_rep');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_REPORT'), "btn_ico" => "sf-icon-shuffle");
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('reports');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

			$additionBottomRight = '';
			$additionBottomLeft = '';

			$headTitle=JText::_('COM_SURVEYFORCE_SF_CROSS_REPORT');

			SF_showTop('report', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>
			<fieldset>
				<legend><?php echo JText::_("COM_SURVEYFORCE_SF_REPORT_DETAILS")?></legend>

				<div class="control-group">
					<label class="control-label" for="surveys"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_SURVEY')?>:</label>
					<div class="controls">
						<?php echo $lists['surveys']?>
					</div>
				</div>

			<?php if ($lists['mquest_id'] != ''):?>
				<div class="control-group">
					<label class="control-label" for="mquest_id"><?php echo JText::_('COM_SURVEYFORCE_SF_SEL_COL_QUEST')?>:</label>
					<div class="controls">
						<?php echo $lists['mquest_id']?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="cquest_id"><?php echo JText::_('COM_SURVEYFORCE_SF_SEL_QUEST_INCLUDED')?>:</label>
					<div class="controls">
						<?php echo $lists['cquest_id']?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="start_date"><?php echo JText::_('COM_SURVEYFORCE_SF_FROM_DATE')?>:</label>
					<div class="controls">
						<?php echo JHTML::_('calendar','', 'start_date','start_date','%Y-%m-%d' , array('size'=>15,'maxlength'=>"19"));	?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="end_date"><?php echo JText::_('COM_SURVEYFORCE_SF_TO_DATE')?>:</label>
					<div class="controls">
						<?php echo JHTML::_('calendar','', 'end_date','end_date','%Y-%m-%d' , array('size'=>15,'maxlength'=>"19"));?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="is_complete"><?php echo JText::_('COM_SURVEYFORCE_SF_INCLUDE_COMPL')?>:</label>
					<div class="controls">
						<input type="checkbox" name="is_complete" id="is_complete" checked="checked" value="1" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="is_notcomplete"><?php echo JText::_('COM_SURVEYFORCE_SF_INCLUDE_COMPL')?>:</label>
					<div class="controls">
						<input type="checkbox" name="is_notcomplete" id="is_notcomplete" checked="checked" value="1" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="rep_type"><?php echo JText::_('COM_SURVEYFORCE_SF_GET_REP_IN')?>:</label>
					<div class="controls">
						<select name="rep_type" id="rep_type" class="inputbox" >
							<option value="pdf" selected="selected"><?php echo JText::_('COM_SURVEYFORCE_SF_ACROBAT_PDF')?></option>
							<option value="csv"><?php echo JText::_('COM_SURVEYFORCE_SF_EXCEL_CSV')?></option>
						</select>
					</div>
				</div>
			<?php else:?>
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<?php echo JText::_('COM_SURVEYFORCE_SF_CROSS_REP_NOT_CREATED');?>
					</div>
				</div>
			<?php endif;?>
			</fieldset>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="task" value="cross_rep" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		</form><br/><br/></div>
<?php
	}
	
	function SF_showIReport( &$rows, &$lists, &$pageNav, $option, $is_i = false ) {
		global  $Itemid,$Itemid_s;
		
		
		?>
		<script type="text/javascript" language="javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.forms.adminForm;
			if ( ((pressbutton == 'view_irep_surv')) && (form.boxchecked.value == "0")) {
				alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
			}
			else {
				submitform( pressbutton );
				return;
			}
		}
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'report', 'btn_js' => "Joomla.submitbutton('view_irep_surv');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_REPORT'), "btn_ico" => "sf-icon-newspaper");
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('reports');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

			$link = "index.php?option=com_surveyforce&task=usergroups";
			$additionBottomRight = _PN_DISPLAY_NR . $pageNav->getLimitBox( $link ) . '&nbsp;'.$pageNav->writePagesCounter(1). '&nbsp;&nbsp;';
			$additionBottomLeft =  JText::_('COM_SURVEYFORCE_SF_INCLUDE_IMP_SCALE').'<br/>'.$lists['category'];

			$headTitle=JText::_('COM_SURVEYFORCE_SF_CSV_REP_SEL_SURVEY');

			SF_showTop('reports', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		<table width="100%" class="table table-striped">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="20" ><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th ><?php echo JText::_("COM_SURVEYFORCE_SF_NAME")?></th>
					<th ><?php echo JText::_("COM_SURVEYFORCE_SF_ACTIVE")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_CATEGORY")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_AUTHOR")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_PUBLIC")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_FOR_INVITED")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_FOR_REG")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_FOR_USER_IN_LISTS")?></th>
					<th><?php echo JText::_("COM_SURVEYFORCE_SF_EXPIRED_ON")?>:</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$k = 1;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$link = '#';
			$img_published	= $row->published ? 'btn_accept.png' : 'btn_cancel.png';
			$task_published	= $row->published ? 'unpublish_surv' : 'publish_surv';
			$alt_published 	= $row->published ? JText::_('COM_SURVEYFORCE_SF_PUBLISHED')  : JText::_('COM_SURVEYFORCE_SF_UNPUBLISHED') ;
			$img_public		= $row->sf_public ? 'btn_accept.png' : 'btn_cancel.png';
			$img_invite		= $row->sf_invite ? 'btn_accept.png' : 'btn_cancel.png';
			$img_reg		= $row->sf_reg ? 'btn_accept.png' : 'btn_cancel.png';
			$img_spec		= $row->sf_special ? 'btn_accept.png' : 'btn_cancel.png';
			$checked = JHtml::_('grid.id', $i, $row->id);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td align="left">
					<?php echo mosToolTip( $row->sf_descr, JText::_('COM_SURVEYFORCE_SF_SURV_DESCRIPTION'), 280, 'tooltip.png', $row->sf_name, $link );?>
				</td>
				<td align="left">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
						<img src="components/com_surveyforce/assets/images/toolbar/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
					</a>
				</td>
				<td align="left">
					<?php echo $row->sf_catname; ?>
				</td>
				<td align="left">
					<?php echo $row->username; ?>
				</td>
				<td align="left">
						<img src="components/com_surveyforce/assets/images/toolbar/<?php echo $img_public;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
				</td>
				<td align="left">
						<img src="components/com_surveyforce/assets/images/toolbar/<?php echo $img_invite;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
				</td>
				<td align="left">
						<img src="components/com_surveyforce/assets/images/toolbar/<?php echo $img_reg;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
				</td>
				<td align="left">
						<img src="components/com_surveyforce/assets/images/toolbar/<?php echo $img_spec;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
				</td>				
				<td align="left">
						<?php if ($row->sf_date_expired == "0000-00-00 00:00:00")
							echo ("");
						else
							echo ($row->sf_date_expired);?>
				</td>
			</tr>
			<?php
			$k = 3 - $k;
		}
		?>
			</tbody>
		</table>
		<div class="pagination">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=usergroups"; 
			echo $pageNav->writePagesLinks($link).'<br/>';
		?>
		</div>

		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get('task')?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form><br/><br/></div>
		<?php
	}
	
	function show_results( $rows, $lists, $option ) {
		global  $Itemid,$Itemid_s;
	?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('surveys');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

			$additionBottomRight = '';
			$additionBottomLeft =  JText::_('COM_SURVEYFORCE_SF_SURVEY').' : '.$lists['survey'];

			$headTitle=JText::_('COM_SURVEYFORCE_SF_SURVEY_RESULTS');

			SF_showTop('report', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

		<table width="100%" class="table table-striped">
		<tr>
			<td colspan="2" align="left"><?php echo JText::_('COM_SURVEYFORCE_SF_SURVEY_RESULTS')?> - <?php echo $lists['sname']?></td>
		</tr>
		</table>
		<?php foreach( $rows as $row ){
			if ($row) {	?>			
			<?php echo $row;?><br/>
		<?php } 
		} ?>
		
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="show_results" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="boxchecked" value="0" />
		</form><br/><br/></div>
	<?php
	}
	
	function SF_showEmailsList( &$rows, &$pageNav, $option ) {
		global  $Itemid,$Itemid_s;
		
		?>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm">
		<script type="text/javascript" language="javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.forms.adminForm;
			if ( ((pressbutton == 'edit_email') || (pressbutton == 'del_email')) && (form.boxchecked.value == "0")) {
				alert('<?php echo JText::_('COM_SURVEYFORCE_SF_ALERT_SELECT_ITEM');?>');
			}
			else {
				submitform( pressbutton );
				return;
			}
		}
		</script>

			<?php
			$toolbar = array();

			$additionBottomRight = '';
			$additionBottomLeft =  '';

			$headTitle=JText::_('COM_SURVEYFORCE_SF_CR_EMAIL');

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft, 'new_email');
			?>

		<table width="100%" class="table table-striped">
			<thead>
				<tr>
					<th width="20">#</td>
					<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_SUBJECT') ?></th>
					<th style="width:40%"><?php echo JText::_('COM_SURVEYFORCE_BODY') ?></th>
					<th><?php echo JText::_('COM_SURVEYFORCE_REPLY_TO') ?></th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];

				$link = JRoute::_( "index.php?option=com_surveyforce&task=editA_email&id=". $row->id);

				$checked = JHtml::_('grid.id', $i, $row->id);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo $pageNav->rowNumber( $i ); ?></td>
					<td><?php echo $checked; ?></td>
					<td>
						<a href="<?php echo $link; ?>" title="Edit email">
						<?php echo $row->email_subject; ?>
						</a>
					</td>
					<td>
						<?php echo strip_tags($row->email_body); ?>
					</td>
					<td>
						<?php echo $row->email_reply; ?>
					</td>
					<td>
						<div class="btn-group">
							<button class="btn btn-default" type="button"><?php echo JText::_('COM_SURVEYFORCE_SF_SELECT_ACTION')?></button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							    <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li>
									<a onclick="Joomla.submitbutton('edit_email'); return false;" href="javascript: void(0);"><i class="sf-icon-pencil"></i> Edit</a>
								</li>
								<li>
									<a onclick="Joomla.submitbutton('del_email'); return false;" href="javascript: void(0);"><i class="sf-icon-trash"></i> Delete</a>
								</li>
							</ul>
						</div>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			<tbody>
		</table>

		<a onclick="Joomla.submitbutton('usergroups'); return false;" href="javascript: void(0);" class="btn btn-default"><i class="sf-icon-left-open"></i> Back</a>
		<div style="clear:both;"></div>
		<div class="pagination" style="margin-left:30%;">
		<?php 
			$link = "index.php?option=com_surveyforce&amp;task=emails"; 
			echo $pageNav->writePagesLinks($link).'<br/>';
		?>
		</div>
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="task" value="emails" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form><br/><br/>
		</div>
		<?php
	}
	
	function SF_editEmail( &$row, &$lists, $option ) {
		global  $Itemid,$Itemid_s;

		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel_email') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			var reg_email = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/;

			if (form.email_subject.value == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_EMAIL_MUST_NAME') ?>" );
			} else if (form.email_body.value == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_EMAIL_MUST_BODY') ?>" );
			} else if (form.email_reply.value == ""){
				alert( "<?php echo JText::_('COM_SURVEYFORCE_EMAIL_MUST_REMAIL') ?>" );
			} else if (!reg_email.test(form.email_reply.value)) {
				alert( "<?php echo JText::_('COM_SURVEYFORCE_EMAIL_MUST_VALID') ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<div class="contentpane surveyforce">
		<form action="<?php echo JRoute::_("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'save',   'btn_js' => "Joomla.submitbutton('save_email');",   "btn_str" => JText::_("COM_SURVEYFORCE_SF_SAVE"),   "btn_ico" => "sf-icon-ok-circled");
			$toolbar[] = array('btn_type' => 'apply',  'btn_js' => "Joomla.submitbutton('apply_email');",  "btn_str" => JText::_("COM_SURVEYFORCE_SF_APPLY"),  "btn_ico" => "sf-icon-ok");
			$toolbar[] = array('btn_type' => 'cancel', 'btn_js' => "Joomla.submitbutton('cancel_email');", "btn_str" => JText::_("COM_SURVEYFORCE_SF_CANCEL"), "btn_ico" => "sf-icon-cancel-circled");

			$additionBottomRight = '';
			$additionBottomLeft =  '';

			$headTitle=($row->id ? JText::_('COM_SURVEYFORCE_EDIT_EMAIL') : JText::_('COM_SURVEYFORCE_NEW_EMAIL'));

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>
			<fieldset>
				<legend>Email Details</legend>

				<div class="control-group">
					<label class="control-label" for="email_subject"><?php echo JText::_('Subject')?>:</label>
					<div class="controls">
						<input class="text_area" type="text" name="email_subject" id="email_subject" size="50" maxlength="100" value="<?php echo $row->email_subject; ?>" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="email_body"><?php echo JText::_('Body')?>:</label>
					<div class="controls">
						<span class="label label-info">Use the following constants: #name#, #link#.</span><br/><br/>
						<textarea class="text_area" name="email_body" id="email_body" cols="36" rows="5" style="height:250px;width:340px;"><?php echo $row->email_body; ?></textarea>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="email_subject"><?php echo JText::_('Reply to')?>:</label>
					<div class="controls">
						<input class="text_area" type="text" name="email_reply" id="email_reply" size="50" maxlength="100" value="<?php echo $row->email_reply; ?>" />
					</div>
				</div>
			</fieldset>
		<br />
		<input type="hidden" name="option" value="com_surveyforce" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="user_id" value="<?php echo JFactory::getUser()->id; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
		<input type="hidden" name="task" value="" />
		</form><br/><br/>
		</div>
		<?php
	}

	function SF_inviteUsers( &$row, &$lists, $option ) {
		global $task, $Itemid,$Itemid_s, $my;
		?>
		<script language="javascript" type="text/javascript">
			function getObj(name)
			{
				if (document.getElementById)  {  return document.getElementById(name);  }
				else if (document.all)  {  return document.all[name];  }
				else if (document.layers)  {  return document.layers[name];  }
			}
		</script>
		<script language="javascript" type="text/javascript">
			<!--
			function StartInvitation() {
				var form = document.adminForm;
				var inv_frame = getObj('invite_frame');
				inv_frame.src = '<?php echo JUri::root();?>index.php?no_html=1&option=com_surveyforce&view=authoring&task=invitation_start&email='+form.email_id.value+'&list='+<?php echo $row->id?>;
			}

			function StopInvitation() {
				var form = document.adminForm;
				form.Start.value = 'Resume';
				if (!document.all)
					for (var i=0;i<top.frames.length;i++)
						top.frames[i].stop()
				else
					for (var i=0;i<top.frames.length;i++)
						top.frames[i].document.execCommand('Stop')
			}

			Joomla.submitbutton = function(pressbutton) {
				var form = document.adminForm;
				submitform( pressbutton );
			}
			//-->
		</script>
		<div class="contentpane surveyforce">
			<form action="<?php echo SFRoute("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
				<?php
				$toolbar = array();
				$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('usergroups');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");
				$additionBottomRight = '';
				$additionBottomLeft =  '';

				$headTitle=JText::_('COM_SURVEYFORCE_INVITE_USERS');

				SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
				?>
				<fieldset>
					<legend>Invitation Details</legend>

					<div class="control-group">
						<label class="control-label" for="survey"><?php echo JText::_('List of users')?>:</label>
						<div class="controls">
							<?php echo $row->listname; ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="survey"><?php echo JText::_('Email')?>:</label>
						<div class="controls">
							<?php echo $lists['email_list']; ?>
						</div>
					</div>

					<div class="form-actions">
						<span class="label label-info" id="div_invite_log_txt">
							<?php if ($row->is_invited == 0) { ?>
								Press Start to begin invitations sending process.
							<?php } elseif ($row->is_invited == 1) { ?>
								Users from the following list had been sent invitations before.
							<?php } elseif ($row->is_invited == 2) { ?>
								Press Start to continue invitations sending process.
							<?php } ?>
						</span><br/><br/>

						<button type="button" class="btn btn-primary" id="Start_button" onclick="StartInvitation();">Start</button>
						<button type="button" class="btn" onclick="StopInvitation();">Stop</button>
					</div>

					<div class="control-group">
						<div id="div_invite_log" style="width:0; background-color:#000000; color:#FFFFFF; text-align:center"></div>
					</div>
				</fieldset>
				<br />
				<input type="hidden" name="option" value="com_surveyforce" />
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
				<input type="hidden" name="task" value="usergroups" />
				<input type="hidden" name="view" value="authoring" />

			</form><br/><br/>
		</div>
		<iframe src="" style="display:none " id="invite_frame">
		</iframe>
	<?php

	}

	function SF_remindUsers( &$row, &$lists, $option ) {
		global $task, $Itemid,$Itemid_s, $my;
?>
	<script language="javascript" type="text/javascript">
		function getObj(name)
		{
			if (document.getElementById)  {  return document.getElementById(name);  }
			else if (document.all)  {  return document.all[name];  }
			else if (document.layers)  {  return document.layers[name];  }
		}

		function StartRemind() {
			var form = document.adminForm;
			var inv_frame = getObj('invite_frame');
			inv_frame.src = '<?php echo JUri::root();?>index.php?no_html=1&option=com_surveyforce&view=authoring&task=remind_start&email='+form.email_id.value+'&list='+<?php echo $row->id?>;
		}

		function StopRemind() {
			var form = document.adminForm;
			form.Start.value = 'Resume';
			if (!document.all)
				for (var i=0;i<top.frames.length;i++)
					top.frames[i].stop()
			else
				for (var i=0;i<top.frames.length;i++)
					top.frames[i].document.execCommand('Stop')
		}

		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			submitform( pressbutton );
		}
		//-->
	</script>
	<div class="contentpane surveyforce">
		<form action="<?php echo SFRoute("index.php?option=com_surveyforce")?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

			<?php
			$toolbar = array();
			$toolbar[] = array('btn_type' => 'back', 'btn_js' => "Joomla.submitbutton('usergroups');", 'btn_str' => JText::_('COM_SURVEYFORCE_SF_BACK'), "btn_ico" => "sf-icon-left-open");

			$additionBottomRight = '';
			$additionBottomLeft =  '';

			$headTitle=JText::_('COM_SURVEYFORCE_REMIND_USERS');

			SF_showTop('usergroups', $headTitle, $toolbar, $additionBottomRight, @$additionBottomLeft);
			?>

			<fieldset>
				<legend>Reminder Details</legend>

				<div class="control-group">
					<label class="control-label" for="survey"><?php echo JText::_('List of users')?>:</label>
					<div class="controls">
						<?php echo $row->listname; ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="survey"><?php echo JText::_('Email')?>:</label>
					<div class="controls">
						<?php echo $lists['email_list']; ?>
					</div>
				</div>

				<div class="form-actions">
					<span class="label label-info" id="div_invite_log_txt">
						Press Start to begin reminders sending process.
					</span><br/><br/>

					<button type="button" class="btn btn-primary" id="Start_button" onclick="StartRemind();">Start</button>
					<button type="button" class="btn" onclick="StopRemind();">Stop</button>
				</div>

				<div class="control-group">
					<div id="div_invite_log" style="width:0; background-color:#000000; color:#FFFFFF; text-align:center"></div>
				</div>
			</fieldset>
			<br />
			<input type="hidden" name="option" value="com_surveyforce" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid?>" />
			<input type="hidden" name="task" value="usergroups" />
			<input type="hidden" name="view" value="authoring" />

		</form><br/><br/>
	</div>
	<iframe src="" style="display:none " id="invite_frame">
	</iframe>
<?php
}
}

function SF_editorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {

	$editor = JFactory::getConfig()->get('editor');
	$editor = JEditor::getInstance($editor);
	//public function display($name, $html, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())

	echo $editor->display( $hiddenField, $content, $width, $height,$col, $row, false );
}
?>