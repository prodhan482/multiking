<?php


namespace App\Http\Controllers\Cli\Maintenance;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestCase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Maintainance:TestCase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
        set_time_limit(6000000);
        date_default_timezone_set('America/Los_Angeles');
    }

    public function handle()
    {
        /*$files = Storage::disk('local')->directories('public');
        print_r($files);


        echo "hi";
        print_r(Storage::disk('local')->delete('public/store_logo/file.txt'));*/
    }
}
