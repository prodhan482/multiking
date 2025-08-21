<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSpaceFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("SET SESSION sql_require_primary_key = 0;");

        DB::statement("CREATE TABLE `pending_dig_ocn_spc` (
          `id` varchar(40) NOT NULL,
          `upload_absolute_path` text NOT NULL,
          `remote_file_name` text NOT NULL,
          `table_name` varchar(300) NOT NULL,
          `column_name` text NOT NULL,
          `table_primary_key_val` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        DB::statement("ALTER TABLE `pending_dig_ocn_spc` ADD PRIMARY KEY (`id`);");

        DB::statement("CREATE TABLE `sc_sim_card_files` (
        `row_id` VARCHAR(40) NOT NULL,
        `file_path` TEXT NOT NULL,
        `sc_sim_card_id` VARCHAR(40) NOT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY  (`row_id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8;");

        DB::statement("ALTER TABLE `sc_simcard_offer_reseller` ADD `space_uploaded` ENUM('uploaded','not_uploaded') NOT NULL DEFAULT 'not_uploaded' AFTER `upload_path`");
        DB::statement("ALTER TABLE `sc_simcard_offer` ADD `space_uploaded` ENUM('uploaded','not_uploaded') NOT NULL DEFAULT 'not_uploaded' AFTER `upload_path`");
        DB::statement("ALTER TABLE `payment_receipt_upload` ADD `space_uploaded` ENUM('uploaded','not_uploaded') NOT NULL DEFAULT 'not_uploaded' AFTER `file_path`");
        DB::statement("ALTER TABLE `sc_sim_card_files` ADD `space_uploaded` ENUM('uploaded','not_uploaded') NOT NULL DEFAULT 'not_uploaded' AFTER `file_path`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP TABLE `pending_dig_ocn_spc`;' );
    }
}
