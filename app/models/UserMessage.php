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

    const ACTION_FORGET_PASSWORD = 1003;

    const ACTION_ACTIVE = 1004;

    const CAN_READE = 1010;

    const CANT_READE = 1011;

    public $timestamps = true;

    protected $softDelete = true;

    protected $table = 'user_message';

    protected $connection = 'base';

    public static function processUserMessage(Eloquent $user_message, $use_id)
    {
        if ($user_message) {
            //process invite info
            if ($user_message->user_to == -1 && $user_message->action_type == self::ACTION_INVITE) {
                $atu = ATURelationModel::withTrashed()->where('user_id', $use_id)->where('app_id', $user_message->app_id)->first();
                if (!$atu || !$atu->exists) {
                    $atu = new ATURelationModel();
                    $atu->user_id = $use_id;
                    $atu->app_id = $user_message->app_id;
                    $atu->save();
                }
            } elseif ($user_message->action_type == self::ACTION_REMOVE) {
                $atu = ATURelationModel::where('user_id', $use_id)->where('app_id', $user_message->app_id);
                $atu->delete();
                return '找不到用户信息';
            }

//            $user_message->softDelete();
        }
        return '找不到用户信息';
    }

    public static function sendMsgByID($from_id, $app_id, $to_id, $mail_address, $action)
    {
        return self::buildMsg($from_id, $app_id, $to_id, $mail_address, $action);
    }

    static function buildMsg($from_id, $app_id, $to_id, $mail_address, $action)
    {
        $message = new UserMessage();
        $message->user_from = $from_id;
        $message->user_to = $to_id;
        $message->app_id = $app_id;
        $message->action_type = $action;
        $message->mail_address = $mail_address;
        $message->save();
        return $message->id;

    }

    public static function processMessageByID($from_id, $app_id, $to_id, $action)
    {
        $invited_user = User::checkExistsByID($to_id);
        if ($invited_user === false) {
            //todo send invite mail
            return '江湖上找不到此人!!!';
        }
        self::buildMsg($from_id, $app_id, $invited_user->id, $invited_user->email, $action);
        return '处理成功';
    }

    public static function processMessageByMail($from_id, $app_id, $mail_address, $action)
    {
        $invited_user = User::checkExistsByMail($mail_address);
        if ($invited_user === false) {
            //todo send invite mail
            $msg_id = self::buildMsg($from_id, $app_id, -1, $mail_address, $action);
            self::sendMail(self::buildSedUrl(URL::action('UserMessageController@receive'), $mail_address, $msg_id), $mail_address);
            return '对方还不是我们的用户呀,请少侠放心,我们已经通过龙门镖局快马加鞭的把邮件发送到对方信箱了!';
        } else {
            if(!ATURelationModel::hasAccessRight($app_id,$invited_user->id)){
                $atu = new ATURelationModel();
                $atu->user_id = $invited_user->id;
                $atu->app_id = $app_id;
                $atu->save();
                return "添加成功";
            }
            return "用户已经加入此APP, 请不要重复添加";

        }
    }

    static function sendMail($url, $send_to)
    {
        \Utils\CMSLog::debug(sprintf('send mail to: %s, url :%s', $send_to, $url));
        return Mail::send('emails.info', array('url' => $url), function ($message) use ($send_to) {
            $message->to($send_to)->subject('Pow Cloud 信使!');
        });

    }

    static function buildSedUrl($url, $send_to, $msg_id)
    {
        $token = \Utils\UseHelper::makeToken(time() . $msg_id, \Utils\UseHelper::$default_key);
        $sed_url = $url . '?sed=' . urlencode($token);
        \Utils\CMSLog::debug(sprintf('we send mail to %s,and build token was :%s,encode token :%s', $send_to, $token, urlencode($token)));
        return $sed_url;
    }

    static function checkSed($sed, $delete_msg_b = false)
    {

        $message = \Utils\UseHelper::checkToken($sed, \Utils\UseHelper::$default_key);
        $message_id = substr($message, 10);
        $timespan = substr($message, 0, 10);
        $current = time();
        if ($current - $timespan > 60 * 15) {
            return '连接已经过了安全期,已经失效,请重新提交请求 :(';
        }

        $user_message = UserMessage::find($message_id);
        if (!$user_message->exists) {
            return '连接已经被使用过了,请重新提交请求 :(';
        }
        if ($delete_msg_b) {
            $user_message->delete();
        }
        return $user_message;
    }

    public static function checkMessageExists($from_id, $to_id, $action)
    {
        $message_count = UserMessage::where('user_from', $from_id)->where('user_to', $to_id)->where('action_type', $action)->where("state", self::CAN_READE)->count();
        if ($message_count < 0) {
            return false;
        }
        return true;
    }


    public  static function buildActiveEmail($user_id,$email){
        $user_active = self::buildMsg($user_id, -1, -1, $email, self::ACTION_ACTIVE);
        self::sendMail(UserMessage::buildSedUrl(URL::action('UserMessageController@receive'), $email, $user_active), $email);
    }

}