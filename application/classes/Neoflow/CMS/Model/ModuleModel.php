<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Handler\Config;
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
    public static $properties = ['module_id', 'name', 'folder', 'route', 'title'];

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
     * Render module frontend.
     *
     * @return string
     */
    public function render($view)
    {
        $moduleFilePath = $this->getPath('/frontend.php');

        return $view->renderFile($moduleFilePath, array(
                'app' => $this->app(),));
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
