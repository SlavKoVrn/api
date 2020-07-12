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
    const MESSAGE_MAX_LENTH = 10000;

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

                $date = date('Y-m-d H:i:s');
                $_author=Authors::findOne(['phone'=>$message['phone']]);
                if ($_author){
                    $_author->datetime_last_message=$date;
                }else{
                    $_author=new Authors();
                    $_author->phone=$message['phone'];
                    $_author->datetime_first_message=$date;
                }
                $_author->messages_count++;
                $_author->is_banned=0;
                $_author->save();

                $message_length = mb_strlen($message['message']);
                $_messages_count = ceil($message_length/self::MESSAGE_MAX_LENTH);
                for ($i=0;$i<$_messages_count;$i++){
                    $_message = new Messages();
                    $_message->author_id=$_author->id;
                    $_message->content=mb_substr($message['message'],$i*self::MESSAGE_MAX_LENTH,self::MESSAGE_MAX_LENTH);
                    $_message->datetime=$date;
                    $_message->is_deleted=0;
                    $_message->save();
                }
            }
            $transaction->commit();
            throw new \yii\web\HttpException(200, 'added '.count($post['messages']), 200);

        } catch (\RuntimeException $e) {
            $transaction->rollBack();
            throw new \yii\web\HttpException(500, 'not all added', 500);

        }
    }
}
