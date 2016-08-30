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
        <div class="sidebar-content logged-in-user">
            <div class="media">
                <!--<p><small><?= translate('Logged in as') ?>:</small></p>-->
                <span class="media-left">
                    <i class="fa fa-3x fa-user media-object"></i>
                </span>
                <div class="media-body">
                    <h4 class="media-heading text-primary"><?= $view->getAuthenticatedUser()->getFullName() ?></h4>
                    <?= $view->getAuthenticatedUser()->role()->fetch()->title ?>
                </div>
            </div>
            <!--<hr />-->
            <?php if ($view->app()->service('auth')->isAuthenticated()) { ?>
                                                                                                                                    <!--                <a href="<?= generate_url('backend_logout') ?>" title="Logout" class="btn btn-xs btn-primary btn-icon-left btn-icon">
                                                                                                                                                        <i class="fa fa-sign-out"></i>
                                                                                                                                                        <span class="hidden-xs"> Logout</span></a>-->
            <?php } ?>
        </div>
        <ul class="nav sidebar-nav">
            <li <?= $view->isCurrentRoute('dashboard*', 'class="active"') ?>>
                <a href="<?= generate_url('dashboard_index') ?>"><i class="fa fa-fw icon fa-dashboard"></i> <?= translate('Dashboard') ?></a>
            </li>

            <?php if (has_permission('manage_pages') || has_permission('manage_navigations')) { ?>
                <li <?= $view->isCurrentRoute(array('navigation*', 'page*', 'section*', 'mod*'), 'class="active"') ?>>
                    <a href="#"><i class="fa fa-fw icon fa-files-o"></i> <?= translate('Content') ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if (has_permission('manage_pages')) { ?>
                            <li <?= $view->isCurrentRoute(array('page*', 'section*', 'mod*'), 'class="active"') ?>>
                                <a href="<?= generate_url('page_index') ?>"><?= translate('Pages') ?></a>
                            </li>
                            <?php
                        }
                        if (has_permission('manage_navigations')) {

                            ?>
                            <li <?= $view->isCurrentRoute('navigation*', 'class="active"') ?>>
                                <a href="<?= generate_url('navigation_index') ?>"><?= translate('Navigations') ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            if (has_permission('manage_modules') || has_permission('manage_templates')) {

                ?>
                <li>
                    <a href="#">
                        <i class="fa fa-fw icon fa-bar-chart-o"></i> <?= translate('Extensions') ?><span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <?php if (has_permission('manage_modules')) { ?>
                            <li>
                                <a href="#"><?= translate('Modules') ?></a>
                            </li>
                            <?php
                        }
                        if (has_permission('manage_templates')) {

                            ?>
                            <li>
                                <a href="#"><?= translate('Themes') ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            if (has_permission('manage_media')) {

                ?>
                <li>
                    <a href="#"><i class="fa fa-fw icon fa-picture-o"></i> Media</a>
                </li>
                <?php
            }
            if (has_permission('settings')) {

                ?>
                <li <?= $view->isCurrentRoute(array('setting*'), 'class="active"') ?>>
                    <a href="<?= generate_url('setting_index') ?>"><i class="fa fa-fw icon fa-edit"></i> <?= translate('Settings') ?></a>
                </li>
                <?php
            }
            if (has_permission('manage_users') || has_permission('manage_roles')) {

                ?>

                <li <?= $view->isCurrentRoute(array('user*', 'role*'), 'class="active"') ?>>
                    <a href="#"><i class="fa fa-fw icon fa-users"></i> <?= translate('Accounts') ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if (has_permission('manage_users')) { ?>
                            <li <?= $view->isCurrentRoute('user*', 'class="active"') ?>>
                                <a href="<?= generate_url('user_index') ?>"><?= translate('Users') ?></a>
                            </li>
                            <?php
                        }
                        if (has_permission('manage_roles')) {

                            ?>
                            <li <?= $view->isCurrentRoute('role*', 'class="active"') ?>>
                                <a href="<?= generate_url('role_index') ?>"><?= translate('Roles') ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            if (has_permission(array('maintenance'))) {

                ?>
                <li <?= $view->isCurrentRoute('maintenance*', 'class="active"') ?>>
                    <a href="<?= generate_url('maintenance_index') ?>"><i class="fa fa-fw icon fa-picture-o"></i> <?= translate('Maintenance') ?></a>
                </li>
            <?php } ?>
        </ul>
        <div class="sidebar-content hidden-xs">
            <ul class="list-unstyled">
                <li>Version 0.1-dev</li>
            </ul>
        </div>
    </div>
</nav>