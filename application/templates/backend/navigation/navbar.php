<div class="panel panel-default">
    <ul class="nav nav-pills nav-xs-stacked">
        <li <?= $view->isCurrentRoute(array('navitem_index', 'navitem_*'), 'class="active"') ?>>
            <a href="<?= generate_url('navitem_index', array('id' => $navigation->id())) ?>">
                <i class="fa fa-fw fa-th-list"></i> <?= translate('Items') ?>
            </a>
        </li>
        <li <?= $view->isCurrentRoute('navigation_edit', 'class="active"') ?>>
            <a href="<?= generate_url('navigation_edit', array('id' => $navigation->id())) ?>">
                <i class="fa fa-fw fa-cog"></i> <?= translate('Settings') ?>
            </a>
        </li>
    </ul>
</div>

<?= $view->renderTemplate('backend/navigation/infos', array('navigation' => $navigation)) ?>