<?php
namespace app\controllers;

use app\components\WalletService;
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
    private $walletService;
    private const COMMAND_HELP = '/help';
    private const COMMAND_GET_INFO_ABOUT_WALLET = '/info';
    private const COMMAND_RESET = '/reset';
    private const COMMAND_RESET_NEW = '/reset_new';
    private const COMMAND_DEFAULT = '/default';

    public function __construct(
        $id,
        $module,
        TelegramService $telegramService,
        WalletService $walletService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->telegramService = $telegramService;
        $this->walletService = $walletService;
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

            switch ($messageText) {
                case self::COMMAND_HELP:
                    $message = 'Первое слово - сумма с плюсом или минусом, второе - код денежного фонда, третье - коммент (не обязателен). Разделять пробелами';
                    $message .= PHP_EOL . 'Актуальные коды фондов' . json_encode(Wallet::getFieldByCode());
                    $this->telegramService->sendMessage($message);

                    return true;
                case self::COMMAND_GET_INFO_ABOUT_WALLET:
                    $message = 'Остаток денег на счете = ' . $this->walletService->getLastWalletInfo();
                    $this->telegramService->sendMessage($message);

                    return true;
                case self::COMMAND_RESET:
                    $this->walletService->resetWallets();
                    $message = 'Все кошельки очищены';
                    $this->telegramService->sendMessage($message);

                    return true;
                case self::COMMAND_RESET_NEW:
                    $params = $this->parseCommand($messageText, self::COMMAND_RESET_NEW);
                    $this->walletService->setNewWalletToEmptyBase($params);
                    $message = 'Все кошельки очищены и заданы новые значения';
                    $this->telegramService->sendMessage($message);

                    return true;
            }

            //if all OK
            $params = $this->parseCommand($messageText);
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
            $message = 'Error!';
            $this->telegramService->sendMessage($message);
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

    private function parseCommand(string $text, string $operationType = self::COMMAND_DEFAULT): array
    {
        $arrayValidCount = 0;
        $array = explode(' ', trim($text));
        switch ($operationType) {
            case self::COMMAND_DEFAULT:
                $arrayValidCount = 3;
                break;
            case self::COMMAND_RESET_NEW:
                $arrayValidCount = 7;
                break;
        }
        $currentCount = count($array);
        if ($currentCount !== $arrayValidCount) {
            $this->telegramService->sendMessage("Нужно точное количество ($arrayValidCount) слов. Прислано: $currentCount");
            throw new InvalidArgumentException();
        }

        return $array;
    }
}
