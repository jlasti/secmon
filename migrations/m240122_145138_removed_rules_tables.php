<?php

use yii\db\Migration;

/**
 * Class m240122_145138_removed_rules_tables
 */
class m240122_145138_removed_rules_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%normalization_rules}}');
        $this->dropTable('{{%sec_rules}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240122_145138_removed_rules_tables cannot be reverted.\n";

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%normalization_rules}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'link' => $this->string(),
            'state' => $this->boolean(),
            'type' => $this->string(),
        ], $tableOptions);

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sec_rules}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'link' => $this->string(),
            'state' => $this->boolean(),
            'type' => $this->string(),
        ], $tableOptions);

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_145138_removed_rules_tables cannot be reverted.\n";

        return false;
    }
    */
}
