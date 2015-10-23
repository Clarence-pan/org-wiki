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
     * @param string $keyword
     * @return array
	 */
	public function search($keyword)
	{
        if (!$keyword){
            return [
                'repo' => $this,
                'keyword' => $keyword,
            ];
        }

        chdir($this->path);
        $cmd = sprintf('find . -type d "(" -path "*/.htmlCache" -o -path "*/.git" ")" -prune -type d "(" -name ".#*" -o -name "*.png" -o -name "*.gif" -o -name "*.jpg" -o -name "*.jpeg" -o -name "*.bmp" -o -name "*.bin" -o -name "*.bak" -o -name "*~" -o -name "*.ico" ")" -prune -o  -type f "(" -iname "*.*" ")" -print0 | "xargs" -0 grep -i -nH -e "%s"', addslashes($keyword));
        exec($cmd, $outputLines, $error);
        if ($error !== 0){
            return [
                'repo' => $this,
                'keyword' => $keyword,
                'cmd' => $cmd,
                'found' => [],
                'others' => $outputLines,
            ];
        }

        $found = [];
        $others = [];
        foreach ($outputLines as $line) {
            if (preg_match('/^(?<file>[^:]*):(?<line>\d+):(?<content>.*)$/', $line, $matches)){
                $matches['file'] = Utils::normalizePath($matches['file']);
                if (!isset($found[$matches['file']])){
                    $found[$matches['file']] = [];
                }

                $found[$matches['file']][$matches['line']] = $matches['content'];
            } else {
                $others[] = $line;
            }
        }

        return [
            'repo' => $this,
            'keyword' => $keyword,
            'cmd' => $cmd,
            'found' => $found,
            'others' => $others
        ];
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
                $pages[] = $self->getPageByName($file, false);
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