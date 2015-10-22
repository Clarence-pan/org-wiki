<?php


namespace html\dom;


class DivElement extends Element{

    public function __construct($properties=[]){
        parent::__construct('div', $properties);
    }
} 