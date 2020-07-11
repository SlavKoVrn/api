<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%authors}}".
 *
 * @property int $id
 * @property string|null $phone
 * @property string|null $datetime_first_message
 * @property string|null $datetime_last_message
 * @property int|null $messages_count
 * @property int|null $is_banned
 */
class Authors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%authors}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_first_message', 'datetime_last_message'], 'safe'],
            [['messages_count', 'is_banned'], 'integer'],
            [['phone'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'datetime_first_message' => 'Datetime First Message',
            'datetime_last_message' => 'Datetime Last Message',
            'messages_count' => 'Messages Count',
            'is_banned' => 'Is Banned',
        ];
    }
}
