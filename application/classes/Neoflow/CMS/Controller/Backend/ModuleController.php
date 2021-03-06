<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\Framework\Support\Validation\ValidationException;
use function translate;

class ModuleController extends BackendController
{

    public function indexAction($args)
    {

        return $this->render('backend/module/index', array(
                'modules' => ModuleModel::findAll()
        ));
    }

    public function installAction($args)
    {

        // Create module
        $module = ModuleModel::create(array(
                'package' => $this->request()->getFile('package'),
        ));

        try {

            // Install and save module
            if ($module && $module->install() && $module->validate() && $module->save()) {
                $this->setSuccessAlert(translate('Successful installed'));
            } else {
                throw new Exception('Create module failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }
        return $this->redirectToRoute('module_index');
    }
}
