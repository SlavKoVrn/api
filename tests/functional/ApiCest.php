<?php

class ApiCest
{
    public function postMessages(\FunctionalTester $I)
    {
        $I->expectTo('добавление сообщений с данными пользователей');
        $messages=[
            [
                'message'=>'message 1',
                'phone'=>'+7(101)11-11-11',
            ],
            [
                'message'=>'message 2',
                'phone'=>'+7(202)22-22-22',
            ],
        ];
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('http://api.kadastrcard.ru/api/webhook', [
            'messages'=>$messages
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'name'=>'OK',
            'message'=>'added '.count($messages),
            'code'=>'200',
            'status'=>'200',
        ]);
    }

    public function getMessages(\FunctionalTester $I)
    {
        $I->expectTo('список сообщений с данными пользователей');
        $I->sendGET('http://api.kadastrcard.ru/api/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType([
            'message_id' => 'string|null',
            'author_id' => 'string|null',
            'datetime'=>'string|null',
            'datetime_first_message'=>'string|null',
            'datetime_last_message'=>'string|null',
            'messages_count'=>'string|null',
            'phone'=>'string|null',
            'content'=>'string|null',
        ]);
    }

}
