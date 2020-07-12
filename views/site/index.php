<?php
use app\modules\api\models\Messages;
use app\modules\api\models\Authors;

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */

$this->title = 'Yii2 API RESTfull';

$query = new \yii\db\Query();
$query->select(['a.phone','m.content','m.datetime'])
    ->from(['m'=>Messages::tableName()])
    ->innerJoin(['a'=>Authors::tableName()],'m.author_id=a.id');
$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => ['pageSize' => 20],
]);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Yii2 API RESTfull</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <?php yii\widgets\Pjax::begin(['id' => 'messages']) ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        'phone',
                        'content',
                        [
                            'attribute'=>'datetime',
                            'value'=>function($model){
                                return date('d.m.Y H:i',strtotime($model['datetime']));
                            }
                        ]
                    ]
                ]) ?>
                <?php yii\widgets\Pjax::end() ?>
            </div>
        </div>

    </div>
</div>
