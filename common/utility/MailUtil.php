<?php

/**
 * Created by PhpStorm.
 * User: Iven.Wu
 * Date: 2017-01-13
 * Time: 15:32
 */

namespace common\utility;

use common\models\Channel;
use common\models\Deliver;
use common\models\LogAutoCheck;
use common\models\SendMailLog;
use Yii;

class MailUtil
{

    public static function sendCreateChannel(Channel $channel)
    {
        $mail = Yii::$app->mailer->compose('channel_created', ['channel' => $channel]);
        $mail->setTo($channel->email);
        if (isset($channel->om0)) {
            $mail->setCc($channel->om0->email);
        }
        $mail->setSubject('TR account create success');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'channel create', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
    }

    public static function sendSTSCreateMail(Deliver $deliver)
    {
        $mail = Yii::$app->mailer->compose('sts_created', ['deliver' => $deliver]);
        $mail->setTo($deliver->channel->email);
        $mail->setSubject('TR New Campaign');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 's2s create', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
    }

    /**
     * @param Channel $channel
     * @param Deliver[] $delivers
     * @return string
     */
    public static function sendStsChannelMail($channel, $delivers)
    {
        $mail = Yii::$app->mailer->compose('sts_channel_created', ['delivers' => $delivers, 'channel' => $channel]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
        $mail->setSubject('New Offers from TR');
        $isSend = 0;
        if ($mail->send()) {
            foreach ($delivers as $deliver) {
                $deliver->is_send_create = 1;
                $deliver->save();
            }
            $isSend = 1;
        }
        $param = array('type' => 's2s create', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return "Channel " . $channel->id . ' send success!';
        } else {
            return "Channel " . $channel->id . ' send Fail!';
        }
    }


    /**
     * @param Deliver $deliver
     * @return string
     */
    public static function capUpdate($deliver)
    {
        $channel = Channel::findIdentity($deliver->channel_id);
        $mail = Yii::$app->mailer->compose('update_cap', ['deliver' => $deliver]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
        $mail->setSubject('Cap Update - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'cap update', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Deliver $deliver
     * @return string
     */
    public static function payoutUpdate($deliver)
    {

        $channel = Channel::findIdentity($deliver->channel_id);
        $mail = Yii::$app->mailer->compose('update_payout', ['deliver' => $deliver]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
        $mail->setSubject('Payout Update - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'update_payout', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Deliver $deliver
     * @return string
     */
    public static function paused($deliver)
    {

        $channel = Channel::findIdentity($deliver->channel_id);
        $mail = Yii::$app->mailer->compose('pause', ['deliver' => $deliver]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
        $mail->setSubject('Pausing Campaign - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'pause', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param LogAutoCheck[] $checks
     */
    public static function autoCheckCvr($checks)
    {
        $mail = Yii::$app->mailer->compose('auto_check', ['checks' => $checks]);
        $mail->setTo('operations@traffic.red');
        $mail->setSubject('Anticheat - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'autoCheck', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            foreach ($checks as $check) {
                $check->is_send = 1;
                $check->save();
            }
        }
    }
    /*
    * @param Deliver $deliver
    * @return string
    */
    public static function updateGeo($deliver)
    {

        $channel = Channel::findIdentity($deliver->channel_id);
        $mail = Yii::$app->mailer->compose('updateGeo', ['deliver' => $deliver]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
        $mail->setSubject('Geo Update - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'updateGeo', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }

    /*
   * @param Deliver $deliver
   * @return string
   */
    public static function updateCreativeLink($deliver)
    {
        $channel = Channel::findIdentity($deliver->channel_id);
        $mail = Yii::$app->mailer->compose('update_creative_link', ['deliver' => $deliver]);
        $mail->setTo($channel->email);
        $cc = array($channel->om0->email);
        if (!empty($channel->cc_email) && !empty(explode(';', $channel->cc_email))) {
            $cc = array_merge($cc, explode(';', $channel->cc_email));
        }
        $mail->setCc($cc);
//        $mail->setTo('2539131080@qq.com');
        $mail->setSubject('Creative Update - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'update_creative_link', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param LogAutoCheck[] $checks
     */
    public static function autoCheckCap($checks)
    {
        $mail = Yii::$app->mailer->compose('over_cap', ['checks' => $checks]);
        $mail->setTo('operations@traffic.red');
        $mail->setSubject('Over Cap Alarm - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'autoCheck', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            foreach ($checks as $check) {
                $check->is_send = 1;
                $check->save();
            }
        }
    }

    public static function sendGoodOffers($campaigns,$channel)
    {
//        $user_id = Yii::$app->user->id;
        $mail = Yii::$app->mailer->compose('good_campaign', ['campaigns' => $campaigns,'channel' =>$channel ]);
//        $mail->setTo("2539131080@qq.com");
        $mail->setTo($channel->email);
        if (isset($channel->om0)) {
            $mail->setCc($channel->om0->email);
        }

        $mail->setSubject('Good Offers Recommendation - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'send good offers', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $advertiser
     * @return bool
     */
    public static function numberConfirm($advertiser)
    {
        $mail = Yii::$app->mailer->compose('number_confirm', ['advertiser' => $advertiser]);
        $mail->setTo($advertiser->email);
        $cc = array($advertiser->bd0->email);
        if (!empty($advertiser->cc_email) && !empty(explode(';', $advertiser->cc_email))) {
            $cc = array_merge($cc, explode(';', $advertiser->cc_email));
        }
        $mail->setCc($cc);
//        $mail->setTo("2539131080@qq.com");
        $mail->setSubject('Number Confirm - TR');
        $isSend = 0;
        if ($mail->send()) {
            $isSend = 1;
        }
        $param = array('type' => 'cap update', 'isSend' => $isSend);
        SendMailLog::saveMailLog($mail, $param);
        if ($isSend) {
            return true;
        } else {
            return false;
        }
    }
}