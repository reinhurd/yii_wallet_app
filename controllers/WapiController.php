<?php
namespace app\controllers;

use app\models\Wallet;
use app\models\WalletChange;
use app\components\TelegramService;
use yii\base\InvalidArgumentException;
use yii\rest\ActiveController;
use yii\web\Response;
use Yii;

class WapiController extends ActiveController
{
    public $modelClass = 'app\models\Wallet';
    /** @var Wallet|null */
    private $lastWallet;
    private $telegramService;
    private const COMMAND_GET_INFO_ABOUT_WALLET = '/info';

    public function __construct($id, $module, TelegramService $telegramService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->telegramService = $telegramService;
    }

    //todo make new endpoint access through telegram webhooks
    public function actionGetLastWalletInfo()
    {
        return $this->lastWallet;
    }

    /*
     * todo create global endpoint to telegram, with help, and call this method from there
     */
    public function actionTelegram()
    {
        $message = Yii::$app->request->post('message');
        try {
            $messageText = $message['text'];
            $params = $this->parseCommand($messageText);
            if ($messageText === TelegramService::COMMAND_HELP) {
                $message = 'Первое слово - сумма с плюсом или минусом, второе - код денежного фонда, третье - коммент (не обязателен). Разделять пробелами';
                $message .= PHP_EOL . 'Актуальные коды фондов' . json_encode(Wallet::getFieldByCode());
                $this->telegramService->sendMessage($message);

                throw new InvalidArgumentException();
            } elseif ($messageText === self::COMMAND_GET_INFO_ABOUT_WALLET) {
                $message = 'Остаток денег на счете = ' . $this->lastWallet->money_all;
                $this->telegramService->sendMessage($message);

                throw new InvalidArgumentException();
            }
            $changeValue = $params[0];
            $entityCode = $params[1];
            $comment = $params[2];

            if (!isset($entityCode) || !isset($changeValue)) {
                throw new InvalidArgumentException();
            }

            $entityName = Wallet::getFieldByCode()[(int)$entityCode] ?? null;
            if ($entityName === null) {
                throw new InvalidArgumentException();
            }
        } catch (InvalidArgumentException $exception) {
            return true;
        }

        $newWalletChange = new WalletChange();
        $newWalletChange->entity_name = $entityName;
        $newWalletChange->change_value = $changeValue;
        $newWalletChange->comment = $comment;

        if (!$newWalletChange->save()) {
            print_r($newWalletChange->getErrors());
            return true;
        }
        $lastLastWallet = Wallet::find()->where(['id' => $newWalletChange->wallet_id])->one();
        $message = 'Success'  . $newWalletChange->id . ' ' . $newWalletChange->entity_name . ' New total sum' . $lastLastWallet->money_all;

        $this->telegramService->sendMessage($message);

        return $newWalletChange;
    }

    /*
     * todo method to set wallet values globally
     */
    public function actionResetWallet()
    {

    }

    private function parseCommand(string $text): array
    {
        $array = explode(' ', trim($text));
        if (count($array) !== 3) {
            $this->telegramService->sendMessage('Нужно три слова');
            throw new InvalidArgumentException();
        }

        return $array;
    }
}
