<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Backend - Lahaina CMS</title>

        <!-- Meta data -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Favicons -->
        <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->getThemeUrl('/img/favicon-96x96.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= $this->getThemeUrl('/img/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= $this->getThemeUrl('/img/favicon-16x16.png') ?>">

        <!-- Boostrap CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/bootstrap/css/bootstrap.css') ?>" rel="stylesheet" />

        <!-- Perfect Scrollbar CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/perfect-scrollbar/css/perfect-scrollbar.css') ?>" rel="stylesheet" />

        <!-- MetisMenu CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/metisMenu/metisMenu.min.css') ?>" rel="stylesheet" />

        <!-- Select2 CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/select2/css/select2.min.css') ?>" rel="stylesheet" />
        <link href="<?= $this->getThemeUrl('/vendor/select2/css/select2-bootstrap.css') ?>" rel="stylesheet" />

        <!-- Nestable CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/nestable/css/nestable.css') ?>" rel="stylesheet" />

        <!-- Flag icon CSS -->
        <link href="<?= $this->getThemeUrl('/vendor/flag-icon-css/css/flag-icon.css') ?>" rel="stylesheet" />

        <!-- Theme CSS -->
        <link href="<?= $this->getThemeUrl('/css/style.css') ?>" rel="stylesheet" />

        <!-- Vendor Fonts -->
        <link href="<?= $this->getThemeUrl('/vendor/font-awesome-4.5/css/font-awesome.min.css') ?>" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,400italic,700|Roboto:400,400italic,500,700,900" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body>
        <div id="wrapper">

            <?php if ($this->hasBlock(2)) { ?>

                <div id="page-wrapper-fluid">
                    <?= $this->renderTemplate('navigation/navbar-top') ?>

                    <div class="page-wrapper">
                        <div class="container">
                            <?= $this->renderTemplate('page-brand') ?>
                            <?= $this->getBlock(2) ?>
                        </div>
                    </div>
                </div>

            <?php } else { ?>
                <div id="page-wrapper-sidebar">

                    <?= $this->renderTemplate('navigation/navbar-top') ?>
                    <?= $this->renderTemplate('navigation/sidebar') ?>


                    <div class="page-wrapper">
                        <div class="container-fluid">

                            <?= $this->renderTemplate('page-title') ?>

                            <?= $this->renderAlert() ?>
                            <?= $this->getBlock(1) ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
        <!-- /#wrapper -->


        <!-- jQuery -->
        <script src="<?= $this->getThemeUrl('/vendor/jquery/jquery-2.2.2.min.js') ?>"></script>

        <!-- Bootstrap JavaScript -->
        <script src="<?= $this->getThemeUrl('/vendor/bootstrap/js/bootstrap.js') ?>"></script>

        <!-- Metis Menu jQuery Plugin JavaScript -->
        <script src="<?= $this->getThemeUrl('/vendor/metisMenu/metisMenu.js') ?>"></script>

        <!-- Nestable jQuery Plugin JavaScript -->
        <script src="<?= $this->getThemeUrl('/vendor/nestable/jquery.nestable.js') ?>"></script>

        <!-- Perfect Scrollbar jQuery Plugin JavaScript -->
        <script src="<?= $this->getThemeUrl('/vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.js') ?>"></script>

        <!-- Select2 jQuery Plugin JavaScript -->
        <script src="<?= $this->getThemeUrl('/vendor/select2/js/select2.full.js') ?>"></script>
        <script src="<?= $this->getThemeUrl('/vendor/select2/js/i18n/' . $this->app()->get('translator')->getCurrentLanguageCode() . '.js') ?>"></script>

        <!-- Custom Theme JavaScript -->
        <script src="<?= $this->getThemeUrl('/js/script.js') ?>"></script>

    </body>
</html>
