<?php

namespace app\components;

use app\models\Wallet;
use Yii;
use yii\base\InvalidArgumentException;

class TelegramService
{
    const COMMAND_HELP = '/help';

    public function sendMessage($text)
    {
        $url = Yii::$app->params['telegramFinanceBot'] . "sendmessage";

        $ch = curl_init();
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
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    public function parseCommand(string $text): array
    {


        $array = explode(' ', trim($text));
        if (count($array) !== 3) {
            $this->sendMessage('Нужно три слова');
            throw new InvalidArgumentException();
        }

        return $array;
    }
}
