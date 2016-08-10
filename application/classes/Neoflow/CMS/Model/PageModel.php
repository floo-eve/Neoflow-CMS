<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Support\Validation\Validator;
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
        'description', 'keywords', 'is_active', 'visibility',
        'is_active', 'language_id', ];

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
                ->getConfig()
                ->getUrl('/'.implode('/', array_reverse($uriParts)));
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
     * Render page to view.
     *
     * @param FrontendView $view
     *
     * @return string
     */
    public function renderToView($view)
    {
        $view->set('page_title', $this->title);

        $sections = $this->sections()
            ->orderByAsc('position')
            ->fetchAll();

        foreach ($sections as $section) {
            $content = $section->render($view);
            $view->addContentToBlock($section->block, $content);
        }

        return $view;
    }

    /**
     * Save page.
     *
     * @param bool $validate
     *
     * @return bool
     */
    public function save($validate = true)
    {
        if (!$this->slug) {
            $this->slug = slugify($this->title);
        }

        return parent::save($validate);
    }

    /**
     * Create and save page.
     *
     * @param array $data
     * @param bool  $validate
     *
     * @return self|bool
     */
    public static function create($data, $validate = true)
    {
        $page = parent::create($data, $validate);

        if ($page) {
            NavitemModel::create(array(
                'navigation_id' => 1,
                'page_id' => $page->id(),
                'language_id' => $page->language_id,
                'parent_navitem_id' => $page->parent_navitem_id ?: null,
                ), $validate);

            if ($page->module_id) {
                SectionModel::create(array(
                    'page_id' => $page->id(),
                    'module_id' => $page->module_id,
                    'is_active' => true,
                    'block' => 1,
                    ), $validate);
            }
        }

        return $page;
    }

    /**
     * Validate page.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->toArray());

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
     * Get config.
     *
     * @return Config
     */
    protected function getConfig()
    {
        return $this->app()->get('config');
    }
}
