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

    public function actionView($pageName, $embed=false){
        try{
            $user = User::getCurrentLoginUser();
            $page = $user->repository->getPageByName($pageName);
            if ($page instanceof ImageWikiPage){
                $this->renderRawWikiPage($page);
            } else {
                $this->layout = $embed ? false : $this->layout;
                $this->render('view', array('page' => $page));
            }
        } catch (NotFoundException $e){
            throw new CHttpException(404, $e->getMessage());
        }
    }

    public function actionViewRaw($pageName){
        try{
            $user = User::getCurrentLoginUser();
            $page = $user->repository->getPageByName($pageName);
            $this->renderRawWikiPage($page);
        } catch (NotFoundException $e){
            throw new CHttpException(404, $e->getMessage());
        }
    }

    public function actionSearch($keyword=null){
        try{
            $user = User::getCurrentLoginUser();
            $searchResult = $user->repository->search($keyword);
            $this->render('search', $searchResult);
        } catch (NotFoundException $e){
            throw new CHttpException(404, $e->getMessage());
        }
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

        ob_start();
        $cmd = sprintf('iconv -f "%s" -t "%s" "%s"', $from, $to, $page->path);
        system($cmd, $return);
        $converted = ob_get_contents();
        ob_end_clean();

        if ($return != 0){
            throw new CHttpException(500, "Iconv failed: ".$return);
        }

        file_put_contents($page->path, $converted);

        echo 'OK';
    }

    /**
     * render raw content of wiki page
     * @param WikiPage $page
     * @param bool     $end
     */
    public function renderRawWikiPage(WikiPage $page, $end=true){
        header('Content-Type: '.$page->contentType);
        readfile($page->path);
        $end and Yii::app()->end();
    }
} 