<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazAtributes
 *
 * @author Александр
 */
abstract class ZakazAtributes extends ZakazBlock {

    public function attributeLabels() {
        return [
            'id'                             => '№',
            'dateofadmission'                => 'Дата:',
            'dateofadmissionText'            => 'Дата приема:',
            'deadline'                       => 'Срок сдачи',
            'deadlineText'                   => 'Срок сдачи',
            'date_of_receipt'                => 'Дата поступления',
            'ourmanager_id'                  => 'Менеджер',
            'ourmanagername'                 => 'Менеджер',
            'zak_id'                         => 'Заказчик',
            'zak_idText'                     => 'Заказчик',
            'manager_id'                     => 'Конт. лицо',
            'manager_id_text'                => 'Конт. лицо',
            'manager_phone_text'             => 'Тел.',
            'manager_email_text'             => 'E-mail',
            'total_coast'                    => 'Стоимость',
            'total_coastText'                => 'Стоимость',
            'total_spending'                 => 'Выплаты',
            'spending'                       => 'Исполнитель',
            'spending2'                      => 'Материал',
            'profit'                         => 'Прибыль',
            'calulateProfit'                 => 'Прибыль',
            'calulateProfitText'             => 'Прибыль',
            'production_id'                  => 'Продукция',
            'production_idText'              => 'Продукция',
            'name'                           => 'Наименование',
            'number_of_copies'               => 'Тираж',
            'number_of_copies1'              => 'Тираж2',
            'number_of_copiesText'           => 'Тираж',
            'number_of_copies1Text'          => 'Тираж2',
            'worktypes_id'                   => 'Вид работ',
            'worktypes_id_text'              => 'Вид работ',
            'stage'                          => 'Этап работы',
            'stageText'                      => 'Этап работы',
            'division_of_work'               => 'Работа',
            'division_of_work_text'          => 'Работа',
            'method_of_payment'              => 'Форма оплаты',
            'method_of_payment_text'         => 'Форма оплаты',
            'invoice_from_this_company'      => 'Фирма', //счет от фирмы
            'invoice_from_this_company_text' => 'Фирма', //счет от фирмы
            'account_number'                 => '№ счета',
            'attention'                      => 'Особое внимание',
            'product_size'                   => 'Размер готового изделия',
            're_print'                       => 'Перепечатка',
            'is_express'                     => 'Срочно',
            'format_printing_block'          => 'Формат печатного блока',
            'num_of_products_in_block'       => 'Кол-во изделий в блоке',
            'num_of_printing_block'          => 'Кол-во печатных блоков',
            'blocks_per_sheet'               => 'Кол-во блоков c листа',
            'blocks_type'                    => 'Тип блока',
            'material_block_format'          => 'Параметры блока',
            'empt'                           => '',
            'specification'                  => 'Спецификация',
            'podryadview'                    => 'Исполнитель',
            'podryad_name_list'              => 'Исполн.Фирм',
            'podryad_total_coast'            => 'Исп. сумма',
            'podryad_total_coast_list'       => 'Исполнитель',
            'podryad_paied_list'             => 'Сумма опл',
            'podryad_residue_list'           => 'Сумма не опл',
            'material_count_list'            => 'Кол-во',
            'material_post_list'             => 'Поставщик',
            'material_name_list'             => 'Материал',
            'material_paied_list'            => 'Сумма опл',
            'material_residue_list'          => 'Сумма не опл',
            'material_info_list'             => 'Доп.инфо',
            'material_ordered'               => 'Заказан',
            'material_delivery'              => 'Получен',
            'material_info_name'             => 'Название',
            'material_info_color'            => 'Цвет',
            'material_info_format'           => 'Формат',
            'material_info_razmerlista'      => 'Размер листа',
            'material_info_density'          => 'Плотность',
            'material_is_on_sklad'           => 'Материал на складе',
            'material_total_coast'           => 'Мат. сумма',
            'material_total_coast_list'      => 'Материал',
            'material_firms'                 => 'Мат. фирма',
            'design_comment'                 => 'Коментарий от дизайнера',
            'proizv_comment'                 => 'Коментарий от производства',
            'material_coast_comerc'          => 'Комерчиская стоимость материала',
            'profit_type'                    => 'Тип прибыли',
            'deadline_time'                  => 'Время',
            'oplata'                         => 'Опл. сумма',
            'oplataText'                     => 'Опл. сумма',
            'oplata_status'                  => 'опл./не опл.',
            'oplata_status_text'             => 'опл./не опл.',
            'other_spends_list'              => 'Прочее',
            'other_spends_list_col'          => 'Траты проч. назв',
            'other_spends_summ'              => 'Прочее сумма',
            'exec_transport_paid'            => 'Транспорт 1',
            'exec_transport2_paid'           => 'Транспорт 2',
            'exec_speed_paid'                => 'Срочность',
            'exec_markup_paid'               => 'Наценка',
            'exec_delivery_paid'             => 'Доставка',
            'exec_bonus_paid'                => 'Бонус',
            'wages'                          => 'Оклад:',
            'percent1'                       => '% Астерион',
            'percent2'                       => '% Подрядчик',
            'percent3'                       => '% Сверх прибыль',
            'pechiatnikTxt'                  => 'Печатник',
            'zakUrFace'                      => 'Юр.лицо заказчика'
        ];
    }

}
