<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EpgLiveCheck extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'EpgLiveCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync epgLiveInfo for cms.';

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
            $vm = new ApiModel('live');
            //$updateData = $vm->whereRaw('expired <='.$time)->get();
            $vm->setTable('live');
            $updateData = $vm->newQuery()->get();
            if ($updateData) {
                foreach ($updateData as $ud) {
                    $epgLive = new \Library\DataSource\Data\EpgLive($ud->tv_id);
                    if ($newData = $epgLive->getData()) {
                        \Utils\CMSLog::debug('更新直播:' . $ud->id);
                        if((int)$newData['geo']['type']===1){
                            $newData['geo']['data'] = array_keys(array_except(\Config::get('params.areaFilterList'),$newData['geo']['data'] ));
                            $newData['geo']['force']= array_keys(array_except(\Config::get('params.areaFilterList'),$newData['geo']['force']));
                        }
                        $ud->setTable('live');
                        $ud->update($newData);
                        sleep(1);
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