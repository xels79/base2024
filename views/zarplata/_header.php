<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\components\MyHelplers;

$idHtml = isset($idHtml) ? $idHtml : false;
?>
<?php if ($idHtml === false): ?>
    <div class="zarplata-table">
        <h3 class="zarplata-caption">
            <b><i><?= $monthName ?> <?= $curentYear ?>г. </i></b> - рабочих дней: <b><?= $day_count ?></b> или : <b><?= $day_count * 8 ?></b> <?= MyHelplers::endingNums($day_count * 8, ['час', 'часа', 'часов']) ?>
        </h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Почасовая</th>
                    <th class="space2">&nbsp;</th>
                    <th>оклад</th>
                    <th>руб/час</th>
                    <th>часы</th>
                    <th>з/п</th>
                    <th>премия</th>
                    <th>вычеты</th>
                    <th class="space">&nbsp;</th>
                    <th>карта</th>
                    <th>карта</th>
                    <th>22 число</th>
                    <th></th>
                    <th>07 число</th>
                    <th>Остаток</th>
                    <th>Пояснения</th>
                </tr>
            </thead>
            <tbody>
                <?=
                $this->render('_rowsOther', [
                    'zarplata'    => $zarplata,
                    'month_id'    => $month_id,
                    'payment_ids' => [0, 1, 2, 3, 4],
                    'idHtml'      => $idHtml,
                    'day_count'   => $day_count
                ])
                ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <table><tbody>
            <?=
            $this->render('_rowsOther', [
                'zarplata'    => $zarplata,
                'month_id'    => $month_id,
                'payment_ids' => [0, 1, 2, 3, 4],
                'idHtml'      => $idHtml,
                'day_count'   => $day_count
            ])
            ?>
        </tbody></table>
<?php endif; ?>

