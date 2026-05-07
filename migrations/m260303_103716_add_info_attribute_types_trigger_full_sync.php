<?php

use yii\db\Migration;

class m260303_103716_add_info_attribute_types_trigger_full_sync extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%misp_events}}', 'info', $this->text()->null());

        $this->addColumn('{{%misp_settings}}', 'attribute_types', $this->json()->null());
        $this->addColumn('{{%misp_settings}}', 'trigger_full_sync', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%misp_settings}}', 'export_tags', $this->json()->null());

        $this->update('{{%misp_settings}}', [
            'attribute_types'   => json_encode([]),
            'trigger_full_sync' => false,
        ], ['id' => 1]);

        $this->update('{{%misp_settings}}', [
            'export_tags' => json_encode(['tlp:amber', 'type:OSINT', 'workflow:state="complete"']),
        ], ['id' => 1]);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%misp_settings}}', 'trigger_full_sync');
        $this->dropColumn('{{%misp_settings}}', 'attribute_types');
        $this->dropColumn('{{%misp_events}}', 'info');
        $this->dropColumn('{{%misp_settings}}', 'export_tags');
    }
}