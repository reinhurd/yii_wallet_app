<?php

namespace app\components;

use Yii;

class TelegramService
{
    private $chatId;
    private $urlTelegramBotSendMethod;

    public function __construct()
    {
        $this->chatId = Yii::$app->params['telebotChatId'];
        $this->urlTelegramBotSendMethod = Yii::$app->params['telegramFinanceBot'] . "sendmessage";
    }

    public function sendMessage($text)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlTelegramBotSendMethod);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array(
                'chat_id' => $this->chatId,
                'text' => $text
            ))
        );
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }
}
