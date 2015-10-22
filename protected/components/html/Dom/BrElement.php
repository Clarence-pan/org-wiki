<?php


namespace html\dom;


class BrElement extends Element{
    public function __construct(){
        parent::__construct('br', []);
    }

    public function toHtml(){
        return '<br/>';
    }
} 