<?php


class WikiPageNotFoundException extends NotFoundException{
    public function __construct($pageName, $repoPath, $prevException=null){
        parent::__construct(sprintf("Wiki page '%s' not found in '%s'.", $repoPath, $pageName), 1, $prevException);
    }
} 