<?php

namespace app\modules\api\controllers;

use yii\rest\ActiveController;
use app\modules\api\models\Messages;
use app\modules\api\models\Authors;

/**
 * Default controller for the `api` module
 */
class WebhookController extends ActiveController
{
    public $modelClass = Messages::class;

    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    protected function verbs() {
        $verbs = parent::verbs();
        $verbs['index'] = ['POST'];
        return $verbs;
    }

    public function actionIndex(){

        $post=\Yii::$app->request->post();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($post['messages'] as $message){

                $_author=Authors::findOne(['phone'=>$message['phone']]);
                if ($_author){
                    $_author->datetime_last_message=date('Y-m-d H:i:s');
                }else{
                    $_author=new Authors();
                    $_author->phone=$message['phone'];
                    $_author->datetime_first_message=date('Y-m-d H:i:s');
                }
                $_author->messages_count++;
                $_author->is_banned=0;
                $_author->save();

                $_message = new Messages();
                $_message->author_id=$_author->id;
                $_message->content=$message['message'];
                $_message->datetime=date('Y-m-d H:i:s');
                $_message->is_deleted=0;
                $_message->save();
            }
            $transaction->commit();
            throw new \yii\web\HttpException(200, 'added '.count($post['messages']), 200);

        } catch (\RuntimeException $e) {
            $transaction->rollBack();
            throw new \yii\web\HttpException(500, 'not all added', 500);

        }
    }
}
