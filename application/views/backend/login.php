<?php $view->startBlock(2) ?>

<div class="row">
    <div class="col-sm-6 col-lg-4">

        <?= $view->renderTemplate('page-brand') ?>

        <div class="login-panel">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $view->translate('Login') ?></h3>
                </div>
                <div class="panel-body">

                    <?= $view->renderAlert() ?>

                    <form role="form" method="post" action="<?= $view->generateUrl('backend_auth') ?>">
                        <div class="form-group">
                            <label for="inputEmail">
                                <?= $view->translate('Email address') ?>
                            </label>
                            <input id="inputEmail" class="form-control" name="email" type="text" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword">
                                <?= $view->translate('Password') ?>
                            </label>
                            <input id="inputPassword" class="form-control" name="password" type="password" value="">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-sign-in"></i><?= $view->translate('Login') ?>
                            </button>
                        </div>
                    </form>

                    <hr />

                    <a href="<?= $view->generateUrl('backend_lost_password') ?>"><?= $view->translate('Lost your password?') ?></a>
                </div>
            </div>


            <footer class="page-footer text-center">
                Neoflow CMS is released under the <a href="https://github.com/Neoflow/Neoflow-CMS/blob/master/LICENSE">MIT License</a>.
            </footer>

        </div>
    </div>
</div>

<?php $view->stopBlock() ?>