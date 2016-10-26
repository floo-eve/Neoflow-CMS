<?php

namespace Neoflow\CMS\Model;

use Exception;
use Neoflow\CMS\Support\Module\ManagerInterface;
use Neoflow\Framework\Common\Container;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Support\Filesystem\Folder;
use ZipArchive;

class ModuleModel extends AbstractEntityModel {

    /**
     * @var string
     */
    public static $tableName = 'modules';

    /**
     * @var string
     */
    public static $primaryKey = 'module_id';

    /**
     * @var array
     */
    public static $properties = ['module_id', 'name', 'folder', 'frontend_route', 'backend_route', 'manager_class'];

    /**
     * Get repository to fetch section.
     *
     * @return EntityRepository
     */
    public function section() {
        return $this->belongsTo('\\WebsiteBaker\\Models\\Section', 'section_id');
    }

    /**
     * Get module manager
     * @return ManagerInterface
     * @throws Exception
     */
    public function getManager() {
        $managerClass = $this->manager_class;
        if ($managerClass && class_exists($managerClass)) {
            return new $managerClass($this->getPath('config.ini'));
        }
        throw new Exception('Module manager not found: ' . $managerClass);
    }

    public function install() {

        if ($this->package) {
            $packageName = basename($this->package['name']);
            $packagePath = $this->config()->getTempPath(DIRECTORY_SEPARATOR . $packageName);
            $tempModulePath = $this->config()->getTempPath(uniqid());
            if (move_uploaded_file($this->package['tmp_name'], $packagePath)) {

                $zip = new ZipArchive();
                if ($zip->open($packagePath)) {
                    $zip->extractTo($tempModulePath);
                    $zip->close();

                    $configFilePath = $tempModulePath . DIRECTORY_SEPARATOR . 'config.ini';
                    if (is_file($configFilePath)) {
                        $folder = new Folder($tempModulePath);

                        $config = new Container(parse_ini_file($configFilePath, true), true, true);

                        $this->name = $config->get('name');
                        $this->folder = $config->get('folder');
                        $this->frontend_route = $config->get('frontend_route');
                        $this->backend_route = $config->get('backend_route');
                        $this->manager_class = $config->get('manager_class');

                        $folder->move($this->getPath());

                        // ##################################################
                        // HEEEEEEEEEEEEEEEEEEEREEEEEEEEEEEEEeee

                        return;
                    } else {
                        unlink($tempModulePath);
                        throw new Exception('Module files e.g. config.ini in root directory of module package not found');
                    }
                }
                throw new Exception('Cannot unpack module package: ' . $packageName);
            }
            throw new Exception('Could not move uploaded module package to temp folder');
        }
        throw new Exception('There is no package to install');
    }

    /**
     * Get module url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri = '') {
        return $this
                        ->config()
                        ->getModulesUrl('/' . $this->folder . '/' . $uri);
    }

    /**
     * Get module path.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getPath($uri = '') {
        return $this
                        ->config()
                        ->getModulesPath('/' . $this->folder . '/' . $uri);
    }

}
