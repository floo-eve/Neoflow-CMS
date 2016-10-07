<?= $view->renderTemplate('backend/navigation/navbar', array('navigation' => $navigation)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit item') ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('navitem_update') ?>" class="form-horizontal">
                    <input value="<?= $navitem->id() ?>" type="hidden" name="navitem_id" />

                    <div class="form-group">
                        <label for="inputTitle" class="col-sm-2 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" value="<?= $navitem->title ?>" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectPage" class="col-sm-2 control-label">
                            <?= translate('Page') ?>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="page_id" id="selectPage">
                                <?= $view->renderNavitemOptions($pageNavitems, 0, array($navitem->page_id), array(), 'page_id') ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('parent_navitem_id', 'has-error') ?>">
                        <label for="selectParentNavitem" class="col-sm-2 control-label">
                            <?= translate('Parent item') ?>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="parent_navitem_id" id="selectParentNavitem">
                                <option value=""><?= translate('None') ?></option>
                                <?= $view->renderNavitemOptions($navitems, 0, array($navitem->parent_navitem_id), array($navitem->id())) ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <label class="radio-inline">
                                <input name="is_visible" value="1" type="radio" <?= ($navitem->is_visible ? 'checked' : '') ?> /> <?= translate('Visible') ?>
                            </label>
                            <label class="radio-inline">
                                <input name="is_visible" value="0" type="radio" <?= ($navitem->is_visible ? '' : 'checked') ?> /> <?= translate('Hidden') ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
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
