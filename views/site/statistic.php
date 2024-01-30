<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @var $this yii\web\View
 * @var $managers_exists array
 * $var $managers array
 * @var $periud int
 */

use app\widgets\JSRegister;
use app\components\MyHelplers;
use yii\bootstrap\Html;

$this->title = 'Статистика';
$mnCnt = 1;
$dataStore = '';
?>
<?php
JSRegister::begin( [
    'key'      => 'select-form-script',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    let sendData = function () {
        let fd = new FormData($('#select-form').get(0));
        let url = URI(window.location.href);
        let tmp = URI.parse(window.location.href);
        let query = URI.parseQuery(tmp.query);
        let newUrl = URI({
            hostname: tmp.hostname,
            path: tmp.path,
            protocol: tmp.protocol,
        });
        newUrl.setSearch('r', query.r);


        console.log(newUrl.toString());
        if ($.type($(this).attr('name')) !== 'undefined') {
            if ($.type($(this).attr('checked')) !== 'undefined') {
                $(this).removeAttr('checked');
            } else {
                $(this).attr('checked', true);
            }
        }
        if ($.type($(this).attr('data-key')) !== 'undefined') {
            fd.set('periud', $(this).attr('data-key'));
            newUrl.setSearch('periud', $(this).attr('data-key'));
        } else {
            fd.set('periud', $('#select-form').find('.btn-success').attr('data-key'));
            newUrl.setSearch('periud', $('#select-form').find('.btn-success').attr('data-key'));
        }


        if ($.type($(this).attr('data-from')) !== 'undefined') {
            newUrl.setSearch('periud_from', $(this).attr('data-from'));
            fd.set('periud_from', $(this).attr('data-from'));
        } else if (query.periud_from) {
            newUrl.setSearch('periud_from', query.periud_from);
            fd.set('periud_from', query.periud_from);
        }
        if ($.type($(this).attr('data-to')) !== 'undefined') {
            newUrl.setSearch('periud_to', $(this).attr('data-to'));
            fd.set('periud_to', $(this).attr('data-to'));
        } else if (query.periud_to) {
            newUrl.setSearch('periud_to', query.periud_to);
            fd.set('periud_to', query.periud_to);
        }
        $.each($('[type=checkbox]'), function () {
            if ($.type($(this).attr('checked')) !== 'undefined') {
                newUrl.setSearch($(this).attr('name'), 'on');
            }
        });
        window.history.pushState(null, "Title", newUrl.toString());
        $.ajax({
            type: 'post',
            url: $(this).attr('action'),
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            forceSync: false,
            success: function (data) {
                $('#statistic-cont').replaceWith($(data));
                selFReStart();
            }
        });

    };
    let selFReStart = function () {
        $('#select-form').find('ul').find('a').click(function (e) {
            sendData.call(this);
        });
        $('#select-form').find('[type="checkbox"],[type="button"]').click(function (e) {
            if ($(this).attr('data-key') === '0')
                return;
            e.preventDefault();
            sendData.call(this);
        });
    }
    selFReStart();
</script>
<?php JSRegister::end(); ?>

<div class="statistic" id="statistic-cont">
    <form method="post" id="select-form" class="hidden-print">
        <div class="row">
            <div class="col-md-1 text-right">Менеджер:</div>
            <?php foreach ( $managers_exists as $el ): ?>
                <?php if ( $mnCnt / 5 === 1 ): ?>
                </div>
                <div class="row">
                <?php endif; ?>
                <div class="col-md-1 <?= ($mnCnt / 5 === 1) ? ' col-md-offset-1' : '' ?>">
                    <div class="form-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="inlineCheckbox<?= $mnCnt++ ?>" value="on" name="managers[<?= $el['id'] ?>]"<?= in_array( $el['id'], array_keys( $managers ) ) ? ' checked' : '' ?>><?= $el['realname'] ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <div class="btn-group" role="group">
                    <?=
                    Html::button( 'Месяц' . Html::tag( 'span', '', ['class' => 'caret'] ), [
                        'class' => ['btn', 'btn-' . ($periud === 0 ? 'success' : 'default'),],
                        'data'  => [
                            'key'    => 0,
                            'toggle' => 'dropdown'
                ]] )
                    ?>
                    <ul class="dropdown-menu">
                        <?php foreach ( array_reverse( $intervals['dd'] ) as $dd ): ?>
                            <?php
                            if ( $dataStore !== Yii::$app->formatter->asDate( $dd['from'], 'Y' ) ) {
                                $dataStore = Yii::$app->formatter->asDate( $dd['from'], 'Y' );
                                echo Html::tag( 'li', $dataStore . ' год', ['class' => 'dropdown-header'] );
                            }
                            ?>
                            <?=
                            Html::tag( 'li', Html::tag( 'a', Yii::$app->formatter->asDate( $dd['from'], 'MMMM' ), [
                                        'href' => '#',
                                        'data' => [
                                            'key'  => 0,
                                            'from' => $dd['from'],
                                            'to'   => $dd['to']
                                        ]
                            ] ) )
                            ?>
                        <?php endforeach; ?>
                    </ul>
                    <?=
                    Html::button( '3 Месяца', ['class' => ['btn', 'btn-' . ($periud === 1 ? 'success' : 'default')],
                        'data'  => [
                            'key'  => 1,
                            'from' => $intervals['but']['3M']['from'],
                            'to'   => $intervals['but']['3M']['to']
                ]] )
                    ?>
                    <?=
                    Html::button( '6 Месяце', ['class' => ['btn', 'btn-' . ($periud === 2 ? 'success' : 'default')],
                        'data'  => [
                            'key'  => 2,
                            'from' => $intervals['but']['6M']['from'],
                            'to'   => $intervals['but']['6M']['to']
                ]] )
                    ?>
                    <?=
                    Html::button( 'Год', ['class' => ['btn', 'btn-' . ($periud === 3 ? 'success' : 'default')],
                        'data'  => [
                            'key'  => 3,
                            'from' => $intervals['but']['12M']['from'],
                            'to'   => $intervals['but']['12M']['to']
                ]] )
                    ?>
                </div>
            </div>
        </div>
    </form>
    <div class="m-row">
        <?php for ( $i = 0, $row = 1; $i < count( $test ); $i++, $row = 1 ): ?>
            <?php if ( $i && $i % 3 === 0 ): ?>
            </div>
            <div class="m-row">
            <?php endif; ?>
            <div class="statistic-info-pan">
                <img src="/css/pic/Grafiki_manager_1.png"/>
                <span class="m-name"><?= $test[$i]['manger_name'] ?></span>
                <span class="m-name m-date"><?= $current_interval ?></span>
                <span class="m-small m-row1 m-1-col"><?= $test[$i]['materials'] ?></span>
                <span class="m-small m-row1 m-2-col"><?= round( $test[$i]['materials'] - $test[$i]['msterial_spend'], 0 ) ?></span>
                <span class="m-small m-row2 m-1-col"><?= round( $test[$i]['pod'], 0 ) ?></span>
                <span class="m-small m-row2 m-2-col"><?= round( $test[$i]['pod_profit'], 0 ) ?></span>
                <span class="m-small m-row3 m-1-col"><?= round( $test[$i]['asterion'], 0 ) ?></span>
                <span class="m-small m-row3 m-2-col"><?= round( $test[$i]['asterion_profit'], 0 ) ?></span>
                <span class="m-small m-row4 m-1-col"><?= round( $test[$i]['other_total'], 0 ) ?></span>
                <span class="m-small m-row4 m-2-col"><?= round( $test[$i]['other_total'] - $test[$i]['other_spend'], 0 ) ?></span>
                <span class="m-small m-row4 m-25-col"><?= round( $test[$i]['other_percent'], 0 ) ?></span>
                <span class="m-medium m-total1"><?= round( $test[$i]['total1'], 0 ) ?></span>
                <span class="m-medium m-total1 m-profit"><?= round( $test[$i]['total1_profit'], 0 ) ?></span>
                <span class="m-small m-row6 m-3-col"><?= round( $test[$i]['w_other_total'], 0 ) ?></span>
                <span class="m-small m-row6 m-4-col"><?= round( $test[$i]['w_other_total'] - $test[$i]['w_other_spend'], 0 ) ?></span>
                <span class="m-small m-row7 m-3-col"><?= round( $test[$i]['w_total'], 0 ) ?></span>
                <span class="m-small m-row7 m-4-col"><?= round( $test[$i]['w_total'] - $test[$i]['w_spend'], 0 ) ?></span>
                <span class="m-small m-row2 m-25-col"><?= round( $test[$i]['pod_percent'], 0 ) ?></span>
                <span class="m-small m-row3 m-25-col"><?= round( $test[$i]['asterion_percent'], 0 ) ?></span>
                <span class="m-medium m-row8 m-zp-col"><?= round( $test[$i]['wages'], 0 ) ?></span>
                <span class="m-medium m-row8 m-zpperc-col"><?= round( $test[$i]['wages_percent'], 0 ) ?></span>
                <?php foreach ( $works as $work ): ?>
                    <span class="m-row<?= $row ?> m-header-col"><?= $work['name'] ?></span>
                    <span class="m-small m-row<?= $row ?> m-3-col"><?= round( $test[$i]['w_total' . MyHelplers::translit( $work['name'], true )], 2 ) ?></span>
                    <span class="m-small m-row<?= $row++ ?> m-4-col"><?= round( $test[$i]['w_total' . MyHelplers::translit( $work['name'], true )] - $test[$i]['w_total' . MyHelplers::translit( $work['name'], true ) . '_spend'], 2 ) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
