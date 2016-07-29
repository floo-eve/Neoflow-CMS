<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\SectionModel;

class SectionService
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
}
