<?php

use yii\db\Migration;

/**
 * Class m210608_150209_insert_correlation_rules
 */
class m210608_150209_insert_correlation_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	$this->insert('sec_rules', [
            'id' => 3,
            'name' => "apache_correlation",
            'link' => "/var/www/html/secmon/rules/active/correlation/apache_correlation.rule",
            'state' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210608_150209_insert_correlation_rules cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_150209_insert_correlation_rules cannot be reverted.\n";

        return false;
    }
    */
}
