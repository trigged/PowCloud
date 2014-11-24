<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::get('mail', function () {
    return Response::view('emails.info', array());
});

Route::get('403', function () {

    return Response::view('errors.403', array(), 403);
});

Route::get('nginx', function () {
    $request = Request::create('api/items', 'GET');
    return Route::dispatch($request)->getContent();
});


Route::resource('dashboard', 'DashBoardController');
Route::resource('app', 'AppController');
Route::resource('team', 'UserController');

Route::get('addMember', 'DashBoardController@addMember');
Route::post('delete', 'DashBoardController@delete');
Route::get('editApp', 'DashBoardController@editApp');
Route::post('storeMember', 'DashBoardController@storeMember');
Route::get('addApp', 'DashBoardController@addApp');
Route::post('storeApp', 'DashBoardController@storeApp');
Route::post('updateApp', 'DashBoardController@updateApp');

//user message
Route::any('user_message/invite', 'UserMessageController@invite');
Route::any('user_message/receive', 'UserMessageController@receive');
Route::get('user_message/forget', 'UserMessageController@viewForget');
Route::post('user_message/forget', 'UserMessageController@forget');
Route::get('user/info', 'UserMessageController@viewReset');
Route::post('user_message/reset', 'UserMessageController@resetPassword');
Route::any('user/resend', 'UserMessageController@reSendActiveMail');


//login
Route::get('register', 'LoginController@register');
Route::get('logout', 'LoginController@logout');
Route::get('login', 'LoginController@login');
Route::any('login/loginStore', 'LoginController@loginStore');
Route::post('login/registerUser', 'LoginController@registerUser');


//Host
Route::post('/host/restore/{id}', 'HostController@restore'); //恢复删除
Route::resource('/host', 'HostController');

//Model
Route::get('table/{table}/docs_html', 'SchemaBuilderController@docsHtml');
Route::get('docs_{table}', 'SchemaBuilderController@docs');
Route::any('table/options/{table}', 'SchemaBuilderController@tableOptions');
Route::get('tableSchema/{table}', 'SchemaBuilderController@tableSchema');
Route::any('tableSchema/{table}/addFiled', 'SchemaBuilderController@addField');
Route::delete('tableSchema/{table}/{field}', 'SchemaBuilderController@destroyField');
Route::resource('/table', 'SchemaBuilderController');
Route::get('table/api_info/{table}', 'SchemaBuilderController@apiInfo');

//Forms
Route::post('/forms/{form}/field/rank', 'FormsController@rank');
Route::get('forms', 'FormsController@forms');
Route::post('/form/storeField/{table}', 'FormsController@storeField');
Route::get('/form/addField/{table}', 'FormsController@addField');
Route::get('/form/addtiming/{table}', 'FormsController@addTiming');
Route::get('/form/deltiming/{table}', 'FormsController@delTiming');
Route::resource('/form', 'FormsController');

//path
Route::resource('/path', 'PathController');

//data link
Route::post('/data_link/check', 'DataLinkController@checkMappingItem');
Route::post('/data_link/delete', 'DataLinkController@deleteItem');

Route::resource('/data_link', 'DataLinkController');

//Record
Route::get('/record/{record}/detail', 'RecordController@detail');
Route::post('/record/{record}/recover', 'RecordController@recover');
Route::delete('/record/{record}', 'RecordController@destroy');

//Ext
Route::post('toTop', 'ExtController@top');
Route::get('suggest', 'ExtController@suggest');
Route::post('ajax_analyse', 'ExtController@ajax_analyse');
Route::get('geo_areal_filter', 'ExtController@areaList');
Route::get('geo_areal_radio_filter', 'ExtController@areaRadioList');
Route::post('table_rank_sort', 'ExtController@rank');
Route::get('formhtml', 'ExtController@formHtml');


//Geo
Route::put('geo_shortcut/{id}/update', 'GeoController@shortcut_update');
Route::get('geo_shortcut/{id}/edit', 'GeoController@shortcut_edit');
Route::delete('geo_shortcut/{id}', 'GeoController@shortcut_destroy');
Route::post('geo_shortcut', 'GeoController@shortcut_store');
Route::get('geo_shortcut/create', 'GeoController@shortcut_create');
Route::get('geo_shortcut', 'GeoController@shortcut');


//CMS

Route::post('cms/online', 'CmsController@online');
Route::post('cms/offline', 'CmsController@offline');

Route::get('/page/{page}', 'CmsController@page');
Route::post('/cache/refresh/{table}/{target}', 'CmsController@cacheRefresh');
Route::post('/cms/{table}/{id}/restore', 'CmsController@restore');
Route::get('/cms/{table}/{id}/detail', 'CmsController@detail');
Route::resource('/cms', 'CmsController');

//code edit widget
Route::get('widget/create', 'CodeFragmentController@createWidget');
Route::delete('widget/{widget}', 'CodeFragmentController@disableWidget');
Route::put('widget/{widget}', 'CodeFragmentController@updateWidget');
Route::post('widget/store', 'CodeFragmentController@storeWidget');
Route::get('widget/{widget}', 'CodeFragmentController@widgetDetail');
Route::get('widget/{widget}/edit', 'CodeFragmentController@editWidget');
Route::get('widget', 'CodeFragmentController@widget');

//Mysql Redis Execute
Route::any('execute/mysql', 'CodeFragmentController@mysql');
Route::any('execute/redis', 'CodeFragmentController@redis');

//code edit hook
Route::get('hook/create', 'CodeFragmentController@createHook');
Route::delete('hook/{hook}', 'CodeFragmentController@disableHook');
Route::put('hook/{hook}', 'CodeFragmentController@updateHook');
Route::post('hook/store', 'CodeFragmentController@storeHook');
Route::get('hook/{hook}', 'CodeFragmentController@hookDetail');
Route::get('hook/{hook}/edit', 'CodeFragmentController@editHook');
Route::get('hook', 'CodeFragmentController@hook');


Route::get('system', 'SystemController@system');
Route::resource('advanced', 'AdvancedController');

//monitor
Route::get('monitor/mysql', 'MonitorController@mysql');
Route::get('monitor/redis', 'MonitorController@redis');
Route::get('monitor/cache', 'MonitorController@cache');
Route::get('monitor/api', 'MonitorController@api');
Route::resource('monitor', 'MonitorController');


//user limit
Route::resource('limit', 'LimitController');
Route::get('limit/store', 'LimitController@store');
Route::get('limit/{limit}/setAdmin', 'LimitController@setAdmin');
Route::get('limit/{limit}/cancelAdmin', 'LimitController@cancelAdmin');
Route::get('limit/{limit}/handleUser', 'LimitController@handleUser');
Route::put('limit/{limit}', 'LimitController@update');
Route::put('limit/{limit}/updateUser', 'LimitController@updateUser');
Route::post('limit/searchUser', 'LimitController@searchUser');
Route::get('group', 'LimitController@group');
Route::get('user', 'LimitController@user');


Route::get('show_api', 'CmsController@show_api');
