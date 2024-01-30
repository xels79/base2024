<?php

use yii\db\Migration;

/**
 * Class m210215_103249_staticSpendTableCreate
 */
class m210215_103249_staticSpendTableCreate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('static_spends',[
            'id'                    =>$this->primaryKey(),
            'date'                  =>$this->date()->notNull()->comment('Дата'),
//            'percent'               =>$this->float()->defaultValue(0)->comment('%'),
            'officeRental'          =>$this->float()->defaultValue(0)->comment('Аренда Кролл'),
            'arsiRent'               =>$this->float()->defaultValue(0)->comment('Аренда Арси'),
            'cuttingIntFFighting'   =>$this->float()->defaultValue(0)->comment('Резка, интернет, пожарка'),
            'utilityBills'          =>$this->float()->defaultValue(0)->comment('Комуналка'),
            'forCars'               =>$this->float()->defaultValue(0)->comment('Въезд машин'),
            'ndsAsterio'            =>$this->float()->defaultValue(0)->comment('НДС Астро'),
            'nalogZPAsterio'        =>$this->float()->defaultValue(0)->comment('Налоги на з/п Астро'),
            'nalogZPAST'            =>$this->float()->defaultValue(0)->comment('Налоги на з/п АСТ'),
            'sixPercentAST'         =>$this->float()->defaultValue(0)->comment('6% АСТ'),
            'bank1'                 =>$this->float()->defaultValue(0)->comment('Банк1'),
            'bank2'                 =>$this->float()->defaultValue(0)->comment('Банк2'),
            'bank3'                 =>$this->float()->defaultValue(0)->comment('Банк3'),
            'bank4'                 =>$this->float()->defaultValue(0)->comment('Банк4'),
            'cashWithdrawal'        =>$this->float()->defaultValue(0)->comment('Снятие наличных'),
            'rezerved1'             =>$this->float()->defaultValue(0)->comment('Резерв'),
            'rezerved2'             =>$this->float()->defaultValue(0)->comment('Резерв')
        ]);
        $this->createIndex ( 'un-for-date-staticSpend', 'static_spends', 'date', true );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex( 'un-for-date-staticSpend', 'static_spends' );
        $this->dropTable( 'static_spends' );
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210215_103249_staticSpendTableCreate cannot be reverted.\n";

        return false;
    }
    */
}
