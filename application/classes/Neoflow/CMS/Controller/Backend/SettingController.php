<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;
use Neoflow\Framework\Support\Validation\ValidationException;

class SettingController extends BackendController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view->setTitle('Settings');
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
     * Save action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function saveAction($args)
    {
        // Get post data
        $postData = $this->getRequest()->getPostData();
        $settingPostData = $postData->get('setting');
        $languagePostData = $settingPostData->get('language');

        // Get model entities
        $setting = SettingModel::findById(1);
        $languages = LanguageModel::findAll();

        try {

            // Save setting
            $setting->language_id = $settingPostData->get('language_id');
            $setting->website_title = $settingPostData->get('website_title');
            $setting->website_description = $settingPostData->get('website_description');
            $setting->keywords = $settingPostData->get('keywords');
            $setting->author = $settingPostData->get('author');
            $setting->theme_id = $settingPostData->get('theme_id');
            $setting->backend_theme_id = $settingPostData->get('backend_theme_id');

            if ($setting->validate() && $setting->save()) {

                // Save active languages
                $activeLanguageIds = $languagePostData->get('active_language_ids');
                foreach ($languages as $language) {
                    $language->is_active = false;
                    if ($setting->language_id === $language->id() || in_array($language->id(), $activeLanguageIds)) {
                        $language->is_active = true;
                    }
                    $language->save();
                }
                $this->setFlash('alert', new SuccessAlert('{0} successful saved', array('Settings')));
            } else {
                $this->setFlash('alert', new DangerAlert('Save failed'));
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }

        return $this->redirectToRoute('setting_index');
    }
}
