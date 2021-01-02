<?php

use yii\db\Migration;

/**
 * Handles the creation of table `wallet_change`.
 */
class m210102_122003_create_wallet_change_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('wallet_change', [
            'id' => $this->primaryKey(),
            'entity_name' => $this->string(100),
            'wallet_id' => $this->integer(),
            'change_value' => $this->integer(),
            'comment' => $this->string(1000),
            'created_at' => $this->dateTime()->defaultExpression('NOW()')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('wallet_change');
    }
}
