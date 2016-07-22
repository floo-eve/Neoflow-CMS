<div class="row">    <div class="col-lg-8">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title">                    <?= $this->translate('All pages') ?>                </h3>            </div>            <?php if (count($languages) > 1) { ?>                <ul class="nav nav-tabs">                    <?php foreach ($languages as $language) { ?>                        <li <?= ($language->id() === $activeLanguage->id() ? 'class="active"' : '') ?>>                            <a href="<?= $this->generateUrl('page_index', array('language_id' => $language->id())) ?>">                                <?= $language->renderFlagIcon() ?> <?= $language->getTranslatedTitle() ?>                            </a>                        </li>                    <?php } ?>                </ul>            <?php } ?>            <div class="panel-body">                <?php if ($navitems) { ?>                    <div class="nestable" data-save-url="<?= $this->generateUrl('navigation_update_navitem_order') ?>" id="nestable">                        <?= $this->renderNavitemNestable($navitems) ?>                    </div>                    <ul class="list-inline">                        <li><i class="fa fa-fw fa-eye"></i> = <?= $this->translate('Active') ?></li>                        <li><i class="fa fa-fw fa-lock"></i> = <?= $this->translate('Restricted') ?></li>                        <li><i class="fa fa-fw fa-eye-slash"></i> = <?= $this->translate('Hidden') ?></li>                        <li><i class="fa fa-fw fa-ban"></i> = <?= $this->translate('Disabled') ?></li>                    </ul>                <?php } else { ?>                    <p class="alert alert-warning"><?= $this->translate('Sorry, no page found') ?></p>                <?php } ?>            </div>        </div>    </div>    <div class="col-lg-4">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title"><?= $this->translate('New page') ?></h3>            </div>            <div class="panel-body">                <form method="post" action="<?= $this->generateUrl('page_create') ?>" class="form-horizontal">                    <input type="hidden" value="<?= $activeLanguage->id() ?>" name="language_id" />                    <div class="form-group">                        <label for="inputTitle" class="col-sm-3 control-label">                            <?= $this->translate('Title') ?>                        </label>                        <div class="col-sm-9">                            <input id="inputTitle" type="text" required class="form-control" name="title" maxlength="50" minlength="3" />                        </div>                    </div>                    <div class="form-group">                        <label for="selectPage" class="col-sm-3 control-label">                            <?= $this->translate('Top page') ?>                        </label>                        <div class="col-sm-9">                            <select class="form-control select2" name="parent_navitem_id" id="selectPage">                                <option value=""><?= $this->translate('None') ?></option>                                <?= $this->renderNavitemOptions($navitems) ?>                            </select>                        </div>                    </div>                    <div class="form-group">                        <div class="col-sm-offset-3 col-sm-9">                            <button type="submit" class="btn btn-primary btn-icon"><i class="fa fa-fw fa-save"></i><?= $this->translate('Create') ?></button>                        </div>                    </div>                </form>            </div>        </div>    </div></div><?phpif ($this->hasBlock(3)) {    echo $this->getBlock(3);}?>