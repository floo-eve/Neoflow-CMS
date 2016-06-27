<?php

namespace Lahaina\CMS\Model;

use Lahaina\Framework\Core\AbstractModel;
use Lahaina\Framework\Persistence\ORM;

class LanguageModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'languages';

    /**
     * @var string
     */
    public static $primaryKey = 'language_id';

    /**
     * @var array
     */
    public static $properties = ['language_id', 'is_active', 'code', 'title'];

    /**
     * Get sections.
     *
     * @return ORM
     */
    public function pages()
    {
        return $this->hasMany('\\Lahaina\\CMS\\Model\\PageModel', 'language_id');
    }

    /**
     * Get setting.
     *
     * @return ORM
     */
    public function setting()
    {
        return $this->hasOne('\\Lahaina\\CMS\\Model\\SettingModel', 'language_id');
    }

    /**
     * Get translated language title.
     *
     * @return string
     */
    public function getTranslatedTitle()
    {
        return $this->app()->get('translator')->translate($this->title);
    }

    /**
     * Render flag icon to html output.
     *
     * @return string
     */
    public function renderFlagIcon()
    {
        return '<i class="flag-icon flag-icon-'.$this->flag_code.'"></i>';
    }
}
