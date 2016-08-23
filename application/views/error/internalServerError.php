<h1>500 - Internal server error</h1>
<?php if ($exception) { ?>
    <h2><?= get_class($exception) ?>: <?= $exception->getMessage() ?></h2>
    <hr />
    <p><small><?= nl2br($exception->getTraceAsString()) ?></small></p>
    <hr />
    <p><?= $this->translator()->formatDateTime(new DateTime()) ?></p>
<?php } else { ?>
    <h2><?= $message ?></h2>
<?php } ?>