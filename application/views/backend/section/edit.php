<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= translate('Edit page section') ?><span class="label label-primary pull-right"><?= $page->title ?></span>
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