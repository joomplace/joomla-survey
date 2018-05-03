<?php
/**
 * Survey Force Deluxe Component for Joomla 3
 * @package SurveyForce
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS', '/');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_surveyforceInstallerScript
{

	function uninstall($parent)
	{
		echo '<p>' . JText::_('COM_SURVEYFORCE_SURVEYFORCE_COMPONENT_UNINSTALLED') . '</p>';
	}

	function update($parent)
	{
		echo '<font style="font-size:2em; color:#55AA55;" >' . JText::_('COM_SURVEYFORCE_UPDATE_TEXT') . '</font><br/><br/>';
		if(file_exists(JPATH_ADMINISTRATOR .'/components/com_surveyforce/surveyforce.xml')){
			$xml = JFactory::getXML(JPATH_ADMINISTRATOR .'/components/com_surveyforce/surveyforce.xml');
			$this->version_from = $version = preg_split( '/(\s|\.)/', $xml->version );
			if($version[1]<=3 && $version[2]<=2 && $version[3]<6){
				$db = JFactory::getDbo();
                $sql = "UPDATE `#__survey_force_user_answers` ua LEFT JOIN `#__survey_force_quests` q ON ua.`quest_id`=q.`id` SET ua.`answer`=(@temp:=ua.`answer`), ua.`answer`=ua.`ans_field`, ua.`ans_field`=@temp WHERE q.`sf_qtype`=9";
                $db->setQuery($sql);
				$db->setQuery($query);
			}
		}
	}

	function preflight($type, $parent)	// before install/update
	{

	}

	function postflight($type, $parent)	// after install/update
	{
		$db = JFactory::getDBO();

		$dashboard = array(
			'Categories' =>  array('link'=>'categories', 'img'=>'icon-48-category.png'),
			'Surveys' =>  array('link'=>'surveys', 'img'=>'icon-48-surveys.png'),
			'User Lists' =>  array('link'=>'listusers', 'img'=>'icon-48-user.png'),
			'Authors' =>  array('link'=>'authors', 'img'=>'icon-48-authors.png'),
			'E-mails List' =>  array('link'=>'emails', 'img'=>'icon-48-email-lists.png'),
			'Reports' =>  array('link'=>'reports', 'img'=>'icon-48-reports.png'),
			'Configuration' =>  array('link'=>'configuration', 'img'=>'icon-48-config.png'),
			'Sample surveys' =>  array('link'=>'samples', 'img'=>'icon-48-sample.png'),
			'Help' =>  array('link'=>'http://www.joomplace.com/video-tutorials-and-documentation/survey-force-deluxe/index.html?administrators_guide.htm', 'img'=>'icon-48-help.png'),
		);

		foreach ($dashboard as $item_name => $params)
		{
			$db->setQuery("DELETE FROM `#__survey_force_dashboard_items` WHERE `title` = '".$item_name."'");
			$db->execute();

			$href = 'index.php?option=com_surveyforce&view='.$params['link'];
			if ( strpos($params['link'], 'http://') !== false )
				$href = $params['link'];

			$db->setQuery("INSERT IGNORE INTO `#__survey_force_dashboard_items` (`title`, `url`, `icon`, `published`) VALUES
							('".$item_name."', '".$href."', '".JUri::root()."administrator/components/com_surveyforce/assets/images/".$params['img']."', 1)");
			$db->execute();
		}

		$newColumns = array(
			'survs' => array(
				'asset_id' => "INT( 10 ) NOT NULL",
				'sf_use_css' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_enable_descr' => "TINYINT( 4 ) DEFAULT '1' NOT NULL",
				'sf_progressbar_type' => "TINYINT( 1 ) DEFAULT '0' NOT NULL AFTER `sf_progressbar`",
				'sf_reg_voting' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_inv_voting' => "TINYINT( 4 ) DEFAULT '1' NOT NULL",
				'sf_template' => "INT( 11 ) DEFAULT '1' NOT NULL",
				'sf_pub_voting' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_pub_control' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'surv_short_descr' => "TEXT",
				'sf_after_start' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_redirect_enable' => "TINYINT( 3 ) DEFAULT '0' NOT NULL",
				'sf_redirect_url' => "VARCHAR( 250 ) DEFAULT ''",
				'sf_redirect_delay' => "INT( 15 ) DEFAULT '0' NOT NULL",
				'sf_prev_enable' => "TINYINT( 3 ) DEFAULT '1' NOT NULL",
				'sf_anonymous' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_friend' => "TINYINT( 4 ) DEFAULT '0' NOT NULL AFTER `sf_reg`",
				'sf_friend_voting' => "TINYINT( 4 ) DEFAULT '0' NOT NULL AFTER `sf_reg_voting`",
				'sf_random' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'sf_step' => "INT(3) NOT NULL",
				'sf_date_started'=> "DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00'",
				'sf_date_expired'=> "DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00'",
			),
			'quests' => array(
				'sf_default_hided' => "TINYINT( 4 ) DEFAULT '0' NOT NULL",
				'is_final_question' => "TINYINT( 3 ) DEFAULT '0' NOT NULL",
				'sf_qdescr' => "TEXT NOT NULL"
			),
			'user_starts' => array(
				'sf_ip_address' => "VARCHAR(255) DEFAULT '' NOT NULL",
			),
			'emails' => array(
				'user_id' => "INT( 11 ) DEFAULT '0' NOT NULL",
			),
			'cats' => array(
				'ordering' => "INT(11) NOT NULL",
				'user_id' => "INT( 11 ) DEFAULT '0' NOT NULL",
				'published' => "TINYINT(4) NOT NULL DEFAULT '1' AFTER `sf_catdescr`",
			),
			'templates' => array(
				'sf_display_name' => "VARCHAR( 255 ) NOT NULL",
				'display' => "TINYINT( 1 ) DEFAULT '1' NOT NULL",
			),
		);

		foreach ($newColumns as $table => $fields)
		{
			$oldColumns = $db->getTableColumns('#__survey_force_'.$table);

			foreach ( $fields as $key => $value)
			{
				if ( empty($oldColumns[$key]) )
				{
					$db->setQuery('ALTER TABLE `#__survey_force_'.$table.'` ADD `'.$key.'` '.$value);
					$db->execute();
					if($key == 'sf_date_expired')
					{
						$db->setQuery('UPDATE `#__survey_force_'.$table.'` SET `sf_date_expired`=`sf_date`');
						$db->execute();
						$db->setQuery('ALTER TABLE `#__survey_force_'.$table.'` DROP `sf_date`');
						$db->execute();
					}
				}
			}
		}

		$newKeys = array(
			'user_answers' => array( 'name'=>'surv_ind',   'key' => 'survey_id' ),
			'user_answers' => array( 'name'=>'quest_ind' , 'key' => 'quest_id' ),
			'user_answers' => array( 'name'=>'ans_ind' ,   'key' => 'answer' ),
			'fields' => array(  'name'=>'quest_ind',  'key' => 'quest_id' ),
			'scales' => array(  'name'=>'quest_ind' , 'key' => 'quest_id' ),
		);

		foreach ($newKeys as $table => $key)
		{
			$tableKeys = $db->getTableKeys('#__survey_force_'.$table);
			foreach ($tableKeys as $keyInfo)
			{
				if ( $keyInfo->Key_name == $key['name'] )
					$exists = true;
			}

			if ( empty($exists) )
			{
				$db->setQuery("ALTER TABLE `#__survey_force_".$table."` ADD INDEX `".$key['name']."` (`".$key['key']."`)");
			}
		}

		$insertSql = array(
			"TRUNCATE TABLE `#__survey_force_config`;",
			"DELETE FROM `#__survey_force_cats` WHERE `id` = 1;",
			"INSERT INTO `#__survey_force_cats` (id, sf_catname, sf_catdescr, published) VALUES (1, 'Uncategorised', 'default', 1);",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('sf_version', '3.1.1.003');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_aqua_color1', 'F2F2F2');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_aqua_color2', 'E7E7E7');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_aqua_color3', 'EFEFEF');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_aqua_color4', 'FDFDFD');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_axis_color1', 'C9C9C9');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_axis_color2', '9E9E9E');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_bar_color1', '2A47B5');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_bar_color2', '21388F');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_bar_color3', 'ACACD2');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_bar_color4', '75758F');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_height', '300');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('b_width', '500');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_cont', '666666');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_drag', 'cccccc');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_highlight', 'eeeeee');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_aqua_color1', 'F2F2F2');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_aqua_color2', 'E7E7E7');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_aqua_color3', 'EFEFEF');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_aqua_color4', 'FDFDFD');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_axis_color1', 'C9C9C9');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_axis_color2', '9E9E9E');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_height', '300');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('p_width', '500');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('sf_enable_lms_integration', '0');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('sf_enable_jomsocial_integration', '0');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('sf_result_type', 'Bar');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('fe_lang', 'default');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_border', '000000');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_text', '333333');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_completed', 'cccccc');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('color_uncompleted', 'ffffff');",
			"INSERT INTO `#__survey_force_config` (`config_var`, `config_value`) VALUES ('fe_lang', 'default');",
			"DELETE FROM `#__survey_force_templates` WHERE id IN (1,2,3,4);",
			"INSERT INTO `#__survey_force_templates` (id, sf_name) VALUES (2, 'surveyforce_new');",
			"INSERT INTO `#__survey_force_templates` (id, sf_name) VALUES (1, 'surveyforce_standart');",
			"INSERT INTO `#__survey_force_templates` (id, sf_name) VALUES (3, 'surveyforce_pretty_green');",
			"INSERT INTO `#__survey_force_templates` (id, sf_name) VALUES (4, 'surveyforce_pretty_blue');",
			"DELETE FROM `#__survey_force_iscales` WHERE `id` = 1;",
			"INSERT INTO `#__survey_force_iscales` (id, iscale_name) VALUES (1, 'How important is this question for you?');",
			"DELETE FROM `#__survey_force_iscales_fields` WHERE `iscale_id` = 1;",
			"INSERT INTO `#__survey_force_iscales_fields` (iscale_id, isf_name, ordering) VALUES (1, 'Not at all', 0);",
			"INSERT INTO `#__survey_force_iscales_fields` (iscale_id, isf_name, ordering) VALUES (1, 'Not important', 1);",
			"INSERT INTO `#__survey_force_iscales_fields` (iscale_id, isf_name, ordering) VALUES (1, 'Neutral', 2);",
			"INSERT INTO `#__survey_force_iscales_fields` (iscale_id, isf_name, ordering) VALUES (1, 'Important', 3);",
			"INSERT INTO `#__survey_force_iscales_fields` (iscale_id, isf_name, ordering) VALUES (1, 'Very important', 4);",
		);

		foreach ( $insertSql as $sql)
		{
			$db->setQuery($sql);
			$db->execute();
		}

		$updateSql = array(
			"UPDATE `#__survey_force_templates` SET display =0 WHERE sf_name = 'surveyforce_new'",
			"UPDATE `#__survey_force_config` SET config_value = '3.2.0.001' WHERE config_var = 'sf_version';",

			"UPDATE `#__survey_force_templates` SET `sf_display_name` = 'Standart template' WHERE sf_name = 'surveyforce_standart'",
			"UPDATE `#__survey_force_templates` SET `sf_display_name` = 'New style template' WHERE sf_name = 'surveyforce_new'",
			"UPDATE `#__survey_force_templates` SET `sf_display_name` = 'Pretty Green template' WHERE sf_name = 'surveyforce_pretty_green'",
			"UPDATE `#__survey_force_templates` SET `sf_display_name` = 'Pretty Blue template' WHERE sf_name = 'surveyforce_pretty_blue'",
			"UPDATE `#__survey_force_quests` SET `sf_impscale` = '0' WHERE sf_qtype = '7'",
		);

		foreach ( $updateSql as $sql )
		{
			$db->setQuery($sql);
			$db->execute();
		}
	}

}