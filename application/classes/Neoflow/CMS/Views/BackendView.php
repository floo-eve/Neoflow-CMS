<?php

namespace Neoflow\CMS\Views;

use Neoflow\CMS\Core\AbstractView;

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
     * Check wether validation error exists.
     *
     * @param string $key
     * @param mixed  $returnValue
     *
     * @return mixed
     */
    public function hasValidationError($key = '', $returnValue = true)
    {
        if ($this->service('validation')->hasError($key)) {
            return $returnValue;
        }

        return false;
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
     * Set back route as back url.
     *
     * @param string $routeKey
     * @param array  $args
     *
     * @return self
     */
    public function setBackRoute($routeKey, $args = array())
    {
        $backUrl = $this->generateUrl($routeKey, $args);

        return $this->setBackUrl($backUrl);
    }

    /**
     * Get back url.
     *
     * @return mixed
     */
    public function getBackUrl()
    {
        return $this->get('back_url');
    }

    /**
     * Set back url.
     *
     * @param string $backUrl
     *
     * @return self
     */
    public function setBackUrl($backUrl)
    {
        return $this->set('back_url', $backUrl);
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
