<?php

use yii\db\Migration;

/**
 * Class m190505_150618_insert_normalization_rules
 */
class m210403_113834_022_insert_normalization_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('normalization_rules', [
            'id' => 1,
            'name' => "iptables",
            'link' => "/var/www/html/secmon/rules/active/normalization/iptables.rule",
            'state' => 1,
        ]);

        $this->insert('normalization_rules', [
            'id' => 2,
            'name' => "sshd",
            'link' => "/var/www/html/secmon/rules/active/normalization/sshd.rule",
            'state' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190505_150618_insert_normalization_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190505_150618_insert_normalization_rules cannot be reverted.\n";

        return false;
    }
    */
}
