<?php


use Operator\ReadApi;

class MonitorController extends \BaseController
{
    public $nav = 'monitor';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('cms.system', array());
    }

    public function mysql()
    {
        $this->menu = 'monitor.mysql';
        try {

            $tablesInfo = DB::select(DB::raw('show table status'));

        } catch (PDOException $exception) {

            return Response::make('Database error!' . $exception->getCode());
        }

        return $this->render('monitor.mysql', array(
            'tablesInfo' => $tablesInfo
        ));
    }

    public function redis()
    {
        $this->menu = 'monitor.redis';

        try {
            $redisInfo = ReadApi::redis()->info();
            $beforePing = microtime(true);
            ReadApi::redis()->ping();
            $afterPing = microtime(true);
            $pingTime = $afterPing - $beforePing;
            $redisInfo['ping'] = $pingTime;
        } catch (RedisException $exception) {

            return Response::make('Redis error!' . $exception->getCode());
        }

        return $this->render('monitor.redis', array(
            'redisInfo' => $redisInfo
        ));
    }

    public function cache()
    {
        $this->menu = 'monitor.cache';
        $tables = SchemaBuilder::all();

        $result = array();
        $result['route'] = array('name' => '路由');
        $current = $count = 0;
        $miss = '';
        foreach ($tables as $table) {

            $path = $table->path['name'];
            if ($path && $path != -1) {
                $count += 1;
                if (RouteManager::findController($path)) {
                    $current += 1;
                    continue;
                }
                $miss .= $table->table_name . $path . ' :' . "\r\n";
            }
        }
        $result['route']['value'] = (string)$current . '/' . (string)$count;
        $result['route']['state'] = $current == $count;
        return $this->render('monitor.cache', array(
            'result' => $result,
            'miss'   => $miss

        ));
    }

    public function api()
    {
        $this->menu = 'monitor.api';

        $methods = array('index', 'create', 'delete', 'show');
        $tables_name = SchemaBuilder::lists('table_name', 'id');

        $date_begin = '';
        $date_end = '';

        $apiData = array();

        foreach ($tables_name as $table_name) {
            foreach ($methods as $method) {
                $apiInfo = \Operator\ReadApi::getApiInfo($table_name, $method);
                foreach ($apiInfo as $key => $value) {
                    list($date, $type) = explode(':', $key);
                    if ($date == date('Y-m-d')) {
                        $apiData[$table_name][$method][$type] = $value;
                    }
                }
            }
        }

        return $this->render('monitor.api', array(
            'apiData'  => $apiData,
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}