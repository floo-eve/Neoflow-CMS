<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Support\Validation\Validator;

class SettingModel extends AbstractEntityModel
{

    /**
     * @var string
     */
    public static $tableName = 'settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public static $properties = ['setting_id', 'website_title', 'website_description',
        'keywords', 'author', 'theme_id',
        'backend_theme_id', 'language_id',];

    /**
     * Get repository to fetch frontend theme.
     *
     * @return EntityRepository
     */
    public function theme()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ThemeModel', 'theme_id');
    }

    /**
     * Get repository to fetch backend theme.
     *
     * @return EntityRepository
     */
    public function backendTheme()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ThemeModel', 'backend_theme_id');
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
     * Validate setting.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required()
            ->betweenLength(3, 50)
            ->set('website_title', 'Website title');

        $validator
            ->maxlength(150)
            ->set('website_description', 'Website description');

        $validator
            ->maxlength(255)
            ->set('keywords', 'Keywords');

        $validator
            ->maxlength(50)
            ->set('author', 'Author');

        return $validator->validate();
    }

    /**
     * Save settings.
     *
     * @return bool
     */
    public function save()
    {
        if (parent::save()) {

            // Update activation of languages
            $activeLanguageIds = $this->active_language_ids;
            LanguageModel::findAll()->each(function ($language) use ($activeLanguageIds) {
                $language->is_active = false;
                if ($this->language_id == $language->id() || in_array($language->id(), $activeLanguageIds)) {
                    $language->is_active = true;
                }
                $language->save();
            });

            return true;
        }
        return false;
    }
}
