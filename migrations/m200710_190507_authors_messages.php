<?php

use yii\db\Migration;

/**
 * Class m200710_190507_authors_messages
 */
class m200710_190507_authors_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_authors_phone', '{{%authors}}');
        $this->dropIndex('idx_messages_author_id', '{{%messages}}');

        $this->dropTable('{{%messages}}');
        $this->dropTable('{{%authors}}');
    }

}
