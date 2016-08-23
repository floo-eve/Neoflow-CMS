<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;

class LanguageModel extends AbstractEntityModel
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
    public static $properties = ['language_id', 'is_active', 'code', 'title', 'flag_code'];

    /**
     * Get repository to fetch pages.
     *
     * @return EntityRepository
     */
    public function pages()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\PageModel', 'language_id');
    }

    /**
     * Get repository to fetch setting.
     *
     * @return EntityRepository
     */
    public function setting()
    {
        return $this->hasOne('\\Neoflow\\CMS\\Model\\SettingModel', 'language_id');
    }

    /**
     * Render flag icon to html output.
     *
     * @return string
     */
    public function renderFlagIcon()
    {
        return '<i class="flag-icon flag-icon-' . $this->flag_code . '"></i>';
    }
}
