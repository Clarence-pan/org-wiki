<?php

namespace html\dom;

class LinkElement extends Element{
    public function __construct($properties=[]){
        parent::__construct('a', $properties);
    }
} 