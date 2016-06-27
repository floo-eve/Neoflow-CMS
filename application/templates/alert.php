<p class="alert alert-<?= $alert->getType() ?>">
    <?= implode('<br />', $alert->getMessage()) ?>
</p>