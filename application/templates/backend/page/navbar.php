
<nav class="navbar navbar-default">
    <div class="navbar-header">
        <span class="navbar-text visible-xs-inline-block">
            <?= $this->translate('Settings') ?>
        </span>
        <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-fw fa-bars"></i>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav">
            <li <?= $this->isCurrentRoute('page_sections', 'class="active"') ?>>
                <a href="<?= $this->generateUrl('page_sections', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-list-alt"></i> <?= $this->translate('Sections') ?>
                </a>
            </li>
            <li <?= $this->isCurrentRoute('page_settings', 'class="active"') ?>>
                <a href="<?= $this->generateUrl('page_settings', array('id' => $page->id())) ?>">
                    <i class="fa fa-fw fa-cog"></i><?= $this->translate('Settings') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

