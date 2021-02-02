<?php

use yii\db\Migration;

/**
 * Class m190503_112049_add_refresh_time
 */
class m190503_112049_add_refresh_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    // Use up()/down() to run migration code without a transaction.
    public function safeUp()
    {
        $this->addColumn('views', 'refresh_time', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('views', 'refresh_time');
    }
}
