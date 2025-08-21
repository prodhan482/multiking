<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class AddPermissionCorrection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::table('aauth_perms')->insert(['name' => 'Simcard::list_banner', 'definition' => 'View All Banner Promotion For Sim Card', 'id' => 70]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 70, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::create_banner', 'definition' => 'Create Banner Promotion For Sim Card', 'id' => 71]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 71, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::update_banner', 'definition' => 'Update Banner Promotion For Sim Card', 'id' => 72]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 72, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::table('aauth_perms')->insert(['name' => 'Simcard::remove_banner', 'definition' => 'Remove Banner Promotion For Sim Card', 'id' => 73]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 73, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::statement("CREATE TABLE `sc_product_promotion` (
          `id` varchar(40) NOT NULL,
          `product_id` varchar(40) NOT NULL,
          `title` varchar(255) DEFAULT NULL,
          `description` text,
          `size` enum('25_width','50_width','full_width') DEFAULT 'full_width',
          `file_name` text,
          `status` enum('inactive','active') DEFAULT 'active',
          `created_at` datetime DEFAULT NULL,
          `created_by` varchar(40) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `sc_product_promotion` ADD PRIMARY KEY (`id`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('aauth_perms')->where("id", "=", 70)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 70)->delete();

        DB::table('aauth_perms')->where("id", "=", 71)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 71)->delete();

        DB::table('aauth_perms')->where("id", "=", 72)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 72)->delete();

        DB::table('aauth_perms')->where("id", "=", 73)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 73)->delete();

        DB::statement( 'DROP TABLE `sc_product_promotion`;' );
    }
}
