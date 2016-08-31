<nav class="navbar navbar-default">
    <div class="navbar-header">
        <span class="navbar-text visible-xs-inline-block">
            <?= translate('Settings') ?>
        </span>
        <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-fw fa-bars"></i>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav">
            <li <?= $view->isCurrentRoute(array('navitem_index', 'navitem_*'), 'class="active"') ?>>
                <a href="<?= generate_url('navitem_index', array('id' => $navigation->id())) ?>">
                    <i class="fa fa-fw fa-th-list"></i> <?= translate('Items') ?>
                </a>
            </li>
            <li <?= $view->isCurrentRoute('navigation_edit', 'class="active"') ?>>
                <a href="<?= generate_url('navigation_edit', array('id' => $navigation->id())) ?>">
                    <i class="fa fa-fw fa-cog"></i> <?= translate('Details') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

