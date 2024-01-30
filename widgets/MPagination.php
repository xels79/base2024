<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;

/**
 * Description of MPagination
 *
 * @author Александр
 */
class MPagination extends \yii\data\Pagination{
    public function createUrl($page, $pageSize = null, $absolute = false)
    {
        return 'test';
        Yii::trace('MPagination', \yii\helpers\VarDumper::dumpAsString($this->params));
        Yii::$app->end();
        $page = (int) $page;
        $pageSize = (int) $pageSize;
        return 'test';
        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page == 0 && $this->forcePageParam) {
            $params[$this->pageParam] = $page + 1;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize != $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }
        $params[0] = $this->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        } else {
            return $urlManager->createUrl($params);
        }
    }
}
