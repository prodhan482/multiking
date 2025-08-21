<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class NewCorrection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::table('aauth_perms')->insert(['name' => 'Simcard::remove_simcard_files', 'definition' => 'Remove SimCard Related Uploaded Files', 'id' => 67]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 67, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);


        DB::table('aauth_perms')->insert(['name' => 'Simcard::lock', 'definition' => 'Lock Un-Lock Simcard Edit', 'id' => 68]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 68, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);


        DB::table('aauth_perms')->insert(['name' => 'Simcard::update', 'definition' => 'Update Simcard', 'id' => 69]);
        DB::table('aauth_perms_group')->insert(['prem_id' => 69, 'group_defination' => 'SIM Card', 'perm_group_id' => 8]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('aauth_perms')->where("id", "=", 67)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 67)->delete();

        DB::table('aauth_perms')->where("id", "=", 68)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 68)->delete();

        DB::table('aauth_perms')->where("id", "=", 69)->delete();
        DB::table('aauth_perms_group')->where("prem_id", "=", 69)->delete();
    }
}
