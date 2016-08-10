<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;

class NavitemController extends BackendController
{
    /**
     * Reorder navitems action.
     
     *
     
     * @param array $args
     
     * @return JsonResponse
     */
    public function reorderAction($args)
    {
        $json = file_get_contents('php://input');

        $result = false;

        if (is_json($json)) {
            $result = $this

                ->getService('navitem')

                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse(array('success' => $result));
    }
}
