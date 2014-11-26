<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class pathDaemon extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
     * @return void
     */
    public function fire()
    {
        $ids = AppModel::all(array('id'))->lists('id');
        foreach ($ids as $id) {
            \Utils\AppChose::updateConf($id);
            try{

                DB::reconnect();
            }
            catch (Exception $e) {
                continue;
            }
            $tables = SchemaBuilder::all();
            foreach ($tables as $table) {
                $this->info(sprintf('## %s, table_name: %s, path: %s',$id,$table->table_name,$table->path['name']));
                $path = $table->path;
                RouteManager::addRouteWithRestFul($path['name'], $table->table_name, $path['expire'],array('index'  => (int)$table->index, 'store' => (int)$table->create,
                                                                                                                           'update' => (int)$table->update, 'delete' => (int)$table->delete));
            }

        }


    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('app', null, InputOption::VALUE_OPTIONAL, 'app', null),
        );
    }


}