<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Hi and welcome!</h3>
    </div>
    <div class="panel-body">
        <p>
            <i>Hello World</i> was developped for test-cases only and is the
            first official page-module of the Neoflow CMS.
        </p>
        <p>
            Change your message in the following textarea to create and publish
            your first <i>Hello World</i> message on your website.
        </p>

        <hr />

        <form method="post" action="<?= generate_url('mod_hello_world_update') ?>" class="form-horizontal">

            <div class="form-group <?= $this->hasValidationError('message', 'has-error') ?>">
                <input type="hidden" value="<?= $message->id() ?>" name="message_id" />
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
