<?php
/**
 * @var $this WikiController
 * @var $page WikiPage
 */


/**
 * @var CClientScript $clientScript
 */
$clientScript = Yii::app()->clientScript;

$clientScript->registerCssFile('/css/jquery.expander.css');
$clientScript->registerCssFile('/css/org-wiki.css');
$clientScript->registerScriptFile('/js/jquery.js');
$clientScript->registerScriptFile('/js/jquery.expander.js');

?>

<div id="wiki-content">
    <?php  echo $page->htmlContent; ?>
</div>

<script>
    $(function(){
        $('#wiki-content').find('h1,h2,h3,h4,h5,h6').initExpander(true);
    });
</script>
