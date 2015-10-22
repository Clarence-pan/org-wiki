<?php


namespace html\dom;


class RawHtmlElement extends Element{
    public function __construct($html){
        parent::__construct('', ['innerHtml' => $html]);
    }

    public function setInnerHtml($innerHtml){
        $this->_innerHtml = $innerHtml;
    }

    public function getInnerHtml($innerHtml){
        return $this->_innerHtml;
    }

    private $_innerHtml;
} 