<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\CMS\Support\Alert\SuccessAlert;
use Neoflow\CMS\Support\Alert\WarningAlert;

class MaintenanceController extends BackendController
{
    public function indexAction($args)
    {
        $this->view->setTitle('Maintenance');

        return $this->render('backend/maintenance/index');
    }

    public function deleteCacheAction($args)
    {
        $cacheTag = $this->getRequest()->getPost('cache');

        $cache = $this->app()->get('cache');
        if ($cacheTag) {
            if ($cacheTag === 'all') {
                $cache->clear();
            } else {
                $cache->deleteByTag($cacheTag);
            }
            $alert = new SuccessAlert('Cache successful cleared');
        } else {
            $alert = new WarningAlert('No cache selected');
        }
        $this->setFlash('alert', $alert);

        return $this->redirectToRoute('maintenance_index');
    }

    public function createDumpAction($args)
    {
        $filename = 'backup-'.date('d-m-Y').'.sql';
        //$mime = "application/x-text";
        //header("Content-Type: " . $mime);
        //header('Content-Disposition: attachment; filename="' . $filename . '"');

        $db = $this->config()->get('database')->get('dbname');
        $user = $this->config()->get('database')->get('username');
        $pw = $this->config()->get('database')->get('password');
        $host = $this->config()->get('database')->get('host');

        echo "mysqldump --user=$user --host=$host $db";

        $dump = shell_exec("mysqldump --user=$user --host=$host $db");

        passthru("mysqldump --user=$user --host=$host $db");
        echo $dump;
        exit;

        $response = new Response();
        $response->setContent($dump);
        // $response->setHeader('Content-Type: ' . $mime);
        $response->setHeader('Content-Disposition: attachment; filename="'.$filename.'"');

        return $response;
        //$this->app()->get('database')->exec('mysqldump --user=... --password=... --host=... DB_NAME > /path/to/output/file.sql');
    }
}
