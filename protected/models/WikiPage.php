<?php


/**
 * Class WikiPage
 * @property string textContent
 * @property string htmlContent
 * @property string path
 * @property string htmlCachePath
 * @property string name
 * @property string title
 * @property WikiRepository repository
 * @property User owner
 */
abstract class WikiPage extends BaseModel
{
    public static function create($attributes){
        $pageName = $attributes['name'];
        if (isset($attributes['pageType'])){
            $pageType = $attributes['pageType'];
        } else {
            $pageType = Utils::getFileExt($pageName);
        }

        $class = ucfirst($pageType) . 'WikiPage';
        if (!class_exists($class)){
            throw new WikiPageTypeNotSupportedException($pageType, $pageName);
        }

        return new $class($attributes);
    }

    /**
     * @return User
     */
    public function getOwner(){
        return $this->repository->getOwner();
    }

    /**
     * @return string HTML内容
     */
    public function getHtmlContent(){
        $self = $this;
        return Lazy::init($this->_htmlContent, function() use ($self){
            if (WIKI_HTML_CACHE_ENABLE and file_exists($this->htmlCachePath) and filemtime($this->htmlCachePath) >= filemtime($this->path)){
                return file_get_contents($this->htmlCachePath);
            }

            $html = $self->toHtml();

            Utils::mkdirIfNotExists(dirname($this->htmlCachePath));
            file_put_contents($this->htmlCachePath, $html);

            return $html;
        });
    }

    /**
     * @return string html
     */
    public function toHtml(){
        throw new \html\err\NotImplementedException(__METHOD__);
    }

    /**
     * @return string 获取文本内容
     */
    public function getTextContent(){
        $self = $this;
        return Lazy::init($this->_textContent, function() use($self){
            return file_get_contents($self->path);
        });
    }

    /**
     * @return string 获取文件的路径，包括后缀名
     */
    public function getPath(){
        return Utils::concatPath($this->repository->path, $this->name);
    }

    /**
     * @return string html缓存的路径
     */
    public function getHtmlCachePath(){
        return Utils::concatPath($this->repository->htmlCachePath, $this->name.'.html');
    }

    /**
     * @return string 获取文件的扩展名
     */
    public static function getFileExtension(){
        return Config::instance()->wikiPageFileExtensionName;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        throw new \html\err\NotSupportedException(__METHOD__);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->name;
    }

    /**
     * @param WikiRepository $repository
     */
    public function setRepository($repository) {
        $this->_repository = $repository;
    }

    /**
     * @return WikiRepository
     */
    public function getRepository() {
        return $this->_repository;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return array 获取代码段的类型
     */
    public function getCodeBlockTypes(){
        if (preg_match_all('/<pre\s+class="brush:\s*(?<lang>\w+)"/', $this->htmlContent, $matches)){
            return array_unique((array)$matches['lang']);
        }

        return [];
    }

    private $_name;
    private $_textContent;
    private $_htmlContent;
    private $_repository;
}