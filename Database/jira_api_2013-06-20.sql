# ************************************************************
# Sequel Pro SQL dump
# バージョン 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# ホスト: 127.0.0.1 (MySQL 5.5.29)
# データベース: jira_api
# 作成時刻: 2013-06-20 16:09:23 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# テーブルのダンプ ATTACHMENTS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ATTACHMENTS`;

CREATE TABLE `ATTACHMENTS` (
  `attachment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `issue_key` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `attachment_size` int(11) DEFAULT NULL,
  `attachment_author_username` varchar(255) DEFAULT NULL,
  `attachment_created` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attachment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ COMMENTS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `COMMENTS`;

CREATE TABLE `COMMENTS` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `issue_key` varchar(50) DEFAULT NULL,
  `comment_author_username` varchar(50) DEFAULT NULL,
  `comment_created` varchar(50) DEFAULT NULL,
  `comment_content` text,
  `data_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ COMPONENT_TO_ISSUE
# ------------------------------------------------------------

DROP TABLE IF EXISTS `COMPONENT_TO_ISSUE`;

CREATE TABLE `COMPONENT_TO_ISSUE` (
  `issue_id` int(11) NOT NULL DEFAULT '0',
  `component_id` int(11) NOT NULL DEFAULT '0',
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_id`,`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ COMPONENTS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `COMPONENTS`;

CREATE TABLE `COMPONENTS` (
  `component_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `component_name` varchar(255) DEFAULT NULL,
  `component_info_url` varchar(255) DEFAULT NULL,
  `component_description` text,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ CRONLOGS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `CRONLOGS`;

CREATE TABLE `CRONLOGS` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT '0',
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `api_calls` int(11) DEFAULT '0',
  `db_entries` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ CUSTOMFIELDS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `CUSTOMFIELDS`;

CREATE TABLE `CUSTOMFIELDS` (
  `customfield_id` varchar(255) NOT NULL DEFAULT '',
  `customfield_key` varchar(255) DEFAULT NULL,
  `customfield_name` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customfield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ CUSTOMFIELDVALUES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `CUSTOMFIELDVALUES`;

CREATE TABLE `CUSTOMFIELDVALUES` (
  `issue_key` varchar(255) NOT NULL DEFAULT '',
  `customfield_id` varchar(255) NOT NULL DEFAULT '',
  `customfield_value` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_key`,`customfield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ETC
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ETC`;

CREATE TABLE `ETC` (
  `etc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `etc_field_name` varchar(255) DEFAULT NULL,
  `etc_value` text,
  `issue_key` varchar(255) DEFAULT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`etc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ FIELDS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `FIELDS`;

CREATE TABLE `FIELDS` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `issue_key` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `field_name` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ FIXVERSION_TO_ISSUE
# ------------------------------------------------------------

DROP TABLE IF EXISTS `FIXVERSION_TO_ISSUE`;

CREATE TABLE `FIXVERSION_TO_ISSUE` (
  `issue_id` int(11) NOT NULL DEFAULT '0',
  `fixversion_id` int(11) NOT NULL DEFAULT '0',
  `data_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_id`,`fixversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ FIXVERSIONS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `FIXVERSIONS`;

CREATE TABLE `FIXVERSIONS` (
  `fixversion_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fixversion_name` varchar(255) DEFAULT NULL,
  `fixversion_info_url` varchar(255) DEFAULT NULL,
  `fixversion_archived` varchar(255) DEFAULT NULL,
  `fixversion_released` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fixversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ISSUE_COMPONENT
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUE_COMPONENT`;

CREATE TABLE `ISSUE_COMPONENT` (
  `issue_key` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `component_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_key`,`component_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# テーブルのダンプ ISSUE_FIXVERSION
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUE_FIXVERSION`;

CREATE TABLE `ISSUE_FIXVERSION` (
  `issue_key` varchar(255) NOT NULL DEFAULT '',
  `fix_version_name` varchar(255) NOT NULL DEFAULT '',
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_key`,`fix_version_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ISSUE_LINK_TYPES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUE_LINK_TYPES`;

CREATE TABLE `ISSUE_LINK_TYPES` (
  `issue_link_type_id` int(11) unsigned NOT NULL,
  `issue_link_type_name` varchar(50) DEFAULT NULL,
  `inward_link_description` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ISSUE_LINKS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUE_LINKS`;

CREATE TABLE `ISSUE_LINKS` (
  `issue_1_id` int(11) NOT NULL DEFAULT '0',
  `issue_2_id` int(11) NOT NULL DEFAULT '0',
  `issue_link_type_id` int(11) NOT NULL DEFAULT '0',
  `issue_1_key` varchar(50) DEFAULT NULL,
  `inward_link_description` varchar(50) DEFAULT NULL,
  `issue_2_key` varchar(50) DEFAULT NULL,
  `data_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_1_id`,`issue_2_id`,`issue_link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ISSUE_TYPES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUE_TYPES`;

CREATE TABLE `ISSUE_TYPES` (
  `issue_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_name` varchar(255) DEFAULT NULL,
  `issue_type_description` varchar(255) DEFAULT NULL,
  `issue_type_icon_url` varchar(255) DEFAULT NULL,
  `issue_type_info_url` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ ISSUES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ISSUES`;

CREATE TABLE `ISSUES` (
  `issue_key` varchar(255) NOT NULL DEFAULT '',
  `issue_id` int(11) DEFAULT NULL,
  `issue_title` varchar(255) DEFAULT NULL,
  `issue_link` varchar(255) DEFAULT NULL,
  `issue_summary` varchar(255) DEFAULT NULL,
  `issue_description` text,
  `issue_environment` varchar(255) DEFAULT NULL,
  `parent_issue_key` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `work_ratio` int(11) DEFAULT NULL,
  `project_key` varchar(10) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `issue_assignee_username` varchar(50) DEFAULT NULL,
  `issue_assignee_displayname` varchar(50) DEFAULT NULL,
  `issue_reporter_username` varchar(50) DEFAULT NULL,
  `issue_reporter_displayname` varchar(50) DEFAULT NULL,
  `issue_created` varchar(50) DEFAULT NULL,
  `issue_updated` varchar(50) DEFAULT NULL,
  `resolution_name` varchar(255) DEFAULT NULL,
  `issue_resolved_date` varchar(50) DEFAULT NULL,
  `issue_views` int(11) DEFAULT NULL,
  `issue_votes` int(11) DEFAULT NULL,
  `issue_watches` int(11) DEFAULT NULL,
  `issue_component` varchar(255) DEFAULT NULL,
  `issue_due` varchar(255) DEFAULT NULL,
  `time_original_estimate` int(11) DEFAULT NULL,
  `aggregate_original_estimate` int(11) DEFAULT NULL,
  `aggregate_time_original_estimate` int(11) DEFAULT NULL,
  `aggregate_time_remaining_estimate` int(11) DEFAULT NULL,
  `aggregate_time_estimate` int(11) DEFAULT NULL,
  `aggregate_time_spent` int(11) DEFAULT NULL,
  `time_estimate` int(11) DEFAULT NULL,
  `time_spent` int(11) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ LABELS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `LABELS`;

CREATE TABLE `LABELS` (
  `issue_key` varchar(255) NOT NULL DEFAULT '',
  `label_value` varchar(255) NOT NULL DEFAULT '',
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_key`,`label_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ PRIORITIES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `PRIORITIES`;

CREATE TABLE `PRIORITIES` (
  `priority_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `priority_name` varchar(255) DEFAULT NULL,
  `priority_icon_url` varchar(255) DEFAULT NULL,
  `priority_info_url` varchar(255) DEFAULT NULL,
  `priority_description` varchar(255) DEFAULT NULL,
  `priority_status_color` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`priority_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ PROJECTS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `PROJECTS`;

CREATE TABLE `PROJECTS` (
  `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_key` varchar(255) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `project_info_url` varchar(255) DEFAULT NULL,
  `16x16` varchar(255) DEFAULT NULL,
  `48x48` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ RESOLUTIONS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `RESOLUTIONS`;

CREATE TABLE `RESOLUTIONS` (
  `resolution_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resolution_name` varchar(255) DEFAULT NULL,
  `resolution_info_url` varchar(255) DEFAULT NULL,
  `resolution_description` text,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`resolution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ SECURITIES
# ------------------------------------------------------------

DROP TABLE IF EXISTS `SECURITIES`;

CREATE TABLE `SECURITIES` (
  `security_id` int(11) unsigned NOT NULL,
  `security_name` varchar(255) DEFAULT NULL,
  `security_description` text,
  `security_info_url` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`security_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ STATUS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `STATUS`;

CREATE TABLE `STATUS` (
  `status_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(255) DEFAULT NULL,
  `status_icon_url` varchar(255) DEFAULT NULL,
  `status_description` varchar(255) DEFAULT NULL,
  `status_info_url` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ SUBTASKS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `SUBTASKS`;

CREATE TABLE `SUBTASKS` (
  `subtask_issue_key` varchar(255) NOT NULL DEFAULT '',
  `parent_issue_key` varchar(255) DEFAULT NULL,
  `subtask_id` int(11) DEFAULT NULL,
  `subtask_info_url` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subtask_issue_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ USERS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `USERS`;

CREATE TABLE `USERS` (
  `username` varchar(20) NOT NULL DEFAULT '',
  `user_display_name` varchar(255) DEFAULT NULL,
  `user_email_address` varchar(255) DEFAULT NULL,
  `user_info_url` varchar(255) DEFAULT NULL,
  `user_active` tinyint(1) DEFAULT NULL,
  `user_time_zone` varchar(255) DEFAULT NULL,
  `16x16` varchar(255) DEFAULT NULL,
  `48x48` varchar(255) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ VERSION_TO_ISSUE
# ------------------------------------------------------------

DROP TABLE IF EXISTS `VERSION_TO_ISSUE`;

CREATE TABLE `VERSION_TO_ISSUE` (
  `issue_id` int(11) NOT NULL DEFAULT '0',
  `version_id` int(11) NOT NULL DEFAULT '0',
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`issue_id`,`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# テーブルのダンプ VERSIONS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `VERSIONS`;

CREATE TABLE `VERSIONS` (
  `version_id` int(11) unsigned NOT NULL,
  `version_name` varchar(255) DEFAULT NULL,
  `version_description` text,
  `version_info_url` varchar(255) DEFAULT NULL,
  `version_archived` int(11) DEFAULT NULL,
  `version_released` int(11) DEFAULT NULL,
  `version_release_date` varchar(11) DEFAULT NULL,
  `data_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ WORKLOGS
# ------------------------------------------------------------

DROP TABLE IF EXISTS `WORKLOGS`;

CREATE TABLE `WORKLOGS` (
  `worklog_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) DEFAULT NULL,
  `project_key` varchar(255) DEFAULT NULL,
  `issue_key` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `work_date` datetime DEFAULT NULL,
  `hours` float DEFAULT NULL,
  `data_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`worklog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
