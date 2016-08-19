<nav class="sidebar scrollbox" role="navigation">
    <div class="sidebar-header">
        <a href="<?= generate_url('dashboard_index') ?>" class="sidebar-brand">
            <img class="img-responsive" title="Neoflow CMS" alt="Neoflow CMS" src="<?= $view->getThemeUrl('img/logo_red.png') ?>" />
            <span class="hidden-xs">content management system</span>
        </a>
        <a href="javascript:void(0);" class="sidebar-toggle collapsed" data-toggle="collapse" data-target=".sidebar-collapse">
            <i class="fa fa-fw fa-bars fa-2x"></i>
        </a>
    </div>
    <div class="sidebar-collapse collapse">
        <div class="sidebar-content">
            <div class="media">
                <p><small><?= translate('Logged in as') ?></small></p>
                <span class="media-left">
                    <i class="fa fa-3x fa-user media-object"></i>
                </span>
                <div class="media-body">
                    <h4 class="media-heading text-primary"><?= $view->getAuthenticatedUser()->getFullName() ?></h4>
                    <?= $view->getAuthenticatedUser()->role()->fetch()->title ?>
                </div>
            </div>
        </div>
        <ul class="nav sidebar-nav">
            <li <?= $view->isCurrentRoute('dashboard*', 'class="active"') ?>>
                <a href="<?= generate_url('dashboard_index') ?>"><i class="fa fa-fw icon fa-dashboard"></i> <?= translate('Dashboard') ?></a>
            </li>

            <li <?= $view->isCurrentRoute(array('navigation*', 'page*', 'section*', 'mod*'), 'class="active"') ?>>
                <a href="#"><i class="fa fa-fw icon fa-files-o"></i> <?= translate('Content') ?><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li <?= $view->isCurrentRoute(array('page*', 'section*', 'mod*'), 'class="active"') ?>>
                        <a href="<?= generate_url('page_index') ?>"><?= translate('Pages') ?></a>
                    </li>
                    <li <?= $view->isCurrentRoute('navigation*', 'class="active"') ?>>
                        <a href="<?= generate_url('navigation_index') ?>"><?= translate('Navigations') ?></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-fw icon fa-bar-chart-o"></i> <?= translate('Extensions') ?><span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="#"><?= translate('Modules') ?></a>
                    </li>
                    <li>
                        <a href="#"><?= translate('Themes') ?></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-fw icon fa-picture-o"></i> Media</a>
            </li>
            <li <?= $view->isCurrentRoute(array('setting*'), 'class="active"') ?>>
                <a href="<?= generate_url('setting_index') ?>"><i class="fa fa-fw icon fa-edit"></i> <?= translate('Settings') ?></a>
            </li>
            <li <?= $view->isCurrentRoute(array('user*', 'role*'), 'class="active"') ?>>
                <a href="#"><i class="fa fa-fw icon fa-users"></i> <?= translate('Accounts') ?><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li <?= $view->isCurrentRoute('user*', 'class="active"') ?>>
                        <a href="<?= generate_url('user_index') ?>"><?= translate('Users') ?></a>
                    </li>
                    <li <?= $view->isCurrentRoute('role*', 'class="active"') ?>>
                        <a href="<?= generate_url('role_index') ?>"><?= translate('Roles') ?></a>
                    </li>
                </ul>
            </li>
            <li <?= $view->isCurrentRoute('maintenance*', 'class="active"') ?>>
                <a href="<?= generate_url('maintenance_index') ?>"><i class="fa fa-fw icon fa-picture-o"></i> <?= translate('Maintenance') ?></a>
            </li>
        </ul>
        <div class="sidebar-content hidden-xs">
            <ul class="list-unstyled">
                <li>Version 0.1-dev</li>
            </ul>
        </div>
    </div>
</nav>