<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;

class SettingController extends BackendController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Set title
        $this->view
            ->setTitle('Settings');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    public function checkPermission()
    {
        return has_permission('settings');
    }

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function indexAction($args)
    {
        // Get setting entity (from database or self-made if validation has failed)
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $setting = new SettingModel($data);
        } else {
            $setting = SettingModel::findById(1);
        }

        // Get additional model entities
        $languages = LanguageModel::findAll();
        $themes = ThemeModel::findAll();

        return $this->render('backend/setting/index', array(
                'setting' => $setting,
                'languages' => $languages,
                'themes' => $themes,
        ));
    }

    /**
     * Update action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function updateAction($args)
    {
        // Get post data
        $postData = $this->request()->getPostData();
        $settingPostData = $postData->get('setting');
        $languagePostData = $settingPostData->get('language');

        // Get model entities
        $languages = LanguageModel::findAll();

        try {

            // Update settings
            $settings = SettingModel::update(array(
                    'language_id' => $settingPostData->get('language_id'),
                    'website_title' => $settingPostData->get('website_title'),
                    'website_description' => $settingPostData->get('website_description'),
                    'keywords' => $settingPostData->get('keywords'),
                    'author' => $settingPostData->get('author'),
                    'theme_id' => $settingPostData->get('theme_id'),
                    'backend_theme_id' => $settingPostData->get('backend_theme_id'),
                    ), '1');

            if ($settings->validate() && $settings->save()) {

                // Save active languages
                $activeLanguageIds = $languagePostData->get('active_language_ids');
                foreach ($languages as $language) {
                    $language->is_active = false;
                    if ($settings->language_id === $language->id() || in_array($language->id(), $activeLanguageIds)) {
                        $language->is_active = true;
                    }
                    $language->save();
                }
                $this->setSuccessAlert(translate('{0} successful updated', array('Settings')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('setting_index');
    }
}
