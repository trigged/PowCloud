<?php

class UserMessageController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function invite()
    {
        $email = Input::get("email");
        $app_id = Input::get("app_id");
        $action = (int)Input::get("action");
        $from_id = $this->getCurrentUserID();

        if ($action !== UserMessage::ACTION_REMOVE && $action !== UserMessage::ACTION_INVITE) {
            //not support action
            $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
        }
        if ($action === UserMessage::ACTION_REMOVE && $user_id = Input::get('user_id')) {
            //todo  notification not support ,so no need create message
            ATURelationModel::where('user_id', $user_id)->where('aap_id', $app_id)->delete();
            $result = true;
        } else {
            //invite
            if (empty($email)) {
                $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
            }
            $result = UserMessage::processMessageByMail($from_id, $app_id, $email, $action);
        }
        if ($result !== true) {
            $this->ajaxResponse(BaseController::$FAILED, $result);
        }
        $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('DashBoardController@index'));
    }

    public function receive()
    {

        $sed = Input::get('sed');
        $user_message = UserMessage::checkSed($sed);
        if (gettype($user_message) == 'object') {
            if ($user_message->action_type == UserMessage::ACTION_INVITE) {
                header('Location:' . URL::action('LoginController@register', array('msg_id' => $user_message->id, 'email' => $user_message->mail_address)));
            } elseif ($user_message->action_type == UserMessage::ACTION_ACTIVE) {
                $user = User::find($user_message->user_from);
                if ($user) {
                    $user->status = User::ENABLE;
                    $user->save();
                    return $this->location(1, '激活成功,欢迎少侠到来');
                }
            }

        }
        return $this->location(-1, $user_message);
    }

    public function reSendActiveMail()
    {
        if (!$user = Auth::user()) {
            return $this->location(-1, '请先登录!');
        }
        UserMessage::buildActiveEmail($user->id, $user->email);
        $this->ajaxResponse(BaseController::$SUCCESS, '邮件发送成功,稍后请检查邮箱,若长时间没有收到,请查看垃圾箱!','','index');
    }

    public function viewForget()
    {

        return $this->render('user.forget');
    }

    public function viewReset()
    {
        $sed = Input::get('sed');
        $params = array();
        if ($sed) {
            $user_message = UserMessage::checkSed($sed);
            if ($user_message->action_type == UserMessage::ACTION_FORGET_PASSWORD) {
                $params['action'] = UserMessage::ACTION_FORGET_PASSWORD;
                $params['msg_id'] = $user_message->id;
                $params['user_id'] = $user_message->user_from;
            }
        } else if (!Auth::check()) {
            return $this->location(-1, '请先登录!');
        }
        $params['status'] = Auth::user()->status;
        return $this->render('user.reset', $params);
    }

    public function forget()
    {
        $email = Input::get('email');
        $user = User::checkExistsByMail($email);
        if (!$user) {
            $this->ajaxResponse(BaseController::$FAILED, '找不到用户!');
        }
        $msg_id = UserMessage::buildMsg($user->id, -1, -1, $email, UserMessage::ACTION_FORGET_PASSWORD);
        $url = UserMessage::buildSedUrl(URL::action('UserMessageController@viewReset'), $email, $msg_id);
        UserMessage::sendMail($url, $email);
        \Utils\CMSLog::debug($url);
        $this->ajaxResponse(BaseController::$SUCCESS, '邮件发送成功,稍后请检查邮箱,若长时间没有收到,请查看垃圾箱!');
    }

    public function resetPassword()
    {
        $msg_id = Input::get('msg_id');
        $pwd = sha1(Input::get('new_pwd'));
        $old_pwd = sha1(Input::get('old_pwd'));

        if ($msg_id) {
            $user_message = UserMessage::find($msg_id);
            if ($user_message && $user_message->action_type == UserMessage::ACTION_FORGET_PASSWORD) {
                if ($user = User::find($user_message->user_from)) {
                    $user->pwd = $pwd;
                    $user->save();
                    $this->ajaxResponse(BaseController::$SUCCESS, '修改成功,请重新登陆,请妥善保管您的密码', '', URL::action('DashBoardController@index'));
                }
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
            }
        }

        if ($user = Auth::user()) {
            if ($user->pwd != $old_pwd) {
                $this->ajaxResponse(BaseController::$FAILED, '旧密码错误');
            }
            $user->pwd = $pwd;
            $user->save();
            LoginController::logout();
            $this->ajaxResponse(BaseController::$SUCCESS, '修改成功,请重新登陆,请妥善保管您的密码', '', URL::action('DashBoardController@index'));
        }
        return $this->location(-1, '请先登录!');

    }

    public function index()
    {
        //
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