<?php

namespace Neoflow\Framework\ORM;

use Neoflow\Framework\Common\Collection;

class EntityCollection extends Collection {

    /**
     * Delete model entities in collection.
     *
     * @return bool
     */
    public function delete() {
        $result = true;
        $this->each(function($item) use ($result) {
            if (!$item->delete()) {
                $result = false;
            }
        });
        return $result;
    }

    /**
     * Apply property or callback mapper to model entities
     *
     * @param callable|string $callback
     * @return array
     */
    public function map($callback) {
        if (is_string($callback)) {
            $callback = function($entity) use ($callback) {
                return $entity->$callback;
            };
        }
        return parent::map($callback);
    }

}
