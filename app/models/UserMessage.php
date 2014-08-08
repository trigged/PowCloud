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

    public $timestamps = true;

    protected $table = 'user_message';

    protected $connection = 'base';

    public static function processUserMessage($user_message, $use_id)
    {
        if ($user_message) {
            //process invite info
            if ($user_message->user_to == -1 && $user_message->action_type == self::ACTION_INVITE && $user_message->state == self::CAN_READE) {
                $atu = new ATURelationModel();
                $atu->user_id = $use_id;
                $atu->app_id = $user_message->app_id;
                $atu->save();
//                $user_message->delete();
            }
        }
    }

    public static function sendMsgByID($from_id, $app_id, $to_id, $mail_address, $action)
    {
        return self::saveMsg($from_id, $app_id, $to_id, $mail_address, $action);
    }

    static function saveMsg($from_id, $app_id, $to_id, $mail_address, $action)
    {
        $message = new UserMessage();
        $message->user_from = $from_id;
        $message->user_to = $to_id;
        $message->app_id = $app_id;
        if ($action === self::ACTION_INVITE) {
            $message->action_type = self::ACTION_INVITE;
        } elseif ($action === self::ACTION_REMOVE) {

            $message->action_type = self::ACTION_REMOVE;
        }
        $message->mail_address = $mail_address;
        $message->state = UserMessage::CAN_READE;
        $message->save();
        //todo update cache
        return $message->id;

    }

    public static function sendMsgByMail($from_id, $app_id, $mail_address, $action)
    {
        $invited_user = User::checkExistsByMail($mail_address);
        if ($invited_user === false) {
            //todo send invite mail
            self::sendMail($mail_address, self::saveMsg($from_id, $app_id, -1, $mail_address, $action));
            return '对方还不是我们的用户呀,请少侠放心,我们已经通过龙门镖局快马加鞭的把邮件发送到对方信箱了!';
        }
        return self::saveMsg($from_id, $app_id, $invited_user->id, $mail_address, $action);
    }

    static function sendMail($send_to, $msg_id)
    {
        $token = \Utils\UseHelper::makeToken(time(), \Utils\UseHelper::$default_key);
        $token .= $msg_id;
        //todo user may send many times so you need check first
        $url = URL::action('UserMessageController@receive') . '?sed=' . urlencode($token);
        \Utils\CMSLog::debug(sprintf('we send mail to %s,and build token was :%s,encode token :%s', $send_to, $token, urlencode($token)));
        return Mail::send('emails.info', array('url' => $url), function ($message) use ($send_to) {
            $message->to($send_to)->subject('Pow Server 邀请!');
        });

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