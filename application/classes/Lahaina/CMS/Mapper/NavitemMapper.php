<?php

namespace Lahaina\CMS\Mapper;

use \Lahaina\Framework\Core\AbstractMapper;

class NavitemMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Lahaina\\CMS\\Model\\NavitemModel';

    public function findAllByLanguageId($language_id, $nested = true)
    {
        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $language_id)
            ->fetchAll();

        $result = array();
        foreach ($navitems as $navitem) {
            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();
        }
    }

    public function updateOrder(array $order, $parent_id = null)
    {
        $position = 1;
        foreach ($order as $item) {
            $navitem = $this->findById($item['id']);
            $navitem->position = $position++;
            $navitem->parent_navitem_id = $parent_id;
            $navitem->collapsed = $item['collapsed'];
            $navitem->save();

            if (isset($item['children'])) {
                $this->updateOrder($item['children'], $item['id']);
            }
        }
        return true;
    }
}
