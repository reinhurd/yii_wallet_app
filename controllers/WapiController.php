<?php
namespace app\controllers;

use app\models\Wallet;
use yii\rest\ActiveController;

class WapiController extends ActiveController
{
    public $modelClass = 'app\models\Wallet';
    private $lastWallet;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }

    public function actionGetLastWalletInfo()
    {
        return $this->lastWallet;
    }

    public function actionSetWalletChange()
    {
        $lastEntry = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$lastEntry instanceof Wallet) {
            return 0;
        }
    }
}

