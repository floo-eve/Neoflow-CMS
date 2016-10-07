<?php
$language = $page->language()->fetch();

?>

<div class="page-note">
    <ul class="list-inline">
        <li class="h4"><?= translate('Current page') ?>: <?= $page->title ?></li>
        <li class="small">ID: <?= $page->id() ?></li>
        <li class="small"><?= translate('Language') ?>: <?= $language->translated('title') ?> (<?= $language->code ?>)
    </ul>
    <p class="small"><?= translate('Last change by {0} on {1}', array('John Doe', '20.07.2016, 12:45')) ?></p>
</div>