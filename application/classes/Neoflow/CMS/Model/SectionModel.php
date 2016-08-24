<?php

namespace Neoflow\CMS\Model;

use InvalidArgumentException;
use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Support\Validation\Validator;

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
        'position', 'block', 'is_active',];

    /**
     * Get repository to fetch page.
     *
     * @return EntityRepository
     */
    public function page()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }

    /**
     * Get repository to fetch module.
     *
     * @return EntityRepository
     */
    public function module()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ModuleModel', 'module_id');
    }

    /**
     * Render module view of section to html output.
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

    /**
     * Save section.
     *
     * @return bool
     */
    public function save()
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

        return parent::save();
    }

    /**
     * Validate setting.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required()
            ->set('module_id', 'Module');

        return $validator->validate();
    }
}
