<?php

namespace Neoflow\Framework\Handler;

use DateTime;

class Translator
{

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var array
     */
    protected $translation = array();

    /**
     * @var array
     */
    protected $fallbackTranslation = array();

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d h:m';

    /**
     * @var string
     */
    protected $fallbackDateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $fallbackDateTimeFormat = 'Y-m-d h:m';

    /**
     * @var string
     */
    protected $activeLanguageCode;

    /**
     * @var bool
     */
    protected $isFallback = false;

    /**
     * @var string
     */
    protected $fallbackLanguageCode = 'en';

    /**
     * Get fallback language code.
     *
     * @return string
     */
    public function getFallbackLanguageCode()
    {
        return $this->fallbackLanguageCode;
    }

    /**
     * Get default language code (first of usable languages).
     *
     * @return string
     */
    public function getDefaultLanguageCode()
    {
        return $this->config()->get('languages')[0];
    }

    /**
     * Load translation.
     *
     * @return Translator
     */
    public function loadTranslation()
    {
        // Load translation file
        $translationFile = $this->config()
            ->getPath('/application/i18n/' . $this->activeLanguageCode . '.php');
        $this->runTranslationFile($translationFile);

        // Load fallback translation file
        $fallbackTranslationFile = $this->config()
            ->getPath('/application/i18n/' . $this->fallbackLanguageCode . '.php');
        $this->runTranslationFile($fallbackTranslationFile, true);

        return $this;
    }

    /**
     * Identify language code.
     *
     * @return Translator
     */
    public function identifyLanguage()
    {
        $request = $this->app()->get('request');

        // Set default language code as current
        $this->activeLanguageCode = $this->getDefaultLanguageCode();

        // Get language code from HTTP header
        $httpLanguage = strtolower($request->getHttpLanguage());

        // Get language code from uri
        $uriLanguage = strtolower($request->getUriLanguage());

        // Get language code from session
        $sessionLanguage = $this->session()->get('_language');

        // Set current language code
        if ($uriLanguage && in_array($uriLanguage, $this->config()->get('languages'))) {
            $this->activeLanguageCode = $uriLanguage;
        } elseif ($sessionLanguage && in_array($sessionLanguage, $this->config()->get('languages'))) {
            $this->activeLanguageCode = $sessionLanguage;
        } elseif ($httpLanguage && in_array($httpLanguage, $this->config()->get('languages'))) {
            $this->activeLanguageCode = $httpLanguage;
        }

        // Set language code to session
        $this->session()->set('_language', $this->activeLanguageCode);

        return $this;
    }

    /**
     * Get active language code.
     *
     * @return string
     */
    public function getActiveLanguageCode()
    {
        return $this->activeLanguageCode;
    }

    /**
     * Set date format.
     *
     * @param string $format
     *
     * @return Translator
     */
    public function setDateFormat($format)
    {
        if ($this->isFallback) {
            $this->fallbackDateFormat = $format;
        } else {
            $this->dateFormat = $format;
        }

        return $this;
    }

    /**
     * Set date time format.
     *
     * @param string $format
     *
     * @return Translator
     */
    public function setDateTimeFormat($format)
    {
        if ($this->isFallback) {
            $this->fallbackDateTimeFormat = $format;
        } else {
            $this->dateTimeFormat = $format;
        }

        return $this;
    }

    /**
     * Add translation.
     *
     * @param array $translation
     *
     * @return Translator
     */
    public function addTranslation($translation)
    {
        if ($this->isFallback) {
            $this->fallbackTranslation = array_merge($this->fallbackTranslation, $translation);
        } else {
            $this->translation = array_merge($this->translation, $translation);
        }

        return $this;
    }

    /**
     * Run translation file.
     *
     * @param string $translationFile
     * @param bool   $isFallback
     *
     * @return Translator
     */
    protected function runTranslationFile($translationFile, $isFallback = false)
    {
        $this->isFallback = $isFallback;
        if (is_file($translationFile)) {
            include $translationFile;
        }
        $this->isFallback = false;

        return $this;
    }

    /**
     * Translate key and values.
     *
     * @param string $key
     * @param array  $values
     * @param string $errorPrefix
     *
     * @return string
     */
    public function translate($key, $values = array(), $errorPrefix = '!', $translateValues = true)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $translation = $errorPrefix . $errorPrefix . $key;

        if (isset($this->translation[$key])) {
            $translation = $this->translation[$key];
        } elseif (isset($this->fallbackTranslation[$key])) {
            $translation = $errorPrefix . $this->fallbackTranslation[$key];
        }

        foreach ($values as $placeholder => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (is_a($value, '\\DateTime')) {
                $value = $this->formatDate($value);
            }
            if ($translateValues) {
                $value = $this->translate($value, array(), '', false);
            }
            $translation = str_replace('{' . $placeholder . '}', $value, $translation);
        }

        return $translation;
    }

    /**
     * Get date format.
     *
     * @param string $timeFormat
     *
     * @return string
     */
    public function getDateFormat($timeFormat = '')
    {
        return $this->dateFormat . $timeFormat;
    }

    /**
     * Format date.
     *
     * @param DateTime $dateTime
     *
     * @return string
     */
    public function formatDate(DateTime $dateTime)
    {
        return $dateTime->format($this->dateFormat);
    }

    /**
     * Format date and time.
     *
     * @param DateTime $dateTime
     *
     * @return string
     */
    public function formatDateTime(DateTime $dateTime)
    {
        return $dateTime->format($this->dateTimeFormat);
    }
}
