<?php

namespace app\components;

use Yii;

class TelegramService
{
    const COMMAND_HELP = '/help';

    public function sendMessage($text)
    {
        $url = Yii::$app->params['telegramFinanceBot'] . "sendmessage";

        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array(
                'chat_id' => Yii::$app->params['telebotChatId'],
                'text' => $text
            ))
        );
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }
}
