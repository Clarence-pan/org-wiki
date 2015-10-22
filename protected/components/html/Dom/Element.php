<?php

namespace html\dom;
use html\err\InvalidPropertyException;
use html\err\NotImplementedException;

/**
 * Class Element
 * @package html\dom
 * @property $tagName string
 * @property $html string
 * @property $innerText string
 * @property $innerHtml string
 */
class Element {
    protected function __construct($tagName, $properties){
        $this->tagName = $tagName;
        $this->setProperties($properties);
    }

    protected function setProperties($properties){
        foreach ($properties as $name => $value) {
            $this->{$name} = $value;
        }
    }

    public function getTagName(){
        return $this->_tagName;
    }

    public function setTagName($tagName){
        $this->_tagName = strtolower($tagName);
    }

    public function setAttribute($name, $value){
        $this->_attributes[$name] = $value;
    }

    public function getAttribute($name){
        return $this->_attributes[$name];
    }

    /**
     * @param string $name
     * @param null $value
     * @return $this|mixed
     */
    public function attr($name, $value=null){
        if (is_null($value)){
            return $this->_attributes[$name];
        } else {
            $this->_attributes[$name] = $value;
            return $this;
        }
    }

    public function add(Element $element){
        $this->_children[] = $element;
        return $this;
    }

    /**
     * @param Element $element
     * @return $this
     */
    public function remove(Element $element){
        foreach ($this->_children as $index => $child) {
            if ($element === $child){
                array_slice($this->_children, $index, 1);
                return $this;
            }
        }
        return $this;
    }

    /**
     * Empty all children
     * @return $this
     */
    public function clear(){
        $this->_children = [];
        return $this;
    }

    /**
     * Append an element
     * @param Element $element
     * @return $this
     */
    public function append(Element $element){
        $this->_children[] = $element;
        return $this;
    }

    /**
     * Prepend an element
     * @param Element $element
     * @return $this
     */
    public function prepend(Element $element){
        array_unshift($this->_children, $element);
        return $this;
    }

    public function getHtml(){
        return $this->toHtml();
    }

    public function setHtml($html){
        throw new NotImplementedException(__METHOD__);
    }

    public function setText($text){
        throw new NotImplementedException(__METHOD__);
    }

    public function getText(){
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @return string Inner HTML
     * @throws \html\err\InvalidPropertyException
     */
    public function getInnerHtml(){
        if (!$this->_children){
            return '';
        }

        $htmlChildren = [];
        foreach ($this->_children as $child) {
            if (!$child instanceof Element){
                throw new InvalidPropertyException("children");
            }
            $htmlChildren[] = $child->html;
        }

        return implode('', $htmlChildren);
    }

    public function setInnerHtml($html){
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @return string HTML
     */
    public function toHtml(){
        return sprintf('<%s%s>%s</%s>', $this->tagName, $this->_getAttributesAsString(), $this->innerHtml, $this->tagName);
    }

    /**
     * encode html
     * @param $text string
     * @return string
     */
    public static function encode($text){
        return htmlspecialchars($text);
    }

    public function __get($propertyName){
        if (method_exists($this, ($getter = "get" . $propertyName))){
            return $this->{$getter}();
        } else if (property_exists($this, ($innerPropertyName = '_' . $propertyName))){
            return $this->{$innerPropertyName};
        }

        throw new InvalidPropertyException($propertyName);
    }

    public function __set($propertyName, $value){
        $setter = 'set' . $propertyName;
        if (method_exists($this, $setter)){
            return $this->{$setter}($value);
        } else if (property_exists($this, ($innerPropertyName = '_' . $propertyName))){
            return $this->{$innerPropertyName} = $value;
        }

        throw new InvalidPropertyException($propertyName);
    }

    protected function _getAttributesAsString(){
        if (!$this->_attributes){
            return '';
        }

        $all = ' ';
        foreach ($this->_attributes as $name => $value) {
            $all .= self::encode($name).'="'.addslashes(self::encode($value)).'" ';
        }

        return $all;
    }

    protected $_tagName;
    protected $_attributes;
    protected $_children;
} 