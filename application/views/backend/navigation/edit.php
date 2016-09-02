<?= $view->renderTemplate('backend/navigation/navbar', array('navigation' => $navigation)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit navigation') ?><span class="label label-primary pull-right"><?= $navigation->title ?></span>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('navigation_update') ?>" class="form-horizontal">
                    <input value="<?= $navigation->id() ?>" type="hidden" name="navigation_id" />

                    <div class="form-group">
                        <label for="inputTitle" class="col-sm-2 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" value="<?= $navigation->title ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textareaDescription" class="col-sm-2 control-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"><?= $navigation->description ?></textarea>
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
