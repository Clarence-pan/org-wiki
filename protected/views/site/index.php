<?php
/**
 * @var $this SiteController
 * @var $user User
 */

$this->pageTitle = "Index of Wiki";

if ($user){
    echo $user->repository->indexPage->htmlContent;
} else {
    echo CHtml::link('Please login firstly.', '/site/login');
    echo <<<HTML
<script>
    window.location.href = '/login';
</script>
HTML;

}

