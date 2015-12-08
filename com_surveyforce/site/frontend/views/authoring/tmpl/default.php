<?php
/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Surveyforce Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

$is_author = @$this->is_author;
$authoring = @$this->authoring;

if ( !$is_author ) {
	echo SurveyforceTemplates::Survey_blocked( $this->sf_config );
} else {
?>
<script type="text/javascript">
	function isChecked(a,b){if("undefined"===typeof b&&(b=document.getElementById("adminForm"),!b))b=document.adminForm;!0==a?b.boxchecked.value++:b.boxchecked.value--}
	function checkAll (a,b){b||(b="cb");if(a.form){for(var c=0,d=0,f=a.form.elements.length;d<f;d++){var e=a.form.elements[d];if(e.type==a.type&&(b&&0==e.id.indexOf(b)||!b))e.checked=a.checked,c+=!0==e.checked?1:0}if(a.form.boxchecked)a.form.boxchecked.value=c;return!0}return!1};
</script>
	<style>#limit { width: 55px; }</style>
<?php
	echo $this->html;

}