<nav class="navbar navbar-default">
    <div class="navbar-header">
        <span class="navbar-text visible-xs-inline-block">
            <?= $view->translate('Settings') ?>
        </span>
        <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-fw fa-bars"></i>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav">
            <li <?= $view->isCurrentRoute(array('page_sections', 'section*', 'mod*'), 'class="active"') ?>>
                <a href="<?= $view->generateUrl('page_sections', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-th-list"></i> <?= $view->translate('Sections') ?>
                </a>
            </li>
            <li <?= $view->isCurrentRoute('page_settings', 'class="active"') ?>>
                <a href="<?= $view->generateUrl('page_settings', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-cog"></i><?= $view->translate('Settings') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

