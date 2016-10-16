<?php

namespace Neoflow\Framework\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;

class Collection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable {

    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = array()) {
        $this->items = $items;
    }

    /**
     * Apply callback to collection items.
     *
     * @param callable $callback
     *
     * @return self;
     *
     * @throws InvalidArgumentException
     */
    public function each($callback) {
        if (is_callable($callback)) {
            array_walk_recursive($this->items, $callback);
            return $this;
        }
        throw new InvalidArgumentException('Callback is not callable');
    }

    /**
     * Filter collection items where are matching.
     *
     * @param string $property
     * @param string $value
     *
     * @return self
     */
    public function where($property, $value) {
        return $this->filter(function ($item) use ($property, $value) {
                    return $item->{$property} == $value;
                });
    }

    /**
     * Filter collection items where are not matching.
     *
     * @param string $property
     * @param string $value
     *
     * @return self
     */
    public function whereNot($property, $value) {
        return $this->filter(function ($item) use ($property, $value) {
                    return $item->{$property} != $value;
                });
    }

    /**
     * Apply callback to filters collection items.
     *
     * @param callable $callback
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function filter($callback) {
        if (is_callable($callback)) {
            $result = array_filter($this->items, $callback);

            return new self($result);
        }
        throw new InvalidArgumentException('Callback is not callable');
    }

    /**
     * Apply mapper callback to collection items.
     *
     * @param callable $callback
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function map($callback) {
        if (is_callable($callback)) {
            $result = array_map($callback, $this->items);

            return new self($result);
        }
        throw new InvalidArgumentException('Callback is not callable');
    }

    /**
     * Join collection items to a string.
     *
     * @param callable $callback
     * @param string   $seperator
     *
     * @return string
     */
    public function implode($callback, $seperator = ', ') {
        $result = $this->map($callback)->toArray();

        return implode($seperator, $result);
    }

    /**
     * Get first collection item
     *
     * @return mixed
     */
    public function first() {
        return reset($this->items);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count() {
        return count($this->items);
    }

    /**
     * Get the collection of items as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->items;
    }

    /**
     * Get the collection of items as json.
     *
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public function toJson($options = 0, $depth = 512) {
        return json_encode($this->items, $options, $depth);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->items[$offset];
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $offset
     * @param mixed $item
     */
    public function offsetSet($offset, $item) {
        if (is_null($offset)) {
            $this->items[] = $item;
        } else {
            $this->items[$offset] = $item;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $offset
     */
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }

    /**
     * Convert collection into array to get JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize() {
        return $this->items;
    }

    /**
     * Sort entities by entity property
     * @param string $property
     * @param string $order
     * @return self
     */
    public function sort($property, $order = 'ASC') {
        usort($this->entities, function($a, $b) use($property) {
            return strcmp($a->{$property}, $b->{$property});
        });
        if ($order === 'DESC') {
            $this->items = array_reverse($this->items);
        }
        return $this;
    }

    /**
     * Extract a slice of the collection items
     * @param int $length
     * @param int $offset
     * @return self
     */
    public function slice($length, $offset = 0) {
        if (is_int($length) && $length > 0) {
            $this->items = array_slice($this->items, $offset, $length);
        }
        return $this;
    }

}
