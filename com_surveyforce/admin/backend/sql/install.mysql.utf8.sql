CREATE TABLE IF NOT EXISTS `#__survey_force_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

--  `#__survey_force_config`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_config` (
`config_var` varchar(50) NOT NULL default '',
`config_value` text NOT NULL
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_user_chain`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_user_chain` (
`id` int(11) NOT NULL auto_increment,
`start_id` int(11) NOT NULL default '0',
`survey_id` int(11) NOT NULL default '0',
`unique_id` varchar(32) default '',
`invite_id` int(11) NOT NULL default '0',
`sf_time` int(11) NOT NULL default '0',
`sf_chain` text,
PRIMARY KEY  (`id`),
KEY `start_id` (`start_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_previews`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_previews` (
`id` int(11) NOT NULL auto_increment,
`start_id` int(11) NOT NULL default '0',
`survey_id` int(11) NOT NULL default '0',
`unique_id` varchar(32) NOT NULL default '',
`preview_id` varchar( 32 ) NOT NULL default '',
`time` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `start_id` (`start_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_templates`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_templates` (
`id` int(11) NOT NULL auto_increment,
`sf_name` varchar(250) NOT NULL default '',
`sf_display_name` VARCHAR( 255 ) NOT NULL,
`display` tinyint(1) DEFAULT '1' NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_cats`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_cats` (
`id` int(11) NOT NULL auto_increment,
`sf_catname` varchar(250) NOT NULL default '',
`sf_catdescr` text NOT NULL,
`published` tinyint(4) NOT NULL default '1',
`user_id` int( 11 ) DEFAULT '0' NOT NULL,
PRIMARY KEY  (`id`),
UNIQUE KEY `sf_catname` (`sf_catname`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_emails`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_emails` (
`id` int(11) NOT NULL auto_increment,
`email_subject` varchar(100) NOT NULL default '',
`email_body` text NOT NULL,
`email_reply` varchar(100) NOT NULL default '',
`user_id` int( 11 ) DEFAULT '0' NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_fields`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_fields` (
`id` int(11) NOT NULL auto_increment,
`quest_id` int(11) NOT NULL default '0',
`ftext` text NOT NULL,
`alt_field_id` int(11) NOT NULL default '0',
`is_main` int(11) NOT NULL default '0',
`is_true` int(11) NOT NULL default '0',
`ordering` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `quest_id` (`quest_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_invitations`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_invitations` (
`id` int(11) NOT NULL auto_increment,
`invite_num` varchar(32) NOT NULL default '',
`user_id` int(11) NOT NULL default '0',
`inv_status` tinyint(4) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `user_id` (`user_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_iscales`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_iscales` (
`id` int(11) NOT NULL auto_increment,
`iscale_name` varchar(100) NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_iscales_fields`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_iscales_fields` (
`id` int(11) NOT NULL auto_increment,
`iscale_id` int(11) NOT NULL,
`isf_name` varchar(50) NOT NULL,
`ordering` int(11) NOT NULL,
PRIMARY KEY  (`id`),
KEY `iscale_id` (`iscale_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_listusers`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_listusers` (
`id` int(11) NOT NULL auto_increment,
`listname` varchar(50) NOT NULL default '',
`survey_id` int(11) NOT NULL default '0',
`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
`date_invited` datetime NOT NULL default '0000-00-00 00:00:00',
`date_remind` datetime NOT NULL default '0000-00-00 00:00:00',
`is_invited` tinyint(4) NOT NULL default '0',
`sf_author_id` int(11) NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_quests`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_quests` (
`id` int(11) NOT NULL auto_increment,
`sf_survey` int(11) NOT NULL default '0',
`sf_qtype` int(11) NOT NULL default '0',
`sf_qtext` text NOT NULL,
`sf_impscale` int(11) NOT NULL default '0',
`sf_rule` int(11) NOT NULL default '0',
`sf_fieldtype` varchar(255) NOT NULL default '',
`ordering` int(11) NOT NULL default '0',
`sf_compulsory` TINYINT DEFAULT '1' NOT NULL,
`sf_section_id` int(11) DEFAULT '0' NOT NULL,
`published` tinyint(4) NOT NULL default '0',
`sf_qstyle` INT( 11 ) DEFAULT '0' NOT NULL,
`sf_num_options` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_default_hided` TINYINT(4) DEFAULT '0' NOT NULL,
`is_final_question` TINYINT(3) DEFAULT '0' NOT NULL,
PRIMARY KEY  (`id`),
KEY `sf_survey` (`sf_survey`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_rules`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_rules` (
`id` int(11) NOT NULL auto_increment,
`quest_id` int(11) NOT NULL default '0',
`answer_id` int(11) NOT NULL default '0',
`next_quest_id` int(11) NOT NULL default '0',
`alt_field_id` INT(11) DEFAULT '0' NOT NULL,
`priority` INT(11) DEFAULT '0' NOT NULL,
PRIMARY KEY  (`id`),
KEY `quest_id` (`quest_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_scales`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_scales` (
`id` int(11) NOT NULL auto_increment,
`quest_id` int(11) NOT NULL default '0',
`stext` varchar(250) NOT NULL default '',
`ordering` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `quest_id` (`quest_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_survs`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_survs` (
`id` int(11) NOT NULL auto_increment,
`sf_name` varchar(250) NOT NULL default '',
`sf_descr` text NOT NULL,
`sf_image` varchar(50) NOT NULL default '',
`sf_cat` int(11) NOT NULL default '0',
`sf_lang` int(11) NOT NULL default '0',
`sf_date` datetime NOT NULL default '0000-00-00 00:00:00',
`sf_author` int(11) NOT NULL default '0',
`sf_public` tinyint(4) NOT NULL default '0',
`sf_invite` tinyint(4) NOT NULL default '0',
`sf_reg` tinyint(4) NOT NULL default '0',
`published` tinyint(4) NOT NULL default '0',
`sf_fpage_type` tinyint(4) DEFAULT '0' NOT NULL ,
`sf_fpage_text` TEXT,
`sf_special` TEXT NOT NULL,
`sf_auto_pb` tinyint(4) DEFAULT '1' NOT NULL,
`sf_progressbar` TINYINT( 4 ) DEFAULT '1' NOT NULL,
`sf_progressbar_type` TINYINT(1) DEFAULT '0' NOT NULL,
`asset_id` int(10) NOT NULL default '0',
`sf_use_css` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_enable_descr` TINYINT(4) DEFAULT '1' NOT NULL,
`sf_reg_voting` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_inv_voting` TINYINT(4) DEFAULT '1' NOT NULL,
`sf_template` INT( 11 ) DEFAULT '1' NOT NULL,
`sf_pub_voting` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_pub_control` TINYINT(4) DEFAULT '0' NOT NULL,
`surv_short_descr` TEXT,
`sf_after_start` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_redirect_enable` TINYINT(3) DEFAULT '0' NOT NULL,
`sf_redirect_url` VARCHAR( 250 ) DEFAULT '',
`sf_redirect_delay` INT(15) DEFAULT '0' NOT NULL,
`sf_prev_enable` TINYINT(3) DEFAULT '1' NOT NULL,
`sf_anonymous` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_friend` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_friend_voting` TINYINT(4) DEFAULT '0' NOT NULL,
`sf_random` TINYINT(4) DEFAULT '0' NOT NULL,
PRIMARY KEY  (`id`),
KEY `sf_cat` (`sf_cat`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_user_ans_txt`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_user_ans_txt` (
`id` int(11) NOT NULL auto_increment,
`start_id` int(11) NOT NULL default '0',
`ans_txt` TEXT NOT NULL,
PRIMARY KEY  (`id`),
KEY `start_id` (`start_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_user_answers`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_user_answers` (
`id` int(11) NOT NULL auto_increment,
`start_id` int(11) NOT NULL default '0',
`survey_id` int(11) NOT NULL default '0',
`quest_id` int(11) NOT NULL default '0',
`answer` int(11) NOT NULL default '0',
`ans_field` int(11) NOT NULL default '0',
`next_quest_id` int(11) NOT NULL default '0',
`sf_time` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`id`),
KEY `start_id` (`start_id`),
KEY `ua_index` (`quest_id`,`survey_id`,`start_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_user_answers_imp`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_user_answers_imp` (
`id` int(11) NOT NULL auto_increment,
`start_id` int(11) NOT NULL,
`survey_id` int(11) NOT NULL,
`quest_id` int(11) NOT NULL,
`iscale_id` int(11) NOT NULL,
`iscalefield_id` int(11) NOT NULL,
`sf_imptime` datetime default '0000-00-00 00:00:00',
PRIMARY KEY  (`id`),
KEY `ua_imp_index` (`quest_id`,`survey_id`,`iscale_id`,`start_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_user_starts`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_user_starts` (
`id` int(11) NOT NULL auto_increment,
`unique_id` varchar(32) NOT NULL default '',
`usertype` tinyint(4) NOT NULL default '0',
`user_id` int(11) NOT NULL default '0',
`invite_id` int(11) NOT NULL default '0',
`sf_time` datetime NOT NULL default '0000-00-00 00:00:00',
`survey_id` int(11) NOT NULL default '0',
`is_complete` tinyint(4) NOT NULL default '0',
`sf_ip_address` VARCHAR(255) DEFAULT '' NOT NULL,
PRIMARY KEY  (`id`),
KEY `survey_id` (`survey_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_users`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_users` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(50) NOT NULL default '',
`lastname` varchar(50) NOT NULL default '',
`email` varchar(100) NOT NULL default '',
`list_id` int(11) NOT NULL default '0',
`invite_id` int(11) NOT NULL default '0',
`is_invited` int(11) NOT NULL default '0',
`is_reminded` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `list_id` (`list_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_qsections`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_qsections` (
`id` int(11) NOT NULL auto_increment,
`sf_name` varchar(250) NOT NULL default '',
`addname` tinyint(4) default '0' NOT NULL,
`ordering` tinyint(4) NOT NULL default '0',
`sf_survey_id` int(11) NOT NULL default '0',
PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_def_answers`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_def_answers` (
`id` int(11) NOT NULL auto_increment,
`survey_id` int(11) NOT NULL default '0',
`quest_id` int(11) NOT NULL default '0',
`answer` int(11) NOT NULL default '0',
`ans_field` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_authors`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_authors` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_quest_show`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_quest_show` (
`id` int(11) NOT NULL auto_increment,
`quest_id` int(11) NOT NULL default '0',
`survey_id` int(11) NOT NULL default '0',
`quest_id_a` int(11) NOT NULL default '0',
`answer` int(11) NOT NULL default '0',
`ans_field` int(11) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `quest_id` (`quest_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
--
--  `#__survey_force_dashboard_items`
--
CREATE TABLE IF NOT EXISTS `#__survey_force_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__survey_force_qtypes`;
CREATE TABLE IF NOT EXISTS `#__survey_force_qtypes` (
`id` int(11) NOT NULL auto_increment,
`sf_qtype` varchar(50) NOT NULL default '',
`sf_plg_name` varchar(128) NOT NULL,
PRIMARY KEY  (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (1, 'LikertScale', 'likertscale');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (2, 'PickOne', 'pickone');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (3, 'PickMany', 'pickmany');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (4, 'Short Answer', 'shortanswer');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (5, 'Ranking Drop-Down', 'rankingdropdown');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (6, 'Ranking Drag''and''Drop', 'rankingdraganddrop');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (7, 'Boilerplate', 'boilerplate');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (8, 'Page Break', 'pagebreak');
INSERT INTO #__survey_force_qtypes (id, sf_qtype, sf_plg_name) VALUES (9, 'Ranking', 'ranking');

UPDATE #__survey_force_config SET config_value = '3.1.1.001' WHERE config_var = 'sf_version';

UPDATE #__survey_force_templates SET `sf_display_name` = 'Standart template' WHERE sf_name = 'surveyforce_standart';
UPDATE #__survey_force_templates SET `sf_display_name` = 'New style template' WHERE sf_name = 'surveyforce_new';
UPDATE #__survey_force_templates SET `sf_display_name` = 'Pretty Green template' WHERE sf_name = 'surveyforce_pretty_green';
UPDATE #__survey_force_templates SET `sf_display_name` = 'Pretty Blue template' WHERE sf_name = 'surveyforce_pretty_blue';