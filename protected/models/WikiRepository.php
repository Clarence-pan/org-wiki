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
            $files = scandir($self->path);
            $pageExtension = WikiPage::getFileExtension();

            foreach ($files as $file){
                if (String::from($file)->endWith($pageExtension)){
                    $pages[] = $self->getPageByName(String::from($file)->cutTail($pageExtension));
                }
            }

            return $pages;
        });
    }

    /**
     * @param $pageName
     * @return WikiPage
     */
    public function getPageByName($pageName){
        $self = $this;
        $r = Lazy::init($this->_cachedPages[$pageName], function()use($self, $pageName){
            return new WikiPage(array(
                'name' => $pageName,
                'repository' => $self
            ));
        });
        return $r;
    }

    /**
     * @return WikiPage
     */
    public function getIndexPage(){
        return $this->getPageByName('index');
    }

    private $_owner;
    private $_pages;
    private $_cachedPages = array();
}