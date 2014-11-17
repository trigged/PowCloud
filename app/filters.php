<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/
App::before(function (\Illuminate\Http\Request $request) {
    $path = $request->getPathInfo();
    $method = $request->getMethod();
    $req = $request->getRequestUri();
    $handler = sha1($req . time());
    $token = Input::get('token');
    if (empty($token)) {
        \Utils\AppChose::updateCache((int)\Utils\UseHelper::checkToken(Config::get('app.default_token'), \Utils\UseHelper::$default_key));
    }
    if ($token) {
        $app_id = (int)\Utils\UseHelper::checkToken($token, \Utils\UseHelper::$default_key);
        if (empty($app_id)) {
            return Response::json(array(
                'code'    => -1,
                'message' => 'request not found!',
                'data'    => array()), 404);
        }
        \Utils\AppChose::updateConf($app_id);
    }

    Log::info(vsprintf('Handler :%s, ReqUri:%s', array($handler, $req)));
    \Utils\CMSLog::$requestHandler = $handler;
    if ('/' === $path)
        return;

    if ($result = RouteManager::findController($path)) {
        //if cache has this route ,this req must be api
        if (empty($result) or !isset($result['model']) or !isset($result['right'])) {
            return Response::json(array(
                'code'    => -1,
                'message' => 'request not found!',
                'data'    => array()), 404);
        }
        $request->request->add(array('_db' => 'user'));
        $request->request->set('_model', $result['model']);
        $request->request->set('_right', $result['right']);
        $request->request->set('expire', $result['expire']);
    } elseif ($token && !starts_with($path, '/api_')) {
        return Response::json(array(
            'code'    => -1,
            'message' => 'request not found  or path missing',
            'data'    => array()), 404);

    }
//    elseif (true) {
//        Route::$method($path, 'DashBoardController@index');
//    }
});


App::after(function ($request, $response) {
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function () {
    if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function () {
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function () {
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

if (Config::get('app.debug', false)) {
    Event::listen('illuminate.query', function ($query, $bindings) {

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else if (is_string($binding)) {
                $bindings[$i] = "'$binding'";
            }
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);

        \Utils\CMSLog::debug($query);
    });
}
