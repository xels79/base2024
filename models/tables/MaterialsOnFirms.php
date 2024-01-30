<?php

namespace app\models\tables;

use Yii;
use app\models\admin\Post;
use app\models\tables\Tablestypes;
use app\models\tables\DependTable;
use app\models\tables\DependTables;
use app\components\MyHelplers;

/**
 * This is the model class for table "materials_on_firms".
 * @author Александр
 * @property int $id
 * @property int $firm_id Сноска на фирму
 * @property int $m_type Сноска на тип материала
 * @property int $m_id Сноска на материал
 * @property double $coast Стоимость
 * @property double $optcoast Стоимость опта
 * @property double $recomendetcoast Стоимость по прайсу
 * @property int $optfrom Количество с которого начинается опт
 * @property int $update Время последнего обновления
 */
class MaterialsOnFirms extends \yii\db\ActiveRecord {

    protected $_firmName = null;
    protected $_matType = null;
    protected $_calculated_colum = null;
    protected $_calculated_colum_cached = false;
    protected $_cCols = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'materials_on_firms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['m_type', 'm_id', 'firm_id'], 'required'],
            [['m_type', 'm_id', 'firm_id', 'update', 'optfrom'], 'integer'],
            [['coast', 'optcoast','recomendetcoast'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'       => 'ID',
            'm_type'   => 'Сноска на тип материала',
            'm_id'     => 'Сноска на материал',
            'coast'    => 'Стоимость',
            'optfrom'  => 'Опт от...',
            'optcoast' => 'Опт стоимость',
            'recomendetcoast' => 'Стоим. по прайсу'
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->update = time();
            return true;
        } else {
            return false;
        }
    }

    public function setFirmName($val) {
        $this->_firmName = !$val ? null : $val;
    }

    public function getFirmName() {
        if ($model = Post::findOne($this->firm_id)) {
            return $model->mainName;
        } else {
            return 'не найден';
        }
    }

    public function setMatType($val) {
        $this->_matType = !$val ? null : $val;
    }

    public function getMatType() {
        if ($model = Tablestypes::findOne($this->m_type)) {
            return $model->name;
        } else {
            return 'не найден';
        }
    }

    public function getFirm() {
        return $this->hasOne(Post::className(), ['firm_id' => 'firm_id']);
    }

    public function getMaterialType() {
        return $this->hasOne(Tablestypes::className(), ['id' => 'm_type']);
    }

    private $mats = null;

    public function getName() {
        if ($modelB = Tablestypes::findOne($this->m_type)) {
            $mat = MyHelplers::materialInfoByContent($modelB, $this->toArray([], ['type_id', 'mat_id']));
            Yii::debug($this->toArray([], ['type_id', 'mat_id']), 'MaterialsOnFirms $this');
            $i = 0;
            $rVal = '';
            foreach (\yii\helpers\Json::decode($mat['value']['struct']) as $el) {
                if ($rVal)
                    $rVal .= ', ';
                $rVal .= $el . ':' . $mat['query']['name' . $i++];
            }
            //return \yii\helpers\VarDumper::dumpAsString($mat,10,true);
            return $rVal;
        } else {
            return '';
        }
    }

    public function extraFields() {
        return [
            'type_id' => 'm_type',
            'mat_id'  => 'm_id'
        ];
    }

    public function calculated_colum($m_type, $m_id = null) {
        $rVal = [];
        $struct = Tablestypes::find()->where(['id' => $m_type])->one();
        if (!$struct)
            return $rVal;
        $name = $struct->translitName;
        if ($struct = \yii\helpers\Json::decode($struct->struct)) {
            foreach ($struct as $el) {
                $rVal[] = [
                    'label' => $el,
                    'value' => 'нет'
                ];
            }
        }
        $struct = DependTables::dependsTablesNamesFromRus($name, $struct);
        if ($m_id) {
            $i = count($struct) - 1;
            $query = DependTable::createObject($struct[$i])->find()->where(['`' . $struct[$i] . '`.`id`' => $m_id]);
            $select = ['`' . $struct[$i] . '`.`name` as `name' . $i . '`'];
            $order='`'.$struct[$i].'`.`name`';
            $prev = $struct[$i--];
            for (; $i > -1;) {
                $query->join('LEFT OUTER JOIN', $struct[$i], '`' . $struct[$i] . '`.`' . DependTables::$pKey . '`=`' . $prev . '`.`' . DependTables::$reference . '`');
                $select[] = '`' . $struct[$i] . '`.`name` as `name' . $i . '`';
                $order='`'.$struct[$i].'`.`name`';
                $prev = $struct[$i--];
            }
            Yii::debug($order, 'MaterialsOnFirmsSearch_order');
            $query->select($select);
            //$query->orderBy($order);
            $res = $query->asArray()->one();
        } else {
            $res = null;
        }
        if (is_array($res)) {
            for ($i = 0; $i < count($rVal); $i++) {
                if (array_key_exists('name' . $i, $res)) {
                    $rVal[$i]['value'] = $res['name' . $i];
                }
            }
        }
        for ($i = 0; $i < count($rVal); $i++) {
            $tmp = [];
            foreach (DependTable::createObject($struct[$i])->find()->orderBy('name')->asArray()->all() as $el) {
                if (!array_key_exists($el['name'], $tmp))
                    $tmp[$el['name']] = [];
                $tmp[$el['name']][] = $el['id'];
            };
            $tmp = array_map(function($val) {
                return '[ ' . implode(', ', $val) . ' ]';
            }, $tmp);

            $rVal[$i]['filter'] = array_flip($tmp);
        }
        Yii::debug(['$rVal' => $rVal, '$res' => $res], 'calculated_colum');
        return $rVal;
    }

    public function __calculated_colum() {
        if ($this->isNewRecord) {
            $this->_calculated_colum = [];
            return;
        }
        if ($this->_calculated_colum_cached)
            return;
        $this->_calculated_colum = $this->calculated_colum($this->m_type, $this->m_id);
        $this->_calculated_colum_cached = true;
    }

    public function __get($name) {
        $tmp = explode('_', $name);
        if (count($tmp) === 2 && $tmp[0] === 'cCol') {
            if ($this->_calculated_colum === null) {
                $this->__calculated_colum();
            }
            if (is_array($this->_calculated_colum) && array_key_exists((int) $tmp[1], $this->_calculated_colum)) {
                return $this->_calculated_colum[(int) $tmp[1]]['value'];
            } else {
                return 'нет';
            }
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $val) {
        $tmp = explode('_', $name);
        if (count($tmp) === 2 && $tmp[0] === 'cCol') {
            $this->_cCols[(int) $tmp[1]] = $val;
        } else {
            parent::__set($name, $val);
        }
    }

}
