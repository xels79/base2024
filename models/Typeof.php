<?php

namespace app\models;


/**
 * This is the model class for table "orders_names".
 *
 * @property integer $id
 * @property string $name
 */
class Typeof extends OneFieldTable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'typeof';
    }

}
