<?php

namespace app\components;

use Yii;

class TelegramService
{
    public function sendMessage($text)
    {
        file_get_contents(
            Yii::$app->params['telegramFinanceBot'] . "/sendmessage?chat_id=" . Yii::$app->params['telebotChatId'] . "&text=" . $text
        );
    }
}
