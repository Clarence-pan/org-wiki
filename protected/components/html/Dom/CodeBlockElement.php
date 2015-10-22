<?php


namespace html\dom;


class CodeBlockElement extends Element {
    public function __construct($properties=[]){
        parent::__construct('pre', $properties);
    }

    public function setLang($lang){
        $this->_lang = $lang;

        if ($lang){
            $this->attr('class', 'brush: '.$lang);
        } else {
            $this->attr('class', '');
        }
    }

    public function setCode($code){
        $this->_code = $code;

        $this->add(new TextElement($code));
    }

    protected $_code;
    protected $_lang;
} 