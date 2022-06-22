<?php

use yii\db\Migration;

/**
 * Class m210615_074043_insert_correlation_rules
 */
class m210615_074043_insert_correlation_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('sec_rules', [
            'id' => 4,
            'name' => "sshd_parent_events",
            'link' => "/var/www/secmon/rules/available/correlation/ssh_correlation_final.rule",
            'state' => 0,
        ]);

        $this->insert('sec_rules', [
            'id' => 5,
            'name' => "corr_test",
            'link' => "/var/www/secmon/rules/available/correlation/corr_test.rule",
            'state' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_074043_insert_correlation_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_074043_insert_correlation_rules cannot be reverted.\n";

        return false;
    }
    */
}
