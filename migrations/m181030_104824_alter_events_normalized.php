<?php

use yii\db\Migration;

/**
 * Class m181030_104824_alter_events_normalized_
 */
class m181030_104824_alter_events_normalized extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('events_normalized','src_port',$this->integer());
        $this->alterColumn('events_normalized','dst_port',$this->integer());

        $this->addColumn('events_normalized', 'src_code', $this->string());
        $this->addColumn('events_normalized', 'dst_code', $this->string());
        $this->addColumn('events_normalized', 'src_country', $this->string());
        $this->addColumn('events_normalized', 'dst_country', $this->string());
        $this->addColumn('events_normalized', 'src_city', $this->string());
        $this->addColumn('events_normalized', 'dst_city', $this->string());
        $this->addColumn('events_normalized', 'src_latitude', $this->float());
        $this->addColumn('events_normalized', 'dst_latitude', $this->float());
        $this->addColumn('events_normalized', 'src_longitude', $this->float());
        $this->addColumn('events_normalized', 'dst_longitude', $this->float());
        $this->addColumn('events_normalized', 'analyzed', $this->boolean()->defaultValue(false));

        $this->createIndex('idx_analyzed_id', 'events_normalized', 'analyzed');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181030_104824_alter_events_normalized_ cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_104824_alter_events_normalized_ cannot be reverted.\n";

        return false;
    }
    */
}
