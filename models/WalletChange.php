<?php

namespace app\models;

use Yii;

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
}
