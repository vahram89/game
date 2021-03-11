<?php

use yii\db\Migration;

/**
 * Class m210311_063226_unit_table
 */
class m210311_063226_unit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('units', [
            'id' => $this->primaryKey(10)->unsigned()->notNull(),
            'logo' => $this->string(255),
            'name' => $this->string(255),
            'string' => $this->text(),
            'win' => $this->tinyInteger(1)->defaultValue(0),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("units");
    }

}
