<?php

namespace Neoflow\CMS\Controller\Backend;

use \Neoflow\CMS\Controller\BackendController;
use \Neoflow\CMS\Mapper\NavitemMapper;
use \Neoflow\Framework\HTTP\Responsing\JsonResponse;

class NavitemController extends BackendController
{

    /**
     * @var NavitemMapper
     */
    protected $navitemMapper;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Create mapper
        $this->navitemMapper = new NavitemMapper();
    }

    public function reorderAction($args)
    {
        $json = file_get_contents('php://input');
        $result = false;
        if (is_json($json)) {
            $result = $this->navitemMapper->updateOrder(json_decode($json, true));
        }
        return new JsonResponse(array('success' => (bool) $result));
    }
}
