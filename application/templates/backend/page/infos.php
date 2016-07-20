
<ul class="list-inline">
    <li><strong><?= $this->translate('Current page') ?>: <?= $page->title ?></strong></li>
    <li class="small">ID: <?= $page->id() ?></li>
</ul>
<p><?= $this->translate('Last change by {0} on {1}', array('John Doe', '20.07.2016, 12:45')) ?></p>
