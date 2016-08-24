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
            <li <?= $view->isCurrentRoute(array('page_sections', 'section*', 'mod*'), 'class="active"') ?>>
                <a href="<?= generate_url('page_sections', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-th-list"></i> <?= translate('Sections') ?>
                </a>
            </li>
            <li <?= $view->isCurrentRoute('page_edit', 'class="active"') ?>>
                <a href="<?= generate_url('page_edit', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-cog"></i><?= translate('Settings') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

