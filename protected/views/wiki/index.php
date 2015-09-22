<?php
/**
 * @var $this WikiController
 * @var $pages array
 */

echo "<ul>";
foreach ($pages as $page) {
    echo "<li>";
    echo CHtml::link($page->name, $this->createUrl('/wiki/view', array('pageName' => $page->name)));
    echo "</li>";
}
echo "</ul>";


 