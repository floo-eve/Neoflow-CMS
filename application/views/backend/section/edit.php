<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="row">

    <div class="col-lg-8">
        <?= $view->getBlock('module') ?>
    </div>

    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit section') ?>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('section_update') ?>" class="form-horizontal">
                    <input value="<?= $section->id() ?>" type="hidden" name="section_id" />
                    <div class="form-group">
                        <label for="inputModule" class="col-sm-3 control-label">
                            <?= translate('Module') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="inputModule" class="form-control" disabled value="<?= $module->name ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <label class="radio-inline">
                                <input name="is_active" value="1" type="radio" <?= ($section->is_active ? 'checked' : '') ?> /> <?= translate('Enabled') ?>
                            </label>
                            <label class="radio-inline">
                                <input name="is_active" value="0" type="radio" <?= ($section->is_active ? '' : 'checked') ?> /> <?= translate('Disabled') ?>
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
