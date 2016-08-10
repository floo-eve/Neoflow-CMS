<nav class="navbar navbar-top navbar-default" role="navigation">    <div class="container-fluid">        <p class="nav navbar-text brand">            <?= $view->translate('Backend') ?>        </p>        <ul class="nav navbar-nav navbar-right">            <?php if (count($view->get('languages')) > 1) {
    ?>                <li class="dropdown">                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">                        <?= $view->get('currentLanguage')->renderFlagIcon() ?>                    </a>                    <ul class="dropdown-menu">                        <?php foreach ($view->get('languages') as $language) {
    ?>                            <li <?= ($language->code === $view->app()->get('translator')->getCurrentLanguageCode() ? 'class="active"' : '') ?>>                                <a href="<?= $view->generateUrl('', array(), $language->code) ?>">                                    <?= $language->renderFlagIcon() ?>                                </a>                            </li>                        <?php 
} ?>                    </ul>                </li>                <?php
}            if ($view->app()->get('session')->get('user_id')) {
    ?>                <li>                    <a href="#" title="Profil bearbeiten"><i class="fa fa-fw fa-user fa-fw"></i> <span class="visible-lg-inline">Profil bearbeiten</span></a>                </li>                <?php
}

            ?>            <li>                <a href="<?= $view->generateUrl('frontend_index') ?>" title="Zum Frontend"><i class="fa fa-fw fa-desktop fa-fw"></i> <span class="visible-lg-inline">Zum Frontend</span></a>            </li>            <?php if ($view->app()->get('session')->get('user_id')) {
    ?>                <li>                    <a href="<?= $view->generateUrl('backend_logout') ?>" title="Logout"><i class="fa fa-fw fa-sign-out fa-fw"></i><span class="hidden-xs"> Logout</span></a>                </li>                <?php
}

            ?>        </ul>    </div></nav>