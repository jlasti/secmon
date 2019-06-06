<?php

use yii\db\Migration;

/**
 * Class m190505_150643_insert_sec_rules
 */
class m190505_150643_insert_sec_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('sec_rules', [
            'id' => 1,
            'name' => "portscan",
            'link' => "/var/www/secmon/rules/active/correlation/portscan.rule",
            'state' => 1,
        ]);

        $this->insert('sec_rules', [
            'id' => 2,
            'name' => "sshd_correlation",
            'link' => "/var/www/secmon/rules/active/correlation/sshd_correlation.rule",
            'state' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190505_150643_insert_sec_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190505_150643_insert_sec_rules cannot be reverted.\n";

        return false;
    }
    */
}
