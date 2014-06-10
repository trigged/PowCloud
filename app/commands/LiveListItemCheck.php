<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;


class LiveListItemCheck extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'LiveListItemCheck';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'live_list_item(直播电视台) 定时更新';

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
        $pid = $this->option('app');
        if ($pid) {
            \Utils\AppChose::updateConf($pid);
            $time = time();
            $vm = new ApiModel('live_list_item');
            //$updateData = $vm->whereRaw('expired <='.$time)->get();
            $vm->setTable('live_list_item');
            $updateData = $vm->newQuery()->get();
            $update = array();
            if ($updateData) {
                foreach ($updateData as $ud) {
                    sleep(1);
                    $epgLive = new \Library\DataSource\Data\EpgLive(trim($ud->content_id));
                    if ($newData = $epgLive->getData()) {
                        \Utils\CMSLog::debug('更新直播:' . $ud->id);
                        if((int)$newData['geo']['type']===1){
                            $update['geo']['type'] =1;
                            $update['geo']['data'] = array_keys(array_except(\Config::get('params.areaFilterList'),$newData['geo']['data'] ));
                            $update['geo']['force']= array_keys(array_except(\Config::get('params.areaFilterList'),$newData['geo']['force']));
                        }else
                            $update['geo'] = $newData['geo'];
                        $update ['title'] = $newData['tv_name'];
                        $update ['icon'] = $newData['tv_icon'];
                        $update ['current_play'] = $newData['current_play'];
                        $ud->setTable('live_list_item');
                        $ud->update($update);
                    }

                    unset($epgLive);
                }
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