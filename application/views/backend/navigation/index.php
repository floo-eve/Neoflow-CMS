<?php ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('All navigations') ?>
                </h3>
            </div>

            <div class="panel-body">

                <?php if ($navigations->count()) { ?>

                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>
                                    <?= translate('Title') ?>
                                </th>
                                <th>
                                    <?= translate('Description') ?>
                                </th>
                                <th class="no-order no-search"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($navigations as $navigation) { ?>
                                <tr>
                                    <td class="nowrap">
                                        <a href="<?= generate_url('navitem_index', array('id' => $navigation->id())) ?>" <?= ($navigation->id() === 1 ? 'class="disabled"' : '') ?>>
                                            <?= $navigation->title ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= nl2br($navigation->description) ?>
                                    </td>
                                    <td class="text-right nowrap">

                                        <a href="<?= generate_url('navitem_index', array('id' => $navigation->id())) ?>" class="btn btn-default btn-xs" title="<?= translate('Items') ?>">
                                            <i class="fa fa-fw fa-th-list"></i>
                                        </a>
                                        <a href="<?= generate_url('navigation_edit', array('id' => $navigation->id())) ?>" class="btn btn-default btn-xs" title="<?= translate('Details') ?>">
                                            <i class="fa fa-fw fa-cog"></i>
                                        </a>
                                        <a <?= ($navigation->id() === 1 ? 'disabled' : '') ?> href="<?= generate_url('navigation_delete', array('id' => $navigation->id())) ?>" class="btn btn-primary btn-xs confirm" data-message="<?= translate('Are you sure you want to delete it?') ?>" title="<?= translate('Delete {0}', array($navigation->title)) ?>">
                                            <i class="fa fa-fw fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                <?php } else { ?>
                    <p class="text-center text-muted"><?= translate('No results found') ?></p>
                <?php } ?>

            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= translate('Create navigation') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('navigation_create') ?>" class="form-horizontal">
                    <div class="form-group">
                        <label for="inputTitle" class="col-sm-3 control-label">
                            <?= translate('Title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textareaDescription" class="col-sm-3 control-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"></textarea>
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