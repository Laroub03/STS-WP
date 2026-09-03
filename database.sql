-- MySQL dump 10.13  Distrib 8.4.0, for Win64 (x86_64)
--
-- Host: ::1    Database: local
-- ------------------------------------------------------
-- Server version	8.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_actionscheduler_actions`
--

DROP TABLE IF EXISTS `wp_actionscheduler_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_actions` (
  `action_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hook` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `scheduled_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `scheduled_date_local` datetime DEFAULT '0000-00-00 00:00:00',
  `priority` tinyint unsigned NOT NULL DEFAULT '10',
  `args` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schedule` longtext COLLATE utf8mb4_unicode_520_ci,
  `group_id` bigint unsigned NOT NULL DEFAULT '0',
  `attempts` int NOT NULL DEFAULT '0',
  `last_attempt_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `last_attempt_local` datetime DEFAULT '0000-00-00 00:00:00',
  `claim_id` bigint unsigned NOT NULL DEFAULT '0',
  `extended_args` varchar(8000) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`action_id`),
  KEY `hook_status_scheduled_date_gmt` (`hook`(163),`status`,`scheduled_date_gmt`),
  KEY `status_scheduled_date_gmt` (`status`,`scheduled_date_gmt`),
  KEY `scheduled_date_gmt` (`scheduled_date_gmt`),
  KEY `args` (`args`),
  KEY `group_id` (`group_id`),
  KEY `last_attempt_gmt` (`last_attempt_gmt`),
  KEY `claim_id_status_priority_scheduled_date_gmt` (`claim_id`,`status`,`priority`,`scheduled_date_gmt`),
  KEY `status_last_attempt_gmt` (`status`,`last_attempt_gmt`),
  KEY `status_claim_id` (`status`,`claim_id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_actions`
--

LOCK TABLES `wp_actionscheduler_actions` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_actions` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_actions` VALUES (173,'action_scheduler_run_recurring_actions_schedule_hook','complete','2026-08-28 12:12:30','2026-08-28 14:12:30',20,'[]','O:32:\"ActionScheduler_IntervalSchedule\":5:{s:22:\"\0*\0scheduled_timestamp\";i:1787919150;s:18:\"\0*\0first_timestamp\";i:1787919150;s:13:\"\0*\0recurrence\";i:86400;s:49:\"\0ActionScheduler_IntervalSchedule\0start_timestamp\";i:1787919150;s:53:\"\0ActionScheduler_IntervalSchedule\0interval_in_seconds\";i:86400;}',1,1,'2026-08-28 12:12:30','2026-08-28 14:12:30',1,NULL);
INSERT INTO `wp_actionscheduler_actions` VALUES (174,'action_scheduler/migration_hook','complete','2026-08-28 12:13:30','2026-08-28 14:13:30',10,'[]','O:30:\"ActionScheduler_SimpleSchedule\":2:{s:22:\"\0*\0scheduled_timestamp\";i:1787919210;s:41:\"\0ActionScheduler_SimpleSchedule\0timestamp\";i:1787919210;}',2,1,'2026-08-28 12:13:33','2026-08-28 14:13:33',3,NULL);
INSERT INTO `wp_actionscheduler_actions` VALUES (175,'action_scheduler_run_recurring_actions_schedule_hook','complete','2026-08-29 12:12:30','2026-08-29 14:12:30',20,'[]','O:32:\"ActionScheduler_IntervalSchedule\":5:{s:22:\"\0*\0scheduled_timestamp\";i:1788005550;s:18:\"\0*\0first_timestamp\";i:1787919150;s:13:\"\0*\0recurrence\";i:86400;s:49:\"\0ActionScheduler_IntervalSchedule\0start_timestamp\";i:1788005550;s:53:\"\0ActionScheduler_IntervalSchedule\0interval_in_seconds\";i:86400;}',1,1,'2026-08-31 08:19:40','2026-08-31 10:19:40',25,NULL);
INSERT INTO `wp_actionscheduler_actions` VALUES (176,'action_scheduler_run_recurring_actions_schedule_hook','complete','2026-09-01 08:19:40','2026-09-01 10:19:40',20,'[]','O:32:\"ActionScheduler_IntervalSchedule\":5:{s:22:\"\0*\0scheduled_timestamp\";i:1788250780;s:18:\"\0*\0first_timestamp\";i:1787919150;s:13:\"\0*\0recurrence\";i:86400;s:49:\"\0ActionScheduler_IntervalSchedule\0start_timestamp\";i:1788250780;s:53:\"\0ActionScheduler_IntervalSchedule\0interval_in_seconds\";i:86400;}',1,1,'2026-09-02 07:00:15','2026-09-02 09:00:15',53,NULL);
INSERT INTO `wp_actionscheduler_actions` VALUES (177,'action_scheduler_run_recurring_actions_schedule_hook','pending','2026-09-03 07:00:15','2026-09-03 09:00:15',20,'[]','O:32:\"ActionScheduler_IntervalSchedule\":5:{s:22:\"\0*\0scheduled_timestamp\";i:1788418815;s:18:\"\0*\0first_timestamp\";i:1787919150;s:13:\"\0*\0recurrence\";i:86400;s:49:\"\0ActionScheduler_IntervalSchedule\0start_timestamp\";i:1788418815;s:53:\"\0ActionScheduler_IntervalSchedule\0interval_in_seconds\";i:86400;}',1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,NULL);
/*!40000 ALTER TABLE `wp_actionscheduler_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_claims`
--

DROP TABLE IF EXISTS `wp_actionscheduler_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_claims` (
  `claim_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_created_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`claim_id`),
  KEY `date_created_gmt` (`date_created_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_claims`
--

LOCK TABLES `wp_actionscheduler_claims` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_actionscheduler_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_groups`
--

DROP TABLE IF EXISTS `wp_actionscheduler_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_groups` (
  `group_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `slug` (`slug`(191))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_groups`
--

LOCK TABLES `wp_actionscheduler_groups` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_groups` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_groups` VALUES (1,'ActionScheduler');
INSERT INTO `wp_actionscheduler_groups` VALUES (2,'action-scheduler-migration');
/*!40000 ALTER TABLE `wp_actionscheduler_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_actionscheduler_logs`
--

DROP TABLE IF EXISTS `wp_actionscheduler_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_actionscheduler_logs` (
  `log_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `log_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `log_date_local` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`log_id`),
  KEY `action_id` (`action_id`),
  KEY `log_date_gmt` (`log_date_gmt`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_actionscheduler_logs`
--

LOCK TABLES `wp_actionscheduler_logs` WRITE;
/*!40000 ALTER TABLE `wp_actionscheduler_logs` DISABLE KEYS */;
INSERT INTO `wp_actionscheduler_logs` VALUES (1,173,'action created','2026-08-28 12:12:30','2026-08-28 14:12:30');
INSERT INTO `wp_actionscheduler_logs` VALUES (2,174,'action created','2026-08-28 12:12:30','2026-08-28 14:12:30');
INSERT INTO `wp_actionscheduler_logs` VALUES (3,173,'action started via WP Cron','2026-08-28 12:12:30','2026-08-28 14:12:30');
INSERT INTO `wp_actionscheduler_logs` VALUES (4,173,'action complete via WP Cron','2026-08-28 12:12:30','2026-08-28 14:12:30');
INSERT INTO `wp_actionscheduler_logs` VALUES (5,175,'action created','2026-08-28 12:12:30','2026-08-28 14:12:30');
INSERT INTO `wp_actionscheduler_logs` VALUES (6,174,'action started via WP Cron','2026-08-28 12:13:33','2026-08-28 14:13:33');
INSERT INTO `wp_actionscheduler_logs` VALUES (7,174,'action complete via WP Cron','2026-08-28 12:13:33','2026-08-28 14:13:33');
INSERT INTO `wp_actionscheduler_logs` VALUES (8,175,'action started via Async Request','2026-08-31 08:19:40','2026-08-31 10:19:40');
INSERT INTO `wp_actionscheduler_logs` VALUES (9,175,'action complete via Async Request','2026-08-31 08:19:40','2026-08-31 10:19:40');
INSERT INTO `wp_actionscheduler_logs` VALUES (10,176,'action created','2026-08-31 08:19:40','2026-08-31 10:19:40');
INSERT INTO `wp_actionscheduler_logs` VALUES (11,176,'action started via WP Cron','2026-09-02 07:00:14','2026-09-02 09:00:14');
INSERT INTO `wp_actionscheduler_logs` VALUES (12,176,'action complete via WP Cron','2026-09-02 07:00:15','2026-09-02 09:00:15');
INSERT INTO `wp_actionscheduler_logs` VALUES (13,177,'action created','2026-09-02 07:00:15','2026-09-02 09:00:15');
/*!40000 ALTER TABLE `wp_actionscheduler_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_commentmeta`
--

DROP TABLE IF EXISTS `wp_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_commentmeta`
--

LOCK TABLES `wp_commentmeta` WRITE;
/*!40000 ALTER TABLE `wp_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_comments`
--

DROP TABLE IF EXISTS `wp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'comment',
  `comment_parent` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved` (`comment_approved`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10)),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_comments`
--

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_links`
--

DROP TABLE IF EXISTS `wp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_links` (
  `link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint unsigned NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_links`
--

LOCK TABLES `wp_links` WRITE;
/*!40000 ALTER TABLE `wp_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=620 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_options`
--

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` VALUES (1,'siteurl','http://sts-wp.local','yes');
INSERT INTO `wp_options` VALUES (2,'home','http://sts-wp.local','yes');
INSERT INTO `wp_options` VALUES (3,'blogname','Super Total Service','yes');
INSERT INTO `wp_options` VALUES (4,'blogdescription','Erhvervsservice, byggeri og rengoring','yes');
INSERT INTO `wp_options` VALUES (5,'users_can_register','0','yes');
INSERT INTO `wp_options` VALUES (6,'admin_email','admin@supertotalservice.dk','yes');
INSERT INTO `wp_options` VALUES (7,'start_of_week','1','yes');
INSERT INTO `wp_options` VALUES (8,'use_balanceTags','0','yes');
INSERT INTO `wp_options` VALUES (9,'use_smilies','1','yes');
INSERT INTO `wp_options` VALUES (10,'require_name_email','1','yes');
INSERT INTO `wp_options` VALUES (11,'comments_notify','1','yes');
INSERT INTO `wp_options` VALUES (12,'posts_per_rss','10','yes');
INSERT INTO `wp_options` VALUES (13,'rss_use_excerpt','0','yes');
INSERT INTO `wp_options` VALUES (14,'mailserver_url','mail.example.com','yes');
INSERT INTO `wp_options` VALUES (15,'mailserver_login','login@example.com','yes');
INSERT INTO `wp_options` VALUES (16,'mailserver_pass','password','yes');
INSERT INTO `wp_options` VALUES (17,'mailserver_port','110','yes');
INSERT INTO `wp_options` VALUES (18,'default_category','1','yes');
INSERT INTO `wp_options` VALUES (19,'default_comment_status','open','yes');
INSERT INTO `wp_options` VALUES (20,'default_ping_status','open','yes');
INSERT INTO `wp_options` VALUES (21,'default_pingback_flag','0','yes');
INSERT INTO `wp_options` VALUES (22,'posts_per_page','10','yes');
INSERT INTO `wp_options` VALUES (23,'date_format','F j, Y','yes');
INSERT INTO `wp_options` VALUES (24,'time_format','g:i a','yes');
INSERT INTO `wp_options` VALUES (25,'links_updated_date_format','F j, Y g:i a','yes');
INSERT INTO `wp_options` VALUES (26,'comment_moderation','0','yes');
INSERT INTO `wp_options` VALUES (27,'moderation_notify','1','yes');
INSERT INTO `wp_options` VALUES (28,'permalink_structure','/%postname%/','yes');
INSERT INTO `wp_options` VALUES (29,'rewrite_rules','a:94:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:12:\"sitemap\\.xml\";s:23:\"index.php?sitemap=index\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";s:27:\"[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"([^/]+)/embed/?$\";s:37:\"index.php?name=$matches[1]&embed=true\";s:20:\"([^/]+)/trackback/?$\";s:31:\"index.php?name=$matches[1]&tb=1\";s:40:\"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:35:\"([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:28:\"([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&paged=$matches[2]\";s:35:\"([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&cpage=$matches[2]\";s:24:\"([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?name=$matches[1]&page=$matches[2]\";s:16:\"[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:26:\"[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:46:\"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:22:\"[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";}','yes');
INSERT INTO `wp_options` VALUES (30,'hack_file','0','yes');
INSERT INTO `wp_options` VALUES (31,'blog_charset','UTF-8','yes');
INSERT INTO `wp_options` VALUES (32,'moderation_keys','','no');
INSERT INTO `wp_options` VALUES (33,'active_plugins','a:6:{i:0;s:35:\"google-site-kit/google-site-kit.php\";i:1;s:30:\"seo-by-rank-math/rank-math.php\";i:2;s:43:\"sts-content-manager/sts-content-manager.php\";i:3;s:37:\"sts-news-manager/sts-news-manager.php\";i:4;s:45:\"sts-projects-manager/sts-projects-manager.php\";i:5;s:31:\"wpconvert-cpt/wpconvert-cpt.php\";}','yes');
INSERT INTO `wp_options` VALUES (34,'category_base','','yes');
INSERT INTO `wp_options` VALUES (35,'ping_sites','https://rpc.pingomatic.com/','yes');
INSERT INTO `wp_options` VALUES (36,'comment_max_links','2','yes');
INSERT INTO `wp_options` VALUES (37,'gmt_offset','0','yes');
INSERT INTO `wp_options` VALUES (38,'default_email_category','1','yes');
INSERT INTO `wp_options` VALUES (39,'template','supertotalservice-dk-main','yes');
INSERT INTO `wp_options` VALUES (40,'stylesheet','supertotalservice-dk-main','yes');
INSERT INTO `wp_options` VALUES (41,'comment_registration','0','yes');
INSERT INTO `wp_options` VALUES (42,'html_type','text/html','yes');
INSERT INTO `wp_options` VALUES (43,'use_trackback','0','yes');
INSERT INTO `wp_options` VALUES (44,'default_role','subscriber','yes');
INSERT INTO `wp_options` VALUES (45,'db_version','61833','yes');
INSERT INTO `wp_options` VALUES (46,'uploads_use_yearmonth_folders','1','yes');
INSERT INTO `wp_options` VALUES (47,'upload_path','','yes');
INSERT INTO `wp_options` VALUES (48,'blog_public','1','yes');
INSERT INTO `wp_options` VALUES (49,'default_link_category','2','yes');
INSERT INTO `wp_options` VALUES (50,'show_on_front','posts','yes');
INSERT INTO `wp_options` VALUES (51,'tag_base','','yes');
INSERT INTO `wp_options` VALUES (52,'show_avatars','1','yes');
INSERT INTO `wp_options` VALUES (53,'avatar_rating','G','yes');
INSERT INTO `wp_options` VALUES (54,'upload_url_path','','yes');
INSERT INTO `wp_options` VALUES (55,'thumbnail_size_w','150','yes');
INSERT INTO `wp_options` VALUES (56,'thumbnail_size_h','150','yes');
INSERT INTO `wp_options` VALUES (57,'thumbnail_crop','1','yes');
INSERT INTO `wp_options` VALUES (58,'medium_size_w','300','yes');
INSERT INTO `wp_options` VALUES (59,'medium_size_h','300','yes');
INSERT INTO `wp_options` VALUES (60,'avatar_default','mystery','yes');
INSERT INTO `wp_options` VALUES (61,'large_size_w','1024','yes');
INSERT INTO `wp_options` VALUES (62,'large_size_h','1024','yes');
INSERT INTO `wp_options` VALUES (63,'image_default_link_type','none','yes');
INSERT INTO `wp_options` VALUES (64,'image_default_size','','yes');
INSERT INTO `wp_options` VALUES (65,'image_default_align','','yes');
INSERT INTO `wp_options` VALUES (66,'close_comments_for_old_posts','0','yes');
INSERT INTO `wp_options` VALUES (67,'close_comments_days_old','14','yes');
INSERT INTO `wp_options` VALUES (68,'thread_comments','1','yes');
INSERT INTO `wp_options` VALUES (69,'thread_comments_depth','5','yes');
INSERT INTO `wp_options` VALUES (70,'page_comments','0','yes');
INSERT INTO `wp_options` VALUES (71,'comments_per_page','50','yes');
INSERT INTO `wp_options` VALUES (72,'default_comments_page','newest','yes');
INSERT INTO `wp_options` VALUES (73,'comment_order','asc','yes');
INSERT INTO `wp_options` VALUES (74,'timezone_string','Europe/Copenhagen','yes');
INSERT INTO `wp_options` VALUES (75,'WPLANG','da_DK','yes');
INSERT INTO `wp_options` VALUES (76,'widget_categories','a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (77,'widget_text','a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (78,'widget_rss','a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (79,'sidebars_widgets','a:2:{s:19:\"wp_inactive_widgets\";a:0:{}s:13:\"array_version\";i:3;}','yes');
INSERT INTO `wp_options` VALUES (80,'current_theme','supertotalservice.dk-main (WPConvert)','yes');
INSERT INTO `wp_options` VALUES (81,'theme_mods_supertotalservice-dk-main','a:2:{s:18:\"nav_menu_locations\";a:2:{s:7:\"primary\";i:2;s:6:\"footer\";i:3;}s:18:\"custom_css_post_id\";i:-1;}','yes');
INSERT INTO `wp_options` VALUES (82,'wp_user_roles','a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:92:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:22:\"manage_privacy_options\";b:1;s:14:\"resume_plugins\";b:1;s:13:\"resume_themes\";b:1;s:8:\"edit_css\";b:1;s:9:\"customize\";b:1;s:11:\"delete_site\";b:1;s:15:\"edit_categories\";b:1;s:17:\"delete_categories\";b:1;s:16:\"manage_post_tags\";b:1;s:14:\"edit_post_tags\";b:1;s:16:\"delete_post_tags\";b:1;s:17:\"install_languages\";b:1;s:16:\"update_languages\";b:1;s:22:\"wpconvert_edit_content\";b:1;s:23:\"rank_math_edit_htaccess\";b:1;s:16:\"rank_math_titles\";b:1;s:17:\"rank_math_general\";b:1;s:17:\"rank_math_sitemap\";b:1;s:21:\"rank_math_404_monitor\";b:1;s:22:\"rank_math_link_builder\";b:1;s:22:\"rank_math_redirections\";b:1;s:22:\"rank_math_role_manager\";b:1;s:19:\"rank_math_analytics\";b:1;s:23:\"rank_math_site_analysis\";b:1;s:25:\"rank_math_onpage_analysis\";b:1;s:24:\"rank_math_onpage_general\";b:1;s:25:\"rank_math_onpage_advanced\";b:1;s:24:\"rank_math_onpage_snippet\";b:1;s:23:\"rank_math_onpage_social\";b:1;s:20:\"rank_math_content_ai\";b:1;s:19:\"rank_math_admin_bar\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:45:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:16:\"delete_post_tags\";b:1;s:16:\"manage_post_tags\";b:1;s:14:\"edit_post_tags\";b:1;s:15:\"edit_categories\";b:1;s:17:\"delete_categories\";b:1;s:22:\"wpconvert_edit_content\";b:1;s:23:\"rank_math_site_analysis\";b:1;s:25:\"rank_math_onpage_analysis\";b:1;s:24:\"rank_math_onpage_general\";b:1;s:24:\"rank_math_onpage_snippet\";b:1;s:23:\"rank_math_onpage_social\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:14:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:25:\"rank_math_onpage_analysis\";b:1;s:24:\"rank_math_onpage_general\";b:1;s:24:\"rank_math_onpage_snippet\";b:1;s:23:\"rank_math_onpage_social\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}','on');
INSERT INTO `wp_options` VALUES (83,'fresh_site','0','yes');
INSERT INTO `wp_options` VALUES (84,'medium_large_size_w','768','yes');
INSERT INTO `wp_options` VALUES (85,'medium_large_size_h','0','yes');
INSERT INTO `wp_options` VALUES (86,'wp_page_for_privacy_policy','0','yes');
INSERT INTO `wp_options` VALUES (87,'initial_db_version','58975','yes');
INSERT INTO `wp_options` VALUES (88,'finished_splitting_shared_terms','1','yes');
INSERT INTO `wp_options` VALUES (89,'site_icon','0','yes');
INSERT INTO `wp_options` VALUES (90,'nav_menu_options','a:1:{s:5:\"_data\";a:0:{}}','yes');
INSERT INTO `wp_options` VALUES (91,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (92,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (93,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (94,'widget_links','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (95,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (96,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (97,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (98,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (99,'widget_meta','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (100,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (101,'widget_recent-posts','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (102,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (103,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (104,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (105,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (106,'widget_block','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (107,'cron','a:15:{i:1788347010;a:1:{s:26:\"action_scheduler_run_queue\";a:1:{s:32:\"0d04ed39571b55704c122d726248bbac\";a:3:{s:8:\"schedule\";s:12:\"every_minute\";s:4:\"args\";a:1:{i:0;s:7:\"WP Cron\";}s:8:\"interval\";i:60;}}}i:1788348309;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1788349180;a:1:{s:41:\"googlesitekit_cron_update_remote_features\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1788351057;a:1:{s:31:\"wpseo_permalink_structure_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788359109;a:4:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1788375567;a:1:{s:21:\"wp_update_user_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1788386545;a:1:{s:35:\"rank_math/content-ai/update_prompts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788393600;a:2:{s:35:\"rank_math/redirection/clean_trashed\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:30:\"rank_math/links/internal_links\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788418767;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788418786;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788418890;a:1:{s:30:\"wp_delete_temp_updater_backups\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1788418895;a:1:{s:41:\"wp_privacy_personal_data_cleanup_requests\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788430896;a:1:{s:13:\"wpseo-reindex\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1788445509;a:1:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}','on');
INSERT INTO `wp_options` VALUES (110,'wpconvert_slugfix_step1_hash','cc763e00e5b44e7ca886d2174fbb768f','auto');
INSERT INTO `wp_options` VALUES (112,'wpconvert_tplrepair_hash','cc763e00e5b44e7ca886d2174fbb768f','auto');
INSERT INTO `wp_options` VALUES (115,'wpconvert_editor_allowed_roles','a:2:{i:0;s:13:\"administrator\";i:1;s:6:\"editor\";}','off');
INSERT INTO `wp_options` VALUES (116,'wpconvert_editor_caps_version','1','off');
INSERT INTO `wp_options` VALUES (120,'recently_edited','a:2:{i:0;s:123:\"C:\\Users\\Ayoub\\Local Sites\\sts-wp\\app\\public/wp-content/plugins/frontedit-live-block-editor/frontedit-live-block-editor.php\";i:1;s:0:\"\";}','off');
INSERT INTO `wp_options` VALUES (121,'sticky_posts','a:0:{}','on');
INSERT INTO `wp_options` VALUES (122,'uninstall_plugins','a:0:{}','off');
INSERT INTO `wp_options` VALUES (123,'page_for_posts','0','on');
INSERT INTO `wp_options` VALUES (124,'page_on_front','0','on');
INSERT INTO `wp_options` VALUES (125,'default_post_format','0','on');
INSERT INTO `wp_options` VALUES (126,'link_manager_enabled','0','on');
INSERT INTO `wp_options` VALUES (127,'show_comments_cookies_opt_in','1','on');
INSERT INTO `wp_options` VALUES (128,'admin_email_lifespan','1802701553','on');
INSERT INTO `wp_options` VALUES (129,'disallowed_keys','','off');
INSERT INTO `wp_options` VALUES (130,'comment_previously_approved','1','on');
INSERT INTO `wp_options` VALUES (131,'auto_plugin_theme_update_emails','a:0:{}','off');
INSERT INTO `wp_options` VALUES (132,'auto_update_core_dev','enabled','on');
INSERT INTO `wp_options` VALUES (133,'auto_update_core_minor','enabled','on');
INSERT INTO `wp_options` VALUES (134,'auto_update_core_major','enabled','on');
INSERT INTO `wp_options` VALUES (135,'wp_force_deactivated_plugins','a:0:{}','on');
INSERT INTO `wp_options` VALUES (136,'wp_attachment_pages_enabled','0','on');
INSERT INTO `wp_options` VALUES (137,'wp_notes_notify','1','on');
INSERT INTO `wp_options` VALUES (138,'db_upgraded','','on');
INSERT INTO `wp_options` VALUES (141,'recovery_keys','a:0:{}','off');
INSERT INTO `wp_options` VALUES (146,'wpconvert_routes_hash','cc763e00e5b44e7ca886d2174fbb768f','auto');
INSERT INTO `wp_options` VALUES (147,'wpconvert_default_content_cleaned','1','auto');
INSERT INTO `wp_options` VALUES (148,'wpconvert_menu_dedupe_done','1','auto');
INSERT INTO `wp_options` VALUES (150,'wpconvert_menu_created','1','auto');
INSERT INTO `wp_options` VALUES (151,'wpconvert_menus_hash','ae11a8b7049240d3f2f7dbbd1d8d162a','auto');
INSERT INTO `wp_options` VALUES (167,'auto_core_update_notified','a:4:{s:4:\"type\";s:7:\"success\";s:5:\"email\";s:26:\"admin@supertotalservice.dk\";s:7:\"version\";s:3:\"7.1\";s:9:\"timestamp\";i:1787209295;}','off');
INSERT INTO `wp_options` VALUES (168,'wpconvert_blogstub_hash','cc763e00e5b44e7ca886d2174fbb768f','auto');
INSERT INTO `wp_options` VALUES (169,'wpconvert_slugfix_step2_hash','cc763e00e5b44e7ca886d2174fbb768f','auto');
INSERT INTO `wp_options` VALUES (174,'finished_updating_comment_type','1','auto');
INSERT INTO `wp_options` VALUES (175,'_transient_wp_styles_for_blocks','a:2:{s:4:\"hash\";s:32:\"dce45f35a661f6dbc871de68563f9c2e\";s:6:\"blocks\";a:9:{s:32:\"832dc2d864d79097d8b8b493ad93453b\";s:0:\"\";s:32:\"45d3e0c4afcbd8cf25cb1ba51abfb3d7\";s:46:\":root :where(.wp-block-icon svg){width: 24px;}\";s:32:\"feca6e996f694be2d29599793228e0d7\";s:0:\"\";s:32:\"5eef131663eddaf830554df656fc2968\";s:324:\":where(.wp-block-gallery.is-layout-flex){gap: var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) );}:where(.wp-block-gallery.is-layout-grid){gap: var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) );}\";s:32:\"c99c05932c6685777ec5b856698fcc7d\";s:118:\":where(.wp-block-latest-posts.is-layout-flex){gap: 1.25em;}:where(.wp-block-latest-posts.is-layout-grid){gap: 1.25em;}\";s:32:\"dec8d648f30b13caec8e61374591787d\";s:120:\":where(.wp-block-post-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-post-template.is-layout-grid){gap: 1.25em;}\";s:32:\"6c35533f7a92cce94808323603db9fc8\";s:120:\":where(.wp-block-term-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-term-template.is-layout-grid){gap: 1.25em;}\";s:32:\"6a0505cd5c78a87ed77570cda43c1132\";s:102:\":where(.wp-block-columns.is-layout-flex){gap: 2em;}:where(.wp-block-columns.is-layout-grid){gap: 2em;}\";s:32:\"25a66f156386551185570f72a9f7d44e\";s:69:\":root :where(.wp-block-pullquote){font-size: 1.5em;line-height: 1.6;}\";}}','on');
INSERT INTO `wp_options` VALUES (179,'wpconvert_rewrite_check','a:4:{s:7:\"verdict\";s:2:\"ok\";s:3:\"sig\";s:32:\"19e483845b6508509513500de5314710\";s:4:\"time\";i:1787209307;s:6:\"healed\";i:0;}','auto');
INSERT INTO `wp_options` VALUES (180,'wpconvert_media_imported','1','auto');
INSERT INTO `wp_options` VALUES (191,'can_compress_scripts','0','on');
INSERT INTO `wp_options` VALUES (206,'_site_transient_wp_plugin_dependencies_plugin_data','a:0:{}','off');
INSERT INTO `wp_options` VALUES (207,'recently_activated','a:1:{s:24:\"wordpress-seo/wp-seo.php\";i:1787919052;}','off');
INSERT INTO `wp_options` VALUES (208,'wp_calendar_block_has_published_posts','1','auto');
INSERT INTO `wp_options` VALUES (219,'sts_news_seed_version','1.0.1','auto');
INSERT INTO `wp_options` VALUES (230,'category_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (231,'wpconvert_editor_notice_dismissed','1','off');
INSERT INTO `wp_options` VALUES (239,'sts_content_migrated_v2','1','off');
INSERT INTO `wp_options` VALUES (240,'sts_content_pages_linked','2.1.1','auto');
INSERT INTO `wp_options` VALUES (254,'wpconvert_cpts_installed_at','1787232848','off');
INSERT INTO `wp_options` VALUES (255,'wpconvert_cpts_wizard_dismissed','0','off');
INSERT INTO `wp_options` VALUES (256,'wpconvert_cpts_purge_on_uninstall','0','off');
INSERT INTO `wp_options` VALUES (257,'wpconvert_cpts_needs_flush','1','off');
INSERT INTO `wp_options` VALUES (258,'wpconvert_cpts','a:0:{}','off');
INSERT INTO `wp_options` VALUES (275,'_transient_health-check-site-status-result','{\"good\":19,\"recommended\":4,\"critical\":1}','on');
INSERT INTO `wp_options` VALUES (318,'wpconvert_edits','a:1:{s:14:\"wpc_66bf3c95ea\";a:12:{s:4:\"type\";s:4:\"text\";s:5:\"value\";s:324:\"\r\n        <div class=\"metric\"><strong>24/7</strong><span>Beredskab</span></div>\r\n        <div class=\"metric\"><strong>100%</strong><span>Fleksibel</span></div>\r\n        <div class=\"metric\"><strong>+20 års</strong><span>Erfaring</span></div>\r\n        <div class=\"metric\"><strong>2 t</strong><span>Svartid</span></div>\r\n      \";s:4:\"href\";s:0:\"\";s:3:\"alt\";s:0:\"\";s:6:\"target\";s:0:\"\";s:6:\"hidden\";b:0;s:6:\"bgType\";s:0:\"\";s:9:\"videoType\";s:0:\"\";s:12:\"thumbnailUrl\";s:0:\"\";s:7:\"bgColor\";s:0:\"\";s:9:\"textColor\";s:0:\"\";s:10:\"updated_at\";s:25:\"2026-08-25T08:37:34+00:00\";}}','off');
INSERT INTO `wp_options` VALUES (355,'googlesitekit_db_version','1.185.0','auto');
INSERT INTO `wp_options` VALUES (356,'googlesitekit_has_connected_admins','0','auto');
INSERT INTO `wp_options` VALUES (360,'auto_update_plugins','a:2:{i:0;s:35:\"google-site-kit/google-site-kit.php\";i:1;s:30:\"seo-by-rank-math/rank-math.php\";}','off');
INSERT INTO `wp_options` VALUES (363,'_transient_googlesitekit_verification_meta_tags','a:0:{}','on');
INSERT INTO `wp_options` VALUES (406,'yoast_migrations_free','a:1:{s:7:\"version\";s:4:\"28.3\";}','auto');
INSERT INTO `wp_options` VALUES (407,'wpseo','a:124:{s:8:\"tracking\";b:0;s:16:\"toggled_tracking\";b:0;s:22:\"license_server_version\";b:0;s:15:\"ms_defaults_set\";b:0;s:40:\"ignore_search_engines_discouraged_notice\";b:0;s:19:\"indexing_first_time\";b:1;s:16:\"indexing_started\";b:0;s:15:\"indexing_reason\";s:21:\"post_type_made_public\";s:29:\"indexables_indexing_completed\";b:0;s:13:\"index_now_key\";s:0:\"\";s:7:\"version\";s:4:\"28.3\";s:16:\"previous_version\";s:0:\"\";s:20:\"disableadvanced_meta\";b:1;s:30:\"enable_headless_rest_endpoints\";b:1;s:17:\"ryte_indexability\";b:0;s:11:\"baiduverify\";s:0:\"\";s:12:\"googleverify\";s:0:\"\";s:8:\"msverify\";s:0:\"\";s:12:\"yandexverify\";s:0:\"\";s:12:\"ahrefsverify\";s:0:\"\";s:9:\"site_type\";s:0:\"\";s:20:\"has_multiple_authors\";s:0:\"\";s:16:\"environment_type\";s:0:\"\";s:23:\"content_analysis_active\";b:1;s:23:\"keyword_analysis_active\";b:1;s:34:\"inclusive_language_analysis_active\";b:0;s:21:\"enable_admin_bar_menu\";b:1;s:26:\"enable_cornerstone_content\";b:1;s:18:\"enable_xml_sitemap\";b:1;s:24:\"enable_text_link_counter\";b:1;s:16:\"enable_index_now\";b:1;s:19:\"enable_ai_generator\";b:1;s:22:\"ai_enabled_pre_default\";b:0;s:22:\"show_onboarding_notice\";b:1;s:18:\"first_activated_on\";i:1787739697;s:13:\"myyoast-oauth\";b:0;s:26:\"semrush_integration_active\";b:1;s:14:\"semrush_tokens\";a:0:{}s:20:\"semrush_country_code\";s:2:\"us\";s:19:\"permalink_structure\";s:12:\"/%postname%/\";s:8:\"home_url\";s:19:\"http://sts-wp.local\";s:18:\"dynamic_permalinks\";b:0;s:17:\"category_base_url\";s:0:\"\";s:12:\"tag_base_url\";s:0:\"\";s:21:\"custom_taxonomy_slugs\";a:0:{}s:29:\"enable_enhanced_slack_sharing\";b:1;s:23:\"enable_metabox_insights\";b:1;s:23:\"enable_link_suggestions\";b:1;s:26:\"algolia_integration_active\";b:0;s:14:\"import_cursors\";a:0:{}s:13:\"workouts_data\";a:1:{s:13:\"configuration\";a:1:{s:13:\"finishedSteps\";a:0:{}}}s:28:\"configuration_finished_steps\";a:0:{}s:36:\"dismiss_configuration_workout_notice\";b:0;s:34:\"dismiss_premium_deactivated_notice\";b:0;s:19:\"importing_completed\";a:0:{}s:26:\"wincher_integration_active\";b:1;s:14:\"wincher_tokens\";a:0:{}s:36:\"wincher_automatically_add_keyphrases\";b:0;s:18:\"wincher_website_id\";s:0:\"\";s:18:\"first_time_install\";b:1;s:34:\"should_redirect_after_install_free\";b:0;s:34:\"activation_redirect_timestamp_free\";i:1787739697;s:18:\"remove_feed_global\";b:0;s:27:\"remove_feed_global_comments\";b:0;s:25:\"remove_feed_post_comments\";b:0;s:19:\"remove_feed_authors\";b:0;s:22:\"remove_feed_categories\";b:0;s:16:\"remove_feed_tags\";b:0;s:29:\"remove_feed_custom_taxonomies\";b:0;s:22:\"remove_feed_post_types\";b:0;s:18:\"remove_feed_search\";b:0;s:21:\"remove_atom_rdf_feeds\";b:0;s:17:\"remove_shortlinks\";b:0;s:21:\"remove_rest_api_links\";b:0;s:20:\"remove_rsd_wlw_links\";b:0;s:19:\"remove_oembed_links\";b:0;s:16:\"remove_generator\";b:0;s:20:\"remove_emoji_scripts\";b:0;s:24:\"remove_powered_by_header\";b:0;s:22:\"remove_pingback_header\";b:0;s:28:\"clean_campaign_tracking_urls\";b:0;s:16:\"clean_permalinks\";b:0;s:32:\"clean_permalinks_extra_variables\";s:0:\"\";s:14:\"search_cleanup\";b:0;s:20:\"search_cleanup_emoji\";b:0;s:23:\"search_cleanup_patterns\";b:0;s:22:\"search_character_limit\";i:50;s:20:\"deny_search_crawling\";b:0;s:21:\"deny_wp_json_crawling\";b:0;s:20:\"deny_adsbot_crawling\";b:0;s:19:\"deny_ccbot_crawling\";b:0;s:29:\"deny_google_extended_crawling\";b:0;s:20:\"deny_gptbot_crawling\";b:0;s:27:\"redirect_search_pretty_urls\";b:0;s:29:\"least_readability_ignore_list\";a:0:{}s:27:\"least_seo_score_ignore_list\";a:0:{}s:23:\"most_linked_ignore_list\";a:0:{}s:24:\"least_linked_ignore_list\";a:0:{}s:28:\"indexables_page_reading_list\";a:5:{i:0;b:0;i:1;b:0;i:2;b:0;i:3;b:0;i:4;b:0;}s:25:\"indexables_overview_state\";s:21:\"dashboard-not-visited\";s:28:\"last_known_public_post_types\";a:3:{i:0;s:4:\"post\";i:1;s:4:\"page\";i:2;s:11:\"sts_project\";}s:28:\"last_known_public_taxonomies\";a:3:{i:0;s:8:\"category\";i:1;s:8:\"post_tag\";i:2;s:11:\"post_format\";}s:23:\"last_known_no_unindexed\";a:1:{s:40:\"wpseo_total_unindexed_post_type_archives\";i:1787916362;}s:14:\"new_post_types\";a:1:{i:2;s:11:\"sts_project\";}s:14:\"new_taxonomies\";a:0:{}s:34:\"show_new_content_type_notification\";b:1;s:44:\"site_kit_configuration_permanently_dismissed\";b:0;s:18:\"site_kit_connected\";b:0;s:37:\"site_kit_tracking_setup_widget_loaded\";s:3:\"yes\";s:41:\"site_kit_tracking_first_interaction_stage\";s:5:\"setup\";s:40:\"site_kit_tracking_last_interaction_stage\";s:5:\"setup\";s:52:\"site_kit_tracking_setup_widget_temporarily_dismissed\";s:2:\"no\";s:52:\"site_kit_tracking_setup_widget_permanently_dismissed\";s:2:\"no\";s:31:\"google_site_kit_feature_enabled\";b:0;s:25:\"ai_free_sparks_started_on\";N;s:15:\"enable_llms_txt\";b:0;s:15:\"last_updated_on\";b:0;s:17:\"default_seo_title\";a:0:{}s:21:\"default_seo_meta_desc\";a:0:{}s:18:\"first_activated_by\";i:1;s:34:\"enable_schema_aggregation_endpoint\";b:0;s:38:\"schema_aggregation_endpoint_enabled_on\";N;s:16:\"enable_task_list\";b:1;s:13:\"enable_schema\";b:1;}','auto');
INSERT INTO `wp_options` VALUES (408,'wpseo_titles','a:129:{s:17:\"forcerewritetitle\";b:0;s:9:\"separator\";s:7:\"sc-dash\";s:16:\"title-home-wpseo\";s:42:\"%%sitename%% %%page%% %%sep%% %%sitedesc%%\";s:18:\"title-author-wpseo\";s:41:\"%%name%%, Author at %%sitename%% %%page%%\";s:19:\"title-archive-wpseo\";s:38:\"%%date%% %%page%% %%sep%% %%sitename%%\";s:18:\"title-search-wpseo\";s:63:\"You searched for %%searchphrase%% %%page%% %%sep%% %%sitename%%\";s:15:\"title-404-wpseo\";s:35:\"Page not found %%sep%% %%sitename%%\";s:25:\"social-title-author-wpseo\";s:8:\"%%name%%\";s:26:\"social-title-archive-wpseo\";s:8:\"%%date%%\";s:31:\"social-description-author-wpseo\";s:0:\"\";s:32:\"social-description-archive-wpseo\";s:0:\"\";s:29:\"social-image-url-author-wpseo\";s:0:\"\";s:30:\"social-image-url-archive-wpseo\";s:0:\"\";s:28:\"social-image-id-author-wpseo\";i:0;s:29:\"social-image-id-archive-wpseo\";i:0;s:19:\"metadesc-home-wpseo\";s:0:\"\";s:21:\"metadesc-author-wpseo\";s:0:\"\";s:22:\"metadesc-archive-wpseo\";s:0:\"\";s:9:\"rssbefore\";s:0:\"\";s:8:\"rssafter\";s:53:\"The post %%POSTLINK%% appeared first on %%BLOGLINK%%.\";s:20:\"noindex-author-wpseo\";b:0;s:28:\"noindex-author-noposts-wpseo\";b:1;s:21:\"noindex-archive-wpseo\";b:1;s:14:\"disable-author\";b:0;s:12:\"disable-date\";b:0;s:19:\"disable-post_format\";b:0;s:18:\"disable-attachment\";b:1;s:20:\"breadcrumbs-404crumb\";s:25:\"Error 404: Page not found\";s:29:\"breadcrumbs-display-blog-page\";b:1;s:20:\"breadcrumbs-boldlast\";b:0;s:25:\"breadcrumbs-archiveprefix\";s:12:\"Archives for\";s:18:\"breadcrumbs-enable\";b:1;s:16:\"breadcrumbs-home\";s:4:\"Home\";s:18:\"breadcrumbs-prefix\";s:0:\"\";s:24:\"breadcrumbs-searchprefix\";s:16:\"You searched for\";s:15:\"breadcrumbs-sep\";s:2:\"»\";s:12:\"website_name\";s:0:\"\";s:11:\"person_name\";s:0:\"\";s:11:\"person_logo\";s:0:\"\";s:22:\"alternate_website_name\";s:0:\"\";s:12:\"company_logo\";s:0:\"\";s:12:\"company_name\";s:0:\"\";s:22:\"company_alternate_name\";s:0:\"\";s:17:\"company_or_person\";s:7:\"company\";s:25:\"company_or_person_user_id\";b:0;s:17:\"stripcategorybase\";b:0;s:26:\"open_graph_frontpage_title\";s:12:\"%%sitename%%\";s:25:\"open_graph_frontpage_desc\";s:0:\"\";s:26:\"open_graph_frontpage_image\";s:0:\"\";s:24:\"publishing_principles_id\";i:0;s:25:\"ownership_funding_info_id\";i:0;s:29:\"actionable_feedback_policy_id\";i:0;s:21:\"corrections_policy_id\";i:0;s:16:\"ethics_policy_id\";i:0;s:19:\"diversity_policy_id\";i:0;s:28:\"diversity_staffing_report_id\";i:0;s:15:\"org-description\";s:0:\"\";s:9:\"org-email\";s:0:\"\";s:9:\"org-phone\";s:0:\"\";s:14:\"org-legal-name\";s:0:\"\";s:17:\"org-founding-date\";s:0:\"\";s:20:\"org-number-employees\";s:0:\"\";s:10:\"org-vat-id\";s:0:\"\";s:10:\"org-tax-id\";s:0:\"\";s:7:\"org-iso\";s:0:\"\";s:8:\"org-duns\";s:0:\"\";s:11:\"org-leicode\";s:0:\"\";s:9:\"org-naics\";s:0:\"\";s:10:\"title-post\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:13:\"metadesc-post\";s:0:\"\";s:12:\"noindex-post\";b:0;s:23:\"display-metabox-pt-post\";b:1;s:23:\"post_types-post-maintax\";i:0;s:21:\"schema-page-type-post\";s:7:\"WebPage\";s:24:\"schema-article-type-post\";s:7:\"Article\";s:17:\"social-title-post\";s:9:\"%%title%%\";s:23:\"social-description-post\";s:0:\"\";s:21:\"social-image-url-post\";s:0:\"\";s:20:\"social-image-id-post\";i:0;s:10:\"title-page\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:13:\"metadesc-page\";s:0:\"\";s:12:\"noindex-page\";b:0;s:23:\"display-metabox-pt-page\";b:1;s:23:\"post_types-page-maintax\";i:0;s:21:\"schema-page-type-page\";s:7:\"WebPage\";s:24:\"schema-article-type-page\";s:4:\"None\";s:17:\"social-title-page\";s:9:\"%%title%%\";s:23:\"social-description-page\";s:0:\"\";s:21:\"social-image-url-page\";s:0:\"\";s:20:\"social-image-id-page\";i:0;s:16:\"title-attachment\";s:39:\"%%title%% %%page%% %%sep%% %%sitename%%\";s:19:\"metadesc-attachment\";s:0:\"\";s:18:\"noindex-attachment\";b:0;s:29:\"display-metabox-pt-attachment\";b:1;s:29:\"post_types-attachment-maintax\";i:0;s:27:\"schema-page-type-attachment\";s:7:\"WebPage\";s:30:\"schema-article-type-attachment\";s:4:\"None\";s:18:\"title-tax-category\";s:53:\"%%term_title%% Archives %%page%% %%sep%% %%sitename%%\";s:21:\"metadesc-tax-category\";s:0:\"\";s:28:\"display-metabox-tax-category\";b:1;s:20:\"noindex-tax-category\";b:0;s:25:\"social-title-tax-category\";s:23:\"%%term_title%% Archives\";s:31:\"social-description-tax-category\";s:0:\"\";s:29:\"social-image-url-tax-category\";s:0:\"\";s:28:\"social-image-id-tax-category\";i:0;s:26:\"taxonomy-category-ptparent\";i:0;s:18:\"title-tax-post_tag\";s:53:\"%%term_title%% Archives %%page%% %%sep%% %%sitename%%\";s:21:\"metadesc-tax-post_tag\";s:0:\"\";s:28:\"display-metabox-tax-post_tag\";b:1;s:20:\"noindex-tax-post_tag\";b:0;s:25:\"social-title-tax-post_tag\";s:23:\"%%term_title%% Archives\";s:31:\"social-description-tax-post_tag\";s:0:\"\";s:29:\"social-image-url-tax-post_tag\";s:0:\"\";s:28:\"social-image-id-tax-post_tag\";i:0;s:26:\"taxonomy-post_tag-ptparent\";i:0;s:21:\"title-tax-post_format\";s:53:\"%%term_title%% Archives %%page%% %%sep%% %%sitename%%\";s:24:\"metadesc-tax-post_format\";s:0:\"\";s:31:\"display-metabox-tax-post_format\";b:1;s:23:\"noindex-tax-post_format\";b:1;s:28:\"social-title-tax-post_format\";s:23:\"%%term_title%% Archives\";s:34:\"social-description-tax-post_format\";s:0:\"\";s:32:\"social-image-url-tax-post_format\";s:0:\"\";s:31:\"social-image-id-tax-post_format\";i:0;s:29:\"taxonomy-post_format-ptparent\";i:0;s:14:\"person_logo_id\";i:0;s:15:\"company_logo_id\";i:0;s:17:\"company_logo_meta\";b:0;s:16:\"person_logo_meta\";b:0;s:29:\"open_graph_frontpage_image_id\";i:0;}','auto');
INSERT INTO `wp_options` VALUES (409,'wpseo_social','a:20:{s:13:\"facebook_site\";s:0:\"\";s:13:\"instagram_url\";s:0:\"\";s:12:\"linkedin_url\";s:0:\"\";s:11:\"myspace_url\";s:0:\"\";s:16:\"og_default_image\";s:0:\"\";s:19:\"og_default_image_id\";s:0:\"\";s:18:\"og_frontpage_title\";s:0:\"\";s:17:\"og_frontpage_desc\";s:0:\"\";s:18:\"og_frontpage_image\";s:0:\"\";s:21:\"og_frontpage_image_id\";s:0:\"\";s:9:\"opengraph\";b:1;s:13:\"pinterest_url\";s:0:\"\";s:15:\"pinterestverify\";s:0:\"\";s:7:\"twitter\";b:1;s:12:\"twitter_site\";s:0:\"\";s:17:\"twitter_card_type\";s:19:\"summary_large_image\";s:11:\"youtube_url\";s:0:\"\";s:13:\"wikipedia_url\";s:0:\"\";s:17:\"other_social_urls\";a:0:{}s:12:\"mastodon_url\";s:0:\"\";}','auto');
INSERT INTO `wp_options` VALUES (410,'wpseo_llmstxt','a:7:{s:23:\"llms_txt_selection_mode\";s:4:\"auto\";s:13:\"about_us_page\";i:0;s:12:\"contact_page\";i:0;s:10:\"terms_page\";i:0;s:19:\"privacy_policy_page\";i:0;s:9:\"shop_page\";i:0;s:20:\"other_included_pages\";a:0:{}}','auto');
INSERT INTO `wp_options` VALUES (411,'wpseo_tracking_only','a:3:{s:25:\"task_list_first_opened_on\";s:0:\"\";s:22:\"task_first_actioned_on\";s:0:\"\";s:36:\"frontend_inspector_first_actioned_on\";s:0:\"\";}','auto');
INSERT INTO `wp_options` VALUES (440,'_site_transient_timeout_php_check_986ab27a5c44eb5941b7e3b238532f66','1788507110','off');
INSERT INTO `wp_options` VALUES (441,'_site_transient_php_check_986ab27a5c44eb5941b7e3b238532f66','a:5:{s:19:\"recommended_version\";s:3:\"8.3\";s:15:\"minimum_version\";s:3:\"7.4\";s:12:\"is_supported\";b:0;s:9:\"is_secure\";b:1;s:13:\"is_acceptable\";b:1;}','off');
INSERT INTO `wp_options` VALUES (444,'sts_projects_page_id','156','auto');
INSERT INTO `wp_options` VALUES (445,'sts_projects_installed','1.0.0','auto');
INSERT INTO `wp_options` VALUES (452,'_site_transient_timeout_browser_98289dd1c8427f7ac9bc8f4d0003f2e0','1788519605','off');
INSERT INTO `wp_options` VALUES (453,'_site_transient_browser_98289dd1c8427f7ac9bc8f4d0003f2e0','a:10:{s:4:\"name\";s:6:\"Chrome\";s:7:\"version\";s:9:\"151.0.0.0\";s:8:\"platform\";s:7:\"Windows\";s:10:\"update_url\";s:29:\"https://www.google.com/chrome\";s:7:\"img_src\";s:43:\"http://s.w.org/images/browsers/chrome.png?1\";s:11:\"img_src_ssl\";s:44:\"https://s.w.org/images/browsers/chrome.png?1\";s:15:\"current_version\";s:2:\"18\";s:7:\"upgrade\";b:0;s:8:\"insecure\";b:0;s:6:\"mobile\";b:0;}','off');
INSERT INTO `wp_options` VALUES (492,'wpseo_llms_txt_content_hash','','auto');
INSERT INTO `wp_options` VALUES (498,'action_scheduler_hybrid_store_demarkation','172','auto');
INSERT INTO `wp_options` VALUES (499,'schema-ActionScheduler_StoreSchema','8.0.1787919150','auto');
INSERT INTO `wp_options` VALUES (500,'schema-ActionScheduler_LoggerSchema','3.0.1787919150','auto');
INSERT INTO `wp_options` VALUES (501,'rank_math_known_post_types','a:4:{s:4:\"post\";s:4:\"post\";s:4:\"page\";s:4:\"page\";s:10:\"attachment\";s:10:\"attachment\";s:11:\"sts_project\";s:11:\"sts_project\";}','auto');
INSERT INTO `wp_options` VALUES (502,'rank_math_modules','a:14:{i:0;s:12:\"link-counter\";i:1;s:9:\"analytics\";i:2;s:12:\"seo-analysis\";i:3;s:7:\"sitemap\";i:4;s:12:\"rich-snippet\";i:5;s:11:\"woocommerce\";i:6;s:10:\"buddypress\";i:7;s:7:\"bbpress\";i:8;s:3:\"acf\";i:9;s:11:\"web-stories\";i:10;s:10:\"content-ai\";i:11;s:16:\"instant-indexing\";i:12;s:13:\"ai-visibility\";i:13;s:9:\"local-seo\";}','auto');
INSERT INTO `wp_options` VALUES (503,'rank-math-options-general','a:47:{s:19:\"strip_category_base\";s:3:\"off\";s:24:\"attachment_redirect_urls\";s:2:\"on\";s:27:\"attachment_redirect_default\";s:19:\"http://sts-wp.local\";s:23:\"nofollow_external_links\";s:3:\"off\";s:20:\"nofollow_image_links\";s:3:\"off\";s:25:\"new_window_external_links\";s:2:\"on\";s:11:\"add_img_alt\";s:3:\"off\";s:14:\"img_alt_format\";s:11:\" %filename%\";s:13:\"add_img_title\";s:3:\"off\";s:16:\"img_title_format\";s:22:\"%title% %count(title)%\";s:11:\"breadcrumbs\";s:3:\"off\";s:21:\"breadcrumbs_separator\";s:1:\"-\";s:16:\"breadcrumbs_home\";s:2:\"on\";s:22:\"breadcrumbs_home_label\";s:4:\"Home\";s:26:\"breadcrumbs_archive_format\";s:15:\"Archives for %s\";s:25:\"breadcrumbs_search_format\";s:14:\"Results for %s\";s:21:\"breadcrumbs_404_label\";s:25:\"404 Error: page not found\";s:31:\"breadcrumbs_ancestor_categories\";s:3:\"off\";s:21:\"breadcrumbs_blog_page\";s:3:\"off\";s:16:\"404_monitor_mode\";s:6:\"simple\";s:17:\"404_monitor_limit\";i:100;s:35:\"404_monitor_ignore_query_parameters\";s:2:\"on\";s:24:\"redirections_header_code\";s:3:\"301\";s:18:\"redirections_debug\";s:3:\"off\";s:23:\"console_caching_control\";s:2:\"90\";s:21:\"console_email_reports\";s:2:\"on\";s:23:\"console_email_frequency\";s:7:\"monthly\";s:22:\"wc_remove_product_base\";s:3:\"off\";s:23:\"wc_remove_category_base\";s:3:\"off\";s:31:\"wc_remove_category_parent_slugs\";s:3:\"off\";s:18:\"rss_before_content\";s:0:\"\";s:17:\"rss_after_content\";s:0:\"\";s:19:\"wc_remove_generator\";s:2:\"on\";s:24:\"remove_shop_snippet_data\";s:2:\"on\";s:18:\"frontend_seo_score\";s:3:\"off\";s:29:\"frontend_seo_score_post_types\";a:1:{i:0;s:4:\"post\";}s:27:\"frontend_seo_score_position\";s:3:\"top\";s:10:\"setup_mode\";s:4:\"easy\";s:21:\"content_ai_post_types\";a:3:{i:0;s:4:\"post\";i:1;s:4:\"page\";i:2;s:11:\"sts_project\";}s:18:\"content_ai_country\";s:3:\"all\";s:15:\"content_ai_tone\";s:6:\"Formal\";s:19:\"content_ai_audience\";s:16:\"General Audience\";s:19:\"content_ai_language\";s:6:\"Danish\";s:15:\"analytics_stats\";s:2:\"on\";s:15:\"toc_block_title\";s:17:\"Table of Contents\";s:20:\"toc_block_list_style\";s:2:\"ul\";s:15:\"llms_post_types\";a:3:{i:0;s:4:\"post\";i:1;s:4:\"page\";i:2;s:11:\"sts_project\";}}','auto');
INSERT INTO `wp_options` VALUES (504,'rank-math-options-titles','a:121:{s:24:\"noindex_empty_taxonomies\";s:2:\"on\";s:15:\"title_separator\";s:1:\"-\";s:17:\"capitalize_titles\";s:3:\"off\";s:17:\"twitter_card_type\";s:19:\"summary_large_image\";s:19:\"knowledgegraph_type\";s:7:\"company\";s:19:\"knowledgegraph_name\";s:19:\"Super Total Service\";s:12:\"website_name\";s:19:\"Super Total Service\";s:19:\"local_business_type\";s:12:\"Organization\";s:20:\"local_address_format\";s:43:\"{address} {locality}, {region} {postalcode}\";s:13:\"opening_hours\";a:7:{i:0;a:2:{s:3:\"day\";s:6:\"Monday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:1;a:2:{s:3:\"day\";s:7:\"Tuesday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:2;a:2:{s:3:\"day\";s:9:\"Wednesday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:3;a:2:{s:3:\"day\";s:8:\"Thursday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:4;a:2:{s:3:\"day\";s:6:\"Friday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:5;a:2:{s:3:\"day\";s:8:\"Saturday\";s:4:\"time\";s:11:\"09:00-17:00\";}i:6;a:2:{s:3:\"day\";s:6:\"Sunday\";s:4:\"time\";s:11:\"09:00-17:00\";}}s:20:\"opening_hours_format\";s:3:\"off\";s:14:\"homepage_title\";s:34:\"%sitename% %page% %sep% %sitedesc%\";s:20:\"homepage_description\";s:0:\"\";s:22:\"homepage_custom_robots\";s:3:\"off\";s:23:\"disable_author_archives\";s:3:\"off\";s:15:\"url_author_base\";s:6:\"author\";s:20:\"author_custom_robots\";s:2:\"on\";s:13:\"author_robots\";a:1:{i:0;s:7:\"noindex\";}s:20:\"author_archive_title\";s:30:\"%name% %sep% %sitename% %page%\";s:19:\"author_add_meta_box\";s:2:\"on\";s:21:\"disable_date_archives\";s:2:\"on\";s:18:\"date_archive_title\";s:30:\"%date% %page% %sep% %sitename%\";s:12:\"search_title\";s:38:\"%search_query% %page% %sep% %sitename%\";s:9:\"404_title\";s:31:\"Page Not Found %sep% %sitename%\";s:19:\"date_archive_robots\";a:1:{i:0;s:7:\"noindex\";}s:14:\"noindex_search\";s:2:\"on\";s:24:\"noindex_archive_subpages\";s:3:\"off\";s:26:\"noindex_password_protected\";s:3:\"off\";s:32:\"pt_download_default_rich_snippet\";s:7:\"product\";s:29:\"author_slack_enhanced_sharing\";s:2:\"on\";s:13:\"pt_post_title\";s:24:\"%title% %sep% %sitename%\";s:19:\"pt_post_description\";s:9:\"%excerpt%\";s:14:\"pt_post_robots\";a:1:{i:0;s:5:\"index\";}s:21:\"pt_post_custom_robots\";s:3:\"off\";s:28:\"pt_post_default_rich_snippet\";s:7:\"article\";s:28:\"pt_post_default_article_type\";s:11:\"BlogPosting\";s:28:\"pt_post_default_snippet_name\";s:11:\"%seo_title%\";s:28:\"pt_post_default_snippet_desc\";s:17:\"%seo_description%\";s:30:\"pt_post_slack_enhanced_sharing\";s:2:\"on\";s:17:\"pt_post_ls_use_fk\";s:6:\"titles\";s:20:\"pt_post_add_meta_box\";s:2:\"on\";s:20:\"pt_post_bulk_editing\";s:7:\"editing\";s:24:\"pt_post_link_suggestions\";s:2:\"on\";s:24:\"pt_post_primary_taxonomy\";s:8:\"category\";s:13:\"pt_page_title\";s:24:\"%title% %sep% %sitename%\";s:19:\"pt_page_description\";s:9:\"%excerpt%\";s:14:\"pt_page_robots\";a:1:{i:0;s:5:\"index\";}s:21:\"pt_page_custom_robots\";s:3:\"off\";s:28:\"pt_page_default_rich_snippet\";s:7:\"article\";s:28:\"pt_page_default_article_type\";s:7:\"Article\";s:28:\"pt_page_default_snippet_name\";s:11:\"%seo_title%\";s:28:\"pt_page_default_snippet_desc\";s:17:\"%seo_description%\";s:30:\"pt_page_slack_enhanced_sharing\";s:2:\"on\";s:17:\"pt_page_ls_use_fk\";s:6:\"titles\";s:20:\"pt_page_add_meta_box\";s:2:\"on\";s:20:\"pt_page_bulk_editing\";s:7:\"editing\";s:24:\"pt_page_link_suggestions\";s:2:\"on\";s:19:\"pt_attachment_title\";s:24:\"%title% %sep% %sitename%\";s:25:\"pt_attachment_description\";s:9:\"%excerpt%\";s:20:\"pt_attachment_robots\";a:1:{i:0;s:7:\"noindex\";}s:27:\"pt_attachment_custom_robots\";s:2:\"on\";s:34:\"pt_attachment_default_rich_snippet\";s:3:\"off\";s:34:\"pt_attachment_default_article_type\";s:7:\"Article\";s:34:\"pt_attachment_default_snippet_name\";s:11:\"%seo_title%\";s:34:\"pt_attachment_default_snippet_desc\";s:17:\"%seo_description%\";s:36:\"pt_attachment_slack_enhanced_sharing\";s:3:\"off\";s:26:\"pt_attachment_add_meta_box\";s:3:\"off\";s:20:\"pt_sts_project_title\";s:24:\"%title% %sep% %sitename%\";s:26:\"pt_sts_project_description\";s:9:\"%excerpt%\";s:21:\"pt_sts_project_robots\";a:1:{i:0;s:5:\"index\";}s:28:\"pt_sts_project_custom_robots\";s:3:\"off\";s:35:\"pt_sts_project_default_rich_snippet\";s:3:\"off\";s:35:\"pt_sts_project_default_article_type\";s:7:\"Article\";s:35:\"pt_sts_project_default_snippet_name\";s:11:\"%seo_title%\";s:35:\"pt_sts_project_default_snippet_desc\";s:17:\"%seo_description%\";s:37:\"pt_sts_project_slack_enhanced_sharing\";s:3:\"off\";s:24:\"pt_sts_project_ls_use_fk\";s:6:\"titles\";s:27:\"pt_sts_project_add_meta_box\";s:2:\"on\";s:27:\"pt_sts_project_bulk_editing\";s:7:\"editing\";s:31:\"pt_sts_project_link_suggestions\";s:2:\"on\";s:16:\"pt_product_title\";s:24:\"%title% %sep% %sitename%\";s:22:\"pt_product_description\";s:9:\"%excerpt%\";s:17:\"pt_product_robots\";a:1:{i:0;s:5:\"index\";}s:24:\"pt_product_custom_robots\";s:3:\"off\";s:31:\"pt_product_default_rich_snippet\";s:7:\"product\";s:31:\"pt_product_default_article_type\";s:7:\"Article\";s:31:\"pt_product_default_snippet_name\";s:11:\"%seo_title%\";s:31:\"pt_product_default_snippet_desc\";s:17:\"%seo_description%\";s:33:\"pt_product_slack_enhanced_sharing\";s:2:\"on\";s:20:\"pt_product_ls_use_fk\";s:6:\"titles\";s:23:\"pt_product_add_meta_box\";s:2:\"on\";s:23:\"pt_product_bulk_editing\";s:7:\"editing\";s:27:\"pt_product_link_suggestions\";s:2:\"on\";s:27:\"pt_product_primary_taxonomy\";s:11:\"product_cat\";s:18:\"pt_web-story_title\";s:24:\"%title% %sep% %sitename%\";s:24:\"pt_web-story_description\";s:9:\"%excerpt%\";s:19:\"pt_web-story_robots\";a:1:{i:0;s:5:\"index\";}s:26:\"pt_web-story_custom_robots\";s:3:\"off\";s:33:\"pt_web-story_default_rich_snippet\";s:7:\"article\";s:33:\"pt_web-story_default_article_type\";s:7:\"Article\";s:33:\"pt_web-story_default_snippet_name\";s:11:\"%seo_title%\";s:33:\"pt_web-story_default_snippet_desc\";s:17:\"%seo_description%\";s:35:\"pt_web-story_slack_enhanced_sharing\";s:3:\"off\";s:25:\"pt_web-story_add_meta_box\";s:3:\"off\";s:18:\"tax_category_title\";s:23:\"%term% %sep% %sitename%\";s:19:\"tax_category_robots\";a:1:{i:0;s:5:\"index\";}s:25:\"tax_category_add_meta_box\";s:2:\"on\";s:26:\"tax_category_custom_robots\";s:3:\"off\";s:24:\"tax_category_description\";s:18:\"%term_description%\";s:35:\"tax_category_slack_enhanced_sharing\";s:2:\"on\";s:25:\"tax_category_bulk_editing\";i:0;s:18:\"tax_post_tag_title\";s:23:\"%term% %sep% %sitename%\";s:19:\"tax_post_tag_robots\";a:1:{i:0;s:7:\"noindex\";}s:25:\"tax_post_tag_add_meta_box\";s:2:\"on\";s:26:\"tax_post_tag_custom_robots\";s:2:\"on\";s:24:\"tax_post_tag_description\";s:18:\"%term_description%\";s:35:\"tax_post_tag_slack_enhanced_sharing\";s:2:\"on\";s:25:\"tax_post_tag_bulk_editing\";i:0;s:31:\"remove_product_cat_snippet_data\";s:2:\"on\";s:31:\"remove_product_tag_snippet_data\";s:2:\"on\";s:28:\"tax_post_format_add_meta_box\";s:2:\"on\";}','auto');
INSERT INTO `wp_options` VALUES (505,'rank-math-options-sitemap','a:17:{s:14:\"items_per_page\";i:200;s:14:\"include_images\";s:2:\"on\";s:22:\"include_featured_image\";s:3:\"off\";s:13:\"exclude_roles\";a:2:{i:0;s:11:\"contributor\";i:1;s:10:\"subscriber\";}s:12:\"html_sitemap\";s:2:\"on\";s:20:\"html_sitemap_display\";s:9:\"shortcode\";s:17:\"html_sitemap_sort\";s:9:\"published\";s:23:\"html_sitemap_seo_titles\";s:6:\"titles\";s:15:\"authors_sitemap\";s:2:\"on\";s:15:\"pt_post_sitemap\";s:2:\"on\";s:15:\"pt_page_sitemap\";s:2:\"on\";s:21:\"pt_attachment_sitemap\";s:3:\"off\";s:22:\"pt_sts_project_sitemap\";s:2:\"on\";s:18:\"pt_product_sitemap\";s:2:\"on\";s:20:\"pt_web-story_sitemap\";s:3:\"off\";s:20:\"tax_category_sitemap\";s:2:\"on\";s:20:\"tax_post_tag_sitemap\";s:3:\"off\";}','auto');
INSERT INTO `wp_options` VALUES (506,'rank-math-options-instant-indexing','a:2:{s:15:\"bing_post_types\";a:2:{i:0;s:4:\"post\";i:1;s:4:\"page\";}s:16:\"indexnow_api_key\";s:32:\"0e917917d38346a2bc912e0f591c8269\";}','auto');
INSERT INTO `wp_options` VALUES (509,'rank_math_version','1.0.277.1','auto');
INSERT INTO `wp_options` VALUES (510,'rank_math_db_version','1','auto');
INSERT INTO `wp_options` VALUES (511,'rank_math_install_date','1787926350','auto');
INSERT INTO `wp_options` VALUES (517,'action_scheduler_lock_async-request-runner','6a9546f3ba5432.57274770|1788167983','no');
INSERT INTO `wp_options` VALUES (518,'rank_math_notifications','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (519,'rank_math_registration_skip','1','auto');
INSERT INTO `wp_options` VALUES (520,'rank_math_review_notice_date','1789135957','off');
INSERT INTO `wp_options` VALUES (521,'rank_math_pro_notice_date','1788790357','off');
INSERT INTO `wp_options` VALUES (522,'rank_math_review_posts_converted','1','auto');
INSERT INTO `wp_options` VALUES (523,'_transient_rank_math_first_submenu_id','rank-math','on');
INSERT INTO `wp_options` VALUES (525,'action_scheduler_migration_status','complete','auto');
INSERT INTO `wp_options` VALUES (526,'as_has_wp_comment_logs','no','on');
INSERT INTO `wp_options` VALUES (527,'_transient__rank_math_site_type','business','on');
INSERT INTO `wp_options` VALUES (529,'rank_math_is_configured','1','off');
INSERT INTO `wp_options` VALUES (530,'rank_math_google_analytic_profile','a:3:{s:7:\"country\";s:3:\"all\";s:7:\"profile\";s:0:\"\";s:19:\"enable_index_status\";s:1:\"1\";}','auto');
INSERT INTO `wp_options` VALUES (531,'rank_math_google_analytic_options','a:11:{s:10:\"adsense_id\";s:0:\"\";s:10:\"account_id\";s:0:\"\";s:11:\"property_id\";s:0:\"\";s:7:\"view_id\";s:0:\"\";s:14:\"measurement_id\";s:0:\"\";s:11:\"stream_name\";s:0:\"\";s:7:\"country\";s:3:\"all\";s:12:\"install_code\";s:0:\"\";s:12:\"anonymize_ip\";s:0:\"\";s:11:\"local_ga_js\";s:0:\"\";s:16:\"exclude_loggedin\";s:0:\"\";}','auto');
INSERT INTO `wp_options` VALUES (542,'rank_math_viewed_index_status','1','auto');
INSERT INTO `wp_options` VALUES (561,'_site_transient_update_core','O:8:\"stdClass\":4:{s:7:\"updates\";a:1:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:6:\"latest\";s:8:\"download\";s:63:\"https://downloads.wordpress.org/release/da_DK/wordpress-7.1.zip\";s:6:\"locale\";s:5:\"da_DK\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:63:\"https://downloads.wordpress.org/release/da_DK/wordpress-7.1.zip\";s:10:\"no_content\";s:0:\"\";s:11:\"new_bundled\";s:0:\"\";s:7:\"partial\";s:0:\"\";s:8:\"rollback\";s:0:\"\";}s:7:\"current\";s:3:\"7.1\";s:7:\"version\";s:3:\"7.1\";s:11:\"php_version\";s:3:\"7.4\";s:13:\"mysql_version\";s:5:\"5.5.5\";s:11:\"new_bundled\";s:3:\"6.7\";s:15:\"partial_version\";s:0:\"\";}}s:12:\"last_checked\";i:1788347369;s:15:\"version_checked\";s:3:\"7.1\";s:12:\"translations\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (562,'_site_transient_update_themes','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1788332420;s:7:\"checked\";a:1:{s:25:\"supertotalservice-dk-main\";s:5:\"2.0.0\";}s:8:\"response\";a:0:{}s:9:\"no_update\";a:0:{}s:12:\"translations\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (563,'_site_transient_update_plugins','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1788332420;s:8:\"response\";a:1:{s:30:\"seo-by-rank-math/rank-math.php\";O:8:\"stdClass\":13:{s:2:\"id\";s:30:\"w.org/plugins/seo-by-rank-math\";s:4:\"slug\";s:16:\"seo-by-rank-math\";s:6:\"plugin\";s:30:\"seo-by-rank-math/rank-math.php\";s:11:\"new_version\";s:9:\"1.0.277.2\";s:3:\"url\";s:47:\"https://wordpress.org/plugins/seo-by-rank-math/\";s:7:\"package\";s:69:\"https://downloads.wordpress.org/plugin/seo-by-rank-math.1.0.277.2.zip\";s:5:\"icons\";a:2:{s:2:\"1x\";s:61:\"https://ps.w.org/seo-by-rank-math/assets/icon.svg?rev=3438330\";s:3:\"svg\";s:61:\"https://ps.w.org/seo-by-rank-math/assets/icon.svg?rev=3438330\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:72:\"https://ps.w.org/seo-by-rank-math/assets/banner-1544x500.png?rev=2639678\";s:2:\"1x\";s:71:\"https://ps.w.org/seo-by-rank-math/assets/banner-772x250.png?rev=2639678\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"6.7\";s:6:\"tested\";s:3:\"7.1\";s:12:\"requires_php\";s:3:\"7.4\";s:16:\"requires_plugins\";a:0:{}}}s:12:\"translations\";a:2:{i:0;a:7:{s:4:\"type\";s:6:\"plugin\";s:4:\"slug\";s:16:\"seo-by-rank-math\";s:8:\"language\";s:5:\"da_DK\";s:7:\"version\";s:9:\"1.0.277.1\";s:7:\"updated\";s:19:\"2026-02-14 18:00:57\";s:7:\"package\";s:87:\"https://downloads.wordpress.org/translation/plugin/seo-by-rank-math/1.0.277.1/da_DK.zip\";s:10:\"autoupdate\";b:1;}i:1;a:7:{s:4:\"type\";s:6:\"plugin\";s:4:\"slug\";s:15:\"google-site-kit\";s:8:\"language\";s:5:\"da_DK\";s:7:\"version\";s:7:\"1.186.0\";s:7:\"updated\";s:19:\"2026-03-06 10:36:18\";s:7:\"package\";s:84:\"https://downloads.wordpress.org/translation/plugin/google-site-kit/1.186.0/da_DK.zip\";s:10:\"autoupdate\";b:1;}}s:9:\"no_update\";a:1:{s:35:\"google-site-kit/google-site-kit.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:29:\"w.org/plugins/google-site-kit\";s:4:\"slug\";s:15:\"google-site-kit\";s:6:\"plugin\";s:35:\"google-site-kit/google-site-kit.php\";s:11:\"new_version\";s:7:\"1.186.0\";s:3:\"url\";s:46:\"https://wordpress.org/plugins/google-site-kit/\";s:7:\"package\";s:66:\"https://downloads.wordpress.org/plugin/google-site-kit.1.186.0.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:68:\"https://ps.w.org/google-site-kit/assets/icon-256x256.png?rev=3606666\";s:2:\"1x\";s:68:\"https://ps.w.org/google-site-kit/assets/icon-128x128.png?rev=3606666\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:71:\"https://ps.w.org/google-site-kit/assets/banner-1544x500.png?rev=3606666\";s:2:\"1x\";s:70:\"https://ps.w.org/google-site-kit/assets/banner-772x250.png?rev=3606666\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"5.2\";}}s:7:\"checked\";a:6:{s:30:\"seo-by-rank-math/rank-math.php\";s:9:\"1.0.277.1\";s:35:\"google-site-kit/google-site-kit.php\";s:7:\"1.186.0\";s:43:\"sts-content-manager/sts-content-manager.php\";s:5:\"2.0.0\";s:37:\"sts-news-manager/sts-news-manager.php\";s:5:\"1.0.0\";s:45:\"sts-projects-manager/sts-projects-manager.php\";s:5:\"1.0.0\";s:31:\"wpconvert-cpt/wpconvert-cpt.php\";s:5:\"1.4.5\";}}','off');
INSERT INTO `wp_options` VALUES (605,'_site_transient_timeout_theme_roots','1788334220','off');
INSERT INTO `wp_options` VALUES (606,'_site_transient_theme_roots','a:1:{s:25:\"supertotalservice-dk-main\";s:7:\"/themes\";}','off');
INSERT INTO `wp_options` VALUES (613,'_site_transient_timeout_wp_theme_files_patterns-a443ec923b20bc8d1bc612c2756ef559','1788348575','off');
INSERT INTO `wp_options` VALUES (615,'_site_transient_wp_theme_files_patterns-a443ec923b20bc8d1bc612c2756ef559','a:2:{s:7:\"version\";s:5:\"2.0.0\";s:8:\"patterns\";a:0:{}}','off');
INSERT INTO `wp_options` VALUES (619,'_transient_doing_cron','1788347261.3650510311126708984375','on');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=966 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_postmeta`
--

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,1,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (2,1,'_wp_page_template','page-hvem-er-sts.php');
INSERT INTO `wp_postmeta` VALUES (3,2,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (5,3,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (6,3,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (7,4,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (8,4,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (9,5,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (10,5,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (11,6,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (12,6,'_wp_page_template','page-erhvervsrengoering.php');
INSERT INTO `wp_postmeta` VALUES (13,7,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (15,8,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (16,8,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (17,9,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (18,9,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (19,10,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (20,10,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (21,11,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (22,11,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (23,12,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (24,12,'_wp_page_template','page-handelsbetingelser.php');
INSERT INTO `wp_postmeta` VALUES (25,13,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (26,13,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (27,14,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (28,14,'_wp_page_template','page-kontakt.php');
INSERT INTO `wp_postmeta` VALUES (29,15,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (30,15,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (31,16,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (32,16,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (33,17,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (34,17,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (35,18,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (36,18,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (37,19,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (38,19,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (39,20,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (40,20,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (41,21,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (42,21,'_wp_page_template','page-service.php');
INSERT INTO `wp_postmeta` VALUES (43,22,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (44,22,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (45,23,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (46,23,'_wp_page_template','page-sts-byg.php');
INSERT INTO `wp_postmeta` VALUES (47,24,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (48,24,'_wp_page_template','page-sts-mal.php');
INSERT INTO `wp_postmeta` VALUES (49,25,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (50,25,'_wp_page_template','page-sts-ren.php');
INSERT INTO `wp_postmeta` VALUES (51,26,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (52,26,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (53,27,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (54,27,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (55,28,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (56,28,'_wp_page_template','page-vi-udfoerer-nedrivning-af-mink-farm-til-fast-pris.php');
INSERT INTO `wp_postmeta` VALUES (57,29,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (58,29,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (59,30,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (60,30,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (61,31,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (62,31,'_wp_page_template','page-vinduespudsning-polering-afrens-m-v-tilbydes-baade-manuelt-med-rentvandsanlaeg.php');
INSERT INTO `wp_postmeta` VALUES (63,32,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (64,33,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (65,33,'_wp_page_template','page-components-footer.php');
INSERT INTO `wp_postmeta` VALUES (66,34,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (67,34,'_wp_page_template','page-components-header.php');
INSERT INTO `wp_postmeta` VALUES (68,35,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (69,36,'_wpconvert_generated','1');
INSERT INTO `wp_postmeta` VALUES (70,36,'_wp_page_template','page-velkommen-asbestarbejde-2.php');
INSERT INTO `wp_postmeta` VALUES (162,47,'_wp_attached_file','2026/08/Groen-Smiley.jpg');
INSERT INTO `wp_postmeta` VALUES (163,47,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:850;s:6:\"height\";i:567;s:4:\"file\";s:24:\"2026/08/Groen-Smiley.jpg\";s:8:\"filesize\";i:24103;s:5:\"sizes\";a:3:{s:6:\"medium\";a:5:{s:4:\"file\";s:24:\"Groen-Smiley-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6300;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:24:\"Groen-Smiley-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3355;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:24:\"Groen-Smiley-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:20673;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (164,48,'_wp_attached_file','2026/08/asbest-og-nedrivning.jpg');
INSERT INTO `wp_postmeta` VALUES (165,48,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1384;s:4:\"file\";s:32:\"2026/08/asbest-og-nedrivning.jpg\";s:8:\"filesize\";i:299262;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:32:\"asbest-og-nedrivning-300x208.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:208;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:17952;}s:5:\"large\";a:5:{s:4:\"file\";s:33:\"asbest-og-nedrivning-1024x709.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:709;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:118079;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:32:\"asbest-og-nedrivning-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7562;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:32:\"asbest-og-nedrivning-768x531.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:531;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:77555;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:34:\"asbest-og-nedrivning-1536x1063.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1063;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:211836;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (166,49,'_wp_attached_file','2026/08/byggepladsservice-20260807-140437-156972.jpg');
INSERT INTO `wp_postmeta` VALUES (167,49,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:570;s:6:\"height\";i:400;s:4:\"file\";s:52:\"2026/08/byggepladsservice-20260807-140437-156972.jpg\";s:8:\"filesize\";i:49515;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:52:\"byggepladsservice-20260807-140437-156972-300x211.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:211;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:19009;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:52:\"byggepladsservice-20260807-140437-156972-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7961;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (168,50,'_wp_attached_file','2026/08/byggepladsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (169,50,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1331;s:4:\"file\";s:29:\"2026/08/byggepladsservice.jpg\";s:8:\"filesize\";i:483422;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:29:\"byggepladsservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:15481;}s:5:\"large\";a:5:{s:4:\"file\";s:30:\"byggepladsservice-1024x681.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:681;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:143215;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:29:\"byggepladsservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6592;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:29:\"byggepladsservice-768x511.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:511;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:85037;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:31:\"byggepladsservice-1536x1022.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1022;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:295379;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (170,51,'_wp_attached_file','2026/08/ejendomsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (171,51,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:27:\"2026/08/ejendomsservice.jpg\";s:8:\"filesize\";i:104692;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:27:\"ejendomsservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7202;}s:5:\"large\";a:5:{s:4:\"file\";s:28:\"ejendomsservice-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:40213;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:27:\"ejendomsservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3699;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:27:\"ejendomsservice-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:26403;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:29:\"ejendomsservice-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:73586;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (172,52,'_wp_attached_file','2026/08/epoxy-og-specialmaling.jpg');
INSERT INTO `wp_postmeta` VALUES (173,52,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1325;s:4:\"file\";s:34:\"2026/08/epoxy-og-specialmaling.jpg\";s:8:\"filesize\";i:353063;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:34:\"epoxy-og-specialmaling-300x199.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:199;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:11068;}s:5:\"large\";a:5:{s:4:\"file\";s:35:\"epoxy-og-specialmaling-1024x678.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:678;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:94037;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:34:\"epoxy-og-specialmaling-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4899;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:34:\"epoxy-og-specialmaling-768x509.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:509;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:55774;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:36:\"epoxy-og-specialmaling-1536x1018.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1018;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:201729;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (174,53,'_wp_attached_file','2026/08/facademaling.jpg');
INSERT INTO `wp_postmeta` VALUES (175,53,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:24:\"2026/08/facademaling.jpg\";s:8:\"filesize\";i:263459;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:24:\"facademaling-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:11226;}s:5:\"large\";a:5:{s:4:\"file\";s:25:\"facademaling-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:72630;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:24:\"facademaling-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5184;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:24:\"facademaling-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:45310;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:26:\"facademaling-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:150521;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (176,54,'_wp_attached_file','2026/08/gartnerservice.jpg');
INSERT INTO `wp_postmeta` VALUES (177,54,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:26:\"2026/08/gartnerservice.jpg\";s:8:\"filesize\";i:588132;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:26:\"gartnerservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:17007;}s:5:\"large\";a:5:{s:4:\"file\";s:27:\"gartnerservice-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:161399;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:26:\"gartnerservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6999;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:26:\"gartnerservice-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:94556;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:28:\"gartnerservice-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:340488;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (178,55,'_wp_attached_file','2026/08/glatfoere-bekaempelse-snerydning-og-saltning.jpg');
INSERT INTO `wp_postmeta` VALUES (179,55,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:56:\"2026/08/glatfoere-bekaempelse-snerydning-og-saltning.jpg\";s:8:\"filesize\";i:363827;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:56:\"glatfoere-bekaempelse-snerydning-og-saltning-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:14591;}s:5:\"large\";a:5:{s:4:\"file\";s:57:\"glatfoere-bekaempelse-snerydning-og-saltning-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:121097;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:56:\"glatfoere-bekaempelse-snerydning-og-saltning-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6467;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:56:\"glatfoere-bekaempelse-snerydning-og-saltning-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:73630;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:58:\"glatfoere-bekaempelse-snerydning-og-saltning-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:235619;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (180,58,'_wp_attached_file','2026/08/grass.jpg');
INSERT INTO `wp_postmeta` VALUES (181,57,'_wp_attached_file','2026/08/grass.jpg');
INSERT INTO `wp_postmeta` VALUES (182,58,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:17:\"2026/08/grass.jpg\";s:8:\"filesize\";i:20126;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"grass-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:13783;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:17:\"grass-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6436;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (183,57,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:17:\"2026/08/grass.jpg\";s:8:\"filesize\";i:20126;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"grass-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:0;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:17:\"grass-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6436;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (184,59,'_wp_attached_file','2026/08/gulvbehandling.jpg');
INSERT INTO `wp_postmeta` VALUES (185,59,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1335;s:4:\"file\";s:26:\"2026/08/gulvbehandling.jpg\";s:8:\"filesize\";i:224381;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:12085;}s:5:\"large\";a:5:{s:4:\"file\";s:27:\"gulvbehandling-1024x684.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:684;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:81036;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5367;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-768x513.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:513;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:52052;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:28:\"gulvbehandling-1536x1025.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1025;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:150743;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (186,60,'_wp_attached_file','2026/08/gulvbehandling.jpg');
INSERT INTO `wp_postmeta` VALUES (187,60,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1335;s:4:\"file\";s:26:\"2026/08/gulvbehandling.jpg\";s:8:\"filesize\";i:224381;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:12085;}s:5:\"large\";a:5:{s:4:\"file\";s:27:\"gulvbehandling-1024x684.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:684;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:81036;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5367;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:26:\"gulvbehandling-768x513.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:513;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:52052;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:28:\"gulvbehandling-1536x1025.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1025;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:150743;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (188,61,'_wp_attached_file','2026/08/haandvaerkere.jpg');
INSERT INTO `wp_postmeta` VALUES (189,61,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:25:\"2026/08/haandvaerkere.jpg\";s:8:\"filesize\";i:234791;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:25:\"haandvaerkere-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:13568;}s:5:\"large\";a:5:{s:4:\"file\";s:26:\"haandvaerkere-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:84716;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:25:\"haandvaerkere-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6487;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:25:\"haandvaerkere-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:54598;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:27:\"haandvaerkere-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:157624;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (190,62,'_wp_attached_file','2026/08/insta-800-certificeret-kontrol-og-inspektion.jpg');
INSERT INTO `wp_postmeta` VALUES (191,62,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:56:\"2026/08/insta-800-certificeret-kontrol-og-inspektion.jpg\";s:8:\"filesize\";i:191797;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:56:\"insta-800-certificeret-kontrol-og-inspektion-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:8610;}s:5:\"large\";a:5:{s:4:\"file\";s:57:\"insta-800-certificeret-kontrol-og-inspektion-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:61509;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:56:\"insta-800-certificeret-kontrol-og-inspektion-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3667;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:56:\"insta-800-certificeret-kontrol-og-inspektion-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:37312;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:58:\"insta-800-certificeret-kontrol-og-inspektion-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:123872;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (192,64,'_wp_attached_file','2026/08/lady-1-20260807-135638-ae645e.jpg');
INSERT INTO `wp_postmeta` VALUES (193,63,'_wp_attached_file','2026/08/lady-1-20260807-135638-ae645e.jpg');
INSERT INTO `wp_postmeta` VALUES (194,64,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:533;s:6:\"height\";i:400;s:4:\"file\";s:41:\"2026/08/lady-1-20260807-135638-ae645e.jpg\";s:8:\"filesize\";i:16680;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:41:\"lady-1-20260807-135638-ae645e-300x225.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:225;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6706;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:41:\"lady-1-20260807-135638-ae645e-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3357;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (195,63,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:533;s:6:\"height\";i:400;s:4:\"file\";s:41:\"2026/08/lady-1-20260807-135638-ae645e.jpg\";s:8:\"filesize\";i:16680;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:41:\"lady-1-20260807-135638-ae645e-300x225.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:225;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6706;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:41:\"lady-1-20260807-135638-ae645e-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3357;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (196,65,'_wp_attached_file','2026/08/lady-1.jpg');
INSERT INTO `wp_postmeta` VALUES (197,66,'_wp_attached_file','2026/08/lady-1.jpg');
INSERT INTO `wp_postmeta` VALUES (198,65,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:1920;s:6:\"height\";i:400;s:4:\"file\";s:18:\"2026/08/lady-1.jpg\";s:8:\"filesize\";i:41078;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"lady-1-300x63.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:63;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:2406;}s:5:\"large\";a:5:{s:4:\"file\";s:19:\"lady-1-1024x213.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:213;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:14800;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:18:\"lady-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:1719;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:18:\"lady-1-768x160.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:160;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9307;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:19:\"lady-1-1536x320.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:320;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:27717;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (199,66,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:1920;s:6:\"height\";i:400;s:4:\"file\";s:18:\"2026/08/lady-1.jpg\";s:8:\"filesize\";i:41078;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"lady-1-300x63.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:63;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:2406;}s:5:\"large\";a:5:{s:4:\"file\";s:19:\"lady-1-1024x213.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:213;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:14800;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:18:\"lady-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:1719;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:18:\"lady-1-768x160.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:160;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9307;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:19:\"lady-1-1536x320.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:320;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:27717;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (200,67,'_wp_attached_file','2026/08/maler.jpg');
INSERT INTO `wp_postmeta` VALUES (201,67,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:17:\"2026/08/maler.jpg\";s:8:\"filesize\";i:199379;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"maler-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:8317;}s:5:\"large\";a:5:{s:4:\"file\";s:18:\"maler-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:58921;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:17:\"maler-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3919;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:17:\"maler-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:36417;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:19:\"maler-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:119434;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (202,68,'_wp_attached_file','2026/08/mandskabsudlejning.jpg');
INSERT INTO `wp_postmeta` VALUES (203,68,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:30:\"2026/08/mandskabsudlejning.jpg\";s:8:\"filesize\";i:566390;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:30:\"mandskabsudlejning-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:11736;}s:5:\"large\";a:5:{s:4:\"file\";s:31:\"mandskabsudlejning-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:118352;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:30:\"mandskabsudlejning-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5608;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:30:\"mandskabsudlejning-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:63871;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:32:\"mandskabsudlejning-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:289952;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (204,69,'_wp_attached_file','2026/08/mask-20260807-141816-8a6891.jpg');
INSERT INTO `wp_postmeta` VALUES (205,70,'_wp_attached_file','2026/08/mask-20260807-141816-8a6891.jpg');
INSERT INTO `wp_postmeta` VALUES (206,69,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:39:\"2026/08/mask-20260807-141816-8a6891.jpg\";s:8:\"filesize\";i:12185;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:39:\"mask-20260807-141816-8a6891-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:0;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:39:\"mask-20260807-141816-8a6891-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5178;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (207,70,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:39:\"2026/08/mask-20260807-141816-8a6891.jpg\";s:8:\"filesize\";i:12185;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:39:\"mask-20260807-141816-8a6891-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9398;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:39:\"mask-20260807-141816-8a6891-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5178;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (208,72,'_wp_attached_file','2026/08/mask.jpg');
INSERT INTO `wp_postmeta` VALUES (209,71,'_wp_attached_file','2026/08/mask.jpg');
INSERT INTO `wp_postmeta` VALUES (210,72,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:16:\"2026/08/mask.jpg\";s:8:\"filesize\";i:12745;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:16:\"mask-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9511;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:16:\"mask-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5188;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (211,71,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:16:\"2026/08/mask.jpg\";s:8:\"filesize\";i:12745;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:16:\"mask-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9511;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:16:\"mask-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5188;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (212,73,'_wp_attached_file','2026/08/murer.jpg');
INSERT INTO `wp_postmeta` VALUES (213,74,'_wp_attached_file','2026/08/murer.jpg');
INSERT INTO `wp_postmeta` VALUES (214,74,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:17:\"2026/08/murer.jpg\";s:8:\"filesize\";i:518721;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"murer-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:0;}s:5:\"large\";a:5:{s:4:\"file\";s:18:\"murer-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:144751;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:17:\"murer-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6737;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:17:\"murer-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:85431;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:19:\"murer-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:304006;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (215,73,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:17:\"2026/08/murer.jpg\";s:8:\"filesize\";i:518721;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:17:\"murer-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:16162;}s:5:\"large\";a:5:{s:4:\"file\";s:18:\"murer-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:144751;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:17:\"murer-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6737;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:17:\"murer-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:85431;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:19:\"murer-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:304006;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (216,75,'_wp_attached_file','2026/08/nedrivningsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (217,76,'_wp_attached_file','2026/08/nedrivningsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (218,75,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:30:\"2026/08/nedrivningsservice.jpg\";s:8:\"filesize\";i:576166;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:18056;}s:5:\"large\";a:5:{s:4:\"file\";s:31:\"nedrivningsservice-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:169978;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7351;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:100665;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:32:\"nedrivningsservice-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:346382;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (219,76,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:30:\"2026/08/nedrivningsservice.jpg\";s:8:\"filesize\";i:576166;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:18056;}s:5:\"large\";a:5:{s:4:\"file\";s:31:\"nedrivningsservice-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:169978;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7351;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:30:\"nedrivningsservice-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:100665;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:32:\"nedrivningsservice-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:346382;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (220,77,'_wp_attached_file','2026/08/rengoering-efter-haandvaerkere.jpg');
INSERT INTO `wp_postmeta` VALUES (221,77,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1334;s:4:\"file\";s:42:\"2026/08/rengoering-efter-haandvaerkere.jpg\";s:8:\"filesize\";i:273726;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:42:\"rengoering-efter-haandvaerkere-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:8280;}s:5:\"large\";a:5:{s:4:\"file\";s:43:\"rengoering-efter-haandvaerkere-1024x683.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:683;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:65613;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:42:\"rengoering-efter-haandvaerkere-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4265;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:42:\"rengoering-efter-haandvaerkere-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:37911;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:44:\"rengoering-efter-haandvaerkere-1536x1025.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1025;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:148105;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (222,78,'_wp_attached_file','2026/08/rengoering.jpg');
INSERT INTO `wp_postmeta` VALUES (223,78,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:22:\"2026/08/rengoering.jpg\";s:8:\"filesize\";i:179917;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:22:\"rengoering-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:10864;}s:5:\"large\";a:5:{s:4:\"file\";s:23:\"rengoering-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:66404;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:22:\"rengoering-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4662;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:22:\"rengoering-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:42752;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:24:\"rengoering-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:123835;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (224,79,'_wp_attached_file','2026/08/slider-overlay-small.jpg');
INSERT INTO `wp_postmeta` VALUES (225,79,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:1140;s:6:\"height\";i:300;s:4:\"file\";s:32:\"2026/08/slider-overlay-small.jpg\";s:8:\"filesize\";i:26195;s:5:\"sizes\";a:4:{s:6:\"medium\";a:5:{s:4:\"file\";s:31:\"slider-overlay-small-300x79.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:79;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3322;}s:5:\"large\";a:5:{s:4:\"file\";s:33:\"slider-overlay-small-1024x269.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:269;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:20849;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:32:\"slider-overlay-small-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:2757;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:32:\"slider-overlay-small-768x202.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:202;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:13568;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (226,80,'_wp_attached_file','2026/08/sliderdark-20260811-143051-2c441c.jpg');
INSERT INTO `wp_postmeta` VALUES (227,80,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:533;s:6:\"height\";i:400;s:4:\"file\";s:45:\"2026/08/sliderdark-20260811-143051-2c441c.jpg\";s:8:\"filesize\";i:18823;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:45:\"sliderdark-20260811-143051-2c441c-300x225.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:225;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7689;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:45:\"sliderdark-20260811-143051-2c441c-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3700;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (228,81,'_wp_attached_file','2026/08/sliderdark.jpg');
INSERT INTO `wp_postmeta` VALUES (229,81,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:1140;s:6:\"height\";i:400;s:4:\"file\";s:22:\"2026/08/sliderdark.jpg\";s:8:\"filesize\";i:39147;s:5:\"sizes\";a:4:{s:6:\"medium\";a:5:{s:4:\"file\";s:22:\"sliderdark-300x105.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:105;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5011;}s:5:\"large\";a:5:{s:4:\"file\";s:23:\"sliderdark-1024x359.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:359;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:31326;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:22:\"sliderdark-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3031;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:22:\"sliderdark-768x269.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:269;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:20294;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (230,82,'_wp_attached_file','2026/08/spartelarbejde-og-filtopsaetning.jpg');
INSERT INTO `wp_postmeta` VALUES (231,82,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1334;s:4:\"file\";s:44:\"2026/08/spartelarbejde-og-filtopsaetning.jpg\";s:8:\"filesize\";i:190453;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:44:\"spartelarbejde-og-filtopsaetning-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4786;}s:5:\"large\";a:5:{s:4:\"file\";s:45:\"spartelarbejde-og-filtopsaetning-1024x683.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:683;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:42296;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:44:\"spartelarbejde-og-filtopsaetning-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:2751;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:44:\"spartelarbejde-og-filtopsaetning-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:23000;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:46:\"spartelarbejde-og-filtopsaetning-1536x1025.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1025;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:105132;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (232,83,'_wp_attached_file','2026/08/specialopgaver.jpg');
INSERT INTO `wp_postmeta` VALUES (233,83,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:570;s:6:\"height\";i:400;s:4:\"file\";s:26:\"2026/08/specialopgaver.jpg\";s:8:\"filesize\";i:41027;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:26:\"specialopgaver-300x211.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:211;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:12794;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:26:\"specialopgaver-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5550;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (234,84,'_wp_attached_file','2026/08/thinkstockphotos-120274510-20260807-135556-c4b521.jpg');
INSERT INTO `wp_postmeta` VALUES (235,84,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:266;s:6:\"height\";i:200;s:4:\"file\";s:61:\"2026/08/thinkstockphotos-120274510-20260807-135556-c4b521.jpg\";s:8:\"filesize\";i:10129;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:61:\"thinkstockphotos-120274510-20260807-135556-c4b521-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5401;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (236,85,'_wp_attached_file','2026/08/thinkstockphotos-120274510.jpg');
INSERT INTO `wp_postmeta` VALUES (237,85,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:360;s:6:\"height\";i:200;s:4:\"file\";s:38:\"2026/08/thinkstockphotos-120274510.jpg\";s:8:\"filesize\";i:12008;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:38:\"thinkstockphotos-120274510-300x167.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:167;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:8668;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:38:\"thinkstockphotos-120274510-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4726;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (238,86,'_wp_attached_file','2026/08/thinkstockphotos-926208774.jpg');
INSERT INTO `wp_postmeta` VALUES (239,86,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:570;s:6:\"height\";i:302;s:4:\"file\";s:38:\"2026/08/thinkstockphotos-926208774.jpg\";s:8:\"filesize\";i:22439;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:38:\"thinkstockphotos-926208774-300x159.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:159;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:9094;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:38:\"thinkstockphotos-926208774-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4205;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (240,87,'_wp_attached_file','2026/08/toemrer.jpg');
INSERT INTO `wp_postmeta` VALUES (241,87,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:19:\"2026/08/toemrer.jpg\";s:8:\"filesize\";i:270848;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:19:\"toemrer-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:13142;}s:5:\"large\";a:5:{s:4:\"file\";s:20:\"toemrer-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:88981;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:19:\"toemrer-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:5745;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:19:\"toemrer-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:56310;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:21:\"toemrer-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:171944;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (242,88,'_wp_attached_file','2026/08/trappeopgangsmaling.jpg');
INSERT INTO `wp_postmeta` VALUES (243,88,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:31:\"2026/08/trappeopgangsmaling.jpg\";s:8:\"filesize\";i:176400;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:31:\"trappeopgangsmaling-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:8149;}s:5:\"large\";a:5:{s:4:\"file\";s:32:\"trappeopgangsmaling-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:55111;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:31:\"trappeopgangsmaling-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:4121;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:31:\"trappeopgangsmaling-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:33970;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:33:\"trappeopgangsmaling-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:110909;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (244,89,'_wp_attached_file','2026/08/vicevaertservice.jpg');
INSERT INTO `wp_postmeta` VALUES (245,89,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1333;s:4:\"file\";s:28:\"2026/08/vicevaertservice.jpg\";s:8:\"filesize\";i:138173;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:28:\"vicevaertservice-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:7268;}s:5:\"large\";a:5:{s:4:\"file\";s:29:\"vicevaertservice-1024x682.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:682;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:48763;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:28:\"vicevaertservice-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:3594;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:28:\"vicevaertservice-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:30817;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:30:\"vicevaertservice-1536x1024.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1024;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:91675;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (246,90,'_wp_attached_file','2026/08/vinduespolering.jpg');
INSERT INTO `wp_postmeta` VALUES (247,90,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:2000;s:6:\"height\";i:1325;s:4:\"file\";s:27:\"2026/08/vinduespolering.jpg\";s:8:\"filesize\";i:274150;s:5:\"sizes\";a:5:{s:6:\"medium\";a:5:{s:4:\"file\";s:27:\"vinduespolering-300x199.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:199;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:12647;}s:5:\"large\";a:5:{s:4:\"file\";s:28:\"vinduespolering-1024x678.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:678;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:94849;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:27:\"vinduespolering-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:6342;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:27:\"vinduespolering-768x509.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:509;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:59105;}s:9:\"1536x1536\";a:5:{s:4:\"file\";s:29:\"vinduespolering-1536x1018.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:1018;s:9:\"mime-type\";s:10:\"image/jpeg\";s:8:\"filesize\";i:181354;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (248,91,'_wp_attached_file','2026/08/Servicenormen.png');
INSERT INTO `wp_postmeta` VALUES (249,91,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:269;s:6:\"height\";i:146;s:4:\"file\";s:25:\"2026/08/Servicenormen.png\";s:8:\"filesize\";i:29795;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:25:\"Servicenormen-150x146.png\";s:5:\"width\";i:150;s:6:\"height\";i:146;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:22506;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (250,92,'_wp_attached_file','2026/08/layer-10.png');
INSERT INTO `wp_postmeta` VALUES (251,92,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:350;s:6:\"height\";i:193;s:4:\"file\";s:20:\"2026/08/layer-10.png\";s:8:\"filesize\";i:30040;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:20:\"layer-10-300x165.png\";s:5:\"width\";i:300;s:6:\"height\";i:165;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:53354;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:20:\"layer-10-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:30253;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (252,93,'_wp_attached_file','2026/08/logo-sts-rgb.png');
INSERT INTO `wp_postmeta` VALUES (253,93,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:185;s:6:\"height\";i:70;s:4:\"file\";s:24:\"2026/08/logo-sts-rgb.png\";s:8:\"filesize\";i:3636;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:23:\"logo-sts-rgb-150x70.png\";s:5:\"width\";i:150;s:6:\"height\";i:70;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:4470;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (254,94,'_wp_attached_file','2026/08/screenshot.png');
INSERT INTO `wp_postmeta` VALUES (255,94,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:1200;s:6:\"height\";i:900;s:4:\"file\";s:22:\"2026/08/screenshot.png\";s:8:\"filesize\";i:246595;s:5:\"sizes\";a:4:{s:6:\"medium\";a:5:{s:4:\"file\";s:22:\"screenshot-300x225.png\";s:5:\"width\";i:300;s:6:\"height\";i:225;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:36649;}s:5:\"large\";a:5:{s:4:\"file\";s:23:\"screenshot-1024x768.png\";s:5:\"width\";i:1024;s:6:\"height\";i:768;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:231950;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:22:\"screenshot-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:13475;}s:12:\"medium_large\";a:5:{s:4:\"file\";s:22:\"screenshot-768x576.png\";s:5:\"width\";i:768;s:6:\"height\";i:576;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:152055;}}s:10:\"image_meta\";a:13:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}s:3:\"alt\";s:0:\"\";}}');
INSERT INTO `wp_postmeta` VALUES (256,23,'_edit_lock','1787210028:1');
INSERT INTO `wp_postmeta` VALUES (257,98,'_sts_service_id','1786000000001');
INSERT INTO `wp_postmeta` VALUES (258,98,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (259,98,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (260,98,'_sts_service_hero_title','Erhvervsrengøring til kontorer, industri og butikker');
INSERT INTO `wp_postmeta` VALUES (262,98,'_sts_service_benefits','a:6:{i:0;s:40:\"Daglig, ugentlig og månedlig rengøring\";i:1;s:38:\"Kontorrengøring, industri og butikker\";i:2;s:31:\"Miljøvenlige rengøringsmidler\";i:3;s:38:\"INSTA 800-certificeret kvalitetssystem\";i:4;s:22:\"Faste personlige teams\";i:5;s:39:\"Ingen bindingstid – fleksible aftaler\";}');
INSERT INTO `wp_postmeta` VALUES (263,99,'_sts_service_id','1786000000002');
INSERT INTO `wp_postmeta` VALUES (264,99,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (265,99,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (266,99,'_sts_service_hero_title','Erfarne håndværkere til virksomheder og ejendomme');
INSERT INTO `wp_postmeta` VALUES (268,99,'_sts_service_benefits','a:6:{i:0;s:36:\"Maler, murer, tømrer og gulvservice\";i:1;s:36:\"Hurtig mobilisering ved akutte behov\";i:2;s:26:\"Fast pris på alle opgaver\";i:3;s:31:\"Erfarne og autoriserede fagfolk\";i:4;s:29:\"Både små og store projekter\";i:5;s:34:\"Koordinering af flere håndvækere\";}');
INSERT INTO `wp_postmeta` VALUES (269,100,'_sts_service_id','1786000000003');
INSERT INTO `wp_postmeta` VALUES (270,100,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (271,100,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (272,100,'_sts_service_hero_title','Professionel byggepladsservice fra start til slut');
INSERT INTO `wp_postmeta` VALUES (274,100,'_sts_service_benefits','a:6:{i:0;s:43:\"Opsætning og nedtagning af sanitetsmoduler\";i:1;s:39:\"Daglig og ugentlig byggepladsrengøring\";i:2;s:34:\"Asbest- og byggeaffaldshåndtering\";i:3;s:38:\"Skraldeindsamling og containerlogistik\";i:4;s:31:\"Hurtig respons ved akutte behov\";i:5;s:30:\"Dokumenteret miljøhåndtering\";}');
INSERT INTO `wp_postmeta` VALUES (275,101,'_sts_service_id','1786000000004');
INSERT INTO `wp_postmeta` VALUES (276,101,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (277,101,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (278,101,'_sts_service_hero_title','Professionel asbestsanering og nedrivning');
INSERT INTO `wp_postmeta` VALUES (280,101,'_sts_service_benefits','a:6:{i:0;s:39:\"Myndighedsgodkendte saneringsprocedurer\";i:1;s:41:\"Autoriserede fagfolk med miljøuddannelse\";i:2;s:34:\"Fuld dokumentation til myndigheder\";i:3;s:47:\"Sikker bortskaffelse af asbestholdigt materiale\";i:4;s:27:\"Kortlægning og rådgivning\";i:5;s:35:\"Kombination med nedrivning tilbydes\";}');
INSERT INTO `wp_postmeta` VALUES (281,102,'_sts_service_id','1786000000005');
INSERT INTO `wp_postmeta` VALUES (282,102,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (283,102,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (284,102,'_sts_service_hero_title','Professionelt murerarbejde til erhverv og ejendomme');
INSERT INTO `wp_postmeta` VALUES (286,102,'_sts_service_benefits','a:6:{i:0;s:36:\"Reparation og renovering af murværk\";i:1;s:30:\"Flisearbejde og terrassætning\";i:2;s:24:\"Udstøbning og fundering\";i:3;s:35:\"Sokkelreparation og fugtbeskyttelse\";i:4;s:28:\"Fast pris og aftalt tidsplan\";i:5;s:33:\"Samordning med andre håndvækere\";}');
INSERT INTO `wp_postmeta` VALUES (287,103,'_sts_service_id','1786000000006');
INSERT INTO `wp_postmeta` VALUES (288,103,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (289,103,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (290,103,'_sts_service_hero_title','Professionelle malerydelser til virksomheder og ejendomme');
INSERT INTO `wp_postmeta` VALUES (292,103,'_sts_service_benefits','a:6:{i:0;s:28:\"Indvendig og udvendig maling\";i:1;s:23:\"Tapetsering og grunding\";i:2;s:32:\"Overfladebehandling og spartling\";i:3;s:28:\"Maling af facader og vinduer\";i:4;s:32:\"Faste priser og præcis tidsplan\";i:5;s:36:\"Minimal gene for ejendommens brugere\";}');
INSERT INTO `wp_postmeta` VALUES (293,104,'_sts_service_id','1786000000007');
INSERT INTO `wp_postmeta` VALUES (294,104,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (295,104,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (296,104,'_sts_service_hero_title','Professionelt tømrerarbejde og snedkerservice');
INSERT INTO `wp_postmeta` VALUES (298,104,'_sts_service_benefits','a:6:{i:0;s:37:\"Opsætning af lofter, vægge og gulve\";i:1;s:29:\"Montering af døre og vinduer\";i:2;s:33:\"Skræddersyede trækonstruktioner\";i:3;s:29:\"Reparation og vedligeholdelse\";i:4;s:33:\"Samordning med andre håndvækere\";i:5;s:28:\"Præcise tilbud og fast pris\";}');
INSERT INTO `wp_postmeta` VALUES (299,105,'_sts_service_id','1786000000008');
INSERT INTO `wp_postmeta` VALUES (300,105,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (301,105,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (302,105,'_sts_service_hero_title','Professionel gulvbehandling til erhverv og institutioner');
INSERT INTO `wp_postmeta` VALUES (304,105,'_sts_service_benefits','a:6:{i:0;s:36:\"Polering og polish af alle gulvtyper\";i:1;s:33:\"Lakering og oliering af trægulve\";i:2;s:24:\"Grundig maskinrengøring\";i:3;s:28:\"Pletfjerning og restaurering\";i:4;s:30:\"Lavt støjniveau under arbejde\";i:5;s:32:\"Hurtigt tørret – kort nedetid\";}');
INSERT INTO `wp_postmeta` VALUES (305,106,'_sts_service_id','1786000000009');
INSERT INTO `wp_postmeta` VALUES (306,106,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (307,106,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (308,106,'_sts_service_hero_title','Professionel gartnerservice til erhverv og ejendomme');
INSERT INTO `wp_postmeta` VALUES (310,106,'_sts_service_benefits','a:6:{i:0;s:29:\"Græsslåning og hækklipning\";i:1;s:33:\"Beplantning og ukrudtsbekæmpelse\";i:2;s:24:\"Oprydning og bladsamling\";i:3;s:34:\"Snerydning og saltning om vinteren\";i:4;s:34:\"Fast aftale med fleksibel frekvens\";i:5;s:29:\"Rapportering og dokumentation\";}');
INSERT INTO `wp_postmeta` VALUES (311,107,'_sts_service_id','1786000000010');
INSERT INTO `wp_postmeta` VALUES (312,107,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (313,107,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (314,107,'_sts_service_hero_title','Fleksibel mandskabsudlejning til erhverv og byggeprojekter');
INSERT INTO `wp_postmeta` VALUES (316,107,'_sts_service_benefits','a:6:{i:0;s:29:\"Lager, produktion og logistik\";i:1;s:21:\"Rengøring og service\";i:2;s:21:\"Byggepladsassistenter\";i:3;s:36:\"Sezional og projektbaseret bemanding\";i:4;s:35:\"Hurtig mobilisering på kort varsel\";i:5;s:41:\"Ingen ansvarsbyrde – vi er arbejdsgiver\";}');
INSERT INTO `wp_postmeta` VALUES (317,108,'_sts_service_id','1786000000011');
INSERT INTO `wp_postmeta` VALUES (318,108,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (319,108,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (320,108,'_sts_service_hero_title','Professionel vinduespolering til kontorer og erhvervsejendomme');
INSERT INTO `wp_postmeta` VALUES (322,108,'_sts_service_benefits','a:6:{i:0;s:37:\"Indvendig og udvendig vinduespolering\";i:1;s:37:\"Rentvandssystem til udvendig polering\";i:2;s:32:\"Stribefrit og blærfrit resultat\";i:3;s:23:\"Glæsvæg og tagvinduer\";i:4;s:28:\"Fleksibel frekvens og aftale\";i:5;s:34:\"Effektiv og tidsbegrænset indsats\";}');
INSERT INTO `wp_postmeta` VALUES (323,109,'_sts_service_id','1786000000012');
INSERT INTO `wp_postmeta` VALUES (324,109,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (325,109,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (326,109,'_sts_service_hero_title','Grundig rengøring og slutrengøring efter håndværkere');
INSERT INTO `wp_postmeta` VALUES (328,109,'_sts_service_benefits','a:6:{i:0;s:33:\"Fjernelse af byggeaffald og støv\";i:1;s:38:\"Rengøring af alle overflader og gulve\";i:2;s:27:\"Vindues- og rammerengøring\";i:3;s:26:\"Sanitetsrøring og køkken\";i:4;s:51:\"Hurtig mobilisering når håndværkerne er færdige\";i:5;s:32:\"Aflevering klar til ibrugtagning\";}');
INSERT INTO `wp_postmeta` VALUES (329,110,'_sts_service_id','1786000000013');
INSERT INTO `wp_postmeta` VALUES (330,110,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (331,110,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (332,110,'_sts_service_hero_title','Professionel viceværtservice til ejendomme og boligselskaber');
INSERT INTO `wp_postmeta` VALUES (333,110,'_sts_service_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/vicevaertservice.jpg');
INSERT INTO `wp_postmeta` VALUES (334,110,'_sts_service_benefits','a:6:{i:0;s:33:\"Daglige og ugentlige driftsrunder\";i:1;s:35:\"Småreparationer og vedligeholdelse\";i:2;s:32:\"Affaldshåndtering og containere\";i:3;s:22:\"Snerydning og saltning\";i:4;s:30:\"Beboerkontakt og kommunikation\";i:5;s:27:\"Løbende tilstandsrapporter\";}');
INSERT INTO `wp_postmeta` VALUES (335,111,'_sts_service_id','1786000000014');
INSERT INTO `wp_postmeta` VALUES (336,111,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (337,111,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (338,111,'_sts_service_hero_title','INSTA 800 certificeret rengøringskvalitet og inspektion');
INSERT INTO `wp_postmeta` VALUES (340,111,'_sts_service_benefits','a:6:{i:0;s:41:\"INSTA 800-certificeret rengøringsmetodik\";i:1;s:39:\"Objektiv måling af rengøringskvalitet\";i:2;s:38:\"Løbende dokumentation og rapportering\";i:3;s:44:\"Tilpassede kvalitetsniveauer til jeres behov\";i:4;s:37:\"Bruges ved udbudsprocesser og revisor\";i:5;s:26:\"Nordisk anerkendt standard\";}');
INSERT INTO `wp_postmeta` VALUES (341,112,'_sts_service_id','1786000000015');
INSERT INTO `wp_postmeta` VALUES (342,112,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (343,112,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (344,112,'_sts_service_hero_title','Professionel ejendomsservice til bolig- og erhvervsejendomme');
INSERT INTO `wp_postmeta` VALUES (346,112,'_sts_service_benefits','a:6:{i:0;s:40:\"Rengøring af fælledsarealer og trapper\";i:1;s:35:\"Småreparationer og vedligeholdelse\";i:2;s:38:\"Affaldshåndtering og containerservice\";i:3;s:34:\"Snerydning og glatførebekæmpelse\";i:4;s:24:\"Fast ejendomsserviceteam\";i:5;s:25:\"Løbende tilsynsrapporter\";}');
INSERT INTO `wp_postmeta` VALUES (347,113,'_sts_service_id','1786000000018');
INSERT INTO `wp_postmeta` VALUES (348,113,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (349,113,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (350,113,'_sts_service_hero_title','Facademaling');
INSERT INTO `wp_postmeta` VALUES (352,113,'_sts_service_benefits','a:3:{i:0;s:20:\"Udvendig opfriskning\";i:1;s:25:\"Vejrbestandige løsninger\";i:2;s:15:\"Ensartet finish\";}');
INSERT INTO `wp_postmeta` VALUES (353,114,'_sts_service_id','1786000000019');
INSERT INTO `wp_postmeta` VALUES (354,114,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (355,114,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (356,114,'_sts_service_hero_title','Spartelarbejde og filtopsætning');
INSERT INTO `wp_postmeta` VALUES (358,114,'_sts_service_benefits','a:3:{i:0;s:17:\"Jævne overflader\";i:1;s:23:\"Klargøring før maling\";i:2;s:24:\"Hurtig og ren udførelse\";}');
INSERT INTO `wp_postmeta` VALUES (359,115,'_sts_service_id','1786000000020');
INSERT INTO `wp_postmeta` VALUES (360,115,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (361,115,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (362,115,'_sts_service_hero_title','Trappeopgangsmaling');
INSERT INTO `wp_postmeta` VALUES (364,115,'_sts_service_benefits','a:3:{i:0;s:22:\"Slidstærke overflader\";i:1;s:18:\"Pæn fællesopgang\";i:2;s:19:\"Planlagt udførelse\";}');
INSERT INTO `wp_postmeta` VALUES (365,116,'_sts_service_id','1786000000021');
INSERT INTO `wp_postmeta` VALUES (366,116,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (367,116,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (368,116,'_sts_service_hero_title','Epoxy og specialmaling');
INSERT INTO `wp_postmeta` VALUES (370,116,'_sts_service_benefits','a:3:{i:0;s:18:\"Robuste overflader\";i:1;s:17:\"Tekniske miljøer\";i:2;s:19:\"Nem vedligeholdelse\";}');
INSERT INTO `wp_postmeta` VALUES (371,117,'_sts_service_id','1786000000016');
INSERT INTO `wp_postmeta` VALUES (372,117,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (373,117,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (374,117,'_sts_service_hero_title','Professionel snerydning, saltning og glatførebekæmpelse');
INSERT INTO `wp_postmeta` VALUES (376,117,'_sts_service_benefits','a:6:{i:0;s:31:\"DøgnovervAgning af vejrforhold\";i:1;s:40:\"Hurtig udrykning når vejret kræver det\";i:2;s:37:\"Snerydning med maskinel og håndkraft\";i:3;s:34:\"Saltning og grønning af is og rim\";i:4;s:30:\"Dokumenterede servicerapporter\";i:5;s:37:\"Fast årsaftale til forudsigelig pris\";}');
INSERT INTO `wp_postmeta` VALUES (377,118,'_sts_service_id','1786000000017');
INSERT INTO `wp_postmeta` VALUES (378,118,'_sts_service_icon','');
INSERT INTO `wp_postmeta` VALUES (379,118,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (380,118,'_sts_service_hero_title','Sikker og effektiv nedrivning af bygninger og konstruktioner');
INSERT INTO `wp_postmeta` VALUES (381,118,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (382,118,'_sts_service_benefits','a:6:{i:0;s:28:\"Selektiv og total nedrivning\";i:1;s:31:\"Kortlægning af farlige stoffer\";i:2;s:39:\"Miljørigtig sortering og bortskaffelse\";i:3;s:30:\"Myndighedsgodkendte procedurer\";i:4;s:34:\"Fuld dokumentation og rapportering\";i:5;s:28:\"Fast pris på hele projektet\";}');
INSERT INTO `wp_postmeta` VALUES (384,119,'_sts_original_id','1785919182972');
INSERT INTO `wp_postmeta` VALUES (385,119,'_sts_news_image','http://sts-wp.local/supertotalservice.dk/media/uploads/2018/05/byggepladsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (387,120,'_sts_original_id','1785912710002');
INSERT INTO `wp_postmeta` VALUES (388,120,'_sts_news_image','http://sts-wp.local/supertotalservice.dk/media/uploads/2018/05/thinkstockphotos-926208774.jpg');
INSERT INTO `wp_postmeta` VALUES (392,119,'_sts_news_original_id','1785919182972');
INSERT INTO `wp_postmeta` VALUES (394,120,'_sts_news_original_id','1785912710002');
INSERT INTO `wp_postmeta` VALUES (395,116,'_edit_lock','1787228016:1');
INSERT INTO `wp_postmeta` VALUES (399,126,'_thumbnail_id','94');
INSERT INTO `wp_postmeta` VALUES (400,126,'_sts_news_image','http://sts-wp.local/wp-content/uploads/2026/08/screenshot.png');
INSERT INTO `wp_postmeta` VALUES (401,126,'_edit_lock','1787646290:1');
INSERT INTO `wp_postmeta` VALUES (402,119,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (403,119,'_wp_trash_meta_time','1787227819');
INSERT INTO `wp_postmeta` VALUES (404,119,'_wp_desired_post_slug','hjemmeside-redesign');
INSERT INTO `wp_postmeta` VALUES (405,2,'_edit_lock','1787235203:1');
INSERT INTO `wp_postmeta` VALUES (406,118,'_edit_lock','1787228022:1');
INSERT INTO `wp_postmeta` VALUES (407,113,'_edit_lock','1787228049:1');
INSERT INTO `wp_postmeta` VALUES (408,5,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (409,5,'_sts_service_icon','🧪');
INSERT INTO `wp_postmeta` VALUES (410,5,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (411,5,'_sts_service_hero_title','Epoxy og specialmaling');
INSERT INTO `wp_postmeta` VALUES (412,5,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/epoxy-og-specialmaling.jpg');
INSERT INTO `wp_postmeta` VALUES (413,5,'_sts_service_benefits','a:3:{i:0;s:18:\"Robuste overflader\";i:1;s:17:\"Tekniske miljøer\";i:2;s:19:\"Nem vedligeholdelse\";}');
INSERT INTO `wp_postmeta` VALUES (414,5,'_sts_service_description','Specialiserede malerbehandlinger til gulve, tekniske rum og områder med særlige krav til slid og rengøring.');
INSERT INTO `wp_postmeta` VALUES (415,9,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (416,9,'_sts_service_icon','❄️');
INSERT INTO `wp_postmeta` VALUES (417,9,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (418,9,'_sts_service_hero_title','Snerydning og saltning');
INSERT INTO `wp_postmeta` VALUES (419,9,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/glatfoere-bekaempelse-snerydning-og-saltning.jpg');
INSERT INTO `wp_postmeta` VALUES (420,9,'_sts_service_benefits','a:3:{i:0;s:10:\"Vintervagt\";i:1;s:16:\"Hurtig udrykning\";i:2;s:17:\"Sikre adgangsveje\";}');
INSERT INTO `wp_postmeta` VALUES (421,9,'_sts_service_description','Vinterberedskab med snerydning og saltning for sikre adgangsforhold.');
INSERT INTO `wp_postmeta` VALUES (422,18,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (423,18,'_sts_service_icon','🏚️');
INSERT INTO `wp_postmeta` VALUES (424,18,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (425,18,'_sts_service_hero_title','Nedrivningsservice');
INSERT INTO `wp_postmeta` VALUES (426,18,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (427,18,'_sts_service_benefits','a:3:{i:0;s:17:\"Sikker nedrivning\";i:1;s:18:\"Affaldshåndtering\";i:2;s:19:\"Dokumenteret proces\";}');
INSERT INTO `wp_postmeta` VALUES (428,18,'_sts_service_description','Planlagt og sikker nedrivning med respekt for miljøkrav og omkringliggende drift.');
INSERT INTO `wp_postmeta` VALUES (429,19,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (430,19,'_sts_service_icon','🧹');
INSERT INTO `wp_postmeta` VALUES (431,19,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (432,19,'_sts_service_hero_title','Professionel Erhvervsrengøring');
INSERT INTO `wp_postmeta` VALUES (433,19,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/rengoering.jpg');
INSERT INTO `wp_postmeta` VALUES (434,19,'_sts_service_benefits','a:3:{i:0;s:24:\"Dag- og skifterengøring\";i:1;s:18:\"Standardrengøring\";i:2;s:15:\"Dybderengøring\";}');
INSERT INTO `wp_postmeta` VALUES (435,19,'_sts_service_description','Professionel rengøring af kontorer, butikker og industrielle omgivelser med høj kvalitet og fleksibel planlægning.');
INSERT INTO `wp_postmeta` VALUES (436,11,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (437,11,'_sts_service_icon','🔨');
INSERT INTO `wp_postmeta` VALUES (438,11,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (439,11,'_sts_service_hero_title','Håndværkerservice');
INSERT INTO `wp_postmeta` VALUES (440,11,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/haandvaerkere.jpg');
INSERT INTO `wp_postmeta` VALUES (441,11,'_sts_service_benefits','a:3:{i:0;s:12:\"Malerarbejde\";i:1;s:12:\"Murerarbejde\";i:2;s:14:\"Tømrerarbejde\";}');
INSERT INTO `wp_postmeta` VALUES (442,11,'_sts_service_description','Maler-, murer- og tømreropgaver samlet i en fleksibel håndværkerservice til erhverv.');
INSERT INTO `wp_postmeta` VALUES (443,3,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (444,3,'_sts_service_icon','🏗️');
INSERT INTO `wp_postmeta` VALUES (445,3,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (446,3,'_sts_service_hero_title','Byggepladsservice');
INSERT INTO `wp_postmeta` VALUES (447,3,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/byggepladsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (448,3,'_sts_service_benefits','a:3:{i:0;s:21:\"Opstart og etablering\";i:1;s:14:\"Løbende drift\";i:2;s:21:\"Afsluttende oprydning\";}');
INSERT INTO `wp_postmeta` VALUES (449,3,'_sts_service_description','Komplet service til byggepladser med etablering, drift og oprydning i alle faser.');
INSERT INTO `wp_postmeta` VALUES (450,2,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (451,2,'_sts_service_icon','⚠️');
INSERT INTO `wp_postmeta` VALUES (452,2,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (453,2,'_sts_service_hero_title','Asbest og nedrivning');
INSERT INTO `wp_postmeta` VALUES (454,2,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/asbest-og-nedrivning.jpg');
INSERT INTO `wp_postmeta` VALUES (455,2,'_sts_service_benefits','a:3:{i:0;s:13:\"Sikker proces\";i:1;s:19:\"Miljødokumentation\";i:2;s:18:\"Erfarent personale\";}');
INSERT INTO `wp_postmeta` VALUES (456,2,'_sts_service_description','Sikker håndtering af asbest og nedrivningsopgaver med dokumenteret miljøkontrol.');
INSERT INTO `wp_postmeta` VALUES (457,17,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (458,17,'_sts_service_icon','🧱');
INSERT INTO `wp_postmeta` VALUES (459,17,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (460,17,'_sts_service_hero_title','Murerarbejde');
INSERT INTO `wp_postmeta` VALUES (461,17,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/murer.jpg');
INSERT INTO `wp_postmeta` VALUES (462,17,'_sts_service_benefits','a:3:{i:0;s:10:\"Reparation\";i:1;s:9:\"Ombygning\";i:2;s:11:\"Vedligehold\";}');
INSERT INTO `wp_postmeta` VALUES (463,17,'_sts_service_description','Fagligt stærkt murerarbejde til reparation, ombygning og vedligehold af erhvervsejendomme.');
INSERT INTO `wp_postmeta` VALUES (464,15,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (465,15,'_sts_service_icon','🖌️');
INSERT INTO `wp_postmeta` VALUES (466,15,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (467,15,'_sts_service_hero_title','Malertjenester');
INSERT INTO `wp_postmeta` VALUES (468,15,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/maler.jpg');
INSERT INTO `wp_postmeta` VALUES (469,15,'_sts_service_benefits','a:3:{i:0;s:16:\"Indvendig maling\";i:1;s:15:\"Udvendig maling\";i:2;s:20:\"Vedligeholdelsesplan\";}');
INSERT INTO `wp_postmeta` VALUES (470,15,'_sts_service_description','Professionel maling indvendigt og udvendigt med fokus på holdbarhed og finish.');
INSERT INTO `wp_postmeta` VALUES (471,26,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (472,26,'_sts_service_icon','🪵');
INSERT INTO `wp_postmeta` VALUES (473,26,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (474,26,'_sts_service_hero_title','Tømrerarbejde');
INSERT INTO `wp_postmeta` VALUES (475,26,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/toemrer.jpg');
INSERT INTO `wp_postmeta` VALUES (476,26,'_sts_service_benefits','a:3:{i:0;s:10:\"Renovering\";i:1;s:9:\"Montering\";i:2;s:14:\"Specialopgaver\";}');
INSERT INTO `wp_postmeta` VALUES (477,26,'_sts_service_description','Tømreropgaver udført med præcision til kontor, butik, lager og ejendom.');
INSERT INTO `wp_postmeta` VALUES (478,10,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (479,10,'_sts_service_icon','✨');
INSERT INTO `wp_postmeta` VALUES (480,10,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (481,10,'_sts_service_hero_title','Gulvbehandling');
INSERT INTO `wp_postmeta` VALUES (482,10,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/gulvbehandling.jpg');
INSERT INTO `wp_postmeta` VALUES (483,10,'_sts_service_benefits','a:3:{i:0;s:4:\"Rens\";i:1;s:5:\"Pleje\";i:2;s:15:\"Efterbehandling\";}');
INSERT INTO `wp_postmeta` VALUES (484,10,'_sts_service_description','Rens, pleje og behandling af gulve så de holder længere og fremstår præsentable.');
INSERT INTO `wp_postmeta` VALUES (485,8,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (486,8,'_sts_service_icon','🌿');
INSERT INTO `wp_postmeta` VALUES (487,8,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (488,8,'_sts_service_hero_title','Gartnerservice');
INSERT INTO `wp_postmeta` VALUES (489,8,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/gartnerservice.jpg');
INSERT INTO `wp_postmeta` VALUES (490,8,'_sts_service_benefits','a:3:{i:0;s:14:\"Løbende pleje\";i:1;s:13:\"Sæsonarbejde\";i:2;s:9:\"Oprydning\";}');
INSERT INTO `wp_postmeta` VALUES (491,8,'_sts_service_description','Pleje af grønne områder og udendørs arealer året rundt for et velholdt udtryk.');
INSERT INTO `wp_postmeta` VALUES (492,16,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (493,16,'_sts_service_icon','👷');
INSERT INTO `wp_postmeta` VALUES (494,16,'_sts_service_category','byg');
INSERT INTO `wp_postmeta` VALUES (495,16,'_sts_service_hero_title','Mandskabsudlejning');
INSERT INTO `wp_postmeta` VALUES (496,16,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/mandskabsudlejning.jpg');
INSERT INTO `wp_postmeta` VALUES (497,16,'_sts_service_benefits','a:3:{i:0;s:14:\"Hurtig opstart\";i:1;s:18:\"Fleksibel varighed\";i:2;s:20:\"Erfarne medarbejdere\";}');
INSERT INTO `wp_postmeta` VALUES (498,16,'_sts_service_description','Fleksibel bemanding med kvalificerede folk til både korte og længerevarende opgaver.');
INSERT INTO `wp_postmeta` VALUES (499,30,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (500,30,'_sts_service_icon','🪟');
INSERT INTO `wp_postmeta` VALUES (501,30,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (502,30,'_sts_service_hero_title','Vinduespolering');
INSERT INTO `wp_postmeta` VALUES (503,30,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/vinduespolering.jpg');
INSERT INTO `wp_postmeta` VALUES (504,30,'_sts_service_benefits','a:3:{i:0;s:11:\"Fast aftale\";i:1;s:13:\"Høj kvalitet\";i:2;s:25:\"Ind- og udvendig polering\";}');
INSERT INTO `wp_postmeta` VALUES (505,30,'_sts_service_description','Effektiv vinduespolering med rene resultater for kontorer, butikker og større bygninger.');
INSERT INTO `wp_postmeta` VALUES (506,20,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (507,20,'_sts_service_icon','🧽');
INSERT INTO `wp_postmeta` VALUES (508,20,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (509,20,'_sts_service_hero_title','Rengøring efter håndværkere');
INSERT INTO `wp_postmeta` VALUES (510,20,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/rengoering-efter-haandvaerkere.jpg');
INSERT INTO `wp_postmeta` VALUES (511,20,'_sts_service_benefits','a:3:{i:0;s:14:\"Slutrengøring\";i:1;s:14:\"Støvfjernelse\";i:2;s:20:\"Klar til indflytning\";}');
INSERT INTO `wp_postmeta` VALUES (512,20,'_sts_service_description','Slutrengøring efter bygge- og renoveringsarbejde så lokaler hurtigt kan tages i brug.');
INSERT INTO `wp_postmeta` VALUES (513,29,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (514,29,'_sts_service_icon','🏠');
INSERT INTO `wp_postmeta` VALUES (515,29,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (516,29,'_sts_service_hero_title','Viceværtservice');
INSERT INTO `wp_postmeta` VALUES (517,29,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/vicevaertservice.jpg');
INSERT INTO `wp_postmeta` VALUES (518,29,'_sts_service_benefits','a:3:{i:0;s:15:\"Løbende tilsyn\";i:1;s:16:\"Småreparationer\";i:2;s:19:\"Beboer-/brugerfokus\";}');
INSERT INTO `wp_postmeta` VALUES (519,29,'_sts_service_description','Daglig drift og vedligehold af ejendomme med faste rutiner og hurtig opfølgning.');
INSERT INTO `wp_postmeta` VALUES (520,13,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (521,13,'_sts_service_icon','📋');
INSERT INTO `wp_postmeta` VALUES (522,13,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (523,13,'_sts_service_hero_title','INSTA 800');
INSERT INTO `wp_postmeta` VALUES (524,13,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/insta-800-certificeret-kontrol-og-inspektion.jpg');
INSERT INTO `wp_postmeta` VALUES (525,13,'_sts_service_benefits','a:3:{i:0;s:7:\"Kontrol\";i:1;s:13:\"Dokumentation\";i:2;s:16:\"Kvalitetssikring\";}');
INSERT INTO `wp_postmeta` VALUES (526,13,'_sts_service_description','Kontrol og inspektion efter INSTA 800-standarden med tydelig dokumentation.');
INSERT INTO `wp_postmeta` VALUES (527,4,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (528,4,'_sts_service_icon','🏢');
INSERT INTO `wp_postmeta` VALUES (529,4,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (530,4,'_sts_service_hero_title','Ejendomsservice');
INSERT INTO `wp_postmeta` VALUES (531,4,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/ejendomsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (532,4,'_sts_service_benefits','a:3:{i:0;s:15:\"Helhedsløsning\";i:1;s:14:\"Løbende drift\";i:2;s:18:\"Fast kontaktperson\";}');
INSERT INTO `wp_postmeta` VALUES (533,4,'_sts_service_description','Komplet ejendomsservice med fokus på drift, vedligehold og et professionelt helhedsindtryk.');
INSERT INTO `wp_postmeta` VALUES (534,7,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (535,7,'_sts_service_icon','🏢');
INSERT INTO `wp_postmeta` VALUES (536,7,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (537,7,'_sts_service_hero_title','Facademaling');
INSERT INTO `wp_postmeta` VALUES (538,7,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/facademaling.jpg');
INSERT INTO `wp_postmeta` VALUES (539,7,'_sts_service_benefits','a:3:{i:0;s:20:\"Udvendig opfriskning\";i:1;s:25:\"Vejrbestandige løsninger\";i:2;s:15:\"Ensartet finish\";}');
INSERT INTO `wp_postmeta` VALUES (540,7,'_sts_service_description','Udvendig facademaling til erhvervsejendomme, boligforeninger og institutioner med fokus på holdbarhed og helhedsindtryk.');
INSERT INTO `wp_postmeta` VALUES (541,22,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (542,22,'_sts_service_icon','🪜');
INSERT INTO `wp_postmeta` VALUES (543,22,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (544,22,'_sts_service_hero_title','Spartelarbejde og filtopsætning');
INSERT INTO `wp_postmeta` VALUES (545,22,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/spartelarbejde-og-filtopsaetning.jpg');
INSERT INTO `wp_postmeta` VALUES (546,22,'_sts_service_benefits','a:3:{i:0;s:17:\"Jævne overflader\";i:1;s:23:\"Klargøring før maling\";i:2;s:24:\"Hurtig og ren udførelse\";}');
INSERT INTO `wp_postmeta` VALUES (547,22,'_sts_service_description','Klargøring af vægge og lofter med spartelarbejde og filtopsætning før maling i kontorer, opgange og erhvervslokaler.');
INSERT INTO `wp_postmeta` VALUES (548,27,'_sts_is_service','1');
INSERT INTO `wp_postmeta` VALUES (549,27,'_sts_service_icon','🧭');
INSERT INTO `wp_postmeta` VALUES (550,27,'_sts_service_category','mal');
INSERT INTO `wp_postmeta` VALUES (551,27,'_sts_service_hero_title','Trappeopgangsmaling');
INSERT INTO `wp_postmeta` VALUES (552,27,'_sts_service_image','http://sts-wp.local/supertotalservice.dk/media/uploads/stock-images/trappeopgangsmaling.jpg');
INSERT INTO `wp_postmeta` VALUES (553,27,'_sts_service_benefits','a:3:{i:0;s:22:\"Slidstærke overflader\";i:1;s:18:\"Pæn fællesopgang\";i:2;s:19:\"Planlagt udførelse\";}');
INSERT INTO `wp_postmeta` VALUES (554,27,'_sts_service_description','Maling og opfriskning af trappeopgange og fællesarealer med slidstærke produkter og pæn afslutning.');
INSERT INTO `wp_postmeta` VALUES (555,2,'_sts_service_id','101');
INSERT INTO `wp_postmeta` VALUES (556,101,'_sts_service_page_id','2');
INSERT INTO `wp_postmeta` VALUES (557,3,'_sts_service_id','100');
INSERT INTO `wp_postmeta` VALUES (558,100,'_sts_service_page_id','3');
INSERT INTO `wp_postmeta` VALUES (559,4,'_sts_service_id','112');
INSERT INTO `wp_postmeta` VALUES (560,112,'_sts_service_page_id','4');
INSERT INTO `wp_postmeta` VALUES (561,5,'_sts_service_id','116');
INSERT INTO `wp_postmeta` VALUES (562,116,'_sts_service_page_id','5');
INSERT INTO `wp_postmeta` VALUES (563,19,'_sts_service_id','98');
INSERT INTO `wp_postmeta` VALUES (564,98,'_sts_service_page_id','19');
INSERT INTO `wp_postmeta` VALUES (565,7,'_sts_service_id','113');
INSERT INTO `wp_postmeta` VALUES (566,113,'_sts_service_page_id','7');
INSERT INTO `wp_postmeta` VALUES (567,8,'_sts_service_id','106');
INSERT INTO `wp_postmeta` VALUES (568,106,'_sts_service_page_id','8');
INSERT INTO `wp_postmeta` VALUES (569,10,'_sts_service_id','105');
INSERT INTO `wp_postmeta` VALUES (570,105,'_sts_service_page_id','10');
INSERT INTO `wp_postmeta` VALUES (571,11,'_sts_service_id','99');
INSERT INTO `wp_postmeta` VALUES (572,99,'_sts_service_page_id','11');
INSERT INTO `wp_postmeta` VALUES (573,13,'_sts_service_id','111');
INSERT INTO `wp_postmeta` VALUES (574,111,'_sts_service_page_id','13');
INSERT INTO `wp_postmeta` VALUES (575,15,'_sts_service_id','103');
INSERT INTO `wp_postmeta` VALUES (576,103,'_sts_service_page_id','15');
INSERT INTO `wp_postmeta` VALUES (577,16,'_sts_service_id','107');
INSERT INTO `wp_postmeta` VALUES (578,107,'_sts_service_page_id','16');
INSERT INTO `wp_postmeta` VALUES (579,17,'_sts_service_id','102');
INSERT INTO `wp_postmeta` VALUES (580,102,'_sts_service_page_id','17');
INSERT INTO `wp_postmeta` VALUES (581,18,'_sts_service_id','118');
INSERT INTO `wp_postmeta` VALUES (582,118,'_sts_service_page_id','18');
INSERT INTO `wp_postmeta` VALUES (583,20,'_sts_service_id','109');
INSERT INTO `wp_postmeta` VALUES (584,109,'_sts_service_page_id','20');
INSERT INTO `wp_postmeta` VALUES (585,9,'_sts_service_id','117');
INSERT INTO `wp_postmeta` VALUES (586,117,'_sts_service_page_id','9');
INSERT INTO `wp_postmeta` VALUES (587,22,'_sts_service_id','114');
INSERT INTO `wp_postmeta` VALUES (588,114,'_sts_service_page_id','22');
INSERT INTO `wp_postmeta` VALUES (589,26,'_sts_service_id','104');
INSERT INTO `wp_postmeta` VALUES (590,104,'_sts_service_page_id','26');
INSERT INTO `wp_postmeta` VALUES (591,27,'_sts_service_id','115');
INSERT INTO `wp_postmeta` VALUES (592,115,'_sts_service_page_id','27');
INSERT INTO `wp_postmeta` VALUES (593,29,'_sts_service_id','110');
INSERT INTO `wp_postmeta` VALUES (594,110,'_sts_service_page_id','29');
INSERT INTO `wp_postmeta` VALUES (595,30,'_sts_service_id','108');
INSERT INTO `wp_postmeta` VALUES (596,108,'_sts_service_page_id','30');
INSERT INTO `wp_postmeta` VALUES (598,148,'_sts_service_icon','🧼');
INSERT INTO `wp_postmeta` VALUES (599,148,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (600,148,'_sts_service_hero_title','');
INSERT INTO `wp_postmeta` VALUES (601,148,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (602,148,'_sts_service_benefits','a:2:{i:0;s:9:\"Fordel et\";i:1;s:9:\"Fordel to\";}');
INSERT INTO `wp_postmeta` VALUES (603,148,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (604,149,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (605,149,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (606,149,'_sts_service_id','148');
INSERT INTO `wp_postmeta` VALUES (607,148,'_sts_service_page_id','149');
INSERT INTO `wp_postmeta` VALUES (608,149,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (609,149,'_wp_trash_meta_time','1787231244');
INSERT INTO `wp_postmeta` VALUES (610,149,'_wp_desired_post_slug','testservice-skiltevask');
INSERT INTO `wp_postmeta` VALUES (611,148,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (612,148,'_wp_trash_meta_time','1787231244');
INSERT INTO `wp_postmeta` VALUES (613,148,'_wp_desired_post_slug','testservice-skiltevask');
INSERT INTO `wp_postmeta` VALUES (614,7,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (615,30,'_edit_lock','1787232880:1');
INSERT INTO `wp_postmeta` VALUES (617,2,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (618,101,'_sts_service_hero_class','hero-amber');
INSERT INTO `wp_postmeta` VALUES (619,101,'_sts_service_eyebrow','Asbestsanering');
INSERT INTO `wp_postmeta` VALUES (620,101,'_sts_service_hero_text','Asbest kræver specialviden og autoriseret håndtering. STS ApS gennemfører alle saneringer sikkert, dokumenteret og i overensstemmelse med miljøreglerne.');
INSERT INTO `wp_postmeta` VALUES (621,101,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:19:\"Miljøundersøgelse\";s:11:\"description\";s:67:\"Vi kortlægger asbest og farlige stoffer før arbejdet påbegyndes.\";}i:1;a:2:{s:5:\"title\";s:22:\"Afspærring og sikring\";s:11:\"description\";s:64:\"Arbejdsområdet sikres med godkendte afspærringer og lægtning.\";}i:2;a:2:{s:5:\"title\";s:21:\"Kontrolleret sanering\";s:11:\"description\";s:78:\"Asbestmaterialer fjernes efter godkendte procedurer og med beskyttelsesudstyr.\";}i:3;a:2:{s:5:\"title\";s:30:\"Dokumentation og bortskaffelse\";s:11:\"description\";s:65:\"Alt affald bortskaffes miljørigtigt med fuld dokumentationsspor.\";}}}');
INSERT INTO `wp_postmeta` VALUES (622,101,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (623,2,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (624,100,'_sts_service_hero_class','hero-blue');
INSERT INTO `wp_postmeta` VALUES (625,100,'_sts_service_eyebrow','Byggepladsservice');
INSERT INTO `wp_postmeta` VALUES (626,100,'_sts_service_hero_text','En velorganiseret og ren byggeplads er en sikker og produktiv byggeplads. STS leverer alle serviceelementer under et tag – så I kan fokusere på at bygge.');
INSERT INTO `wp_postmeta` VALUES (627,100,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:16:\"Etableringsfasen\";s:11:\"description\";s:78:\"Vi opsætter sanitetsmoduler, containere og niche-løsninger til byggepladsen.\";}i:1;a:2:{s:5:\"title\";s:24:\"Daglig byggepladsservice\";s:11:\"description\";s:70:\"Løbende rengøring og affaldshåndtering under hele projektforløbet.\";}i:2;a:2:{s:5:\"title\";s:14:\"Specialopgaver\";s:11:\"description\";s:72:\"Håndtering af byggeaffald, miljøfarlige materialer og tungere rydning.\";}i:3;a:2:{s:5:\"title\";s:14:\"Slutrengøring\";s:11:\"description\";s:62:\"Grundig slutrengøring så byggeriet er klar til ibrugtagning.\";}}}');
INSERT INTO `wp_postmeta` VALUES (628,100,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (629,3,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (630,112,'_sts_service_hero_class','hero-indigo');
INSERT INTO `wp_postmeta` VALUES (631,112,'_sts_service_eyebrow','Ejendomsservice');
INSERT INTO `wp_postmeta` VALUES (632,112,'_sts_service_hero_text','En velplejede ejendom beværer sin værdi og tiltrækker tilfredse lejere og beboere. STS leverer komplet ejendomsservice med en fast kontaktperson og fleksibel aftalestruktur.');
INSERT INTO `wp_postmeta` VALUES (633,112,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:18:\"Ejendomsgennemgang\";s:11:\"description\";s:67:\"Vi undersøger ejendommen og aftaler en skræddersyet servicepakke.\";}i:1;a:2:{s:5:\"title\";s:24:\"Fast ejendomsserviceteam\";s:11:\"description\";s:59:\"Samme team står for alle løbende opgaver på din ejendom.\";}i:2;a:2:{s:5:\"title\";s:15:\"Løbende tilsyn\";s:11:\"description\";s:73:\"Vi rapporterer regelmæssigt om ejendommens tilstand og eventuelle behov.\";}i:3;a:2:{s:5:\"title\";s:11:\"Akut hjælp\";s:11:\"description\";s:56:\"Hurtig indsats når uventede problemer kræver handling.\";}}}');
INSERT INTO `wp_postmeta` VALUES (634,112,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (635,4,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (636,116,'_sts_service_hero_class','hero-red');
INSERT INTO `wp_postmeta` VALUES (637,116,'_sts_service_eyebrow','Epoxy og specialmaling');
INSERT INTO `wp_postmeta` VALUES (638,116,'_sts_service_hero_text','Specialiserede malerbehandlinger til gulve, tekniske rum og områder med særlige krav til slid og rengøring.');
INSERT INTO `wp_postmeta` VALUES (639,116,'_sts_service_show_about','1');
INSERT INTO `wp_postmeta` VALUES (640,5,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (641,98,'_sts_service_hero_class','hero-teal');
INSERT INTO `wp_postmeta` VALUES (642,98,'_sts_service_eyebrow','Erhvervsrengøring');
INSERT INTO `wp_postmeta` VALUES (643,98,'_sts_service_hero_text','Et rent arbejdsmiljø øger trivslen, produktiviteten og det professionelle udtryk overfor kunder og samarbejdspartnere. STS leverer pålidelig erhvervsrengøring med faste teams og dokumenteret kvalitetssystem.');
INSERT INTO `wp_postmeta` VALUES (644,98,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:16:\"Behovsafdækning\";s:11:\"description\";s:76:\"Vi kortlægger jeres lokaler og udarbejder en skræddersyet rengøringsplan.\";}i:1;a:2:{s:5:\"title\";s:20:\"Fast rengøringsteam\";s:11:\"description\";s:76:\"Samme team besøger jer fast – det skaber tryghed og høj faglig standard.\";}i:2;a:2:{s:5:\"title\";s:25:\"Løbende kvalitetskontrol\";s:11:\"description\";s:69:\"Alle opgaver dokumenteres og kontrolleres efter INSTA 800-principper.\";}i:3;a:2:{s:5:\"title\";s:16:\"Fleksibel aftale\";s:11:\"description\";s:66:\"Vi tilpasser frekvens og omfang til jeres drift, sæson og budget.\";}}}');
INSERT INTO `wp_postmeta` VALUES (645,98,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (646,19,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (647,113,'_sts_service_hero_class','hero-amber');
INSERT INTO `wp_postmeta` VALUES (648,113,'_sts_service_eyebrow','Facademaling');
INSERT INTO `wp_postmeta` VALUES (649,113,'_sts_service_hero_text','Udvendig facademaling til erhvervsejendomme, boligforeninger og institutioner med fokus på holdbarhed og helhedsindtryk.');
INSERT INTO `wp_postmeta` VALUES (650,113,'_sts_service_show_about','1');
INSERT INTO `wp_postmeta` VALUES (651,7,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (652,106,'_sts_service_hero_class','hero-green');
INSERT INTO `wp_postmeta` VALUES (653,106,'_sts_service_eyebrow','Gartnerservice');
INSERT INTO `wp_postmeta` VALUES (654,106,'_sts_service_hero_text','Grønne og velholdte udearealer styrker virksomhedens udtryk og skaber et behageligt miljø for medarbejdere og gæster. STS gartnere holder din ejendom præsentabel gennem alle årstider.');
INSERT INTO `wp_postmeta` VALUES (655,106,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:19:\"Sezionel gennemgang\";s:11:\"description\";s:79:\"Vi aftaler årets vedligeholdelsesplan og tilpasser den til ejendommens behøv.\";}i:1;a:2:{s:5:\"title\";s:20:\"Regelmæssige besøg\";s:11:\"description\";s:58:\"Fast kadence med græsslåning, hækklipning og oprydning.\";}i:2;a:2:{s:5:\"title\";s:26:\"Sprøjtning og beplantning\";s:11:\"description\";s:62:\"Ukrudtsbekæmpelse og ny beplantning når sorten tilsiger det.\";}i:3;a:2:{s:5:\"title\";s:13:\"Vinterservice\";s:11:\"description\";s:63:\"Snerydning og glatførebekæmpelse i månederne med vintervær.\";}}}');
INSERT INTO `wp_postmeta` VALUES (656,106,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (657,8,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (658,105,'_sts_service_hero_class','hero-blue');
INSERT INTO `wp_postmeta` VALUES (659,105,'_sts_service_eyebrow','Gulvbehandling');
INSERT INTO `wp_postmeta` VALUES (660,105,'_sts_service_hero_text','Et velholdt gulv signalerer professionalisme og forlænger gulvets levetid markant. STS udfører gulvbehandling på alle typer overflader med minimal driftsforstyrrelser.');
INSERT INTO `wp_postmeta` VALUES (661,105,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:13:\"Gulvvurdering\";s:11:\"description\";s:71:\"Vi vurderer gulvets tilstand og anbefaler den bedste behandlingsmetode.\";}i:1;a:2:{s:5:\"title\";s:12:\"Forberedelse\";s:11:\"description\";s:55:\"Gulvet rengøres grundigt og forberedes til behandling.\";}i:2;a:2:{s:5:\"title\";s:20:\"Behandling og polish\";s:11:\"description\";s:57:\"Polering, lakering eller oliering udføres professionelt.\";}i:3;a:2:{s:5:\"title\";s:25:\"Aflevering og rådgivning\";s:11:\"description\";s:59:\"Vi guider jer i fremtidig pleje så gulvet holder længere.\";}}}');
INSERT INTO `wp_postmeta` VALUES (662,105,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (663,10,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (664,99,'_sts_service_hero_class','hero-navy');
INSERT INTO `wp_postmeta` VALUES (665,99,'_sts_service_eyebrow','Håndværkere');
INSERT INTO `wp_postmeta` VALUES (666,99,'_sts_service_hero_text','Når der opstår behov for hurtig og kompetent håndvækerservice, er STS klar. Vi mobiliserer de rette fagfolk hurtigt og sørger for en præcis og effektiv løsning.');
INSERT INTO `wp_postmeta` VALUES (667,99,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:29:\"Forespørgsel og besigtigelse\";s:11:\"description\";s:56:\"Vi afklarer opgaven hurtigt og giver et præcist tilbud.\";}i:1;a:2:{s:5:\"title\";s:15:\"Valg af fagfolk\";s:11:\"description\";s:64:\"Vi matcher opgaven med de rette håndvækere fra vores netværk.\";}i:2;a:2:{s:5:\"title\";s:10:\"Udførelse\";s:11:\"description\";s:67:\"Arbejdet udføres professionelt, til aftalt pris og til aftalt tid.\";}i:3;a:2:{s:5:\"title\";s:24:\"Færdigt og dokumenteret\";s:11:\"description\";s:56:\"Vi afleverer med dokumentation og gennemgang ved ønske.\";}}}');
INSERT INTO `wp_postmeta` VALUES (668,99,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (669,11,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (670,111,'_sts_service_hero_class','hero-slate');
INSERT INTO `wp_postmeta` VALUES (671,111,'_sts_service_eyebrow','INSTA 800 Certificering');
INSERT INTO `wp_postmeta` VALUES (672,111,'_sts_service_hero_text','INSTA 800 er den nordiske standard for måling og dokumentation af rengøringskvalitet. STS ApS er certificeret og anvender standarden til at sikre og bevise høj kvalitet i alt rengøringsarbejde.');
INSERT INTO `wp_postmeta` VALUES (673,111,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:16:\"Behovsafdækning\";s:11:\"description\";s:71:\"Vi kortlægger jeres rengøringsbehov og fastlægger kvalitetsniveauer.\";}i:1;a:2:{s:5:\"title\";s:15:\"Rengøringsplan\";s:11:\"description\";s:56:\"En detaljeret plan med frekvenser og metoder udarbejdes.\";}i:2;a:2:{s:5:\"title\";s:21:\"Udførelse og måling\";s:11:\"description\";s:67:\"Rengøring udføres og måles objektivt efter INSTA 800-standarden.\";}i:3;a:2:{s:5:\"title\";s:13:\"Dokumentation\";s:11:\"description\";s:54:\"I modtager løbende rapporter med målte kvalitetstal.\";}}}');
INSERT INTO `wp_postmeta` VALUES (674,111,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (675,13,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (676,103,'_sts_service_hero_class','hero-slate');
INSERT INTO `wp_postmeta` VALUES (677,103,'_sts_service_eyebrow','Malerarbejde');
INSERT INTO `wp_postmeta` VALUES (678,103,'_sts_service_hero_text','Et nyt malingsstrøg kan transformere et rum, forny et udtryk og beskytte bygningens overflader. STS malere leverer præcist og velordnet arbejde med respekt for jeres dagligdag.');
INSERT INTO `wp_postmeta` VALUES (679,103,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:22:\"Besigtigelse og tilbud\";s:11:\"description\";s:53:\"Vi besigter opgaven og giver et fast præcist tilbud.\";}i:1;a:2:{s:5:\"title\";s:25:\"Klargøring og afdækning\";s:11:\"description\";s:47:\"Alle overflader afdækkes og rummet klargøres.\";}i:2;a:2:{s:5:\"title\";s:16:\"Maling og finish\";s:11:\"description\";s:47:\"Vi påfører maling i aftalt farve og kvalitet.\";}i:3;a:2:{s:5:\"title\";s:23:\"Oprydning og aflevering\";s:11:\"description\";s:55:\"Vi rydder op og afleverer et rent og færdigt resultat.\";}}}');
INSERT INTO `wp_postmeta` VALUES (680,103,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (681,15,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (682,107,'_sts_service_hero_class','hero-navy');
INSERT INTO `wp_postmeta` VALUES (683,107,'_sts_service_eyebrow','Mandskabsudlejning');
INSERT INTO `wp_postmeta` VALUES (684,107,'_sts_service_hero_text','Har I brug for ekstra hænder hurtigt? STS stiller erfaret og pålidelig arbejdskraft til rådighed – til projekter, sygefravær, sezionale behov og meget mere.');
INSERT INTO `wp_postmeta` VALUES (685,107,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:16:\"Behovsafdækning\";s:11:\"description\";s:59:\"Vi afklarer hvad I har brug for og når I har brug for det.\";}i:1;a:2:{s:5:\"title\";s:29:\"Match med egnede medarbejdere\";s:11:\"description\";s:67:\"Vi finder de rette profiler fra vores stab af erfarne medarbejdere.\";}i:2;a:2:{s:5:\"title\";s:19:\"Hurtig mobilisering\";s:11:\"description\";s:62:\"Medarbejdere kan som regel være på plads inden for 24 timer.\";}i:3;a:2:{s:5:\"title\";s:20:\"Løbende opfølgning\";s:11:\"description\";s:58:\"Vi sikrer at bemandingen lever op til jeres forventninger.\";}}}');
INSERT INTO `wp_postmeta` VALUES (686,107,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (687,16,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (688,102,'_sts_service_hero_class','hero-navy');
INSERT INTO `wp_postmeta` VALUES (689,102,'_sts_service_eyebrow','Murerarbejde');
INSERT INTO `wp_postmeta` VALUES (690,102,'_sts_service_hero_text','Fra små reparationer til større renoveringsprojekter – STS murere leverer solide og holdbare løsninger. Vi samarbejder tæt med bygherre og andre håndvækere for et problemfrit forløb.');
INSERT INTO `wp_postmeta` VALUES (691,102,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:19:\"Opmåling og tilbud\";s:11:\"description\";s:68:\"Vi opmåler og udarbejder et præcist tilbud med aftalte materialer.\";}i:1;a:2:{s:5:\"title\";s:12:\"Forberedelse\";s:11:\"description\";s:58:\"Arbejdsområdet afdækkes og forberedes for minimal gener.\";}i:2;a:2:{s:5:\"title\";s:10:\"Udførelse\";s:11:\"description\";s:67:\"Murerarbejde udføres fagmæssigt korrekt og til aftalte deadlines.\";}i:3;a:2:{s:5:\"title\";s:20:\"Kvalitetsgodkendelse\";s:11:\"description\";s:50:\"Vi gennemgår arbejdet med kunden før aflevering.\";}}}');
INSERT INTO `wp_postmeta` VALUES (692,102,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (693,17,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (694,118,'_sts_service_hero_class','hero-red');
INSERT INTO `wp_postmeta` VALUES (695,118,'_sts_service_eyebrow','Nedrivningsservice');
INSERT INTO `wp_postmeta` VALUES (696,118,'_sts_service_hero_text','Nedrivning kræver erfaring, planlægning og håndtering af miljøfarlige materialer. STS leverer kompetent nedrivningsservice med dokumenteret miljøhåndtering og klar tidsplan.');
INSERT INTO `wp_postmeta` VALUES (697,118,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:35:\"Kortlægning og miljøundersøgelse\";s:11:\"description\";s:73:\"Vi kortlægger bygningen for farlige stoffer før nedrivning påbegyndes.\";}i:1;a:2:{s:5:\"title\";s:21:\"Myndighedsgodkendelse\";s:11:\"description\";s:55:\"Vi sørger for nødvendige tilladelser og godkendelser.\";}i:2;a:2:{s:5:\"title\";s:23:\"Kontrolleret nedrivning\";s:11:\"description\";s:69:\"Nedrivning udføres sikkert med fokus på genbrugssortring og miljø.\";}i:3;a:2:{s:5:\"title\";s:30:\"Bortskaffelse og dokumentation\";s:11:\"description\";s:63:\"Alt materiale bortskaffes miljørigtigt med fuld dokumentation.\";}}}');
INSERT INTO `wp_postmeta` VALUES (698,118,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (699,18,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (700,109,'_sts_service_hero_class','hero-teal');
INSERT INTO `wp_postmeta` VALUES (701,109,'_sts_service_eyebrow','Rengøring efter håndværkere');
INSERT INTO `wp_postmeta` VALUES (702,109,'_sts_service_hero_text','Når håndværkerne er færdige, er det tid til en grundig oprydning. STS leverer hurtig og grøndig slutrengøring så lokaler, butikker og kontorer er klar til brug hurtigst muligt.');
INSERT INTO `wp_postmeta` VALUES (703,109,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:18:\"Aftale og tidsplan\";s:11:\"description\";s:65:\"Vi aftaler en tidsplan der passer til håndværkernes afslutning.\";}i:1;a:2:{s:5:\"title\";s:15:\"Grov rengøring\";s:11:\"description\";s:48:\"Byggeaffald, støv og snavs fjernes systematisk.\";}i:2;a:2:{s:5:\"title\";s:22:\"Grundig slutrengøring\";s:11:\"description\";s:53:\"Alle overflader, gulve og vinduer rengøres grundigt.\";}i:3;a:2:{s:5:\"title\";s:13:\"Klar til brug\";s:11:\"description\";s:53:\"Vi afleverer lokalerne rene og klar til ibrugtagning.\";}}}');
INSERT INTO `wp_postmeta` VALUES (704,109,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (705,20,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (706,117,'_sts_service_hero_class','hero-slate');
INSERT INTO `wp_postmeta` VALUES (707,117,'_sts_service_eyebrow','Snerydning og saltning');
INSERT INTO `wp_postmeta` VALUES (708,117,'_sts_service_hero_text','Glatte veje og stier er en sikkerheds- og ansvarsmæssig udfordring. STS sørger for, at jeres ejendom er sikker og fremkommelig, også i de mest krævende vintersituationer.');
INSERT INTO `wp_postmeta` VALUES (709,117,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:14:\"Beredskabsplan\";s:11:\"description\";s:73:\"Vi udarbejder en vinterserviceplan så al logistik er klar før vinteren.\";}i:1;a:2:{s:5:\"title\";s:23:\"OvervAgning af vejrdata\";s:11:\"description\";s:57:\"Vi følger vejrvarsler og aktiverer snerydning proaktivt.\";}i:2;a:2:{s:5:\"title\";s:22:\"Snerydning og saltning\";s:11:\"description\";s:68:\"Hurtig og grundig snerydning samt saltning af alle vejrådne flader.\";}i:3;a:2:{s:5:\"title\";s:13:\"Dokumentation\";s:11:\"description\";s:65:\"Vi dokumenterer alle indsatser som kravævende ift. ansvarsloven.\";}}}');
INSERT INTO `wp_postmeta` VALUES (710,117,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (711,9,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (712,114,'_sts_service_hero_class','hero-indigo');
INSERT INTO `wp_postmeta` VALUES (713,114,'_sts_service_eyebrow','Spartelarbejde og filtopsætning');
INSERT INTO `wp_postmeta` VALUES (714,114,'_sts_service_hero_text','Klargøring af vægge og lofter med spartelarbejde og filtopsætning før maling i kontorer, opgange og erhvervslokaler.');
INSERT INTO `wp_postmeta` VALUES (715,114,'_sts_service_show_about','1');
INSERT INTO `wp_postmeta` VALUES (716,22,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (717,104,'_sts_service_hero_class','hero-blue');
INSERT INTO `wp_postmeta` VALUES (718,104,'_sts_service_eyebrow','Tømrerarbejde');
INSERT INTO `wp_postmeta` VALUES (719,104,'_sts_service_hero_text','Tømrer- og snedkerarbejde kræver håndvæksmæssig præcision og erfaring. STS tømrere leverer solide trækonstruktioner, installationer og reparationer med respekt for æstetik og funktion.');
INSERT INTO `wp_postmeta` VALUES (720,104,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:20:\"Gennemgang og tilbud\";s:11:\"description\";s:49:\"Vi gennemgår opgaven og giver et konkret tilbud.\";}i:1;a:2:{s:5:\"title\";s:14:\"Materialevælg\";s:11:\"description\";s:66:\"Vi rådgiver om materialer og udførelse i h.t. ønsker og budget.\";}i:2;a:2:{s:5:\"title\";s:10:\"Udførelse\";s:11:\"description\";s:57:\"Tømrerarbejdet udføres præcist og til aftalt tidsplan.\";}i:3;a:2:{s:5:\"title\";s:16:\"Kvalitetskontrol\";s:11:\"description\";s:64:\"Vi gennemgår alt arbejde og sikrer at det lever op til aftalen.\";}}}');
INSERT INTO `wp_postmeta` VALUES (721,104,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (722,26,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (723,115,'_sts_service_hero_class','hero-slate');
INSERT INTO `wp_postmeta` VALUES (724,115,'_sts_service_eyebrow','Trappeopgangsmaling');
INSERT INTO `wp_postmeta` VALUES (725,115,'_sts_service_hero_text','Maling og opfriskning af trappeopgange og fællesarealer med slidstærke produkter og pæn afslutning.');
INSERT INTO `wp_postmeta` VALUES (726,115,'_sts_service_show_about','1');
INSERT INTO `wp_postmeta` VALUES (727,27,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (728,110,'_sts_service_hero_class','hero-indigo');
INSERT INTO `wp_postmeta` VALUES (729,110,'_sts_service_eyebrow','Viceværtservice');
INSERT INTO `wp_postmeta` VALUES (730,110,'_sts_service_hero_text','En dygtig vicevært er fundamentet for en velfungerende ejendom. STS leverer professionel viceværtservice der sikrer ejendommens daglige drift, trivsel og vedligeholdelse.');
INSERT INTO `wp_postmeta` VALUES (731,110,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:12:\"Opstartmøde\";s:11:\"description\";s:62:\"Vi afklarer ejendommens behov og aftaler en fast servicepakke.\";}i:1;a:2:{s:5:\"title\";s:20:\"Regelmæssige rundes\";s:11:\"description\";s:49:\"Fast besøg med tilsyn, oprydning og småopgaver.\";}i:2;a:2:{s:5:\"title\";s:13:\"Beboerkontakt\";s:11:\"description\";s:67:\"Vi er jeres beboeres kontaktpunkt for driftsrelaterede spørgsmål.\";}i:3;a:2:{s:5:\"title\";s:12:\"Rapportering\";s:11:\"description\";s:70:\"Vi rapporterer løbende om ejendommens tilstand og eventuelle mangler.\";}}}');
INSERT INTO `wp_postmeta` VALUES (732,110,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (733,29,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (734,108,'_sts_service_hero_class','hero-teal');
INSERT INTO `wp_postmeta` VALUES (735,108,'_sts_service_eyebrow','Vinduespolering');
INSERT INTO `wp_postmeta` VALUES (736,108,'_sts_service_hero_text','Rene vinduer gør et professionelt og velholdt udtryk – både indefra og udvendig. STS vinduespudsere arbejder effektivt med godkendte metoder og efterlader et stribefrit resultat.');
INSERT INTO `wp_postmeta` VALUES (737,108,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:18:\"Sådan arbejder vi\";s:5:\"title\";s:62:\"En struktureret proces fra første kontakt til færdig opgave.\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:22:\"Inventering af vinduer\";s:11:\"description\";s:66:\"Vi kortlægger alle vinduer og aftaler frekvens og adgangsforhold.\";}i:1;a:2:{s:5:\"title\";s:15:\"Rentvandssystem\";s:11:\"description\";s:64:\"Udvendig polering med rentvandssystem for et blærfrit resultat.\";}i:2;a:2:{s:5:\"title\";s:25:\"Manuel indvendig polering\";s:11:\"description\";s:56:\"Indvendig polering med professionelle midler og skraber.\";}i:3;a:2:{s:5:\"title\";s:11:\"Fast aftale\";s:11:\"description\";s:73:\"Vi etablerer en løbende aftale tilpasset jeres behov og æstetiske krav.\";}}}');
INSERT INTO `wp_postmeta` VALUES (738,108,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (739,30,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (740,151,'_sts_service_icon','🧼');
INSERT INTO `wp_postmeta` VALUES (741,151,'_sts_service_category','ren');
INSERT INTO `wp_postmeta` VALUES (742,151,'_sts_service_eyebrow','');
INSERT INTO `wp_postmeta` VALUES (743,151,'_sts_service_hero_title','');
INSERT INTO `wp_postmeta` VALUES (744,151,'_sts_service_hero_text','');
INSERT INTO `wp_postmeta` VALUES (745,151,'_sts_service_hero_class','');
INSERT INTO `wp_postmeta` VALUES (746,151,'_sts_service_show_about','0');
INSERT INTO `wp_postmeta` VALUES (747,151,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (748,151,'_sts_service_benefits','a:0:{}');
INSERT INTO `wp_postmeta` VALUES (749,151,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:0:\"\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (750,152,'_sts_service_template','1');
INSERT INTO `wp_postmeta` VALUES (751,152,'_wp_page_template','sts-service-page');
INSERT INTO `wp_postmeta` VALUES (752,152,'_sts_service_id','151');
INSERT INTO `wp_postmeta` VALUES (753,151,'_sts_service_page_id','152');
INSERT INTO `wp_postmeta` VALUES (754,152,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (755,152,'_wp_trash_meta_time','1787233976');
INSERT INTO `wp_postmeta` VALUES (756,152,'_wp_desired_post_slug','testservice-skiltevask');
INSERT INTO `wp_postmeta` VALUES (757,151,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (758,151,'_wp_trash_meta_time','1787233976');
INSERT INTO `wp_postmeta` VALUES (759,151,'_wp_desired_post_slug','testservice-skiltevask');
INSERT INTO `wp_postmeta` VALUES (761,15,'_edit_lock','1787235331:1');
INSERT INTO `wp_postmeta` VALUES (762,19,'_edit_lock','1787297347:1');
INSERT INTO `wp_postmeta` VALUES (763,101,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (764,100,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (765,112,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (766,116,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (767,116,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:0:\"\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (768,98,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (769,113,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (770,113,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:0:\"\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (771,106,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (772,105,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (773,99,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (774,111,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (775,103,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (776,107,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (777,102,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (778,109,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (779,117,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (780,114,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (781,114,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:0:\"\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (782,104,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (783,115,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (784,115,'_sts_service_process','a:3:{s:7:\"eyebrow\";s:0:\"\";s:5:\"title\";s:0:\"\";s:5:\"steps\";a:4:{i:0;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:1;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:2;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}i:3;a:2:{s:5:\"title\";s:0:\"\";s:11:\"description\";s:0:\"\";}}}');
INSERT INTO `wp_postmeta` VALUES (785,108,'_sts_service_image','');
INSERT INTO `wp_postmeta` VALUES (787,90,'_wp_attachment_image_alt','Professionel vinduespolering af ruder med indvasker og skraber');
INSERT INTO `wp_postmeta` VALUES (788,89,'_wp_attachment_image_alt','Vicevært udfører kontrol og vedligeholdelse af ejendom');
INSERT INTO `wp_postmeta` VALUES (789,87,'_wp_attachment_image_alt','Tømrer skærer træ med rundsav til byggeprojekt');
INSERT INTO `wp_postmeta` VALUES (790,88,'_wp_attachment_image_alt','Nymalet trappeopgang i etageejendom med flot trætrappe');
INSERT INTO `wp_postmeta` VALUES (791,82,'_wp_attachment_image_alt','Maler udfører spartelarbejde på væg før opsætning af glasfilt');
INSERT INTO `wp_postmeta` VALUES (792,83,'_wp_attachment_image_alt','Håndværker sliber trætrappe med rystepudser under specialopgave');
INSERT INTO `wp_postmeta` VALUES (793,78,'_wp_attachment_image_alt','Professionelt rengøringsudstyr og støvsuger klar til rengøring');
INSERT INTO `wp_postmeta` VALUES (794,77,'_wp_attachment_image_alt','Industrielt rengøringsudstyr til rengøring efter håndværkere på byggeplads');
INSERT INTO `wp_postmeta` VALUES (795,76,'_wp_attachment_image_alt','Professionel nedrivningsservice og kontrolleret nedbrydning af bygning');
INSERT INTO `wp_postmeta` VALUES (796,76,'_wp_old_slug','nedrivningsservice-2');
INSERT INTO `wp_postmeta` VALUES (797,75,'_wp_attachment_image_alt','Professionel nedrivningsservice og kontrolleret nedbrydning af bygning');
INSERT INTO `wp_postmeta` VALUES (798,73,'_wp_attachment_image_alt','Faglært murer udfører renovering og murerværk');
INSERT INTO `wp_postmeta` VALUES (799,73,'_wp_old_slug','murer-2');
INSERT INTO `wp_postmeta` VALUES (800,74,'_wp_attachment_image_alt','Faglært murer udfører renovering og murerværk');
INSERT INTO `wp_postmeta` VALUES (801,67,'_wp_attachment_image_alt','Maler udfører indvendigt malerarbejde på vægge og lofter');
INSERT INTO `wp_postmeta` VALUES (802,68,'_wp_attachment_image_alt','Udlejning af faglært mandskab og håndværkere til byggebranchen');
INSERT INTO `wp_postmeta` VALUES (803,61,'_wp_attachment_image_alt','Erfarne håndværkere samarbejder om renoveringsprojekt');
INSERT INTO `wp_postmeta` VALUES (804,62,'_wp_attachment_image_alt','INSTA 800 certificeret kontrol og inspektion af rengøringskvalitet');
INSERT INTO `wp_postmeta` VALUES (805,59,'_wp_attachment_image_alt','Professionel gulvbehandling og slibning af massive trægulve');
INSERT INTO `wp_postmeta` VALUES (806,60,'_wp_attachment_image_alt','Professionel gulvbehandling og slibning af massive trægulve');
INSERT INTO `wp_postmeta` VALUES (807,55,'_wp_attachment_image_alt','Professionel snerydning og saltning mod glatføre om vinteren');
INSERT INTO `wp_postmeta` VALUES (808,53,'_wp_attachment_image_alt','Facademaling af etageejendom mod vind og vejr');
INSERT INTO `wp_postmeta` VALUES (809,54,'_wp_attachment_image_alt','Gartnerservice udfører pasning og vedligeholdelse af grønne områder');
INSERT INTO `wp_postmeta` VALUES (810,52,'_wp_attachment_image_alt','Slidstærk epoxy og specialmaling påført industrigulv');
INSERT INTO `wp_postmeta` VALUES (811,51,'_wp_attachment_image_alt','Ejendomsservice med vedligeholdelse af bygninger og udendørsarealer');
INSERT INTO `wp_postmeta` VALUES (812,49,'_wp_attachment_image_alt','Byggepladsservice med oprydning og logistik på byggepladsen');
INSERT INTO `wp_postmeta` VALUES (813,50,'_wp_attachment_image_alt','Byggepladsservice med oprydning og logistik på byggepladsen');
INSERT INTO `wp_postmeta` VALUES (814,48,'_wp_attachment_image_alt','Certificeret asbestsanering og sikker nedrivning af sundhedsskadelige materialer');
INSERT INTO `wp_postmeta` VALUES (815,156,'_sts_project_template','1');
INSERT INTO `wp_postmeta` VALUES (816,156,'_wp_page_template','sts-projects-archive');
INSERT INTO `wp_postmeta` VALUES (817,157,'_sts_project_category','mal');
INSERT INTO `wp_postmeta` VALUES (818,157,'_sts_project_location','København Ø');
INSERT INTO `wp_postmeta` VALUES (819,157,'_sts_project_client','Ejendomsselskabet Østerbro A/S');
INSERT INTO `wp_postmeta` VALUES (820,157,'_sts_project_scope','1.400 m² facade fordelt på 5 etager');
INSERT INTO `wp_postmeta` VALUES (821,157,'_sts_project_duration','Udført på 12 arbejdsdage');
INSERT INTO `wp_postmeta` VALUES (822,157,'_sts_project_completed','Maj 2026');
INSERT INTO `wp_postmeta` VALUES (823,157,'_sts_project_services','a:5:{i:0;s:27:\"Højtryksrensning af facade\";i:1;s:30:\"Reparation af revner og skader\";i:2;s:21:\"Omfugning af murværk\";i:3;s:33:\"Grunding og to gange facademaling\";i:4;s:23:\"Afdækning og oprydning\";}');
INSERT INTO `wp_postmeta` VALUES (824,157,'_sts_project_materials','a:3:{i:0;s:38:\"Silikoneharpiksmaling, diffusionsåben\";i:1;s:23:\"Mineralsk facadegrunder\";i:2;s:31:\"Miljømærket afrensningsmiddel\";}');
INSERT INTO `wp_postmeta` VALUES (825,157,'_sts_project_before_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/facademaling.jpg');
INSERT INTO `wp_postmeta` VALUES (826,157,'_sts_project_after_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/trappeopgangsmaling.jpg');
INSERT INTO `wp_postmeta` VALUES (827,157,'_sts_project_gallery','a:4:{i:0;s:94:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/facademaling.jpg\";i:1;s:101:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/trappeopgangsmaling.jpg\";i:2;s:87:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/maler.jpg\";i:3;s:114:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/spartelarbejde-og-filtopsaetning.jpg\";}');
INSERT INTO `wp_postmeta` VALUES (828,157,'_sts_project_featured','1');
INSERT INTO `wp_postmeta` VALUES (829,158,'_sts_project_category','ren');
INSERT INTO `wp_postmeta` VALUES (830,158,'_sts_project_location','Aarhus C');
INSERT INTO `wp_postmeta` VALUES (831,158,'_sts_project_client','');
INSERT INTO `wp_postmeta` VALUES (832,158,'_sts_project_scope','860 m² kontorlokaler over 3 etager');
INSERT INTO `wp_postmeta` VALUES (833,158,'_sts_project_duration','Udført på 3 dage');
INSERT INTO `wp_postmeta` VALUES (834,158,'_sts_project_completed','Marts 2026');
INSERT INTO `wp_postmeta` VALUES (835,158,'_sts_project_services','a:5:{i:0;s:33:\"Fjernelse af byggestøv og affald\";i:1;s:33:\"Afrensning af vinduer inde og ude\";i:2;s:31:\"Rengøring af ventilationsriste\";i:3;s:20:\"Gulvvask og polering\";i:4;s:24:\"Sanitær hovedrengøring\";}');
INSERT INTO `wp_postmeta` VALUES (836,158,'_sts_project_materials','a:2:{i:0;s:31:\"Svanemærkede rengøringsmidler\";i:1;s:35:\"Mikrofibersystem uden kemi til glas\";}');
INSERT INTO `wp_postmeta` VALUES (837,158,'_sts_project_before_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/rengoering-efter-haandvaerkere.jpg');
INSERT INTO `wp_postmeta` VALUES (838,158,'_sts_project_after_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/rengoering.jpg');
INSERT INTO `wp_postmeta` VALUES (839,158,'_sts_project_gallery','a:4:{i:0;s:112:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/rengoering-efter-haandvaerkere.jpg\";i:1;s:92:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/rengoering.jpg\";i:2;s:97:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/vinduespolering.jpg\";i:3;s:96:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/gulvbehandling.jpg\";}');
INSERT INTO `wp_postmeta` VALUES (840,158,'_sts_project_featured','0');
INSERT INTO `wp_postmeta` VALUES (841,159,'_sts_project_category','byg');
INSERT INTO `wp_postmeta` VALUES (842,159,'_sts_project_location','Odense SØ');
INSERT INTO `wp_postmeta` VALUES (843,159,'_sts_project_client','');
INSERT INTO `wp_postmeta` VALUES (844,159,'_sts_project_scope','Lagerhal på 2.100 m² med eternittag');
INSERT INTO `wp_postmeta` VALUES (845,159,'_sts_project_duration','Udført på 6 uger');
INSERT INTO `wp_postmeta` VALUES (846,159,'_sts_project_completed','Januar 2026');
INSERT INTO `wp_postmeta` VALUES (847,159,'_sts_project_services','a:5:{i:0;s:39:\"Kortlægning og prøvetagning af asbest\";i:1;s:43:\"Etablering af sanerings- og sikkerhedszoner\";i:2;s:25:\"Demontering af eternittag\";i:3;s:39:\"Kontrolleret nedrivning af bygningskrop\";i:4;s:35:\"Sortering og godkendt bortskaffelse\";}');
INSERT INTO `wp_postmeta` VALUES (848,159,'_sts_project_materials','a:2:{i:0;s:37:\"Godkendt asbestemballage og mærkning\";i:1;s:29:\"Støvbindende sprinkleranlæg\";}');
INSERT INTO `wp_postmeta` VALUES (849,159,'_sts_project_before_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/asbest-og-nedrivning.jpg');
INSERT INTO `wp_postmeta` VALUES (850,159,'_sts_project_after_image','http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/nedrivningsservice.jpg');
INSERT INTO `wp_postmeta` VALUES (851,159,'_sts_project_gallery','a:4:{i:0;s:102:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/asbest-og-nedrivning.jpg\";i:1;s:100:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/nedrivningsservice.jpg\";i:2;s:99:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/byggepladsservice.jpg\";i:3;s:95:\"http://sts-wp.local/wp-content/themes/supertotalservice-dk-main/assets/images/haandvaerkere.jpg\";}');
INSERT INTO `wp_postmeta` VALUES (852,159,'_sts_project_featured','0');
INSERT INTO `wp_postmeta` VALUES (853,160,'_menu_item_type','custom');
INSERT INTO `wp_postmeta` VALUES (854,160,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (855,160,'_menu_item_object_id','160');
INSERT INTO `wp_postmeta` VALUES (856,160,'_menu_item_object','custom');
INSERT INTO `wp_postmeta` VALUES (857,160,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (858,160,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (859,160,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (860,160,'_menu_item_url','http://sts-wp.local/');
INSERT INTO `wp_postmeta` VALUES (861,160,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (862,161,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (863,161,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (864,161,'_menu_item_object_id','21');
INSERT INTO `wp_postmeta` VALUES (865,161,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (866,161,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (867,161,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (868,161,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (869,161,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (870,161,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (871,162,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (872,162,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (873,162,'_menu_item_object_id','156');
INSERT INTO `wp_postmeta` VALUES (874,162,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (875,162,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (876,162,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (877,162,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (878,162,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (879,162,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (880,163,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (881,163,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (882,163,'_menu_item_object_id','125');
INSERT INTO `wp_postmeta` VALUES (883,163,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (884,163,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (885,163,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (886,163,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (887,163,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (888,163,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (889,164,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (890,164,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (891,164,'_menu_item_object_id','1');
INSERT INTO `wp_postmeta` VALUES (892,164,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (893,164,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (894,164,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (895,164,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (896,164,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (897,164,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (898,165,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (899,165,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (900,165,'_menu_item_object_id','14');
INSERT INTO `wp_postmeta` VALUES (901,165,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (902,165,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (903,165,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (904,165,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (905,165,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (906,165,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (907,166,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (908,166,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (909,166,'_menu_item_object_id','14');
INSERT INTO `wp_postmeta` VALUES (910,166,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (911,166,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (912,166,'_menu_item_classes','a:2:{i:0;s:3:\"btn\";i:1;s:11:\"btn-primary\";}');
INSERT INTO `wp_postmeta` VALUES (913,166,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (914,166,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (915,166,'_wpconvert_original_classes','btn btn-primary');
INSERT INTO `wp_postmeta` VALUES (916,166,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (917,167,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (918,167,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (919,167,'_menu_item_object_id','21');
INSERT INTO `wp_postmeta` VALUES (920,167,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (921,167,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (922,167,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (923,167,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (924,167,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (925,167,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (926,168,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (927,168,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (928,168,'_menu_item_object_id','1');
INSERT INTO `wp_postmeta` VALUES (929,168,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (930,168,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (931,168,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (932,168,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (933,168,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (934,168,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (935,169,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (936,169,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (937,169,'_menu_item_object_id','14');
INSERT INTO `wp_postmeta` VALUES (938,169,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (939,169,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (940,169,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (941,169,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (942,169,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (943,169,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (944,170,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (945,170,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (946,170,'_menu_item_object_id','12');
INSERT INTO `wp_postmeta` VALUES (947,170,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (948,170,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (949,170,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (950,170,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (951,170,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (952,170,'_wpconvert_has_children','0');
INSERT INTO `wp_postmeta` VALUES (953,158,'_sts_project_hero_class','hero-teal');
INSERT INTO `wp_postmeta` VALUES (954,158,'_sts_project_address','');
INSERT INTO `wp_postmeta` VALUES (955,158,'_sts_project_cover','');
INSERT INTO `wp_postmeta` VALUES (956,159,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (957,157,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (958,158,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (959,156,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (960,125,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (961,126,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (962,1,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (963,2,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (964,3,'rank_math_internal_links_processed','1');
INSERT INTO `wp_postmeta` VALUES (965,4,'rank_math_internal_links_processed','1');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_posts`
--

DROP TABLE IF EXISTS `wp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_posts` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `type_status_author` (`post_type`,`post_status`,`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_posts`
--

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;
INSERT INTO `wp_posts` VALUES (1,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Hvem Er Sts','','publish','closed','closed','','hvem-er-sts','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/hvem-er-sts/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (2,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Asbestsanering og nedrivning | Miljørigtig håndtering – STS ApS','Sikker håndtering af asbest og nedrivningsopgaver med dokumenteret miljøkontrol.','publish','closed','closed','','asbest-og-nedrivning','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/asbest-og-nedrivning/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (3,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Byggepladsservice | Rengøring og support til byggeprojekter – STS ApS','Komplet service til byggepladser med etablering, drift og oprydning i alle faser.','publish','closed','closed','','byggepladsservice','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/byggepladsservice/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (4,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Ejendomsservice | Fast og fleksibel service til din ejendom – STS ApS','Komplet ejendomsservice med fokus på drift, vedligehold og et professionelt helhedsindtryk.','publish','closed','closed','','ejendomsservice','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/ejendomsservice/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (5,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Epoxy og specialmaling - STS ApS','Specialiserede malerbehandlinger til gulve, tekniske rum og områder med særlige krav til slid og rengøring.','publish','closed','closed','','epoxy-og-specialmaling','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/epoxy-og-specialmaling/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (6,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','STS ApS | Erhvervsrengoering','','publish','closed','closed','','erhvervsrengoering','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/erhvervsrengoering/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (7,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Facademaling - STS ApS','Udvendig facademaling til erhvervsejendomme, boligforeninger og institutioner med fokus på holdbarhed og helhedsindtryk.','publish','closed','closed','','facademaling','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/facademaling/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (8,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Gartnerservice | Vedligeholdelse af have og grønne arealer – STS ApS','Pleje af grønne områder og udendørs arealer året rundt for et velholdt udtryk.','publish','closed','closed','','gartnerservice','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/gartnerservice/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (9,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Snerydning og saltning | Glatførebekæmpelse til erhverv – STS ApS','Vinterberedskab med snerydning og saltning for sikre adgangsforhold.','publish','closed','closed','','glatfoere-bekaempelse-snerydning-og-saltning','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/glatfoere-bekaempelse-snerydning-og-saltning/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (10,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Gulvbehandling | Polering, lakering og vedligeholdelse af gulve – STS ApS','Rens, pleje og behandling af gulve så de holder længere og fremstår præsentable.','publish','closed','closed','','gulvbehandling','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/gulvbehandling/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (11,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Håndværkere til erhverv | Maler, murer, tømrer og gulvservice – STS ApS','Maler-, murer- og tømreropgaver samlet i en fleksibel håndværkerservice til erhverv.','publish','closed','closed','','haandvaerkere','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/haandvaerkere/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (12,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Handelsbetingelser | STS ApS','','publish','closed','closed','','handelsbetingelser','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/handelsbetingelser/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (13,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','INSTA 800 Certificering | Kvalitetskontrol af rengøring – STS ApS','Kontrol og inspektion efter INSTA 800-standarden med tydelig dokumentation.','publish','closed','closed','','insta-800-certificeret-kontrol-og-inspektion','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/insta-800-certificeret-kontrol-og-inspektion/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (14,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Kontakt STS ApS – Få et gratis og uforpligtende tilbud i dag','','publish','closed','closed','','kontakt','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/kontakt/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (15,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Maler til erhverv | Professionel malerservice i Danmark – STS ApS','Professionel maling indvendigt og udvendigt med fokus på holdbarhed og finish.','publish','closed','closed','','maler','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/maler/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (16,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Mandskabsudlejning | Fleksibel arbejdskraft til erhverv – STS ApS','Fleksibel bemanding med kvalificerede folk til både korte og længerevarende opgaver.','publish','closed','closed','','mandskabsudlejning','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/mandskabsudlejning/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (17,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Murerarbejde til erhverv | Professionelle murere i Danmark – STS ApS','Fagligt stærkt murerarbejde til reparation, ombygning og vedligehold af erhvervsejendomme.','publish','closed','closed','','murer','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/murer/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (18,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Nedrivningsservice | Professionel nedrivning til fast pris – STS ApS','Planlagt og sikker nedrivning med respekt for miljøkrav og omkringliggende drift.','publish','closed','closed','','nedrivningsservice','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/nedrivningsservice/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (19,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Erhvervsrengøring | Professionel kontorrengøring og industririengøring – STS ApS','Professionel rengøring af kontorer, butikker og industrielle omgivelser med høj kvalitet og fleksibel planlægning.','publish','closed','closed','','rengoering','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',0,'http://sts-wp.local/rengoering/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (20,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Rengøring efter håndværkere | Slutrengøring af lokaler – STS ApS','Slutrengøring efter bygge- og renoveringsarbejde så lokaler hurtigt kan tages i brug.','publish','closed','closed','','rengoering-efter-haandvaerkere','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/rengoering-efter-haandvaerkere/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (21,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Serviceydelser | Erhvervsrengøring, håndværk og byggepladsservice - STS ApS','','publish','closed','closed','','service','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/service/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (22,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Spartelarbejde og filtopsætning - STS ApS','Klargøring af vægge og lofter med spartelarbejde og filtopsætning før maling i kontorer, opgange og erhvervslokaler.','publish','closed','closed','','spartelarbejde-og-filtopsaetning','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/spartelarbejde-og-filtopsaetning/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (23,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','STS Byg | Byggepladsservice, nedrivning og håndværk - STS ApS','','publish','closed','closed','','sts-byg','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/sts-byg/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (24,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','STS Mal | Professionelle maler- og finishydelser - STS ApS','','publish','closed','closed','','sts-mal','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/sts-mal/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (25,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','STS Ren | Rengøring, drift og ejendomspleje - STS ApS','','publish','closed','closed','','sts-ren','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/sts-ren/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (26,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Tømrerarbejde til erhverv | Snedker og tømrer i Danmark – STS ApS','Tømreropgaver udført med præcision til kontor, butik, lager og ejendom.','publish','closed','closed','','toemrer','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/toemrer/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (27,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Trappeopgangsmaling - STS ApS','Maling og opfriskning af trappeopgange og fællesarealer med slidstærke produkter og pæn afslutning.','publish','closed','closed','','trappeopgangsmaling','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/trappeopgangsmaling/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (28,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Nedrivning af minkfarm til fast pris | STS ApS','','publish','closed','closed','','vi-udfoerer-nedrivning-af-mink-farm-til-fast-pris','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/vi-udfoerer-nedrivning-af-mink-farm-til-fast-pris/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (29,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Viceværtservice | Driftshænder til bolig- og erhvervsejendomme – STS ApS','Daglig drift og vedligehold af ejendomme med faste rutiner og hurtig opfølgning.','publish','closed','closed','','vicevaertservice','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/vicevaertservice/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (30,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Vinduespolering | Professionel vinduespolering til erhverv – STS ApS','Effektiv vinduespolering med rene resultater for kontorer, butikker og større bygninger.','publish','closed','closed','','vinduespolering','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',0,'http://sts-wp.local/vinduespolering/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (31,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','STS ApS | Vinduespudsning Polering Afrens M V Tilbydes Baade Manuelt Med Rentvandsanlaeg','','publish','closed','closed','','vinduespudsning-polering-afrens-m-v-tilbydes-baade-manuelt-med-rentvandsanlaeg','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',0,'http://sts-wp.local/vinduespudsning-polering-afrens-m-v-tilbydes-baade-manuelt-med-rentvandsanlaeg/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (32,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert.','Components','','draft','closed','closed','','components','','','2026-08-20 09:05:03','2026-08-20 07:05:03','',0,'http://sts-wp.local/components/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (33,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Footer','','publish','closed','closed','','footer','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',32,'http://sts-wp.local/components/footer/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (34,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Header','','publish','closed','closed','','header','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',32,'http://sts-wp.local/components/header/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (35,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert.','Velkommen','','draft','closed','closed','','velkommen','','','2026-08-20 09:05:03','2026-08-20 07:05:03','',0,'http://sts-wp.local/velkommen/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (36,1,'2026-08-19 16:25:16','2026-08-19 14:25:16','This page is automatically generated by WPConvert. The content is displayed using the page template.','Asbestarbejde | Certificeret asbestsanering og miljøhåndtering – STS ApS','','publish','closed','closed','','asbestarbejde-2','','','2026-08-19 16:25:16','2026-08-19 14:25:16','',35,'http://sts-wp.local/velkommen/asbestarbejde-2/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (47,1,'2026-08-20 08:59:29','2026-08-20 06:59:29','','Groen-Smiley','','inherit','open','closed','','groen-smiley','','','2026-08-20 08:59:29','2026-08-20 06:59:29','',0,'http://sts-wp.local/wp-content/uploads/2026/08/Groen-Smiley.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (48,1,'2026-08-20 08:59:30','2026-08-20 06:59:30','','asbest-og-nedrivning','Asbest og nedrivning. Lovpligtig og sikker håndtering af asbestholdige materialer.','inherit','open','closed','','asbest-og-nedrivning-2','','','2026-08-26 12:19:46','2026-08-26 10:19:46','',0,'http://sts-wp.local/wp-content/uploads/2026/08/asbest-og-nedrivning.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (49,1,'2026-08-20 08:59:31','2026-08-20 06:59:31','','byggepladsservice-20260807-140437-156972','Effektiv byggepladsservice. Sikrer en ryddelig, sikker og velfungerende byggeplads.','inherit','open','closed','','byggepladsservice-20260807-140437-156972','','','2026-08-26 12:19:19','2026-08-26 10:19:19','',0,'http://sts-wp.local/wp-content/uploads/2026/08/byggepladsservice-20260807-140437-156972.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (50,1,'2026-08-20 08:59:31','2026-08-20 06:59:31','','byggepladsservice','Effektiv byggepladsservice. Sikrer en ryddelig, sikker og velfungerende byggeplads.','inherit','open','closed','','byggepladsservice-2','','','2026-08-26 12:19:28','2026-08-26 10:19:28','',0,'http://sts-wp.local/wp-content/uploads/2026/08/byggepladsservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (51,1,'2026-08-20 08:59:32','2026-08-20 06:59:32','','ejendomsservice','Total ejendomsservice. Pålidelig drift og vedligeholdelse skræddersyet til dine behov.','inherit','open','closed','','ejendomsservice-2','','','2026-08-26 12:19:01','2026-08-26 10:19:01','',0,'http://sts-wp.local/wp-content/uploads/2026/08/ejendomsservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (52,1,'2026-08-20 08:59:33','2026-08-20 06:59:33','','epoxy-og-specialmaling','Epoxy- og specialmaling. Ekstremt slidstærke overflader til industri og erhverv.','inherit','open','closed','','epoxy-og-specialmaling-2','','','2026-08-26 12:18:48','2026-08-26 10:18:48','',0,'http://sts-wp.local/wp-content/uploads/2026/08/epoxy-og-specialmaling.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (53,1,'2026-08-20 08:59:34','2026-08-20 06:59:34','','facademaling','Beskyttende facademaling. Forlænger bygningens levetid og giver facaden et æstetisk løft.','inherit','open','closed','','facademaling-2','','','2026-08-26 12:18:14','2026-08-26 10:18:14','',0,'http://sts-wp.local/wp-content/uploads/2026/08/facademaling.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (54,1,'2026-08-20 08:59:34','2026-08-20 06:59:34','','gartnerservice','Komplet gartnerservice. Vi sørger for, at dine udendørsarealer altid fremstår velholdte.','inherit','open','closed','','gartnerservice-2','','','2026-08-26 12:18:30','2026-08-26 10:18:30','',0,'http://sts-wp.local/wp-content/uploads/2026/08/gartnerservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (55,1,'2026-08-20 08:59:37','2026-08-20 06:59:37','','glatfoere-bekaempelse-snerydning-og-saltning','Effektiv snerydning og saltning. Vi holder stier, p-pladser og veje sikre hele vinteren.','inherit','open','closed','','glatfoere-bekaempelse-snerydning-og-saltning-2','','','2026-08-26 12:17:42','2026-08-26 10:17:42','',0,'http://sts-wp.local/wp-content/uploads/2026/08/glatfoere-bekaempelse-snerydning-og-saltning.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (57,1,'2026-08-20 09:01:35','2026-08-20 07:01:35','','grass','','inherit','open','closed','','grass','','','2026-08-20 09:01:35','2026-08-20 07:01:35','',0,'http://sts-wp.local/wp-content/uploads/2026/08/grass.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (58,1,'2026-08-20 09:01:35','2026-08-20 07:01:35','','grass','','inherit','open','closed','','grass','','','2026-08-20 09:01:35','2026-08-20 07:01:35','',0,'http://sts-wp.local/wp-content/uploads/2026/08/grass.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (59,1,'2026-08-20 09:01:36','2026-08-20 07:01:36','','gulvbehandling','Professionel gulvbehandling. Beskytter og forlænger levetiden på dine gulve.','inherit','open','closed','','gulvbehandling-2','','','2026-08-26 12:17:12','2026-08-26 10:17:12','',0,'http://sts-wp.local/wp-content/uploads/2026/08/gulvbehandling.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (60,1,'2026-08-20 09:01:36','2026-08-20 07:01:36','','gulvbehandling','Professionel gulvbehandling. Beskytter og forlænger levetiden på dine gulve.','inherit','open','closed','','gulvbehandling-3','','','2026-08-26 12:17:25','2026-08-26 10:17:25','',0,'http://sts-wp.local/wp-content/uploads/2026/08/gulvbehandling.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (61,1,'2026-08-20 09:01:37','2026-08-20 07:01:37','','haandvaerkere','Samlet håndværkerløsning. Vi koordinerer hele projektet fra start til slut.','inherit','open','closed','','haandvaerkere-2','','','2026-08-26 12:16:43','2026-08-26 10:16:43','',0,'http://sts-wp.local/wp-content/uploads/2026/08/haandvaerkere.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (62,1,'2026-08-20 09:01:37','2026-08-20 07:01:37','','insta-800-certificeret-kontrol-og-inspektion','INSTA 800-certificeret inspektion. Dokumenteret og uafhængig kontrol af rengøringsstandarden.','inherit','open','closed','','insta-800-certificeret-kontrol-og-inspektion-2','','','2026-08-26 12:16:58','2026-08-26 10:16:58','',0,'http://sts-wp.local/wp-content/uploads/2026/08/insta-800-certificeret-kontrol-og-inspektion.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (63,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','lady-1-20260807-135638-ae645e','','inherit','open','closed','','lady-1-20260807-135638-ae645e','','','2026-08-20 09:01:38','2026-08-20 07:01:38','',0,'http://sts-wp.local/wp-content/uploads/2026/08/lady-1-20260807-135638-ae645e.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (64,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','lady-1-20260807-135638-ae645e','','inherit','open','closed','','lady-1-20260807-135638-ae645e','','','2026-08-20 09:01:38','2026-08-20 07:01:38','',0,'http://sts-wp.local/wp-content/uploads/2026/08/lady-1-20260807-135638-ae645e.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (65,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','lady-1','','inherit','open','closed','','lady-1','','','2026-08-20 09:01:38','2026-08-20 07:01:38','',0,'http://sts-wp.local/wp-content/uploads/2026/08/lady-1.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (66,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','lady-1','','inherit','open','closed','','lady-1-2','','','2026-08-20 09:01:38','2026-08-20 07:01:38','',0,'http://sts-wp.local/wp-content/uploads/2026/08/lady-1.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (67,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','maler','Kvalitetsbevidst malerarbejde. Få et holdbart og flot resultat til både private og erhverv.','inherit','open','closed','','maler-2','','','2026-08-26 12:16:06','2026-08-26 10:16:06','',0,'http://sts-wp.local/wp-content/uploads/2026/08/maler.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (68,1,'2026-08-20 09:01:38','2026-08-20 07:01:38','','mandskabsudlejning','Fleksibel mandskabsudlejning. Få kvalificerede ekstra hænder til dine projekter med kort varsel.','inherit','open','closed','','mandskabsudlejning-2','','','2026-08-26 12:16:29','2026-08-26 10:16:29','',0,'http://sts-wp.local/wp-content/uploads/2026/08/mandskabsudlejning.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (69,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','mask-20260807-141816-8a6891','','inherit','open','closed','','mask-20260807-141816-8a6891','','','2026-08-20 09:01:40','2026-08-20 07:01:40','',0,'http://sts-wp.local/wp-content/uploads/2026/08/mask-20260807-141816-8a6891.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (70,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','mask-20260807-141816-8a6891','','inherit','open','closed','','mask-20260807-141816-8a6891','','','2026-08-20 09:01:40','2026-08-20 07:01:40','',0,'http://sts-wp.local/wp-content/uploads/2026/08/mask-20260807-141816-8a6891.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (71,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','mask','','inherit','open','closed','','mask','','','2026-08-20 09:01:40','2026-08-20 07:01:40','',0,'http://sts-wp.local/wp-content/uploads/2026/08/mask.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (72,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','mask','','inherit','open','closed','','mask','','','2026-08-20 09:01:40','2026-08-20 07:01:40','',0,'http://sts-wp.local/wp-content/uploads/2026/08/mask.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (73,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','murer','Professionelt murerarbejde. Alt fra fliselægning og opmuring til totalrenoveringer.','inherit','open','closed','','murer-2-2','','','2026-08-26 12:10:50','2026-08-26 10:10:50','',0,'http://sts-wp.local/wp-content/uploads/2026/08/murer.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (74,1,'2026-08-20 09:01:40','2026-08-20 07:01:40','','murer','Professionelt murerarbejde. Alt fra fliselægning og opmuring til totalrenoveringer.','inherit','open','closed','','murer-2','','','2026-08-26 12:15:46','2026-08-26 10:15:46','',0,'http://sts-wp.local/wp-content/uploads/2026/08/murer.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (75,1,'2026-08-20 09:01:41','2026-08-20 07:01:41','','nedrivningsservice','Sikker og effektiv nedrivningsservice. Vi håndterer alt fra miljøsanering til komplet oprydning.','inherit','open','closed','','nedrivningsservice-2','','','2026-08-26 12:10:27','2026-08-26 10:10:27','',0,'http://sts-wp.local/wp-content/uploads/2026/08/nedrivningsservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (76,1,'2026-08-20 09:01:41','2026-08-20 07:01:41','','nedrivningsservice','Sikker og effektiv nedrivningsservice. Vi håndterer alt fra miljøsanering til komplet oprydning.','inherit','open','closed','','nedrivningsservice-2-2','','','2026-08-26 12:10:05','2026-08-26 10:10:05','',0,'http://sts-wp.local/wp-content/uploads/2026/08/nedrivningsservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (77,1,'2026-08-20 09:01:42','2026-08-20 07:01:42','','rengoering-efter-haandvaerkere','Rengøring efter håndværkere. Vi fjerner alt bygestøv og snavs, så lokalerne er klar til brug.','inherit','open','closed','','rengoering-efter-haandvaerkere-2','','','2026-08-26 11:45:53','2026-08-26 09:45:53','',0,'http://sts-wp.local/wp-content/uploads/2026/08/rengoering-efter-haandvaerkere.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (78,1,'2026-08-20 09:01:43','2026-08-20 07:01:43','','rengoering','Grundig rengøring til erhverv og privat. Vi leverer et rent og sundt indeklima hver gang.','inherit','open','closed','','rengoering-2','','','2026-08-26 11:45:32','2026-08-26 09:45:32','',0,'http://sts-wp.local/wp-content/uploads/2026/08/rengoering.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (79,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','slider-overlay-small','','inherit','open','closed','','slider-overlay-small','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/slider-overlay-small.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (80,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','sliderdark-20260811-143051-2c441c','','inherit','open','closed','','sliderdark-20260811-143051-2c441c','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/sliderdark-20260811-143051-2c441c.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (81,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','sliderdark','','inherit','open','closed','','sliderdark','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/sliderdark.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (82,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','spartelarbejde-og-filtopsaetning','Professionelt spartelarbejde og filtopsætning. Skaber helt glatte vægge klar til maling.','inherit','open','closed','','spartelarbejde-og-filtopsaetning-2','','','2026-08-26 11:44:42','2026-08-26 09:44:42','',0,'http://sts-wp.local/wp-content/uploads/2026/08/spartelarbejde-og-filtopsaetning.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (83,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','specialopgaver','Løsning af specialopgaver. Vi tager os af udfordrende og skræddersyede renoveringsprojekter.','inherit','open','closed','','specialopgaver','','','2026-08-26 11:45:14','2026-08-26 09:45:14','',0,'http://sts-wp.local/wp-content/uploads/2026/08/specialopgaver.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (84,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','thinkstockphotos-120274510-20260807-135556-c4b521','','inherit','open','closed','','thinkstockphotos-120274510-20260807-135556-c4b521','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/thinkstockphotos-120274510-20260807-135556-c4b521.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (85,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','thinkstockphotos-120274510','','inherit','open','closed','','thinkstockphotos-120274510','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/thinkstockphotos-120274510.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (86,1,'2026-08-20 09:01:44','2026-08-20 07:01:44','','thinkstockphotos-926208774','','inherit','open','closed','','thinkstockphotos-926208774','','','2026-08-20 09:01:44','2026-08-20 07:01:44','',0,'http://sts-wp.local/wp-content/uploads/2026/08/thinkstockphotos-926208774.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (87,1,'2026-08-20 09:01:45','2026-08-20 07:01:45','','toemrer','Faguddannet tømrerarbejde. Vi udfører alt fra mindre reparationer til store trækonstruktioner.','inherit','open','closed','','toemrer-2','','','2026-08-26 11:43:41','2026-08-26 09:43:41','',0,'http://sts-wp.local/wp-content/uploads/2026/08/toemrer.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (88,1,'2026-08-20 09:01:45','2026-08-20 07:01:45','','trappeopgangsmaling','Trappeopgangsmaling i høj kvalitet. Giv ejendommens opgang et slidstærkt og præsentabelt udtryk.','inherit','open','closed','','trappeopgangsmaling-2','','','2026-08-26 11:44:19','2026-08-26 09:44:19','',0,'http://sts-wp.local/wp-content/uploads/2026/08/trappeopgangsmaling.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (89,1,'2026-08-20 09:01:46','2026-08-20 07:01:46','','vicevaertservice','Pålidelig viceværtservice. Vi holder ejendommens fællesarealer og tekniske anlæg i topform.','inherit','open','closed','','vicevaertservice-2','','','2026-08-26 11:43:25','2026-08-26 09:43:25','',0,'http://sts-wp.local/wp-content/uploads/2026/08/vicevaertservice.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (90,1,'2026-08-20 09:01:47','2026-08-20 07:01:47','','vinduespolering','Effektiv vinduespolering. Vi sikrer krystalklare og stribefrie ruder på alle etager.','inherit','open','closed','','vinduespolering-2','','','2026-08-26 11:43:05','2026-08-26 09:43:05','',0,'http://sts-wp.local/wp-content/uploads/2026/08/vinduespolering.jpg',0,'attachment','image/jpeg',0);
INSERT INTO `wp_posts` VALUES (91,1,'2026-08-20 09:01:48','2026-08-20 07:01:48','','Servicenormen','','inherit','open','closed','','servicenormen','','','2026-08-20 09:01:48','2026-08-20 07:01:48','',0,'http://sts-wp.local/wp-content/uploads/2026/08/Servicenormen.png',0,'attachment','image/png',0);
INSERT INTO `wp_posts` VALUES (92,1,'2026-08-20 09:01:48','2026-08-20 07:01:48','','layer-10','','inherit','open','closed','','layer-10','','','2026-08-20 09:01:48','2026-08-20 07:01:48','',0,'http://sts-wp.local/wp-content/uploads/2026/08/layer-10.png',0,'attachment','image/png',0);
INSERT INTO `wp_posts` VALUES (93,1,'2026-08-20 09:01:48','2026-08-20 07:01:48','','logo-sts-rgb','','inherit','open','closed','','logo-sts-rgb','','','2026-08-20 09:01:48','2026-08-20 07:01:48','',0,'http://sts-wp.local/wp-content/uploads/2026/08/logo-sts-rgb.png',0,'attachment','image/png',0);
INSERT INTO `wp_posts` VALUES (94,1,'2026-08-20 09:01:48','2026-08-20 07:01:48','','screenshot','','inherit','open','closed','','screenshot','','','2026-08-20 09:01:48','2026-08-20 07:01:48','',0,'http://sts-wp.local/wp-content/uploads/2026/08/screenshot.png',0,'attachment','image/png',0);
INSERT INTO `wp_posts` VALUES (95,1,'2026-08-20 09:05:03','2026-08-20 07:05:03','This page is automatically generated by WPConvert.','Components','','inherit','closed','closed','','32-revision-v1','','','2026-08-20 09:05:03','2026-08-20 07:05:03','',32,'http://sts-wp.local/?p=95',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (96,1,'2026-08-20 09:05:03','2026-08-20 07:05:03','This page is automatically generated by WPConvert.','Velkommen','','inherit','closed','closed','','35-revision-v1','','','2026-08-20 09:05:03','2026-08-20 07:05:03','',35,'http://sts-wp.local/?p=96',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (97,1,'2026-08-20 09:13:35','2026-08-20 07:13:35','{\"version\": 3, \"isGlobalStylesUserThemeJSON\": true }','Custom Styles','','publish','closed','closed','','wp-global-styles-supertotalservice-dk-main','','','2026-08-20 09:13:35','2026-08-20 07:13:35','',0,'http://sts-wp.local/wp-global-styles-supertotalservice-dk-main/',0,'wp_global_styles','',0);
INSERT INTO `wp_posts` VALUES (98,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Erhvervsrengøring</h2>\r\nVi leverer professionel rengøring tilpasset jeres drift, lokaler og tidsplan.','Erhvervsrengøring','Professionel rengøring af kontorer, butikker og industrielle omgivelser med høj kvalitet og fleksibel planlægning.','publish','closed','closed','','rengoering','','','2026-08-21 10:42:02','2026-08-21 08:42:02','',0,'http://sts-wp.local/ydelse/rengoering/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (99,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Håndværkere</h2>\r\nVi løser både små og store håndværksopgaver med fokus på kvalitet og overholdte aftaler.','Håndværkere','Maler-, murer- og tømreropgaver samlet i en fleksibel håndværkerservice til erhverv.','publish','closed','closed','','haandvaerkere','','','2026-08-21 10:43:11','2026-08-21 08:43:11','',0,'http://sts-wp.local/ydelse/haandvaerkere/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (100,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Byggepladsservice</h2>\r\nVi understøtter byggeprojekter med praktisk drift og løbende vedligehold.','Byggepladsservice','Komplet service til byggepladser med etablering, drift og oprydning i alle faser.','publish','closed','closed','','byggepladsservice','','','2026-08-21 10:41:10','2026-08-21 08:41:10','',0,'http://sts-wp.local/ydelse/byggepladsservice/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (101,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Asbestsanering</h2>\r\nVi udfører sanering og nedrivning efter gældende krav til sikkerhed og dokumentation.','Asbestsanering','Sikker håndtering af asbest og nedrivningsopgaver med dokumenteret miljøkontrol.','publish','closed','closed','','asbest-og-nedrivning','','','2026-08-21 10:41:01','2026-08-21 08:41:01','',0,'http://sts-wp.local/ydelse/asbest-og-nedrivning/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (102,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Murerarbejde</h2>\r\nVi håndterer alt fra mindre reparationer til større murerentrepriser.','Murerarbejde','Fagligt stærkt murerarbejde til reparation, ombygning og vedligehold af erhvervsejendomme.','publish','closed','closed','','murer','','','2026-08-21 10:44:42','2026-08-21 08:44:42','',0,'http://sts-wp.local/ydelse/murer/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (103,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Malertjenester</h2>\r\nVi udfører malerarbejde effektivt og med høj kvalitet i alle overflader.','Malerarbejde','Professionel maling indvendigt og udvendigt med fokus på holdbarhed og finish.','publish','closed','closed','','maler','','','2026-08-21 10:44:04','2026-08-21 08:44:04','',0,'http://sts-wp.local/ydelse/maler/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (104,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Tømrerarbejde</h2>\r\nVi leverer fleksible tømrerløsninger til både renovering og nyopbygning.','Tømrerarbejde','Tømreropgaver udført med præcision til kontor, butik, lager og ejendom.','publish','closed','closed','','toemrer','','','2026-08-21 11:03:46','2026-08-21 09:03:46','',0,'http://sts-wp.local/ydelse/toemrer/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (105,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Gulvbehandling</h2>\r\nVi tilbyder professionel behandling af gulve med fokus på slidstyrke og udtryk.','Gulvbehandling','Rens, pleje og behandling af gulve så de holder længere og fremstår præsentable.','publish','closed','closed','','gulvbehandling','','','2026-08-21 10:42:55','2026-08-21 08:42:55','',0,'http://sts-wp.local/ydelse/gulvbehandling/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (106,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Gartnerservice</h2>\r\nVi vedligeholder udearealer, beplantning og grønne miljøer for erhverv.','Gartnerservice','Pleje af grønne områder og udendørs arealer året rundt for et velholdt udtryk.','publish','closed','closed','','gartnerservice','','','2026-08-21 10:42:28','2026-08-21 08:42:28','',0,'http://sts-wp.local/ydelse/gartnerservice/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (107,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Mandskabsudlejning</h2>\r\nVi stiller pålideligt mandskab til rådighed, når kapaciteten skal skaleres.','Mandskabsudlejning','Fleksibel bemanding med kvalificerede folk til både korte og længerevarende opgaver.','publish','closed','closed','','mandskabsudlejning','','','2026-08-21 10:44:23','2026-08-21 08:44:23','',0,'http://sts-wp.local/ydelse/mandskabsudlejning/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (108,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Vinduespolering</h2>\r\nVi holder vinduer rene og præsentable med faste eller fleksible intervaller.','Vinduespolering','Effektiv vinduespolering med rene resultater for kontorer, butikker og større bygninger.','publish','closed','closed','','vinduespolering','','','2026-08-21 11:04:35','2026-08-21 09:04:35','',0,'http://sts-wp.local/ydelse/vinduespolering/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (109,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Rengøring efter håndværkere</h2>\r\nVi fjerner støv, byggesnavs og rester efter håndværksarbejde.','Rengøring efter håndværkere','Slutrengøring efter bygge- og renoveringsarbejde så lokaler hurtigt kan tages i brug.','publish','closed','closed','','rengoering-efter-haandvaerkere','','','2026-08-21 11:02:10','2026-08-21 09:02:10','',0,'http://sts-wp.local/ydelse/rengoering-efter-haandvaerkere/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (110,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Viceværtservice</h2>\r\nVi tager hånd om den løbende ejendomsdrift med fokus på stabil service.','Viceværtservice','Daglig drift og vedligehold af ejendomme med faste rutiner og hurtig opfølgning.','publish','closed','closed','','vicevaertservice','','','2026-08-21 11:04:22','2026-08-21 09:04:22','',0,'http://sts-wp.local/ydelse/vicevaertservice/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (111,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>INSTA 800</h2>\r\nVi udfører kontrol og kvalitetssikring efter INSTA 800 med klare rapporter.','INSTA 800 Certificering','Kontrol og inspektion efter INSTA 800-standarden med tydelig dokumentation.','publish','closed','closed','','insta-800-certificeret-kontrol-og-inspektion','','','2026-08-21 10:43:29','2026-08-21 08:43:29','',0,'http://sts-wp.local/ydelse/insta-800-certificeret-kontrol-og-inspektion/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (112,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Ejendomsservice</h2>\r\nVi samler rengøring, drift og vedligehold i en effektiv løsning.','Ejendomsservice','Komplet ejendomsservice med fokus på drift, vedligehold og et professionelt helhedsindtryk.','publish','closed','closed','','ejendomsservice','','','2026-08-21 10:41:21','2026-08-21 08:41:21','',0,'http://sts-wp.local/ydelse/ejendomsservice/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (113,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Facademaling</h2>\r\nVi udfører facademaling, der beskytter bygningen og løfter det samlede visuelle udtryk.','Facademaling','Udvendig facademaling til erhvervsejendomme, boligforeninger og institutioner med fokus på holdbarhed og helhedsindtryk.','publish','closed','closed','','facademaling','','','2026-08-21 10:42:15','2026-08-21 08:42:15','',0,'http://sts-wp.local/ydelse/facademaling/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (114,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Spartelarbejde og filtopsætning</h2>\r\nVi skaber jævne og professionelle overflader som det rigtige fundament for malerarbejdet.','Spartelarbejde og filtopsætning','Klargøring af vægge og lofter med spartelarbejde og filtopsætning før maling i kontorer, opgange og erhvervslokaler.','publish','closed','closed','','spartelarbejde-og-filtopsaetning','','','2026-08-21 11:03:24','2026-08-21 09:03:24','',0,'http://sts-wp.local/ydelse/spartelarbejde-og-filtopsaetning/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (115,1,'2026-08-20 11:06:58','2026-08-20 09:06:58','<h2>Trappeopgangsmaling</h2>\r\nVi maler trappeopgange med fokus på slidstyrke, fremkommelighed og et pænt førstehåndsindtryk.','Trappeopgangsmaling','Maling og opfriskning af trappeopgange og fællesarealer med slidstærke produkter og pæn afslutning.','publish','closed','closed','','trappeopgangsmaling','','','2026-08-21 11:04:06','2026-08-21 09:04:06','',0,'http://sts-wp.local/ydelse/trappeopgangsmaling/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (116,1,'2026-08-20 11:06:59','2026-08-20 09:06:59','<h2>Epoxy og specialmaling</h2>\r\nVi leverer robuste specialoverflader til miljøer med høje krav til funktion, slidstyrke og rengøringsvenlighed.','Epoxy og specialmaling','Specialiserede malerbehandlinger til gulve, tekniske rum og områder med særlige krav til slid og rengøring.','publish','closed','closed','','epoxy-og-specialmaling','','','2026-08-21 10:41:49','2026-08-21 08:41:49','',0,'http://sts-wp.local/ydelse/epoxy-og-specialmaling/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (117,1,'2026-08-20 11:06:59','2026-08-20 09:06:59','<h2>Snerydning og saltning</h2>\r\nVi holder arealer farbare og sikre gennem hele vintersæsonen.','Snerydning og saltning','Vinterberedskab med snerydning og saltning for sikre adgangsforhold.','publish','closed','closed','','glatfoere-bekaempelse-snerydning-og-saltning','','','2026-08-21 11:02:43','2026-08-21 09:02:43','',0,'http://sts-wp.local/ydelse/glatfoere-bekaempelse-snerydning-og-saltning/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (118,1,'2026-08-20 11:06:59','2026-08-20 09:06:59','<h2>Nedrivningsservice</h2>\r\nVi håndterer nedrivningsopgaver effektivt med fokus på sikker udførelse.','Nedrivningsservice','Planlagt og sikker nedrivning med respekt for miljøkrav og omkringliggende drift.','publish','closed','closed','','nedrivningsservice','','','2026-08-21 11:00:08','2026-08-21 09:00:08','',0,'http://sts-wp.local/ydelse/nedrivningsservice/',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (119,1,'2026-08-05 10:39:42','2026-08-05 08:39:42','Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\r\n\r\nLorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\r\n\r\nLorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.','Hjemmeside redesign','Vi er stolte af at præsenter vores hjemmeside nye design.','trash','open','open','','hjemmeside-redesign__trashed','','','2026-08-20 14:10:19','2026-08-20 12:10:19','',0,'http://sts-wp.local/hjemmeside-redesign/',0,'post','',0);
INSERT INTO `wp_posts` VALUES (120,1,'2026-08-05 08:51:50','2026-08-05 06:51:50','<p>Når foråret kommer, er det det perfekte tidspunkt at give din virksomhed en grundig generøring. En ren arbejdsplads øger trivslen blandt medarbejderne og skaber et positivt indtryk på besøgende.</p><h2>Tips til en vellykket generøring:</h2><ul><li>Start med at afdække behovene for hver afdeling</li><li>Planlæg arbejdet uden for arbejdstid hvis muligt</li><li>Brug miljøvenlige rengøringsmidler</li><li>Kontroller kvaliteten underveis</li></ul><p>Lad os hjælpe dig! Kontakt STS ApS for et gratis tilbud på din forårsgenerøring.</p>','Sådan forbereder du din virksomhed til forårsgenerøring','Generøring er vigtig for at opretholde en ren og professionel arbejdsplads. Læs vores tips til at planlægge og udføre en vellykket forårsgenerøring.','publish','open','open','','sadan-forbereder-du-din-virksomhed-til-forarsgeneroring','','','2026-08-20 11:54:56','2026-08-20 09:54:56','',0,'http://sts-wp.local/sadan-forbereder-du-din-virksomhed-til-forarsgeneroring/',0,'post','',0);
INSERT INTO `wp_posts` VALUES (123,1,'2026-08-20 11:28:43','2026-08-20 09:28:43','Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\r\n\r\nLorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.\r\n\r\nLorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.','Hjemmeside redesign','Vi er stolte af at præsenter vores hjemmeside nye design.','inherit','closed','closed','','119-revision-v1','','','2026-08-20 11:28:43','2026-08-20 09:28:43','',119,'http://sts-wp.local/?p=123',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (124,1,'2026-08-20 11:28:43','2026-08-20 09:28:43','<p>Når foråret kommer, er det det perfekte tidspunkt at give din virksomhed en grundig generøring. En ren arbejdsplads øger trivslen blandt medarbejderne og skaber et positivt indtryk på besøgende.</p><h2>Tips til en vellykket generøring:</h2><ul><li>Start med at afdække behovene for hver afdeling</li><li>Planlæg arbejdet uden for arbejdstid hvis muligt</li><li>Brug miljøvenlige rengøringsmidler</li><li>Kontroller kvaliteten underveis</li></ul><p>Lad os hjælpe dig! Kontakt STS ApS for et gratis tilbud på din forårsgenerøring.</p>','Sådan forbereder du din virksomhed til forårsgenerøring','Generøring er vigtig for at opretholde en ren og professionel arbejdsplads. Læs vores tips til at planlægge og udføre en vellykket forårsgenerøring.','inherit','closed','closed','','120-revision-v1','','','2026-08-20 11:28:43','2026-08-20 09:28:43','',120,'http://sts-wp.local/?p=124',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (125,0,'2026-08-20 13:59:35','2026-08-20 11:59:35','','Nyheder','','publish','closed','closed','','blog','','','2026-08-20 13:59:35','2026-08-20 11:59:35','',0,'http://sts-wp.local/blog/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (126,1,'2026-08-20 13:59:35','2026-08-20 11:59:35','<p>Vi er glade for at kunne præsentere STS’ nye website. Siden er blevet redesignet fra bunden med fokus på at gøre det enklere for virksomheder, ejendomme og byggeprojekter at finde den rigtige hjælp.</p>\r\n\r\n<h2>Et bedre overblik over vores services</h2>\r\n<p>STS tilbyder mange forskellige serviceområder, og det skal være nemt at se, hvad vi kan løse. Derfor har vi samlet vores ydelser i en mere overskuelig struktur, hvor du hurtigt kan gå fra det overordnede serviceområde til den konkrete opgave. Uanset om du leder efter professionel rengøring, malerarbejde, byggepladsservice eller håndværkerhjælp, er vejen blevet kortere.</p>\r\n\r\n<h2>Designet til en travl hverdag</h2>\r\n<p>Det nye design er bygget til at fungere på både computer, tablet og mobil. Tekster, billeder og kontaktmuligheder er placeret, så du kan orientere dig hurtigt, også når du står midt i en opgave. Vi har samtidig gjort det lettere at finde kontaktoplysninger og sende en forespørgsel, når behovet opstår.</p>\r\n\r\n<h2>Samme faglighed, bedre digital oplevelse</h2>\r\n<p>Redesignet ændrer ikke på det vigtigste: vores fokus på kvalitet, ordentlig kommunikation og løsninger, der fungerer i praksis. Det nye website giver os en bedre platform til at vise vores arbejde, dele viden og fortælle om de opgaver, vi løser for vores kunder i hele Danmark.</p>\r\n\r\n<p>Vi håber, du får en god oplevelse på siden. Har du spørgsmål eller en opgave, du vil drøfte, er du altid velkommen til at kontakte os.</p>','STS har fået nyt website','Vi har redesignet STS’ website, så det er lettere at finde den rigtige service, få overblik og tage kontakt til os.','publish','open','open','','sts-har-faaet-nyt-website','','','2026-08-20 13:59:35','2026-08-20 11:59:35','',0,'http://sts-wp.local/sts-har-faaet-nyt-website/',0,'post','',0);
INSERT INTO `wp_posts` VALUES (127,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Epoxy og specialmaling - STS ApS','Specialiserede malerbehandlinger til gulve, tekniske rum og områder med særlige krav til slid og rengøring.','inherit','closed','closed','','5-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',5,'http://sts-wp.local/?p=127',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (128,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Snerydning og saltning | Glatførebekæmpelse til erhverv – STS ApS','Vinterberedskab med snerydning og saltning for sikre adgangsforhold.','inherit','closed','closed','','9-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',9,'http://sts-wp.local/?p=128',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (129,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Nedrivningsservice | Professionel nedrivning til fast pris – STS ApS','Planlagt og sikker nedrivning med respekt for miljøkrav og omkringliggende drift.','inherit','closed','closed','','18-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',18,'http://sts-wp.local/?p=129',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (130,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Erhvervsrengøring | Professionel kontorrengøring og industririengøring – STS ApS','Professionel rengøring af kontorer, butikker og industrielle omgivelser med høj kvalitet og fleksibel planlægning.','inherit','closed','closed','','19-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',19,'http://sts-wp.local/?p=130',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (131,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Håndværkere til erhverv | Maler, murer, tømrer og gulvservice – STS ApS','Maler-, murer- og tømreropgaver samlet i en fleksibel håndværkerservice til erhverv.','inherit','closed','closed','','11-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',11,'http://sts-wp.local/?p=131',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (132,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Byggepladsservice | Rengøring og support til byggeprojekter – STS ApS','Komplet service til byggepladser med etablering, drift og oprydning i alle faser.','inherit','closed','closed','','3-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',3,'http://sts-wp.local/?p=132',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (133,1,'2026-08-20 14:38:00','2026-08-20 12:38:00','This page is automatically generated by WPConvert. The content is displayed using the page template.','Asbestsanering og nedrivning | Miljørigtig håndtering – STS ApS','Sikker håndtering af asbest og nedrivningsopgaver med dokumenteret miljøkontrol.','inherit','closed','closed','','2-revision-v1','','','2026-08-20 14:38:00','2026-08-20 12:38:00','',2,'http://sts-wp.local/?p=133',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (134,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Murerarbejde til erhverv | Professionelle murere i Danmark – STS ApS','Fagligt stærkt murerarbejde til reparation, ombygning og vedligehold af erhvervsejendomme.','inherit','closed','closed','','17-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',17,'http://sts-wp.local/?p=134',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (135,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Maler til erhverv | Professionel malerservice i Danmark – STS ApS','Professionel maling indvendigt og udvendigt med fokus på holdbarhed og finish.','inherit','closed','closed','','15-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',15,'http://sts-wp.local/?p=135',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (136,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Tømrerarbejde til erhverv | Snedker og tømrer i Danmark – STS ApS','Tømreropgaver udført med præcision til kontor, butik, lager og ejendom.','inherit','closed','closed','','26-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',26,'http://sts-wp.local/?p=136',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (137,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Gulvbehandling | Polering, lakering og vedligeholdelse af gulve – STS ApS','Rens, pleje og behandling af gulve så de holder længere og fremstår præsentable.','inherit','closed','closed','','10-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',10,'http://sts-wp.local/?p=137',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (138,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Gartnerservice | Vedligeholdelse af have og grønne arealer – STS ApS','Pleje af grønne områder og udendørs arealer året rundt for et velholdt udtryk.','inherit','closed','closed','','8-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',8,'http://sts-wp.local/?p=138',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (139,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Mandskabsudlejning | Fleksibel arbejdskraft til erhverv – STS ApS','Fleksibel bemanding med kvalificerede folk til både korte og længerevarende opgaver.','inherit','closed','closed','','16-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',16,'http://sts-wp.local/?p=139',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (140,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Vinduespolering | Professionel vinduespolering til erhverv – STS ApS','Effektiv vinduespolering med rene resultater for kontorer, butikker og større bygninger.','inherit','closed','closed','','30-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',30,'http://sts-wp.local/?p=140',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (141,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Rengøring efter håndværkere | Slutrengøring af lokaler – STS ApS','Slutrengøring efter bygge- og renoveringsarbejde så lokaler hurtigt kan tages i brug.','inherit','closed','closed','','20-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',20,'http://sts-wp.local/?p=141',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (142,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Viceværtservice | Driftshænder til bolig- og erhvervsejendomme – STS ApS','Daglig drift og vedligehold af ejendomme med faste rutiner og hurtig opfølgning.','inherit','closed','closed','','29-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',29,'http://sts-wp.local/?p=142',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (143,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','INSTA 800 Certificering | Kvalitetskontrol af rengøring – STS ApS','Kontrol og inspektion efter INSTA 800-standarden med tydelig dokumentation.','inherit','closed','closed','','13-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',13,'http://sts-wp.local/?p=143',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (144,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Ejendomsservice | Fast og fleksibel service til din ejendom – STS ApS','Komplet ejendomsservice med fokus på drift, vedligehold og et professionelt helhedsindtryk.','inherit','closed','closed','','4-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',4,'http://sts-wp.local/?p=144',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (145,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Facademaling - STS ApS','Udvendig facademaling til erhvervsejendomme, boligforeninger og institutioner med fokus på holdbarhed og helhedsindtryk.','inherit','closed','closed','','7-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',7,'http://sts-wp.local/?p=145',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (146,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Spartelarbejde og filtopsætning - STS ApS','Klargøring af vægge og lofter med spartelarbejde og filtopsætning før maling i kontorer, opgange og erhvervslokaler.','inherit','closed','closed','','22-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',22,'http://sts-wp.local/?p=146',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (147,1,'2026-08-20 14:38:01','2026-08-20 12:38:01','This page is automatically generated by WPConvert. The content is displayed using the page template.','Trappeopgangsmaling - STS ApS','Maling og opfriskning af trappeopgange og fællesarealer med slidstærke produkter og pæn afslutning.','inherit','closed','closed','','27-revision-v1','','','2026-08-20 14:38:01','2026-08-20 12:38:01','',27,'http://sts-wp.local/?p=147',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (148,1,'2026-08-20 15:06:24','2026-08-20 13:06:24','','Testservice Skiltevask','En testservice oprettet for at validere den nye STS-servicetemplate.','trash','closed','closed','','testservice-skiltevask__trashed','','','2026-08-20 15:07:24','2026-08-20 13:07:24','',0,'http://sts-wp.local/?sts_service=testservice-skiltevask',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (149,1,'2026-08-20 15:06:24','2026-08-20 13:06:24','','Testservice Skiltevask','','trash','closed','closed','','testservice-skiltevask__trashed','','','2026-08-20 15:07:24','2026-08-20 13:07:24','',0,'http://sts-wp.local/testservice-skiltevask/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (150,1,'2026-08-20 15:07:24','2026-08-20 13:07:24','','Testservice Skiltevask','','inherit','closed','closed','','149-revision-v1','','','2026-08-20 15:07:24','2026-08-20 13:07:24','',149,'http://sts-wp.local/?p=150',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (151,1,'2026-08-20 15:52:45','2026-08-20 13:52:45','','Testservice Skiltevask','Test af ny service.','trash','closed','closed','','testservice-skiltevask__trashed-2','','','2026-08-20 15:52:56','2026-08-20 13:52:56','',0,'http://sts-wp.local/?sts_service=testservice-skiltevask',0,'sts_service','',0);
INSERT INTO `wp_posts` VALUES (152,1,'2026-08-20 15:52:45','2026-08-20 13:52:45','','Testservice Skiltevask','','trash','closed','closed','','testservice-skiltevask__trashed-2','','','2026-08-20 15:52:56','2026-08-20 13:52:56','',0,'http://sts-wp.local/testservice-skiltevask/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (153,1,'2026-08-20 15:52:56','2026-08-20 13:52:56','','Testservice Skiltevask','','inherit','closed','closed','','152-revision-v1','','','2026-08-20 15:52:56','2026-08-20 13:52:56','',152,'http://sts-wp.local/?p=153',0,'revision','',0);
INSERT INTO `wp_posts` VALUES (156,0,'2026-08-28 09:33:22','2026-08-28 07:33:22','','Projekter','','publish','closed','closed','','projekter','','','2026-08-28 09:33:22','2026-08-28 07:33:22','',0,'http://sts-wp.local/projekter/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (157,0,'2026-08-28 09:34:29','2026-08-28 07:34:29','Facaden var præget af mange års vejrlig med afskalning, algevækst og flere gennemgående revner i pudslaget.\n\nVi startede med en skånsom højtryksrensning, hvorefter alle revner blev udkradset og repareret. Defekte fuger blev skiftet, og hele facaden blev grundet før to lag diffusionsåben facademaling.\n\nArbejdet blev udført fra stillads uden at genere ejendommens erhvervslejere, og hele pladsen blev afleveret ryddet og rengjort.','Facademaling af erhvervsejendom','Komplet afrensning og opmaling af en slidt murstensfacade, inklusive reparation af revner og udskiftning af defekte fuger.','publish','closed','closed','','facademaling-erhvervsejendom-koebenhavn','','','2026-08-28 09:51:52','2026-08-28 07:51:52','',0,'http://sts-wp.local/projekter/facademaling-erhvervsejendom-koebenhavn/',0,'sts_project','',0);
INSERT INTO `wp_posts` VALUES (158,0,'2026-08-28 09:34:29','2026-08-28 07:34:29','Efter en gennemgribende renovering skulle lokalerne være klar til indflytning på under en uge.\n\nVores team arbejdede i to hold og gennemførte først en grov byggerengøring med fjernelse af byggestøv, klæbrester og affald. Derefter fulgte en komplet hovedrengøring med vinduespudsning, gulvbehandling og sanitær rengøring.\n\nOpgaven blev afsluttet med en INSTA 800-baseret kvalitetskontrol sammen med kunden.','Hovedrengøring efter håndværkere','Byggerengøring og efterfølgende hovedrengøring af nyrenoverede kontorlokaler, klar til indflytning dagen efter.','publish','closed','closed','','hovedrengoering-efter-haandvaerkere-aarhus','','','2026-08-28 09:51:52','2026-08-28 07:51:52','',0,'http://sts-wp.local/projekter/hovedrengoering-efter-haandvaerkere-aarhus/',1,'sts_project','',0);
INSERT INTO `wp_posts` VALUES (159,0,'2026-08-28 09:34:29','2026-08-28 07:34:29','Lagerhallen skulle fjernes for at give plads til nyt byggeri, men taget indeholdt asbestholdige eternitplader.\n\nVi udarbejdede en saneringsplan og etablerede afspærrede zoner, før taget blev demonteret manuelt af certificeret personale i fuldt værnemiddeludstyr. Alt asbestholdigt materiale blev emballeret, mærket og kørt til godkendt deponi med fuld dokumentation.\n\nDerefter blev bygningskroppen revet ned maskinelt, og materialerne blev sorteret på pladsen, så over 80 % kunne genanvendes.','Nedrivning og asbestsanering af lagerhal','Kontrolleret nedrivning af en ældre lagerhal med asbestholdigt tagmateriale, inklusive sortering og bortskaffelse af alt materiale.','publish','closed','closed','','nedrivning-asbestsanering-lagerhal-odense','','','2026-08-28 09:51:52','2026-08-28 07:51:52','',0,'http://sts-wp.local/projekter/nedrivning-asbestsanering-lagerhal-odense/',2,'sts_project','',0);
INSERT INTO `wp_posts` VALUES (160,1,'2026-08-28 09:48:47','2026-08-28 07:48:47','','Forside','','publish','closed','closed','','forside','','','2026-08-28 09:48:47','2026-08-28 07:48:47','',0,'http://sts-wp.local/forside/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (161,1,'2026-08-28 09:48:47','2026-08-28 07:48:47','','Service','','publish','closed','closed','','service','','','2026-08-28 09:48:47','2026-08-28 07:48:47','',0,'http://sts-wp.local/service/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (162,1,'2026-08-28 09:48:47','2026-08-28 07:48:47',' ','','','publish','closed','closed','','162','','','2026-08-28 09:48:47','2026-08-28 07:48:47','',0,'http://sts-wp.local/162/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (163,1,'2026-08-28 09:48:48','2026-08-28 07:48:48',' ','','','publish','closed','closed','','163','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/163/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (164,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Om os','','publish','closed','closed','','om-os','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/om-os/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (165,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Kontakt','','publish','closed','closed','','kontakt','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/kontakt/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (166,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Få et tilbud','','publish','closed','closed','','faa-et-tilbud','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/faa-et-tilbud/',7,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (167,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Alle ydelser','','publish','closed','closed','','alle-ydelser','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/alle-ydelser/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (168,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Om STS ApS','','publish','closed','closed','','om-sts-aps','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/om-sts-aps/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (169,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Kontakt os','','publish','closed','closed','','kontakt-os','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/kontakt-os/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (170,1,'2026-08-28 09:48:48','2026-08-28 07:48:48','','Handelsbetingelser','','publish','closed','closed','','handelsbetingelser','','','2026-08-28 09:48:48','2026-08-28 07:48:48','',0,'http://sts-wp.local/handelsbetingelser/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (171,1,'2026-08-28 13:00:06','0000-00-00 00:00:00','','Automatisk kladde','','auto-draft','open','open','','','','','2026-08-28 13:00:06','0000-00-00 00:00:00','',0,'http://sts-wp.local/?p=171',0,'post','',0);
/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_rank_math_internal_links`
--

DROP TABLE IF EXISTS `wp_rank_math_internal_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_rank_math_internal_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_id` bigint unsigned NOT NULL,
  `target_post_id` bigint unsigned NOT NULL,
  `type` varchar(8) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link_direction` (`post_id`,`type`),
  KEY `target_post_id` (`target_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_rank_math_internal_links`
--

LOCK TABLES `wp_rank_math_internal_links` WRITE;
/*!40000 ALTER TABLE `wp_rank_math_internal_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_rank_math_internal_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_rank_math_internal_meta`
--

DROP TABLE IF EXISTS `wp_rank_math_internal_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_rank_math_internal_meta` (
  `object_id` bigint unsigned NOT NULL,
  `internal_link_count` int unsigned DEFAULT '0',
  `external_link_count` int unsigned DEFAULT '0',
  `incoming_link_count` int unsigned DEFAULT '0',
  PRIMARY KEY (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_rank_math_internal_meta`
--

LOCK TABLES `wp_rank_math_internal_meta` WRITE;
/*!40000 ALTER TABLE `wp_rank_math_internal_meta` DISABLE KEYS */;
INSERT INTO `wp_rank_math_internal_meta` VALUES (1,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (2,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (3,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (4,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (125,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (126,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (156,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (157,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (158,0,0,0);
INSERT INTO `wp_rank_math_internal_meta` VALUES (159,0,0,0);
/*!40000 ALTER TABLE `wp_rank_math_internal_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_relationships`
--

DROP TABLE IF EXISTS `wp_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_relationships`
--

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;
INSERT INTO `wp_term_relationships` VALUES (97,4,0);
INSERT INTO `wp_term_relationships` VALUES (119,1,0);
INSERT INTO `wp_term_relationships` VALUES (120,1,0);
INSERT INTO `wp_term_relationships` VALUES (126,1,0);
INSERT INTO `wp_term_relationships` VALUES (160,2,0);
INSERT INTO `wp_term_relationships` VALUES (161,2,0);
INSERT INTO `wp_term_relationships` VALUES (162,2,0);
INSERT INTO `wp_term_relationships` VALUES (163,2,0);
INSERT INTO `wp_term_relationships` VALUES (164,2,0);
INSERT INTO `wp_term_relationships` VALUES (165,2,0);
INSERT INTO `wp_term_relationships` VALUES (166,2,0);
INSERT INTO `wp_term_relationships` VALUES (167,3,0);
INSERT INTO `wp_term_relationships` VALUES (168,3,0);
INSERT INTO `wp_term_relationships` VALUES (169,3,0);
INSERT INTO `wp_term_relationships` VALUES (170,3,0);
/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_taxonomy`
--

DROP TABLE IF EXISTS `wp_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint unsigned NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_taxonomy`
--

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
INSERT INTO `wp_term_taxonomy` VALUES (1,1,'category','',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (2,2,'nav_menu','',0,7);
INSERT INTO `wp_term_taxonomy` VALUES (3,3,'nav_menu','',0,4);
INSERT INTO `wp_term_taxonomy` VALUES (4,4,'wp_theme','',0,1);
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_termmeta`
--

DROP TABLE IF EXISTS `wp_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_termmeta`
--

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_terms`
--

DROP TABLE IF EXISTS `wp_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_terms` (
  `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_terms`
--

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;
INSERT INTO `wp_terms` VALUES (1,'Uncategorized','uncategorized',0);
INSERT INTO `wp_terms` VALUES (2,'Primary Menu','primary-menu',0);
INSERT INTO `wp_terms` VALUES (3,'Footer Menu','footer-menu',0);
INSERT INTO `wp_terms` VALUES (4,'supertotalservice-dk-main','supertotalservice-dk-main',0);
/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_usermeta`
--

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (1,1,'nickname','admin');
INSERT INTO `wp_usermeta` VALUES (2,1,'first_name','');
INSERT INTO `wp_usermeta` VALUES (3,1,'last_name','');
INSERT INTO `wp_usermeta` VALUES (4,1,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (5,1,'wp_user_level','10');
INSERT INTO `wp_usermeta` VALUES (6,1,'dismissed_wp_pointers','plugin_editor_notice');
INSERT INTO `wp_usermeta` VALUES (7,1,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (8,1,'session_tokens','a:0:{}');
INSERT INTO `wp_usermeta` VALUES (9,1,'wp_dashboard_quick_press_last_post_id','171');
INSERT INTO `wp_usermeta` VALUES (10,1,'description','');
INSERT INTO `wp_usermeta` VALUES (11,1,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (12,1,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (13,1,'infinite_scrolling','true');
INSERT INTO `wp_usermeta` VALUES (14,1,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (15,1,'admin_color','modern');
INSERT INTO `wp_usermeta` VALUES (16,1,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (17,1,'locale','');
INSERT INTO `wp_usermeta` VALUES (18,1,'wp_persisted_preferences','a:3:{s:4:\"core\";a:1:{s:26:\"isComplementaryAreaVisible\";b:1;}s:14:\"core/edit-post\";a:1:{s:12:\"welcomeGuide\";b:0;}s:9:\"_modified\";s:24:\"2026-08-20T07:13:40.545Z\";}');
INSERT INTO `wp_usermeta` VALUES (19,1,'wp_user-settings','editor=tinymce&libraryContent=browse');
INSERT INTO `wp_usermeta` VALUES (20,1,'wp_user-settings-time','1787235184');
INSERT INTO `wp_usermeta` VALUES (22,1,'_yoast_wpseo_introductions','a:1:{s:30:\"schema-aggregator-announcement\";a:2:{s:7:\"is_seen\";b:1;s:7:\"seen_on\";i:1787739702;}}');
INSERT INTO `wp_usermeta` VALUES (23,1,'_yoast_wpseo_bulk_editor_tour_opt_in_notification_seen','1');
INSERT INTO `wp_usermeta` VALUES (25,1,'manageedit-postcolumnshidden','a:3:{i:0;s:0:\"\";i:1;s:15:\"rank_math_title\";i:2;s:21:\"rank_math_description\";}');
INSERT INTO `wp_usermeta` VALUES (26,1,'manageedit-postcolumnshidden_default','1');
INSERT INTO `wp_usermeta` VALUES (27,1,'manageedit-pagecolumnshidden','a:3:{i:0;s:0:\"\";i:1;s:15:\"rank_math_title\";i:2;s:21:\"rank_math_description\";}');
INSERT INTO `wp_usermeta` VALUES (28,1,'manageedit-pagecolumnshidden_default','1');
INSERT INTO `wp_usermeta` VALUES (29,1,'manageedit-sts_projectcolumnshidden','a:3:{i:0;s:0:\"\";i:1;s:15:\"rank_math_title\";i:2;s:21:\"rank_math_description\";}');
INSERT INTO `wp_usermeta` VALUES (30,1,'manageedit-sts_projectcolumnshidden_default','1');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_users`
--

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (1,'admin','$wp$2y$10$zJLP6gfBgK3o49AN8waMb.PTAMiuBwu8qhZde8sKV/9cg8p6iO0Am','admin','admin@supertotalservice.dk','','2026-08-19 13:39:44','',0,'Admin');
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_expiring_store`
--

DROP TABLE IF EXISTS `wp_yoast_expiring_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_expiring_store` (
  `key_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `exp` datetime NOT NULL,
  PRIMARY KEY (`key_name`),
  KEY `exp_index` (`exp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_expiring_store`
--

LOCK TABLES `wp_yoast_expiring_store` WRITE;
/*!40000 ALTER TABLE `wp_yoast_expiring_store` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_yoast_expiring_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_indexable`
--

DROP TABLE IF EXISTS `wp_yoast_indexable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_indexable` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `permalink` longtext COLLATE utf8mb4_unicode_520_ci,
  `permalink_hash` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `object_id` bigint DEFAULT NULL,
  `object_type` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `object_sub_type` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `author_id` bigint DEFAULT NULL,
  `post_parent` bigint DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_520_ci,
  `description` mediumtext COLLATE utf8mb4_unicode_520_ci,
  `breadcrumb_title` text COLLATE utf8mb4_unicode_520_ci,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `is_protected` tinyint(1) DEFAULT '0',
  `has_public_posts` tinyint(1) DEFAULT NULL,
  `number_of_pages` int unsigned DEFAULT NULL,
  `canonical` longtext COLLATE utf8mb4_unicode_520_ci,
  `primary_focus_keyword` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `primary_focus_keyword_score` int DEFAULT NULL,
  `readability_score` int DEFAULT NULL,
  `is_cornerstone` tinyint(1) DEFAULT '0',
  `is_robots_noindex` tinyint(1) DEFAULT '0',
  `is_robots_nofollow` tinyint(1) DEFAULT '0',
  `is_robots_noarchive` tinyint(1) DEFAULT '0',
  `is_robots_noimageindex` tinyint(1) DEFAULT '0',
  `is_robots_nosnippet` tinyint(1) DEFAULT '0',
  `twitter_title` text COLLATE utf8mb4_unicode_520_ci,
  `twitter_image` longtext COLLATE utf8mb4_unicode_520_ci,
  `twitter_description` longtext COLLATE utf8mb4_unicode_520_ci,
  `twitter_image_id` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `twitter_image_source` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_title` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_description` longtext COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image` longtext COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image_id` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `open_graph_image_source` text COLLATE utf8mb4_unicode_520_ci,
  `open_graph_image_meta` mediumtext COLLATE utf8mb4_unicode_520_ci,
  `link_count` int DEFAULT NULL,
  `incoming_link_count` int DEFAULT NULL,
  `prominent_words_version` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blog_id` bigint NOT NULL DEFAULT '1',
  `language` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `region` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schema_page_type` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `schema_article_type` varchar(64) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `has_ancestors` tinyint(1) DEFAULT '0',
  `estimated_reading_time_minutes` int DEFAULT NULL,
  `version` int DEFAULT '1',
  `object_last_modified` datetime DEFAULT NULL,
  `object_published_at` datetime DEFAULT NULL,
  `inclusive_language_score` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_type_and_sub_type` (`object_type`,`object_sub_type`),
  KEY `object_id_and_type` (`object_id`,`object_type`),
  KEY `permalink_hash_and_object_type` (`permalink_hash`,`object_type`),
  KEY `subpages` (`post_parent`,`object_type`,`post_status`,`object_id`),
  KEY `prominent_words` (`prominent_words_version`,`object_type`,`object_sub_type`,`post_status`),
  KEY `published_sitemap_index` (`object_published_at`,`is_robots_noindex`,`object_type`,`object_sub_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_indexable`
--

LOCK TABLES `wp_yoast_indexable` WRITE;
/*!40000 ALTER TABLE `wp_yoast_indexable` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_yoast_indexable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_indexable_hierarchy`
--

DROP TABLE IF EXISTS `wp_yoast_indexable_hierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_indexable_hierarchy` (
  `indexable_id` int unsigned NOT NULL,
  `ancestor_id` int unsigned NOT NULL,
  `depth` int unsigned DEFAULT NULL,
  `blog_id` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`indexable_id`,`ancestor_id`),
  KEY `indexable_id` (`indexable_id`),
  KEY `ancestor_id` (`ancestor_id`),
  KEY `depth` (`depth`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_indexable_hierarchy`
--

LOCK TABLES `wp_yoast_indexable_hierarchy` WRITE;
/*!40000 ALTER TABLE `wp_yoast_indexable_hierarchy` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_yoast_indexable_hierarchy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_migrations`
--

DROP TABLE IF EXISTS `wp_yoast_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wp_yoast_migrations_version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_migrations`
--

LOCK TABLES `wp_yoast_migrations` WRITE;
/*!40000 ALTER TABLE `wp_yoast_migrations` DISABLE KEYS */;
INSERT INTO `wp_yoast_migrations` VALUES (1,'20171228151840');
INSERT INTO `wp_yoast_migrations` VALUES (2,'20171228151841');
INSERT INTO `wp_yoast_migrations` VALUES (3,'20190529075038');
INSERT INTO `wp_yoast_migrations` VALUES (4,'20191011111109');
INSERT INTO `wp_yoast_migrations` VALUES (5,'20200408101900');
INSERT INTO `wp_yoast_migrations` VALUES (6,'20200420073606');
INSERT INTO `wp_yoast_migrations` VALUES (7,'20200428123747');
INSERT INTO `wp_yoast_migrations` VALUES (8,'20200428194858');
INSERT INTO `wp_yoast_migrations` VALUES (9,'20200429105310');
INSERT INTO `wp_yoast_migrations` VALUES (10,'20200430075614');
INSERT INTO `wp_yoast_migrations` VALUES (11,'20200430150130');
INSERT INTO `wp_yoast_migrations` VALUES (12,'20200507054848');
INSERT INTO `wp_yoast_migrations` VALUES (13,'20200513133401');
INSERT INTO `wp_yoast_migrations` VALUES (14,'20200609154515');
INSERT INTO `wp_yoast_migrations` VALUES (15,'20200616130143');
INSERT INTO `wp_yoast_migrations` VALUES (16,'20200617122511');
INSERT INTO `wp_yoast_migrations` VALUES (17,'20200702141921');
INSERT INTO `wp_yoast_migrations` VALUES (18,'20200728095334');
INSERT INTO `wp_yoast_migrations` VALUES (19,'20201202144329');
INSERT INTO `wp_yoast_migrations` VALUES (20,'20201216124002');
INSERT INTO `wp_yoast_migrations` VALUES (21,'20201216141134');
INSERT INTO `wp_yoast_migrations` VALUES (22,'20210817092415');
INSERT INTO `wp_yoast_migrations` VALUES (23,'20211020091404');
INSERT INTO `wp_yoast_migrations` VALUES (24,'20230417083836');
INSERT INTO `wp_yoast_migrations` VALUES (25,'20260105111111');
INSERT INTO `wp_yoast_migrations` VALUES (26,'20260325155530');
/*!40000 ALTER TABLE `wp_yoast_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_primary_term`
--

DROP TABLE IF EXISTS `wp_yoast_primary_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_primary_term` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint DEFAULT NULL,
  `term_id` bigint DEFAULT NULL,
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blog_id` bigint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `post_taxonomy` (`post_id`,`taxonomy`),
  KEY `post_term` (`post_id`,`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_primary_term`
--

LOCK TABLES `wp_yoast_primary_term` WRITE;
/*!40000 ALTER TABLE `wp_yoast_primary_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_yoast_primary_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_yoast_seo_links`
--

DROP TABLE IF EXISTS `wp_yoast_seo_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_yoast_seo_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `post_id` bigint unsigned DEFAULT NULL,
  `target_post_id` bigint unsigned DEFAULT NULL,
  `type` varchar(8) DEFAULT NULL,
  `indexable_id` int unsigned DEFAULT NULL,
  `target_indexable_id` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `width` int unsigned DEFAULT NULL,
  `size` int unsigned DEFAULT NULL,
  `language` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_direction` (`post_id`,`type`),
  KEY `indexable_link_direction` (`indexable_id`,`type`),
  KEY `url_index` (`url`),
  KEY `target_indexable_id_index` (`target_indexable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_yoast_seo_links`
--

LOCK TABLES `wp_yoast_seo_links` WRITE;
/*!40000 ALTER TABLE `wp_yoast_seo_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_yoast_seo_links` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-02 13:09:33
