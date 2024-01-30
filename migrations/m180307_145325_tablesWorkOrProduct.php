<?php

use yii\db\Migration;

/**
 * Class m180307_145325_tablesWorkOrProduct
 */
class m180307_145325_tablesWorkOrProduct extends Migration
{
    public $tName1='WOPPost';
    public $tName2='WOPPod';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $options=[
            'WOPid'=>$this->primaryKey(),
            'referensId'=>$this->integer()->notNull()->comment('Сноска к связаной таблице'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
        $this->createTable($this->tName1, $options);
        $this->createTable($this->tName2, $options);
        
        $this->createIndex('fk-'.$this->tName1, $this->tName1, 'firm_id');
        $this->addForeignKey(
                'fk-'.$this->tName1,
                $this->tName1,
                'firm_id',
                'firmPost',
                'firm_id','CASCADE','CASCADE'
        );
        
        $this->createIndex('fk-'.$this->tName2, $this->tName2, 'firm_id');
        $this->addForeignKey(
                'fk-'.$this->tName2,
                $this->tName2,
                'firm_id',
                'firmPod',
                'firm_id','CASCADE','CASCADE'
        );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-'.$this->tName1, $this->tName1);
        $this->dropIndex('fk-'.$this->tName1, $this->tName1);
        $this->dropTable($this->tName1);

        $this->dropForeignKey('fk-'.$this->tName2, $this->tName2);
        $this->dropIndex('fk-'.$this->tName2, $this->tName2);
        $this->dropTable($this->tName2);

        return true;
    }
}
