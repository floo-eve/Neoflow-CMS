<?php

namespace Neoflow\CMS\Mapper;

use \Neoflow\Framework\Core\AbstractMapper;

class NavitemMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Neoflow\\CMS\\Model\\NavitemModel';

    /**
     * 
     * @param array $order
     * @param type $parent_id
     * @return boolean
     */
    public function updateOrder(array $order, $parent_id = null)
    {
        foreach ($order as $index => $item) {
            $navitem = $this->findById($item['id']);
            $navitem->position = ++$index;
            $navitem->parent_navitem_id = $parent_id;
            $navitem->save();

            if (isset($item['children'])) {
                $this->updateOrder($item['children'], $item['id']);
            }
        }
        return true;
    }
}
