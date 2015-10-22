<?php


class HtmlWikiPage extends WikiPage{

    public function getContentType(){
        return 'text/html';
    }

    public function toHtml(){
        return file_get_contents($this->path);
    }
} 