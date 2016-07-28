<div class="page-title">
    <h1>
        <?= $this->getTitle() ?> <small><?= $this->getSubtitle() ?></small>


    </h1>

    <?php if ($this->get('back_url')) { ?>
        <a href="<?= $this->get('back_url') ?>" class="btn btn-default btn-xs btn-back btn-icon btn-icon-left">
            <i class="fa fa-chevron-left"></i><?= $this->translate('Back') ?>
        </a>
    <?php } ?>
</div>