<?php

class OrgWikiPage extends WikiPage {

    public function toHtml(){
        $parser = new html\parser\org\Parser();
        $dom = $parser->parse($this->textContent);
        return $dom->toHtml();
    }

    public function getTitle(){
        return Lazy::init($this->_title, function (){
            $textContent = $this->getTextContent();
            foreach (explode("\n", $textContent) as $line) {
                $line = trim($line);
                if ($line[0] != '#'){
                    return ltrim($line, '*');
                }
            }

            return $this->name;
        });
    }

    protected $_title;
} 