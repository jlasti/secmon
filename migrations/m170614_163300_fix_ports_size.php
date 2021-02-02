<?php

use yii\db\Migration;

class m170614_163300_fix_ports_size extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->alterColumn('events_normalized', 'src_port', $this->integer());
        $this->alterColumn('events_normalized', 'dst_port', $this->integer());
    }

    public function safeDown()
    {

    }
}
