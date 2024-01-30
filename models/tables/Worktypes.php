<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\tables;

/**
 * Description of Zak
 *
 * @author Александр
 */
class Worktypes extends TablesSmall{
    public static function tableName() {
        return 'worktypes';
    }
    public function getCatText(){
        return ['А','Б','В','Д'][$this->category];
    }
}
