<?php


namespace html\dom;
use html\err\NotSupportedException;

/**
 * Class TextElement
 * @package html\dom
 * @property $text string
 */
class TextElement extends Element{
    public function __construct($text){
        parent::__construct('', ['text' => $text]);
    }

    public function getText(){
        return implode('', $this->_children);
    }

    public function setText($text){
        $this->_children = [ $text ];
    }

    public function toHtml(){
        return self::encode($this->text);
    }

    public function add(Element $element){
        if ($element instanceof TextElement){
            $this->_children[] = $element->text;
            return $this;
        } else {
            throw new NotSupportedException("add ".get_class($element)." to text element");
        }
    }

    public function append(Element $element){
        return $this->add($element);
    }

    public function remove(Element $element){
        throw new NotSupportedException(__METHOD__);
    }


    public function prepend(Element $element){
        if ($element instanceof TextElement){
            array_unshift($this->_children, $element->text);
            return $this;
        } else {
            throw new NotSupportedException("add ".get_class($element)." to text element");
        }
    }

}