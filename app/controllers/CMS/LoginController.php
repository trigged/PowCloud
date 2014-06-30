<?php

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/6/13
 * Time: 1:57 PM
 * To change this template use File | Settings | File Templates.
 */

class LoginController extends Controller
{

    const AREA_IP = 'http://ip.taobao.com/service/getIpInfo.php?ip=';

    public function registerUser()
    {
        $username = Input::get('username');
        $pwd = Input::get('password');
        $email = Input::get('email');
        //check username
        $name_count = User::where('name', $username)->count();
        if ($name_count > 0) {
            $this->ajaxResponse('', '用户名已经存在，大侠换个名字吧', '/register');
        }
        //check email exists
        $email_count = User::where('email', $email)->count();
        if ($email_count > 0) {
            $this->ajaxResponse('', '改邮箱已经注册过了，大侠还是换一个吧或者直接登录', '/register');
        }
        $tel = Input::get('tel');
        $user = new User();
        $user->name = $username;
        #$user->token = \Utils\UseHelper::makeToken($user->id);
        if (!Config::get('app.allow_register')) {
            $user->status = User::DISABLE;
        }
        $user->email = $email;
        $user->tel = $tel;
        $user->pwd = sha1($pwd);
        $user->save();
        $this->ajaxResponse('', '注册成功', '/dashboard');
    }

    protected function ajaxResponse($data = array(), $status = 'success', $message = '', $redirect = '')
    {
        $return = array(
            'status'   => $status,
            'message'  => $message,
            'data'     => $data,
            'redirect' => $redirect,
        );

//        if($successRedirect || $failRedirect)
//            \Utils\Env::messageTip("messageTip", $status, $message);
        echo json_encode($return);

        //在强制退出支 触发结束事件
        App::shutdown();

        exit(1);
    }

    public function login()
    {
        return View::make('cms.login');
    }

    public function register()
    {
        return View::make('cms.register');
    }

    public function loginStore()
    {
        $username = Input::get('username');
        $pwd = sha1(Input::get('password'));

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
//        PHPCas::logout();
        return Redirect::to('/dashboard');
    }
}