<?php

namespace GovindTomar\CrudGeneratorApi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use GovindTomar\CrudGeneratorApi\Helpers\Helper;
use GovindTomar\CrudGeneratorApi\Helpers\ModelHelper;
use GovindTomar\CrudGeneratorApi\Helpers\ControllerApiHelper;
use GovindTomar\CrudGeneratorApi\Helpers\RequestHelper;
use GovindTomar\CrudGeneratorApi\Helpers\ActionHelper;

class CrudGeneratorApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
                        {name : Class (singular), e.g User}
                        {--fields= : Field names for the form & migration.}
                        {--model= : Tables for select option}
                        {--migration= : Tables for select option}
                        {--delete= : Delete CRUD files}
                        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $fields = $this->option('fields');
        $tables = $this->option('model');
        $migration = $this->option('migration');
        $delete = $this->option('delete');

        // Delete when call
        if ($delete == 'y' || $delete == 'yes') {
            File::delete(app_path("/Models/{$name}.php"));
            File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));
            File::delete(app_path("/Http/Requests".Helper::forslash().Helper::namespace()."/{$name}Request.php"));
            ActionHelper::delete_current_table($name);

            return false;
        }  

        // Create directory when not exists 
        if(!file_exists(app_path('/Http/Controllers'.Helper::forslash().Helper::namespace()))){
            mkdir(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()));
        }

        if(!file_exists(app_path('/Http/Requests'))){
            mkdir(app_path("/Http/Requests"));
        }

        if(!file_exists(app_path('/Http/Requests'.Helper::forslash().Helper::namespace()))){
            mkdir(app_path("/Http/Requests".Helper::forslash().Helper::namespace()));
        }

        if(!file_exists(app_path('/Models'))){
            mkdir(app_path("/Models"));
        }

        // Clean old code
        File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));

        File::delete(app_path("/Models/{$name}.php"));
        ModelHelper::model($name, $fields, $tables);

        File::delete(app_path("/Http/Requests/".Helper::forslash().Helper::namespace()."/{$name}Request.php"));
        RequestHelper::request($name, $fields, $tables);


        /**********************************************************************
         * 
         *       Generate New Code
         *
         * *******************************************************************/

        ControllerApiHelper::controller($name, $fields, $tables);
    
        ActionHelper::api_routes($name, $fields);
        
        if ($migration == 'y' || $migration == 'yes') {
            ActionHelper::migration($name, $fields, $tables);
        }       

    }


}
