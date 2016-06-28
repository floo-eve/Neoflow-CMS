<?= $this->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $this->translate('Page settings') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $this->generateUrl('page_update') ?>" class="form-horizontal">
                    <input value="<?= $page->id() ?>" type="hidden" name="page_id" />

                    <div class="form-group <?= $this->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= $this->translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" value="<?= $page->title ?>" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $this->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= $this->translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="255" id="textareaDescription" rows="3"><?= $page->description ?></textarea>
                        </div>
                    </div>
                    <div class="form-group <?= $this->hasValidationError('keywords', 'has-error') ?>">
                        <label for="inputKeywords" class="col-sm-3 control-label">
                            <?= $this->translate('Keywords') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputKeywords" value="<?= $page->keywords ?>" type="text" class="form-control" name="keywords" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="selectPage" class="col-sm-3 control-label">
                            <?= $this->translate('Top page') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="parent_navitem_id" id="selectPage">
                                <option value=""><?= $this->translate('None') ?></option>
                                <?= $this->renderNavitemOptions($navitems, 0, $parentNavitemId) ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="selectVisibility" class="col-sm-3 control-label">
                            <?= $this->translate('Visibility') ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="visibility" id="selectVisibility">
                                <option value="visible" <?= ($page->visibility === 'visible' ? 'selected' : '') ?>><?= $this->translate('Visible') ?></option>
                                <option value="restricted" <?= ($page->visibility === 'restricted' ? 'selected' : '') ?>><?= $this->translate('Restricted') ?></option>
                                <option value="hidden" <?= ($page->visibility === 'hidden' ? 'selected' : '') ?>><?= $this->translate('Hidden') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input name="is_active" value="0" type="hidden" />
                                    <input name="is_active" value="1" type="checkbox" <?= ($page->is_active ? 'checked' : '') ?>> <?= $this->translate('Page is active') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> <?= $this->translate('Update') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
