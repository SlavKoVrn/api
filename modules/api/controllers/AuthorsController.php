<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Authors;

class AuthorsController extends \yii\rest\ActiveController
{
    public $modelClass = Authors::class;
}
