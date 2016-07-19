<?phpnamespace Neoflow\CMS\Model;use \Neoflow\CMS\Mapper\PageMapper;use \Neoflow\CMS\Views\FrontendView;use \Neoflow\Framework\Core\AbstractModel;use \Neoflow\Framework\Handler\Validation\ValidationException;use \Neoflow\Framework\Handler\Validation\Validator;use \Neoflow\Framework\Persistence\ORM;use function \slugify;class PageModel extends AbstractModel{    /**     * @var string     */    public static $tableName = 'pages';    /**     * @var string     */    public static $primaryKey = 'page_id';    /**     * @var array     */    public static $properties = ['page_id', 'title', 'slug',        'description', 'keywords', 'is_active', 'visibility',        'is_active', 'language_id'];    /**     * Get sections.     *     * @return ORM     */    public function sections()    {        return $this->hasMany('\\Neoflow\\CMS\\Model\\SectionModel', 'page_id');    }    /**     * Get parent page.     *     * @return ORM     */    public function parentPage()    {        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'parent_page_id');    }    /**     * Get parent pages.     *     * @return array     */    public function getParentPages()    {        $parentPages = array();        $page = $this;        while (1) {            $page = $page->parentPage()->fetch();            if ($page) {                $parentPages[] = $page;            } else {                break;            }        }        return $parentPages;    }    /**     * Get url.     *     * @return string     */    public function getUrl()    {        $uriParts = array();        if ($this->position > 1 || $this->parent_page_id !== null) {            $uriParts[] = $this->slug;        }        foreach ($this->getParentPages() as $parentPage) {            $uriParts[] = $parentPage->slug;        }        return \Neoflow\Registry::get('config')->getUrl() . '/' . implode('/', array_reverse($uriParts));    }    /**     * Get child pages.     *     * @return ORM     */    public function childPages()    {        return $this->hasMany('\\Neoflow\\CMS\\Model\\PageModel', 'parent_page_id');    }    /**     * Get language     *     * @return Language     */    public function language()    {        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'language_id');    }    /**     * Get navitems     *     * @return Language     */    public function navitems()    {        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'page_id');    }    /**     * Render to view     *     * @param FrontendView $view     * @return string     */    public function renderToView($view)    {        $view->set('page_title', $this->title);        $sections = $this->sections()            ->orderByAsc('position')            ->fetchAll();        foreach ($sections as $section) {            $content = $section->render($view);            $view->addContentToBlock($section->block, $content);        }        return $view;    }    /**     * Save page     *     * @return boolean     */    public function save()    {        if (!$this->slug) {            $this->slug = slugify($this->title);        }        if ($this->validate()) {            return parent::save();        }        return false;    }    /**     * Validate setting entity     *     * @return bool     */    public function validate()    {        $validator = new Validator($this->toArray());        $validator            ->required()            ->betweenLength(3, 50)            ->callback(function($title, $language_id, $page_id) {                $pageMapper = new PageMapper();                $pages = $pageMapper->getOrm()                    ->where('title', '=', $title)                    ->where('language_id', '=', $language_id)                    ->where('page_id', '!=', $page_id)                    ->fetchAll();                return (!$pages || count($pages) === 0);            }, '{0} has to be unique', array($this->page_id, $this->language_id))            ->set('title', 'Title');        return (bool) $validator->validate();    }    public function delete()    {        $navitem = $this->navitems()->where('navigation_id', '=', 1)->fetch();        $childNavitems = $navitem->childNavitems()->fetchAll();        if ($childNavitems && count($childNavitems) > 0) {            throw new ValidationException('Delete sub pages first');        }        $navitems = $this->navitems()->fetchAll();        foreach ($navitems as $navitem) {            $navitem->delete();        }        $sections = $this->sections()->fetchAll();        foreach ($sections as $section) {            $section->delete();        }        return parent::delete();    }}