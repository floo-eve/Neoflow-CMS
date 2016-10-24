<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;

class ModuleModel extends AbstractEntityModel
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
    public static $properties = ['module_id', 'name', 'folder', 'route', 'title', 'frontend_route', 'backend_route', 'module_manager'];

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
     * Get module manager
     * @return \Neoflow\CMS\Support\Module\ManagerInterface
     * @throws \Exception
     */
    public function getManager()
    {
        $managerClass = $this->manager_class;
        if ($managerClass && class_exists($managerClass)) {
            return new $managerClass();
        }
        throw new \Exception('Module manager not found: ' . $managerClass);
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
