<?php
use yii\db\Migration;
use app\models\sklad\SkladColorSerias;
use app\models\sklad\SkladColors;
use app\models\sklad\SkladColorsInfo;
use app\models\sklad\SkladColorOtherDate;
use app\models\sklad\SkladPaket;
use app\models\sklad\SkladPaketInfo;
use app\models\sklad\SkladPaketFirms;
use app\models\sklad\SkladBumaga;
use app\models\sklad\SkladBumagaInfo;
use app\models\sklad\SkladBumagaFirms;
use yii\helpers\Console;
/**
 * Class m180407_121724_sklad_paints
 */
class m180407_121724_sklad_paints extends Migration
{
    public $colors=[
        ['color'=>'fefefe','name'=>'Белая'],
        ['color'=>'f3e531','name'=>'Желтая'],
        ['color'=>'fad706','name'=>'Желтая'],
        ['color'=>'ebcc15','name'=>'Желтая'],
        ['color'=>'e26b0a','name'=>'Оранж'],
        ['color'=>'ff0000','name'=>'Красная'],
        ['color'=>'ff0000','name'=>'Красная'],
        ['color'=>'cc0000','name'=>'Красная'],
        ['color'=>'800000','name'=>'Бордовая'],
        ['color'=>'cc0066','name'=>'Розовая'],
        ['color'=>'538dd5','name'=>'Голубая'],
        ['color'=>'0070c0','name'=>'Голубая'],
        ['color'=>'000066','name'=>'Синия'],
        ['color'=>'003399','name'=>'Синия'],
        ['color'=>'000e2a','name'=>'Т. Синия'],
        ['color'=>'018d40','name'=>'Зеленая'],
        ['color'=>'00cc00','name'=>'Зеленая'],
        ['color'=>'006009','name'=>'Зеленая'],
        ['color'=>'007400','name'=>'Зеленая'],
        ['color'=>'e26b0a','name'=>'Охра'],
        ['color'=>'4c2504','name'=>'Коричн'],
        ['color'=>'000000','name'=>'Черная'],
        ['color'=>'ffffff','name'=>'Прозрач.'],

        ['color'=>'00b0f0','name'=>'C'],
        ['color'=>'cc0066','name'=>'M'],
        ['color'=>'ffff00','name'=>'Y'],
        ['color'=>'000000','name'=>'K']
    ];
    public $serias=[
        [
            'name'=>'85 серия - Пакеты',
            'content'=>[
                85101,
                85200,
                85201,
                85202,
                85301,
                85302,
                85303,
                85304,
                85305,
                85343,
                85400,
                85401,
                85403,
                85404,
                85405,
                85500,
                85501,
                85503,
                85541,
                85601,
                85603,
                85700,
                85000,
                85497,
                85397,
                85297,
                85797,
            ]
        ],
        [
            'name'=>'38 серия - матовая',
            'content'=>[
                38024,
                38200,
                38201,
                38202,
                38301,
                38302,
                38303,
                38304,
                38305,
                38343,
                38400,
                38401,
                38403,
                38404,
                38405,
                38500,
                38501,
                38503,
                38541,
                38601,
                38603,
                38700,
                38000,

                38497,
                38397,
                38297,
                38797,
            ]
        ],
        [
            'name'=>'35 серия - глянец',
            'content'=>[
                35101,
                35200,
                35201,
                35202,
                35301,
                35302,
                35303,
                35304,
                35305,
                35343,
                35400,
                35401,
                35403,
                35404,
                35405,
                35500,
                35501,
                35503,
                35541,
                35601,
                35440,
                35700,
                35000,

                35497,
                35397,
                35297,
                35797,
            ]
        ],
        [
            'name'=>'58 серия - трасфер',
            'content'=>[
                58101,
                58200,
                58201,
                58202,
                58301,
                58302,
                58303,
                58304,
                58305,
                58343,
                58400,
                58401,
                58403,
                58404,
                58405,
                58500,
                58501,
                58503,
                58541,
                58601,
                58603,
                58700,
                58000,

                58497,
                58397,
                58297,
                58797,
            ]
        ]
    ];
    public $paketsSizes=['38 50', '30 40', '22 34', '36 45', '45 50', '60 50', '70 50', '50 60', '70 60'];
    public $pakets=[
        [
            'name'=>'Тонар',
            'content'=>[
                ['name'=>'Белые','color'=>'ffffff'],
                ['name'=>'Черные','color'=>'000000'],
                ['name'=>'Темно-Синие','color'=>'0f243e'],
                ['name'=>'Ярко-Синие','color'=>'0000ff'],
                ['name'=>'Серебро','color'=>'a9a9a9'],
                ['name'=>'Золото','color'=>'c2a702'],
                ['name'=>'Оранжевые','color'=>'fc8208'],
                ['name'=>'Желтые','color'=>'ffff00'],
                ['name'=>'Красные','color'=>'ff0000'],
                ['name'=>'Зеленые','color'=>'008e00'],
                ['name'=>'Бежевые','color'=>'ffff66'],
                ['name'=>'Бордовые','color'=>'800000'],
                ['name'=>'Серые','color'=>'717171'],
                ['name'=>'Сиреневые','color'=>'7030a0'],
            ]
        ],
        [
            'name'=>'Аванпак',
            'content'=>[
                ['name'=>'Прозрачный','color'=>'ffffff'],
                ['name'=>'Белые','color'=>'ffffff'],
                ['name'=>'Черные','color'=>'000000'],
                ['name'=>'Темно-Синие','color'=>'0f243e'],
                ['name'=>'Светло-Синие','color'=>'1111ff'],
                ['name'=>'Зеленый','color'=>'00bb00'],
                ['name'=>'Голубые','color'=>'96ceff'],
                ['name'=>'Бежевые','color'=>'ffff66'],
                ['name'=>'Красные','color'=>'ff0000'],
                ['name'=>'Оранжевые','color'=>'fc8208'],
                ['name'=>'Фиолетовый','color'=>'8b73ff'],
                ['name'=>'Серебро','color'=>'edf7f4'],
                ['name'=>'Бордовые','color'=>'800000'],
                ['name'=>'Желтые','color'=>'ffff00'],
            ]
        ]
    ];
    public $bumaga=[
        [
            'name'=>'ТачКавер',
            'content'=>[
                ['name'=>'Холодный голубой','color'=>'B2EAC1'],
                ['name'=>'Сине-зеленый','color'=>'155368'],
                ['name'=>'Ярко-красный','color'=>'FF0000'],
                ['name'=>'Синий Парижский','color'=>'1F1F51'],
                ['name'=>'Слоновая кость','color'=>'FFFB91'],
                ['name'=>'Зеленый','color'=>'094109'],
                ['name'=>'Св. серый','color'=>'E4E285'],
                ['name'=>'Серый','color'=>'505050'],
                ['name'=>'Бежевый','color'=>'D59427'],
                ['name'=>'Св. коричневый', 'color'=>'9A4400'],
                ['name'=>'Коричневый', 'color'=>'592C21'],
                ['name'=>'Бордо','color'=>'8B0E13'],
                ['name'=>'Сливовый','color'=>'80194F']
            ]
        ],
        [
            'name'=>'Полилит',
            'content'=>[
                ['name'=>'296'],
                ['name'=>'396'],
                ['name'=>'501']
            ]
        ],
        [
            'name'=>'Самоклейка пленка',
            'content'=>[
                ['name'=>'Белая пост.'],
                ['name'=>'Прозрачная пост.'],
                ['name'=>'Белая съемный'],
                ['name'=>'Прозрачная съемый']
            ]
        ],
        [
            'name'=>'Сплендергель',
            'content'=>[
                ['name'=>'300 гр.'],
                ['name'=>'340 гр.'],
            ]
        ],
        [
            'name'=>'Конверты',
            'content'=>[
                ['name'=>'С65 без окна'],
                ['name'=>'С5 без окна'],
                ['name'=>'С4 без окна'],
                ['name'=>'С 65 с окном'],
                ['name'=>'С5 с окном'],
                ['name'=>'С4 с окном']
            ]
        ],
    ];
    /**
     * {@inheritdoc}
     */
    private function addPaketTables(){
        $this->createTable('sklad_paket_info',[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Название'),
            'color'=>$this->string(7)->notNull()->comment('Цвет'),
        ]);
        $this->createTable('sklad_paket_firms', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Серия')
        ]);
        
