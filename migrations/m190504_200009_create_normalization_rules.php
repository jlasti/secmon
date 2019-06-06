<?php

use yii\db\Migration;

/**
 * Class m190504_200009_create_normalization_rules
 */
class m190504_200009_create_normalization_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createNormalizationRulesTable();
    }

    private function createNormalizationRulesTable()
    {
        $this->createTable('normalization_rules', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(),
            'link' => $this->string(),
            'type' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('normalization_rules');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190504_200009_create_normalization_rules cannot be reverted.\n";

        return false;
    }
    */
}
