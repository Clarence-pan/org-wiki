<?php
/**
 * @var $this WikiController
 * @var $repo WikiRepository
 * @var $keyword string
 * @var $cmd string
 * @var $found array
 * @var $others array
 */

$this->pageTitle = $keyword . ' - Search In Wiki'


?>

<form class="form" method="get" action="<?= $this->createUrl('search') ?>">
    <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" />
    <button type="submit">Search</button>
</form>

<div class="tip">
    <?= count($found) ?> page(s) found.
</div>

<div class="search-results">
<?php foreach ($found as $pageName => $pageFound): ?>
    <div class="search-result-page">
        <div class="page-name"><?= CHtml::link($pageName, $this->createUrl('view', ['pageName' => $pageName])) ?></div>
        <ul>
            <?php foreach ($pageFound as $line => $content): ?>
                <li><code><?= $line ?></code> <span><?= htmlspecialchars($content) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
</div>

<?php if (!empty($others)): ?>
    <div class="search-result-comment">
        <?= implode('<br/>', array_map('htmlspecialchars', $others)) ?>
    </div>
<?php endif; ?>

<div class="cmd">
    <code><?= $cmd ?></code>
</div>
