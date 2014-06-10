<?php


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class zhejiang_cb_ott extends Command
{


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    #region BIP API

    /**
     * 浙江移动流量统计月表
     * @var string
     */
    private $flow_monthly = 'ott_atv_ipd9026_flow_monthly/140526/140602/';

    /**
     * 浙江移动分类别流量统计月表
     * @var string
     */
    private $category_flow_monthly = 'ott_atv_ipd9026_type_monthly/140526/140602/';

    /**
     * 浙江移动分类top10月表
     * @var string
     */
    private $category_top_monthly = 'ott_atv_ipd9026_category_top10_monthly/140526/140602/';

    /**
     * 浙江移动流量统计周表
     * @var string
     */
    private $flow_weekly = 'ott_atv_ipd9026_flow_weekly/140526/140602/';

    /**
     * 浙江移动分类别流量统计周表
     * @var string
     */
    private $category_flow_weekly = 'ott_atv_ipd9026_type_weekly/140526/140602/';

    /**
     * 浙江移动分类top10周表
     * @var string
     */
    private $category_top_weekly = 'ott_atv_ipd9026_category_top10/140526/140602/';

    /**
     * 浙江移动流量统计日表
     * @var string
     */
    private $flow_daily = 'ott_atv_ipd9026_flow_daily/140526/140602/';

    /**
     * 浙江移动分类top10日表
     * @var string
     */
    private $category_top_daily = 'ott_atv_ipd9026_category_top10_dayily/140526/140602/';

    /**
     * 浙江移动分类别流量统计日表
     * @var string
     */
    private $category_flow_daily = 'ott_atv_ipd9026_type_daily/140526/140602/';

    #endregion

    private $bip_domain;

    public function __construct()
    {
        parent::__construct();
        $this->bip_domain = Config::get('app.bip', 'http://api.bip.idc.pplive.cn/bip.appserver/json/');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info("you call me");
        $app_id = $this->option('app');
        $this->info($app_id);
        if (!$app_id) {
            $this->info('missing app params');
            return;
        }
        \Utils\AppChose::updateConf($app_id);

        $today = date('y-m-d', time());

        $last_week_start = date('y-m-d', strtotime("last week"));
        $last_week_end = date('y-m-d', strtotime("last week +6 day"));

        $last_month_start = date('y-m-d', strtotime("last month"));
        $last_month_end = date('y-m-t', strtotime("-1 month"));

        $this->getDailyData();

    }

    function getDailyData()
    {

        if (\Utils\DBMaker::checkTable('category_flow_daily')) {
            $url = $this->bip_domain . $this->category_flow_daily;
            $category_flow_daily = Utils\NetHelpers::call_api($url);
            var_dump($category_flow_daily);
            var_dump(json_decode($category_flow_daily));
            if ($category_flow_daily = json_decode($category_flow_daily)) {
                var_dump($category_flow_daily);
            }

        } else {
            var_dump('no data');
        }

    }

    function getWeeklyData()
    {

    }

    function getMonthData()
    {

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