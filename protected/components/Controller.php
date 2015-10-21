<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    /**
     * @return string|void
     */
    public function createUrl($route,$params=array(),$ampersand='&'){
        if ($route == '/site/index'){
            return '/index.php' . self::buildQuery($params);
        } elseif ($route == '/wiki/view' && $params['pageName']){
            $pageName = $params['pageName'];
            unset($params['pageName']);
            return '/wiki/'.$pageName.self::buildQuery($params);
        } elseif ($route == '/wiki/index'){
            return '/list'.self::buildQuery($params);
        } elseif ($route == '/site/page' && $params['view']){
            return '/page/'.$params['view'];
        }

        return parent::createUrl($route, $params, $ampersand);
    }

    /**
     * build query with params
     * @param $params array
     * @return string
     */
    public static function buildQuery($params){
        return $params ? '?'.http_build_query($params) : '';
    }

}