<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_messages}}`.
 */
class m250401_185830_create_chat_messages_table extends Migration
{
    public function up()
    {
        $this->createTable('chat_messages', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('chat_messages');
    }
}
