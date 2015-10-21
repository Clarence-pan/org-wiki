<?php


class WikiPageTypeNotSupportedException extends NotFoundException {
    public function __construct($wikiPageType, $wikiPageName){
        parent::__construct(sprintf("Invalid page type: %s (page name: %s)", $wikiPageType, $wikiPageName));
    }
} 