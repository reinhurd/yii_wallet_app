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

    public function actionSetWalletChange()
    {
        $changeValue = Yii::$app->request->get('change_value');
        $comment = Yii::$app->request->get('comment');
        $entityCode = Yii::$app->request->get('entity_code');

        if (!isset($entityCode) || !isset($changeValue)) {
            throw new InvalidArgumentException();
        }

        $entityName = Wallet::getFieldByCode()[$entityCode] ?? null;
        if ($entityName === null) {
            throw new InvalidArgumentException();
        }

        $newWalletChange = new WalletChange();
        $newWalletChange->entity_name = $entityName;
        $newWalletChange->change_value = $changeValue;
        $newWalletChange->comment = $comment;

        if (!$newWalletChange->save()) {
            print_r($newWalletChange->getErrors());
            return false;
        }

        $this->telegramService->sendMessage('Success!');

        return $newWalletChange;
    }
}

