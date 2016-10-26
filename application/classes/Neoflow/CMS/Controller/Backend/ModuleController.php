<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;

class ModuleController extends BackendController {

    public function indexAction($args) {
        
        return $this->render('backend/module/index', array(
            'modules' => ModuleModel::findAll()
        ));
        
    }
    

}
