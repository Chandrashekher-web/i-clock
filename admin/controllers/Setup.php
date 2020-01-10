<?php

    if (!defined('BASEPATH'))
    {
        exit('No direct script access allowed');
    }

    class Setup extends MY_Controller
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function index()
        {

            $dataValues = array("ALTER DATABASE `B3G` COLLATE utf8_unicode_ci",
                "ALTER TABLE `attendance` CHANGE `idx` `attendance_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `empIdx` `employee_id` INT(11) NULL, CHANGE `readerIdx` `reader_id` INT(11) DEFAULT 0 NOT NULL;",
                "ALTER TABLE `attendance` ADD COLUMN `employee_pin` VARCHAR(255) NOT NULL AFTER `job`, ADD COLUMN `downloaded` ENUM('Yes','No') NULL AFTER `employee_pin`, ADD COLUMN `created_at` TIMESTAMP NULL COMMENT 'CURRENT_TIMESTAMP' AFTER `downloaded`;",
                "ALTER TABLE `attendance` CHANGE `employee_pin` `employee_pin` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `attendance_id`, CHANGE `reader_id` `reader_id` INT(11) DEFAULT 0 NOT NULL AFTER `employee_id`;",
                "ALTER TABLE `attendance_archive` CHANGE `idx` `archive_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `empIdx` `employee_id` INT(11) NULL, CHANGE `readerIdx` `reader_id` INT(11) DEFAULT 0 NOT NULL;",
                "RENAME TABLE `commands` TO `reader_command`;",
                "ALTER TABLE `reader_command` CHANGE `idx` `command_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `readerIdx` `reader_id` INT(11) NULL, CHANGE `sent` `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP NULL, ADD COLUMN `cmd_id` INT(11) NULL AFTER `created_at`, ADD COLUMN `sourceinfo` VARCHAR(255) NULL AFTER `cmd_id`, ADD COLUMN `ip_address` VARCHAR(30) NULL AFTER `sourceinfo`;",
                "ALTER TABLE `readers` CHANGE `idx` `reader_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `Stamp` `stamp` BIGINT(20) DEFAULT 0 NULL, CHANGE `Delay` `delay` INT(11) DEFAULT 30 NULL, CHANGE `TTimes` `ttimes` VARCHAR(64) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '00:00;14:00' NULL, CHANGE `TInterval` `transmission_interval` TINYINT(4) DEFAULT 1 NULL, CHANGE `OpStamp` `opstamp` BIGINT(20) DEFAULT 0 NULL, CHANGE `siteIdx` `site_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `UiStamp` `uistamp` BIGINT(20) DEFAULT 0 NOT NULL, CHANGE `FpStamp` `fpstamp` BIGINT(20) DEFAULT 0 NOT NULL, CHANGE `fpSource` `fpsource` ENUM('Yes','No') DEFAULT 'No' NULL, ADD COLUMN `department_id` INT(11) NULL AFTER `fpsource`, ADD COLUMN `facestamp` BIGINT(20) NULL AFTER `department_id`, ADD COLUMN `temp` TINYINT(4) NULL AFTER `facestamp`, ADD COLUMN `lat` DOUBLE NULL AFTER `temp`, ADD COLUMN `lon` DOUBLE NULL AFTER `lat`, ADD COLUMN `online` TINYINT(4) NULL AFTER `lon`, ADD COLUMN `zone` CHAR(1) NULL AFTER `online`, ADD COLUMN `type` CHAR(1) NULL AFTER `zone`, ADD COLUMN `password_exempted` ENUM('Yes','No') DEFAULT 'No' NULL AFTER `type`, ADD COLUMN `order_id` INT(11) NULL AFTER `password_exempted`;",
                "RENAME TABLE `readers` TO `reader`;",
                "RENAME TABLE `site_info` TO `site`;",
                "ALTER TABLE `site` CHANGE `siteIdx` `site_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `notes` `notes` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL, ADD COLUMN `site_code` VARCHAR(255) NULL AFTER `notes`;",
                "RENAME TABLE `sites` TO `site_trans`;",
                "ALTER TABLE `site_trans` CHANGE `idx` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `license` `license_validity` DATE NULL, CHANGE `format` `data_format` TEXT CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL, ADD COLUMN `data_format_other` TEXT NULL AFTER `data_format`, CHANGE `code` `license_key` VARCHAR(11) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL, CHANGE `serverIP` `server_ip` VARCHAR(16) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '107.170.15.81' NOT NULL, CHANGE `serverPort` `server_port` VARCHAR(11) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '80' NOT NULL, CHANGE `SWVer` `swver` VARCHAR(35) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL, ADD COLUMN `site_id` INT(11) NULL AFTER `swver`, ADD COLUMN `status2` ENUM('Enabled','Disabled') DEFAULT 'Enabled' NULL AFTER `site_id`;",
                "RENAME TABLE `departments` TO `department`;",
                "ALTER TABLE `department` CHANGE `idx` `department_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `siteIdx` `site_id` INT(11) DEFAULT 0 NOT NULL;",
                "ALTER TABLE `employee` CHANGE `idx` `employee_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `pin` `pin` VARCHAR(17) CHARSET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `password` `password` VARCHAR(12) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL AFTER `name`, CHANGE `upgrade` `upgrade` TINYINT(4) DEFAULT 1 NULL AFTER `password`, CHANGE `priv` `priv` TINYINT(4) DEFAULT 0 NOT NULL AFTER `upgrade`, CHANGE `card` `card` VARCHAR(12) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL AFTER `priv`, CHANGE `siteIdx` `site_id` INT(11) DEFAULT 0 NOT NULL, ADD COLUMN `status` ENUM('Active','Inactive') DEFAULT 'Active' NULL AFTER `readerIdx`;",
                "ALTER TABLE `employee_fp` CHANGE `empIdx` `employee_id` INT(11) NULL, CHANGE `fp` `fid` INT(11) NULL, ADD COLUMN `readertype` CHAR(1) DEFAULT '0' NULL AFTER `upgrade`, ADD COLUMN `source_fp` ENUM('Yes','No') DEFAULT 'Yes' NULL AFTER `readertype`;",
                "ALTER TABLE `employee_fp` ADD COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD KEY(`id`);",
                "RENAME TABLE `personel` TO `admin`;",
                "ALTER TABLE `admin` CHANGE `idx` `admin_id` INT(11) NOT NULL AUTO_INCREMENT, ADD COLUMN `pin` VARCHAR(20) NULL AFTER `name`, CHANGE `permlevel` `permlevel` INT(11) DEFAULT 0 NOT NULL AFTER `pin`, CHANGE `siteIdx` `site_id` INT(11) DEFAULT 0 NOT NULL AFTER `permlevel`, CHANGE `pin` `password` VARCHAR(16) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL AFTER `site_id`, ADD COLUMN `status` ENUM('Active','Disabled') DEFAULT 'Active' NULL AFTER `password`, ADD COLUMN `user_type` ENUM('Super Admin','Site Admin') DEFAULT 'Super Admin' NULL AFTER `status`, ADD COLUMN `created_at` DATETIME NULL AFTER `user_type`, ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP NULL AFTER `created_at`, CHANGE `depIdxs` `depIdxs` VARCHAR(2048) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL AFTER `updated_at`;",
                "ALTER TABLE `admin` CHANGE `password` `password` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NOT NULL;",
                "ALTER TABLE `attendance` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `attendance_archive` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `admin` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `department` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `employee` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `employee_fp` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `reader` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `reader_command` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE  `site` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "ALTER TABLE `site_trans` ENGINE='InnoDB' COLLATE 'utf8_unicode_ci';",
                "CREATE TABLE `employee_face` ( `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `employee_id` int(11) NOT NULL DEFAULT '0',  `fid` int(11) NOT NULL DEFAULT '0',  `data` text COLLATE utf8_unicode_ci,  `upgrade` tinyint(4) DEFAULT '1',  `readertype` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',  `source_fp` enum('Yes','No') COLLATE utf8_unicode_ci DEFAULT 'No',  PRIMARY KEY (`id`),  UNIQUE KEY `employee_id` (`employee_id`,`fid`),  KEY `employee_id_2` (`employee_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
                "CREATE TABLE `employee_reader_trans` (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `employee_id` int(11) NOT NULL DEFAULT '0',  `reader_id` int(11) NOT NULL DEFAULT '0',  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`),  KEY `employee_id` (`employee_id`),  KEY `reader_id` (`reader_id`)) ENGINE=InnoDB AUTO_INCREMENT=16964 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
                "CREATE TABLE `preferences` (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `server_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,  `expiry_date` date DEFAULT NULL,  `offline_timeout` int(11) DEFAULT NULL,  `license_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,  `old_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `auto_refresh_time` int(11) DEFAULT NULL COMMENT 'in seconds',  PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
                "ALTER TABLE `preferences` ADD `command_max_capacity` int(11) NULL COMMENT 'in kb', ADD `command_max_number` int(11) NULL AFTER `command_max_capacity`;",
                "CREATE TABLE `reader_command_history` (  `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `command_id` int(11) NOT NULL DEFAULT '0',  `cmd_id` int(11) DEFAULT NULL,  `reader_id` int(11) NOT NULL DEFAULT '0',  `command` text COLLATE utf8_unicode_ci,  `sourceinfo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `ip_address` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`history_id`)) ENGINE=InnoDB AUTO_INCREMENT=421 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
                "CREATE TABLE `reader_command_history_unsuccessful` (  `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `command_id` int(11) NOT NULL DEFAULT '0',  `cmd_id` int(11) DEFAULT NULL,  `reader_id` int(11) NOT NULL DEFAULT '0',  `command` text COLLATE utf8_unicode_ci,  `sourceinfo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  `ip_address` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`history_id`)) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
                "CREATE TABLE `reader_command_library` (  `command_id` int(11) NOT NULL AUTO_INCREMENT,  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,  `command_description` text COLLATE utf8_unicode_ci NOT NULL,  PRIMARY KEY (`command_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
            );
            foreach ($dataValues as $key => $values)
            {
                $this->db->query($values);
            }
            exit;
        }

        public function admin_update()
        {
            $this->load->model('admin/User_model');
            $this->db->select('*');
            $this->db->from('admin');
            $admin_data = $this->db->get()->result_array();

            if (!empty($admin_data) && count($admin_data) > 0)
            {
                foreach ($admin_data as $admin)
                {

                    $dataValues = array(
                        'admin_id' => $admin['admin_id'],
                        'password' => md5($admin['password'])
                    );

                    //   $this->User_model->save_admin($dataValues);
                }
            }
            exit;
        }

        public function employee_reader_trans_update()
        {
            $this->load->model('iclock_model');
            $this->db->select('employee_id,readerIdx,');
            $this->db->from('employee');
            $emp_data = $this->db->get()->result_array();

            $this->db->select('reader_id,sn');
            $this->db->from('reader');
            $get_reader_data = $this->db->get()->result_array();

            if (!empty($get_reader_data) && count($get_reader_data) > 0)
            {
                foreach ($get_reader_data as $reader)
                {
                    $arr_reader[$reader['sn']] = $reader['reader_id'];
                }
            }

            if (!empty($emp_data) && count($emp_data) > 0)
            {
                foreach ($emp_data as $emp)
                {
                    $emp_reader = explode('|', $emp['readerIdx']);
                    foreach ($emp_reader as $empreader)
                    {
                        if (!empty($empreader))
                        {
                            if (!empty($arr_reader[$empreader]))
                            {
                                $insertdata['employee_id'] = $emp['employee_id'];
                                $insertdata['reader_id'] = $arr_reader[$empreader];
                                $this->iclock_model->save_employee_reader($insertdata);
                            }
                        }
                    }
                }
            }
            exit;
        }

        public function reader_department_update()
        {

            $this->db->select('*');
            $this->db->from('department');
            $get_depart = $this->db->get()->result_array();
            $depart_arr = array();
            if (!empty($get_depart) && count($get_depart) > 0)
            {
                foreach ($get_depart as $depart)
                {
                    $depart_arr[$depart['name']] = $depart['department_id'];
                }
            }

            $this->db->select('department,reader_id,');
            $this->db->from('reader');
            $reader_data = $this->db->get()->result_array();

            if (!empty($reader_data) && count($reader_data) > 0)
            {
                foreach ($reader_data as $reader)
                {
                    if (!empty($depart_arr[$reader['department']]))
                    {
                        $insertdata['department_id'] = $depart_arr[$reader['department']];

                        $this->db->where('reader_id', $reader['reader_id']);
                        $this->db->update('reader', $insertdata);
                    }
                }
            }
            exit;
        }

        public function remove_and_update_field()
        {

            $dataValues = array(
                "ALTER TABLE `admin` DROP `depIdxs`;",
                "ALTER TABLE `attendance` CHANGE `downloaded` `downloaded` enum('Yes','No') COLLATE 'latin1_swedish_ci' NULL DEFAULT 'No' AFTER `job`, CHANGE `created_at` `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `downloaded`;",
                "ALTER TABLE `attendance` CHANGE `attendance_id` `attendance_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `employee_pin` `employee_pin` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL AFTER `attendance_id`, CHANGE `employee_id` `employee_id` int(11) NULL DEFAULT '0' AFTER `employee_pin`, CHANGE `mode` `mode` smallint(6) NULL AFTER `clock`, CHANGE `status` `status` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'N' AFTER `mode`, CHANGE `work` `work` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `status`, CHANGE `job` `job` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `work`, CHANGE `downloaded` `downloaded` enum('Yes','No') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'No' AFTER `job`;",
                "ALTER TABLE `attendance_archive` CHANGE `archive_id` `archive_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `employee_id` `employee_id` int(11) NOT NULL DEFAULT '0' AFTER `archive_id`, CHANGE `status` `status` char(1) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'N' AFTER `mode`, CHANGE `work` `work` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `reader_id`, CHANGE `job` `job` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `work`;",
                "ALTER TABLE `department` CHANGE `department_id` `department_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `name` `name` varchar(35) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `department_id`; ",
                "ALTER TABLE `employee` CHANGE `employee_id` `employee_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `pin` `pin` varchar(17) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `employee_id`, CHANGE `name` `name` varchar(50) COLLATE 'utf8_unicode_ci' NOT NULL AFTER `pin`, CHANGE `password` `password` varchar(12) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `name`,CHANGE `card` `card` varchar(12) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `priv`, DROP `readerIdx`,CHANGE `status` `status` enum('Active','Inactive') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'Active' AFTER `site_id`;",
                "ALTER TABLE `employee_fp` CHANGE `data` `data` text COLLATE 'utf8_unicode_ci' NULL AFTER `fid`, CHANGE `readertype` `readertype` char(1) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '0' AFTER `upgrade`,CHANGE `source_fp` `source_fp` enum('Yes','No') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'No' AFTER `readertype`;",
                "ALTER TABLE `reader_command`CHANGE `command_id` `command_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST,CHANGE `cmd_id` `cmd_id` int(11) NULL AFTER `command_id`,CHANGE `reader_id` `reader_id` int(11) NOT NULL DEFAULT '0' AFTER `cmd_id`,CHANGE `command` `command` text COLLATE 'utf8_unicode_ci' NULL AFTER `reader_id`,CHANGE `status` `status` enum('Active','Inactive') COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'Inactive' AFTER `command`,CHANGE `sourceinfo` `sourceinfo` varchar(255) COLLATE 'utf8_unicode_ci' NULL AFTER `status`,CHANGE `ip_address` `ip_address` varchar(30) COLLATE 'utf8_unicode_ci' NULL AFTER `sourceinfo`,CHANGE `created_at` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `ip_address`;",
                "ALTER TABLE `site`CHANGE `site_id` `site_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST,CHANGE `name` `name` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `site_id`,CHANGE `contact` `contact` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `name`,CHANGE `notes` `notes` text COLLATE 'utf8_unicode_ci' NULL AFTER `contact`,CHANGE `site_code` `site_code` varchar(35) COLLATE 'utf8_unicode_ci' NULL AFTER `notes`;",
                "ALTER TABLE `site_trans`DROP `status`,CHANGE `status2` `status` enum('Enabled','Disabled') COLLATE 'latin1_swedish_ci' NULL DEFAULT 'Enabled' AFTER `site_id`;",
                "ALTER TABLE `reader` DROP `department`;",
                "ALTER TABLE `admin` CHANGE `admin_id` `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `name` `name` varchar(35) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `admin_id`, CHANGE `pin` `pin` varchar(20) COLLATE 'utf8_unicode_ci' NULL AFTER `name`, CHANGE `password` `password` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `site_id`, CHANGE `status` `status` enum('Active','Disabled') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'Active' AFTER `password`, CHANGE `user_type` `user_type` enum('Super Admin','Site Admin') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'Super Admin' AFTER `status`, CHANGE `updated_at` `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;",
                "ALTER TABLE `attendance` CHANGE `reader_id` `reader_id` int(11) NOT NULL DEFAULT '0' AFTER `employee_id`, CHANGE `clock` `clock` datetime NULL AFTER `reader_id`, CHANGE `mode` `mode` smallint(6) NULL AFTER `clock`, CHANGE `status` `status` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'N' AFTER `mode`, CHANGE `work` `work` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `status`, CHANGE `job` `job` varchar(3) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `work`, CHANGE `downloaded` `downloaded` enum('Yes','No') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'No' AFTER `job`;",
                "ALTER TABLE `employee` CHANGE `status` `status` enum('Active','Inactive') COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'Active' AFTER `site_id`;",
                "ALTER TABLE `site` CHANGE `site_code` `site_code` varchar(35) COLLATE 'utf8_unicode_ci' NULL AFTER `site_id`, CHANGE `name` `name` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `site_code`, CHANGE `contact` `contact` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `name`, CHANGE `notes` `notes` text COLLATE 'utf8_unicode_ci' NULL AFTER `contact`;",
                "ALTER TABLE `site_trans` CHANGE `id` `id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `site_id` `site_id` int(11) NULL AFTER `id`,CHANGE `license_validity` `license_validity` date NULL AFTER `site_id`,CHANGE `data_format` `data_format` text COLLATE 'utf8_unicode_ci' NOT NULL AFTER `license_validity`,CHANGE `data_format_other` `data_format_other` text COLLATE 'utf8_unicode_ci' NULL AFTER `data_format`,CHANGE `license_key` `license_key` varchar(11) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `data_format_other`,CHANGE `server_ip` `server_ip` varchar(16) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '107.170.15.81' AFTER `license_key`,CHANGE `server_port` `server_port` varchar(11) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '80' AFTER `server_ip`,CHANGE `swver` `swver` varchar(35) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `server_port`,CHANGE `status` `status` enum('Enabled','Disabled') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'Enabled' AFTER `swver`;",
                "ALTER TABLE `reader` CHANGE `reader_id` `reader_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST,CHANGE `sn` `sn` varchar(20) COLLATE 'utf8_unicode_ci' NULL AFTER `reader_id`,CHANGE `name` `name` varchar(64) COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' AFTER `sn`,CHANGE `department_id` `department_id` int(11) NULL AFTER `name`,CHANGE `site_id` `site_id` int(11) NOT NULL DEFAULT '0' AFTER `department_id`,CHANGE `stamp` `stamp` bigint(20) NULL DEFAULT '0' AFTER `site_id`,CHANGE `delay` `delay` int(11) NULL DEFAULT '30' AFTER `stamp`,CHANGE `ttimes` `ttimes` varchar(64) COLLATE 'utf8_unicode_ci' NULL DEFAULT '00:00;14:00' AFTER `delay`,CHANGE `transmission_interval` `transmission_interval` int(11) NULL DEFAULT '1' AFTER `ttimes`,CHANGE `opstamp` `opstamp` bigint(20) NULL DEFAULT '0' AFTER `transmission_interval`,CHANGE `seen` `seen` timestamp NULL AFTER `opstamp`,CHANGE `uistamp` `uistamp` bigint(20) NOT NULL DEFAULT '0' AFTER `seen`,CHANGE `fpstamp` `fpstamp` bigint(20) NOT NULL DEFAULT '0' AFTER `uistamp`,CHANGE `fpsource` `fpsource` enum('Yes','No') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'No' AFTER `fpstamp`,CHANGE `zone` `zone` char(1) COLLATE 'utf8_unicode_ci' NULL AFTER `online`,CHANGE `type` `type` char(1) COLLATE 'utf8_unicode_ci' NULL AFTER `zone`,CHANGE `password_exempted` `password_exempted` enum('Yes','No') COLLATE 'utf8_unicode_ci' NULL DEFAULT 'No' AFTER `type`;"
            );
            foreach ($dataValues as $key => $values)
            {
                $this->db->query($values);
            }
            exit;
        }

    }

    // 6530152200130,

    