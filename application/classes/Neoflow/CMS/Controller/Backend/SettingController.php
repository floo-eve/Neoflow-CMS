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
     * Index action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function indexAction($args)
    {
        // Get settings or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $settings = new SettingModel($data);
        } else {
            $settings = SettingModel::findById(1);
            if (!$settings) {
                throw new Exception('Settings not found (ID: '.$args['id'].')');
            }
        }

        return $this->render('backend/setting/index', array(
                'setting' => $settings,
                'languages' => LanguageModel::findAll(),
                'themes' => ThemeModel::findAll(),
        ));
    }

    /**
     * Update settings action.
     *
     * @param array $args
     *
     * @return \Neoflow\Framework\HTTP\Responsing\RedirectResponse
     */
    public function updateAction($args)
    {
        // Get post data
        $postData = $this->request()->getPostData();

        try {

            // Update settings
            $settings = SettingModel::update(array(
                    'language_id' => $postData->get('language_id'),
                    'website_title' => $postData->get('website_title'),
                    'website_description' => $postData->get('website_description'),
                    'keywords' => $postData->get('keywords'),
                    'author' => $postData->get('author'),
                    'theme_id' => $postData->get('theme_id'),
                    'backend_theme_id' => $postData->get('backend_theme_id'),
                    'language_id' => $postData->get('language_id'),
                    ), 1);

            // Validate and save settings
            if ($settings && $settings->validate() && $settings->save()) {

                // Update and save activity state of languages
                $activeLanguageIds = $postData->get('active_language_ids');
                LanguageModel::findAll()->each(function ($language) use ($settings, $activeLanguageIds) {
                    $language->is_active = false;
                    if ($settings->language_id == $language->id() || in_array($language->id(), $activeLanguageIds)) {
                        $language->is_active = true;
                    }
                    $language->save();
                });

                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update settings failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('setting_index');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return has_permission('settings');
    }
}
