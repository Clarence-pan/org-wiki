<?php


namespace html\dom;


use html\err\NotSupportedException;

class RawHtmlElement extends Element{
    public function __construct($html){
        parent::__construct('', ['innerHtml' => $html]);
    }

    public function setInnerHtml($innerHtml){
        $this->_innerHtml = $innerHtml;
    }

    public function getInnerHtml(){
        return $this->_innerHtml;
    }

    public function add(Element $element){
        if ($element instanceof TextElement){
            $this->_innerHtml .= $element->innerHtml;
            return $this;
        } else {
            throw new NotSupportedException("add ".get_class($element)." to innerHtml element");
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
            $this->_innerHtml = $element->innerHtml . $this->_innerHtml;
            return $this;
        } else {
            throw new NotSupportedException("add ".get_class($element)." to innerHtml element");
        }
    }

    private $_innerHtml;
} 