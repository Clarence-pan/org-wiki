<?php


namespace html\parser\org;


use html\dom\DivElement;
use html\dom\TextElement;

class Parser {

    public function parse($text){
        $div = new DivElement(['class' => 'org-mode-page']);

        $div->add(new TextElement("This is a test!"));

        return $div;
    }
}