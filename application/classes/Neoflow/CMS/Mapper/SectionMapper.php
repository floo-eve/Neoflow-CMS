<?php

namespace Neoflow\CMS\Mapper;

use \Neoflow\Framework\Core\AbstractMapper;

class SectionMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Neoflow\\CMS\\Model\\SectionModel';

    /**
     * Update order
     *
     * @param array $order
     * @param type $parent_id
     * @return boolean
     */
    public function updateOrder(array $order)
    {
        $result = true;
        foreach ($order as $index => $item) {
            $section = $this->findById($item['id']);
            $section->position = ++$index;
            if (!$section->save()) {
                $result = false;
            }
        }
        return $result;
    }
}
