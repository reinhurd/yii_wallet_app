<?php

use yii\db\Migration;

/**
 * Handles the creation of table `wallet`.
 */
class m210102_121907_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('wallet', [
            'id' => $this->primaryKey(),
            'money_all' => $this->integer(),
            'money_everyday' => $this->integer(),
            'money_medfond' => $this->integer(),
            'money_long_clothes' => $this->integer(),
            'money_long_gifts' => $this->integer(),
            'money_long_reserves' => $this->integer(),
            'money_long_deposits' => $this->integer(),
            'money_credits' => $this->integer(),
            'last_update_date' => $this->dateTime()->defaultExpression('NOW()')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('wallet');
    }
}
