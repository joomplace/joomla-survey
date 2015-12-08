<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );
$contents = "<table>
			<tr>
				<td style=\"vertical-align: top;padding:4px\">
				<img src='".JURI::base()."components/com_community/assets/error.gif' alt='' />
				</td>
				<td style=\"vertical-align: top;padding:4px\">
				 " .JText::_('PLG_SURVEYFORCE_NOT_INSTALLED') . "
				</td>
			</tr>
			</table>";

echo $contents;			

?>