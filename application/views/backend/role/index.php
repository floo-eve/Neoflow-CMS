<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('All {0}', array('Roles')) ?>
                </h3>
            </div>
            <div class="panel-body">

                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th><?= $view->translate('Title') ?></th>
                            <th><?= $view->translate('Description') ?></th>
                            <th><?= $view->translate('Permissions') ?></th>
                            <th class="no-order no-search"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role) { ?>
                            <tr>
                                <td>
                                    <a href="<?= $view->generateUrl('role_edit', array('id' => $role->id())) ?>" title="<?= $view->translate('Edit {0}', array($role->title)) ?>">
                                        <?= $role->title ?>
                                    </a>
                                </td>
                                <td><?= nl2br($role->description) ?></td>
                                <td><?= $role->permissions()->fetchAll()->implode('title', ', ') ?></td>
                                </td>
                                <td class="text-right nowrap">
                                    <a href="<?= $view->generateUrl('role_edit', array('id' => $role->id())) ?>" class="btn btn-default btn-xs btn-icon btn-icon-left" title="<?= $view->translate('Edit {0}', array($role->title)) ?>">
                                        <i class="fa fa-fw fa-pencil"></i> <?= $view->translate('Edit') ?>
                                    </a>
                                    <a href="<?= $view->generateUrl('role_delete', array('id' => $role->id())) ?>" class="btn btn-danger btn-xs confirm" data-message="<?= $view->translate('Are you sure you want to delete it?') ?>" title="<?= $view->translate('Delete {0}', array($role->title)) ?>">
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
                    <?= $view->translate('Create {0}', array('Role')) ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $view->generateUrl('role_create') ?>" class="form-horizontal">
                    <div class="form-group <?= $view->hasValidationError('title', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= $view->translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" name="title" id="inputTitle" maxlength="50" required class="form-control" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('description', 'has-error') ?>">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= $view->translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" id="textareaDescription" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('permission_ids', 'has-error') ?>">
                        <label for="selectPermissions" class="col-sm-3 control-label">
                            <?= $view->translate('Permissions') ?>
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
                                <i class="fa fa-save"></i><?= $view->translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

