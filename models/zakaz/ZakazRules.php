<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazRules
 *
 * @author Александр
 */
abstract class ZakazRules extends ZakazAtributes {

    public function rules() {
        return [
            [
                [
                    'ourmanager_id',
                    're_print',
                    'num_of_printing_block',
                    'num_of_printing_block1',
                    'profit_type',
                    'blocks_per_sheet',
                    'blocks_per_sheet1',
                    'blocks_type',
                    'blocks_type1',
                    'post_print_uf_lak',
                    'post_print_color_fit',
                    'post_print_thermal_lift',
                    'material_is_on_sklad',
                    'material_is_on_sklad1',
                    'block_zapas',
                    'block_zapas1',
                    'place_of_application_block',
                    'oplata_status',
                    'zakUrFace'
                ],
                'integer'
            ],
            [['zak_id', 'manager_id', 'production_id', 'worktypes_id'], 'integer',
                'min'     => 1, 'message' => 'Нужно что-нибудь выбрать.'],
            [['stage', 'division_of_work', 'method_of_payment', 'invoice_from_this_company'],
                'safe'],
            [[
            'name',
            'attention',
            'account_number',
            'note',
            'tmpName',
            'design_comment',
            'proizv_comment',
            'material_block_format',
            'material_block_format1',
            'post_print_uf_lak_text',
            'post_print_thermal_lift_text',
            'post_print_rezka_text',
            'print_unpacking_coast',
            'print_packing_coast',
            'post_print_firm_name',
                ], 'string'],
            [['num_of_products_in_block',
            'num_of_products_in_block1',
            'total_coast',
            'spending',
            'spending2',
            'material_coast_comerc',
            'exec_bonus_payment',
            'exec_bonus_summ',
            'exec_delivery_payment',
            'exec_delivery_summ',
            'exec_markup_payment',
            'exec_markup_summ',
            'exec_speed_payment',
            'exec_speed_summ',
            'exec_transport2_payment',
            'exec_transport2_summ',
            'exec_transport_payment',
            'exec_transport_summ'
                ], 'number'],
            [['deadline_time'], 'string', 'max' => 5],
            [['product_size', 'format_printing_block', 'format_printing_block1'],
                'string', 'max' => 12],
            [['post_print_agent_name'], 'string', 'max' => 64],
            [['post_print_agent_phone'], 'string', 'max' => 24],
            [['number_of_copies'], 'checkTirage', 'skipOnEmpty' => false, 'params'      => [
                    'required' => true]],
            [['number_of_copies1'], 'checkTirage'],
            [['date_of_receipt', 'date_of_receipt1'], 'default', 'value' => null],
            [['date_of_receipt', 'date_of_receipt1'], 'string'],
            [['dateofadmission', 'deadline'], 'date', 'format' => 'dd.MM.yyyy'],
            [
                [
                    'division_of_work',
                    'stage',
                    'zak_id',
                    'manager_id',
                    'production_id',
                    'worktypes_id',
                ],
                'required', 'message' => 'Нужно что-нибудь выбрать.'
            ],
            [
                [
                    'ourmanager_id',
                    'name',
                    'number_of_copies',
                    'total_coast',
                    'spending',
                    'spending2',
                    'dateofadmission',
                    'deadline'
                ],
                'required', 'message' => 'Нужно заполнить'
            ],
            //[['post_print'], 'default', 'value'=>[]],
            [[
            'post_print_call_to_print',
            'post_print_rezka',
            'exec_transport',
            'exec_speed',
            'exec_markup',
            'exec_delivery',
            'exec_bonus',
            'exec_transport_paid',
            'exec_transport2_paid',
            'exec_speed_paid',
            'exec_markup_paid',
            'exec_delivery_paid',
            'exec_bonus_paid',
            'is_express'
                ], 'boolean'],
            //[[]]
            [['podryad', 'podryad_to_erase', 'materials', 'materials_to_erase', 'colors', 'post_print'], 'safe'],
            ['re_print', 'default', 'value' => 0],
            [['wages', 'percent1', 'percent2', 'percent3'], 'number']
        ];
    }

    public function getWages() {
        if (!$this->isNewRecord) {
            if ($tuser = \app\models\TblUser::findOne((int) $this->ourmanager_id)) {
                return $tuser->wages;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function setWages($val) {

    }

    public function checkTirage($attribute, $params) {
        $errMess = 'Неверное значение (допуск. число * число)';
        $errMess2 = 'Должен быть указан';
        if (strlen($this->$attribute) > 14) {
            $this->addError($attribute, 'Не больше 14-и символов');
        } else if ($this->$attribute) {
            $tmp = mb_split('[xх*XХ]', $this->$attribute);
            if (count($tmp) > 1) {
                if (count($tmp) > 2) {
                    $this->addError($attribute, $errMess);
                } elseif (!is_numeric($tmp[0]) || !is_numeric($tmp[1])) {
                    $this->addError($attribute, $errMess);
                } else {
                    $tmp = [(int) $tmp[0], (int) $tmp[1]];
                    if ($tmp[0] <= $tmp[1]) {
                        $this->$attribute = $tmp[0] . '*' . $tmp[1];
                    } else {
                        $this->$attribute = $tmp[1] . '*' . $tmp[0];
                    }
                }
            } else {
                if (!is_numeric($this->$attribute)) {
                    $this->addError($attribute, $errMess);
                }
            }
        } else {
            if (\yii\helpers\ArrayHelper::getValue($params, 'required', false))
                $this->addError($attribute, $errMess2);
        }
    }

}
