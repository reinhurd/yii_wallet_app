<?php
namespace app\controllers;

use app\models\Wallet;
use app\models\WalletChange;
use app\components\TelegramService;
use yii\base\InvalidArgumentException;
use yii\rest\ActiveController;
use Yii;

class WapiController extends ActiveController
{
    public $modelClass = 'app\models\Wallet';
    private $lastWallet;
    private $telegramService;

    public function __construct($id, $module, TelegramService $telegramService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->telegramService = $telegramService;
    }

    public function actionGetLastWalletInfo()
    {
        return $this->lastWallet;
    }

    /*
     * todo create global endpoint to telegram, with help, and call this method from there
     */
    public function actionSetWalletChange()
    {
        $message = Yii::$app->request->post('message');
        try {
            $params = $this->telegramService->parseCommand($message['text']);

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
}
