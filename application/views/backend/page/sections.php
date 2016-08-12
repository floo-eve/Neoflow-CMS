<?= $view->renderTemplate('backend/page/navbar', array('page' => $page)) ?>

<div class="row">
    <div class="col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $view->translate('Page sections of {0}', array('<i>' . $page->title . '</i>')) ?>
                </h3>
            </div>
            <div class="panel-body">

                <?php if ($sections->count()) { ?>
                    <div class="nestable"  data-max-depth="1" data-save-url="<?= $view->generateUrl('section_reorder') ?>" id="nestable">
                        <?= $view->renderSectionNestable($sections) ?>
                    </div>
                    <ul class="list-inline">
                        <li><i class="fa fa-eye"></i> = <?= $view->translate('Active') ?></li>
                        <li><i class="fa fa-ban"></i> = <?= $view->translate('Disabled') ?></li>
                    </ul>
                <?php } else { ?>
                    <p class="alert alert-warning"><?= $view->translate('No sections found') ?></p>
                <?php } ?>
            </div>
        </div>

    </div>
    <div class="col-lg-4">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $view->translate('New section') ?></h3>
            </div>
            <div class="panel-body">
                <form method="post" action="<?= $view->generateUrl('section_create') ?>" class="form-horizontal">
                    <input value="<?= $page->id() ?>" type="hidden" name="page_id" />

                    <div class="form-group <?= $view->hasValidationError('module_id', 'has-error') ?>">
                        <label for="selectModule" class="col-sm-3 control-label">
                            <?= $view->translate('Module') ?>
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
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input name="is_active" value="0" type="hidden" />
                                    <input name="is_active" value="1" type="checkbox" checked /> <?= $view->translate('Section is active') ?>
                                </label>
                            </div>
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

