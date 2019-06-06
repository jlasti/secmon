<?php

use yii\db\Migration;

/**
 * Class m181021_145148_alter_events_correlated
 */
class m181021_145148_alter_events_correlated extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('events_correlated', 'attack_type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181021_145148_alter_events_correlated cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181021_145148_alter_events_correlated cannot be reverted.\n";

        return false;
    }
    */
}
