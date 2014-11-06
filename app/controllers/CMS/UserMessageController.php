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
            header('Location:' . URL::action('LoginController@register', array('msg_id' => $user_message->id, 'email' => $user_message->mail_address)));
        }
        return $this->location(-1,$user_message);
//        header('Location:' . URL::action('LoginController@register', array('msg_id' => $user_message->id, 'email' => $user_message->mail_address)));
//        $this->ajaxResponse(BaseController::$FAILED, $user_message, '', URL::action('DashBoardController@index'));
    }

    public function forget()
    {
        $email = Input::get('email');

        $user = User::checkExistsByMail($email);
        if (!$user) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }


    }

    public function resetPassword()
    {
        $sed = Input::get('sed');
        if (empty($sed)) {
            return 'error';
        }


        $type = Input::get('type');
        //reset
        if ($type == 1) {

        } else {
            //forget


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