<?php

namespace app\models;

use app\components\WalletService;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "wallet_change".
 *
 * @property int $id
 * @property string|null $entity_name
 * @property int|null $wallet_id
 * @property int|null $change_value
 * @property string|null $comment
 * @property string|null $created_at
 */
class WalletChange extends ActiveRecord
{
    public static function tableName()
    {
        return 'wallet_change';
    }

    public function rules()
    {
        return [
            [['wallet_id', 'change_value'], 'integer'],
            [['entity_name', 'change_value'], 'required'],
            [['created_at'], 'safe'],
            [['entity_name'], 'string', 'max' => 100],
            [['comment'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_name' => 'Entity Name',
            'wallet_id' => 'Wallet ID',
            'change_value' => 'Change Value',
            'comment' => 'Comment',
            'created_at' => 'Created At',
        ];
    }

    public function beforeSave($insert)
    {
        /** @var WalletService $walletService */
        $walletService = Yii::createObject(WalletService::class);
        $newWallet = $walletService->getBeforeSaveWalletChange($this);
        $savedWallet = $walletService->saveWallet($newWallet);
        if (!$savedWallet instanceof Wallet) {
            return false;
        }

        $this->wallet_id = $savedWallet->id;
        $this->created_at = new Expression('NOW()');

        return true;
    }
}
