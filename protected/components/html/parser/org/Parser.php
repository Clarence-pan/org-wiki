<?php


namespace html\parser\org;


use html\dom\DivElement;

class Parser {

    public function parse($text){
        $div = new DivElement(['class' => 'org-mode-page']);



        return $div;
    }
}