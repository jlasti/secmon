<?php

use yii\db\Migration;

/**
 * Handles adding type to table `view_components`.
 */
class m161206_073547_add_type_column_to_view_components_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
    	$this->addColumn('view_components', 'type', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
    }
}
