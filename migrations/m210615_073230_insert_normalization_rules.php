<?php

use yii\db\Migration;

/**
 * Class m210615_073230_insert_normalization_rules
 */
class m210615_073230_insert_normalization_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('normalization_rules', [
            'id' => 4,
            'name' => "group_management",
            'link' => "/var/www/secmon/rules/available/normalization/group_management.rule",
            'state' => 0,
        ]);

        $this->insert('normalization_rules', [
            'id' => 5,
            'name' => "su",
            'link' => "/var/www/secmon/rules/available/normalization/su.rule",
            'state' => 0,
        ]);

        $this->insert('normalization_rules', [
            'id' => 6,
            'name' => "sudo",
            'link' => "/var/www/secmon/rules/available/normalization/sudo.rule",
            'state' => 0,
        ]);

        $this->insert('normalization_rules', [
            'id' => 7,
            'name' => "user_management",
            'link' => "/var/www/secmon/rules/available/normalization/user_management.rule",
            'state' => 0,
        ]);

        $this->insert('normalization_rules', [
            'id' => 8,
            'name' => "test_rule",
            'link' => "/var/www/secmon/rules/available/normalization/normalization_test.rule",
            'state' => 0,
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_073230_insert_normalization_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_073230_insert_normalization_rules cannot be reverted.\n";

        return false;
    }
    */
}
