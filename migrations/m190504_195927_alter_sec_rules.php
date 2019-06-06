<?php

use yii\db\Migration;

/**
 * Class m190504_195927_alter_sec_rules
 */
class m190504_195927_alter_sec_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sec_rules', 'type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190504_195927_alter_sec_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190504_195927_alter_sec_rules cannot be reverted.\n";

        return false;
    }
    */
}
