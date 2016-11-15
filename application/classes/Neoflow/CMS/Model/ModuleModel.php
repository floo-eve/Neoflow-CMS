<?php

namespace Neoflow\CMS\Model;

use Exception;
use Neoflow\CMS\Support\Extension\ManagerInterface;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Support\Validation\Validator;

class ModuleModel extends AbstractExtensionModel
{

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
    public static $properties = ['module_id', 'name', 'folder', 'frontend_route', 'backend_route', 'namespace'];

    /**
     * Get repository to fetch section.
     *
     * @return EntityRepository
     */
    public function section()
    {
        return $this->belongsTo('\\WebsiteBaker\\Models\\Section', 'section_id');
    }

    /**
     * Validate module
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($name, $id) {
                return ModuleModel::repo()
                    ->where('name', '=', $name)
                    ->where('module_id', '!=', $id)
                    ->count() === 0;
            }, '{0} has to be unique', array($this->id()))
            ->set('name', 'Name');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($folder, $id) {
                return ModuleModel::repo()
                    ->where('folder', '=', $folder)
                    ->where('module_id', '!=', $id)
                    ->count() === 0;
            }, '{0} has to be unique', array($this->id()))
            ->set('folder', 'Folder');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->set('frontend_route', 'Frontend Routekey');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->set('backend_route', 'Backend Routekey');

        $validator
            ->maxLength(100)
            ->callback(function ($namespace, $id) {
                return ModuleModel::repo()
                    ->where('namespace', '=', $namespace)
                    ->where('module_id', '!=', $id)
                    ->count() === 0;
            }, '{0} has to be unique', array($this->id()))
            ->set('namespace', 'Namespace');

        return $validator->validate();
    }

    /**
     * Get module manager
     * @return ManagerInterface
     * @throws Exception
     */
    public function getManager()
    {
        $managerClass = $this->namespace . '\\Manager';
        if (class_exists($managerClass)) {
            return new $managerClass();
        }
        throw new Exception('Manager class not found: ' . $managerClass);
    }

    /**
     * Install module package
     *
     * @return bool
     */
    public function install()
    {
        if (parent::install()) {
            return $this->getManager()->install();
        }
        return false;
    }

    /**
     * Get module url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri = '')
    {
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
    public function getPath($uri = '')
    {
        return $this
                ->config()
                ->getModulesPath('/' . $this->folder . '/' . $uri);
    }
}
