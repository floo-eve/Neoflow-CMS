<div class="row">    <div class="col-lg-8">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title">Logfile (last 100 lines)</h3>            </div>            <div class="panel-body">                <textarea rows="15" readonly id="logfileTextarea" class="form-control vresize"><?php                    $file = file($view->getLogger()->getLogFilePath());                    for ($i = max(0, count($file) - 101); $i < count($file); $i++) {                        echo $file[$i];                    }                    ?></textarea>                <script>                    var textarea = document.getElementById('logfileTextarea');                    textarea.scrollTop = textarea.scrollHeight;                </script>                <ul class="list-inline">                    <li>LogLevel: <?= $view->getConfig()->get('logger')->get('level') ?></li>                    <li>LogFile: <?= str_replace($view->getConfig()->getPath(), '...', $view->app()->get('logger')->getLogFilePath()) ?></li>                </ul>            </div>        </div>    </div>    <div class="col-lg-4">        <div class="panel panel-default">            <div class="panel-heading">                <h3 class="panel-title"><?= $view->translate('Cache') ?></h3>            </div>            <div class="panel-body">                <p><?= $view->translate('Please specify which cached data you want to clear') ?>:</p>                <form method="post" action="<?= $view->generateUrl('maintenance_delete_cache') ?>" class="form-horizontal">                    <div class="form-group">                        <div class="col-xs-12">                            <div class="radio">                                <label>                                    <input type="radio" name="cache" checked="" value="all"> <?= $view->translate('All cached data') ?>                                </label>                            </div>                            <div class="radio">                                <label>                                    <input type="radio" name="cache" value="_query"> <?= $view->translate('Only cached database results') ?>                                </label>                            </div>                            <div class="radio">                                <label>                                    <input type="radio" name="cache" value="_route"> <?= $view->translate('Only cached application routes') ?>                                </label>                            </div>                        </div>                    </div>                    <div class="form-group">                        <div class="col-xs-12">                            <button type="submit" class="btn btn-primary"><i class="fa fa-cog"></i> <?= $view->translate('Clear cache') ?></button>                        </div>                    </div>                </form>            </div>        </div>    </div></div>