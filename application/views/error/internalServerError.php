<h1>500 - <?= $view->translate('Internal server errror') ?></h1>
<h2><?= $message ?></h2>
<p><?= $view->app()->get('translator')->formatDateTime(new \DateTime()) ?></p>