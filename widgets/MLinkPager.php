<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;
/**
 * Description of MLinkPager
 *
 * @author Александр
 */
class MLinkPager extends \yii\widgets\LinkPager{
    public $selectPageCssClass='sel';
    public $dropdownCss='dropdown';
    public $selectPage=false;
    protected function renderSelectList(){
//        return Html::tag('li','test');
        $options = ['class' =>$this->pageCssClass];
        Html::addCssClass($options, $this->dropdownCss);
        $links=[];
        $a=Html::tag('a','Выберите страницу'.'<b class="caret"></b>',  array_merge($this->linkOptions,[
            'id'=>$this->id.'_selPage',
            'role'=>'button',
            'data-toggle'=>'dropdown',
            'href'=>'#']));
        for ($i=1;$i<=$this->pagination->pageCount;$i++){
            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = $i-1;           
            if ($this->pagination->page===$i-1){
                Html::addCssClass($linkOptions, 'active');
            }
            $links[]=[
                'label'=>'Стр.: '.$i,
                'url'=>$this->pagination->createUrl($i-1),
                'linkOptions'=>$linkOptions
            ];
        }
        return Html::tag('li',$a.Dropdown::widget([
            'items'=>$links,
            'options'=>[
                'class'=>'dropdown-menu',
                'role'=>'menu',
                'aria-labelledby'=>$this->id.'_selPage'
            ]
        ]),$options);
    }
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
        $buttons = [];
        $currentPage = $this->pagination->getPage();
        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }
        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }
        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
        }
        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }
        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }
        if ($this->pagination->pageCount>$this->maxButtonCount){
            $buttons[]=$this->renderSelectList();
        }
        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }
}
