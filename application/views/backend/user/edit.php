<div class="row">
    <div class="col-lg-7">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit user') ?>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('user_update') ?>" class="form-horizontal">
                    <input value="<?= $user->id() ?>" type="hidden" name="user_id" />

                    <div class="form-group <?= $view->hasValidationError('email', 'has-error') ?>">
                        <label for="inputEmail" class="col-sm-2 control-label">
                            <?= translate('Email address') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputEmail" value="<?= $user->email ?>" type="email" required class="form-control" name="email" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('firstname', 'has-error') ?>">
                        <label for="inputFirstname" class="col-sm-2 control-label">
                            <?= translate('Firstname') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputFirstname" value="<?= $user->firstname ?>" type="text" maxlength="50" class="form-control" name="firstname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('lastname', 'has-error') ?>">
                        <label for="inputLastname" class="col-sm-2 control-label">
                            <?= translate('Lastname') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputLastname" value="<?= $user->lastname ?>" type="text" maxlength="50" class="form-control" name="lastname" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('role_id', 'has-error') ?>">
                        <label for="selectRole" class="col-sm-2 control-label">
                            <?= translate('Role') ?>
                        </label>
                        <div class="col-sm-10">
                            <select <?= ($user->id() === 1 ? 'disabled' : '') ?>  required class="form-control select2" name="role_id" id="selectRole" data-placeholder="">
                                <?php
                                foreach ($roles as $role) {

                                    ?>
                                    <option value="<?= $role->id() ?>" <?= ($role->id() == $user->role_id ? 'selected' : '') ?>><?= $role->title ?></option>
                                    <?php
                                }

                                ?>
                            </select>
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

    <div class="col-lg-5">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Change password') ?>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('user_update_password') ?>" class="form-horizontal">
                    <input value="<?= $user->id() ?>" type="hidden" name="user_id" />

                    <div class="form-group <?= $view->hasValidationError('password', 'has-error') ?>">
                        <label for="inputPassword" class="col-sm-3 control-label">
                            <?= translate('New password') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputPassword" type="password" required class="form-control" name="password" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('password2', 'has-error') ?>">
                        <label for="inputPassword2" class="col-sm-3 control-label">
                            <?= translate('Confirm password') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputPassword2" type="password" class="form-control" name="password2" />
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
