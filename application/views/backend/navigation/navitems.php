<?= $view->renderTemplate('backend/navigation/navbar', array('navigation' => $navigation)) ?>

<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Navigation items') ?><span class="label label-primary pull-right"><?= $navigation->title ?></span>
                </h3>
            </div>

            <?php if (count($languages) > 1) { ?>
                <ul class="nav nav-tabs">
                    <?php foreach ($languages as $language) { ?>
                        <li <?= ($language->id() === $navigationLanguage->id() ? 'class="active"' : '') ?>>
                            <a href="<?= generate_url('navigation_navitems', array('id' => $navigation->id(), 'language_id' => $language->id())) ?>">
                                <?= $language->renderFlagIcon() ?> <?= $language->translated('title') ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <div class="panel-body">

                <?php if ($navitems->count()) { ?>
                    <div class="nestable" id="nestable">
                        <?= $view->renderNavitemNestable($navitems) ?>
                    </div>
                <?php } else { ?>
                    <p class="alert alert-warning"><?= translate('No results found') ?></p>
                <?php } ?>

            </div>
        </div>
    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= translate('Create item') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('navigation_create_navitem') ?>" class="form-horizontal">
                    <input type="hidden" value="<?= $navigationLanguage->id() ?>" name="language_id" />
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectPage" class="col-sm-3 control-label">
                            <?= translate('Page') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="page_id" id="selectPage">
                                <?= $view->renderNavitemOptions($pageNavitems) ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectParentNavitem" class="col-sm-3 control-label">
                            <?= translate('Top item') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="parent_navitem_id" id="selectParentNavitem">
                                <option value=""><?= translate('None') ?></option>
                                <?= $view->renderNavitemOptions($navitems) ?>
                            </select>
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