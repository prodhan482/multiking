<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class C2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::table('aauth_perms')->insert(['name' => 'Simcard::remove_sim', 'definition' => 'Remove Sim Card', 'id' => 74]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 74, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);

        DB::statement("ALTER TABLE `sc_sim_card`  ADD `country_name` VARCHAR(300) NULL DEFAULT NULL  AFTER `sur_name`,  ADD `date_of_birth` VARCHAR(100) NULL DEFAULT NULL  AFTER `country_name`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::table('aauth_perms')->where("id", "=", 74)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 74)->delete();

        DB::statement("ALTER TABLE `sc_sim_card` DROP `country_name`, DROP `date_of_birth`;");
    }
}
