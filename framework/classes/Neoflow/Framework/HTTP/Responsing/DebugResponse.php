<?php

namespace Neoflow\Framework\HTTP\Responsing;

class DebugResponse extends Response
{

    /**
     * Send response.
     */
    public function send()
    {
        $cacheName = $this->app()->get('cache')->getReflection()->getShortName();
        if ($cacheName === 'DummyCache') {
            $cacheName = 'disabled';
        }


        $debugBar = '<div id="debugBar">
            <ul class="list-inline">
                <li>Execution time: <small>' . round($this->app()->getExecutionTime(), 3) . ' seconds</small></li>
                <li>Language: <small>' . $this->translator()->getActiveLanguageCode() . '</small></li>
                <li>Date format: <small>' . $this->translator()->getDateFormat() . '</small></li>
                <li>Default language: <small>' . $this->translator()->getDefaultLanguageCode() . '</small></li>
                <li>Fallback language: <small>' . $this->translator()->getFallbackLanguageCode() . '</small></li>
                <li>Cache: <small>' . $cacheName . '</small></li>
                <li>Log level: <small>' . ($this->logger()->getLoglevel() ? : 'none' ) . '</small></li>
                <li>Executed queries: <small>' . $this->app()->get('database')->getNumberOfQueries() . '</small></li>
                <li>Cached queries: <small>' . count($this->app()->get('cache')->fetchByTag('query')) . '</small></li>
            </ul>
        </div>';
        $pos = strpos($this->getContent(), '</body>');
        $content = substr_replace($this->getContent(), $debugBar, $pos ? : 0, 0);

        $this->setContent($content);

        parent::send();
    }
}
