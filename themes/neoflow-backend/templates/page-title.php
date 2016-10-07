<div class="page-title">
    <h1>
        <?= $view->getTitle() ?> <small><?= $view->getSubtitle() ?></small>


    </h1>

    <?php if ($view->get('back_url')) { ?>
        <a href="<?= $view->get('back_url') ?>" class="btn btn-default btn-xs btn-back btn-icon btn-icon-left">
            <i class="fa fa-chevron-left"></i><?= translate('Back') ?>
        </a>
    <?php } ?>
</div>