<?php

use yii\db\Migration;

/**
 * Class m200501_225443_create_table_order
 */
class m200501_225443_create_table_order extends Migration
{
	public function up()
	{
		$this->createTable(
			"order",
			[
				'id' => $this->primaryKey(11),
				'customer_id' => $this->integer(11)->notNull(),
				'housing_id' => $this->integer(11)->notNull(),
				'floor' => $this->integer(5)->notNull(),
				'room' => $this->integer(5)->notNull(),
				'room_type' => $this->string(1)->notNull(),
				'status' => 'ENUM(\'none\', \'posted\') DEFAULT \'none\' NOT NULL',
				'arrival_date' => $this->date()->notNull(),
				'departure_date' => $this->date()->notNull(),
				'created_date' => $this->dateTime()->notNull()
			]
		);
	}
	
	public function down()
	{
		$this->dropTable('order');
	}
}
