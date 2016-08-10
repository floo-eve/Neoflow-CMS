<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\Core\AbstractService;

class SectionService extends AbstractService
{
    /**
     * Update section order.
     *
     * @param array $order
     *
     * @return bool
     */
    public function updateOrder(array $order)
    {
        foreach ($order as $index => $item) {
            $section = SectionModel::findById($item['id']);
            $section->position = ++$index;
            $section->save();
        }

        return true;
    }

    public function getServiceName()
    {
        return 'section';
    }
}
