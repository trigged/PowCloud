<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/14/13
 * Time: 5:16 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Utils;

use Illuminate\Support\Facades\Schema;

class DBMaker
{
    //ALTER TABLE `cms_2_data`.`data_link_item`  ADD COLUMN `options` VARCHAR(45) NULL AFTER `deleted_at`;

    const DB_CREATE = 'CREATE DATABASE `%s`;';

    const DB_CREATE_DATA = "
    CREATE DATABASE  IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `%s`;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area_mask`
--

DROP TABLE IF EXISTS `area_mask`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_mask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `common`
--

DROP TABLE IF EXISTS `common`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files_id` int(11) NOT NULL,
  `comment` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `data_link`
--

DROP TABLE IF EXISTS `data_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(45) DEFAULT NULL,
  `data_id` varchar(45) DEFAULT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  `updated_at` varchar(45) DEFAULT NULL,
  `deleted_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_link_item`
--

DROP TABLE IF EXISTS `data_link_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_link_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_link_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `table_name` varchar(45) DEFAULT NULL,
  `table_alias` varchar(45) DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  `updated_at` varchar(45) DEFAULT NULL,
  `deleted_at` varchar(45) DEFAULT NULL,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `fk_data_changelog_data_change1_idx` (`data_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `external_data`
--

DROP TABLE IF EXISTS `external_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `external_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `url` varchar(225) NOT NULL,
  `data_type` char(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `content` blob,
  `user_id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL,
  `url` varchar(45) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(45) NOT NULL COMMENT '字段',
  `rank` int(11) DEFAULT NULL COMMENT '顺序',
  `label` varchar(20) NOT NULL COMMENT '标签',
  `dataType` varchar(20) NOT NULL COMMENT '数据类型',
  `type` varchar(20) NOT NULL COMMENT '字段类型',
  `default_value` varchar(225) DEFAULT '',
  `rules` text NOT NULL COMMENT '验证规则',
  `models_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `isVisible` tinyint(4) DEFAULT NULL,
  `isEditable` tinyint(1) NOT NULL DEFAULT '1',
  `visibleByGroup` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geo_shortcut`
--

DROP TABLE IF EXISTS `geo_shortcut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_shortcut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `shortcut` varchar(225) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(45) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (0,'admin',NULL,NULL);
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_operation`
--

DROP TABLE IF EXISTS `group_operation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_operation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `models_id` int(10) unsigned NOT NULL,
  `read` tinyint(4) DEFAULT NULL,
  `edit` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook`
--

DROP TABLE IF EXISTS `hook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `table_name` varchar(45) NOT NULL DEFAULT '',
  `code` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `user_name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `host` varchar(45) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(45) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `cdn` int(11) DEFAULT NULL,
  `log_level` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(45) NOT NULL,
  `table_alias` varchar(45) DEFAULT NULL,
  `property` text,
  `restful` int(11) DEFAULT NULL,
  `path_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `index` tinyint(4) DEFAULT NULL,
  `update` tinyint(4) DEFAULT NULL,
  `create` tinyint(4) DEFAULT NULL,
  `delete` tinyint(4) DEFAULT NULL,
  `group_name` varchar(45) DEFAULT NULL,
  `models_options` text COMMENT '模型配置选项JSON数组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name_UNIQUE` (`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `path`
--

DROP TABLE IF EXISTS `path`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `host_id` int(11) NOT NULL,
  `parent` varchar(45) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `record`
--

DROP TABLE IF EXISTS `record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connect` varchar(45) DEFAULT NULL,
  `table_name` varchar(45) DEFAULT NULL,
  `content_id` tinyint(4) DEFAULT NULL,
  `content` text,
  `user_name` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `hash` varchar(45) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `url` varchar(45) DEFAULT NULL,
  `user_name` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '		',
  `table_name` varchar(45) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `code` text,
  `action` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `user_name` varchar(45) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-10 18:35:22

";

    /**
     * 创建或者修改表结构
     * @param $table_name
     * @param $table_strut
     * @return bool
     */
    public static function createTable($table_name, $table_strut)
    {
        try {
            if (self::checkTable($table_name)) {
                return '表已经存在,不能重复创建(表名唯一)';
            }
            Schema::connection('models')->create($table_name, self::edit($table_strut));
            return true;
        } catch (\Exception $e) {
            $msg = 'create table error :' . $table_name . ' strut :' . json_encode($table_strut) . ' error info :' . $e->getMessage();
            CMSLog::debug($msg);
            return $e->getMessage();;
        }

    }

    public static function checkTable($table_name)
    {
        if (Schema::connection('models')->hasTable($table_name)) {
            return true;
        }
        return false;
    }

    /**
     * @param $table_strut
     * @return callable
     */
    public static function edit($table_strut)
    {
        return function ($table) use ($table_strut) {

            foreach ($table_strut as $name => $params) {
                $method = $params[0];
                if (!method_exists($table, $method)) {
                    CMSLog::debug(sprintf("table error method not exists,name: %s, params %s", $name, $params));
                    throw new \Exception($method . ' 不是符合要求', 400);
                }
            }

            foreach ($table_strut as $name => $params) {
                $method = $params[0];
                $default = null;
                if (isset($params['default'])) {
                    $default = $params['default'];
                    unset($params['default']);
                }
                if ($method === 'enum') {
                    if ($default) {
                        $table->smallInteger($name)->default($default);
                    } else {
                        $table->smallInteger($name);
                    }
                } elseif (count($params) > 1) {
                    $params[0] = $name;
                    if ($default) {
                        //cause not sure how many params in the method ,so use call user func array
                        call_user_func_array(array($table, $method), $params)->default($default);
                    } else {
                        call_user_func_array(array($table, $method), $params);
                    }
                } else {
                    if ($default) {
                        $table->{$method}($name)->default($default);
                    } else {
                        $table->{$method}($name);
                    }
                }
            }
        };
    }

    public static function createDataBase($data_base, $flag = false)
    {
        try {
            if ($flag) {
                \DB::connection('base')->getPdo()->exec(sprintf(self::DB_CREATE_DATA, $data_base, $data_base));
            } else {
                \DB::connection('base')->statement(sprintf(self::DB_CREATE, $data_base));
            }
            return true;
        } catch (\Exception $e) {
            CMSLog::debug('创建库失败 : ' . $data_base . ' ---------- ' . $e->getMessage());
            return '创建库失败';
        }
    }

    public static function reNameField($table_name, $fields)
    {
        try {
            if (self::checkTable($table_name)) {
                Schema::connection('models')->table($table_name, function ($table) use ($fields) {
                    foreach ($fields as $field => $newField) {
                        if (!empty($field) && !empty($newField)) {
                            $table->renameColumn('`' . $field . '`', '`' . $newField . '`');
                        }
                    }
                });
                return true;
            }
            return 'table not exists';
        } catch (\Exception $e) {
            $msg = '字段重命名:' . $fields . ':' . $e->getMessage();
            CMSLog::debug($msg);
            return $msg;
        }
    }

    public static function addField($table_name, $field)
    {
        try {
            if (self::checkTable($table_name)) {
                Schema::connection('models')->table($table_name, self::edit($field));
                return true;
            }
            return 'table not exists';
        } catch (\Exception $e) {
            CMSLog::debug($e->getMessage());
            if ($e->getCode() == 400) {
                return $e->getMessage();
            }
            return '字段已经存在或创建出错请联系管理员';
        }

    }

    public static function deleteField($table_name, $field)
    {
        try {
            if (self::checkTable($table_name)) {
                Schema::connection('models')->table($table_name, function ($table) use ($field) {
                    $table->dropColumn($field);
                });
                return true;
            }
            return 'table not exists';
        } catch (\Exception $e) {
            CMSLog::debug('删除字段:' . $field . ':' . $e->getMessage());
            return false;
        }
    }

    public static function addIndex($table_name, $field)
    {
        try {

            if (Schema::connection('models')->hasTable($table_name)) {
                Schema::connection('models')->table($table_name, function ($table) use ($field) {
                    $table->index(array($field));
                });
                return true;
            }
            return false;
        } catch (\Exception $e) {
            CMSLog::debug(sprintf('make index error %s', $e));
            return false;
        }

    }

}
