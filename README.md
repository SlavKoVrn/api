<p align="center">
    <a href="http://api.kadastrcard.ru" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 API RESTfull</h1>
    <br>
</p>

Demo is at [api.kadastrcard.ru](http://api.kadastrcard.ru)

### 1. Таблицы задания
-------------------

```
authors
--------------
id
phone
datetime_first_message
datetime_last_message
messages_count
is_banned
--------------------------------------------------------
messages
--------------
id
author_id
datetime
content
is_deleted
--------------------------------------------------------
```

~~~
/migrations/m200710_190507_authors_messages.php
~~~

```php
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%authors}}', [
            'id'                     => $this->primaryKey(),
            'phone'                  => $this->string(50),
            'datetime_first_message' => $this->dateTime(),
            'datetime_last_message'  => $this->dateTime(),
            'messages_count'         => $this->integer(),
            'is_banned'              => $this->boolean(),
        ],$tableOptions);

        $this->createIndex('idx_authors_phone', '{{%authors}}', 'phone');

        $this->createTable('{{%messages}}', [
            'id'                     => $this->primaryKey(),
            'author_id'              => $this->integer(),
            'datetime'               => $this->dateTime(),
            'content'                => $this->text(),
            'is_deleted'             => $this->boolean(),
        ],$tableOptions);

        $this->createIndex('idx_messages_author_id', '{{%messages}}', 'author_id');
```

### 2. Установка правил маршрутов

~~~
/config/web.php
~~~

```php
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'api/webhook'=>'api/webhook/index',
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => ['api/messages','api/authors'],
                    'pluralize'     => false,
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'api/webhook',
                    'pluralize'     => false,
                    'extraPatterns' => [
                        'POST index'  => 'index',
                    ],
                ],
            ],
        ],
```

### 3 Установка REST контроллеров

~~~
/modules/api/controllers/MessagesController.php
~~~

```php
namespace app\modules\api\controllers;

use app\modules\api\models\Messages;

class MessagesController extends \yii\rest\ActiveController
{
    public $modelClass = Messages::class;
}
```
~~~
/modules/api/controllers/AuthorsController.php
~~~

```php
namespace app\modules\api\controllers;

use app\modules\api\models\Authors;

class AuthorsController extends \yii\rest\ActiveController
{
    public $modelClass = Authors::class;
}
```
~~~
/modules/api/controllers/WebhookController.php
~~~

```php
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
```

### 4 Первоначальное заполнение базы данных

~~~
/commands/InputController.php
~~~

```php
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
```
