<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ChatMessage extends ActiveRecord
{
    public static function tableName()
    {
        return 'chat_messages';
    }

    public function rules()
    {
        return [
            [['username', 'message'], 'required'],
            [['created_at'], 'integer'],
            [['username', 'message'], 'string', 'max' => 255],
        ];
    }
}