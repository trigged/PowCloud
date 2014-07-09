<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 7/3/14
 * Time: 6:01 PM
 * To change this template use File | Settings | File Templates.
 */

class UserMessage extends Eloquent
{
    const ACTION_INVITE = 1001;

    const ACTION_REMOVE = 1002;

    const CAN_READE = 1010;

    const CANT_READE = 1011;

    protected $timestamps = true;

    protected $table = 'user_message';

    protected $connection = 'base';

    public static function sendMsgByID($from_id, $to_id, $action)
    {
        return self::sendMsg($from_id, $to_id, $action);
    }

    static function sendMsg($from_id, $to_id, $action)
    {
        $message = new UserMessage();
        $message->user_from = $from_id;
        $message->user_to = $to_id;
        if ($action === self::ACTION_INVITE) {
            $message->action_type = self::ACTION_INVITE;
        } elseif ($action === self::ACTION_REMOVE) {

            $message->action_type = self::ACTION_REMOVE;
        }
        $message->state = UserMessage::CAN_READE;
        $message->save();
        return true;
    }

    public static function sendMsgByMail($from_id, $to_email, $action)
    {
        $invited_user = User::checkExistsByMail($to_email);
        if ($invited_user === false) {
            //todo send invite mail
            return '对方还不是我们的用户呀,请少侠放心,我们已经通过龙门镖局快马加鞭的把邮件发送到对方信箱了!';
        }
        return self::sendMsg($from_id, $invited_user->id, $action);
    }

    public static function checkMessageExists($from_id, $to_id, $action)
    {
        $message_count = UserMessage::where('user_from', $from_id)->where('user_to', $to_id)->where('action_type', $action)->where("state", self::CAN_READE)->count();
        if ($message_count < 0) {
            return false;
        }
        return true;
    }

}