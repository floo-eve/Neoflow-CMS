<?= $this->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= $this->translate('Page section of {0}', array('<i>' . $page->title . '</i>')) ?>
        </h3>
    </div>
    <div class="panel-body">
        <ul class="list-inline small text-muted page-info">
            <li><?= $this->translate('Page ID') ?>: <?= $page->id() ?></li>
            <li><?= $this->translate('Section ID') ?>: <?= $section->id() ?></li>
            <li><?= $this->translate('Module') ?>: <?= $module->title ?></li>
        </ul>
    </div>
</div>

<?= $this->getBlock('module') ?>