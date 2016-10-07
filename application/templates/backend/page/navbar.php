<div class="panel panel-default">
    <ul class="nav nav-pills nav-xs-stacked">
        <li <?= $view->isCurrentRoute(array('section_index', 'section*', 'mod*'), 'class="active"') ?>>
            <a href="<?= generate_url('section_index', array('id' => $page->id())) ?>">
                <i class="fa fa-fw fa-th-list"></i> <?= translate('Sections') ?>
            </a>
        </li>
        <li <?= $view->isCurrentRoute('page_edit', 'class="active"') ?>>
            <a href="<?= generate_url('page_edit', array('id' => $page->id())) ?>">
                <i class="fa fa-fw fa-cog"></i> <?= translate('Settings') ?>
            </a>
        </li>
    </ul>

</div>

<?= $view->renderTemplate('backend/page/infos', array('page' => $page)) ?>