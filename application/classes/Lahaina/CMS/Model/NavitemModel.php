<?php

namespace Lahaina\CMS\Model;

use \Lahaina\Framework\Core\AbstractModel;
use \Lahaina\Framework\Persistence\ORM;

class NavitemModel extends AbstractModel
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
        'position', 'collapsed'];

    /**
     * Get child navitems.
     *
     * @return ORM
     */
    public function childNavitems()
    {
        return $this->hasMany('\\Lahaina\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get parent navitem.
     *
     * @return ORM
     */
    public function parentNavitem()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\NavitemModel', 'parent_navitem_id');
    }

    /**
     * Get language
     *
     * @return ORM
     */
    public function language()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\LanguageModel', 'language_id');
    }

    /**
     * Get navigation
     *
     * @return ORM
     */
    public function navigation()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\NavigationModel', 'navigation_id');
    }

    /**
     * Get page
     *
     * @return ORM
     */
    public function page()
    {
        return $this->belongsTo('\\Lahaina\\CMS\\Model\\PageModel', 'page_id');
    }

    public function save()
    {
        if (!$this->title) {
            $page = $this->page()->fetch();
            $this->title = $page->title;
        }

        $this->position = 1;
        $navigation = $this->navigation()->fetch();
        $lastNavitem = $navigation->navitems()
            ->where('parent_navitem_id', '=', $this->parent_navitem_id)
            ->orderByDesc('position')
            ->fetch();

        if ($lastNavitem) {
            $this->position = $lastNavitem->position + 1;
        }

        return parent::save();
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

        parent::delete();
    }
}
