<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('All {0}', array('Pages')) ?>
                </h3>
            </div>

            <?php if (count($languages) > 1) { ?>
                <ul class="nav nav-tabs">
                    <?php foreach ($languages as $language) { ?>
                        <li <?= ($language->id() === $pageLanguage->id() ? 'class="active"' : '') ?>>
                            <a href="<?= $view->generateUrl('page_index', array('language_id' => $language->id())) ?>">
                                <?= $language->renderFlagIcon() ?> <?= $language->translated('title') ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <div class="panel-body">

                <?php if ($navitems->count()) { ?>
                    <div class="nestable" data-save-url="<?= $view->generateUrl('navitem_reorder') ?>" id="nestable">
                        <?= $view->renderNavitemNestable($navitems) ?>
                    </div>
                    <ul class="list-inline">
                        <li><i class="fa fa-eye"></i> = <?= $view->translate('Active') ?></li>
                        <li><i class="fa fa-lock"></i> = <?= $view->translate('Restricted') ?></li>
                        <li><i class="fa fa-eye-slash"></i> = <?= $view->translate('Hidden') ?></li>
                        <li><i class="fa fa-ban"></i> = <?= $view->translate('Disabled') ?></li>
                    </ul>
                <?php } else { ?>
                    <p class="alert alert-warning"><?= $view->translate('No pages found') ?></p>
                <?php } ?>
            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('Create {0}', array('Page')) ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $view->generateUrl('page_create') ?>" class="form-horizontal">
                    <input type="hidden" value="<?= $pageLanguage->id() ?>" name="language_id" />
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= $view->translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('module_id', 'has-error') ?>">
                        <label for="selectModule" class="col-sm-3 control-label">
                            <?= $view->translate('Module') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="module_id" id="selectModule">
                                <option value=""><?= $view->translate('None') ?></option>
                                <?php
                                foreach ($modules as $module) {

                                    ?>
                                    <option value="<?= $module->id() ?>"><?= $module->title ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('visibility', 'has-error') ?>">
                        <label for="selectVisibility" class="col-sm-3 control-label">
                            <?= $view->translate('Visibility') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="visibility" id="selectVisibility">
                                <option value="visible"><?= $view->translate('Visible') ?></option>
                                <option value="restricted"><?= $view->translate('Restricted') ?></option>
                                <option value="hidden"><?= $view->translate('Hidden') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectPage" class="col-sm-3 control-label">
                            <?= $view->translate('Top page') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="parent_navitem_id" id="selectPage">
                                <option value=""><?= $view->translate('None') ?></option>
                                <?= $view->renderNavitemOptions($navitems) ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input name="is_active" value="0" type="hidden" />
                                    <input name="is_active" value="1" type="checkbox" checked /> <?= $view->translate('Page is active') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= $view->translate('Save') ?>
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