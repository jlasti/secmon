<?php

use yii\db\Migration;

/**
 * Class m240305_171602_add_complementary_columns_to_cti_tables
 */
class m240305_171602_add_complementary_columns_to_cti_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Alter existing columns of cti_nerd - add new, remove unused
        $this->renameColumn('cti_nerd', 'asn_name', 'as_name');
        $this->dropColumn('cti_nerd', 'asn_rep');
        $this->addColumn('cti_nerd', 'as_id', $this->integer());
        $this->addColumn('cti_nerd', 'ip_range', $this->string());
        $this->addColumn('cti_nerd', 'ip_range_rep', $this->float());
        $this->addColumn('cti_nerd', 'events', $this->string());
        $this->addColumn('cti_nerd', 'geo_city', $this->string());
        $this->addColumn('cti_nerd', 'geo_country', $this->string());
        $this->addColumn('cti_nerd', 'hostname', $this->string());
        $this->addColumn('cti_nerd', 'last_activity', $this->timestamp());
        $this->addColumn('cti_nerd', 'first_activity', $this->timestamp());

        // Alter existing columns of cti_crowdsec - add new, update unused
        $this->addColumn('cti_crowdsec', 'as_num', $this->integer());
        $this->addColumn('cti_crowdsec', 'as_name', $this->string());
        $this->addColumn('cti_crowdsec', 'ip_range_24', $this->string());
        $this->addColumn('cti_crowdsec', 'ip_range_24_rep', $this->string());
        $this->addColumn('cti_crowdsec', 'geo_city', $this->string());
        $this->addColumn('cti_crowdsec', 'geo_country', $this->string());
        $this->addColumn('cti_crowdsec', 'reverse_dns', $this->string());
        $this->dropColumn('cti_crowdsec', 'first_seen');
        $this->dropColumn('cti_crowdsec', 'last_seen');
        $this->addColumn('cti_crowdsec', 'first_seen', $this->timestamp());
        $this->addColumn('cti_crowdsec', 'last_seen', $this->timestamp());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('cti_nerd', 'as_name', 'asn_name');
        $this->addColumn('cti_nerd', 'asn_rep', $this->float());
        $this->dropColumn('cti_nerd', 'as_id');
        $this->dropColumn('cti_nerd', 'ip_range');
        $this->dropColumn('cti_nerd', 'ip_range_rep');
        $this->dropColumn('cti_nerd', 'events');
        $this->dropColumn('cti_nerd', 'geo_city');
        $this->dropColumn('cti_nerd', 'geo_country');
        $this->dropColumn('cti_nerd', 'hostname');
        $this->dropColumn('cti_nerd', 'last_activity');
        $this->dropColumn('cti_nerd', 'first_activity');
        
        $this->dropColumn('cti_crowdsec', 'as_num',);
        $this->dropColumn('cti_crowdsec', 'as_name');
        $this->dropColumn('cti_crowdsec', 'ip_range_24');
        $this->dropColumn('cti_crowdsec', 'ip_range_24_rep');
        $this->dropColumn('cti_crowdsec', 'geo_city');
        $this->dropColumn('cti_crowdsec', 'geo_country');
        $this->dropColumn('cti_crowdsec', 'reverse_dns');
        $this->alterColumn('cti_crowdsec', 'first_seen', 'string');
        $this->alterColumn('cti_crowdsec', 'last_seen', 'string');
    }
}
