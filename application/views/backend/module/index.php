<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('All modules') ?>
                </h3>
            </div>
            <div class="panel-body">

                <table class="table table-striped datatable">
                    <thead>
                        <tr>
                            <th><?= translate('Name') ?></th>
                            <th class="no-order no-search"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modules as $module) { ?>
                            <tr>
                                <td>
                                    <?= $module->name ?>
                                </td>
                                <td class="text-right nowrap">
                                    Action buttons
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
                    <?= translate('Install module') ?>
                </h3>
            </div>
            <div class="panel-body">
                <form method="post" enctype="multipart/form-data" action="<?= generate_url('module_install') ?>" class="form-horizontal">
                    <div class="form-group">
                        <label for="inputPackage" class="col-sm-3 control-label">
                            <?= translate('Package') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="file" name="package" id="inputPackage" required class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-save"></i><?= translate('Upload') ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

