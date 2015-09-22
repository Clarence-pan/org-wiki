<?php

/**
 * Config
 * @property string $htmlCacheDirName
 * @property string $wikiPageFileExtensionName
 */
class Config extends CComponent
{
    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key){
        if ($this->exists($key)){
            return $this->get($key);
        }
        return parent::__get($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key){
        $this->ensureLoaded();
        return isset($this->_items[$key]);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key){
        $this->ensureLoaded();
        return $this->_items[$key];
    }

    /**
     * 确保已经加载了配置
     */
    public function ensureLoaded(){
        Lazy::init($this->_items, function(){
            /** @var CDbConnection $db */
            $db = Yii::app()->db;
            $items = $db->createCommand()->select(array('key', 'value'))->from('t_config')->queryAll();
            return Matrix::from($items)->indexedBy('key')->column('value');
        });
    }

    /**
     * @return Config
     */
    public static function instance(){
        return Lazy::init(self::$_instance, function(){
           return new Config();
        });
    }

    private $_items;
    private static $_instance;
}