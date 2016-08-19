<nav class="sidebar scrollbox" role="navigation">
    <div class="sidebar-header">
        <a href="<?= $view->generateUrl('dashboard_index') ?>" class="sidebar-brand">
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
                <a href="<?= $view->generateUrl('dashboard_index') ?>"><i class="fa fa-fw icon fa-dashboard"></i> <?= $view->translate('Dashboard') ?></a>
            </li>

            <li <?= $view->isCurrentRoute(array('navigation*', 'page*', 'section*', 'mod*'), 'class="active"') ?>>
                <a href="#"><i class="fa fa-fw icon fa-files-o"></i> <?= $view->translate('Content') ?><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li <?= $view->isCurrentRoute(array('page*', 'section*', 'mod*'), 'class="active"') ?>>
                        <a href="<?= $view->generateUrl('page_index') ?>"><?= $view->translate('Pages') ?></a>
                    </li>
                    <li <?= $view->isCurrentRoute('navigation*', 'class="active"') ?>>
                        <a href="<?= $view->generateUrl('navigation_index') ?>"><?= $view->translate('Navigations') ?></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-fw icon fa-bar-chart-o"></i> <?= $view->translate('Extensions') ?><span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="#"><?= $view->translate('Modules') ?></a>
                    </li>
                    <li>
                        <a href="#"><?= $view->translate('Themes') ?></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-fw icon fa-picture-o"></i> Media</a>
            </li>
            <li <?= $view->isCurrentRoute(array('setting*'), 'class="active"') ?>>
                <a href="<?= $view->generateUrl('setting_index') ?>"><i class="fa fa-fw icon fa-edit"></i> <?= $view->translate('Settings') ?></a>
            </li>
            <li <?= $view->isCurrentRoute(array('user*', 'role*'), 'class="active"') ?>>
                <a href="#"><i class="fa fa-fw icon fa-users"></i> <?= $view->translate('Accounts') ?><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li <?= $view->isCurrentRoute('user*', 'class="active"') ?>>
                        <a href="<?= $view->generateUrl('user_index') ?>"><?= $view->translate('Users') ?></a>
                    </li>
                    <li <?= $view->isCurrentRoute('role*', 'class="active"') ?>>
                        <a href="<?= $view->generateUrl('role_index') ?>"><?= $view->translate('Roles') ?></a>
                    </li>
                </ul>
            </li>
            <li <?= $view->isCurrentRoute('maintenance*', 'class="active"') ?>>
                <a href="<?= $view->generateUrl('maintenance_index') ?>"><i class="fa fa-fw icon fa-picture-o"></i> <?= $view->translate('Maintenance') ?></a>
            </li>
        </ul>
        <div class="sidebar-content hidden-xs">
            <ul class="list-unstyled">
                <li>Version 0.1-dev</li>
            </ul>
        </div>
    </div>
</nav>