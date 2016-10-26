<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use function translate;

class ModuleController extends BackendController {

    public function indexAction($args) {

        return $this->render('backend/module/index', array(
                    'modules' => ModuleModel::findAll()
        ));
    }

    public function installAction($args) {
        // Create module
        $module = ModuleModel::create(array(
                    'package' => $this->request()->getFile('package'),
        ));

        // Install and save module
        if ($module && $module->install() && $module->save()) {
            $this->setSuccessAlert(translate('Successful installed'));
        } else {
            throw new Exception('Create module failed');
        }

        return $this->redirectToRoute('module_index');
    }

}
