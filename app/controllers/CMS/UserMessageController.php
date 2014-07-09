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
        $action = Input::get("action");
        $from_id = $this->getCurrentUserID();
        if ($action !== UserMessage::ACTION_REMOVE && $action !== UserMessage::ACTION_INVITE) {
            //return error
            $this->ajaxResponse('', 'fail', '请不要尝试未知动作啊');
        }
        $result = UserMessage::sendMsgByMail($from_id, $email, $action);
        if ($result !== true) {
            $this->ajaxResponse('', 'fail', $result);
        }
        $this->ajaxResponse(array(), 'success', '邀请发送成功,等待回应中', 'DashBoardController@index');
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