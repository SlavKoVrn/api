<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%messages}}".
 *
 * @property int $id
 * @property int|null $author_id
 * @property string|null $datetime
 * @property string|null $content
 * @property int|null $is_deleted
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%messages}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'is_deleted'], 'integer'],
            [['datetime'], 'safe'],
            [['content'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'datetime' => 'Datetime',
            'content' => 'Content',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
