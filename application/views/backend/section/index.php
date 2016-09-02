<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= translate('Page sections') ?><span class="label label-primary pull-right"><?= $page->title ?></span>
                </h3>
            </div>
            <div class="panel-body">

                <?php if ($sections->count()) { ?>
                    <div class="nestable"  data-max-depth="1" data-save-url="<?= generate_url('section_reorder') ?>" id="nestable">
                        <?= $view->renderSectionNestable($sections) ?>
                    </div>
                    <ul class="list-inline">
                        <li><i class="fa fa-eye"></i> = <?= translate('Enabled') ?></li>
                        <li><i class="fa fa-ban"></i> = <?= translate('Disabled') ?></li>
                    </ul>
                <?php } else { ?>
                    <p class="text-center text-muted"><?= translate('No results found') ?></p>
                <?php } ?>
            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= translate('New section') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= generate_url('section_create') ?>" class="form-horizontal">
                    <input value="<?= $page->id() ?>" type="hidden" name="page_id" />

                    <div class="form-group <?= $view->hasValidationError('module_id', 'has-error') ?>">
                        <label for="selectModule" class="col-sm-3 control-label">
                            <?= translate('Module') ?>
                        </label>
                        <div class="col-sm-9">
                            <select required="" class="form-control select2" name="module_id" id="selectModule" data-placeholder="">
                                <?php
                                foreach ($modules as $module) {

                                    ?>
                                    <option value="<?= $module->id() ?>"><?= $module->title ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-3">
                            <div class="radio">
                                <label>
                                    <input name="is_active" value="1" type="radio" checked /> <?= translate('Enabled') ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="radio">
                                <label>
                                    <input name="is_active" value="0" type="radio" /> <?= translate('Disabled') ?>
                                </label>
                            </div>
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

