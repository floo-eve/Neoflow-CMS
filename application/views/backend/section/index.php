<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= translate('Page section of {0}', array('<i>' . $page->title . '</i>')) ?>
        </h3>
    </div>
    <div class="panel-body">
        <ul class="list-inline small text-muted page-info">
            <li><?= translate('Page ID') ?>: <?= $page->id() ?></li>
            <li><?= translate('Section ID') ?>: <?= $section->id() ?></li>
            <li><?= translate('Module') ?>: <?= $module->title ?></li>
        </ul>
    </div>
</div>

<?= $view->getBlock('module') ?>