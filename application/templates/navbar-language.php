<?php if (count($languages) > 1) { ?>
    <h5 class="text-muted">
        <?= $this->translate('Choose page language') ?>
    </h5>
    <nav class="navbar navbar-default">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <?php foreach ($languages as $language) { ?>
                    <li <?= ($language->id() === $activeLanguage->id() ? 'class="active"' : '') ?>>
                        <a href="<?= $this->generateUrl('page_index', array('language_id' => $language->id())) ?>">
                            <?= $language->renderFlagIcon() ?> <?= $language->getTranslatedTitle() ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </nav>
<?php } ?>

