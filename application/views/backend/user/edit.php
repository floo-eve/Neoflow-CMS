<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('Edit {0}', array('User')) ?>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= $view->generateUrl('user_update') ?>" class="form-horizontal">
                    <input value="<?= $user->id() ?>" type="hidden" name="user_id" />

                    <div class="form-group <?= $view->hasValidationError('email', 'has-error') ?>">
                        <label for="inputEmail" class="col-sm-2 control-label">
                            <?= $view->translate('Title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputEmail" value="<?= $user->email ?>" type="email" required class="form-control" name="email" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('role_id', 'has-error') ?>">
                        <label for="selectRole" class="col-sm-2 control-label">
                            <?= $view->translate('Role') ?>
                        </label>
                        <div class="col-sm-10">
                            <select required class="form-control select2" name="role_id" id="selectRole" data-placeholder="">
                                <?php
                                foreach ($roles as $role) {

                                    ?>
                                    <option value="<?= $role->id() ?>" <?= ($role->id() === $user->role_id ? 'selected' : '') ?>><?= $role->title ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= $view->translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
