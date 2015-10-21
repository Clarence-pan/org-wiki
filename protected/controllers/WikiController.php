<?php

/**
 * Class WikiController
 */
class WikiController extends Controller {

    public function filters(){
        return array(
            array(
                'CAccessControlFilter',
                'rules' => array(
                    array('allow', 'users' => array('@')),
                    array('deny', 'users' => array('?'))
                )
            )
        );
    }

    public function actionIndex(){
        $user = User::getCurrentLoginUser();
        $pages = $user->repository->getPages();

        $this->render('index', array('pages' => $pages));
    }

    public function actionView($pageName){
        $user = User::getCurrentLoginUser();
        $page = $user->repository->getPageByName($pageName);
        $this->render('view', array('page' => $page));
    }

    /**
     * 转换编码
     */
    public function actionIconv(){
        $request = Yii::app()->request;

        if (!$request->isPostRequest){
            throw new CHttpException(400, "Invalid request type");
        }

        $pageName = $request->getParam('pageName');
        $from = $request->getParam('from');
        $to = $request->getParam('to');

        if (!$pageName || !$from || !$to){
            throw new CHttpException(404, "Invalid params");
        }

        $user = User::getCurrentLoginUser();
        $page = $user->repository->getPageByName($pageName);
        if (!$page){
            throw new CHttpException(404, "Page not found");
        }

        $cmd = sprintf('iconv -f "%s" -t "%s" "%s"', $from, $to, $page->path);
        system($cmd, $return);
        if ($return != 0){
            throw new CHttpException(500, "Iconv failed: ".$return);
        }
        echo 'OK';
    }
} 