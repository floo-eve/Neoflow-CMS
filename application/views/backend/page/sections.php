<?= $this->renderTemplate('backend/page/navbar', array('page' => $page)) ?><div class="row">    <div class="col-lg-8">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title">                    <?= $this->translate('Page sections') ?>                </h3>            </div>            <div class="panel-body">                <?= $this->renderTemplate('backend/page/infos', array('page' => $page)) ?>                <hr />                <?php if ($sections) { ?>                    <div class="nestable"  data-max-depth="1" data-save-url="<?= $this->generateUrl('section_update_order') ?>" id="nestable">                        <?= $this->renderSectionNestable($sections) ?>                    </div>                    <ul class="list-inline">                        <li><i class="fa fa-fw fa-eye"></i> = <?= $this->translate('Active') ?></li>                        <li><i class="fa fa-fw fa-ban"></i> = <?= $this->translate('Disabled') ?></li>                    </ul>                <?php } else { ?>                    <p class="alert alert-warning"><?= $this->translate('Sorry, no section found') ?></p>                <?php } ?>                <hr />                <a href="<?= $this->generateUrl('page_index', array('language_id' => $page->language_id)) ?>" class="btn btn-default btn-icon">                    <i class="fa fa-chevron-left"></i><?= $this->translate('Back') ?>                </a>            </div>        </div>    </div>    <div class="col-lg-4">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title"><?= $this->translate('Create section') ?></h3>            </div>            <div class="panel-body">                <form method="post" action="<?= $this->generateUrl('section_create') ?>" class="form-horizontal">                    <input value="<?= $page->id() ?>" type="hidden" name="page_id" />                    <div class="form-group">                        <label for="selectModule" class="col-sm-3 control-label">                            <?= $this->translate('Module') ?>                        </label>                        <div class="col-sm-9">                            <select class="form-control select2" name="module_id" id="selectModule" data-placeholder="">                                <option value=""></option>                                <?php                                foreach ($modules as $module) {                                    ?>                                    <option value="<?= $module->id() ?>"><?= $module->title ?></option>                                    <?php                                }                                ?>                            </select>                        </div>                    </div>                    <div class="form-group">                        <div class="col-sm-offset-3 col-sm-9">                            <div class="checkbox">                                <label>                                    <input name="is_active" value="0" type="hidden" />                                    <input name="is_active" value="1" type="checkbox" <?= ($page->is_active ? 'checked' : '') ?>> <?= $this->translate('Section is active') ?>                                </label>                            </div>                        </div>                    </div>                    <div class="form-group">                        <div class="col-sm-offset-3 col-sm-9">                            <button type="submit" class="btn btn-primary btn-icon">                                <i class="fa fa-fw fa-save"></i><?= $this->translate('Create') ?>                            </button>                        </div>                    </div>                </form>            </div>        </div>    </div></div>