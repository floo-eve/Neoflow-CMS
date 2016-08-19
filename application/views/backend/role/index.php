<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('All roles') ?>
                </h3>
            </div>
            <div class="panel-body">

                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th><?= translate('Title') ?></th>
                            <th><?= translate('Description') ?></th>
                            <th><?= translate('Permissions') ?></th>
                            <th class="no-order no-search"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role) { ?>
                            <tr>
                                <td>
                                    <a href="<?= generate_url('role_edit', array('id' => $role->id())) ?>" title="<?= translate('Edit {0}', array($role->title)) ?>">
                                        <?= $role->title ?>
                                    </a>
                                </td>
                                <td><?= nl2br($role->description) ?></td>
                                <td><?= $role->permissions()->fetchAll()->implode('title', ', ') ?></td>
                                </td>
                                <td class="text-right nowrap">
                                    <a href="<?= generate_url('role_edit', array('id' => $role->id())) ?>" class="btn btn-default btn-xs btn-icon btn-icon-left" title="<?= translate('Edit {0}', array($role->title)) ?>">
                                        <i class="fa fa-fw fa-pencil"></i> <?= translate('Edit') ?>
                                    </a>
                                    <a href="<?= generate_url('role_delete', array('id' => $role->id())) ?>" class="btn btn-primary btn-xs confirm" data-message="<?= translate('Are you sure you want to delete it?') ?>" title="<?= translate('Delete {0}', array($role->title)) ?>">
                                        <i class="fa fa-fw fa-trash-o"></i>
                                    </a>
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
                <h3 class="panel-title">
                    <?= translate('Create role') ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('role_create') ?>" class="form-horizontal">
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="inputTitle" maxlength="20" required class="form-control" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('permission_ids', 'has-error') ?>">
                        <label for="selectPermissions" class="col-sm-3 control-label">
                            <?= translate('Permissions') ?>
                        </label>
                        <div class="col-sm-9">
                            <select required multiple class="form-control select2" name="permission_ids[]" id="selectPermissions" data-placeholder="">
                                <?php
                                foreach ($permissions as $permission) {

                                    ?>
                                    <option value="<?= $permission->id() ?>" data-description="<?= $permission->translated('description') ?>" ><?= $permission->translated('title') ?></option>
                                    <?php
                                }

                                ?>
                            </select>
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

