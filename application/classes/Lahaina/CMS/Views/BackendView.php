<?php

namespace Lahaina\CMS\Views;

use \Lahaina\CMS\Core\AbstractView;

class BackendView extends AbstractView
{

    protected $title = '';
    protected $subtitle = '';

    /**
     * Set theme.
     */
    protected function setTheme()
    {
        $this->theme = $this->app()
            ->get('setting')
            ->backendTheme()
            ->fetch();
    }

    /**
     * Set title.
     *
     * @param string $title
     * @param bool   $translate
     * @param array  $values
     *
     * @return BackendView
     */
    public function setTitle($title, $translate = true, array $values = array())
    {
        $this->title = $title;
        if ($translate) {
            $this->title = $this->translate($title, $values);
        }

        return $this;
    }

    /**
     * Set Subtitle.
     *
     * @param string $subtitle
     * @param bool   $translate
     * @param array  $values
     *
     * @return BackendView
     */
    public function setSubtitle($subtitle, $translate = true, array $values = array())
    {
        $this->subtitle = $subtitle;
        if ($translate) {
            $this->subtitle = $this->translate($subtitle, $values);
        }

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Render alert.
     *
     * @return string
     */
    public function renderAlert()
    {
        $session = $this->app()->get('session');

        if ($session->hasFlash('alert')) {
            return $this->renderTemplate('alert', array(
                    'alert' => $session->getFlash('alert'),
            ));
        }

        return '';
    }
}
