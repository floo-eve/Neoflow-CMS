<?= $view->renderTemplate('backend/navigation/navbar', array('navigation' => $navigation)) ?>

<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Navigation items') ?>
                </h3>
            </div>

            <?php if (count($languages) > 1) { ?>
                <ul class="nav nav-tabs">
                    <?php foreach ($languages as $language) { ?>
                        <li <?= ($language->id() === $navigationLanguage->id() ? 'class="active"' : '') ?>>
                            <a href="<?= generate_url('navitem_index', array('id' => $navigation->id(), 'language_id' => $language->id())) ?>">
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
                        <li><i class="fa fa-eye"></i> = <?= translate('Visible') ?></li>
                        <li><i class="fa fa-ban"></i> = <?= translate('Hidden') ?></li>
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
                <h3 class="panel-title"><?= translate('Create item') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('navitem_create') ?>" class="form-horizontal">
                    <input type="hidden" value="<?= $navigationLanguage->id() ?>" name="language_id" />
                    <input type="hidden" value="<?= $navigation->id() ?>" name="navigation_id" />
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectPage" class="col-sm-3 control-label">
                            <?= translate('Page') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="page_id" id="selectPage">
                                <?= $view->renderNavitemOptions($pageNavitems, 0, array(), array(), 'page_id') ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectParentNavitem" class="col-sm-3 control-label">
                            <?= translate('Parent item') ?>
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
                            <label class="radio-inline">
                                <input name="is_visible" value="1" type="radio" checked/> <?= translate('Visible') ?>
                            </label>
                            <label class="radio-inline">
                                <input name="is_visible" value="0" type="radio" /> <?= translate('Hidden') ?>
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