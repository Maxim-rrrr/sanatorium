<?php

use yii\db\Migration;

/**
 * Class m200501_223339_create_users
 */
class m200501_223339_create_users extends Migration
{
    
    public function up()
    {
		$this->db->createCommand(
			'INSERT INTO user (id, username, password, role)'.
			'VALUES '.
			'(null,\'Администратор санатория\',\'$2y$13$TxsCbrHCT11O3irMATwWgObbQ9wuvaKukNOmNaLn32nFZVuce4mVG\',\'admin\'),'.
			'(null,\'Вожатый\',\'$2y$13$TxsCbrHCT11O3irMATwWgObbQ9wuvaKukNOmNaLn32nFZVuce4mVG\',\'leader\'),'.
			'(null,\'Врач-терапевт\',\'$2y$13$TxsCbrHCT11O3irMATwWgObbQ9wuvaKukNOmNaLn32nFZVuce4mVG\',\'therapist\'),'.
			'(null,\'Медицинский работник\',\'$2y$13$TxsCbrHCT11O3irMATwWgObbQ9wuvaKukNOmNaLn32nFZVuce4mVG\',\'health_worker\'),'.
			'(null,\'Повар\',\'$2y$13$TxsCbrHCT11O3irMATwWgObbQ9wuvaKukNOmNaLn32nFZVuce4mVG\',\'cook\');'
		)->query();
    }

    public function down()
    {
        $this->truncateTable('user');
    }
    
}
