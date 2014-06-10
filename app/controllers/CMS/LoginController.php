<?php
use Library\PHPCas;

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/6/13
 * Time: 1:57 PM
 * To change this template use File | Settings | File Templates.
 */

class LoginController extends MasterController
{

    const AREA_IP = 'http://ip.taobao.com/service/getIpInfo.php?ip=';

    public function register()
    {
        $username = Input::get('username');
        $pwd = Input::get('password');
        $email = Input::get('email');
        if (User::where('name', $username)->get()->exists()) {
            return Redirect::action('LoginController@login', array('info' => '用户名已经存在，大侠换个名字吧', 'code' => -1)); //todo
        }
        $user = new User();
        $user->name = $username;
        $user->email = $email;
        $user->pwd = sha1($pwd);
        $user->token = \Utils\UseHelper::makeToken($user->id);
        return Redirect::action('DashBoardController@index', array('info' => '注册成功！', 'code' => 1)); //todo
    }

    public function login()
    {
        return View::make('cms.login');
    }

    public function loginStore()
    {
        $username = Input::get('username');
        $pwd = sha1(Input::get('password'));
        $query = User::where('name', $username)->where('pwd', $pwd)->toSql();
        $user = User::where('name', $username)->where('pwd', $pwd)->get()->first();
        if (!$user) {
            return Redirect::action('LoginController@login', array('info' => '江湖榜找不到大侠，大侠还是去注册一个账号吧！', 'code' => -1));

        } else {
            $user->last_time = $user->updated_at;
            $area = $this->getLoginArea();
            if (!empty($area)) {
                $user->last_area = $area;
            }
            $user->updateTimestamps();
        }
        if ($user->save()) {
            Auth::login($user);
        }
        return Redirect::action('DashBoardController@index');
    }

    public function getLoginArea()
    {
        $ip = $this->getIP();
        if ($ip) {
            $ip = json_decode(@file_get_contents(self::AREA_IP . $ip), true);
            if ($ip && $ip['code'] == '1') {
                return false;
            }
            if (isset($ip['data']['city']) && isset($ip['data']['isp'])) {
                return $ip['data']['city'] . $ip['data']['isp'];
            }
        }
        return false;
    }

    public function getIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else return false;
        return $ip;
    }


    public function test()
    {
        if (isset($_POST['username'])) {
            var_dump('hello, world');
            exit;
        }

        return View::make('cms.login');
    }

    public function logout()
    {
        Auth::logout();
        Session::clear();
        PHPCas::logout();
        return Redirect::to('/');
    }

}