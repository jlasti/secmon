<?php

use yii\db\Migration;

/**
 * Class m231214_201649_create_cti_tables
 */
class m231214_201649_create_cti_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create cti_crowdsec table
        $this->createTable('cti_crowdsec', [
            'id' => $this->primaryKey(),
            'first_seen' => $this->string(),
            'last_seen' => $this->string(),
            'behavior' => $this->string(),
            'false_pos' => $this->string(),
            'classification' => $this->string(),
            'score_overall' => $this->integer(),
            'last_checked_at' => $this->timestamp(),
        ]);

        // Create cti_nerd table
        $this->createTable('cti_nerd', [
            'id' => $this->primaryKey(),
            'asn_name' => $this->string(),
            'asn_rep' => $this->float(),
            'fmp' => $this->float(),
            'blacklists' => $this->string(),
            'rep' => $this->float(),
            'last_checked_at' => $this->timestamp(),
        ]);

        // Create cti table with foreign keys
        $this->createTable('cti', [
            'id' => $this->primaryKey(),
            'fk_crowdsec_id' => $this->integer(),
            'fk_nerd_id' => $this->integer(),
            'ip' => $this->string(),
        ]);

        // Add foreign keys
        $this->addForeignKey('fk_cti_crowdsec', 'cti', 'fk_crowdsec_id', 'cti_crowdsec', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_cti_nerd', 'cti', 'fk_nerd_id', 'cti_nerd', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop tables in reverse order
        $this->dropForeignKey('fk_cti_nerd', 'cti');
        $this->dropForeignKey('fk_cti_crowdsec', 'cti');
        $this->dropTable('cti');
        $this->dropTable('cti_nerd');
        $this->dropTable('cti_crowdsec');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231214_201649_create_cti_tables cannot be reverted.\n";

        return false;
    }
    */
}
