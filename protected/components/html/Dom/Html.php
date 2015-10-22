<?php

namespace html\dom;



class Html extends Element{

    /**
     * @param string $tagName
     * @param array $properties
     * @return Element
     */
    public static function createElement($tagName, $properties=[]){
        switch ($tagName){
            case 'br':
                return new BrElement();
            case 'a':
                return new LinkElement($properties);
            case 'div':
                return new DivElement($properties);
            case 'prev':
                return new CodeBlockElement($properties);
            case 'text':
                return new TextElement($properties['text']);
            default:
                return new Element($tagName, $properties);
        }
    }

}