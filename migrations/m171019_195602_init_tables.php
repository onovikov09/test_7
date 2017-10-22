<?php

use yii\db\Migration;

class m171019_195602_init_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'nickname' => $this->string(255)->notNull()->unique(),
            'balance' => $this->integer(11)->notNull()->defaultValue(0),
            'is_active' =>  $this->smallInteger()->notNull()->defaultValue(1),
        ]);

        $this->createTable('history', [
            'id' => $this->primaryKey(),
            'from_user' => $this->integer(11)->notNull(),
            'to_user' => $this->integer(11)->notNull(),
            'value' => $this->integer(11)->notNull(),
            'dt' =>  $this->integer(11)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-history-from_user',
            'history',
            'from_user',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-history-to_user',
            'history',
            'to_user',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-history-from_user',
            'history'
        );

        $this->dropForeignKey(
            'fk-history-to_user',
            'history'
        );

        $this->dropTable('history');
        $this->dropTable('user');
    }
}
