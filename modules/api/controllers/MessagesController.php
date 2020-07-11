<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Messages;

class MessagesController extends \yii\rest\ActiveController
{
    public $modelClass = Messages::class;
}