        $cols=[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'color_ref'=>$this->integer()->notNull()->comment('К цвету'),
            'firm_ref'=>$this->integer()->notNull()->comment('К фирмы'),
            'lastupdate'=>$this->timestamp()->notNull()->comment('Обновлено'),
        ];
        foreach ($this->paketsSizes as $el){
            $tmp= explode(' ', $el);
            $cols['coast_sz_'.$tmp[0].'x'.$tmp[1]]=$this->double()->defaultValue(0)->comment('Цена '.$tmp[0].'x'.$tmp[1]);
            $cols['sklad_sz_'.$tmp[0].'x'.$tmp[1]]=$this->double()->defaultValue(0)->comment('Склад '.$tmp[0].'x'.$tmp[1]);
            $cols['rezerv_sz_'.$tmp[0].'x'.$tmp[1]]=$this->double()->defaultValue(0)->comment('Резерв'.$tmp[0].'x'.$tmp[1]);
        }
        $this->createTable('sklad_paket',$cols);
        $this->createIndex('fk-paket-color_ref', 'sklad_paket', 'color_ref');
        $this->addForeignKey('fk-paket-color_ref', 'sklad_paket', 'color_ref', 'sklad_paket_info', 'id');
        $this->createIndex('fk-paket-firm_ref', 'sklad_paket', 'firm_ref');
        $this->addForeignKey('fk-paket-firm_ref', 'sklad_paket', 'firm_ref', 'sklad_paket_firms', 'id');
    }
    private function addBumagaTables(){
        $this->createTable('sklad_bumaga_info',[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Название'),
            'color'=>$this->string(7)->notNull()->comment('Цвет'),
        ]);
        $this->createTable('sklad_bumaga_firms', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Серия')
        ]);
        
        $cols=[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'color_ref'=>$this->integer()->notNull()->comment('К цвету'),
            'firm_ref'=>$this->integer()->notNull()->comment('К фирмы'),
            'sklad'=>$this->double()->defaultValue(0)->comment('Склад'),
            'rezerv'=>$this->double()->defaultValue(0)->comment('Резерв'),
            'lastupdate'=>$this->timestamp()->notNull()->comment('Обновлено'),
        ];
        $this->createTable('sklad_bumaga',$cols);
        $this->createIndex('fk-bumaga-color_ref', 'sklad_bumaga', 'color_ref');
        $this->addForeignKey('fk-bumaga-color_ref', 'sklad_bumaga', 'color_ref', 'sklad_bumaga_info', 'id');
        $this->createIndex('fk-bumaga-firm_ref', 'sklad_bumaga', 'firm_ref');
        $this->addForeignKey('fk-bumaga-firm_ref', 'sklad_bumaga', 'firm_ref', 'sklad_bumaga_firms', 'id');
    }
    public function safeUp()
    {
        $this->createTable('sklad_color_serias', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Серия')
        ]);
        $this->createTable('sklad_colors_info',[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->string()->notNull()->comment('Название'),
            'color'=>$this->string(7)->notNull()->comment('Цвет'),
        ]);
        $this->createTable('sklad_colors',[
            'id'=>$this->primaryKey()->comment('Индекс'),
            'article'=>$this->integer()->notNull()->comment('Артикул'),
            'proizvodstvo'=>$this->double()->defaultValue(0)->comment('Пр-во'),
            'sklad'=>$this->double()->defaultValue(0)->comment('Склад'),
            'procurement'=>$this->double()->defaultValue(0)->comment('Купить'),
            'serias_ref'=>$this->integer()->notNull()->comment('К серии'),
            'color_ref'=>$this->integer()->notNull()->comment('К цвету'),
            'lastupdate'=>$this->timestamp()->notNull()->comment('Обновлено'),
        ]);
        $this->createIndex('fk-serias_ref', 'sklad_colors', 'serias_ref');
        $this->addForeignKey('fk-serias_ref', 'sklad_colors', 'serias_ref', 'sklad_color_serias', 'id');
        $this->createIndex('fk-color_ref', 'sklad_colors', 'color_ref');
        $this->addForeignKey('fk-color_ref', 'sklad_colors', 'color_ref', 'sklad_colors_info', 'id');

        $this->createTable('sklad_color_other_date', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'proizvodstvo'=>$this->double()->defaultValue(0)->comment('Пр-во'),
            'sklad'=>$this->double()->defaultValue(0)->comment('Склад'),
            'procurement'=>$this->double()->defaultValue(0)->comment('Купить'),
            'lastupdate'=>$this->timestamp()->notNull()->comment('Обновлено'),            
        ]);
        $this->proceedDate();
        $this->addPaketTables();
        $this->proceedPaketDate();
        $this->addBumagaTables();
        $this->proceedBumagaDate();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-serias_ref', 'sklad_colors');
        $this->dropForeignKey('fk-color_ref', 'sklad_colors');
        $this->dropForeignKey('fk-paket-color_ref', 'sklad_paket');
        $this->dropForeignKey('fk-paket-firm_ref', 'sklad_paket');
        $this->dropForeignKey('fk-bumaga-firm_ref', 'sklad_bumaga');
        $this->dropForeignKey('fk-bumaga-color_ref', 'sklad_bumaga');
        $this->dropIndex('fk-serias_ref', 'sklad_colors');
        $this->dropIndex('fk-color_ref', 'sklad_colors');
        $this->dropIndex('fk-paket-color_ref', 'sklad_paket');
        $this->dropIndex('fk-paket-firm_ref', 'sklad_paket');
        $this->dropIndex('fk-bumaga-firm_ref', 'sklad_bumaga');
        $this->dropIndex('fk-bumaga-color_ref', 'sklad_bumaga');
        
        $this->dropTable('sklad_bumaga_info');
        $this->dropTable('sklad_bumaga_firms');
        $this->dropTable('sklad_bumaga');
        $this->dropTable('sklad_color_serias');
        $this->dropTable('sklad_colors_info');
        $this->dropTable('sklad_colors');
        $this->dropTable('sklad_color_other_date');
        $this->dropTable('sklad_paket_info');
        $this->dropTable('sklad_paket_firms');
        $this->dropTable('sklad_paket');
        return true;
    }
    private function proceedBumagaDate(){
        $max_progress=0;
        $progress=0;
        foreach ($this->bumaga as $el) $max_progress+=count($el['content']);
        Console::startProgress(0, $max_progress,'Настраиваем склад-бумага: ');
        foreach ($this->bumaga as $bumEl){
            $bumaga=new SkladBumagaFirms();
            $bumaga->name=$bumEl['name'];
            $bumaga->save();
            foreach($bumEl['content'] as $subEl){
                if (isset($el['color'])){
                    $color=SkladBumagaInfo::find()->where(['name'=>$subEl['name'],'color'=>$subEl['color']])->one();
                }else{
                    $color=SkladBumagaInfo::find()->where(['name'=>$subEl['name'],'color'=>''])->one();
                }
                if (!$color){
                    $color=new SkladBumagaInfo();
                    $color->name=$subEl['name'];
                    $color->color=isset($subEl['color'])?$subEl['color']:'#';
                    $color->save();
                }
                $model=new SkladBumaga();
                $model->color_ref=$color->id;
                $model->firm_ref=$bumaga->id;
                $model->save();
                Console::updateProgress($progress++, $max_progress-1,'Настраиваем склад-бумага: ');
            }
        }
        Console::endProgress();
    }
    
    private function proceedPaketDate(){
    /*
     * use app\models\sklad\SkladPaket;
use app\models\sklad\SkladPaketInfo;
     * SkladPaketFirms

     */
        $max_progress=0;
        $progress=0;
        foreach ($this->pakets as $el) $max_progress+=count($el['content']);
        Console::startProgress(0, $max_progress,'Настраиваем склад-пакеты: ');
        foreach ($this->pakets as $pakEl){
            $pakFirm=new SkladPaketFirms();
            $pakFirm->name=$pakEl['name'];
            $pakFirm->save();
            foreach ($pakEl['content'] as $subEl){
                if (!$color=SkladPaketInfo::find()->where(['name'=>$subEl['name'],'color'=>$subEl['color']])->one()){
                    $color=new SkladPaketInfo();
                    $color->name=$subEl['name'];
                    $color->color=$subEl['color'];
                    $color->save();
                }
                $model=new SkladPaket();
                $model->color_ref=$color->id;
                $model->firm_ref=$pakFirm->id;
                if (!$model->save()){
                    echo "\nОшибка сохранения:\n";
                    foreach ($model->firstErrors as $key=>$val){
                        echo "'$key'=>'$val'\n";
                    }
                    return false;
                }
                Console::updateProgress($progress++, $max_progress-1,'Настраиваем склад-пакеты: ');
            }
        }
        Console::endProgress();
        return true;
    }
    private function proceedDate(){
        
        $max_progress=count($this->serias[0]['content'])*count($this->serias);
        $progress=0;
        Console::startProgress(0, $max_progress,'Настраиваем склад-краски: ');
        foreach ($this->serias as $ser_el){
            if (!$ser=SkladColorSerias::find()->where(['name'=>$ser_el['name']])->one()){
                $ser=new SkladColorSerias();
                $ser->name=$ser_el['name'];
                $ser->save();
            }
            for ($i=0;$i<count($ser_el['content']);$i++){
                if (!$color=SkladColorsInfo::find()->where(['color'=>$this->colors[$i]['color'],'name'=>$this->colors[$i]['name']])->one()){
                    $color=new SkladColorsInfo();
                    $color->name=$this->colors[$i]['name'];
                    $color->color=$this->colors[$i]['color'];
                    $color->save();
                }
                $pos=new SkladColors();
                $pos->article=$ser_el['content'][$i];
                $pos->serias_ref=$ser->id;
                $pos->color_ref=$color->id;
                $pos->save();
                Console::updateProgress($progress, $max_progress-1,'Настраиваем склад-краски: ');
                if ($progress<$max_progress) $progress++;
            }
        }
        $max_progress=14;
        Console::endProgress();
        Console::startProgress(0, $max_progress,'Настраиваем склад-краски-химия: ');
        for ($i=0;$i<$max_progress;$i++){
            $model=new SkladColorOtherDate();
            $model->save();
            Console::updateProgress($i, $max_progress-1,'Настраиваем склад-краски-химия: ');
        }
        Console::endProgress();
    }
}
