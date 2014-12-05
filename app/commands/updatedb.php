<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class updatedb extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'updatedb';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update db.';

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
                Schema::connection('mysql')->table('models', \Utils\DBMaker::edit(array('types'=>array('string'))));
                \Utils\DBMaker::addField('types',array());
            }
            catch (Exception $e) {
                $this->info(sprintf('## %s,got exception : %s',$id,$e->getMessage()));
                continue;
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}