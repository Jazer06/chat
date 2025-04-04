<?php

use yii\db\Migration;


/**
 * Handles the creation of table `{{%chat_messages}}`.
 */
class m250401_185830_create_chat_messages_table extends Migration
{
    public function up()
    {
        $dbName = 'chat_db';
        $db = Yii::$app->db;
        

        $schemaExists = $db->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbName", [
            ':dbName' => $dbName
        ])->queryScalar();
        
        if (!$schemaExists) {
            $db->createCommand("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")->execute();
            Yii::$app->session->setFlash('success', "Database {$dbName} created successfully.");
        }
        

        $this->db = new \yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=' . $dbName,
            'username' => Yii::$app->db->username,
            'password' => Yii::$app->db->password,
            'charset' => 'utf8mb4',
        ]);
        

        $this->createTable('chat_messages', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {

        $this->db = new \yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=chat_db',
            'username' => Yii::$app->db->username,
            'password' => Yii::$app->db->password,
            'charset' => 'utf8mb4',
        ]);
        
        $this->dropTable('chat_messages');
        
        // Опционально: удаляем базу данных (если нужно)
        // $db = Yii::$app->db;
        // $db->createCommand("DROP DATABASE IF EXISTS `chat_db`")->execute();
    }
}