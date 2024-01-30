<?php

use yii\db\Migration;
use yii\helpers\Console;
use app\models\TblUser;

/**
 * Class m191106_102108_importdefaultacssessseting
 */
class m191106_102108_importdefaultacssessseting extends Migration {

    public function spaces($str) {
        $rVal = '';
        for ($i = 0; $i < mb_strlen($str); $i++)
            $rVal .= ' ';
        return $rVal;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if (file_exists(__DIR__ . '/rules.json')) {
            echo Console::ansiFormat("Внимание!\n", [Console::FG_RED]);
            echo Console::ansiFormat("Найден файл rules.json :\n", [Console::FG_GREEN]);
            $vals = \yii\helpers\Json::decode(file_get_contents(__DIR__ . '/rules.json'));
            //echo yii\helpers\VarDumper::dumpAsString($vals, 10, true) . "\n";
            foreach ($vals as $key => $vals) {
                $hStr = '"' . $key . '":';
                echo Console::ansiFormat($hStr . "\n", [Console::FG_BLUE]);
                if (count($vals['allow'])) {
                    $sp = $this->spaces($hStr);
                    echo Console::ansiFormat($sp . "\"Разрешено\":\n", [Console::FG_GREEN]);
                    $sp .= $this->spaces('"Разрешено"');
                    foreach ($vals['allow'] as $v) {
                        echo $sp . $v . "\n";
                    }
                }
                if (count($vals['denied'])) {
                    $sp = $this->spaces($hStr);
                    echo Console::ansiFormat($sp . "\"Запрещено\":\n", [Console::FG_GREEN]);
                    $sp .= $this->spaces('"Запрещено"');
                    foreach ($vals['allow'] as $v) {
                        echo $sp . $v . "\n";
                    }
                }
            }
            if (Console::confirm("Внимание!\nДобавиь стандартные правила доступа?")) {
                foreach ($vals as $key => $val) {
                    if ($model = app\models\MyRbac::find()->where(['name' => $key])->one()) {
                        echo Console::ansiFormat("Добовляем к \"$key\"...", [Console::FG_BLUE]);
                        $model->value = \yii\helpers\Json::encode($val);
                        if ($model->save()) {
                            echo Console::ansiFormat("Ok\n", [Console::FG_GREEN]);
                        } else {
                            echo Console::ansiFormat("Ошибка\n", [Console::FG_RED]);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "Отменить изменения не возможно.\nНо миграция отменена.\n";

        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m191106_102108_importdefaultacssessseting cannot be reverted.\n";

      return false;
      }
     */
}
