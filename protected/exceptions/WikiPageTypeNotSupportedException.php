<?php


class WikiPageTypeNotSupportedException extends Exception {
    public function __construct($wikiPageType, $wikiPageName){
        parent::__construct(sprintf("Invalid page type: %s (page name: %s)", $wikiPageType, $wikiPageName));
    }
} 