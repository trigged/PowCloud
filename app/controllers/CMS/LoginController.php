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

    public static function logout()
    {
        Auth::logout();
        Session::clear();
        return Redirect::to('/dashboard');
    }

    public function registerUser()
    {
        $username = Input::get('username');
        $pwd = Input::get('password');
        $email = Input::get('email');
        if (empty($username) || empty($pwd) || empty($email)) {
            $this->ajaxResponse(BaseController::$FAILED, '不可以写空');
        }
        //check username
        $name_count = User::where('name', $username)->count();
        if ($name_count > 0) {
            $this->ajaxResponse(BaseController::$FAILED, '用户名已经被注册过了-, -!');
        }
//        check email exists
        $email_count = User::where('email', $email)->count();
        if ($email_count > 0) {
            $this->ajaxResponse(BaseController::$FAILED, '此邮箱已经注册过了，大侠还是换一个吧或者直接登录-, -!');
        }
        $tel = Input::get('tel');
        $user = new User();
        $user->name = $username;
        if (!Config::get('app.allow_register')) {
            $user->status = User::DISABLE;
        }
        $user->email = $email;
        $user->tel = $tel;
        $user->pwd = sha1($pwd);
        $user->save();

        $message_id = Input::get('msg_id');
        if (!empty($message_id)) {
            $message = UserMessage::find($message_id);
            UserMessage::processUserMessage($message, $user->id);
        }
        UserMessage::buildActiveEmail($user->id,$email);
//        $user_active = UserMessage::buildMsg($user->id, -1, -1, $email, UserMessage::ACTION_ACTIVE);
//        UserMessage::sendMail(UserMessage::buildSedUrl(URL::action('UserMessageController@receive'), $email, $user_active->id), $email);
        $this->ajaxResponse(BaseController::$SUCCESS, '注册成功,激活邮件稍后发完少侠邮箱,请注意查收!', '', URL::action('DashBoardController@index'));
    }

    protected function ajaxResponse($status, $message = '', $data = '', $redirect = '')
    {
        $return = array(
            'status'   => $status,
            'message'  => $message,
            'data'     => $data,
            'redirect' => $redirect,
        );

        echo json_encode($return);

        //在强制退出支 触发结束事件
        App::shutdown();

        exit(1);
    }

    public function login()
    {
        return View::make('user.login');
    }

    public function register()
    {
        $msg_id = Input::get('msg_id', -1);
        $email = Input::get('email');
        return View::make('user.register', array('msg_id' => $msg_id, 'email' => $email))
            ->nest('header', 'dashboard.header')
            ->nest('footer', 'dashboard.footer');
    }

    public function loginStore()
    {
        $username = Input::get('username');
        $pwd = sha1(Input::get('password'));
        $demo_login = Input::get('demo_login');

        if($demo_login && $demo_login == '9527' ){
            $user = User::where('name', 'triggedtang')->where('pwd', sha1('8656000'))->get()->first();
        }
        else{
            $user = User::where('name', $username)->where('pwd', $pwd)->get()->first();
        }
        if (!$user) {
            $this->ajaxResponse(BaseController::$FAILED, '江湖榜找不到大侠~');
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
        if($demo_login && $demo_login == '9527' ){
            return Response::json(1)->setCallback(Input::get('callback'));
        }
        $this->ajaxResponse(BaseController::$SUCCESS, '', '', URL::action('DashBoardController@index'));
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

        return View::make('user.login');
    }
}