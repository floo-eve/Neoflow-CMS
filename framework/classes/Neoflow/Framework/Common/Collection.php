<?php

namespace Neoflow\Framework\Common;

use \ArrayObject;
use \InvalidArgumentException;

class Collection extends ArrayObject
{

    /**
     * Delete model entities in collection
     * @return int
     */
    public function delete()
    {
        $result = 0;
        foreach ($this->getArrayCopy() as $entity) {
            if ($entity->delete()) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * Apply callback to model entities in collection
     *
     * @param callable $callback
     * @param bool $asArray
     * @return Collection|array
     *
     * @throws InvalidArgumentException
     */
    public function map($callback, $asArray = false)
    {
        if (is_callable($callback)) {
            $result = array_map($callback, $this->getArrayCopy());
            if ($asArray) {
                return $result;
            }
            return new self($result);
        }
        throw new InvalidArgumentException('Callback is not callable');
    }

    /**
     * Join model entities to a string
     *
     * @param callable $callback
     * @param string $seperator
     *
     * @return string
     */
    public function implode($callback, $seperator = ', ')
    {
        $result = $this->map($callback, true);
        return implode($seperator, $result);
    }
}
