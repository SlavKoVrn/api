<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\api\models\Authors;
use yii\db\Migration;

class InputController extends Controller
{
    public function actionIndex()
    {
        $migration=new Migration();
        $faker = \Faker\Factory::create('ru_RU');
        for ($i=1;$i<=10;$i++){
            $migration->insert(Authors::tableName(),[
                'phone'=> $faker->phoneNumber,
            ]);
        }

        return ExitCode::OK;
    }
}
