<?php
/**
 * @var $this WikiController
 * @var $page WikiPage
 */

$this->pageTitle = $page->title . ' - Wiki';

/**
 * @var CClientScript $clientScript
 */
$clientScript = Yii::app()->clientScript;

$clientScript->registerCssFile('/css/jquery.expander.css');
$clientScript->registerCssFile('/css/org-wiki.css');
$clientScript->registerScriptFile('/js/jquery.js');
$clientScript->registerScriptFile('/js/jquery.expander.js');
$clientScript->registerScriptFile('/js/jquery.button.js');
?>

<div id="wiki-content">
    <?php  echo $page->htmlContent; ?>
</div>

<script>
    $(function(){
        $('#wiki-content').find('h1,h2,h3,h4,h5,h6').initExpander(true);
    });
</script>

<div id="tools">
   <button id="convertGbkToUtf8" data-loading="Converting GBK to UTF8...">Convert GBK to UTF8</button>
</div>
<script>
    $(function(){
        $('#convertGbkToUtf8').on('click', function(){
            var $btn = $(this);

            if ($btn.button(':isLoading')){
                return false;
            }

            $btn.button('loading');
            $.post(<?= json_encode($this->createUrl('iconv')) ?>, <?= json_encode(['pageName' => $page->name, 'from' => 'GBK', 'to' => 'UTF8']) ?>)
                .done(function(){
                    location.reload();
                })
                .fail(function(){
                    alert('Convertion failed: ' + JSON.stringify(arguments));
                })
                .always(function(){
                    $btn.button('restore');
                });
            return false;
        });
    });
</script>