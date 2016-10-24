<?php $view->startBlock(1) ?>

<div class="row">
    <div class="col-sm-6 col-lg-4">

        <?= $view->renderTemplate('page-brand') ?>

        <div class="login-panel">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= translate('Create new password') ?></h3>
                </div>
                <div class="panel-body">

                    <p>
                        <?= translate('Please enter the new password for your user account, registered under the email address {0}.', array($user->email)) ?>
                    </p>

                    <?= $view->renderAlert() ?>

                    <form role="form" method="post" action="<?= generate_url('backend_update_password') ?>">
                        <input type="hidden" name="user_id" value="<?= $user->id() ?>" />
                        <input type="hidden" name="reset_key" value="<?= $user->reset_key ?>" />
                        <div class="form-group">
                            <label for="inputPassword">
                                <?= translate('Password') ?>
                            </label>
                            <input id="inputPassword" class="form-control" name="password" type="password" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword2">
                                <?= translate('Confirm password') ?>
                            </label>
                            <input id="inputPassword2" class="form-control" name="password2" type="password" autofocus>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= translate('Save') ?>
                            </button>
                        </div>
                    </form>

                    <hr />

                    <a href="<?= generate_url('backend_login') ?>"><?= translate('Login') ?></a>
                </div>
            </div>


            <footer class="page-footer text-center">
                Neoflow CMS is released under the <a href="https://github.com/Neoflow/Neoflow-CMS/blob/master/LICENSE">MIT License</a>.
            </footer>

        </div>
    </div>
</div>

<?php $view->stopBlock() ?>