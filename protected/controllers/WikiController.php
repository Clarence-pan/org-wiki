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
} 