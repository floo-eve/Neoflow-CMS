<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $this->translate('All roles') ?>
                </h3>
            </div>
            <div class="panel-body">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?= $this->translate('Title') ?></th>
                            <th><?= $this->translate('Description') ?></th>
                            <th><?= $this->translate('Permissions') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role) { ?>
                            <tr>
                                <td><?= $role->title ?></td>
                                <td><?= nl2br($role->description) ?></td>
                                <td><?php
                                    echo implode(', ', array_map(function($role) {
                                            return $role->getTranslatedTitle();
                                        }, $role->permissions()->fetchAll()))

                                    ?>
                                </td>
                                <td class="text-right">
                                    DEL, Edit
                                </td>
                            </tr>
                        <?php } ?> 
                    </tbody>
                </table>

            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $this->translate('New role') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $this->generateUrl('role_create') ?>" class="form-horizontal">
                    <div class="form-group <?= $this->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= $this->translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="inputTitle" maxlength="50" required class="form-control" />
                        </div>
                    </div>

                    <div class="form-group <?= $this->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= $this->translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" id="textareaDescription" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group <?= $this->hasValidationError('module_id', 'has-error') ?>">
                        <label for="selectPermissions" class="col-sm-3 control-label">
                            <?= $this->translate('Permissions') ?>
                        </label>
                        <div class="col-sm-9">
                            <select required multiple class="form-control select2" name="permission_ids[]" id="selectPermissions" data-placeholder="">
                                <?php
                                foreach ($permissions as $permission) {

                                    ?>
                                    <option value="<?= $permission->id() ?>" data-description="<?= $permission->getTranslatedDescription() ?>" ><?= $permission->getTranslatedTitle() ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= $this->translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

