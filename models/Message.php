<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Message extends ActiveRecord
{
    public static function tableName()
    {
        return 'chat_messages';
    }
}