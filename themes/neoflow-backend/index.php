<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Backend - Neoflow CMS</title>

        <!-- Meta data -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,400italic,700|Roboto:400,400italic,500,700,900" rel="stylesheet" type="text/css">

        <!-- Additional stylesheets -->
        <?= $view->renderStylesheets() ?>

        <!-- Theme stylesheets -->
        <link href="<?= $view->getThemeUrl('/css/style.css') ?>" rel="stylesheet" />

    </head>
    <body>
        <div id="body-scroll">
            <div id="wrapper">

                <?php if ($view->hasBlock(2)) {

                    ?>

                    <div id="page-wrapper-fluid">
                        <?= $view->renderTemplate('navigation/navbar-top') ?>

                        <div class="page-wrapper">
                            <div class="container">
                                <?= $view->getBlock(2) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                } else {

                    ?>
                    <div id="page-wrapper-sidebar">

                        <?= $view->renderTemplate('navigation/navbar-top') ?>
                        <?= $view->renderTemplate('navigation/sidebar') ?>

                        <div class="page-wrapper">
                            <div class="container-fluid">

                                <?= $view->renderTemplate('page-title') ?>

                                <?= $view->renderAlert() ?>
                                <?= $view->getBlock(1) ?>
                            </div>
                        </div>
                    </div>
                <?php }

                ?>

            </div><!-- /#wrapper -->
        </div><!-- /#body-scroll -->

        <!-- Theme vars -->
        <script>
            var NEOFLOW_LOCALE = '<?= $view->get('currentLanguage')->code ?>',
                    NEOFLOW_URL = '<?= $view->getConfig()->getUrl() ?>',
                    NEOFLOW_THEME_URL = '<?= $view->getThemeUrl() ?>';
        </script>

        <!-- Theme vendor script -->
        <script src="<?= $view->getThemeUrl('/vendor/jquery/jquery-2.2.2.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/metisMenu/js/metisMenu.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/nestable/jquery.nestable.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/bootbox/bootbox.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/js-cookie/js.cookie-2.1.2.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/dataTables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/dataTables/js/dataTables.bootstrap.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/dataTables/js/dataTables.responsive.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/dataTables/js/responsive.bootstrap.min.js') ?>"></script>
        <script src="<?= $view->getThemeUrl('/vendor/select2/js/select2.full.js') ?>"></script>

        <script src="<?= $view->getThemeUrl('/vendor/select2/js/i18n/' . $view->app()->get('translator')->getCurrentLanguageCode() . '.js') ?>"></script>

        <!-- Additional script -->
        <?= $view->renderScripts() ?>

        <!-- Theme script -->
        <script src="<?= $view->getThemeUrl('/js/script.js') ?>"></script>

    </body>
</html>
