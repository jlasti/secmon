<?php

use yii\db\Migration;

/**
 * Class m210608_145822_insert_normalization_rule
 */
class m210608_145822_insert_normalization_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	$this->insert('normalization_rules', [
            'id' => 3,
            'name' => "apache",
            'link' => "/var/www/html/secmon/rules/active/normalization/apache.rule",
            'state' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210608_145822_insert_normalization_rule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_145822_insert_normalization_rule cannot be reverted.\n";

        return false;
    }
    */
}
