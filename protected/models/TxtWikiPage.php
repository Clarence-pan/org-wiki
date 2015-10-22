<?php


class TxtWikiPage extends WikiPage{

    public function toHtml(){
        return \html\dom\Html::createElement('pre', [
            'code' => file_get_contents($this->path),
            'lang' => 'plain'
        ])->toHtml();
    }
} 