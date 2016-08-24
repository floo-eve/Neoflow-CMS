<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Edit role') ?><span class="label label-primary pull-right"><?= $role->title ?></span>
                </h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('role_update') ?>" class="form-horizontal">
                    <input value="<?= $role->id() ?>" type="hidden" name="role_id" />

                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-2 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputTitle" value="<?= $role->title ?>" type="text" required class="form-control" name="title" maxlength="20" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-2 control-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"><?= $role->description ?></textarea>
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('permission_ids', 'has-error') ?>">
                        <label for="selectPermissions" class="col-sm-2 control-label">
                            <?= translate('Permissions') ?>
                        </label>
                        <div class="col-sm-10">
                            <select required multiple class="form-control select2" name="permission_ids[]" id="selectPermissions" data-placeholder="">
                                <?php
                                foreach ($permissions as $permission) {

                                    ?>
                                    <option value="<?= $permission->id() ?>" <?= (in_array($permission->id(), $role->permission_ids) ? 'selected' : '') ?> data-description="<?= $permission->translated('description') ?>" ><?= $permission->translated('title') ?></option>
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
</div>
