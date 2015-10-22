<?php
/**
 * @var $this WikiController
 * @var $pages array
 */

Yii::app()->clientScript->registerScriptFile('/static/js/jquery.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile('/static/js/debounce.js', CClientScript::POS_HEAD);


echo "<ul>";
foreach ($pages as $page) {
    echo "<li>";
    echo CHtml::link($page->name, $this->createUrl('/wiki/view', array('pageName' => $page->name)));
    echo "</li>";
}
echo "</ul>";



?>
<div id="tools">
    <button id="openAllWiki">Open All Wiki Pages</button>
</div>

<style>
    .wiki-page-container{
        display: block;
        width: 100%;
        height: 80vh;
        min-height: 30em;
    }
</style>
<div id="allWiki"> </div>

<script>
    $(function(){
        var pageUrlList = [];
        var curPage = 0;
        $("#openAllWiki").on('click', function(){
            if (pageUrlList.length > 0){
                return;
            }

            $('#content li a').each(function(){
                pageUrlList.push($(this).attr('href'));
            });

            var expand = debounce(function(){
                if (curPage < pageUrlList.length){
                    openWikiPage(pageUrlList[curPage]);
                    curPage += 1;
                }
            });

            expand();

            $(window).on('scroll', function(){
                var $win = $(window);
                if ($win.scrollTop() + $win.height() + 10 >= $('html').height()){
                    expand();
                }
            });
        });




        function openWikiPage(pageUrl){
            if (!pageUrl){
                return;
            }

            $("<h1></h1>").append($("<a></a>").text(pageUrl).attr("href", pageUrl)).appendTo('#allWiki');
            $("<iframe class='wiki-page-container'></iframe>").attr('src', pageUrl).appendTo('#allWiki');
        }
    });

</script>