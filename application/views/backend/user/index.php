<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('All {0}', array('Users')) ?>
                </h3>
            </div>
            <div class="panel-body">

                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th><?= $view->translate('Email address') ?></th>
                            <th><?= $view->translate('Name') ?></th>
                            <th><?= $view->translate('Role') ?></th>
                            <th class="no-order no-search"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr>
                                <td>
                                    <a href="<?= $view->generateUrl('user_edit', array('id' => $user->id())) ?>" title="<?= $view->translate('Edit {0}', array($user->getFullName())) ?>">
                                        <?= $user->email ?>
                                    </a>
                                </td>
                                <td><?= $user->getFullName() ?></td>
                                <td><?= $user->role()->fetch()->title ?></td>
                                </td>
                                <td class="text-right nowrap">
                                    <a href="<?= $view->generateUrl('user_edit', array('id' => $user->id())) ?>" class="btn btn-default btn-xs btn-icon btn-icon-left" title="<?= $view->translate('Edit {0}', array($user->title)) ?>">
                                        <i class="fa fa-fw fa-pencil"></i> <?= $view->translate('Edit') ?>
                                    </a>
                                    <a href="<?= $view->generateUrl('user_delete', array('id' => $user->id())) ?>" class="btn btn-danger btn-xs confirm" data-message="<?= $view->translate('Are you sure you want to delete it?') ?>" title="<?= $view->translate('Delete {0}', array($user->title)) ?>">
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
                    <?= $view->translate('Create {0}', array('User')) ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $view->generateUrl('user_create') ?>" class="form-horizontal">
                    <div class="form-group <?= $view->hasValidationError('email', 'has-error') ?>">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= $view->translate('Email address') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="inputTitle" required class="form-control" />
                        </div>
                    </div>

                    <div class="form-group <?= $view->hasValidationError('role_id', 'has-error') ?>">
                        <label for="selectRole" class="col-sm-3 control-label">
                            <?= $view->translate('Role') ?>
                        </label>
                        <div class="col-sm-9">
                            <select required multiple class="form-control select2" name="role_id" id="selectRole" data-placeholder="">
                                <?php
                                foreach ($roles as $role) {

                                    ?>
                                    <option value="<?= $role->id() ?>"><?= $role->title ?></option>
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

