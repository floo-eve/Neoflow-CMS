<?php

namespace Lahaina\CMS\Model;

use InvalidArgumentException;
use Lahaina\CMS\Views\FrontendView;
use Lahaina\Framework\Core\AbstractModel;
use Lahaina\Framework\Persistence\ORM;

class SectionModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'sections';

    /**
     * @var string
     */
    public static $primaryKey = 'section_id';

    /**
     * @var array
     */
    public static $properties = ['section_id', 'page_id', 'module_id',
        'position', 'block', ];

    /**
     * Get page.
     *
     * @return ORM
     */
    public function page()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\PageModel', 'page_id');
    }

    /**
     * Get module.
     *
     * @return ORM
     */
    public function module()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\ModuleModel', 'module_id');
    }

    /**
     * Render section module view to html output.
     *
     * @param FrontendView $view
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function render($view)
    {
        $view->set('section_id', $this->id());
        $module = $this->module()->fetch();
        if ($module) {
            return $module->render($view);
        }
        throw new InvalidArgumentException('Cannot find module with ID: '.$this->module_id);
    }
}
