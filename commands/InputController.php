<?php

namespace app\commands;

use app\modules\api\models\Authors;
use app\modules\api\models\Messages;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Migration;

class InputController extends Controller
{
    public function actionIndex()
    {
        $migration=new Migration();

        $migration->truncateTable(Authors::tableName());
        $migration->truncateTable(Messages::tableName());

        $faker = \Faker\Factory::create('ru_RU');

        for ($i=1;$i<=10;$i++){
            $migration->insert(Authors::tableName(),[
                'id'=>$i,
                'phone'=> $faker->phoneNumber,
                'datetime_first_message'=>$faker->date('Y-m-d H:i:s'),
                'messages_count'=>100,
                'is_banned'=>0,
            ]);
            for ($j=1;$j<=100;$j++){
                $migration->insert(Messages::tableName(),[
                    'author_id'=>$i,
                    'content'=> $faker->name,
                    'datetime'=>$faker->date('Y-m-d H:i:s'),
                    'is_deleted'=>0,
                ]);
            }
        }

        return ExitCode::OK;
    }
}
