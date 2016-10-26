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
                            <th><?= translate('Email address') ?></th>
                            <th class="no-order no-search"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modules as $module) { ?>
                            <tr>
                                <td>
                                    <?= $module->title ?>
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
                install module form
            </div>
        </div>

    </div>
</div>

