<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class reportData extends Command
{

    const SPECIAL_VIDEO = 'special_video';

    const EPG_VIDEO = 'EpgVideo';

    protected $name = 'check';

    public function __construct()
    {

        parent::__construct();
    }

    public function fire()
    {

        $app_id = $this->option('app');
        if (!$app_id) {
            $this->info('missing app params');
            return;
        }
        \Utils\AppChose::updateConf($app_id);
        $modelsName = Config::get('params.epgCheck');

        $types = array('geo' => 'geo');

        $params = $this->option('type');

        if ($params == 'save') {
            Utils\CheckDataHelper::syncData($types, true);
        } else {
//            \Operator\WriteApi::flashVideoStateCheck();
            \Utils\CheckDataHelper::syncData($types, false);
        }
        \Operator\WriteApi::stopState_check();

        $allZero = true;
        foreach ($modelsName as $modelName) {
            if (\Operator\ReadApi::countZset(\Operator\RedisKey::VIDEO_CHECK . '_' . $modelName) != 0) {
                $allZero = false;
            }
        }
        if ($allZero) {
            \Operator\WriteApi::writeCurrentState_check('ok');
        }

//        if (\Operator\ReadApi::countZset(\Operator\RedisKey::VIDEO_CHECK) == 0) {
//            \Operator\WriteApi::writeCurrentState_check('ok');
//        }
        $this->info('finish refresh data');
    }

    protected function getOptions()
    {
        return array(
            array('type', null, InputOption::VALUE_OPTIONAL, 'show all timing data', null),
            array('app', null, InputOption::VALUE_OPTIONAL, 'app', null),
        );
    }
}