<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SimcardModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::table('aauth_perms')->insert(['name' => 'Simcard::list', 'definition' => 'View All Simcard', 'id' => 40]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 40, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::create', 'definition' => 'Create New Simcard', 'id' => 41]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 41, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::view_stock', 'definition' => 'View Stocked Simcard', 'id' => 42]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 42, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::view_sold', 'definition' => 'View Sold Simcard', 'id' => 43]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 43, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::sale', 'definition' => 'Sale Stocked Simcard', 'id' => 44]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 44, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::promo', 'definition' => 'View Simcard Promo List', 'id' => 45]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 45, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::create_promo', 'definition' => 'Create Simcard Promo', 'id' => 46]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 46, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::view_mnp_operator_list', 'definition' => 'View MNP Operator List', 'id' => 47]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 47, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::create_mnp_operator', 'definition' => 'Create MNP Operator', 'id' => 48]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 48, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);


        DB::table('aauth_perms')->insert(['name' => 'SimCardReport::sales_report', 'definition' => 'Sales Report', 'id' => 49]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 49, 'group_defination' => 'SIM Card Report', 'perm_group_id' => 9]);

        DB::table('aauth_perms')->insert(['name' => 'SimCardReport::recharge_report', 'definition' => 'Recharge Report', 'id' => 50]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 50, 'group_defination' => 'SIM Card Report', 'perm_group_id' => 9]);

        DB::table('aauth_perms')->insert(['name' => 'SimCardReport::adjustment_report', 'definition' => 'Adjustment Report', 'id' => 51]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 51, 'group_defination' => 'SIM Card Report', 'perm_group_id' => 9]);


        DB::table('aauth_perms')->insert(['name' => 'Simcard::create_order', 'definition' => 'Create Sim Card Order', 'id' => 52]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 52, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::approve_order', 'definition' => 'Approve Sim Card Order', 'id' => 53]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 53, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::remove_order', 'definition' => 'Remove Sim Card Order', 'id' => 54]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 54, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::reject_order', 'definition' => 'Reject Sim Card Order', 'id' => 55]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 55, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::view_orders', 'definition' => 'View Sim Card Orders', 'id' => 59]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 59, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);


        DB::table('aauth_perms')->insert(['name' => 'Inventory::view_product', 'definition' => 'View All Product', 'id' => 58]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 58, 'group_defination' => 'Inventory', 'perm_group_id' => 10]);

        DB::table('aauth_perms')->insert(['name' => 'Inventory::create_product', 'definition' => 'Add New Product', 'id' => 56]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 56, 'group_defination' => 'Inventory', 'perm_group_id' => 10]);

        DB::table('aauth_perms')->insert(['name' => 'Inventory::update_product', 'definition' => 'Update Existing Product', 'id' => 57]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 57, 'group_defination' => 'Inventory', 'perm_group_id' => 10]);


        DB::table('aauth_perms')->insert(['name' => 'Simcard::info', 'definition' => 'View SimCard Info', 'id' => 63]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 63, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::upload', 'definition' => 'Upload SimCard Related Files', 'id' => 64]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 64, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::reject', 'definition' => 'Reject SimCard', 'id' => 60]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 60, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::activate', 'definition' => 'Activate SimCard', 'id' => 61]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 61, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::emergency_unlock', 'definition' => 'Emergency Unlock SimCard', 'id' => 62]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 62, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::appoint_sim_card', 'definition' => 'Appoint Sim Card to Orders', 'id' => 65]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 65, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::change_status', 'definition' => 'Change Sim Card Status', 'id' => 66]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 66, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);




        DB::statement("ALTER TABLE `store` ADD `simcard_due_amount` DECIMAL(10,3) NOT NULL DEFAULT '0' AFTER `balance`;");

        Schema::create('inv_products', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('name');
            $table->tinyInteger('enabled')->default('1');
            $table->decimal('price');
            $table->string('type');
            $table->string('created_by');
            $table->timestamps();
        });

        DB::statement("CREATE TABLE `sc_mnp_operator_list` (
          `id` varchar(40) NOT NULL,
          `title` text,
          `description` text,
          `product_id` text,
          `reseller_bonus` varchar(255) DEFAULT NULL,
          `status` enum('disable','enable') DEFAULT 'enable',
          `reseller_offer` text NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_mnp_operator_list` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_simcard_offer` (
          `id` varchar(40) NOT NULL,
          `title` text,
          `description` text,
          `product_id` varchar(40) NOT NULL,
          `bonus` varchar(255) DEFAULT NULL,
          `reseller_price` varchar(100) NOT NULL DEFAULT '0',
          `status` enum('disable','enable') DEFAULT 'enable',
          `reseller_offer` text NOT NULL,
          `upload_path` text NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_simcard_offer` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_simcard_offer_reseller` (
          `row_id` varchar(40) NOT NULL,
          `sc_simcard_offer_id` varchar(40) NOT NULL,
          `store_id` varchar(40) NOT NULL,
          `title` text,
          `description` text,
          `bonus` varchar(255) DEFAULT NULL,
          `reseller_price` varchar(100) NOT NULL DEFAULT '0',
          `status` enum('disable','enable') DEFAULT 'enable',
          `reseller_offer` text NOT NULL,
          `upload_path` text NOT NULL,
          `created_by` varchar(40) NOT NULL,
          `created_at` datetime DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_simcard_offer_reseller` ADD PRIMARY KEY (`sc_simcard_offer_id`, `store_id`);");

        DB::statement("CREATE TABLE `sc_sim_card` (
          `id` varchar(40) NOT NULL,
          `order_id` varchar(40) NOT NULL,
          `product_id` varchar(40) NOT NULL,
          `store_id` varchar(40) NOT NULL,
          `lot_id` varchar(255) DEFAULT NULL,
          `sim_card_iccid` varchar(255) DEFAULT NULL,
          `sim_card_mobile_number` varchar(255) DEFAULT NULL,
          `product_offer_id` varchar(40) DEFAULT '0',
          `cost` decimal(16,6) DEFAULT NULL,
          `sales_price` decimal(16,6) DEFAULT NULL,
          `sales_status` enum('in_stock','sold') DEFAULT 'in_stock',
          `sold_at` datetime DEFAULT NULL,
          `activated_at` datetime DEFAULT NULL,
          `created_at` datetime NOT NULL,
          `ordered_at` datetime DEFAULT NULL,
          `approved_at` datetime DEFAULT NULL,
          `activation_id` int(11) DEFAULT NULL,
          `custom_product_offer` text NOT NULL,
          `other_operator_name` text NOT NULL,
          `sur_name` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `activation_sms_mobile_number` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `codicifiscale` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `mnp_operator_name` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `mnp_iccid_number` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `mnp_iccid_mobile_number` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `mnp_notes` varchar(500) NOT NULL DEFAULT '&nbsp;',
          `ricarica` varchar(255) NOT NULL DEFAULT '&nbsp;',
          `reseller_price` varchar(255) NOT NULL DEFAULT '&nbsp;',
          `status` enum('rejected','approved','trashed','archived','stocked','moved','pending') DEFAULT 'pending',
          `locked` tinyint(1) DEFAULT '0',
          `locked_by` varchar(40) DEFAULT '0',
          `activated_by` varchar(40) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_sim_card` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_sim_card_history` (
          `id` varchar(40) NOT NULL,
          `sim_card_id` VARCHAR(40) NULL DEFAULT NULL,
          `status` enum('rejected','approved','pending') DEFAULT 'rejected',
          `cause` text,
          `created_at` datetime DEFAULT NULL,
          `created_by` varchar(40) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_sim_card_history` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_sim_card_meta_data` (
          `id` varchar(40) NOT NULL,
          `sim_card_id` varchar(40) DEFAULT NULL,
          `meta_key` varchar(255) DEFAULT NULL,
          `meta_value` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_sim_card_meta_data` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_sim_card_lot` (
          `id` varchar(40) NOT NULL,
          `name` varchar(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_sim_card_lot` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_orders` (
          `id` varchar(40) NOT NULL,
          `product_id` varchar(40) NOT NULL,
          `store_id` varchar(40) NOT NULL,
          `quantity` int(11) DEFAULT NULL,
          `status` enum('pending','approved','rejected') DEFAULT 'pending',
          `created_at` datetime DEFAULT NULL,
          `created_by` varchar(40) NOT NULL,
          `saved_simcard_numbers` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_orders` ADD PRIMARY KEY (`id`);");
        DB::statement("ALTER TABLE `sc_orders`  ADD `order_serial` VARCHAR(40) NOT NULL DEFAULT '1'  AFTER `id`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP TABLE `sc_orders`, `sc_sim_card_lot`, `sc_sim_card_meta_data`, `sc_sim_card_history`, `sc_sim_card`, `sc_simcard_offer`, `sc_mnp_operator_list`, `inv_products`;' );
    }
}
