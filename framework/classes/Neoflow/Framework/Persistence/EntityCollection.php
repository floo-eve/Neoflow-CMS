<?php

namespace Neoflow\Framework\Persistence;

class EntityCollection extends \Neoflow\Framework\Common\Collection
{

    /**
     * Delete entities in collection.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->each(function($item) {
                $item->delete();
            });
    }
}
