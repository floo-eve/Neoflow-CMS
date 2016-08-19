<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= translate('Message') ?></h3>
    </div>
    <div class="panel-body">
        <p><?= translate('Change your "Hello World" message here.') ?></p>

        <form method="post" action="<?= generate_url('mod_hello_world_save') ?>" class="form-horizontal">

            <div class="form-group <?= $this->hasValidationError('message', 'has-error') ?>">
                <label for="textareaMessage" class="col-sm-2 control-label">
                    <?= translate('Message') ?>
                </label>
                <div class="col-sm-10">
                    <textarea name="message" class="form-control vresize" maxlength="150" id="textareaMessage" rows="3"><?= $message->message ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="saveSubmit" class="btn btn-primary btn-icon btn-icon-left">
                        <i class="fa fa-floppy-o"></i><?= translate('Save') ?>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
