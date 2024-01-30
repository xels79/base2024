<?php

use yii\db\Migration;
use app\models\MyRbac;

/**
 * Class m200415_143254_addProizvodstvoChiefUser
 */
class m200415_143254_addProizvodstvoChiefUser extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if ($model = MyRbac::find()->where(['engname' => 'proizvodstvo'])->one()) {
            echo "Базовая роль \"proizvodstvo\" - найдена.\nДобовляеи \"proizvodstvochief\"\n";
            $model2 = new MyRbac();
            $model2->name = "Начальник производства";
            $model2->value = $model->value;
            $model2->engname = "proizvodstvochief";
            if ($model2->save()) {
                echo "Начальник производства успешно добавлен.\n";
                return true;
            } else {
                echo "Не удалось добавить начальника производства!\n";
                return false;
            }
        } else {
            echo "Не удалось найти базовую роль \"proizvodstvo\"!.\n";
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m200415_143254_addProizvodstvoChiefUser невозможно отменить.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m200415_143254_addProizvodstvoChiefUser cannot be reverted.\n";

      return false;
      }
     */
}
