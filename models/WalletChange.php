<?php

namespace app\models;

use Yii;
use yii\base\UnknownPropertyException;
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
class WalletChange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_change';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
        //todo replace in component - change wallet value
        $lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$lastWallet instanceof Wallet) {
            return false;
        }
        try {
            $entity_name = $this->entity_name;

            $changedValue = $lastWallet->{$entity_name};
            $newWallet = new Wallet();
            $newWallet->attributes = $lastWallet->attributes;
            $newWallet->{$entity_name} = $changedValue + $this->change_value;
        } catch (UnknownPropertyException $exception) {
            return false;
        }

        if (!$newWallet->save()) {
            return false;
        }

        $this->wallet_id = $newWallet->id;
        $this->created_at = new Expression('NOW()');

        return true;
    }
}
