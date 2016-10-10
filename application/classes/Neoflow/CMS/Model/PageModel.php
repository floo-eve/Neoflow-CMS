<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\Support\Validation\Validator;
use Neoflow\Framework\ORM\EntityRepository;

class PageModel extends AbstractEntityModel
{

    /**
     * @var string
     */
    public static $tableName = 'pages';

    /**
     * @var string
     */
    public static $primaryKey = 'page_id';

    /**
     * @var array
     */
    public static $properties = ['page_id', 'title', 'slug',
        'description', 'keywords', 'is_active', 'is_restricted',
        'language_id',];

    /**
     * Get repository to fetch sections.
     *
     * @return EntityRepository
     */
    public function sections()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\SectionModel', 'page_id');
    }

    /**
     * Get page url.
     *
     * @return string
     */
    public function getUrl()
    {
        $uriParts = array();

        if ($this->position > 1 || $this->parent_page_id !== null) {
            $uriParts[] = $this->slug;
        }

        foreach ($this->getParentPages() as $parentPage) {
            $uriParts[] = $parentPage->slug;
        }

        return $this
                ->config()
                ->getUrl('/' . implode('/', array_reverse($uriParts)));
    }

    /**
     * Get repository to fetch language.
     *
     * @return EntityRepository
     */
    public function language()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'language_id');
    }

    /**
     * Get repository to fetch navitems.
     *
     * @return EntityRepository
     */
    public function navitems()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'page_id');
    }

    /**
     * Save page.
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->slug) {
            $this->slug = slugify($this->title);
        }

        $result = parent::save();

        if ($result) {

            if ($this->isNew) {
                NavitemModel::create(array(
                    'navigation_id' => 1,
                    'page_id' => $this->id(),
                    'language_id' => $this->language_id,
                    'is_visible' => $this->is_active,
                    'parent_navitem_id' => $this->parent_navitem_id ? : null,
                ))->save();
            } else {

                $navitem = NavitemModel::repo()
                    ->where('page_id', '=', $this->id())
                    ->where('navigation_id', '=', 1)
                    ->fetch();

                $navitem->is_visible = $this->is_visible;
                $navitem->parent_navitem_id = $this->parent_navitem_id ? : null;
                $navitem->save();
            }

            if ($this->module_id) {
                SectionModel::create(array(
                    'page_id' => $this->id(),
                    'module_id' => $this->module_id,
                    'is_active' => true,
                    'block' => 1,
                ))->save();
            }
        }
        return $result;
    }

    /**
     * Validate page.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required()
            ->betweenLength(3, 50)
            ->callback(function ($title, $page) {
                $pages = PageModel::repo()
                    ->where('title', '=', $title)
                    ->where('page_id', '!=', $page->id())
                    ->where('language_id', '=', $page->language_id)
                    ->fetchAll();

                return count($pages) === 0;
            }, '{0} has to be unique', array($this))
            ->set('title', 'Title');

        $validator
            ->required()
            ->set('visibility', 'Visibility');

        return $validator->validate();
    }

    /**
     * Delete page.
     *
     * @return bool
     */
    public function delete()
    {
        $navitem = $this->navitems()->where('navigation_id', '=', 1)->fetch();
        $childNavitems = $navitem->childNavitems()->fetchAll();
        if ($childNavitems && count($childNavitems) > 0) {
            foreach ($childNavitems as $childNavitems) {
                $subpage = $childNavitems->page()->fetch();
                $subpage->delete();
            }
        }

        $navitems = $this->navitems()->fetchAll();
        foreach ($navitems as $navitem) {
            $navitem->delete();
        }

        $sections = $this->sections()->fetchAll();
        foreach ($sections as $section) {
            $section->delete();
        }

        return parent::delete();
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
