<?php

/**
 * This is the model class for table "t_wiki_repository".
 *
 * The followings are the available columns in table 't_wiki_repository':
 * @property string $id
 * @property integer $ownerId
 * @property string $path
 * @property string $htmlCachePath
 * @property WikiPage $indexPage
 */
class WikiRepository extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WikiRepository the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_wiki_repository';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ownerId, path', 'required'),
			array('ownerId', 'numerical', 'integerOnly'=>true),
			array('path', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerId, path', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ownerId' => 'Owner',
			'path' => 'Path',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('ownerId',$this->ownerId);
		$criteria->compare('path',$this->path,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @return string
     */
    public function getHtmlCachePath(){
        return Utils::concatPath($this->path, Config::instance()->htmlCacheDirName);
    }

    /**
     * @return User the owner
     */
    public function getOwner(){
        $self = $this;
        return Lazy::init($this->_owner, function() use ($self){
            return User::model()->findByPk($self->ownerId);
        });
    }

    /**
     * @return array of WikiPages
     */
    public function getPages(){
        $self = $this;
        return Lazy::init($this->_pages, function() use ($self){
            $pages = array();

            foreach (FileFinder::find($self->path, ['fileExt' => WikiRepository::getAvailableFileTypes()]) as $file){
                $pages[] = $self->getPageByName($file);
            }

            return $pages;
        });
    }

    /**
     * @param $pageName
     * @param bool $ensureExists
     * @return WikiPage
     */
    public function getPageByName($pageName, $ensureExists=true){
        $self = $this;
        $pageName = $this->normalizePageName($pageName);
        $page = Lazy::init($this->_cachedPages[$pageName], function()use($self, $pageName){
            return WikiPage::create(array(
                'name' => $pageName,
                'repository' => $self
            ));
        });

        if ($ensureExists){
            $pageRealPath = realpath($page->path);
            if (!$pageRealPath || !file_exists($pageRealPath)){
                throw new WikiPageNotFoundException($pageName, $this->path);
            }

            // ensure page's path should start with repo's path
            $repoRealPath = realpath($this->path);
            if (!$repoRealPath || strncmp($repoRealPath, $pageRealPath, strlen($repoRealPath)) != 0){
                throw new WikiPageNotFoundException($pageName, $this->path);
            }
        }

        return $page;
    }

    /**
     * @return WikiPage
     */
    public function getIndexPage(){
        return $this->getPageByName('index');
    }

    /**
     * @param $pageName string
     * @return string
     */
    public function normalizePageName($pageName){
        $pageName = Utils::normalizePath($pageName);

        $filePath = Utils::concatPath($this->path, $pageName);
        if (is_file($filePath)){
            return $pageName;
        }

        $fileTypeList = self::getAvailableFileTypes();

        foreach ($fileTypeList as $fileType) {
            if (is_file($filePath . '.' . $fileType)){
                return $pageName . '.' . $fileType;
            }
        }

        throw new WikiPageNotFoundException($this->path, $pageName);
    }

    /**
     * @return array 所有支持的文件类型
     */
    public static function getAvailableFileTypes(){
        return Lazy::init(self::$_supportedPageFileTypes, function(){
            return explode('|', WIKI_AVAILABLE_FILE_TYPES);
        });
    }

    private static $_supportedPageFileTypes;
    private $_owner;
    private $_pages;
    private $_cachedPages = array();
}