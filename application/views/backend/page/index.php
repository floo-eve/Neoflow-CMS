<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('All pages') ?>
                </h3>
            </div>

            <?php if (count($languages) > 1) { ?>
                <ul class="nav nav-tabs">
                    <?php foreach ($languages as $language) { ?>
                        <li <?= ($language->id() === $activeLanguage->id() ? 'class="active"' : '') ?>>
                            <a href="<?= generate_url('page_index', array('language_id' => $language->id())) ?>">
                                <?= $language->renderFlagIcon() ?> <?= $language->translated('title') ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <div class="panel-body">

                <?php if ($navitems->count()) { ?>
                    <div class="nestable" data-save-url="<?= generate_url('navitem_reorder') ?>" id="nestable">
                        <?= $view->renderNavitemNestable($navitems) ?>
                    </div>
                    <ul class="list-inline">
                        <li><i class="fa fa-eye"></i> = <?= translate('Enabled') ?></li>
                        <li><i class="fa fa-lock"></i> = <?= translate('Restricted') ?></li>
                        <li><i class="fa fa-eye-slash"></i> = <?= translate('Hidden') ?></li>
                        <li><i class="fa fa-ban"></i> = <?= translate('Disabled') ?></li>
                    </ul>
                <?php } else { ?>
                    <p class="text-center text-muted"><?= translate('No results found') ?></p>
                <?php } ?>
            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Create page') ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('page_create') ?>" class="form-horizontal">
                    <input type="hidden" value="<?= $activeLanguage->id() ?>" name="language_id" />
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('module_id', 'has-error') ?>">
                        <label for="selectModule" class="col-sm-3 control-label">
                            <?= translate('Module') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="module_id" id="selectModule">
                                <option value=""><?= translate('None') ?></option>
                                <?php
                                foreach ($modules as $module) {

                                    ?>
                                    <option value="<?= $module->id() ?>"><?= $module->name ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectPage" class="col-sm-3 control-label">
                            <?= translate('Top page') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="parent_navitem_id" id="selectPage">
                                <option value=""><?= translate('None') ?></option>
                                <?= $view->renderNavitemOptions($navitems) ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <label class="radio-inline">
                                <input name="is_active" value="1" type="radio" checked/> <?= translate('Enabled') ?>
                            </label>
                            <label class="radio-inline">
                                <input name="is_active" value="0" type="radio" /> <?= translate('Disabled') ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
if ($view->hasBlock(3)) {

    echo $view->getBlock(3);
}

?>