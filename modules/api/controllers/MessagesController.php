<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Messages;
use app\modules\api\models\Authors;

use yii\data\ActiveDataProvider;

class MessagesController extends \yii\rest\ActiveController
{
    public $modelClass = Messages::class;

    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex(){

        $requestParams = \Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = \Yii::$app->getRequest()->getQueryParams();
        }

        $query = new \yii\db\Query();
        $query->select([
                'm.id message_id',
                'a.id author_id',
                'datetime',
                'datetime_first_message',
                'datetime_last_message',
                'messages_count',
                'phone',
                'content',
            ])
            ->from(['m'=>Messages::tableName()])
            ->innerJoin(['a'=>Authors::tableName()],'m.author_id=a.id');

        return \Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);

    }
}
