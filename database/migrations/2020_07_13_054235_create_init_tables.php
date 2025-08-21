<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::statement("CREATE TABLE `aauth_perms` (
              `id` int UNSIGNED NOT NULL,
              `name` varchar(255) DEFAULT NULL,
              `definition` text
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        DB::statement("CREATE TABLE `aauth_perms_group` (
              `perm_group_id` int UNSIGNED NOT NULL,
              `prem_id` int UNSIGNED NOT NULL,
              `group_defination` varchar(255) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("CREATE TABLE `aauth_perm_to_user` (
              `perm_id` int UNSIGNED NOT NULL,
              `user_id` varchar(40) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("CREATE TABLE `aauth_pms` (
              `id` int UNSIGNED NOT NULL,
              `sender_id` int UNSIGNED NOT NULL,
              `receiver_id` int UNSIGNED NOT NULL,
              `title` varchar(255) NOT NULL,
              `message` text,
              `date_sent` datetime DEFAULT NULL,
              `date_read` datetime DEFAULT NULL,
              `pm_deleted_sender` int DEFAULT NULL,
              `pm_deleted_receiver` int DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("
            CREATE TABLE `aauth_users` (
              `id` varchar(40) NOT NULL,
              `email` varchar(100) NOT NULL,
              `pass` varchar(60) NOT NULL,
              `username` varchar(100) DEFAULT NULL,
              `banned` tinyint(1) DEFAULT '0',
              `last_login` datetime DEFAULT NULL,
              `last_activity` datetime DEFAULT NULL,
              `date_created` datetime DEFAULT NULL,
              `forgot_exp` text,
              `remember_time` datetime DEFAULT NULL,
              `remember_exp` text,
              `verification_code` text,
              `totp_secret` varchar(16) DEFAULT NULL,
              `ip_address` text,
              `created_by` int DEFAULT NULL,
              `user_type` enum('vendor','manager','super_admin','store','basic_user') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'manager',
              `store_vendor_id` text,
              `fcm_token` text,
              `store_vendor_admin` enum('true','false') DEFAULT NULL,
              `modified_by`  varchar(40) NOT NULL DEFAULT '',
              `modified_at` datetime DEFAULT NULL,
              `insecure` varchar(60) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("CREATE TABLE `store` (
          `store_id` varchar(40) NOT NULL,
          `store_name` varchar(255) NOT NULL,
          `parent_store_id` varchar(40) NOT NULL,
          `image_path` TEXT NULL,
          `status` enum('enabled','disabled') NOT NULL,
          `balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `pending_balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `due_euro` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `store_code` VARCHAR(255) NOT NULL DEFAULT '0',
          `commission_percent` TEXT,
          `store_commission_percent` TEXT,
          `mfs_slab` TEXT,
          `note` TEXT,
          `service_charge_slabs` TEXT NOT NULL,
          `service_charge_slabs_t2` TEXT NOT NULL,
          `default_conv_rate_json` TEXT,
          `store_conv_rate_json` TEXT,
          `allowed_products` TEXT,
          `enable_simcard_access` tinyint(1) NOT NULL DEFAULT '0',
          `base_add_balance_commission_rate` DECIMAL(10,3) NOT NULL DEFAULT '2.0',
          `conversion_rate` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `loan_slab` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `loan_balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `store_owner_name` varchar(255) NOT NULL DEFAULT '',
          `store_address` varchar(255) NOT NULL DEFAULT '',
          `store_phone_number` varchar(255) NOT NULL DEFAULT '',
          `transaction_pin` varchar(10) NOT NULL,
          `base_currency` enum('euro','bdt','gbp','usd','cfa_franc') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'bdt',
          `d4` varchar(255) NOT NULL DEFAULT '',
          `notice_meta` TEXT,
          `last_payment_received` DATETIME NULL DEFAULT NULL,
          `last_payment_received_amount` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `created_by` varchar(40) NOT NULL DEFAULT '',
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("CREATE TABLE `vendor` (
          `vendor_id` varchar(40) NOT NULL,
          `vendor_name` varchar(255) NOT NULL,
          `image_path` TEXT NULL,
          `status` enum('enabled','disabled') NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `allowed_mfs` text,
          `b1` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b2` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b3` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b4` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b5` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `d1` varchar(255) NOT NULL DEFAULT '',
          `d2` varchar(255) NOT NULL DEFAULT '',
          `d3` varchar(255) NOT NULL DEFAULT '',
          `d4` varchar(255) NOT NULL DEFAULT '',
          `last_transection_time` bigint(20) NOT NULL DEFAULT '0',
          `transaction_pin` varchar(10) NOT NULL,
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        DB::statement("CREATE TABLE `mfs` (
          `mfs_id` varchar(40) NOT NULL,
          `mfs_name` varchar(255) NOT NULL,
          `image_path` TEXT NULL,
          `default_commission` DECIMAL(6,2) NOT NULL DEFAULT '0.0',
          `default_charge` DECIMAL(6,2) NOT NULL DEFAULT '0.0',
          `status` enum('enabled','disabled') NOT NULL,
          `mfs_type` enum('mobile_recharge','financial_transaction') NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `mfs`
            ADD PRIMARY KEY (`mfs_id`);");

        DB::statement("CREATE TABLE `promotion` (
          `promotion_id` varchar(40) NOT NULL,
          `promotion_name` varchar(255) NOT NULL,
          `mfs_id` varchar(40) NOT NULL,
          `image_path` TEXT NULL,
          `status` enum('enabled','disabled') NOT NULL,
          `b1` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b2` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `d1` varchar(255) NOT NULL DEFAULT '',
          `d2` varchar(255) NOT NULL DEFAULT '',
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `promotion`
            ADD PRIMARY KEY (`promotion_id`);");

        DB::statement("CREATE TABLE `mfs_package` (
          `row_id` varchar(40) NOT NULL,
          `store_id` varchar(40) NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime NOT NULL,
          `mfs_id` varchar(40) NOT NULL,
          `discount` decimal(10,3) NOT NULL,
          `charge` decimal(10,3) NOT NULL,
          `start_slab` decimal(10,3) NOT NULL,
          `end_slab` decimal(10,3) NOT NULL,
          `package_name` varchar(300) NOT NULL,
          `amount` decimal(10,3) NOT NULL,
          `sort_position` INT(10) NOT NULL DEFAULT '1',
          `enabled` INT(1) NOT NULL DEFAULT '1',
          `note` varchar(255) NOT NULL DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `mfs_package`
            ADD PRIMARY KEY (`row_id`);");

        DB::statement("CREATE TABLE `recharge` (
          `recharge_id` varchar(40) NOT NULL,
          `serial_number` INT(12) NOT NULL DEFAULT '0',
          `recharge_type` VARCHAR(255) NOT NULL DEFAULT 'mfs_recharge',
          `mfs_id` varchar(40) NOT NULL DEFAULT '',
          `mfs_package_id` varchar(40) NOT NULL DEFAULT '',
          `mfs_name` varchar(255) NOT NULL DEFAULT '',
          `phone_number` varchar(255) NOT NULL DEFAULT '',
          `recharge_amount` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `mfs_number_type` varchar(255) NOT NULL DEFAULT '',
          `commission_amount` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `base_currency` enum('euro','bdt','gbp','usd','cfa_franc') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'bdt',
          `sending_currency` VARCHAR(255) NOT NULL,
          `note` text,
          `vendor_note` text,
          `recharge_meta` text,
          `store_conversion_rate` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `vendor_balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `store_balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `store_loan_balance` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `due_euro` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b1` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b2` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `b3` DECIMAL(10,3) NOT NULL DEFAULT '0.0',
          `refund_recharge_row_id` varchar(40) NOT NULL DEFAULT '',
          `d2` varchar(255) NOT NULL DEFAULT '',
          `d3` varchar(255) NOT NULL DEFAULT '',
          `d4` varchar(255) NOT NULL DEFAULT '',
          `fixed_mfs_slab_commission` VARCHAR(200) NOT NULL DEFAULT '0',
          `recharge_status` varchar(255) NOT NULL,
          `locked` tinyint(1) NOT NULL DEFAULT '0',
          `locked_by` varchar(40) NOT NULL DEFAULT '',
          `processed_vendor_id` varchar(40)  NOT NULL DEFAULT '',
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `recharge`
            ADD PRIMARY KEY (`recharge_id`);");

        DB::statement("CREATE TABLE `store_phone_numbers` (
          `row_id` varchar(40) NOT NULL,
          `store_id` varchar(40) NOT NULL,
          `name` varchar(255) NOT NULL,
          `phone_number` varchar(255) NOT NULL,
          `created_at` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `store_phone_numbers`
            ADD PRIMARY KEY (`row_id`);");

        DB::statement("CREATE TABLE `adjustment_history` (
          `row_id` varchar(40) NOT NULL,
          `type` enum('store','vendor') NOT NULL,
          `store_vendor_id` varchar(40) NOT NULL,
          `created_on` datetime NOT NULL,
          `adjusted_amount` float(10,3) NOT NULL,
          `adjustment_type` VARCHAR(20) NOT NULL DEFAULT 'mfs',
          `adjustment_type_id` VARCHAR(255) NULL DEFAULT 'none',
          `adjustment_percent` float(10,3) NOT NULL,
          `created_by` VARCHAR(40) NOT NULL DEFAULT 'system',
          `new_balance` float(10,3) NOT NULL,
          `new_balance_euro` float(10,3) NOT NULL,
          `commission` float(10,3) NOT NULL,
          `euro_amount` float(10,3) NOT NULL,
          `conversion_rate` float(10,3) NOT NULL COMMENT 'FROM anything to BDT',
          `received_amount` float(10,3) NOT NULL,
          `note` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `adjustment_history`
            ADD PRIMARY KEY (`row_id`);");


        DB::statement("CREATE TABLE `payment_receipt_upload` (
          `row_id` varchar(40) NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `parent_store_id` varchar(40) NOT NULL,
          `created_at` datetime NOT NULL,
          `modified_at` datetime NOT NULL,
          `status` varchar(100) NOT NULL DEFAULT 'pending',
          `file_path` text NOT NULL,
          `note` text NOT NULL,
          `serial_number` INT(12) NOT NULL DEFAULT '0',
          `admin_note` text NOT NULL,
          `amount` decimal(10,3) NOT NULL DEFAULT '0.000'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `payment_receipt_upload`
            ADD PRIMARY KEY (`row_id`);");


        DB::statement("ALTER TABLE `aauth_perms`
  ADD PRIMARY KEY (`id`) USING BTREE;");
        DB::statement("ALTER TABLE `aauth_perms_group`
  ADD PRIMARY KEY (`perm_group_id`,`prem_id`) USING BTREE;");
        DB::statement("ALTER TABLE `aauth_perm_to_user`
  ADD PRIMARY KEY (`perm_id`,`user_id`) USING BTREE;");
        DB::statement("ALTER TABLE `aauth_pms`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `full_index` (`id`,`sender_id`,`receiver_id`,`date_read`) USING BTREE;");
        DB::statement("ALTER TABLE `aauth_users` ADD PRIMARY KEY (`id`) USING BTREE;");
        DB::statement("ALTER TABLE `store` ADD PRIMARY KEY (`store_id`);");
        DB::statement("ALTER TABLE `vendor` ADD PRIMARY KEY (`vendor_id`);");
        DB::statement("ALTER TABLE `aauth_perms` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;");
        DB::statement("ALTER TABLE `aauth_pms` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;");

        DB::statement("INSERT INTO `aauth_users` (`id`, `email`, `pass`, `username`, `banned`, `last_login`, `last_activity`, `date_created`, `forgot_exp`, `remember_time`, `remember_exp`, `verification_code`, `totp_secret`, `ip_address`, `created_by`, `user_type`, `store_vendor_id`, `store_vendor_admin`, `modified_by`, `modified_at`, `insecure`) VALUES
('5f0b43f1a07d848fff901206128d3', 'admin', '".base64_encode('admin')."', 'admin', 0, NULL, NULL, '".date("Y-m-d h:i:s")."', NULL, NULL, NULL, '', NULL, NULL, NULL, 'super_admin', NULL, 'false', '', NULL, 'admin');");


        DB::statement("INSERT INTO `aauth_perms` (`id`, `name`, `definition`) VALUES
        (1, 'UserController::list', 'View User List'),
        (2, 'UserController::create', 'Create New User'),
        (3, 'UserController::remove', 'Remove Existing User'),
        (4, 'UserController::update', 'Update Existing User'),
        (5, 'UserController::permission', 'View Existing User Permission'),
        (6, 'UserController::update_permission', 'Update Existing User Permission'),
        (7, 'StoreController::list', 'View All Store'),
        (8, 'StoreController::create', 'Create New Store'),
        (9, 'StoreController::update', 'Update Existing Store'),
        (10, 'StoreController::remove', 'Remove Existing Store'),
        (11, 'VendorController::list', 'View All Vendors'),
        (12, 'VendorController::create', 'Create New Vendor'),
        (13, 'VendorController::update', 'Update Existing Vendor'),
        (14, 'VendorController::remove', 'Remove Existing Vendor'),
        (15, 'RechargeController::list', 'View All Recharge Request'),
        (16, 'RechargeController::create', 'Create New Recharge Request'),
        (17, 'RechargeController::update', 'Update Existing Recharge Request'),
        (18, 'PromotionController::list', 'View All Offer'),
        (19, 'PromotionController::create', 'Create New Offer'),
        (20, 'PromotionController::update', 'Update Existing Offer'),
        (21, 'PromotionController::remove', 'Remove Existing Offer'),
        (22, 'ReportController::vendor_adjustment', 'Vendor Adjustment Report'),
        (23, 'ReportController::store_adjustment', 'Store Adjustment Report'),
        (24, 'ReportController::transaction', 'Transaction Report'),
        (25, 'StoreController::adjust', 'Adjust Store Balance'),
        (26, 'VendorController::adjust', 'Adjust Vendor Balance'),
        (27, 'MFSController::list', 'View All MFS'),
        (28, 'MFSController::create', 'Create New MFS'),
        (29, 'MFSController::update', 'Update Existing MFS'),
        (30, 'MFSController::remove', 'Remove Existing MFS'),
        (31, 'MFSController::package_list', 'View All MFS'),
        (32, 'MFSController::create_package', 'Create New MFS Package'),
        (33, 'MFSController::update_package', 'Update Existing MFS Package'),
        (34, 'ReportController::mfs_summery', 'Transaction Report By MFS Summery'),
        (35, 'ReportController::reseller_balance_recharge', 'Reseller Balance Recharge History Report'),
        (36, 'ReportController::reseller_due_adjust', 'Reseller Due Adjust History Report'),
        (37, 'ReportController::reseller_due_statement', 'View Reseller Due Statement'),
        (38, 'ReportController::payment_doc_upload_statement', 'View Reseller Payment Upload Document Statement'),
        (39, 'RechargeController::upload_payment_doc', 'Upload Payment Document');");

        DB::statement("INSERT INTO `aauth_perms_group` (`perm_group_id`, `prem_id`, `group_defination`) VALUES
        (1, 1, 'User'),
        (1, 2, 'User'),
        (1, 3, 'User'),
        (1, 4, 'User'),
        (1, 5, 'User'),
        (1, 6, 'User'),
        (2, 7, 'Store'),
        (2, 8, 'Store'),
        (2, 9, 'Store'),
        (2, 10, 'Store'),
        (3, 11, 'Vendor'),
        (3, 12, 'Vendor'),
        (3, 13, 'Vendor'),
        (3, 14, 'Vendor'),
        (4, 15, 'Recharge'),
        (4, 16, 'Recharge'),
        (4, 17, 'Recharge'),
        (5, 18, 'Promotion'),
        (5, 19, 'Promotion'),
        (5, 20, 'Promotion'),
        (5, 21, 'Promotion'),
        (6, 22, 'Report'),
        (6, 23, 'Report'),
        (6, 24, 'Report'),
        (2, 25, 'Store'),
        (3, 26, 'Vendor'),
        (7, 27, 'MFS'),
        (7, 28, 'MFS'),
        (7, 29, 'MFS'),
        (7, 30, 'MFS'),
        (7, 31, 'MFS'),
        (7, 32, 'MFS'),
        (7, 33, 'MFS'),
        (6, 34, 'Report'),
        (6, 35, 'Report'),
        (6, 36, 'Report'),
        (6, 37, 'Report'),
        (6, 38, 'Report'),
        (4, 39, 'Recharge');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'bKash', 'assets/mfs_logos/bkash.png', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'financial_transaction');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Rocket', 'assets/mfs_logos/rocket.png', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'financial_transaction');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Nagad', 'assets/mfs_logos/nagad.jpg', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'financial_transaction');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Upay', 'assets/mfs_logos/upay.jpeg', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'financial_transaction');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Mobile Recharge (Grameen Phone)', 'assets/mfs_logos/Grameenphone.png', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'mobile_recharge');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Mobile Recharge (BanglaLink)', 'assets/mfs_logos/banglalink.jpeg', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'mobile_recharge');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Mobile Recharge (Robi)', 'assets/mfs_logos/robi.jpeg', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'mobile_recharge');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Mobile Recharge (Airtel)', 'assets/mfs_logos/airtel.png', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'mobile_recharge');");

        DB::statement( "INSERT INTO `mfs` (`mfs_id`, `mfs_name`, `image_path`, `status`, `created_by`, `created_at`, `modified_at`, `default_commission`, `default_charge`, `mfs_type`) VALUES ('".uniqid('').bin2hex(random_bytes(8))."', 'Mobile Recharge (TeleTalk)', 'assets/mfs_logos/teletalk.png', 'enabled', '', '".date("Y-m-d h:i:s")."', '".date("Y-m-d h:i:s")."', '0', '0', 'mobile_recharge');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP TABLE `aauth_perms`, `aauth_perms_group`, `aauth_perm_to_user`, `aauth_pms`, `aauth_users`, `mfs`, `migrations`, `recharge`, `store`, `vendor`;' );
    }
}
