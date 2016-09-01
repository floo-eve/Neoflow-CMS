

<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= translate('General settings') ?></h3>
            </div>
            <div class="panel-body">

                <form method="post" action="<?= generate_url('setting_update') ?>" class="form-horizontal">
                    <div class="form-group <?= $view->hasValidationError('website_title', 'has-error') ?>">
                        <label for="inputWebsiteTitle" class="col-sm-2 control-label">
                            <?= translate('Website title') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputWebsiteTitle" type="text" required value="<?= $setting->website_title ?>" class="form-control" name="website_title" maxlength="50" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('website_description', 'has-error') ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-2 control-label">
                            <?= translate('Website description') ?>
                        </label>
                        <div class="col-sm-10">
                            <textarea name="website_description" class="form-control vresize" maxlength="150" id="textareaWebsiteDescription" rows="3"><?= $setting->website_description ?></textarea>
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('keywords', 'has-error') ?>">
                        <label for="inputKeywords" class="col-sm-2 control-label">
                            <?= translate('Keywords') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputKeywords" type="text" value="<?= $setting->keywords ?>" class="form-control" name="keywords" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group <?= $view->hasValidationError('Author', 'has-error') ?>">
                        <label for="inputAuthor" class="col-sm-2 control-label">
                            <?= translate('Author') ?>
                        </label>
                        <div class="col-sm-10">
                            <input id="inputAuthor" type="text" value="<?= $setting->author ?>" class="form-control" name="author" maxlength="50" />
                        </div>
                    </div>

                    <hr />

                    <div class="form-group">
                        <label for="selectTheme" class="col-sm-2 control-label">
                            <?= translate('Frontend theme') ?>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="theme_id" id="selectTheme">
                                <?php
                                foreach ($themes as $theme) {
                                    if ($theme->type === 'frontend') {

                                        ?>
                                        <option value="<?= $theme->id() ?>"  <?= ($setting->theme_id = $theme->id() ? 'selected' : '') ?>><?= $theme->title ?></option>
                                        <?php
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="selectBackendTheme" class="col-sm-2 control-label">
                            <?= translate('Backend theme') ?>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="backend_theme_id" id="selectBackendTheme">
                                <?php
                                foreach ($themes as $theme) {
                                    if ($theme->type === 'backend') {

                                        ?>
                                        <option value="<?= $theme->id() ?>"  <?= ($setting->theme_id = $theme->id() ? 'selected' : '') ?>><?= $theme->title ?></option>
                                        <?php
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group">
                        <label for="selectDefaultLanguage" class="col-sm-2 control-label">
                            <?= translate('Default language') ?>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="language_id" id="selectDefaultLanguage">
                                <?php foreach ($languages as $language) {

                                    ?>
                                    <option value="<?= $language->id() ?>"  <?= ($language->id() == $setting->language_id ? 'selected' : '') ?>><?= $language->translated('title') ?></option>
                                <?php }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="selectActiveLanguages" class="col-sm-2 control-label"><?= translate('Languages') ?></label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="active_language_ids[]" multiple id="selectActiveLanguages">
                                <?php foreach ($languages as $language) {

                                    ?>
                                    <option value="<?= $language->id() ?>"  <?= ($language->is_active ? 'selected' : '') ?>><?= $language->translated('title') ?></option>
                                <?php }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" name="saveSubmit" class="btn btn-primary btn-icon btn-icon-left">
                                <i class="fa fa-floppy-o"></i><?= translate('Save') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= translate('Advanced settings') ?></h3>
            </div>
            <div class="panel-body">
                <p>
                    <?= translate('Advanced settings description') ?>
                </p>
                <h4><?= translate('Path of the config file') ?>:</h4>
                <ul>
                    <li><i><?= $view->config()->getPath('/config.php') ?></i></li>
                </ul>
            </div>
        </div>
    </div>
</div>
