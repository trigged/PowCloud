<?php

use Illuminate\Console\Command;
use Operator\RedisKey;
use Operator\WriteApi;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class timingData extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    //pub success
    CONST SUCCESS = 2;

    //not pub data

    CONST NOT_TIMING = -1;

    //has timing data or pub failed

    CONST HAS_PUB = 1;

    protected $name = 'timing';

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

        $app_id = $this->option('app');
        if (!$app_id) {
            $this->info('missing app params');
            return;
        }

        \Utils\AppChose::updateConf($app_id);

        //region timing logic
        $type = $this->option('type');
        $time = time();
        if ($type == 'debug') {
            $this->info('current time :' . $time);
            $values = \Operator\ReadApi::getTimingData('+inf', true);
            $this->info('prepare for timing data :' . json_encode($values, true));
        } else {
            $values = \Operator\ReadApi::getTimingData($time);
            foreach ($values as $value) {
                try {
                    $value = \Operator\ReadApi::getTimingInfo($value);
                    $result = explode('::', $value);
                    // type:table_name:id:title
                    if (count($result) == 4) {
                        $timing_type = (int)$result[0];
                        $table_name = $result[1];
                        $value_id = $result[2];
                        $model = new ApiModel($table_name);
                        $model->setTable($table_name);
                        $model = $model->newQueryWithDeleted()->find($value_id);
                        $model->setTable($table_name);
                        $model->processGeo = false;
                        if ($model && $model->exists) {
                            if ((int)$model->timing_state !== $timing_type) {
                                //if cache type was diif from model then it's still will try once
                                $timing_type = $model->timing_state;
                                $this->info('timing data type error  :' . $value);
                            }
                            if ($timing_type === RedisKey::HAS_PUB_FIRST) {
                                $max = $model->newQueryWithDeleted()->orderBy('rank', 'desc')->first(array('id', 'rank'));
                                if ((int)$max->id === $value_id) {
                                    WriteApi::delTimingData($table_name, $value_id);
                                    continue;
                                }
                                $model->rank = $max->rank + 1;
                                $model->timing_state = RedisKey::PUB_ONLINE;
                                $model->restore();
                                WriteApi::delTimingData($table_name, $value_id);
                                $this->info(date('Y-m-d H:i:s', time()) . '  timing data success :' . $value);

                            } elseif ($timing_type === RedisKey::HAS_PUB_OFFLINE) {
                                $model->timing_state = RedisKey::READY_LINE;
                                \Operator\CacheController::delete($table_name, $model);
                                $model->save();
                                $model->delete();
                                WriteApi::delTimingData($table_name, $value_id);
                                $this->info(date('Y-m-d H:i:s', time()) . '  timing data success :' . $value);
                            } elseif ($timing_type === RedisKey::HAS_PUB_ONLINE) {
                                $model->timing_state = RedisKey::PUB_ONLINE;
                                $model->restore();
                                WriteApi::delTimingData($table_name, $value_id);
                                $this->info(date('Y-m-d H:i:s', time()) . '  timing data success :' . $value);
                            }
                        }
                    } else {
                        $this->info('timing data format  error  :' . $value);
                    }
                } catch (Exception $e) {
//                    $this->info($e);
                    $this->info(vsprintf('process error ,error :%s,data :%s', array($e, json_encode($value))));

                }
            }
        }

        //endregion

    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('type', null, InputOption::VALUE_OPTIONAL, 'show all timing data', null),
            array('app', null, InputOption::VALUE_OPTIONAL, 'show db info', null),
        );
    }

}