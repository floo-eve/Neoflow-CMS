<?php

namespace Neoflow\CMS\Model;

use InvalidArgumentException;
use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;

class SectionModel extends AbstractEntityModel
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
        'position', 'block', 'is_active'];

    /**
     * Get page.
     *
     * @return EntityRepository
     */
    public function page()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }

    /**
     * Get module.
     *
     * @return EntityRepository
     */
    public function module()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ModuleModel', 'module_id');
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
        throw new InvalidArgumentException('Cannot find module with ID: ' . $this->module_id);
    }

    public function save($validate = true)
    {
        if (!$this->position) {
            $this->position = 1;
            $page = $this->page()->fetch();
            $lastSection = $page->sections()
                ->orderByDesc('position')
                ->fetch();

            if ($lastSection) {
                $this->position = $lastSection->position + 1;
            }
        }
        return parent::save($validate);
    }

    public function validate()
    {
        $validator = new \Neoflow\Support\Validation\Validator($this->toArray());

        $validator
            ->required()
            ->set('module_id', 'Module');

        return (bool) $validator->validate();
    }
}
