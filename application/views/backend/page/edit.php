<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit page') ?>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('page_update') ?>" class="form-horizontal">
                    <input value="<?= $page->id() ?>" type="hidden" name="page_id" />

                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-2 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputTitle" value="<?= $page->title ?>" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-2 control-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control vresize" maxlength="255" id="textareaDescription" rows="3"><?= $page->description ?></textarea>
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('keywords', 'has-error') ?>">
                        <label for="inputKeywords" class="col-sm-2 control-label">
                            <?= translate('Keywords') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputKeywords" value="<?= $page->keywords ?>" type="text" class="form-control" name="keywords" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="selectPage" class="col-sm-2 control-label">
                            <?= translate('Top page') ?>
                        </label>
                        <div class="col-sm-10">
                            <select data-placeholder="<?= translate('None') ?>" class="form-control select2" name="parent_navitem_id" id="selectPage">
                                <option value="0"><?= translate('None') ?></option>
                                <?= $view->renderNavitemOptions($navitems, 0, array($navitem->parent_navitem_id), array($navitem->id())) ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <label class="radio-inline">
                                <input name="is_active" value="1" type="radio" <?= ($page->is_active ? 'checked' : '') ?> /> <?= translate('Enabled') ?>
                            </label>
                            <label class="radio-inline">
                                <input name="is_active" value="0" type="radio" <?= ($page->is_active ? '' : 'checked') ?> /> <?= translate('Disabled') ?>
                            </label>
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
