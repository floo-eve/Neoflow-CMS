<?phpnamespace Neoflow\CMS\Handler;use \Neoflow\CMS\Repository\LanguageRepository;class Config extends \Neoflow\Framework\Handler\Config{    /**     * App trait     */    use \Neoflow\Framework\AppTrait;    public function getThemesUrl($uri = '')    {        return $this->getUrl('/themes/' . $uri);    }    public function getModulesUrl($uri = '')    {        return $this->getUrl('/modules/' . $uri);    }    public function getBackendUrl($uri = '')    {        return $this->getUrl('/backend/' . $uri);    }    public function getMediaUrl($uri = '')    {        return $this->getUrl('/media/' . $uri);    }    public function getThemesPath($uri = '')    {        return $this->getPath('/themes/' . $uri);    }    public function getModulesPath($uri = '')    {        return $this->getPath('/modules/' . $uri);    }    public function getBackendPath($uri = '')    {        return $this->getPath('/backend/' . $uri);    }    public function getMediaPath($uri = '')    {        return $this->getPath('/media/' . $uri);    }    public function get($key, $default = null)    {        if ($key === 'languages' && !$this->exists('languages')) {            $languageRepository = new LanguageRepository();            $languages = $languageRepository                ->where('is_active', '=', true)                ->fetchAll();            $languageCodes = array_map(function ($language) {                return $language->code;            }, $languages);            $this->set('languages', $languageCodes);        }        return parent::get($key, $default);    }}