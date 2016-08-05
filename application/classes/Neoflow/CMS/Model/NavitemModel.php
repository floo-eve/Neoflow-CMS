<?php

namespace Neoflow\CMS\Model;

use \Neoflow\Framework\ORM\AbstractEntityModel;
use \Neoflow\Framework\ORM\EntityRepository;

class NavitemModel extends AbstractEntityModel
{

    /**
     * @var string
     */
    public static $tableName = 'navitems';

    /**
     * @var string
     */
    public static $primaryKey = 'navitem_id';

    /**
     * @var array
     */
    public static $properties = ['navitem_id', 'title', 'page_id',
        'parent_navitem_id', 'navigation_id', 'language_id',
        'position'];

    /**
     * Get child navitems.
     *
     * @return EntityRepository
     */
    public function childNavitems()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get parent navitem.
     *
     * @return EntityRepository
     */
    public function parentNavitem()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get language
     *
     * @return EntityRepository
     */
    public function language()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'language_id');
    }

    /**
     * Get navigation
     *
     * @return EntityRepository
     */
    public function navigation()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\NavigationModel', 'navigation_id');
    }

    /**
     * Get page
     *
     * @return EntityRepository
     */
    public function page()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }

    public function save($validate = true)
    {
        if (!$this->title) {
            $page = $this->page()->fetch();
            $this->title = $page->title;
        }

        if (!$this->position) {
            $this->position = 1;
            $navigation = $this->navigation()->fetch();
            $lastNavitem = $navigation->navitems()
                ->where('parent_navitem_id', '=', $this->parent_navitem_id)
                ->orderByDesc('position')
                ->fetch();

            if ($lastNavitem) {
                $this->position = $lastNavitem->position + 1;
            }
        }

        return parent::save($validate);
    }

    public function delete()
    {
        if ($this->navigation_id === 1) {
            $page = $this->page()->fetch();
            if ($page) {
                $page->delete();
            }
        }

        $childNavitems = $this->childNavitems()->fetchAll();
        if ($childNavitems) {
            foreach ($childNavitems as $childNavitem) {
                $childNavitem->delete();
            }
        }

        return parent::delete();
    }

    public function validate()
    {
        $validator = new \Neoflow\Framework\Handler\Validation\Validator($this->toArray());

        $validator
            ->callback(function($parent_navitem_id, $navitem) {

                $forbiddenNavitemIds = $navitem->childNavitems()
                    ->orderByAsc('position')
                    ->fetchAll()
                    ->map(function($navitem) {
                        return $navitem->id();
                    })->toArray();

                if ($navitem->id()) {
                    $forbiddenNavitemIds[] = $navitem->id();
                }

                return (!in_array($parent_navitem_id, $forbiddenNavitemIds));
            }, 'The navitem himself or subnavitems cannot be the top navitem', array($this))
            ->set('parent_navitem_id', 'Top navitem');

        return $validator->validate();
    }
}
