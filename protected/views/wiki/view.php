<?php
/**
 * @var $this WikiController
 * @var $page WikiPage
 */


/**
 * @var CClientScript $clientScript
 */
$clientScript = Yii::app()->clientScript;

$clientScript->registerCssFile('/css/json-viewer.css');
$clientScript->registerCssFile('/css/org-wiki.css');
$clientScript->registerScriptFile('/js/jquery.js');
$clientScript->registerScriptFile('/js/json-viewer.js');

?>

<div id="wiki-content">
    <?php  echo $page->htmlContent; ?>
</div>

<div id="padding" style="display:block; height: 100vh">  </div>

<script>
    $(function(){
        $('#wiki-content').find('h1,h2,h3,h4,h5,h6').initExpander(true);
    });
</script>
