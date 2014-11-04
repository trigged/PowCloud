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
            $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//            $this->ajaxResponse(array('name' => 'test'), 'fail', '请求有误,请联系官方人员');
        }
        $user_id = Input::get('user_id');
        if ($action === UserMessage::ACTION_REMOVE && $user_id = Input::get('user_id')) {
            $result = UserMessage::processMessageByMail($from_id, $app_id, $email, $action);
        } else {
            if (empty($email)) {
                $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//                $this->ajaxResponse('', 'fail', '请输入邮箱地址');
            }
            $result = UserMessage::processMessageByMail($from_id, $app_id, $email, $action);
        }
        if ($result !== true) {
            $this->ajaxResponse(BaseController::$FAILED, $result);
//            $this->ajaxResponse('', 'fail', $result);
        }
        $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('DashBoardController@index'));
//        $this->ajaxResponse(array(), 'success', '邀请发送成功,等待回应中', 'DashBoardController@index');
    }

    public function receive()
    {
        $sed = Input::get('sed');
        if (empty($sed)) {
            return 'error';
        }
        $sed = urldecode($sed);
        $message_id = substr($sed, -1);
        $timespan = \Utils\UseHelper::checkToken(substr($sed, 0, -1), \Utils\UseHelper::$default_key);

        $current = time();
        $value = $current - $timespan;
        if ($current - $timespan > 60 * 1500) {
            return sprintf('time out %s,timespan: %s,and miss: %s', $current, $timespan, $value);
        }
        $user_message = UserMessage::find($message_id);
        if (!$user_message->exists) {
            return 'invite not exists';
        }
        //todo location to register and auto add to the app
        header('Location:' . URL::action('LoginController@register', array('msg_id' => $message_id, 'email' => $user_message->mail_address)));
    }

    public function forget()
    {
        $email = Input::get('email');
        $user = User::checkExistsByMail($email);
        if (!$user) {
//            return $this->ajaxResponse('', 'fail', '用户不存在');
        }


    }

    public function resetPassword()
    {
        $sed = Input::get('sed');
        if (empty($sed)) {
            return 'error';
        }
        $sed = urldecode($sed);
        $message_id = substr($sed, -1);
        $timespan = \Utils\UseHelper::checkToken(substr($sed, 0, -1), \Utils\UseHelper::$default_key);

        $current = time();
        $value = $current - $timespan;
        if ($current - $timespan > 60 * 1500) {
            return sprintf('time out %s,timespan: %s,and miss: %s', $current, $timespan, $value);
        }
        $user_message = UserMessage::find($message_id);
        if (!$user_message->exists) {
            return 'invite not exists';
        }
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