<h1>500 - <?= $this->translate('Internal server errror') ?></h1>
<h2><?= $message ?></h2>
<p><?= $this->app()->get('translator')->formatDateTime(new \DateTime()) ?></p>