<?php


use html\dom\Html;

class ImageWikiPage extends WikiPage {

    public function toHtml(){
        return Html::createElement('img', [
            'src' => Yii::app()->controller->createUrl('/wiki/viewRaw', ['pageName' => $this->name]),
            'alt' => $this->title
        ])->toHtml();
    }
} 