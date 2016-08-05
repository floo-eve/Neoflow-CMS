<?php

namespace Neoflow\Framework\ORM;

use Neoflow\Framework\Common\Collection;

class EntityCollection extends Collection
{

    /**
     * Delete entities in collection.
     *
     * @return bool
     */
    public function delete()
    {
        return (bool) $this->each(function($item) {
                $item->delete();
            });
    }
}
