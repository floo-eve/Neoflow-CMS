<?phpnamespace Lahaina\Framework\HTTP\Responsing;class DebugResponse extends Response{    /**     * Send response.     */    public function send()    {        $cacheName = $this->app()->get('cache')->getReflection()->getShortName();        if ($cacheName === 'DisabledCache') {            $cacheName = 'disabled';        }        $debugBar = '<div id="debugBar">            <ul class="list-inline">                <li>Execution time: <small>' . round($this->app()->getExecutionTime(), 3) . ' seconds</small></li>                <li>Language: <small>' . $this->app()->get('translator')->getCurrentLanguageCode() . '</small></li>                <li>Date format: <small>' . $this->app()->get('translator')->getDateFormat() . '</small></li>                <li>Default language: <small>' . $this->app()->get('translator')->getDefaultLanguageCode() . '</small></li>                <li>Fallback language: <small>' . $this->app()->get('translator')->getFallbackLanguageCode() . '</small></li>                <li>Cache: <small>' . $cacheName . '</small></li>                <li>Log level: <small>' . ($this->getLogger()->getLogLevel() ? : 'none' ) . '</small></li>                <li>Executed queries: <small>' . $this->app()->get('database')->getNumberOfQueries() . '</small></li>                <li>Cached queries: <small>' . count($this->app()->get('cache')->fetchByTag('query')) . '</small></li>            </ul>        </div>';        $pos = strpos($this->getContent(), '</body>');        $content = substr_replace($this->getContent(), $debugBar, $pos ? : 0, 0);        $this->setContent($content);        parent::send();    }}