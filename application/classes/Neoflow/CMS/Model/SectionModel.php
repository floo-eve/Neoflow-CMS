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

        if (parent::save()) {
            if ($this->isNew) {
                $module = $this->module()->fetch();
                return $module->getManager()->add($this);
            }
            return true;
        }
        return false;
    }

    /**
     * Delete section
     *
     * @return boolean
     */
    public function delete()
    {
        $module = $this->module()->fetch();
        if ($module && $module->getManager()->remove($this)) {
            return parent::delete();
        }
    }

    /**
     * Validate section.
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

    /**
     * Toggle activation
     *
     * @return self
     */
    public function toggleActivation()
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }
        return $this;
    }
}
