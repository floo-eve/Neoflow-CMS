<?php ?><div class="row">    <div class="col-lg-8">        <div class="panel panel-primary">            <div class="panel-heading">                <h4 class="panel-title"><?= translate('All navigations') ?></h4>            </div>            <div class="panel-body">                <table class="table table-striped">                    <thead>                        <tr>                            <th>                                <?= translate('Title') ?>                            </th>                            <th>                                <?= translate('Description') ?>                            </th>                            <th></th>                        </tr>                    </thead>                    <tbody>                        <?php foreach ($navigations as $navigation) { ?>                            <tr>                                <td class="nowrap">                                    <a href="<?= generate_url('navigation_edit', array('id' => $navigation->id())) ?>" <?= ($navigation->id() === 1 ? 'class="disabled"' : '') ?>>                                        <?= $navigation->title ?>                                    </a>                                </td>                                <td>                                    <?= $navigation->description ?>                                </td>                                <td class="text-right nowrap">                                    <a href="<?= generate_url('navigation_edit', array('id' => $navigation->id())) ?>" class="btn btn-default btn-xs <?= ($navigation->id() === 1 ? 'disabled' : '') ?>">Bearbeiten</a>                                    <a href="<?= generate_url('navigation_delete', array('id' => $navigation->id())) ?>" class="btn btn-primary btn-xs <?= ($navigation->id() === 1 ? 'disabled' : '') ?>"><?= translate('Delete') ?></a>                                </td>                            </tr>                        <?php } ?>                    </tbody>                </table>            </div>        </div>    </div>    <div class="col-lg-4">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title"><?= translate('Create navigation') ?></h3>            </div>            <div class="panel-body">                <form method="post" action="<?= generate_url('navigation_create') ?>" class="form-horizontal">                    <div class="form-group">                        <label for="inputTitle" class="col-sm-2 control-label">                            <?= translate('Title') ?>                        </label>                        <div class="col-sm-10">                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />                        </div>                    </div>                    <div class="form-group">                        <label for="textareaDescription" class="col-sm-2 control-label">                            <?= translate('Description') ?>                        </label>                        <div class="col-sm-10">                            <textarea name="description" class="form-control vresize" maxlength="150" id="textareaDescription" rows="3"></textarea>                        </div>                    </div>                    <div class="form-group">                        <div class="col-sm-offset-2 col-sm-10">                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= translate('Save') ?></button>                        </div>                    </div>                </form>            </div>        </div>    </div></div>