<nav class="navbar navbar-top navbar-default" role="navigation">
    <div class="container-fluid">
        <p class="nav navbar-text brand">
            <?= translate('Backend') ?>
        </p>

        <ul class="nav navbar-nav navbar-right">
            <?php if (count($view->getLanguages()) > 1) {

                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?= $view->getActiveLanguage()->renderFlagIcon() ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach ($view->getLanguages() as $language) {

                            ?>
                            <li <?= ($language->code === $view->translator()->getActiveLanguageCode() ? 'class="active"' : '') ?>>
                                <a href="<?= generate_url('', array(), $language->code) ?>">
                                    <?= $language->renderFlagIcon() ?>
                                </a>
                            </li>
                        <?php }

                        ?>
                    </ul>
                </li>
                <?php
            }
            if ($view->app()->service('auth')->isAuthenticated()) {

                ?>
                <li>
                    <a href="#" title="Profil bearbeiten"><i class="fa fa-fw fa-user fa-fw"></i> <span class="visible-lg-inline">Profil bearbeiten</span></a>
                </li>
                <?php
            }

            ?>
            <li>
                <a href="<?= generate_url('frontend_index') ?>" title="Zum Frontend"><i class="fa fa-fw fa-desktop fa-fw"></i> <span class="visible-lg-inline">Zum Frontend</span></a>
            </li>
            <?php if ($view->app()->service('auth')->isAuthenticated()) {

                ?>
                <li>
                    <a href="<?= generate_url('backend_logout') ?>" title="Logout"><i class="fa fa-fw fa-sign-out fa-fw"></i><span class="hidden-xs"> Logout</span></a>
                </li>
                <?php
            }

            ?>
        </ul>
    </div>
</nav>