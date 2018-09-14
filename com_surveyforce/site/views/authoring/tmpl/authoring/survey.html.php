<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html class=" js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage no-websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients no-cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths"><!--<![endif]--><head>
<script type="text/javascript">
	
	var query = decodeURIComponent(window.location.search);
	if(query.indexOf("cid[0]=") == -1){
		window.location.replace(window.location.href + "&cid[0]=<?php echo $survey->id;?>");
	}
	
	var COM_SURVEYFORCE_SELECT_DATE="<?php echo JText::_('COM_SURVEYFORCE_SELECT_DATE')?>";
	var COM_SF_YOU_HAVANT_ADDED_WITH_SLASH="<?php echo JText::_('COM_SF_YOU_HAVANT_ADDED_WITH_SLASH')?>";
	var COM_SURVEYFORCE_SELECT_PAGE="<?php echo JText::_('COM_SURVEYFORCE_SELECT_PAGE')?>";
	var COM_SURVEYFORCE_FILE_API_ARE_NOT_SUPPORTED="<?php echo JText::_('COM_SURVEYFORCE_FILE_API_ARE_NOT_SUPPORTED')?>";

</script>
<style type="text/css">.gm-style .gm-style-mtc label,.gm-style .gm-style-mtc div{font-weight:400}</style>
<style type="text/css">.gm-style .gm-style-cc span,.gm-style .gm-style-cc a,.gm-style .gm-style-mtc div{font-size:10px}</style>
<link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
<style type="text/css">@media print {  .gm-style .gmnoprint, .gmnoprint {    display:none  }}@media screen {  .gm-style .gmnoscreen, .gmnoscreen {    display:none  }}</style><style type="text/css">.gm-style{font-family:Roboto,Arial,sans-serif;font-size:11px;font-weight:400;text-decoration:none}</style>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- ///  page title  /// -->
      <title><?php echo JText::_('COM_SURVEYFORCE_FA')?></title>
      <meta content="" name="description">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <!-- /// font icons  /// -->
      <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
      <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400">
      <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700">
      <!-- ///  bootstrap-select.css /// -->
      <link href="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/css/plugins/select/bootstrap-select.css" type="text/css" rel="stylesheet">
      <!-- ///  bootstrap.css  /// -->
      <link href="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/bootstrap-3.1.1/css/bootstrap.css" type="text/css" rel="stylesheet">      
      <!-- ///  main CSS file  /// -->        
      <link href="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/css/main.css" type="text/css" rel="stylesheet">
      <link href="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
      <link href="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/css/notifIt.css" type="text/css" rel="stylesheet">        
      
      <script type="text/javascript" src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/ckeditor/ckeditor.js"></script>
      <script type="text/javascript" src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/ckeditor/adapters/jquery.js"></script>
      <script type="text/javascript">
      	<?php $types = array('1' => 'likert-scale', '2' => 'pick-one', '3' => 'pick-many', '4' => 'short-answer', '5' => 'ranking-dropdown', '6' => 'ranking-dragdrop', '7' => 'boilerplate', '8' => 'page-break', '9' => 'ranking', '10' => 'section-separator'); ?>
      	var lastPage = 1;
		var currPage = lastPage;
      	var questionsStack = {};
      	var questOrdering = 0;
      	var sf_step = <?php echo ($survey->sf_step ? $survey->sf_step : 3);?>;

      	<?php if(count($questions)):?>
      	<?php foreach ($questions as $question) {?>
      	var newID = sfGenerateID();
      	<?php
      		$question->sf_qtext = str_replace('"', '\"', $question->sf_qtext);
      		$question->sf_qtext = preg_replace("/\\n|\\r\\n/", "", $question->sf_qtext);
      	?>
      	sfPushStackQuestion(newID, <?php echo $question->id?>, "<?php echo $question->sf_qtext;?>", "<?php echo $types[$question->sf_qtype];?>", <?php echo $question->published?>, <?php echo $question->sf_compulsory?>, <?php echo $question->sf_default_hided?>, <?php echo $question->is_final_question?>, <?php echo $question->sf_qstyle?>, <?php echo $question->sf_impscale;?>);

      	<?php if(count($question->hides)){?>
      	questionsStack[newID].hides = [];
      	<?php foreach ($question->hides as $hide) {?>
      	
      	questionsStack[newID].hides.push({qtype: "<?php echo $types[$question->sf_qtype];?>", question: <?php echo $hide->quest_id_a;?>, option: '', answer: <?php echo $hide->answer;?>});
      	
      	<?php }?>
      	<?php }?>

      	<?php if(count($question->rules)){?>
      	questionsStack[newID].rules = [];
      	<?php foreach ($question->rules as $rule) {?>
      	
      	questionsStack[newID].rules.push({question: <?php echo $rule->next_quest_id;?>,answer: <?php echo $rule->answer_id;?>, option: <?php echo $rule->alt_field_id?>, priority: <?php echo $rule->priority;?>});
      	
      	<?php }?>
      	<?php }?>

		<?php switch($types[$question->sf_qtype]){
			case 'pick-one':
			case 'pick-many':
			?>

			questionsStack[newID].answers = [];
			<?php if(count($question->answers)):?>
			<?php foreach ($question->answers as $answer) {?>

			<?php
				if (!isset($answer->ftext))
				{
					$answer->ftext= '';
				}
				
				$answer->ftext = str_replace('"', '\"', $answer->ftext);
				
				if (!isset($answer->id))
				{
					$answer->id= 0;
				}
			?>
			questionsStack[newID].answers.push({title: "<?php echo $answer->ftext;?>", id: "<?php echo $answer->id;?>"});
			<?php }?>
			<?php if($question->answers[0]->sf_other){?>
			questionsStack[newID].answers[0]['other_option'] = 1;

			<?php
				$question->answers[0]->sf_other->ftext = str_replace('"', '\"', $question->answers[0]->sf_other->ftext);
			?>

			questionsStack[newID].answers[0]['other_option_text'] = '<?php echo $question->answers[0]->sf_other->ftext;?>';
			<?php } else {?>
			questionsStack[newID].answers[0]['other_option'] = 0;
			questionsStack[newID].answers[0]['other_option_text'] = '';
			<?php }?>
			<?php endif;?>
			
			<?php break;

			case 'ranking':
			case 'ranking-dragdrop':
			?>

			questionsStack[newID].answers = [];
			<?php if(count($question->answers['left'])):?>
			<?php foreach ($question->answers['left'] as $ii => $left) {?>

			<?php
				$ltext = str_replace('"', '\"', $left->ftext);
				if (!isset($question->answers['right'][$ii]->ftext))
				{
					$rtext= '';
				} else {
					$rtext = str_replace('"', '\"', $question->answers['right'][$ii]->ftext);
				}
				
				if (!isset($question->answers['right'][$ii]->id))
				{
					$question->answers['right'][$ii]->id= 0;
				}
			?>

			questionsStack[newID].answers.push({left: "<?php echo $ltext?>", right: "<?php echo $rtext;?>", leftid: <?php echo $left->id;?>, rightid: <?php echo $question->answers['right'][$ii]->id;?>});
			<?php }?>
			<?php endif;?>

			<?php break;

			case 'ranking-dropdown':
			case 'likert-scale':
			?>

			<?php if($types[$question->sf_qtype] == 'ranking-dropdown'){?>
			<?php $t = 'ranks';?>

			questionsStack[newID].answers = {options: [], ranks: [], oid: [], rid: []};

			<?php } else {?>
			<?php $t = 'scales'; ?>

			questionsStack[newID].answers = {options: [], scales: [], oid: [], sid: []};

			<?php } ?>
			<?php if(count($question->answers['options'])):?>
			
			<?php foreach ($question->answers['options'] as $option) {?>
			<?php
				$option->ftext = str_replace('"', '\"', $option->ftext);
			?>
			questionsStack[newID].answers.options.push("<?php echo $option->ftext;?>");
			questionsStack[newID].answers.oid.push(<?php echo $option->id;?>);
			<?php }?>
			<?php endif;?>
			<?php if(count($question->answers[$t])):?>

			<?php foreach ($question->answers[$t] as $value) {?>
			<?php
				if($types[$question->sf_qtype] == 'ranking-dropdown'){
					$vtext = str_replace('"', '\"', $value->ftext);
					$ans_id_prop = 'rid';
					$ans_id = $value->id;
				} else {
					$vtext = str_replace('"', '\"', $value->stext);
					$ans_id_prop = 'sid';
					$ans_id = $value->id;
				}
			?>
			questionsStack[newID].answers.<?php echo $t;?>.push("<?php echo $vtext;?>");
			questionsStack[newID].answers.<?php echo $ans_id_prop;?>.push("<?php echo $ans_id;?>");
			<?php }?>
			<?php endif;?>
			<?php break;
			case 'section-separator':
			?>
			questionsStack[newID].sections = [];
			<?php if(count($question->sections)):?>
			<?php foreach ($question->sections as $section):?>
			questionsStack[newID].sections.push(<?php echo $section;?>);
			<?php endforeach;?>
			<?php endif;?>
			<?php break;
		}?>

		<?php }?>
      	<?php endif;?>

      	function sfPushStackQuestion(newID, quest_id, sf_title, sf_qtype, published, sf_compulsory, sf_default_hided, is_final_question, choiceStyle, sf_iscale)
      	{
      		questionsStack[newID] = {};
      		questionsStack[newID].exists = 1;
			questionsStack[newID].page = currPage;
			questionsStack[newID].hides = [];
			questionsStack[newID].rules = [];
			questionsStack[newID].id = quest_id;
			questionsStack[newID].sf_qtitle = sf_title;
			questionsStack[newID].sf_qtype = sf_qtype;
			questionsStack[newID].sf_qdescription = "";
			questionsStack[newID].sf_iscale = sf_iscale;
			questionsStack[newID].published = published;
			questionsStack[newID].sf_compulsory = sf_compulsory;
			questionsStack[newID].sf_default_hided = sf_default_hided;
			questionsStack[newID].is_final_question = is_final_question;
			questionsStack[newID].choiceStyle = choiceStyle;
			questionsStack[newID].questOrdering = questOrdering;

			questOrdering++;
      	}

      	function sfGenerateID()
		{
			var id;
			var idArray = new Array();
			var letters = new Array("A", "B", "C", "D", "E", "F", "G", "H", "I", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "W", "X", "Y", "Z", "a", "b", "c", "d", "e", "f", "g", "h", "i", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "w", "x", "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
			for(var n=0; n <= 9; n++){
				var rnd = Math.round(Math.random() * letters.length);
				idArray.push(letters[rnd]);
			}

			id = idArray.join("");
			return id;
		}

      </script>
      </head>
  <body class="" id="default-body">
    <div id="body">
      <div id="main">

      <div class="account-survey">
        <div class="main  only-nav">
          <div class="clearfix" id="survey-editor">
            <div class="progress-bar">
				<div class="done"></div>
				<div class="not-done"></div>
            </div>
            <div class="toolbox-wrap clearfix">
              <div class="row">
                <form method="post" action="index.php?option=com_surveyforce&task=survey.saveSurvey&tmpl=component" enctype="multipart/form-data" name="surveyForm" id="surveyForm">              
                <div data-wow-animation-name="fadeInDown" style="visibility: visible; animation-name: fadeInDown;" class="col-md-12 wow fadeInDown animated animated">
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs nav-justified">
                    <li id="surveyButton"><a href="#survey" data-toggle="tab">Survey</a></li>
                    <li id="questionsButton" class="active"><a href="#questions" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_QUESTIONS_FA');?></a></li>
                    <li id="pageButton"><a href="#page" data-toggle="tab"><?php echo JText::_('COM_SURVEYFORCE_PAGE_SETTINGS');?></a></li>           
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content">
                    <div class="tab-pane" id="survey">
                      <div class="panel-group" id="panelSurvey">
	                        <div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelSurvey" href="#collapseSurveyDescr">
	                            <h4><?php echo JText::_('COM_SF_SURVEY_DETAILS');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
	                          </div>
	                          <div style="height: auto;" id="collapseSurveyDescr" class="panel-collapse collapse in">
	                            <div class="panel-body">
									<div class="control-group form-inline">
					                    <label class="control-label"><?php echo JText::_('COM_SF_SURVEY_NAME');?>:</label>
					                    <div class="controls">
											<input type="text" class="input-xlarge required" size="30" name="sf_name" id="sf_name" value="<?php echo $survey->sf_name;?>" onkeyup="javascript:sfChangeSurveyName();" />
					                    </div>
					                </div>
					                <div style="clear:both"><br/></div>
						            <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_SURVEY_DESCRIPTION');?>:</label>
				                    	<div class="controls">
											<textarea class="inputbox" name="sf_descr" id="sf_descr" onkeyup="javascript:sfChangeSurveyDescr();" onclick="javascript:sfOpenEditButton(this);" onblur="javascript:sfCloseEditButton(this);"><?php echo $survey->sf_descr?></textarea>
											<div class="edit-html" style="display: none;"><a class="button button-small edit-html-btn" href="#" onclick="javascript:sfOpenCKEEditor(this, 'surveyDescr');"><i class="fa fa-eye edit-html-text"><?php echo JText::_('COM_SF_OPEN_VISUAL_EDITOR');?></i></a></div>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
						            <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_SHORT_DESCRIPTION');?>:</label>
				                    	<div class="controls">
											<textarea class="inputbox" name="surv_short_descr" id="surv_short_descr" onclick="javascript:sfOpenEditButton(this);" onblur="javascript:sfCloseEditButton(this);"><?php echo $survey->surv_short_descr?></textarea>
											<div class="edit-html" style="display: none;"><a class="button button-small edit-html-btn" href="#" onclick="javascript:sfOpenCKEEditor(this, 'surveyShortDescr');"><i class="fa fa-eye edit-html-text"><?php echo JText::_('COM_SF_OPEN_VISUAL_EDITOR');?></i></a></div>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
						            <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_ENABLE_DESCR');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_enable_descr" id="sf_enable_descr" class="css-checkbox" <?php if($survey->sf_enable_descr) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_enable_descr"></label>
				                    	</div>
				                    </div>
	                            </div>
							  </div>
							</div>
						</div>
						<div class="panel-group" id="panelSurveySettings">
							<div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelSurveySettings" href="#collapseSurveySettings">
	                            <h4><?php echo JText::_('COM_SF_SURVEY_SETTINGS');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
	                          </div>
	                          <div style="height: auto;" id="collapseSurveySettings" class="panel-collapse collapse">
	                            <div class="panel-body">
									<div class="control-group form-inline">
					                    <label class="control-label"><?php echo JText::_('COM_SF_IMAGE');?></label>
					                    <div class="controls" id="bkg_thumb">
					                        <a href="#" class="remove" onclick="javascript:sfRemoveImage();return false;"><i class="fa fa-times"></i></a>
					                        <input type="text" class="input-xlarge required upload-field" size="30" name="sf_image" id="sf_image" value="<?php echo $survey->sf_image?>"/>

											<button class="btn btn-large" onclick="javascript:sfSelectFile(this);return false;"><?php echo JText::_('COM_SF_SELECT');?></button>
											<input type="file" class="button button-small" name="image_file" id="image_file" style="display:none"/>
					                    </div>
					                </div>
					                <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SHOW_PROGRESS');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_progressbar" id="sf_progressbar" class="css-checkbox" <?php if($survey->sf_progressbar) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_progressbar"></label>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_TYPE');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_progressbar_type" class="form-control selectpicker" data-validation="required" id="sf_progressbar_type">
							                    <option value="1" <?php if($survey->sf_progressbar_type == '1') echo 'selected="selected"'?> ><?php echo JText::_('COM_SF_COUNT_BY_QUESTIONS');?></option>
							                    <option value="0" <?php if(!$survey->sf_progressbar_type) echo 'selected="selected"'?> ><?php echo JText::_('COM_SF_COUNT_BY_PAGES');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_TEMPLATE');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_template" class="form-control selectpicker" data-validation="required" id="sf_template">
												<option value="2" <?php if($survey->sf_template == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_NEW_TEMPLATE');?></option>
							                    <option value="1" <?php if($survey->sf_template == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_STANDARD_TEMPLATE');?></option>
							                    <option value="3" <?php if($survey->sf_template == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_PRETTY_GREEN_TEMPLATE');?></option>
							                    <option value="4" <?php if($survey->sf_template == '4') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_PRETTY_BLUE_TEMPLATE');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_CATEGORY');?>:</label>
				                    	<div class="controls">
											<?php echo $lists['categories'];?>
				                    	</div>
				                    </div>
									<div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_STARTED_ON');?>:</label>
				                    	<div class="controls">
											<input type="text" class="input-xlarge required datapicker" size="30" name="sf_date_started" id="sf_date_started" value="<?php echo $survey->sf_date_started; ?>"/>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_EXPIRED_ON');?>:</label>
				                    	<div class="controls">
											<input type="text" class="input-xlarge required datapicker" size="30" name="sf_date_expired" id="sf_date_expired" value="<?php echo $survey->sf_date_expired; ?>"/>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_RANDOMIZE_QUESTIONS');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_random" class="form-control selectpicker" data-validation="required" id="sf_random">
							                    <option value="0" <?php if($survey->sf_random == '0') echo 'selected="selected"'?>><?php echo JText::_('COM_SF_NO');?></option>
							                    <option value="1" <?php if($survey->sf_random == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SF_RANDOMIZE_PAGES');?></option>
							                    <option value="2" <?php if($survey->sf_random == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SF_RANDOMIZE_QUESTIONS');?></option>
							                    <option value="3" <?php if($survey->sf_random == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SF_RANDOMIZE_PAGES_AND_QUESTIONS');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_AUTO_INSERT_PB');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_auto_pb" id="sf_auto_pb" class="css-checkbox" <?php if($survey->sf_auto_pb) echo 'checked="checked"'?>/>
											<label class="css-label cb0" for="sf_auto_pb"></label>
				                    	</div>
				                    </div>
				                    <hr/>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_PUBLIC');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_public" id="sf_public" class="css-checkbox" <?php if($survey->sf_public) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_public"></label>
				                    	</div>
				                    </div>
									<div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_VOITING');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_pub_voting" class="form-control selectpicker" data-validation="required" id="sf_pub_voting">
							                    <option value="0" <?php if($survey->sf_pub_voting == '0') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_MULTIPLE_VOTING');?></option>
							                    <option value="1" <?php if($survey->sf_pub_voting == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING');?></option>
							                    <option value="2" <?php if($survey->sf_pub_voting == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING_REPLACE');?></option>
							                    <option value="3" <?php if($survey->sf_pub_voting == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ALLOW_EDIT_ANSWERS');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_CONTROL');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_pub_control" class="form-control selectpicker" data-validation="required" id="sf_pub_control">
							                    <option value="0" <?php if($survey->sf_pub_control == '0') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_NONE');?></option>
							                    <option value="1" <?php if($survey->sf_pub_control == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_IP_ADDR');?></option>
							                    <option value="2" <?php if($survey->sf_pub_control == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_COOKIE');?></option>
							                    <option value="3" <?php if($survey->sf_pub_control == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_BOTH');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_REG_FULL');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_reg" id="sf_reg" class="css-checkbox" <?php if($survey->sf_reg) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_reg"></label>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_VOITING');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_reg_voting" class="form-control selectpicker" data-validation="required" id="sf_reg_voting">
							                    <option value="0" <?php if($survey->sf_reg_voting == '0') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_MULTIPLE_VOTING');?></option>
							                    <option value="1" <?php if($survey->sf_reg_voting == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING');?></option>
							                    <option value="2" <?php if($survey->sf_reg_voting == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING_REPLACE');?></option>
							                    <option value="3" <?php if($survey->sf_reg_voting == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ALLOW_EDIT_ANSWERS');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_INVITED');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_invite" id="sf_invite" class="css-checkbox" <?php if($survey->sf_invite) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_invite"></label>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_VOITING');?>:</label>
				                    	<div class="controls">
											<select data-style="btn" name="sf_inv_voting" class="form-control selectpicker" data-validation="required" id="sf_inv_voting">
							                    <option value="0" <?php if($survey->sf_inv_voting == '0') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_MULTIPLE_VOTING');?></option>
							                    <option value="1" <?php if($survey->sf_inv_voting == '1') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING');?></option>
							                    <option value="2" <?php if($survey->sf_inv_voting == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ONCE_VOTING_REPLACE');?></option>
							                    <option value="3" <?php if($survey->sf_inv_voting == '3') echo 'selected="selected"'?>><?php echo JText::_('COM_SURVEYFORCE_ALLOW_EDIT_ANSWERS');?></option>
							                 </select>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
                                    <div class="control-group form-inline">
                                        <label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_ALLOW_USER_TO');?>:</label>
                                        <div class="controls">
                                            <input type="checkbox" name="sf_allow_continue" id="sf_allow_continue" class="css-checkbox" <?php if($survey->sf_allow_continue) echo 'checked="checked"'?> />
                                            <label class="css-label cb0" for="sf_allow_continue"></label>
                                        </div>
                                    </div>
                                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_FOR_USER_IN_LISTS');?>:</label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_special" id="sf_special" class="css-checkbox" <?php if($survey->sf_special) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_special"></label>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_USERLISTS');?>:</label>
				                    	<div class="controls">
											<?php echo $lists['userlists'];?>
				                    	</div>
				                    </div>
				                    <div style="clear:both"><br/></div>
				                    <div class="control-group form-inline">
				                    	<label class="control-label"><?php echo JText::_('COM_SF_ENABLE_PREV');?></label>
				                    	<div class="controls">
											<input type="checkbox" name="sf_prev_enable" id="sf_prev_enable" class="css-checkbox" <?php if($survey->sf_prev_enable) echo 'checked="checked"'?> />
											<label class="css-label cb0" for="sf_prev_enable"></label>
				                    	</div>
				                    </div>
	                            </div>
	                          </div>
	                        </div>
						</div>
                    </div>

					<div class="tab-pane" id="page">
                      <div class="panel-group" id="panelOptions">
                        <div class="panel panel-default">
                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelOptions" href="#collapse-options">
                            <h4><?php echo JText::_('COM_SURVEYFORCE_OPTIONS');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
                          </div>
                          <div style="height: auto;" id="collapse-options" class="panel-collapse collapse in">
                            <div class="panel-body">
			                  <div class="control-group form-inline">
			                    <label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_AFTER_START');?>:</label>
			                    <div class="controls">					  
			                      <select data-style="btn" name="sf_after_start" class="form-control selectpicker" data-validation="required" id="sf_after_start">
				                    <option value="0" <?php if(!$survey->sf_after_start) echo 'selected="selected"'?> ><?php echo JText::_('COM_SF_SHOW_WARNING');?></option>
				                    <option value="1" <?php if($survey->sf_after_start) echo 'selected="selected"'?> ><?php echo JText::_('COM_SF_SHOW_MESSAGE_AND_SURVEY');?></option>
				                  </select>    
			                    </div>
			                  </div>
			                  <div style="clear:both;"><br/></div>
			                  <div class="control-group form-inline">
			                    <label class="control-label"><?php echo JText::_('COM_SF_FINAL_PAGE');?></label>
			                    <div class="controls">
			                      <select data-style="btn" name="sf_fpage_type" class="form-control selectpicker" data-validation="required" id="sf_fpage_type">
				                    <option value="1" <?php if($survey->sf_fpage_type == '1') echo 'selected="selected"'?> ><?php echo JText::_('COM_SF_SHOW_RESULTS');?></option>
				                    <option value="0" <?php if(!$survey->sf_fpage_type) echo 'selected="selected"'?>><?php echo JText::_('COM_SF_SHOW_TEXT');?></option>
				                    <option value="2" <?php if($survey->sf_fpage_type == '2') echo 'selected="selected"'?>><?php echo JText::_('COM_SF_SHOW_MESSAGE_AND_SURVEY');?></option>
				                  </select>    
			                    </div>
			                  </div>
			                  <div style="clear:both"><br/></div>
				              <div class="control-group form-inline">
		                    	<label class="control-label"><?php echo JText::_('COM_SF_FINAL_PAGE_TEXT');?></label>
		                    	<div class="controls">
									<textarea class="inputbox" name="sf_fpage_text" id="sf_fpage_text" onclick="javascript:sfOpenEditButton(this);" onblur="javascript:sfCloseEditButton(this);"><?php echo $survey->sf_fpage_text;?></textarea>
									<div class="edit-html" style="display: none;"><a class="button button-small edit-html-btn" href="#" onclick="javascript:sfOpenCKEEditor(this, 'customMessage');"><i class="fa fa-eye edit-html-text"><?php echo JText::_('COM_SF_OPEN_VISUAL_EDITOR');?></i></a></div>
		                      </div>
		                      <div style="clear:both"><br/></div>
				              <div class="control-group form-inline">
		                    	<label class="control-label"><?php echo JText::_('COM_SF_REDIRECT');?></label>
		                    	<div class="controls">
									<input type="checkbox" name="sf_redirect_enable" id="sf_redirect_enable" class="css-checkbox" <?php if($survey->sf_redirect_enable) echo 'checked="checked"'?> />
									<label class="css-label cb0" for="sf_redirect_enable"></label>
		                    	</div>
		                      </div>
							  <div style="clear:both"><br/></div>
				              <div class="control-group form-inline">
		                    	<label class="control-label"><?php echo JText::_('COM_SF_REDIRECT_TO_THIS_URL');?></label>
		                    	<div class="controls">
		                      		<input type="text" id="sf_redirect_url" name="sf_redirect_url" size="30" class="input-xlarge" value="<?php echo $survey->sf_redirect_url;?>">
		                      	</div>
		                      </div>
		                      <div style="clear:both"><br/></div>
				              <div class="control-group form-inline">
		                    	<label class="control-label"><?php echo JText::_('COM_SF_DELAY_BEFORE_REDIRECTION');?></label>
		                    	<div class="controls">
		                      		<input type="text" id="sf_redirect_delay" name="sf_redirect_delay" size="30" class="input-xlarge" value="<?php echo $survey->sf_redirect_delay;?>">
		                      	</div>
		                      </div>
		                    </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                    </div>

                    <div class="tab-pane active" id="questions">
                      <div class="panel-group" id="basic-questions">
                        <div class="panel panel-default">
                          <div class="panel-heading" data-toggle="collapse" data-parent="#basic-questions" href="#basic">
                            <h4><?php echo JText::_('COM_SURVEYFORCE_BASIC_QUESTIONS');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
                          </div>
                          <div id="basic" class="panel-collapse collapse in">
                            <div class="panel-body">
                              <p class="help"><?php echo JText::_('COM_SURVEYFORCE_DRAG_QUESTIONS');?></p>
                              <div><div><div class="basicquestions"><ul id="basicquestions" class="connectedSortable tools clearfix"><li field-type="section-separator" class="tool section-separator" title="<?php echo JText::_('COM_SURVEYFORCE_SECTION_HEADING');?>"><i class="fa fa-header"></i><span><?php echo JText::_('COM_SURVEYFORCE_SECTION_HEADING');?></span></li> <li field-type="pick-one" class="tool boolean-choice ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_PICK_ONE_DESCR');?>"><i class="fa fa-dot-circle-o"></i><span><?php echo JText::_('COM_SURVEYFORCE_PICK_ONE');?></span></li> <li field-type="pick-many" class="tool text-response ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_PICK_MANY_DESCR');?>"><i class="fa fa-check-square-o"></i><span><?php echo JText::_('COM_SURVEYFORCE_PICK_MANY');?></span></li> <li field-type="short-answer" class="tool text-response-grid ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_SHORT_ANSWER_DESCR');?>"><i class="fa fa-text-width"></i><span><?php echo JText::_('COM_SURVEYFORCE_SHORT_ANSWER');?></span></li> <li field-type="ranking-dropdown" class="tool single-choice ui-draggable" title="<?php echo JText::_('COM_SF_RANKING_DROPDOWN_DESCR');?>"><i class="fa fa-tasks"></i><span><?php echo JText::_('COM_SF_RANKING_DROPDOWN');?></span></li> <li field-type="ranking-dragdrop" class="tool single-choice-grid ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_RANK_DRAGDROP_DESCR');?>"><i class="fa fa-arrows"></i><span><?php echo JText::_('COM_SURVEYFORCE_RANK_DRAGDROP');?></span></li> <li field-type="boilerplate" class="tool dropdown-choice ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_BOILERPLATE_DESCR');?>"><i class="fa fa-file-o"></i><span><?php echo JText::_('COM_SURVEYFORCE_BOILERPLATE');?></span></li> <li field-type="page-break" class="tool dropdown-grid ui-draggable" title="<?php echo JText::_('COM_SF_PAGE_BREAK_DESCR');?>"><i class="fa fa-chain-broken"></i><span><?php echo JText::_('COM_SF_PAGE_BREAK');?></span></li> <li field-type="ranking" class="tool multiple-choice ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_RANKING_DESCR');?>"><i class="fa fa-bars"></i><span><?php echo JText::_('COM_SURVEYFORCE_RANKING');?></span></li><li field-type="likert-scale" class="tool likert-scale ui-draggable" title="<?php echo JText::_('COM_SURVEYFORCE_LIKERT_SCALE_DESCR');?>"><i class="fa fa-table"></i><span><?php echo JText::_('COM_SURVEYFORCE_LIKERT_SCALE');?></span></li></ul></div></div></div>
                            </div>
                          </div>
                          
                        </div>

                      </div>
                      <div class="panel-group" id="panelProperties">
	                        <div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelProperties" href="#collapseProperties">
	                            <h4><?php echo JText::_('COM_SF_FIELD_PROPERTIES');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
	                          </div>
	                          <div style="height: auto;" id="collapseProperties" class="panel-collapse collapse in">
	                            <div class="panel-body">
	                            	<div id="fieldPropertiesDisable">
	                            		<p class="alert"><?php echo JText::_('COM_SF_SELECT_FIELD_ON_THE_RIGHT');?></p>
	                            	</div>
	              					<div id="fieldProperties" style="display:none;">
							            <div class="control-group form-inline">
											<label class="control-label"><?php echo JText::_('COM_SF_QUESTION_TYPE');?>:</label>
											<div class="controls">
												<span class="label label-primary" id="question-type"></span>
											</div>
							            </div>
							            <br/>
							            <div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SF_QUESTION_TITLE');?>:</label>
					                    	<div class="controls">
												<textarea class="inputbox" id="sf_qtitle" name="sf_qtitle" onclick="javascript:sfOpenEditButton(this);" onblur="javascript:sfCloseEditButton(this);" onkeyup="javascript:sfChangeQuestionTitle();"></textarea>
												<div class="edit-html" style="display: none;"><a class="button button-small edit-html-btn" href="#" onclick="javascript:sfOpenCKEEditor(this, 'questionTitle');"><i class="fa fa-eye edit-html-text">&nbsp;<?php echo JText::_('COM_SF_OPEN_VISUAL_EDITOR');?></i></a></div>
					                    	</div>
					                    </div>
					                    <div style="clear:both" ><br/></div>
							            <div hidden="true"class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SF_EXTRA_DESCRIPTION');?>:</label>
					                    	<div class="controls">
												<textarea class="inputbox" name="sf_qdescription" onclick="javascript:sfOpenEditButton(this);" onblur="javascript:sfCloseEditButton(this);" onkeyup="javascript:sfChangeQuestionDescription();"></textarea>
												<div class="edit-html" style="display: none;"><a class="button button-small edit-html-btn" href="#" onclick="javascript:sfOpenCKEEditor(this, 'questionDescr');"><i class="fa fa-eye edit-html-text">&nbsp;<?php echo JText::_('COM_SF_OPEN_VISUAL_EDITOR');?></i></a></div>
												<br/>
					                    	</div>
					                    </div>
					                </div>
	                            </div>
	                          </div>
	                        </div>
                        </div>
                        <div class="panel-group" id="panelSettings">
	                        <div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelSettings" href="#collapseSettings">
	                            <h4><?php echo JText::_('COM_SURVEYFORCE_ADVANCED_OPTIONS');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
	                          </div>
	                          <div style="height: auto;" class="panel-collapse collapse" id="collapseSettings">
	                            <div class="panel-body">
	                            	<div id="SettingsDisable">
	                            		<p class="alert"><?php echo JText::_('COM_SF_SELECT_FIELD_ON_THE_RIGHT');?></p>
	                            	</div>
	                            	<div id="Settings" style="display:none;">
										<div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_IMP_SCALE');?>:</label>
					                    	<div class="controls">
												<?php echo $lists['i_scales'];?>
							                </div>
							            </div>
							            <div style="clear:both"><br/></div>
							            <div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_ACTIVE');?>:</label>
					                    	<div class="controls">
												<input type="checkbox" name="published" id="published" class="css-checkbox" checked="checked" onclick="javascript:sfSelectCheckbox(this);"/>
												<label class="css-label cb0" for="published"></label>
					                    	</div>
					                    </div>
					                    <div style="clear:both"><br/></div>
					                    <div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_SF_COMPULSORY');?>:</label>
					                    	<div class="controls">
												<input type="checkbox" name="sf_compulsory" id="sf_compulsory" class="css-checkbox" onclick="javascript:sfSelectCheckbox(this);"/>
												<label class="css-label cb0" for="sf_compulsory"></label>
					                    	</div>
					                    </div>
					                    <div style="clear:both"><br/></div>
					                    <div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SF_HIDDEN_BY_DEFAULT');?></label>
					                    	<div class="controls">
												<input type="checkbox" name="sf_default_hided" id="sf_default_hided" class="css-checkbox" onclick="javascript:sfSelectCheckbox(this);"/>
												<label class="css-label cb0" for="sf_default_hided"></label>
					                    	</div>
					                    </div>
					                    <div style="clear:both"><br/></div>
					                    <div class="control-group form-inline">
					                    	<label class="control-label"><?php echo JText::_('COM_SF_IS_FINAL_QUESTION');?>:</label>
					                    	<div class="controls">
												<input type="checkbox" name="is_final_question" id="is_final_question" class="css-checkbox" onclick="javascript:sfSelectCheckbox(this);"/>
												<label class="css-label cb0" for="is_final_question"></label>
					                    	</div>
					                    </div>
					                </div>
	                            </div>
	                          </div>
	                        </div>
	                    </div>
	                    <div class="panel-group" id="panelRules" style="display:none;">
	                        <div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelRules" href="#collapse-rules">
	                            <h4><?php echo JText::_('COM_SF_DELAY_RULES');?><i class="fa fa-sort-desc" style="float:right;"></i></h4>
	                          </div>
	                          <div style="height: auto;" id="collapse-rules" class="panel-collapse collapse">
								<div class="panel-body">
								  <div class="control-group form-inline">
				                    <table id="show_quest" class="table table-striped">
										<tbody>
											<tr id="title">
												<th colspan="4" class="title"><?php echo JText::_('COM_SF_DONT_SHOW_QUESTION');?></th>
											</tr>
										</tbody>
									</table>
				                  </div>
				                  <div class="control-group form-inline">
									<label class="control-label"><?php echo JText::_('COM_SF_FOR_QUESTION');?>:</label>
									<div class="controls">
										<select id="sf_quest_list3" name="sf_quest_list3" onchange="javascript:sfGetAnswers(this);" style="width:250px;">
											<option value="0" selected="selected">- Select question -</option>
										</select>
									</div>
								  </div>
								  <div style="clear:both" id="hide_for_question"><br/></div>
								  <div class="control-group form-inline">
									<label class="control-label"><?php echo JText::_('COM_SF_ANSWER_IS');?></label>
									<div class="controls">
										<select id="f_scale_data" name="f_scale_data" style="width:250px;">
											<option value="0" selected="selected">- Select answer -</option>
										</select>
									</div>
				                  </div>
				                  <div style="clear:both"><br/></div>
				                  <div class="control-group form-inline">
									<button class="btn" onclick="javascript:sfAddHideQuestion();return false;"><?php echo JText::_('COM_SF_ADD');?></button>
				                  </div>
				                  <hr/><br/>
								  <div class="control-group form-inline">
								  <strong><?php echo JText::_('COM_SF_QUESTION_RULES');?></strong>
								  <table id="qfld_tbl_rule" class="table table-striped">
									<tbody>
										<tr>
											<th align="center" width="2%">#</th>
											<th width="25%" class="title rule_option" style="display:none;"><?php echo JText::_('COM_SF_OPTION');?></th>
											<th width="25%" class="title"><?php echo JText::_('COM_SURVEYFORCE_ANSWER');?></th>
											<th width="25%" class="title"><?php echo JText::_('COM_SURVEYFORCE_QUESTION');?></th>
											<th width="10%" class="title"><?php echo JText::_('COM_SURVEYFORCE_PRIORITY_C');?></th>
											<th width="auto"></th>
										</tr>
									</tbody>
								   </table>
								   </div>
								   <div style="clear:both"><br/></div>
								   <div class="rule_option" style="display:none;">
									   <div class="control-group form-inline">
										 <label class="control-label"><?php echo JText::_('COM_SF_IF_OPTION_IS');?>:</label>
										 <div class="controls">
										 	<select id="sf_option_list" name="sf_option_list" style="width:250px;">
												<option value="0">- Select option -</option>
											</select>
										 </div>
									   </div>
									   <div style="clear:both"><br/></div>
								   </div>
								   <div class="control-group form-inline">
									 <label class="control-label"><?php echo JText::_('COM_SF_IF_ANSWER_IS');?>:</label>
									 <div class="controls">
									 	<select id="sf_field_list" name="sf_field_list" style="width:250px;">
											<option value="0">- Select answer -</option>
										</select>
									 </div>
								   </div>
								   <div style="clear:both"><br/></div>
								   <div class="control-group form-inline">
									 <label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_GO_TO_QUEST');?>:</label>
									 <div class="controls">
										 <select id="sf_quest_list" name="sf_quest_list" style="width:250px;">
											<option value="0">- Select question -</option>
										 </select>
									 </div>
								   </div>
								   <div style="clear:both"><br/></div>
								   <div class="control-group form-inline">
									 <label class="control-label"><?php echo JText::_('COM_SURVEYFORCE_PRIORITY_C');?></label>
									 <div class="controls">
									 	<input name="new_priority" id="new_priority" style="width:150px;" value="0" type="text" class="input"/>
									 </div>
								   </div>
								   <div style="clear:both"><br/></div>
								   <div class="control-group form-inline">
										<button class="btn" onclick="javascript:sfAddQuestionRule();return false;"><?php echo JText::_('COM_SURVEYFORCE_SF_ADD');?></button>
				                   </div>
				                   <hr/><br/>
				                   <div style="clear:both"><br/></div>
								   <div class="control-group form-inline">
									 <label class="control-label">
									 	<input type="checkbox" name="super_rule" id="super_rule" class="css-checkbox" />
										<label class="css-label cb0" for="super_rule"></label>
										<?php echo JText::_('COM_SURVEYFORCE_SF_GO_TO_QUEST21');?>:
									 </label>
									 <div class="controls">
										<select id="sf_quest_list2" name="sf_quest_list2" style="width:250px;">
											<option value="0">- Select question -</option>
										 </select>
										 <br/>
										 <small><?php echo JText::_('COM_SF_TO_OVERRIDE_THIS_RULE');?></small>
									 </div>
									</div>
								</div>	
	                          </div>
	                        </div>
	                      </div>

	                      <div class="panel-group" id="panelActions" style="display:none;">
	                    	<div class="panel panel-default">
	                          <div class="panel-heading" data-toggle="collapse" data-parent="#panelActions">
	                            <h4><?php echo JText::_('COM_SF_ADDITIONAL_ACTIONS');?></h4>
	                          </div>
	                          <div style="height: auto;" class="panel-collapse collapse in" id="collapseActions">
	                            <div class="panel-body">
	                    			<div id="survey-editor-field-actions" class="property-group clearfix"> <a id="del_field" class="toolbox-action button button-small" onclick="sfRemoveQuestion();"><i class="fa fa-times">&nbsp;<?php echo JText::_('COM_SURVEYFORCE_DELETE');?></i></a> <a id="move_field" class="toolbox-action button button-small" onclick="javascript:sfMoveQuestionTo();"><i class="fa fa-arrows">&nbsp;<?php echo JText::_('COM_SF_MOVE_TO');?></i></a> <a id="duplicate" class="toolbox-action button button-small" onclick="javascript:sfDublicateQuestion();"><i class="fa fa-files-o">&nbsp;<?php echo JText::_('COM_SF_DUPLICATE');?></i></a></div>
	                    		</div>
	                    	  </div>
	                    	</div>
	                    </div>                          
                    </div>
                                    
                  </div>                
                </div>
                <input type="hidden" name="survey_id" value="<?php echo $survey->id;?>" id="survey_id"/>
				<div id="token">
					<?php echo JHTML::_( 'form.token' ); ?>
				</div>
                <input type="hidden" name="sf_step" value="<?php echo ($survey->sf_step ? $survey->sf_step : 3);?>" id="sf_step"/>
                </form>
              </div>
            </div>

			<div class="buttons">
				<a class="btn btn-small" href="#" onclick="sfGoToAddQuestion();"><span class="fa fa-plus"></span><span class="fa-text"><?php echo JText::_('COM_SF_ADD_QUESTION');?></span></a>
				<a class="btn btn-small" href="#" onclick="javascript:sfSaveSurvey(true);"><span class="fa fa-floppy-o"></span><span class="fa-text"><?php echo JText::_('COM_SURVEYFORCE_SAVE');?></span></a>
				<a class="btn btn-small" href="#" onclick="javascript:sfPublishSurvey(<?php echo $survey->id;?>, '<?php echo JURI::root();?>');"><span class="fa fa-check-circle"></span><span class="fa-text">Publish</span></a>
			</div>

			<div class="survey-top">
				<div class="top-left">
		
					<a class="button save-survey" href="#" onclick="javascript:sfSaveSurvey(true);"><span class="fa fa-floppy-o"></span><span class="fa-text"><?php echo JText::_('COM_SURVEYFORCE_SAVE');?></span></a>

					<a class="button" href="#" onclick="sfGoToAddQuestion();"><span class="fa fa-plus"></span><span class="fa-text"><?php echo JText::_('COM_SF_ADD_QUESTION');?></span></a>

					<a class="button" href="#" onclick="javascript:sfPublishSurvey(<?php echo $survey->id;?>, '<?php echo JURI::root();?>');"><span class="fa fa-check-circle"></span><span class="fa-text">Publish</span></a>
				</div>
				<div class="top-right"><?php echo JText::_('COM_SF_PERIOD_OF_AUTOSAVE_MIN');?>
					<input type="text" size="35" class="textbox" id="autosave" value="10"/>
				</div>
			</div>

            <div class="viewport">
				<div class="pages" id="page1">
					<div class="title"><h2 class="title"><?php echo $survey->sf_name;?></h2><p class="description"><?php echo $survey->sf_descr?></p></div>
					<ol id="survey-questions1" class="page active"><li class="placeholder" style="display: list-item;"><?php echo JText::_('COM_SF_YOU_HAVANT_ADDED');?></li></ol>
				</div>

				<div class="page-buttons clearfix"><div class="controls-right"><a class="tool button button-secondary button-tab-bottom" onclick="javascript:sfDeletePage();" title="<?php echo JText::_('COM_SURVEYFORCE_REMOVE_CURRENT_PAGE');?>"><i class="fa fa-times"></i></a></div><div class="controls"><a class="button button-tab-bottom has-tooltip tooltip-computed" onclick="javascript:sfAddPage(true);"><i class="fa fa-plus">&nbsp;<?php echo JText::_('COM_SF_PAGE');?></i></a></div><div class="tabs" style=""><a href="#" class="button button-tab-bottom page-button active" name="0" style="" id="tab1" onclick="javascript:sfSelectPage(1);">1</a></div></div>
				<div class="sf-hints">
	            	<ol>
		            	<li>
							<small>
								<?php echo JText::_('COM_SURVEYFORCE_HINT1')?>	
							</small>
						</li>
						<li>
							<small>
								<?php echo JText::_('COM_SURVEYFORCE_HINT2')?>	
							</small>
						</li>
						<li>
							<small>
								<?php echo JText::_('COM_SURVEYFORCE_HINT3')?>
							</small>
						</li>
					</ol>
	            </div>
            </div>
            <div class="clear"></div>
            
          </div>
        </div>
      </div>
      </div>
    </div>

	<div id="dialog-confirm" title="<?php echo JText::_('COM_SF_DELETE_QUESTION');?>" style="display:none;">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo JText::_('COM_SF_ARE_YOU_SURE_YOU_WISH_TO_REMOVE_QUESTION');?></p>
	</div>
	<div id="dialog-pageremove-confirm" title="<?php echo JText::_('COM_SF_DELETE_PAGE');?>" style="display:none;">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php echo JText::_('COM_SF_ARE_YOU_SURE_YOU_WISH_TO_REMOVE_PAGE');?></p>
	</div>
	<div id="dialog-editor" title="<?php echo JText::_('COM_SF_VISUAL_EDITOR');?>" style="display:none;">
		<textarea id="CKeditor" name="CKeditor"></textarea>
	</div>
	<div id="dialog-move-to" title="<?php echo JText::_('COM_SF_MOVE_QUESTION');?>" style="display:none;">
		<p><?php echo JText::_('COM_SF_MOVE_THE_SELECTED_QUESTION');?>:
			<select data-style="btn" id="sf_move_to" name="sf_move_to" class="form-control">
				<option value="">-Select Page-</option>
			</select>
		</p>
	</div>
	<div id="dialog-bulk" title="<?php echo JText::_('COM_SF_BULK');?>" style="display:none;">
		<p><?php echo JText::_('COM_SF_LOAD_PRESET_BULK');?>:
		<select id="bulk-selector" onchange="javascript:sfRefreshBulkList(this);"><option value="-">---</option><optgroup label="Presets"><option value="Age">Age</option><option value="Employment">Employment</option><option value="Income Level">Income Level</option><option value="Marital Status">Marital Status</option><option value="Race">Race</option><option value="Months">Months</option><option value="Days">Days</option><option value="Canadian Provinces">Canadian Provinces</option><option value="US States">US States</option><option value="Countries">Countries</option><option value="Continents">Continents</option><option value="How Often?">How Often?</option><option value="Frequency">Frequency</option><option value="How Long?">How Long?</option><option value="Satisfaction">Satisfaction</option><option value="Importance">Importance</option><option value="Happiness">Happiness</option><option value="Agreement">Agreement</option><option value="Comparison">Comparison</option><option value="Probability">Probability</option><option value="10 Scale">10 Scale</option><option value="Gender">Gender</option><option value="Years">Years</option></optgroup></select>
		</p>
		<p class="edit"><textarea class="edit" id="bulk_list" disabled="disabled" style="width:480px;height:190px;"></textarea></p>
	</div>
	<iframe src="javascript:void(0);" name="_saveSurvey" style="display:none;"></iframe>

    <!-- ///  start script  /// -->

    <!-- ///  libs  /// -->
           
    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/libs/modernizr-latest.js"></script>        
    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/libs/jquery-1.11.0.min.js"></script>

    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/libs/jquery-ui.min.js"></script>     
    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/bootstrap-3.1.1/js/bootstrap.min.js"></script>   
    <!-- ///  plugins  /// -->
        
    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/plugins/select/bootstrap-select.min.js"></script>
    <script src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/notifIt.js"></script>            
    <script type="text/javascript" src="<?php echo $base_url;?>components/com_surveyforce/views/authoring/tmpl/authoring/assets/js/survey-apps.min.js"></script>
    <script type="text/javascript">
    	$(document).ready(function(){

    		var filename = '<?php echo $survey->sf_image;?>';

    		$("#bkg_thumb span").remove();

    		if(filename!=''){
	          	// Render thumbnail.
	          	var span = document.createElement('span');
	          	span.innerHTML = ['<img class="thumb" src="<?php echo JURI::root();?>images/com_surveyforce/', filename, '" title="', filename, '"/>'].join('');
	          	$("#bkg_thumb").append(span);
	          	$(".pages").css("background", "url(<?php echo JURI::root();?>images/com_surveyforce/" + filename + ")");
	        }

	        var nextButton = '<div class="nextButton"><button class="btn btn-primary" onclick="sfNextStep();return false;">Next step</button></div>';

			if(sf_step == 1){

				$("#questionsButton").removeClass("active");
				$("#surveyButton").addClass("active");
				$("#pageButton").addClass("disabled");
				$("#questionsButton").addClass("disabled");
				
				$("#survey").addClass("active");
				$("#page").css("display", "none");
				$("#questions").css("display", "none");
				
				$("#pageButton a, #questionsButton a").click(function(){
					return false;
				});

				$("#survey").append(nextButton);

				$(".not-done").css('left', '7px');
				$(".not-done").css('width', '450px');
			}
		
			if(sf_step == 2){

				$("#questionsButton").removeClass("active");
				$("#questionsButton").addClass("active");
				$("#pageButton").addClass("disabled");
				
				$("#questions").addClass("active");
				$("#page").css("display", "none");
				
				$("#pageButton a").click(function(){
					return false;
				});

				$("#questions").append(nextButton);

				$(".done").css('left', '7px');
				$(".done").css('width', '148px');
				$(".not-done").css('left', '154px');
				$(".not-done").css('width', '300px');
			}

    	});
    </script>
</body>
</html>